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
        
        $post_type_key = trim($_POST['type_key'] ?? '');
            $post_title    = trim($_POST['title'] ?? '');
            
            // Fallback for title if it's missing but meta title fields exist
            if ($post_title === '') {
                if (!empty($_POST['meta']['event_name'])) {
                    $post_title = trim($_POST['meta']['event_name']);
                } elseif (!empty($_POST['meta']['paper_title'])) {
                    $post_title = trim($_POST['meta']['paper_title']);
                } elseif (!empty($_POST['meta']['patent_title'])) {
                    $post_title = trim($_POST['meta']['patent_title']);
                } elseif (!empty($_POST['meta']['exam'])) {
                    $post_title = trim($_POST['meta']['exam']);
                } else {
                    $post_title = 'Untitled Document';
                }
            }

            $post_dept_id  = (int)($_POST['dept_id'] ?? 0);
            $post_year_id  = !empty($_POST['year_id']) ? (int)$_POST['year_id'] : null;

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
                foreach ($auth['roles'] as $role) {
                    if ((int)$role['role_id'] === ROLE_STUDENT) {
                        $is_student = true;
                        break;
                    }
                }

                if ($is_student) {
                    $mentor_per_no = trim($_POST['mentor_per_no'] ?? '');
                    if ($mentor_per_no === '') {
                        $error_msg = 'Mentor Per No is required for student uploads.';
                    } else {
                        // CRITICAL FIX: Ensure the resolved Mentor is actually a Faculty member (Role 4) in the same department!
                        $mentor_stmt = $conn->prepare("
                            SELECT up.user_id 
                            FROM user_profiles up
                            JOIN user_roles ur ON up.user_id = ur.user_id
                            WHERE up.per_no = ? AND ur.role_id = 4 AND ur.dept_id = ?
                        ");
                        $mentor_stmt->bind_param("si", $mentor_per_no, $post_dept_id);
                        $mentor_stmt->execute();
                        $mentor_res = $mentor_stmt->get_result();
                        if ($mentor_row = $mentor_res->fetch_assoc()) {
                            $mentor_id = (int)$mentor_row['user_id'];
                        } else {
                            $error_msg = 'Invalid Mentor Per No. No Faculty found with this Per No in your department.';
                        }
                        $mentor_stmt->close();
                    }
                }

                if (empty($error_msg)) {
                    $dept_authorized = false;
                foreach ($auth['roles'] as $role) {
                    if ((int)$role['dept_id'] === $post_dept_id) {
                        $dept_authorized = true;
                        break;
                    }
                }

                foreach ($auth['roles'] as $role) {
                    if (in_array((int)$role['role_id'], [ROLE_ADMIN, ROLE_RND_DEAN], true)) {
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
            } else {
                header("Location: " . BASE_URL . "/public/index.php?route=documents/upload&type=" . urlencode($post_type_key));
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
            die("Invalid request method.");
        }

        if (function_exists('csrfValidate')) {
            csrfValidate();
        }

        $doc_id = isset($_POST['doc_id']) ? (int)$_POST['doc_id'] : 0;
        $action = isset($_POST['action']) ? trim($_POST['action']) : '';
        $comments = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';

        if ($doc_id <= 0 || !in_array($action, ['approve', 'reject', 'resubmit'], true)) {
            die("Invalid request parameters.");
        }

        $doc = doc_get($conn, $doc_id);
        if (!$doc) {
            die("Document not found.");
        }

        if (!doc_can_approve($conn, $auth, $doc)) {
            die("You do not have permission to review this document.");
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
            die("Invalid file ID.");
        }

        // Fetch file record
        $stmt = $conn->prepare("SELECT doc_id, file_path, original_name as original_filename, file_size, mime_type FROM document_files WHERE file_id = ?");
        $stmt->bind_param('i', $file_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $file_record = $res->fetch_assoc();
        $stmt->close();

        if (!$file_record) {
            die("File not found.");
        }

        // Check document access
        $doc = doc_get($conn, $file_record['doc_id']);
        if (!$doc) {
            die("Associated document not found.");
        }

        if (!doc_can_view($conn, $auth, $doc)) {
            die("You do not have permission to download this file.");
        }

        $real_path = __DIR__ . '/../../' . $file_record['file_path'];
        if (!file_exists($real_path)) {
            die("The physical file is missing from the server.");
        }

        // Stream file
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        $is_inline = !empty($_GET['inline']);
        $disposition = $is_inline ? 'inline' : 'attachment';

        header('Content-Description: File Transfer');
        header('Content-Type: ' . ($file_record['mime_type'] ?: 'application/octet-stream'));
        header('Content-Disposition: ' . $disposition . '; filename="' . basename($file_record['original_filename']) . '"');
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
            die("Invalid document ID.");
        }

        $doc = doc_get($conn, $doc_id);
        if (!$doc) {
            die("Document not found.");
        }

        if ((int)$doc['uploaded_by'] !== (int)$auth['user_id'] || strtolower($doc['status']) === 'accepted') {
            die("Unauthorized to edit this document.");
        }

        if (strtolower($doc['status']) === 'pending') {
            $history = doc_get_history($conn, $doc_id);
            foreach ($history as $h) {
                if ($h['action'] === 'approve') {
                    die("Cannot edit a document that is already partially approved. Please request a rejection first.");
                }
            }
        }

        $type_key = $doc['type_key'];
        
        // Update documents table (title, academic_year, dept)
        $post_title = trim($_POST['title'] ?? '');
        $post_dept_id = (int)($_POST['dept_id'] ?? 0);
        $post_year_id = !empty($_POST['year_id']) ? (int)$_POST['year_id'] : (!empty($_POST['academic_year_id']) ? (int)$_POST['academic_year_id'] : 0);
        
        if ($post_dept_id > 0 && validate_dept_filter($post_dept_id) === null) {
            die("Unauthorized department selection.");
        }

        if ($post_title !== '' && $post_dept_id > 0) {
            $stmt = $conn->prepare("UPDATE documents SET title = ?, dept_id = ?, academic_year_id = ?, updated_at = NOW() WHERE doc_id = ?");
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
                    $stmt_old->bind_param('is', $doc_id, $slot_name);
                    $stmt_old->execute();
                    $res_old = $stmt_old->get_result();
                    while ($old_row = $res_old->fetch_assoc()) {
                        $old_path = __DIR__ . '/../../' . $old_row['file_path'];
                        if (file_exists($old_path) && is_file($old_path)) {
                            unlink($old_path);
                        }
                    }
                    $stmt_old->close();

                    // Delete old file record for this slot
                    $del_stmt = $conn->prepare("DELETE FROM document_files WHERE doc_id = ? AND file_label = ?");
                    $del_stmt->bind_param('is', $doc_id, $slot_name);
                    $del_stmt->execute();
                    $del_stmt->close();

                    // Save new file record
                    file_save_record($conn, $doc_id, $store_result['data'], $slot_name);
                    $replaced_any_file = true;
                }
            }
        }

        if ($replaced_any_file) {
            // Re-merge PDFs if multiple exist
            $stmt_f = $conn->prepare("SELECT file_path, file_label FROM document_files WHERE doc_id = ? AND file_label != 'merged_pdf'");
            $stmt_f->bind_param('i', $doc_id);
            $stmt_f->execute();
            $f_res = $stmt_f->get_result();
            $pdfs_to_merge = [];
            $file_slots_by_name = array_column($file_slots, null, 'name');
            while ($f_row = $f_res->fetch_assoc()) {
                if (strtolower(pathinfo($f_row['file_path'], PATHINFO_EXTENSION)) === 'pdf') {
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
                $merged_filename = $type_key . '_merged_' . uniqid() . '.pdf';
                $merged_dir = __DIR__ . '/../../uploads/' . preg_replace('/[^a-z0-9_]/', '', $type_key) . '/';
                if (!is_dir($merged_dir)) { mkdir($merged_dir, 0755, true); }
                $merged_path = $merged_dir . $merged_filename;
                
                try {
                    if (merge_pdfs_with_headings($pdfs_to_merge, $merged_path)) {
                        $rel_merged_path = 'uploads/' . preg_replace('/[^a-z0-9_]/', '', $type_key) . '/' . $merged_filename;
                        
                        $upd_stmt = $conn->prepare("UPDATE documents SET file_path = ? WHERE doc_id = ?");
                        $upd_stmt->bind_param('si', $rel_merged_path, $doc_id);
                        $upd_stmt->execute();
                        $upd_stmt->close();

                        $del_merge_stmt = $conn->prepare("SELECT file_path FROM document_files WHERE doc_id = ? AND file_label = 'merged_pdf'");
                        $del_merge_stmt->bind_param('i', $doc_id);
                        $del_merge_stmt->execute();
                        $res_merge_old = $del_merge_stmt->get_result();
                        while ($old_row = $res_merge_old->fetch_assoc()) {
                            $old_path = __DIR__ . '/../../' . $old_row['file_path'];
                            if (file_exists($old_path) && is_file($old_path)) {
                                unlink($old_path);
                            }
                        }
                        $del_merge_stmt->close();

                        $del_merge_stmt2 = $conn->prepare("DELETE FROM document_files WHERE doc_id = ? AND file_label = 'merged_pdf'");
                        $del_merge_stmt2->bind_param('i', $doc_id);
                        $del_merge_stmt2->execute();
                        $del_merge_stmt2->close();
                        
                        $merged_data = [
                            'original_name' => 'Merged_Document.pdf',
                            'stored_name' => $merged_filename,
                            'file_path' => $rel_merged_path,
                            'mime_type' => 'application/pdf',
                            'file_size' => filesize($merged_path)
                        ];
                        file_save_record($conn, $doc_id, $merged_data, 'merged_pdf');
                    }
                } catch (\Exception $e) {
                    error_log("PDF Re-Merge Failed: " . $e->getMessage());
                }
            } elseif (count($pdfs_to_merge) === 1) {
                $rel_merged_path = str_replace(__DIR__ . '/../../', '', $pdfs_to_merge[0]['path']);
                $upd_stmt = $conn->prepare("UPDATE documents SET file_path = ? WHERE doc_id = ?");
                $upd_stmt->bind_param('si', $rel_merged_path, $doc_id);
                $upd_stmt->execute();
                $upd_stmt->close();
            }
        }

        $log_stmt = $conn->prepare("INSERT INTO document_actions (doc_id, action, acted_by, remarks, acted_at) VALUES (?, 'resubmitted', ?, 'Author updated the document.', NOW())");
        $log_stmt->bind_param('ii', $doc_id, $auth['user_id']);
        $log_stmt->execute();
        $log_stmt->close();

        $_SESSION['success_msg'] = "Document updated successfully.";
        header("Location: " . BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}");
        exit;
    }

    public function bulkAction() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        csrfValidate();
        
        $auth = auth_context();
        global $conn;

        $action_type = $_POST['action_type'] ?? '';
        $doc_ids = $_POST['doc_ids'] ?? [];

        if (empty($doc_ids) || !is_array($doc_ids)) {
            $_SESSION['error_msg'] = "No documents selected for bulk action.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
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
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }

        if ($action_type === 'zip') {
            $this->downloadAsZip($valid_docs, $conn);
        } elseif ($action_type === 'merge_pdf') {
            $this->mergeAsPdf($valid_docs, $conn);
        } else {
            $_SESSION['error_msg'] = "Invalid bulk action.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    private function downloadAsZip($valid_docs, $conn) {
        $zip = new \ZipArchive();
        $zipName = sys_get_temp_dir() . '/FMS_Bulk_Download_' . time() . '.zip';

        if ($zip->open($zipName, \ZipArchive::CREATE) !== TRUE) {
            die("Could not create ZIP file.");
        }

        $hasFiles = false;
        foreach ($valid_docs as $doc) {
            $stmt = $conn->prepare("SELECT file_path, original_name FROM document_files WHERE doc_id = ?");
            $doc_id = $doc['doc_id'];
            $stmt->bind_param('i', $doc_id);
            $stmt->execute();
            $res = $stmt->get_result();
            
            $doc_folder = preg_replace('/[^a-zA-Z0-9]/', '_', $doc['title']) . '_' . $doc_id;

            while ($row = $res->fetch_assoc()) {
                $path = __DIR__ . '/../../' . $row['file_path'];
                if (file_exists($path) && is_file($path)) {
                    $zip->addFile($path, $doc_folder . '/' . $row['original_name']);
                    $hasFiles = true;
                }
            }
            $stmt->close();
        }

        $zip->close();

        if (!$hasFiles) {
            unlink($zipName);
            die("None of the selected documents have valid files attached.");
        }

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=FMS_Bulk_Download.zip');
        header('Content-Length: ' . filesize($zipName));
        readfile($zipName);
        unlink($zipName);
        exit;
    }

    private function mergeAsPdf($valid_docs, $conn) {
        require_once __DIR__ . '/../../core/pdf_merger_service.php';
        
        $pdfs_to_merge = [];
        foreach ($valid_docs as $doc) {
            // Prefer the main merged/compiled PDF for the document if available
            $main_file_path = $doc['file_path'] ?? '';
            $full_path = __DIR__ . '/../../' . $main_file_path;
            
            if (!empty($main_file_path) && file_exists($full_path) && strtolower(pathinfo($full_path, PATHINFO_EXTENSION)) === 'pdf') {
                $pdfs_to_merge[] = [
                    'path' => $full_path,
                    'title' => $doc['title'] ?: $doc['type_label']
                ];
            } else {
                // Fallback: Check individual PDF files if main file is not PDF
                $stmt = $conn->prepare("SELECT file_path, file_label FROM document_files WHERE doc_id = ? AND file_label != 'merged_pdf'");
                $doc_id = $doc['doc_id'];
                $stmt->bind_param('i', $doc_id);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc()) {
                    $path = __DIR__ . '/../../' . $row['file_path'];
                    if (file_exists($path) && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf') {
                        $pdfs_to_merge[] = [
                            'path' => $path,
                            'title' => ($doc['title'] ?: $doc['type_label']) . ' - ' . ucfirst(str_replace('_', ' ', $row['file_label']))
                        ];
                    }
                }
                $stmt->close();
            }
        }

        if (empty($pdfs_to_merge)) {
            die("None of the selected documents contain valid PDF files to merge.");
        }

        $merged_filename = 'Bulk_Merged_' . uniqid() . '.pdf';
        $merged_path = sys_get_temp_dir() . '/' . $merged_filename;

        try {
            if (merge_pdfs_with_headings($pdfs_to_merge, $merged_path)) {
                header('Content-Type: application/pdf');
                header('Content-disposition: attachment; filename=FMS_Bulk_Merged.pdf');
                header('Content-Length: ' . filesize($merged_path));
                readfile($merged_path);
                unlink($merged_path);
                exit;
            } else {
                die("Failed to merge PDFs.");
            }
        } catch (\Exception $e) {
            die("PDF Merge Error: " . $e->getMessage());
        }
    }
}
