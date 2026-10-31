<?php
namespace App\Controllers;

class DocumentActionController extends BaseController {

    public function processUpload(): void {
        $this->requireLogin();
        $this->validateCsrf();

        $error_msg = '';
        $success_msg = '';

        $raw_type_key  = $this->stringParam('type_key', '', 'post');
        $post_type_key = preg_replace('/[^a-z0-9_]/', '', strtolower($raw_type_key));
        $post_title    = trim(str_replace(["\0", "\r", "\n"], '', $this->stringParam('title', '', 'post')));
            
        // Fallback for title if it's missing but meta title fields exist
        if ($post_title === '') {
            $meta = $_POST['meta'] ?? [];
            if (!empty($meta['event_name']) && is_string($meta['event_name'])) {
                $post_title = trim($meta['event_name']);
            } elseif (!empty($meta['event_title']) && is_string($meta['event_title'])) {
                $post_title = trim($meta['event_title']);
            } elseif (!empty($meta['paper_title']) && is_string($meta['paper_title'])) {
                $post_title = trim($meta['paper_title']);
            } elseif (!empty($meta['patent_title']) && is_string($meta['patent_title'])) {
                $post_title = trim($meta['patent_title']);
            } elseif (!empty($meta['exam']) && is_string($meta['exam'])) {
                $post_title = trim($meta['exam']);
            } elseif ($post_type_key === 'student_activity_file' && (($meta['activity_category'] ?? '') === 'Stepcone')) {
                $post_title = 'Stepcone Event';
            } else {
                $post_title = 'Untitled Document';
            }
        }

        $post_dept_id = $this->intParam('dept_id', 0, 'post');
        $post_year_id = self::resolveValidAcademicYearId($this->conn, $_POST['year_id'] ?? ($_POST['academic_year_id'] ?? null));

        $post_doc_type = doc_get_type_by_key($this->conn, $post_type_key);
        if (!$post_doc_type) {
            $error_msg = 'Invalid document type.';
        } elseif ($post_title === '') {
            $error_msg = 'Document title is required.';
        } elseif ($post_dept_id <= 0) {
            $error_msg = 'Department is required.';
        } else {
            $mentor_id = null;
            $is_student = false;
            $user_roles = $this->auth['roles'] ?? [];
            foreach ($user_roles as $role) {
                $r_id = is_array($role) ? (int)($role['role_id'] ?? 0) : (int)$role;
                if ($r_id === ROLE_STUDENT) {
                    $is_student = true;
                    break;
                }
            }

            if ($is_student) {
                $mentor_per_no = $this->stringParam('mentor_per_no', '', 'post');
                if ($mentor_per_no === '') {
                    $error_msg = 'Mentor Per No is required for student uploads.';
                } else {
                    $mentor_role_ids = implode(',', [ROLE_FACULTY, ROLE_HOD, ROLE_DEPT_COORDINATOR]);
                    $mentor_stmt = $this->conn->prepare("
                        SELECT up.user_id 
                        FROM user_profiles up
                        JOIN user_roles ur ON up.user_id = ur.user_id
                        WHERE up.per_no = ? AND ur.role_id IN ($mentor_role_ids) AND ur.dept_id = ?
                    ");
                    if ($mentor_stmt) {
                        $mentor_stmt->bind_param("si", $mentor_per_no, $post_dept_id);
                        $mentor_stmt->execute();
                        $mentor_res = $mentor_stmt->get_result();
                        if ($mentor_row = $mentor_res->fetch_assoc()) {
                            $mentor_id = (int)$mentor_row['user_id'];
                        } else {
                            $error_msg = 'Invalid Mentor Per No. No Faculty found with this Per No in your department.';
                        }
                        $mentor_stmt->close();
                    } else {
                        $error_msg = 'Database error verifying mentor.';
                    }
                }
            }

            if (empty($error_msg)) {
                $dept_authorized = false;
                foreach ($user_roles as $role) {
                    $d_id = is_array($role) ? (int)($role['dept_id'] ?? 0) : (int)($this->auth['dept_id'] ?? 0);
                    if ($d_id === $post_dept_id) {
                        $dept_authorized = true;
                        break;
                    }
                }

                foreach ($user_roles as $role) {
                    $r_id = is_array($role) ? (int)($role['role_id'] ?? 0) : (int)$role;
                    if (in_array($r_id, [ROLE_ADMIN, ROLE_RND_DEAN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true)) {
                        $dept_authorized = true;
                        break;
                    }
                }

                if (!$dept_authorized) {
                    $error_msg = 'You are not authorized to upload for this department.';
                }
            }

            if (empty($error_msg)) {
                // Gather metadata
                $meta_fields = meta_get_fields($post_type_key);
                $meta_data = [];
                if (!empty($meta_fields)) {
                    foreach ($meta_fields as $field) {
                        $field_name = $field['name'];
                        if (isset($_POST['meta'][$field_name])) {
                            $meta_data[$field_name] = $_POST['meta'][$field_name];
                        }
                    }
                }

                // Gather files
                $file_slots = meta_get_file_slots($post_type_key);
                $mapped_files = [];
                if (!empty($file_slots)) {
                    foreach ($file_slots as $slot) {
                        $slot_name = $slot['name'];
                        if (isset($_FILES[$slot_name]) && $_FILES[$slot_name]['error'] !== UPLOAD_ERR_NO_FILE) {
                            $mapped_files[$slot_name] = $_FILES[$slot_name];
                        }
                    }
                }

                $result = doc_create(
                    $this->conn,
                    $post_type_key,
                    $this->userId,
                    $post_dept_id,
                    $post_year_id,
                    $post_title,
                    $meta_data,
                    $mapped_files,
                    $mentor_id
                );

                if (!$result['success']) {
                    $error_msg = $result['error'] ?? 'Failed to create document record.';
                } else {
                    $doc_id = $result['doc_id'];
                    $success_msg = 'Document uploaded successfully.';
                }
            }
        }

        $is_student_activity = (isset($_POST['custom_handler']) && $_POST['custom_handler'] === 'student_activity');

        if ($success_msg) {
            $this->flashSuccess($success_msg);
            unset($_SESSION['_old_input']);
            if ($is_student_activity) {
                $this->redirect(BASE_URL . "/public/index.php?route=student/dashboard");
            } else {
                $this->redirect(BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}");
            }
        } else {
            $this->flashError($error_msg);
            $_SESSION['_old_input'] = $_POST;
            if ($is_student_activity) {
                $this->redirect(BASE_URL . "/public/index.php?route=student/dashboard");
            } else {
                $redirect_url = BASE_URL . "/public/index.php?route=documents/upload&type=" . urlencode($post_type_key);
                $act = $_POST['meta']['activity_category'] ?? ($_GET['activity'] ?? null);
                if (!empty($act) && is_string($act)) {
                    $redirect_url .= "&activity=" . urlencode($act);
                }
                $sub = $_POST['meta']['sub_category'] ?? ($_GET['sub_type'] ?? null);
                if (!empty($sub) && is_string($sub)) {
                    $redirect_url .= "&sub_type=" . urlencode($sub);
                }
                $this->redirect($redirect_url);
            }
        }
    }

    public function approve(): void {
        $this->requireLogin();
        if (!$this->isPost()) {
            $this->flashError("Invalid request method.");
            $this->redirectBack();
        }
        $this->validateCsrf();

        $doc_id = $this->intParam('doc_id', 0, 'post');
        $action = $this->stringParam('action', '', 'post');
        $comments = $this->stringParam('remarks', '', 'post');

        if ($doc_id <= 0 || !in_array($action, ['approve', 'reject', 'resubmit'], true)) {
            $this->flashError("Invalid request parameters.");
            $this->redirectBack();
        }

        if ($action === 'reject' && $comments === '') {
            $this->flashError("Remarks are required when rejecting a document.");
            $this->redirectBack(BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}");
        }

        $doc = doc_get($this->conn, $doc_id);
        if (!$doc) {
            $this->flashError("Document not found.");
            $this->redirectBack();
        }

        $allowed_actions = wf_get_allowed_actions($this->conn, $doc, $this->auth);
        if (!in_array($action, $allowed_actions, true)) {
            $this->flashError("You do not have permission to perform this action on this document.");
            $this->redirectBack(BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}");
        }

        $result = wf_execute_action($this->conn, $doc_id, $this->userId, $action, $comments);
        if ($result['success']) {
            $status_msg = !empty($result['new_status']) ? " New Status: " . ucfirst($result['new_status']) : "";
            $this->flashSuccess("Action performed successfully." . $status_msg);
        } else {
            $this->flashError($result['error'] ?? "Failed to perform action.");
        }
        $this->redirect(BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}");
    }

    public function download(): void {
        $this->requireLogin();
        $file_id = $this->intParam('file_id', 0, 'get');
        if ($file_id <= 0) {
            $this->flashError("Invalid file ID.");
            $this->redirectBack();
        }

        $stmt = $this->conn->prepare("SELECT doc_id, file_path, original_name as original_filename, file_size, mime_type, file_label FROM document_files WHERE file_id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $file_id);
            $stmt->execute();
            $file_record = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        } else {
            $file_record = null;
        }

        if (!$file_record) {
            $this->flashError("File not found.");
            $this->redirectBack();
        }

        $doc = doc_get($this->conn, (int)$file_record['doc_id']);
        if (!$doc) {
            $this->flashError("Associated document not found.");
            $this->redirectBack();
        }

        if (!doc_can_view($this->conn, $this->auth, $doc)) {
            $this->flashError("You do not have permission to download this file.");
            $this->redirectBack(BASE_URL . "/public/index.php?route=documents/view&id=" . (int)$file_record['doc_id']);
        }

        $raw_file_path = $file_record['file_path'];
        $base_dir = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2);
        $target_file = file_exists($raw_file_path) ? $raw_file_path : ($base_dir . '/' . ltrim($raw_file_path, '/\\'));
        $canonical_path = realpath($target_file);
        $allowed_base = realpath($base_dir . '/uploads');
        $canonical_base = $allowed_base ? (rtrim($allowed_base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR) : null;

        if (!$canonical_path || !$canonical_base || stripos($canonical_path, $canonical_base) !== 0 || !is_file($canonical_path)) {
            $this->flashError("The physical file is missing or invalid on the server.");
            $this->redirectBack(BASE_URL . "/public/index.php?route=documents/view&id=" . (int)$file_record['doc_id']);
        }

        // Auto-heal stale merged master PDF from fresh source files if available
        if (($file_record['file_label'] ?? '') === 'merged_pdf') {
            $this->healMergedPdf((int)$file_record['doc_id'], $canonical_path, $base_dir);
        }

        $is_inline = !empty($_GET['inline']);
        file_stream_and_cleanup($canonical_path, $file_record['original_filename'], $file_record['mime_type'] ?: 'application/octet-stream', $is_inline, []);
    }

    private function healMergedPdf(int $docId, string $realPath, string $baseDir): void {
        $child_stmt = $this->conn->prepare("SELECT file_path, file_label FROM document_files WHERE doc_id = ? AND file_label != 'merged_pdf' ORDER BY file_id ASC");
        if (!$child_stmt) return;
        $child_stmt->bind_param('i', $docId);
        $child_stmt->execute();
        $cres = $child_stmt->get_result();
        $child_files = [];
        while ($crow = $cres->fetch_assoc()) {
            $cp = file_exists($crow['file_path']) ? $crow['file_path'] : ($baseDir . '/' . ltrim($crow['file_path'], '/\\'));
            if (file_exists($cp)) {
                $child_files[] = [
                    'path' => realpath($cp),
                    'title' => ucfirst(str_replace('_', ' ', $crow['file_label']))
                ];
            }
        }
        $child_stmt->close();

        if (count($child_files) > 1) {
            require_once __DIR__ . '/../../core/pdf_merger_service.php';
            try {
                merge_pdfs_with_headings($child_files, $realPath);
            } catch (\Throwable $e) {
                error_log("Dynamic heal of merged_pdf failed: " . $e->getMessage());
            }
        }
    }

    public function processUpdate(): void {
        $this->requireLogin();
        $this->validateCsrf();

        $doc_id = $this->intParam('doc_id', 0, 'post');
        if (!$doc_id) {
            $this->flashError("Invalid document ID.");
            $this->redirect(BASE_URL . "/public/index.php?route=documents/my_uploads");
        }

        $doc = doc_get($this->conn, $doc_id);
        if (!$doc) {
            $this->flashError("Document not found.");
            $this->redirect(BASE_URL . "/public/index.php?route=documents/my_uploads");
        }

        if (!doc_can_edit($this->conn, $this->auth, $doc)) {
            $this->flashError("Unauthorized to edit this document.");
            $this->redirect(BASE_URL . "/public/index.php?route=documents/my_uploads");
        }

        $type_key = $doc['type_key'];
        
        $post_title = $this->stringParam('title', '', 'post');
        $post_dept_id = $this->intParam('dept_id', 0, 'post');
        $post_year_id = self::resolveValidAcademicYearId($this->conn, $_POST['year_id'] ?? ($_POST['academic_year_id'] ?? null));
        
        if ($post_title === '' || $post_dept_id <= 0) {
            $this->flashError("Document Title and Department are required.");
            $this->redirect(BASE_URL . "/public/index.php?route=documents/edit&id=" . $doc_id);
        }

        if ($post_dept_id > 0 && validate_dept_filter($post_dept_id) === null) {
            $this->flashError("Unauthorized department selection.");
            $this->redirect(BASE_URL . "/public/index.php?route=documents/my_uploads");
        }

        $stmt = $this->conn->prepare("UPDATE documents SET title = ?, dept_id = ?, academic_year_id = ?, updated_at = NOW() WHERE doc_id = ?");
        if ($stmt) {
            $stmt->bind_param('siii', $post_title, $post_dept_id, $post_year_id, $doc_id);
            $stmt->execute();
            $stmt->close();
        }

        // Update meta
        $meta_data = $_POST['meta'] ?? [];
        if (!empty($meta_data)) {
            $meta_table = meta_get_table($type_key);
            if ($meta_table) {
                $fields = meta_get_fields($type_key);
                $update_cols = [];
                $types = '';
                $values = [];
                foreach ($fields as $field) {
                    $name = $field['name'];
                    if (isset($meta_data[$name])) {
                        $val = $meta_data[$name];
                        if (is_array($val)) {
                            $val = json_encode($val);
                        }
                        $update_cols[] = "`$name` = ?";
                        $types .= 's';
                        $values[] = $val;
                    }
                }
                
                if (!empty($update_cols)) {
                    $sql = "UPDATE `$meta_table` SET " . implode(', ', $update_cols) . " WHERE doc_id = ?";
                    $types .= 'i';
                    $values[] = $doc_id;
                    $stmt = $this->conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param($types, ...$values);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }
        }

        // Handle File Replacements
        $file_slots = meta_get_file_slots($type_key);
        $files = $_FILES ?? [];
        $replaced_any_file = false;

        foreach ($file_slots as $slot) {
            $slot_name = $slot['name'];
            if (!empty($files[$slot_name]) && $files[$slot_name]['error'] === UPLOAD_ERR_OK) {
                $store_result = file_store($files[$slot_name], $type_key, $slot_name);
                if ($store_result['success']) {
                    // Delete physical file before deleting record
                    $stmt_old = $this->conn->prepare("SELECT file_path FROM document_files WHERE doc_id = ? AND file_label = ?");
                    if ($stmt_old) {
                        $stmt_old->bind_param('is', $doc_id, $slot_name);
                        $stmt_old->execute();
                        $res_old = $stmt_old->get_result();
                        $base_dir = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2);
                        while ($old_row = $res_old->fetch_assoc()) {
                            $old_path = $base_dir . '/' . ltrim($old_row['file_path'], '/\\');
                            if (file_exists($old_path) && is_file($old_path)) {
                                @unlink($old_path);
                            }
                        }
                        $stmt_old->close();
                    }

                    // Delete old file record for this slot
                    $del_stmt = $this->conn->prepare("DELETE FROM document_files WHERE doc_id = ? AND file_label = ?");
                    if ($del_stmt) {
                        $del_stmt->bind_param('is', $doc_id, $slot_name);
                        $del_stmt->execute();
                        $del_stmt->close();
                    }

                    // Save new file record
                    file_save_record($this->conn, $doc_id, $store_result['data'], $slot_name);
                    $replaced_any_file = true;
                }
            }
        }

        if ($replaced_any_file) {
            // Re-merge PDFs if multiple exist
            $stmt_f = $this->conn->prepare("SELECT file_path, file_label FROM document_files WHERE doc_id = ? AND file_label != 'merged_pdf'");
            if ($stmt_f) {
                $stmt_f->bind_param('i', $doc_id);
                $stmt_f->execute();
                $f_res = $stmt_f->get_result();
                $pdfs_to_merge = [];
                $file_slots_by_name = array_column($file_slots, null, 'name');
                while ($f_row = $f_res->fetch_assoc()) {
                    $f_ext = strtolower(pathinfo($f_row['file_path'], PATHINFO_EXTENSION));
                    if (in_array($f_ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif'], true)) {
                        $slot_def = $file_slots_by_name[$f_row['file_label']] ?? null;
                        $title = $slot_def ? $slot_def['label'] : ucfirst(str_replace('_', ' ', $f_row['file_label']));
                        $pdfs_to_merge[] = [
                            'path' => __DIR__ . '/../../' . $f_row['file_path'],
                            'title' => $title
                        ];
                    }
                }
                $stmt_f->close();

                if (count($pdfs_to_merge) > 1) {
                    require_once __DIR__ . '/../../core/pdf_merger_service.php';
                    $safe_type_key = preg_replace('/[^a-z0-9_]/', '', strtolower((string)$type_key));
                    $merged_filename = $safe_type_key . '_merged_' . uniqid() . '.pdf';
                    $merged_dir = __DIR__ . '/../../uploads/' . $safe_type_key . '/';
                    if (!is_dir($merged_dir)) { mkdir($merged_dir, 0755, true); }
                    $merged_path = $merged_dir . $merged_filename;
                    
                    try {
                        if (merge_pdfs_with_headings($pdfs_to_merge, $merged_path)) {
                            $rel_merged_path = 'uploads/' . $safe_type_key . '/' . $merged_filename;
                            
                            $upd_stmt = $this->conn->prepare("UPDATE documents SET file_path = ? WHERE doc_id = ?");
                            if ($upd_stmt) {
                                $upd_stmt->bind_param('si', $rel_merged_path, $doc_id);
                                $upd_stmt->execute();
                                $upd_stmt->close();
                            }
                            self::cleanupPreviousMergedPdf($this->conn, $doc_id);
                            
                            $merged_data = [
                                'original_name' => 'Merged_Document.pdf',
                                'stored_name' => $merged_filename,
                                'file_path' => $rel_merged_path,
                                'mime_type' => 'application/pdf',
                                'file_size' => filesize($merged_path)
                            ];
                            file_save_record($this->conn, $doc_id, $merged_data, 'merged_pdf');
                        }
                    } catch (\Throwable $e) {
                        error_log("PDF Re-Merge Failed: " . $e->getMessage());
                    }
                } elseif (count($pdfs_to_merge) === 1) {
                    $base_root = str_replace('\\', '/', (string)realpath(__DIR__ . '/../../'));
                    $norm_path = str_replace('\\', '/', $pdfs_to_merge[0]['path']);
                    $rel_merged_path = ltrim(str_replace($base_root, '', $norm_path), '/\\');
                    $upd_stmt = $this->conn->prepare("UPDATE documents SET file_path = ? WHERE doc_id = ?");
                    if ($upd_stmt) {
                        $upd_stmt->bind_param('si', $rel_merged_path, $doc_id);
                        $upd_stmt->execute();
                        $upd_stmt->close();
                    }
                    self::cleanupPreviousMergedPdf($this->conn, $doc_id);
                } elseif (count($pdfs_to_merge) === 0) {
                    $upd_stmt = $this->conn->prepare("UPDATE documents SET file_path = NULL WHERE doc_id = ?");
                    if ($upd_stmt) {
                        $upd_stmt->bind_param('i', $doc_id);
                        $upd_stmt->execute();
                        $upd_stmt->close();
                    }
                    self::cleanupPreviousMergedPdf($this->conn, $doc_id);
                }
            }
        }

        if (strtolower($doc['status']) === 'rejected') {
            $resubmit_res = wf_execute_action($this->conn, $doc_id, $this->userId, 'resubmit', 'Author updated and resubmitted the document.');
            if (!$resubmit_res['success']) {
                $wf = wf_get_workflow_for_type($this->conn, (int)$doc['type_id']);
                $first_step = $wf ? wf_get_first_step($this->conn, (int)$wf['workflow_id']) : null;
                $step_id = $first_step ? (int)$first_step['step_id'] : 1;
                $rst = $this->conn->prepare("UPDATE documents SET status = 'pending', current_step = ?, rejection_reason = NULL, updated_at = NOW() WHERE doc_id = ?");
                if ($rst) {
                    $rst->bind_param('ii', $step_id, $doc_id);
                    $rst->execute();
                    $rst->close();
                }

                $log_stmt = $this->conn->prepare("INSERT INTO document_actions (doc_id, action, acted_by, remarks, acted_at) VALUES (?, 'resubmitted', ?, 'Author updated and resubmitted the document.', NOW())");
                if ($log_stmt) {
                    $log_stmt->bind_param('ii', $doc_id, $this->userId);
                    $log_stmt->execute();
                    $log_stmt->close();
                }
            }
            $this->flashSuccess("Document updated and resubmitted for review successfully.");
        } elseif (strtolower($doc['status']) === 'draft') {
            $init_res = wf_initialize_document($this->conn, $doc_id, (int)$doc['type_id'], $this->userId);
            if (!$init_res['success']) {
                $wf = wf_get_workflow_for_type($this->conn, (int)$doc['type_id']);
                $first_step = $wf ? wf_get_first_step($this->conn, (int)$wf['workflow_id']) : null;
                $step_id = $first_step ? (int)$first_step['step_id'] : 1;
                $rst = $this->conn->prepare("UPDATE documents SET status = 'pending', current_step = ?, updated_at = NOW() WHERE doc_id = ?");
                if ($rst) {
                    $rst->bind_param('ii', $step_id, $doc_id);
                    $rst->execute();
                    $rst->close();
                }
            }
            $log_stmt = $this->conn->prepare("INSERT INTO document_actions (doc_id, action, acted_by, remarks, acted_at) VALUES (?, 'submitted', ?, 'Author completed draft and submitted document for review.', NOW())");
            if ($log_stmt) {
                $log_stmt->bind_param('ii', $doc_id, $this->userId);
                $log_stmt->execute();
                $log_stmt->close();
            }
            $this->flashSuccess("Draft document updated and submitted for review successfully.");
        } else {
            $log_stmt = $this->conn->prepare("INSERT INTO document_actions (doc_id, action, acted_by, remarks, acted_at) VALUES (?, 'updated', ?, 'Author updated the document.', NOW())");
            if ($log_stmt) {
                $log_stmt->bind_param('ii', $doc_id, $this->userId);
                $log_stmt->execute();
                $log_stmt->close();
            }
            $this->flashSuccess("Document updated successfully.");
        }
        $this->redirect(BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}");
    }

    public function bulkAction(): void {
        $this->requireLogin();
        $this->validateCsrf();

        $action_type = $this->stringParam('action_type', '', 'post');
        $doc_ids = $_POST['doc_ids'] ?? [];

        if (empty($doc_ids) || !is_array($doc_ids)) {
            $this->flashError("No documents selected for bulk action.");
            $this->redirectBack();
        }

        // Validate permissions for all selected documents
        $valid_docs = [];
        foreach ($doc_ids as $id) {
            $doc = doc_get($this->conn, (int)$id);
            if ($doc && doc_can_view($this->conn, $this->auth, $doc)) {
                $valid_docs[] = $doc;
            }
        }

        if (empty($valid_docs)) {
            $this->flashError("You do not have permission to download the selected documents.");
            $this->redirectBack();
        }

        if ($action_type === 'zip') {
            $this->downloadAsZip($valid_docs);
        } elseif ($action_type === 'merge_pdf') {
            $this->mergeAsPdf($valid_docs);
        } elseif ($action_type === 'merge_accreditation_pdf') {
            $this->mergeAsAccreditationPdf($valid_docs);
        } else {
            $this->flashError("Invalid bulk action.");
            $this->redirectBack();
        }
    }

    private function downloadAsZip(array $valid_docs): void {
        $zip = new \ZipArchive();
        $zipName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'FMS_Bulk_Download_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.zip';

        if ($zip->open($zipName, \ZipArchive::CREATE) !== TRUE) {
            $this->flashError("Could not create ZIP file.");
            $this->redirectBack();
        }

        $hasFiles = false;
        $base_dir = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2);
        $allowed_base = realpath($base_dir . '/uploads');
        $canonical_base = $allowed_base ? (rtrim($allowed_base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR) : null;

        foreach ($valid_docs as $doc) {
            $doc_id = (int)$doc['doc_id'];
            $doc_title = is_string($doc['title'] ?? null) ? $doc['title'] : 'doc';
            $doc_folder = preg_replace('/[^a-zA-Z0-9]/', '_', $doc_title) . '_' . $doc_id;

            $stmt = $this->conn->prepare("SELECT file_path, original_name FROM document_files WHERE doc_id = ?");
            if ($stmt) {
                $stmt->bind_param('i', $doc_id);
                $stmt->execute();
                $res = $stmt->get_result();

                while ($row = $res->fetch_assoc()) {
                    $path = file_exists($row['file_path']) ? $row['file_path'] : ($base_dir . '/' . ltrim($row['file_path'], '/\\'));
                    $real_file = realpath($path);
                    if ($real_file && $canonical_base && stripos($real_file, $canonical_base) === 0 && is_file($real_file)) {
                        $safe_orig_name = str_replace(['"', ';', "\r", "\n"], '', basename((string)($row['original_name'] ?? '')));
                        $zip->addFile($real_file, $doc_folder . '/' . ($safe_orig_name ?: 'file_' . $doc_id));
                        $hasFiles = true;
                    }
                }
                $stmt->close();
            }

            // Fallback: Check main document file_path if present
            if (!empty($doc['file_path'])) {
                $mainPath = $base_dir . '/' . ltrim($doc['file_path'], '/\\');
                $real_main = realpath($mainPath);
                if ($real_main && $canonical_base && stripos($real_main, $canonical_base) === 0 && is_file($real_main)) {
                    $zip->addFile($real_main, $doc_folder . '/' . basename($real_main));
                    $hasFiles = true;
                }
            }
        }

        $zip->close();

        if (!$hasFiles) {
            if (file_exists($zipName)) {
                @unlink($zipName);
            }
            $this->flashError("None of the selected documents have valid files attached.");
            $this->redirectBack();
        }

        file_stream_and_cleanup($zipName, 'FMS_Bulk_Download.zip', 'application/zip', false);
    }

    private function mergeAsPdf(array $valid_docs): void {
        require_once __DIR__ . '/../../core/pdf_merger_service.php';
        
        $pdfs_to_merge = [];
        $supported_exts = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
        $base_dir = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2);
        $allowed_base = realpath($base_dir . '/uploads');

        foreach ($valid_docs as $doc) {
            $doc_id = (int)($doc['doc_id'] ?? 0);
            $attached_items = [];

            // 1. Query individual source files from document_files
            $stmt = $this->conn->prepare("SELECT file_path, file_label FROM document_files WHERE doc_id = ? AND file_label != 'merged_pdf' ORDER BY file_id ASC");
            if ($stmt) {
                $stmt->bind_param('i', $doc_id);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc()) {
                    $path = file_exists($row['file_path']) ? $row['file_path'] : ($base_dir . '/' . ltrim($row['file_path'], '/\\'));
                    $real_path = realpath($path);
                    $att_ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    if ($real_path && $allowed_base && stripos($real_path, $allowed_base) === 0 && in_array($att_ext, $supported_exts, true)) {
                        $attached_items[] = [
                            'path' => $real_path,
                            'title' => ($doc['title'] ?: $doc['type_label']) . ' - ' . ucfirst(str_replace('_', ' ', $row['file_label']))
                        ];
                    }
                }
                $stmt->close();
            }

            if (!empty($attached_items)) {
                foreach ($attached_items as $item) {
                    $pdfs_to_merge[] = $item;
                }
            } else {
                // Fallback: main file_path
                $main_file_path = $doc['file_path'] ?? '';
                $full_path = file_exists($main_file_path) ? $main_file_path : ($base_dir . '/' . ltrim($main_file_path, '/\\'));
                $real_main = realpath($full_path);
                $main_ext = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
                if (!empty($main_file_path) && $real_main && $allowed_base && stripos($real_main, $allowed_base) === 0 && in_array($main_ext, $supported_exts, true)) {
                    $pdfs_to_merge[] = [
                        'path' => $real_main,
                        'title' => $doc['title'] ?: $doc['type_label']
                    ];
                }
            }
        }

        if (empty($pdfs_to_merge)) {
            $this->flashError("None of the selected documents contain valid PDF or image files to merge.");
            $this->redirectBack();
        }

        $merged_path = sys_get_temp_dir() . '/Bulk_Merged_' . uniqid() . '.pdf';

        try {
            if (merge_pdfs_with_headings($pdfs_to_merge, $merged_path)) {
                file_stream_and_cleanup($merged_path, 'FMS_Bulk_Merged.pdf', 'application/pdf', true);
            } else {
                if (file_exists($merged_path)) {
                    @unlink($merged_path);
                }
                $this->flashError("Failed to merge PDFs.");
                $this->redirectBack();
            }
        } catch (\Throwable $e) {
            error_log("PDF Merge Exception: " . $e->getMessage());
            if (file_exists($merged_path)) {
                @unlink($merged_path);
            }
            $is_debug = defined('FMS_DEBUG') && FMS_DEBUG;
            $this->flashError($is_debug ? ("PDF Merge Error: " . $e->getMessage()) : "Failed to merge PDFs due to an unexpected error. Please try again.");
            $this->redirectBack();
        }
    }

    private function mergeAsAccreditationPdf(array $valid_docs): void {
        @ini_set('memory_limit', '512M');
        @set_time_limit(180);

        require_once __DIR__ . '/../../core/accreditation_pdf_service.php';
        require_once __DIR__ . '/../../core/pdf_merger_service.php';

        $headings = [
            'criterion' => $this->stringParam('criterion', 'Criterion V - Student Support and Progression', 'post'),
            'key_indicator' => $this->stringParam('key_indicator', 'Key Indicator - 5.1 Student Support', 'post'),
            'metric' => $this->stringParam('metric', '', 'post'),
            'sub_heading' => $this->stringParam('sub_heading', '', 'post')
        ];

        $selected_columns = $_POST['columns'] ?? ['sno', 'event_name', 'year', 'student_count', 'agency', 'page_no'];
        if (!is_array($selected_columns)) {
            $selected_columns = is_string($selected_columns) ? explode(',', $selected_columns) : ['sno', 'event_name', 'year', 'student_count', 'agency', 'page_no'];
        }

        $doc_custom_data = isset($_POST['doc_data']) && is_array($_POST['doc_data']) ? $_POST['doc_data'] : [];
        $temp_cleanup_registry = [];

        $docs_to_merge = [];
        foreach ($valid_docs as $doc) {
            $id = (int)$doc['doc_id'];

            $meta = [];
            $stmt = $this->conn->prepare("
                SELECT msaf.*, y.year_label 
                FROM documents d
                LEFT JOIN academic_years y ON d.academic_year_id = y.year_id
                LEFT JOIN meta_student_activity_file msaf ON d.doc_id = msaf.doc_id
                WHERE d.doc_id = ?
            ");
            if ($stmt) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $meta = $stmt->get_result()->fetch_assoc() ?: [];
                $stmt->close();
            }

            $custom = $doc_custom_data[$id] ?? [];
            $eventName = !empty($custom['event_name']) ? trim($custom['event_name']) : (!empty($meta['event_title']) ? $meta['event_title'] : $doc['title']);
            $year = !empty($custom['year']) ? trim($custom['year']) : ($meta['year_label'] ?? ($doc['academic_year'] ?? ''));
            $studentCount = isset($custom['student_count']) && $custom['student_count'] !== '' ? (int)$custom['student_count'] : (!empty($meta['participant_count']) ? (int)$meta['participant_count'] : 0);
            $agency = isset($custom['agency']) ? trim($custom['agency']) : (!empty($meta['resource_person']) ? $meta['resource_person'] : '');
            $eventDate = !empty($custom['event_date']) ? trim($custom['event_date']) : (!empty($meta['date_from']) ? $meta['date_from'] : '');
            $dateTo = $meta['date_to'] ?? '';

            $resolvedFilePath = '';

            // 1. Prioritize individual source files first
            $fstmt = $this->conn->prepare("SELECT file_path, file_label FROM document_files WHERE doc_id = ? AND file_label != 'merged_pdf' ORDER BY file_id ASC");
            if ($fstmt) {
                $fstmt->bind_param('i', $id);
                $fstmt->execute();
                $fres = $fstmt->get_result();
                $attached_files = [];
                while ($frow = $fres->fetch_assoc()) {
                    $rawFilePath = ltrim($frow['file_path'], '/\\');
                    $p = __DIR__ . '/../../' . $rawFilePath;
                    if (file_exists($p)) {
                        $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                        if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif'], true)) {
                            $attached_files[] = ['path' => $p, 'title' => $frow['file_label']];
                        }
                    }
                }
                $fstmt->close();

                if (count($attached_files) > 1) {
                    $tempMerged = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'doc_' . $id . '_compiled_' . uniqid() . '.pdf';
                    try {
                        if (merge_pdfs_with_headings($attached_files, $tempMerged)) {
                            $resolvedFilePath = $tempMerged;
                            $temp_cleanup_registry[] = $tempMerged;
                        }
                    } catch (\Throwable $e) {
                        $resolvedFilePath = $attached_files[0]['path'] ?? '';
                    }
                } elseif (count($attached_files) === 1) {
                    $resolvedFilePath = $attached_files[0]['path'];
                }
            }

            // 2. Fallback: Try document's main file_path
            if (empty($resolvedFilePath)) {
                $rawDocPath = ltrim($doc['file_path'] ?? '', '/\\');
                $mainPath = __DIR__ . '/../../' . $rawDocPath;
                if (!empty($rawDocPath) && file_exists($mainPath)) {
                    $resolvedFilePath = $mainPath;
                }
            }

            // 3. Fallback: Check document_files for merged_pdf record
            if (empty($resolvedFilePath)) {
                $mstmt = $this->conn->prepare("SELECT file_path FROM document_files WHERE doc_id = ? AND file_label = 'merged_pdf' LIMIT 1");
                if ($mstmt) {
                    $mstmt->bind_param('i', $id);
                    $mstmt->execute();
                    $mres = $mstmt->get_result()->fetch_assoc();
                    $mstmt->close();

                    if (!empty($mres['file_path'])) {
                        $p = __DIR__ . '/../../' . ltrim($mres['file_path'], '/\\');
                        if (file_exists($p)) {
                            $resolvedFilePath = $p;
                        }
                    }
                }
            }

            $docs_to_merge[] = [
                'doc_id' => $id,
                'event_name' => $eventName,
                'year' => $year,
                'student_count' => $studentCount,
                'agency' => $agency,
                'event_date' => $eventDate,
                'date_from' => $eventDate,
                'date_to' => $dateTo,
                'file_path' => $resolvedFilePath
            ];
        }

        if (empty($docs_to_merge)) {
            foreach ($temp_cleanup_registry as $tmp_file) {
                if (file_exists($tmp_file)) {
                    @unlink($tmp_file);
                }
            }
            $this->flashError("No valid documents found to merge.");
            $this->redirectBack();
        }

        $service = new \AccreditationPdfService();
        $merged_filename = 'Accreditation_Report_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.pdf';
        $merged_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $merged_filename;
        $temp_cleanup_registry[] = $merged_path;

        try {
            $service->generateReport($headings, $selected_columns, $docs_to_merge, $merged_path);
            $safe_merged_name = str_replace(['"', ';', "\r", "\n"], '', basename($merged_filename));
            file_stream_and_cleanup($merged_path, $safe_merged_name, 'application/pdf', true, $temp_cleanup_registry);
        } catch (\Throwable $e) {
            error_log("Accreditation PDF Generation Exception: " . $e->getMessage());
            foreach ($temp_cleanup_registry as $tmpF) {
                if (file_exists($tmpF)) {
                    @unlink($tmpF);
                }
            }
            $is_debug = defined('FMS_DEBUG') && FMS_DEBUG;
            $this->flashError($is_debug ? ("Accreditation PDF Generation Error: " . $e->getMessage()) : "Failed to generate accreditation PDF due to an unexpected error. Please try again.");
            $this->redirectBack();
        }
    }

