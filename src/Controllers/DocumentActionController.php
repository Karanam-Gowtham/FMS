<?php
namespace App\Controllers;

class DocumentActionController {

    public function processUpload() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        global $conn;

        $error_msg = '';
        $success_msg = '';

        if (function_exists('csrfValidate')) {
            csrfValidate();
        }

        // --- Legacy Custom Handler Intercept Removed ---
        // The student dashboard now uses the standard DocumentController form flow.
        // --- End Custom Handler ---
        
        $raw_type_key  = (isset($_POST['type_key']) && is_string($_POST['type_key'])) ? trim($_POST['type_key']) : '';
        $post_type_key = preg_replace('/[^a-z0-9_]/', '', strtolower($raw_type_key));
        $post_title    = (isset($_POST['title']) && is_string($_POST['title'])) ? trim(str_replace(["\0", "\r", "\n"], '', $_POST['title'])) : '';
            
            // Fallback for title if it's missing but meta title fields exist
            if ($post_title === '') {
                if (!empty($_POST['meta']['event_name']) && is_string($_POST['meta']['event_name'])) {
                    $post_title = trim($_POST['meta']['event_name']);
                } elseif (!empty($_POST['meta']['event_title']) && is_string($_POST['meta']['event_title'])) {
                    $post_title = trim($_POST['meta']['event_title']);
                } elseif (!empty($_POST['meta']['paper_title']) && is_string($_POST['meta']['paper_title'])) {
                    $post_title = trim($_POST['meta']['paper_title']);
                } elseif (!empty($_POST['meta']['patent_title']) && is_string($_POST['meta']['patent_title'])) {
                    $post_title = trim($_POST['meta']['patent_title']);
                } elseif (!empty($_POST['meta']['exam']) && is_string($_POST['meta']['exam'])) {
                    $post_title = trim($_POST['meta']['exam']);
                } elseif ($post_type_key === 'student_activity_file' && (isset($_POST['meta']['activity_category']) && $_POST['meta']['activity_category'] === 'Stepcone')) {
                    $post_title = 'Stepcone Event';
                } else {
                    $post_title = 'Untitled Document';
                }
            }

            $post_dept_id  = (int)($_POST['dept_id'] ?? 0);
            $post_year_id  = self::resolveValidAcademicYearId($conn, $_POST['year_id'] ?? ($_POST['academic_year_id'] ?? null));

