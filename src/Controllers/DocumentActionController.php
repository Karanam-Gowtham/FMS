<?php
namespace App\Controllers;

class DocumentActionController {

    public function processUpload() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        $conn = db_connect();

        $error_msg = '';
        $success_msg = '';

        if (!function_exists('csrfValidate') || !csrfValidate()) {
            $error_msg = 'Invalid security token. Please try again.';
        } else {
            $post_type_key = trim($_POST['type_key'] ?? '');
            $post_title    = trim($_POST['title'] ?? '');
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
                    $doc_id = doc_create($conn, $post_type_key, $post_title, $post_dept_id, $post_year_id, $auth['user_id']);
                    if (!$doc_id) {
                        $error_msg = 'Failed to create document record.';
                    } else {
                        // Handle metadata
                        $meta_fields = meta_get_fields($post_type_key);
                        if (!empty($meta_fields)) {
                            $meta_data = [];
                            foreach ($meta_fields as $field_key => $field_def) {
                                if (isset($_POST["meta_{$field_key}"])) {
                                    $meta_data[$field_key] = trim($_POST["meta_{$field_key}"]);
                                }
                            }
                            if (!empty($meta_data)) {
                                doc_update_metadata($conn, $doc_id, $meta_data);
                            }
                        }

                        // Handle files
                        $file_slots = meta_get_file_slots($post_type_key);
                        if (!empty($file_slots)) {
                            foreach ($file_slots as $slot_key => $slot_def) {
                                $input_name = "file_{$slot_key}";
                                if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] !== UPLOAD_ERR_NO_FILE) {
                                    doc_add_file($conn, $doc_id, $slot_key, $_FILES[$input_name], $auth['user_id']);
                                }
                            }
                        }

                        // Add action history
                        doc_add_history($conn, $doc_id, $auth['user_id'], 'uploaded', "Document uploaded by user.");
                        $success_msg = 'Document uploaded successfully.';
                    }
                }
            }
        }

        // After processing, either redirect on success or re-render form on error
        if ($success_msg) {
            header("Location: " . BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}&success=1");
            exit;
        } else {
            // Put error back into session and redirect back to form
            // For simplicity, redirect back to upload form with error param
            header("Location: " . BASE_URL . "/public/index.php?route=documents/upload&type=" . urlencode($post_type_key) . "&error=" . urlencode($error_msg));
            exit;
        }
    }

    public function approve() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        $conn = db_connect();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("Invalid request method.");
        }

        if (!function_exists('csrfValidate') || !csrfValidate()) {
            die("Invalid security token.");
        }

        $doc_id = isset($_POST['doc_id']) ? (int)$_POST['doc_id'] : 0;
        $action = isset($_POST['action']) ? trim($_POST['action']) : '';
        $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';

        if ($doc_id <= 0 || !in_array($action, ['approve', 'reject'], true)) {
            die("Invalid request parameters.");
        }

        $doc = doc_get_by_id($conn, $doc_id);
        if (!$doc) {
            die("Document not found.");
        }

        if (!doc_can_approve($conn, $auth, $doc)) {
            die("You do not have permission to review this document.");
        }

        $success = false;
        if ($action === 'approve') {
            $success = doc_approve($conn, $doc_id, $auth, $comments);
        } else {
            $success = doc_reject($conn, $doc_id, $auth, $comments);
        }

        if ($success) {
            header("Location: " . BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}&msg=" . urlencode("Action performed successfully."));
            exit;
        } else {
            header("Location: " . BASE_URL . "/public/index.php?route=documents/view&id={$doc_id}&err=" . urlencode("Failed to perform action."));
            exit;
        }
    }

    public function download() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        $conn = db_connect();

        $file_id = isset($_GET['file_id']) ? (int)$_GET['file_id'] : 0;
        if ($file_id <= 0) {
            die("Invalid file ID.");
        }

        // Fetch file record
        $stmt = $conn->prepare("SELECT doc_id, file_path, original_filename, file_size, mime_type FROM document_files WHERE file_id = ?");
        $stmt->bind_param('i', $file_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $file_record = $res->fetch_assoc();
        $stmt->close();

        if (!$file_record) {
            die("File not found.");
        }

        // Check document access
        $doc = doc_get_by_id($conn, $file_record['doc_id']);
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
        header('Content-Description: File Transfer');
        header('Content-Type: ' . ($file_record['mime_type'] ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . basename($file_record['original_filename']) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($real_path));
        
        readfile($real_path);
        exit;
    }
}
