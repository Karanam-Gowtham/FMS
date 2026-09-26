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

        // --- Custom Handler Intercept ---
        if (isset($_POST['custom_handler']) && $_POST['custom_handler'] === 'student_activity') {
            $_POST['type_key'] = 'stu_act';
            $_POST['title'] = trim($_POST['event_name'] ?? 'Student Activity');
            $_POST['dept_id'] = $auth['roles'][0]['dept_id'] ?? 0;
            $_POST['year_id'] = $_POST['ay_id'] ?? null;
            
            // Build the JSON payload for event details based on category
            $event_details = [
                'event_name' => trim($_POST['event_name'] ?? ''),
                'event_date' => trim($_POST['event_date'] ?? '')
            ];
            $cat = trim($_POST['activity_category'] ?? '');
            if ($cat === 'Co-Curricular') {
                $event_details['event_type'] = trim($_POST['event_type'] ?? '');
                $event_details['event_title'] = trim($_POST['event_title'] ?? '');
                $event_details['host_institution'] = trim($_POST['host_institution'] ?? '');
                $event_details['level'] = trim($_POST['level'] ?? '');
                $event_details['achievement'] = trim($_POST['achievement'] ?? '');
            } elseif ($cat === 'Sports & Games') {
                $sport = trim($_POST['sport_name'] ?? '');
                if ($sport === 'Other') $sport = trim($_POST['sport_name_other'] ?? '');
                $event_details['sport_name'] = $sport;
                $event_details['host_institution'] = trim($_POST['host_institution'] ?? '');
                $event_details['level'] = trim($_POST['level'] ?? '');
                $event_details['achievement'] = trim($_POST['achievement'] ?? '');
            } elseif ($cat === 'NSS & NCC') {
                $event_details['nss_type'] = trim($_POST['nss_type'] ?? '');
                $event_details['location'] = trim($_POST['location'] ?? '');
                $event_details['duration'] = trim($_POST['duration'] ?? '');
            }

            // Build participants JSON
            $participants = [];
            $names = $_POST['participant_names'] ?? [];
            $jntus = $_POST['participant_jntu'] ?? [];
            foreach ($names as $idx => $name) {
                if (trim($name) !== '') {
                    $participants[] = [
                        'name' => trim($name),
                        'jntu' => trim($jntus[$idx] ?? '')
                    ];
                }
            }

            $_POST['meta'] = [
                'activity_category' => $cat,
                'participation_type' => trim($_POST['participation_type'] ?? 'Individual'),
                'event_details' => $event_details, // will be json encoded by doc_insert_meta
                'participants' => $participants
            ];
        }
        // --- End Custom Handler ---
        
        $post_type_key = trim($_POST['type_key'] ?? '');
            $post_title    = trim($_POST['title'] ?? '');
            
            // Fallback for title if it's a student activity or exam
            if ($post_title === '' && (strpos($post_type_key, 'student_') === 0 || $post_type_key === 'exam_qual')) {
                if (!empty($_POST['meta']['event_name'])) {
                    $post_title = trim($_POST['meta']['event_name']);
                } elseif (!empty($_POST['meta']['paper_title'])) {
                    $post_title = trim($_POST['meta']['paper_title']);
                } elseif (!empty($_POST['meta']['exam'])) {
                    $post_title = trim($_POST['meta']['exam']);
                } else {
                    $post_title = 'Student Activity';
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

                if (!$dept_authorized) {
                    $error_msg = 'You are not authorized to upload for this department.';
                } else {
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
                        $mapped_files
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
            if ($is_student_activity) {
                header("Location: " . BASE_URL . "/public/index.php?route=student/dashboard");
            } else {
                header("Location: " . BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}");
            }
            exit;
        } else {
            $_SESSION['error_msg'] = $error_msg;
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
        $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';

        if ($doc_id <= 0 || !in_array($action, ['approve', 'reject'], true)) {
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

        $type_key = $doc['type_key'];
        
        // Update documents table (title, academic_year, dept)
        $post_title = trim($_POST['title'] ?? '');
        $post_dept_id = (int)($_POST['dept_id'] ?? 0);
        $post_year_id = !empty($_POST['year_id']) ? (int)$_POST['year_id'] : (!empty($_POST['academic_year_id']) ? (int)$_POST['academic_year_id'] : 0);
        
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

                        $del_merge_stmt = $conn->prepare("DELETE FROM document_files WHERE doc_id = ? AND file_label = 'merged_pdf'");
                        $del_merge_stmt->bind_param('i', $doc_id);
                        $del_merge_stmt->execute();
                        $del_merge_stmt->close();
                        
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
            }
        }

        $log_stmt = $conn->prepare("INSERT INTO workflow_history (doc_id, action, actor_id, step_label, remarks, acted_at) VALUES (?, 'updated', ?, 'Document Editor', 'Author updated the document.', NOW())");
        $log_stmt->bind_param('ii', $doc_id, $auth['user_id']);
        $log_stmt->execute();
        $log_stmt->close();

        $_SESSION['success_msg'] = "Document updated successfully.";
        header("Location: " . BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}");
        exit;
    }
}