            $post_doc_type = doc_get_type_by_key($conn, $post_type_key);
            if (!$post_doc_type) {
                $error_msg = 'Invalid document type.';
            } elseif ($post_title === '') {
                $error_msg = 'Document title is required.';
            } elseif ($post_dept_id <= 0) {
                $error_msg = 'Department is required.';
            } else {
                $mentor_id = null;
                $is_student = false;
                $user_roles = isset($auth['roles']) && is_array($auth['roles']) ? $auth['roles'] : [];
                foreach ($user_roles as $role) {
                    $r_id = is_array($role) ? (int)($role['role_id'] ?? 0) : (int)$role;
                    if ($r_id === ROLE_STUDENT) {
                        $is_student = true;
                        break;
                    }
                }

                if ($is_student) {
                    $mentor_per_no = (isset($_POST['mentor_per_no']) && is_string($_POST['mentor_per_no'])) ? trim($_POST['mentor_per_no']) : '';
                    if ($mentor_per_no === '') {
                        $error_msg = 'Mentor Per No is required for student uploads.';
                    } else {
                        // Ensure the resolved Mentor is a recognized department faculty member (Faculty, HOD, or Coordinator)
                        $mentor_role_ids = implode(',', [ROLE_FACULTY, ROLE_HOD, ROLE_DEPT_COORDINATOR]);
                        $mentor_stmt = $conn->prepare("
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
                        $d_id = is_array($role) ? (int)($role['dept_id'] ?? 0) : (int)($auth['dept_id'] ?? 0);
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

                }

                if (empty($error_msg) && !$dept_authorized) {
                    $error_msg = 'You are not authorized to upload for this department.';
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

                    // Call the fully encapsulated doc_create function
                    $result = doc_create(
                        $conn,
                        $post_type_key,
                        (int)$auth['user_id'],
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

        // After processing, either redirect on success or re-render form on error
        $is_student_activity = (isset($_POST['custom_handler']) && $_POST['custom_handler'] === 'student_activity');

        if ($success_msg) {
            $_SESSION['success_msg'] = $success_msg;
            unset($_SESSION['_old_input']);
            if ($is_student_activity) {
                header("Location: " . BASE_URL . "/public/index.php?route=student/dashboard");
            } else {
                header("Location: " . BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}");
            }
            exit;
        } else {
            $_SESSION['error_msg'] = $error_msg;
            $_SESSION['_old_input'] = $_POST;
            if ($is_student_activity) {
                header("Location: " . BASE_URL . "/public/index.php?route=student/dashboard");
                exit;
            } else {
                $redirect_url = BASE_URL . "/public/index.php?route=documents/upload&type=" . urlencode($post_type_key);
                if (!empty($_POST['meta']['activity_category']) && is_string($_POST['meta']['activity_category'])) {
                    $redirect_url .= "&activity=" . urlencode($_POST['meta']['activity_category']);
                } elseif (!empty($_GET['activity']) && is_string($_GET['activity'])) {
                    $redirect_url .= "&activity=" . urlencode($_GET['activity']);
                }
                if (!empty($_POST['meta']['sub_category']) && is_string($_POST['meta']['sub_category'])) {
                    $redirect_url .= "&sub_type=" . urlencode($_POST['meta']['sub_category']);
                } elseif (!empty($_GET['sub_type']) && is_string($_GET['sub_type'])) {
                    $redirect_url .= "&sub_type=" . urlencode($_GET['sub_type']);
                }
                header("Location: " . $redirect_url);
            }
            exit;
        }
    }

    public function approve() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        global $conn;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_msg'] = "Invalid request method.";
            $this->safeRedirectBack();
        }

        if (function_exists('csrfValidate')) {
            csrfValidate();
        }

        $doc_id = isset($_POST['doc_id']) ? (int)$_POST['doc_id'] : 0;
        $action = (isset($_POST['action']) && is_string($_POST['action'])) ? trim($_POST['action']) : '';
        $comments = (isset($_POST['remarks']) && is_string($_POST['remarks'])) ? trim($_POST['remarks']) : '';

        if ($doc_id <= 0 || !in_array($action, ['approve', 'reject', 'resubmit'], true)) {
            $_SESSION['error_msg'] = "Invalid request parameters.";
            $this->safeRedirectBack();
        }

        if ($action === 'reject' && $comments === '') {
            $_SESSION['error_msg'] = "Remarks are required when rejecting a document.";
            $this->safeRedirectBack(BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}");
        }

        $doc = doc_get($conn, $doc_id);
        if (!$doc) {
            $_SESSION['error_msg'] = "Document not found.";
            $this->safeRedirectBack();
        }

        $allowed_actions = wf_get_allowed_actions($conn, $doc, $auth);
        if (!in_array($action, $allowed_actions, true)) {
            $_SESSION['error_msg'] = "You do not have permission to perform this action on this document.";
            $this->safeRedirectBack(BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}");
        }

        $result = wf_execute_action($conn, $doc_id, (int)$auth['user_id'], $action, $comments);

        if ($result['success']) {
            $status_msg = $result['new_status'] ? " New Status: " . ucfirst($result['new_status']) : "";
            header("Location: " . BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}&msg=" . urlencode("Action performed successfully." . $status_msg));
            exit;
        } else {
            $error_msg = $result['error'] ?? "Failed to perform action.";
            header("Location: " . BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}&err=" . urlencode($error_msg));
            exit;
        }
    }

    public function download() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        global $conn;

        $file_id = isset($_GET['file_id']) ? (int)$_GET['file_id'] : 0;
        if ($file_id <= 0) {
            $_SESSION['error_msg'] = "Invalid file ID.";
            $this->safeRedirectBack();
        }

        // Fetch file record
        $stmt = $conn->prepare("SELECT doc_id, file_path, original_name as original_filename, file_size, mime_type FROM document_files WHERE file_id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $file_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $file_record = $res->fetch_assoc();
            $stmt->close();
        } else {
            $file_record = null;
        }

        if (!$file_record) {
            $_SESSION['error_msg'] = "File not found.";
            $this->safeRedirectBack();
        }

        // Check document access
        $doc = doc_get($conn, $file_record['doc_id']);
        if (!$doc) {
            $_SESSION['error_msg'] = "Associated document not found.";
            $this->safeRedirectBack();
        }

        if (!doc_can_view($conn, $auth, $doc)) {
            $_SESSION['error_msg'] = "You do not have permission to download this file.";
            $this->safeRedirectBack(BASE_URL . "/public/index.php?route=documents/view&id=" . (int)$file_record['doc_id']);
        }

        $raw_file_path = $file_record['file_path'];
        $base_dir = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2);
        $target_file = file_exists($raw_file_path) ? $raw_file_path : ($base_dir . '/' . ltrim($raw_file_path, '/\\'));
        $canonical_path = realpath($target_file);
        $allowed_base = realpath($base_dir . '/uploads');
        $canonical_base = $allowed_base ? (rtrim($allowed_base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR) : null;

        if (!$canonical_path || !$canonical_base || stripos($canonical_path, $canonical_base) !== 0 || !is_file($canonical_path)) {
            $_SESSION['error_msg'] = "The physical file is missing or invalid on the server.";
            $this->safeRedirectBack(BASE_URL . "/public/index.php?route=documents/view&id=" . (int)$file_record['doc_id']);
        }
        $real_path = $canonical_path;

        // Stream file - clean all nested output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $is_inline = !empty($_GET['inline']);
        $disposition = $is_inline ? 'inline' : 'attachment';
        $safe_filename = str_replace(['"', "\r", "\n"], '', basename($file_record['original_filename']));

        header('Content-Description: File Transfer');
        header('X-Content-Type-Options: nosniff');
        header('Content-Type: ' . ($file_record['mime_type'] ?: 'application/octet-stream'));
        header('Content-Disposition: ' . $disposition . '; filename="' . $safe_filename . '"; filename*=UTF-8\'\'' . rawurlencode($safe_filename));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($real_path));
        
        readfile($real_path);
        exit;
    }
    public function processUpdate() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        global $conn;

