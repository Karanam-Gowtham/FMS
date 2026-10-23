<?php
namespace App\Controllers;

class NAACAPIController {
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
                throw new \Exception("Access denied. You do not have permission to modify NAAC accreditation data.");
            }

            $requested_dept_id = isset($_POST['dept_id']) ? (int)$_POST['dept_id'] : null;
            $dept_id = validate_dept_filter($requested_dept_id);
            if ($dept_id === null || $dept_id <= 0) {
                throw new \Exception("Access denied. You are not authorized to save NAAC data for this department.");
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

            global $conn;
            $conn->begin_transaction();

            $sub_stmt = $conn->prepare("SELECT submission_id FROM naac_submissions WHERE dept_id = ? AND academic_year = ?");
            if (!$sub_stmt) {
                throw new \Exception("Database error: failed to prepare submission statement.");
            }
            $sub_stmt->bind_param("is", $dept_id, $year);
            $sub_stmt->execute();
            $sub_res = $sub_stmt->get_result();
            
            if ($sub_res && $sub_res->num_rows > 0) {
                $sub_id = (int)$sub_res->fetch_assoc()['submission_id'];
            } else {
                $sub_insert = $conn->prepare("INSERT INTO naac_submissions (dept_id, academic_year, status) VALUES (?, ?, 'Draft')");
                if (!$sub_insert) {
                    throw new \Exception("Database error: failed to prepare submission insert.");
                }
                $sub_insert->bind_param("is", $dept_id, $year);
                $sub_insert->execute();
                $sub_id = (int)$conn->insert_id;
                $sub_insert->close();
            }
            $sub_stmt->close();

            $final_json = json_encode($payload);
            
            $crit_stmt = $conn->prepare("SELECT id FROM naac_criteria_data WHERE submission_id = ? AND criterion_number = ?");
            if (!$crit_stmt) {
                throw new \Exception("Database error: failed to prepare criteria query.");
            }
            $crit_stmt->bind_param("ii", $sub_id, $crit_id);
            $crit_stmt->execute();
            $crit_res = $crit_stmt->get_result();
            
            if ($crit_res && $crit_res->num_rows > 0) {
                $update = $conn->prepare("UPDATE naac_criteria_data SET data_json = ? WHERE submission_id = ? AND criterion_number = ?");
                if (!$update) {
                    throw new \Exception("Database error: failed to prepare criteria update.");
                }
                $update->bind_param("sii", $final_json, $sub_id, $crit_id);
                $update->execute();
                $update->close();
            } else {
                $insert = $conn->prepare("INSERT INTO naac_criteria_data (submission_id, criterion_number, data_json) VALUES (?, ?, ?)");
                if (!$insert) {
                    throw new \Exception("Database error: failed to prepare criteria insert.");
                }
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

        } catch (\Throwable $e) {
            if (isset($conn)) {
                try {
                    $conn->rollback();
                } catch (\Throwable $t) {
                }
            }
            echo json_encode([
                'status' => 'error',
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
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
                throw new \Exception("Access denied. You do not have permission to upload NAAC accreditation files.");
            }

            $requested_dept_id = isset($_POST['dept_id']) ? (int)$_POST['dept_id'] : null;
            $dept_id = validate_dept_filter($requested_dept_id);
            if ($dept_id === null || $dept_id <= 0) {
                throw new \Exception("Access denied. You are not authorized to upload NAAC data for this department.");
            }

            if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception("No file uploaded or an upload error occurred.");
            }

            $file = $_FILES['pdf_file'];
            
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            
            if ($mime !== 'application/pdf') {
                throw new \Exception("Invalid file format. Only PDF files are allowed.");
            }

            $upload_dir = __DIR__ . '/../../uploads/naac_pdfs/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $section = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['section'] ?? 'doc');
            $new_filename = sprintf('dept_%d_%s_%s.pdf', $dept_id, $section, uniqid());
            $destination = $upload_dir . $new_filename;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new \Exception("Failed to save the uploaded file.");
            }

            $relative_path = 'uploads/naac_pdfs/' . $new_filename;

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
                throw new \Exception("Access denied. You do not have permission to generate NAAC accreditation reports.");
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
            $pdf->Cell(0, 8, "NAAC Accreditation - Criterion {$crit_id} Report", 0, 1, 'C');
            $pdf->Ln(4);

            $pdf->SetFont('Arial', '', 10.5);
            $pdf->SetTextColor(30, 41, 59);
            $pdf->MultiCell(0, 6.5, "This is an auto-generated compliance report summary for NAAC Criterion {$crit_id}. All mapped proofs and verified department records are archived in the GMRIT Academic Repository.", 0, 'L');
            $pdf->Ln(4);

            $pdf->SetFont('Arial', 'I', 9.5);
            $pdf->SetTextColor(100, 116, 139);
            $pdf->Cell(0, 6, "Report Generated on: " . date('d-m-Y H:i:s'), 0, 1, 'L');
            
            $dir = __DIR__ . "/../../uploads/naac_pdfs/";
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $filename = "dummy_criterion{$crit_id}_" . time() . ".pdf";
            $filepath = $dir . $filename;
            
            $pdf->Output('F', $filepath);

            echo json_encode([
                'success' => true,
                'pdf_url' => BASE_URL . "/public/index.php?route=reports/download&type=naac&file=" . urlencode($filename)
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
