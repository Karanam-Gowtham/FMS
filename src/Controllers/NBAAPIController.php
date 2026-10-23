<?php
namespace App\Controllers;

class NBAAPIController {
    public function save() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        header('Content-Type: application/json');

        try {
            require_login();
            csrfValidate();
            $auth = auth_context();
            $active_role = auth_active_role();

            $active_role_id = auth_active_role_id();
            if (!$active_role_id || $active_role_id === ROLE_STUDENT || !auth_has_role($active_role_id)) {
                throw new \Exception("Access denied. You do not have permission to modify NBA accreditation data.");
            }

            $requested_dept_id = isset($_POST['dept_id']) ? (int)$_POST['dept_id'] : null;
            $dept_id = validate_dept_filter($requested_dept_id);
            if ($dept_id === null || $dept_id <= 0) {
                throw new \Exception("Access denied. You are not authorized to save NBA data for this department.");
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

            global $conn;
            $conn->begin_transaction();

            // 1. Check/Create Submission
            $sub_stmt = $conn->prepare("SELECT submission_id FROM nba_submissions WHERE dept_id = ? AND academic_year = ?");
            if (!$sub_stmt) {
                throw new \Exception("Database error: failed to prepare submission statement.");
            }
            $sub_stmt->bind_param("is", $dept_id, $year);
            $sub_stmt->execute();
            $sub_res = $sub_stmt->get_result();
            
            if ($sub_res && $sub_res->num_rows > 0) {
                $sub_id = (int)$sub_res->fetch_assoc()['submission_id'];
            } else {
                $sub_insert = $conn->prepare("INSERT INTO nba_submissions (dept_id, academic_year, status) VALUES (?, ?, 'Draft')");
                if (!$sub_insert) {
                    throw new \Exception("Database error: failed to prepare submission insert.");
                }
                $sub_insert->bind_param("is", $dept_id, $year);
                $sub_insert->execute();
                $sub_id = (int)$conn->insert_id;
                $sub_insert->close();
            }
            $sub_stmt->close();

            // 2. Check existing criteria data for uploaded files
            $crit_stmt = $conn->prepare("SELECT id, data_json FROM nba_criteria_data WHERE submission_id = ? AND criterion_number = ?");
            if (!$crit_stmt) {
                throw new \Exception("Database error: failed to prepare criteria query.");
            }
            $crit_stmt->bind_param("ii", $sub_id, $crit_id);
            $crit_stmt->execute();
            $crit_res = $crit_stmt->get_result();
            
            $existing_files = [];
            $crit_id_db = null;
            if ($crit_res && $crit_res->num_rows > 0) {
                $row = $crit_res->fetch_assoc();
                $crit_id_db = $row['id'];
                $existing_data = json_decode($row['data_json'], true) ?: [];
                if (isset($existing_data['uploaded_files'])) {
                    $existing_files = $existing_data['uploaded_files'];
                }
            }
            $crit_stmt->close();

            if (!isset($payload['uploaded_files'])) {
                $payload['uploaded_files'] = $existing_files;
            } else {
                $payload['uploaded_files'] = array_merge($existing_files, $payload['uploaded_files']);
            }

            // Generic PDF File Uploader for Criteria 5-9
            $upload_dir = __DIR__ . '/../../uploads/nba_pdfs/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            foreach ($_FILES as $input_name => $file) {
                if ($input_name === 'csv_file' || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) continue;
                if (empty($file['tmp_name']) || !file_exists($file['tmp_name']) || !is_readable($file['tmp_name'])) continue;
                if (($file['size'] ?? 0) > 10 * 1024 * 1024) continue;
                $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
                if ($ext !== 'pdf') continue;

                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
                if ($mime === 'application/pdf') {
                    $section = preg_replace('/[^a-zA-Z0-9_]/', '', $input_name);
                    $new_filename = sprintf('dept_%d_crit%d_%s_%s.pdf', $dept_id, $crit_id, $section, uniqid());
                    $destination = $upload_dir . $new_filename;
                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        $payload['uploaded_files'][$input_name] = 'uploads/nba_pdfs/' . $new_filename;
                    }
                }
            }

            $final_json = json_encode($payload);
            
            if ($crit_id_db) {
                $update = $conn->prepare("UPDATE nba_criteria_data SET data_json = ? WHERE id = ?");
                if (!$update) {
                    throw new \Exception("Database error: failed to prepare criteria update.");
                }
                $update->bind_param("si", $final_json, $crit_id_db);
                $update->execute();
                $update->close();
            } else {
                $insert = $conn->prepare("INSERT INTO nba_criteria_data (submission_id, criterion_number, data_json) VALUES (?, ?, ?)");
                if (!$insert) {
                    throw new \Exception("Database error: failed to prepare criteria insert.");
                }
                $insert->bind_param("iis", $sub_id, $crit_id, $final_json);
                $insert->execute();
                $insert->close();
            }

            $conn->commit();

