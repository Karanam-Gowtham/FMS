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
            $sub_stmt->bind_param("is", $dept_id, $year);
            $sub_stmt->execute();
            $sub_res = $sub_stmt->get_result();
            
            if ($sub_res->num_rows > 0) {
                $sub_id = $sub_res->fetch_assoc()['submission_id'];
            } else {
                $sub_insert = $conn->prepare("INSERT INTO naac_submissions (dept_id, academic_year, status) VALUES (?, ?, 'Draft')");
                $sub_insert->bind_param("is", $dept_id, $year);
                $sub_insert->execute();
                $sub_id = $conn->insert_id;
                $sub_insert->close();
            }
            $sub_stmt->close();

            $final_json = json_encode($payload);
            
            $crit_stmt = $conn->prepare("SELECT id FROM naac_criteria_data WHERE submission_id = ? AND criterion_number = ?");
            $crit_stmt->bind_param("ii", $sub_id, $crit_id);
            $crit_stmt->execute();
            
            if ($crit_stmt->get_result()->num_rows > 0) {
                $update = $conn->prepare("UPDATE naac_criteria_data SET data_json = ? WHERE submission_id = ? AND criterion_number = ?");
                $update->bind_param("sii", $final_json, $sub_id, $crit_id);
                $update->execute();
                $update->close();
            } else {
                $insert = $conn->prepare("INSERT INTO naac_criteria_data (submission_id, criterion_number, data_json) VALUES (?, ?, ?)");
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
            if (isset($conn)) {
                try {
                    $conn->rollback();
                } catch (\Throwable $t) {
                }
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
            csrfValidate();
            $auth = auth_context();
            $active_role = auth_active_role();

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
            csrfValidate();
            $crit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($crit_id <= 0) {
                throw new \Exception("Criterion number is required.");
            }
            
            require_once __DIR__ . '/../../libs/fpdf.php';
            
            $pdf = new \FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'GMR Institute of Technology', 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, "NAAC Accreditation - Criterion {$crit_id} Report", 0, 1, 'C');
            $pdf->Ln(10);
            $pdf->SetFont('Arial', '', 10);
            $pdf->MultiCell(0, 7, "This is an auto-generated draft report for NAAC Criterion {$crit_id}. In production, this document will compile all saved data and mapped attachments dynamically.");
            
            $filename = "dummy_criterion{$crit_id}_" . time() . ".pdf";
            $filepath = __DIR__ . "/../../uploads/naac_pdfs/" . $filename;
            
            $pdf->Output('F', $filepath);

            echo json_encode([
                'success' => true,
                'pdf_url' => BASE_URL . "/uploads/naac_pdfs/" . $filename
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