        if (function_exists('csrfValidate')) {
            csrfValidate();
        }

        $doc_id = isset($_POST['doc_id']) ? (int)$_POST['doc_id'] : 0;
        if (!$doc_id) {
            $_SESSION['error_msg'] = "Invalid document ID.";
            header("Location: " . BASE_URL . "/public/index.php?route=documents/my_uploads");
            exit;
        }

        $doc = doc_get($conn, $doc_id);
        if (!$doc) {
            $_SESSION['error_msg'] = "Document not found.";
            header("Location: " . BASE_URL . "/public/index.php?route=documents/my_uploads");
            exit;
        }

        if (!doc_can_edit($conn, $auth, $doc)) {
            $_SESSION['error_msg'] = "Unauthorized to edit this document.";
            header("Location: " . BASE_URL . "/public/index.php?route=documents/my_uploads");
            exit;
        }

        $type_key = $doc['type_key'];
        
        // Update documents table (title, academic_year, dept)
        $post_title = (isset($_POST['title']) && is_string($_POST['title'])) ? trim($_POST['title']) : '';
        $post_dept_id = (int)($_POST['dept_id'] ?? 0);
        $post_year_id = self::resolveValidAcademicYearId($conn, $_POST['year_id'] ?? ($_POST['academic_year_id'] ?? null));
        
        if ($post_title === '' || $post_dept_id <= 0) {
            $_SESSION['error_msg'] = "Document Title and Department are required.";
            header("Location: " . BASE_URL . "/public/index.php?route=documents/edit&id=" . $doc_id);
            exit;
        }

        if ($post_dept_id > 0 && validate_dept_filter($post_dept_id) === null) {
            $_SESSION['error_msg'] = "Unauthorized department selection.";
            header("Location: " . BASE_URL . "/public/index.php?route=documents/my_uploads");
            exit;
        }