    public function delete(): void {
        $this->requireLogin();
        $this->validateCsrf();

        $doc_id = $this->intParam('doc_id') ?: $this->intParam('id');
        if ($doc_id <= 0) {
            $this->flashError("Invalid document ID specified.");
            $this->redirectBack();
            return;
        }

        $res = doc_delete($this->conn, $doc_id, $this->auth);
        if ($res['success']) {
            $doc_title = $res['doc']['title'] ?? ('Document #' . $doc_id);
            $files_cnt = $res['files_deleted'] ?? 0;
            $storage_info = ($files_cnt > 0) ? " ({$files_cnt} physical storage file(s) purged)" : "";
            $this->flashSuccess("Document #{$doc_id} ('{$doc_title}') has been permanently deleted{$storage_info}.");
        } else {
            $this->flashError($res['error'] ?? "Failed to delete document.");
        }

        $this->redirectBack();
    }

    private static function cleanupPreviousMergedPdf(\mysqli $conn, int $doc_id): void {
        $del_merge_stmt = $conn->prepare("SELECT file_path FROM document_files WHERE doc_id = ? AND file_label = 'merged_pdf'");
        if ($del_merge_stmt) {
            $del_merge_stmt->bind_param('i', $doc_id);
            $del_merge_stmt->execute();
            $res_merge_old = $del_merge_stmt->get_result();
            $base_dir = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2);
            while ($old_row = $res_merge_old->fetch_assoc()) {
                $old_path = $base_dir . '/' . ltrim($old_row['file_path'], '/\\');
                if (file_exists($old_path) && is_file($old_path)) {
                    @unlink($old_path);
                }
            }
            $del_merge_stmt->close();
        }

