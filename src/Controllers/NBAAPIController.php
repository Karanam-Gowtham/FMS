<?php
namespace App\Controllers;

class NBAAPIController {
    public function save() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        header('Content-Type: application/json');

        try {
            require_login();
            $auth = auth_context();
            $active_role = auth_active_role();

            $dept_id = (int)($active_role['dept_id'] ?? 0);
            if ($dept_id <= 0) {
                throw new \Exception("You must have a department assigned to save NBA data.");
            }

            $year = trim($_POST['year'] ?? '');
            if (empty($year)) {
                throw new \Exception("Academic year is required.");
            }
            
            $crit_id = isset($_POST['criterion_number']) ? (int)$_POST['criterion_number'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
            if ($crit_id <= 0) {
                throw new \Exception("Criterion number is required.");
            }

            $json_raw = $_POST['json_data'] ?? '{}';
            $payload = json_decode($json_raw, true) ?: [];

            // CSV Parser specific to Criterion 1 (and potentially others)
            if ($crit_id === 1 && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
                $csv_tmp = $_FILES['csv_file']['tmp_name'];
                if (($handle = fopen($csv_tmp, "r")) !== FALSE) {
                    $parsed_courses = [];
                    $current_course = '';
                    
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        if (empty(array_filter($row))) continue;
                        $col0 = trim($row[0] ?? '');
                        $col1 = trim($row[1] ?? '');
                        
                        if (stripos($col0, 'Course') !== false || stripos($col0, 'Code') !== false) {
                            continue;
                        }

                        if (!empty($col0) && strlen($col0) < 10 && !preg_match('/^CO\d/i', $col0)) {
                            $current_course = $col0;
                            $parsed_courses[$current_course] = [];
                        } elseif (preg_match('/^CO\d/i', $col0) && !empty($current_course)) {
                            $parsed_courses[$current_course][] = [
                                'co' => $col0,
                                'desc' => $col1
                            ];
                        }
                    }
                    fclose($handle);
                    $payload['parsed_csv_data'] = $parsed_courses;
                }
            }

            $conn = db_connect();
            $conn->begin_transaction();

            // 1. Check/Create Submission
            $sub_stmt = $conn->prepare("SELECT submission_id FROM nba_submissions WHERE dept_id = ? AND academic_year = ?");
            $sub_stmt->bind_param("is", $dept_id, $year);
            $sub_stmt->execute();
            $sub_res = $sub_stmt->get_result();
            
            if ($sub_res->num_rows > 0) {
                $sub_id = $sub_res->fetch_assoc()['submission_id'];
            } else {
                $sub_insert = $conn->prepare("INSERT INTO nba_submissions (dept_id, academic_year, status) VALUES (?, ?, 'Draft')");
                $sub_insert->bind_param("is", $dept_id, $year);
                $sub_insert->execute();
                $sub_id = $conn->insert_id;
                $sub_insert->close();
            }
            $sub_stmt->close();

            // 2. Check/Create Criteria Data
            $final_json = json_encode($payload);
            
            $crit_stmt = $conn->prepare("SELECT id FROM nba_criteria_data WHERE submission_id = ? AND criterion_number = ?");
            $crit_stmt->bind_param("ii", $sub_id, $crit_id);
            $crit_stmt->execute();
            
            if ($crit_stmt->get_result()->num_rows > 0) {
                $update = $conn->prepare("UPDATE nba_criteria_data SET data_json = ? WHERE submission_id = ? AND criterion_number = ?");
                $update->bind_param("sii", $final_json, $sub_id, $crit_id);
                $update->execute();
                $update->close();
            } else {
                $insert = $conn->prepare("INSERT INTO nba_criteria_data (submission_id, criterion_number, data_json) VALUES (?, ?, ?)");
                $insert->bind_param("iis", $sub_id, $crit_id, $final_json);
                $insert->execute();
                $insert->close();
            }
            $crit_stmt->close();

            $conn->commit();

            echo json_encode([
                'status' => 'success',
                'success' => true,
                'message' => "Criterion {$crit_id} data saved successfully."
            ]);

        } catch (\Exception $e) {
            if (isset($conn) && $conn->ping()) {
                $conn->rollback();
            }
            echo json_encode([
                'status' => 'error',
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function upload_pdf() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        header('Content-Type: application/json');

        try {
            require_login();
            $auth = auth_context();
            $active_role = auth_active_role();

            $dept_id = (int)($active_role['dept_id'] ?? 0);
            if ($dept_id <= 0) {
                throw new \Exception("You must have a department assigned to upload NBA data.");
            }

            if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception("No file uploaded or an upload error occurred.");
            }

            $file = $_FILES['pdf_file'];
            
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if ($mime !== 'application/pdf') {
                throw new \Exception("Invalid file format. Only PDF files are allowed.");
            }

            $upload_dir = __DIR__ . '/../../uploads/nba_pdfs/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $section = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['section'] ?? 'doc');
            $new_filename = sprintf('dept_%d_%s_%s.pdf', $dept_id, $section, uniqid());
            $destination = $upload_dir . $new_filename;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new \Exception("Failed to save the uploaded file.");
            }

            $relative_path = 'uploads/nba_pdfs/' . $new_filename;

            echo json_encode([
                'status' => 'success',
                'file_path' => $relative_path,
                'message' => 'File uploaded successfully'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function generate_pdf() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        header('Content-Type: application/json');

        try {
            require_login();
            $crit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($crit_id <= 0) {
                throw new \Exception("Criterion number is required.");
            }
            
            // For now, this is a placeholder returning a dummy URL. 
            // In a real scenario, this would call FPDF.
            echo json_encode([
                'success' => true,
                'pdf_url' => BASE_URL . "/uploads/nba_pdfs/dummy_criterion{$crit_id}.pdf"
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