        $stmt = $conn->prepare("UPDATE documents SET title = ?, dept_id = ?, academic_year_id = ?, updated_at = NOW() WHERE doc_id = ?");
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
                    $stmt = $conn->prepare($sql);
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
                    $stmt_old = $conn->prepare("SELECT file_path FROM document_files WHERE doc_id = ? AND file_label = ?");
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
                    $del_stmt = $conn->prepare("DELETE FROM document_files WHERE doc_id = ? AND file_label = ?");
                    if ($del_stmt) {
                        $del_stmt->bind_param('is', $doc_id, $slot_name);
                        $del_stmt->execute();
                        $del_stmt->close();
                    }

                    // Save new file record
                    file_save_record($conn, $doc_id, $store_result['data'], $slot_name);
                    $replaced_any_file = true;
                }
            }
        }

        if ($replaced_any_file) {
            // Re-merge PDFs if multiple exist
            $stmt_f = $conn->prepare("SELECT file_path, file_label FROM document_files WHERE doc_id = ? AND file_label != 'merged_pdf'");
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
                            
                            $upd_stmt = $conn->prepare("UPDATE documents SET file_path = ? WHERE doc_id = ?");
                            if ($upd_stmt) {
                                $upd_stmt->bind_param('si', $rel_merged_path, $doc_id);
                                $upd_stmt->execute();
                                $upd_stmt->close();
                            }
                            self::cleanupPreviousMergedPdf($conn, $doc_id);
                            
                            $merged_data = [
                                'original_name' => 'Merged_Document.pdf',
                                'stored_name' => $merged_filename,
                                'file_path' => $rel_merged_path,
                                'mime_type' => 'application/pdf',
                                'file_size' => filesize($merged_path)
                            ];
                            file_save_record($conn, $doc_id, $merged_data, 'merged_pdf');
                        }
                    } catch (\Throwable $e) {
                        error_log("PDF Re-Merge Failed: " . $e->getMessage());
                    }
                } elseif (count($pdfs_to_merge) === 1) {
                    $base_root = str_replace('\\', '/', (string)realpath(__DIR__ . '/../../'));
                    $norm_path = str_replace('\\', '/', $pdfs_to_merge[0]['path']);
                    $rel_merged_path = ltrim(str_replace($base_root, '', $norm_path), '/\\');
                    $upd_stmt = $conn->prepare("UPDATE documents SET file_path = ? WHERE doc_id = ?");
                    if ($upd_stmt) {
                        $upd_stmt->bind_param('si', $rel_merged_path, $doc_id);
                        $upd_stmt->execute();
                        $upd_stmt->close();
                    }

                    // Clean up any previously generated merged_pdf record and file
                            self::cleanupPreviousMergedPdf($conn, $doc_id);
                } elseif (count($pdfs_to_merge) === 0) {
                    $upd_stmt = $conn->prepare("UPDATE documents SET file_path = NULL WHERE doc_id = ?");
                    if ($upd_stmt) {
                        $upd_stmt->bind_param('i', $doc_id);
                        $upd_stmt->execute();
                        $upd_stmt->close();
                    }
                            self::cleanupPreviousMergedPdf($conn, $doc_id);
                }
            }
        }

        if (strtolower($doc['status']) === 'rejected') {
            $resubmit_res = wf_execute_action($conn, $doc_id, (int)$auth['user_id'], 'resubmit', 'Author updated and resubmitted the document.');
            if (!$resubmit_res['success']) {
                $wf = wf_get_workflow_for_type($conn, (int)$doc['type_id']);
                $first_step = $wf ? wf_get_first_step($conn, (int)$wf['workflow_id']) : null;
                $step_id = $first_step ? (int)$first_step['step_id'] : 1;
                $rst = $conn->prepare("UPDATE documents SET status = 'pending', current_step = ?, rejection_reason = NULL, updated_at = NOW() WHERE doc_id = ?");
                if ($rst) {
                    $rst->bind_param('ii', $step_id, $doc_id);
                    $rst->execute();
                    $rst->close();
                }

                $log_stmt = $conn->prepare("INSERT INTO document_actions (doc_id, action, acted_by, remarks, acted_at) VALUES (?, 'resubmitted', ?, 'Author updated and resubmitted the document.', NOW())");
                if ($log_stmt) {
                    $log_stmt->bind_param('ii', $doc_id, $auth['user_id']);
                    $log_stmt->execute();
                    $log_stmt->close();
                }
            }
            $_SESSION['success_msg'] = "Document updated and resubmitted for review successfully.";
        } elseif (strtolower($doc['status']) === 'draft') {
            $init_res = wf_initialize_document($conn, $doc_id, (int)$doc['type_id'], (int)$auth['user_id']);
            if (!$init_res['success']) {
                $wf = wf_get_workflow_for_type($conn, (int)$doc['type_id']);
                $first_step = $wf ? wf_get_first_step($conn, (int)$wf['workflow_id']) : null;
                $step_id = $first_step ? (int)$first_step['step_id'] : 1;
                $rst = $conn->prepare("UPDATE documents SET status = 'pending', current_step = ?, updated_at = NOW() WHERE doc_id = ?");
                if ($rst) {
                    $rst->bind_param('ii', $step_id, $doc_id);
                    $rst->execute();
                    $rst->close();
                }
            }
            $log_stmt = $conn->prepare("INSERT INTO document_actions (doc_id, action, acted_by, remarks, acted_at) VALUES (?, 'submitted', ?, 'Author completed draft and submitted document for review.', NOW())");
            if ($log_stmt) {
                $log_stmt->bind_param('ii', $doc_id, $auth['user_id']);
                $log_stmt->execute();
                $log_stmt->close();
            }
            $_SESSION['success_msg'] = "Draft document updated and submitted for review successfully.";
        } else {
            $log_stmt = $conn->prepare("INSERT INTO document_actions (doc_id, action, acted_by, remarks, acted_at) VALUES (?, 'updated', ?, 'Author updated the document.', NOW())");
            if ($log_stmt) {
                $log_stmt->bind_param('ii', $doc_id, $auth['user_id']);
                $log_stmt->execute();
                $log_stmt->close();
            }
            $_SESSION['success_msg'] = "Document updated successfully.";
        }
        header("Location: " . BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}");
        exit;
    }

    public function bulkAction() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        csrfValidate();
        
        $auth = auth_context();
        global $conn;

        $action_type = (isset($_POST['action_type']) && is_string($_POST['action_type'])) ? trim($_POST['action_type']) : '';
        $doc_ids = $_POST['doc_ids'] ?? [];

        if (empty($doc_ids) || !is_array($doc_ids)) {
            $_SESSION['error_msg'] = "No documents selected for bulk action.";
            $this->safeRedirectBack();
        }

        // Validate permissions for all selected documents
        $valid_docs = [];
        foreach ($doc_ids as $id) {
            $doc = doc_get($conn, (int)$id);
            if ($doc && doc_can_view($conn, $auth, $doc)) {
                $valid_docs[] = $doc;
            }
        }

        if (empty($valid_docs)) {
            $_SESSION['error_msg'] = "You do not have permission to download the selected documents.";
            $this->safeRedirectBack();
        }

        if ($action_type === 'zip') {
            $this->downloadAsZip($valid_docs, $conn);
        } elseif ($action_type === 'merge_pdf') {
            $this->mergeAsPdf($valid_docs, $conn);
        } elseif ($action_type === 'merge_accreditation_pdf') {
            $this->mergeAsAccreditationPdf($valid_docs, $conn);
        } else {
            $_SESSION['error_msg'] = "Invalid bulk action.";
            $this->safeRedirectBack();
        }
    }

    private function downloadAsZip($valid_docs, $conn) {
        $zip = new \ZipArchive();
        $zipName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'FMS_Bulk_Download_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.zip';

        if ($zip->open($zipName, \ZipArchive::CREATE) !== TRUE) {
            $_SESSION['error_msg'] = "Could not create ZIP file.";
            $this->safeRedirectBack();
        }

        $hasFiles = false;
        $allowed_base = realpath(__DIR__ . '/../../uploads');
        $canonical_base = $allowed_base ? (rtrim($allowed_base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR) : null;

        foreach ($valid_docs as $doc) {
            $doc_id = (int)$doc['doc_id'];
            $doc_title = is_string($doc['title'] ?? null) ? $doc['title'] : 'doc';
            $doc_folder = preg_replace('/[^a-zA-Z0-9]/', '_', $doc_title) . '_' . $doc_id;

            $stmt = $conn->prepare("SELECT file_path, original_name FROM document_files WHERE doc_id = ?");
            if ($stmt) {
                $stmt->bind_param('i', $doc_id);
                $stmt->execute();
                $res = $stmt->get_result();

                $base_dir = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2);
                while ($row = $res->fetch_assoc()) {
                    $path = file_exists($row['file_path']) ? $row['file_path'] : ($base_dir . '/' . ltrim($row['file_path'], '/\\'));
                    $real_file = realpath($path);
                    if ($real_file && $canonical_base && stripos($real_file, $canonical_base) === 0 && is_file($real_file)) {
                        $safe_orig_name = str_replace(['"', ';', "\r", "\n"], '', basename((string)($row['original_name'] ?? '')));
                        if ($safe_orig_name === '') {
                            $safe_orig_name = 'file_' . $doc_id;
                        }
                        $zip->addFile($real_file, $doc_folder . '/' . $safe_orig_name);
                        $hasFiles = true;
                    }
                }
                $stmt->close();
            }

            // Fallback: Check main document file_path if present
            if (!empty($doc['file_path'])) {
                $mainPath = __DIR__ . '/../../' . ltrim($doc['file_path'], '/\\');
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
            $_SESSION['error_msg'] = "None of the selected documents have valid files attached.";
            $this->safeRedirectBack();
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('X-Content-Type-Options: nosniff');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="FMS_Bulk_Download.zip"; filename*=UTF-8\'\'FMS_Bulk_Download.zip');
        $fsize = @filesize($zipName);
        if ($fsize !== false && $fsize >= 0) {
            header('Content-Length: ' . $fsize);
        }
        try {
            readfile($zipName);
        } finally {
            if (file_exists($zipName)) {
                @unlink($zipName);
            }
        }
        exit;
    }

    private function mergeAsPdf($valid_docs, $conn) {
        require_once __DIR__ . '/../../core/pdf_merger_service.php';
        
        $pdfs_to_merge = [];
        $supported_exts = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
        $allowed_base = realpath(__DIR__ . '/../../uploads');

        foreach ($valid_docs as $doc) {
            // Prefer the main merged/compiled PDF for the document if available
            $main_file_path = $doc['file_path'] ?? '';
            $base_dir = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2);
            $full_path = file_exists($main_file_path) ? $main_file_path : ($base_dir . '/' . ltrim($main_file_path, '/\\'));
            $real_main = realpath($full_path);
            
            $main_ext = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
            if (!empty($main_file_path) && $real_main && $allowed_base && stripos($real_main, $allowed_base) === 0 && in_array($main_ext, $supported_exts, true)) {
                $pdfs_to_merge[] = [
                    'path' => $real_main,
                    'title' => $doc['title'] ?: $doc['type_label']
                ];
            } else {
                // Fallback: Check individual PDF and image files if main file is not available
                $stmt = $conn->prepare("SELECT file_path, file_label FROM document_files WHERE doc_id = ? AND file_label != 'merged_pdf'");
                if ($stmt) {
                    $doc_id = $doc['doc_id'];
                    $stmt->bind_param('i', $doc_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    while ($row = $res->fetch_assoc()) {
                        $path = file_exists($row['file_path']) ? $row['file_path'] : ($base_dir . '/' . ltrim($row['file_path'], '/\\'));
                        $real_path = realpath($path);
                        $att_ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                        if ($real_path && $allowed_base && stripos($real_path, $allowed_base) === 0 && in_array($att_ext, $supported_exts, true)) {
                            $pdfs_to_merge[] = [
                                'path' => $real_path,
                                'title' => ($doc['title'] ?: $doc['type_label']) . ' - ' . ucfirst(str_replace('_', ' ', $row['file_label']))
                            ];
                        }
                    }
                    $stmt->close();
                }
            }
        }

        if (empty($pdfs_to_merge)) {
            $_SESSION['error_msg'] = "None of the selected documents contain valid PDF or image files to merge.";
            $this->safeRedirectBack();
        }

        $merged_filename = 'Bulk_Merged_' . uniqid() . '.pdf';
        $merged_path = sys_get_temp_dir() . '/' . $merged_filename;

        try {
            if (merge_pdfs_with_headings($pdfs_to_merge, $merged_path)) {
                while (ob_get_level()) {
                    ob_end_clean();
                }
                header('X-Content-Type-Options: nosniff');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="FMS_Bulk_Merged.pdf"; filename*=UTF-8\'\'FMS_Bulk_Merged.pdf');
                $fsize = @filesize($merged_path);
                if ($fsize !== false && $fsize >= 0) {
                    header('Content-Length: ' . $fsize);
                }
                try {
                    readfile($merged_path);
                } finally {
                    if (file_exists($merged_path)) {
                        @unlink($merged_path);
                    }
                }
                exit;
            } else {
                if (file_exists($merged_path)) {
                    @unlink($merged_path);
                }
                $_SESSION['error_msg'] = "Failed to merge PDFs.";
                $this->safeRedirectBack();
            }
        } catch (\Throwable $e) {
            error_log("PDF Merge Exception: " . $e->getMessage());
            if (file_exists($merged_path)) {
                @unlink($merged_path);
            }
            $is_debug = defined('FMS_DEBUG') && FMS_DEBUG;
            $_SESSION['error_msg'] = $is_debug ? ("PDF Merge Error: " . $e->getMessage()) : "Failed to merge PDFs due to an unexpected error. Please try again.";
            $this->safeRedirectBack();
        }
    }

    private function mergeAsAccreditationPdf($valid_docs, $conn) {
        @ini_set('memory_limit', '512M');
        @set_time_limit(180);

        require_once __DIR__ . '/../../core/accreditation_pdf_service.php';
        require_once __DIR__ . '/../../core/pdf_merger_service.php';

        $headings = [
            'criterion' => (isset($_POST['criterion']) && is_string($_POST['criterion'])) ? trim($_POST['criterion']) : 'Criterion V - Student Support and Progression',
            'key_indicator' => (isset($_POST['key_indicator']) && is_string($_POST['key_indicator'])) ? trim($_POST['key_indicator']) : 'Key Indicator - 5.1 Student Support',
            'metric' => (isset($_POST['metric']) && is_string($_POST['metric'])) ? trim($_POST['metric']) : '',
            'sub_heading' => (isset($_POST['sub_heading']) && is_string($_POST['sub_heading'])) ? trim($_POST['sub_heading']) : ''
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

            // Query DB for complete metadata
            $meta = [];
            $stmt = $conn->prepare("
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

            // Override with custom user inputs from modal preview if present
            $custom = $doc_custom_data[$id] ?? [];
            $eventName = !empty($custom['event_name']) ? trim($custom['event_name']) : (!empty($meta['event_title']) ? $meta['event_title'] : $doc['title']);
            $year = !empty($custom['year']) ? trim($custom['year']) : ($meta['year_label'] ?? ($doc['academic_year'] ?? ''));
            $studentCount = isset($custom['student_count']) && $custom['student_count'] !== '' ? (int)$custom['student_count'] : (!empty($meta['participant_count']) ? (int)$meta['participant_count'] : 0);
            $agency = isset($custom['agency']) ? trim($custom['agency']) : (!empty($meta['resource_person']) ? $meta['resource_person'] : '');
            $eventDate = !empty($custom['event_date']) ? trim($custom['event_date']) : (!empty($meta['date_from']) ? $meta['date_from'] : '');
            $dateTo = $meta['date_to'] ?? '';

            // Find associated proof files (PDFs and Images)
            $resolvedFilePath = '';

            // 1. Check document_files for pre-merged master PDF first
            $mstmt = $conn->prepare("SELECT file_path FROM document_files WHERE doc_id = ? AND file_label = 'merged_pdf' LIMIT 1");
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

            // 2. Try document's main file_path if not resolved
            if (empty($resolvedFilePath)) {
                $rawDocPath = ltrim($doc['file_path'] ?? '', '/\\');
                $mainPath = __DIR__ . '/../../' . $rawDocPath;
                if (!empty($rawDocPath) && file_exists($mainPath)) {
                    $resolvedFilePath = $mainPath;
                }
            }

            // 3. Fallback: Query all attached files (both PDFs and JPG/PNG images)
            if (empty($resolvedFilePath)) {
                $fstmt = $conn->prepare("SELECT file_path, file_label FROM document_files WHERE doc_id = ? ORDER BY file_id ASC");
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
            $_SESSION['error_msg'] = "No valid documents found to merge.";
            $this->safeRedirectBack();
        }

        $service = new \AccreditationPdfService();
        $merged_filename = 'Accreditation_Report_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.pdf';
        $merged_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $merged_filename;
        $temp_cleanup_registry[] = $merged_path;

        try {
            $service->generateReport($headings, $selected_columns, $docs_to_merge, $merged_path);

            while (ob_get_level()) {
                ob_end_clean();
            }

            $safe_merged_name = str_replace(['"', ';', "\r", "\n"], '', basename($merged_filename));
            header('X-Content-Type-Options: nosniff');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $safe_merged_name . '"; filename*=UTF-8\'\'' . rawurlencode($safe_merged_name));
            $fsize = @filesize($merged_path);
            if ($fsize !== false && $fsize >= 0) {
                header('Content-Length: ' . $fsize);
            }
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            try {
                readfile($merged_path);
            } finally {
                // Clean up all temporary files created during this request
                foreach ($temp_cleanup_registry as $tmpF) {
                    if (file_exists($tmpF)) {
                        @unlink($tmpF);
                    }
                }
            }
            exit;
        } catch (\Throwable $e) {
            error_log("Accreditation PDF Generation Exception: " . $e->getMessage());
            foreach ($temp_cleanup_registry as $tmpF) {
                if (file_exists($tmpF)) {
                    @unlink($tmpF);
                }
            }
            $is_debug = defined('FMS_DEBUG') && FMS_DEBUG;
            $_SESSION['error_msg'] = $is_debug ? ("Accreditation PDF Generation Error: " . $e->getMessage()) : "Failed to generate accreditation PDF due to an unexpected error. Please try again.";
            $this->safeRedirectBack();
        }
    }

    private function safeRedirectBack(string $default = ''): void {
        $target = !empty($default) ? $default : '';
        if (empty($target) && !empty($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
            $host = $_SERVER['HTTP_HOST'] ?? '';
            $parsed = parse_url($referer);
            $query = $parsed['query'] ?? '';
            // Avoid looping back to download action on failure
            if (str_contains($query, 'route=documents/download')) {
                $referer = '';
            }
            if (!empty($referer)) {
                if (!empty($parsed['host']) && $parsed['host'] === $host) {
                    $target = $referer;
                } elseif (empty($parsed['host']) && str_starts_with($referer, '/')) {
                    $target = $referer;
                }
            }
        }
        if (empty($target)) {
            $target = BASE_URL . '/public/index.php?route=documents/list';
        }
        header("Location: " . $target);
        exit;
    }

    /**
     * Clean up previously generated merged_pdf file and its database record.
     */
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

    /**
     * Resolves and validates an academic year ID against the academic_years table.
     */
    private static function resolveValidAcademicYearId(\mysqli $conn, mixed $raw_year): ?int {
        if (!empty($raw_year)) {
            $raw_str = trim((string)$raw_year);
            // 1. Check if numeric and matches year_id
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
            // 2. Check if string matches year_label (e.g. '2024-25', '2025-26', '2026-27')
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
        // Fallback: active or latest academic year from DB
        $res = $conn->query("SELECT year_id FROM academic_years ORDER BY year_label DESC LIMIT 1");
        if ($res && $row = $res->fetch_assoc()) {
            return (int)$row['year_id'];
        }
        return null;
    }
}