        $del_merge_stmt2 = $conn->prepare("DELETE FROM document_files WHERE doc_id = ? AND file_label = 'merged_pdf'");
        if ($del_merge_stmt2) {
            $del_merge_stmt2->bind_param('i', $doc_id);
            $del_merge_stmt2->execute();
            $del_merge_stmt2->close();
        }
    }

    private static function resolveValidAcademicYearId(\mysqli $conn, mixed $raw_year): ?int {
        if (!empty($raw_year)) {
            $raw_str = trim((string)$raw_year);
            if (ctype_digit($raw_str)) {
                $id = (int)$raw_str;
                $stmt = $conn->prepare("SELECT year_id FROM academic_years WHERE year_id = ?");
                if ($stmt) {
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $res = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                    if ($res) {
                        return (int)$res['year_id'];
                    }
                }
            }
            $stmt = $conn->prepare("SELECT year_id FROM academic_years WHERE year_label = ?");
            if ($stmt) {
                $stmt->bind_param('s', $raw_str);
                $stmt->execute();
                $res = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                if ($res) {
                    return (int)$res['year_id'];
                }
            }
        }
        $res = $conn->query("SELECT year_id FROM academic_years ORDER BY year_label DESC LIMIT 1");
        if ($res && $row = $res->fetch_assoc()) {
            return (int)$row['year_id'];
        }
        return null;
    }
}