            echo json_encode([
                'status' => 'success',
                'success' => true,
                'message' => "Criterion {$crit_id} data saved successfully."
            ]);

        } catch (\Throwable $e) {
            if (isset($conn)) {
                try {
                    $conn->rollback();
                } catch (\Throwable $t) {
                    // Ignore rollback failures
                }
            }
            error_log("NBA API save failed: " . $e->getMessage());
            $is_debug = defined('FMS_DEBUG') && FMS_DEBUG;
            $msg = ($is_debug || str_starts_with($e->getMessage(), 'Access denied') || str_starts_with($e->getMessage(), 'Academic year') || str_starts_with($e->getMessage(), 'Criterion number'))
                ? $e->getMessage()
                : 'An error occurred while saving NBA accreditation data.';
            echo json_encode([
                'status' => 'error',
                'success' => false,
                'message' => $msg,
                'error' => $msg
            ]);
        }
    }

    public function upload_pdf() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        header('Content-Type: application/json');

        try {
            require_login();
            csrfValidate();
            $auth = auth_context();
            $active_role = auth_active_role();

            $active_role_id = auth_active_role_id();
            if (!$active_role_id || $active_role_id === ROLE_STUDENT || !auth_has_role($active_role_id)) {
                throw new \Exception("Access denied. You do not have permission to upload NBA accreditation files.");
            }

            $requested_dept_id = isset($_POST['dept_id']) ? (int)$_POST['dept_id'] : null;
            $dept_id = validate_dept_filter($requested_dept_id);
            if ($dept_id === null || $dept_id <= 0) {
                throw new \Exception("Access denied. You are not authorized to upload NBA data for this department.");
            }

            if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception("No file uploaded or an upload error occurred.");
            }

            $file = $_FILES['pdf_file'];
            if (($file['size'] ?? 0) > 10 * 1024 * 1024) {
                throw new \Exception("File size exceeds 10 MB limit.");
            }

            $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                throw new \Exception("Invalid file extension. Only .pdf files are allowed.");
            }

            if (empty($file['tmp_name']) || !file_exists($file['tmp_name']) || !is_readable($file['tmp_name'])) {
                throw new \Exception("Uploaded temporary file is missing or unreadable.");
            }
            
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            
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

        } catch (\Throwable $e) {
            echo json_encode([
                'status' => 'error',
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function generate_pdf() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        header('Content-Type: application/json');

        try {
            require_login();
            csrfValidate();

            $active_role_id = auth_active_role_id();
            if (!$active_role_id || $active_role_id === ROLE_STUDENT || !auth_has_role($active_role_id)) {
                throw new \Exception("Access denied. You do not have permission to generate NBA accreditation reports.");
            }

            $crit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($crit_id <= 0) {
                throw new \Exception("Criterion number is required.");
            }
            
            require_once __DIR__ . '/../../libs/fpdf.php';
            
            $pdf = new \FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            
            $logoPath = realpath(__DIR__ . '/../../assets/img/gmr_logo.png') ?: realpath(__DIR__ . '/../../assets/img/logo.png');
            if ($logoPath && file_exists($logoPath)) {
                $pdf->Image($logoPath, 86, 12, 38);
                $pdf->SetY(32);
            } else {
                $pdf->SetY(15);
            }

            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetTextColor(0, 51, 102);
            $pdf->Cell(0, 8, 'GMR Institute of Technology', 0, 1, 'C');
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(80, 80, 80);
            $pdf->Cell(0, 5, 'An Autonomous Institute Affiliated to JNTU-GV', 0, 1, 'C');
            $pdf->Ln(6);

            $pdf->SetDrawColor(0, 51, 102);
            $pdf->SetLineWidth(0.5);
            $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
            $pdf->Ln(6);

            $pdf->SetFont('Arial', 'B', 13);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(0, 8, "NBA Accreditation - Criterion {$crit_id} Report", 0, 1, 'C');
            $pdf->Ln(4);

            $pdf->SetFont('Arial', '', 10.5);
            $pdf->SetTextColor(30, 41, 59);
            $pdf->MultiCell(0, 6.5, "This is an auto-generated compliance report summary for NBA Criterion {$crit_id}. All mapped course outcomes, faculty data, and department records are archived in the GMRIT Academic Repository.", 0, 'L');
            $pdf->Ln(4);

            $pdf->SetFont('Arial', 'I', 9.5);
            $pdf->SetTextColor(100, 116, 139);
            $pdf->Cell(0, 6, "Report Generated on: " . date('d-m-Y H:i:s'), 0, 1, 'L');
            
            $dir = __DIR__ . "/../../uploads/nba_pdfs/";
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $filename = "dummy_criterion{$crit_id}_" . time() . ".pdf";
            $filepath = $dir . $filename;
            
            $pdf->Output('F', $filepath);

            echo json_encode([
                'success' => true,
                'pdf_url' => BASE_URL . "/public/index.php?route=reports/download&type=nba&file=" . urlencode($filename)
            ]);
        } catch (\Throwable $e) {
            echo json_encode([
                'status' => 'error',
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ]);
        }
    }
}
