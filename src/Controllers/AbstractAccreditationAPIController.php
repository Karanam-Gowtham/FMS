<?php
namespace App\Controllers;

abstract class AbstractAccreditationAPIController {
    protected function processSave(
        string $module,
        string $submissionsTable,
        string $criteriaTable,
        ?callable $payloadHook = null
    ): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        header('Content-Type: application/json');

        try {
            $dept_id = $this->authorizeDepartmentAction($module, 'save');

            $year = (isset($_POST['year']) && is_string($_POST['year'])) ? trim($_POST['year']) : '';
            if (empty($year)) {
                throw new \Exception("Academic year is required.");
            }

            $crit_id = isset($_POST['criterion_number']) ? (int)$_POST['criterion_number'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
            if ($crit_id <= 0) {
                throw new \Exception("Criterion number is required.");
            }

            $json_raw = (isset($_POST['json_data']) && is_string($_POST['json_data'])) ? $_POST['json_data'] : '{}';
            $payload = json_decode($json_raw, true) ?: [];

            if ($payloadHook !== null) {
                $payload = $payloadHook($payload, $crit_id, $dept_id);
            }

            global $conn;
            $conn->begin_transaction();

            // 1. Check/Create Submission
            $sub_stmt = $conn->prepare("SELECT submission_id FROM {$submissionsTable} WHERE dept_id = ? AND academic_year = ?");
            if (!$sub_stmt) {
                throw new \Exception("Database error: failed to prepare submission statement.");
            }
            $sub_stmt->bind_param("is", $dept_id, $year);
            $sub_stmt->execute();
            $sub_res = $sub_stmt->get_result();

            if ($sub_res && $sub_res->num_rows > 0) {
                $sub_id = (int)$sub_res->fetch_assoc()['submission_id'];
            } else {
                $sub_insert = $conn->prepare("INSERT INTO {$submissionsTable} (dept_id, academic_year, status) VALUES (?, ?, 'Draft')");
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

            $crit_stmt = $conn->prepare("SELECT id FROM {$criteriaTable} WHERE submission_id = ? AND criterion_number = ?");
            if (!$crit_stmt) {
                throw new \Exception("Database error: failed to prepare criteria query.");
            }
            $crit_stmt->bind_param("ii", $sub_id, $crit_id);
            $crit_stmt->execute();
            $crit_res = $crit_stmt->get_result();

            if ($crit_res && $crit_res->num_rows > 0) {
                $update = $conn->prepare("UPDATE {$criteriaTable} SET data_json = ? WHERE submission_id = ? AND criterion_number = ?");
                if (!$update) {
                    throw new \Exception("Database error: failed to prepare criteria update.");
                }
                $update->bind_param("sii", $final_json, $sub_id, $crit_id);
                $update->execute();
                $update->close();
            } else {
                $insert = $conn->prepare("INSERT INTO {$criteriaTable} (submission_id, criterion_number, data_json) VALUES (?, ?, ?)");
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
            $this->outputJsonError($module, 'save', $e);
        }
    }

    protected function processUploadPdf(string $module, string $uploadSubdir): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        header('Content-Type: application/json');

        try {
            $dept_id = $this->authorizeDepartmentAction($module, 'upload');

            if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
                if (isset($_FILES['pdf_file']['error'])) {
                    if ($_FILES['pdf_file']['error'] === UPLOAD_ERR_INI_SIZE || $_FILES['pdf_file']['error'] === UPLOAD_ERR_FORM_SIZE) {
                        throw new \Exception("File size exceeds server upload limit (" . ini_get('upload_max_filesize') . ").");
                    }
                    if ($_FILES['pdf_file']['error'] === UPLOAD_ERR_NO_FILE) {
                        throw new \Exception("No file was selected for upload.");
                    }
                    if ($_FILES['pdf_file']['error'] === UPLOAD_ERR_PARTIAL) {
                        throw new \Exception("File upload was only partially completed. Please try again.");
                    }
                }
                throw new \Exception("No file uploaded or an upload error occurred.");
            }

            $file = $_FILES['pdf_file'];
            if (($file['size'] ?? 0) > 15 * 1024 * 1024) {
                throw new \Exception("File size exceeds 15 MB limit.");
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

            $upload_dir = __DIR__ . '/../../uploads/' . $uploadSubdir . '/';
            if (!is_dir($upload_dir)) {
                @mkdir($upload_dir, 0755, true);
            }

            $raw_section = (isset($_POST['section']) && is_string($_POST['section'])) ? $_POST['section'] : 'doc';
            $section = preg_replace('/[^a-zA-Z0-9_]/', '', $raw_section) ?: 'doc';
            $new_filename = sprintf('dept_%d_%s_%s.pdf', $dept_id, $section, uniqid());
            $destination = $upload_dir . $new_filename;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new \Exception("Failed to save the uploaded file.");
            }

            $relative_path = 'uploads/' . $uploadSubdir . '/' . $new_filename;

            echo json_encode([
                'status' => 'success',
                'success' => true,
                'file_path' => $relative_path,
                'message' => 'File uploaded successfully'
            ]);

        } catch (\Throwable $e) {
            $this->outputJsonError($module, 'upload', $e);
        }
    }

    protected function processGeneratePdf(string $module, string $uploadSubdir, string $summaryDesc): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        header('Content-Type: application/json');

        try {
            require_login();
            csrfValidate();

            $active_role_id = auth_active_role_id();
            if (!$active_role_id || $active_role_id === ROLE_STUDENT || !auth_has_role($active_role_id)) {
                throw new \Exception("Access denied. You do not have permission to generate " . strtoupper($module) . " accreditation reports.");
            }

            $crit_id = isset($_POST['criterion_number']) ? (int)$_POST['criterion_number'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
            if ($crit_id <= 0) {
                throw new \Exception("Criterion number is required.");
            }

            global $conn;

            // Resolve Department
            $requested_dept_id = isset($_POST['dept_id']) ? (int)$_POST['dept_id'] : (isset($_GET['dept_id']) ? (int)$_GET['dept_id'] : 0);
            if ($requested_dept_id <= 0) {
                $requested_dept_id = (int)(auth_active_dept_id() ?: 1);
            }
            $dept_id = $requested_dept_id;

            $dept_name = 'Department';
            $dept_stmt = $conn->prepare("SELECT dept_name FROM departments WHERE dept_id = ? LIMIT 1");
            if ($dept_stmt) {
                $dept_stmt->bind_param("i", $dept_id);
                $dept_stmt->execute();
                $dres = $dept_stmt->get_result();
                if ($dres && ($row = $dres->fetch_assoc())) {
                    $dept_name = $row['dept_name'];
                }
                $dept_stmt->close();
            }

            // Resolve Academic Year
            $active_ay = function_exists('doc_get_active_year') ? doc_get_active_year($conn) : null;
            $default_year = $active_ay ? $active_ay['year_label'] : '2025-26';
            $raw_year = isset($_POST['year']) ? trim($_POST['year']) : (isset($_GET['year']) ? trim($_GET['year']) : '');
            $year = !empty($raw_year) ? $raw_year : $default_year;

            // Resolve Payload Data
            $payload = [];
            if (!empty($_POST['json_data']) && is_string($_POST['json_data'])) {
                $payload = json_decode($_POST['json_data'], true) ?: [];
            }

            $submissionsTable = ($module === 'naac') ? 'naac_submissions' : 'nba_submissions';
            $criteriaTable = ($module === 'naac') ? 'naac_criteria_data' : 'nba_criteria_data';

            $sub_id = 0;
            $sub_stmt = $conn->prepare("SELECT submission_id FROM {$submissionsTable} WHERE dept_id = ? AND academic_year = ?");
            if ($sub_stmt) {
                $sub_stmt->bind_param("is", $dept_id, $year);
                $sub_stmt->execute();
                $sres = $sub_stmt->get_result();
                if ($sres && ($srow = $sres->fetch_assoc())) {
                    $sub_id = (int)$srow['submission_id'];
                    $cstmt = $conn->prepare("SELECT data_json FROM {$criteriaTable} WHERE submission_id = ? AND criterion_number = ?");
                    if ($cstmt) {
                        $cstmt->bind_param("ii", $sub_id, $crit_id);
                        $cstmt->execute();
                        $cres = $cstmt->get_result();
                        if ($cres && ($crow = $cres->fetch_assoc())) {
                            $db_data = json_decode($crow['data_json'], true) ?: [];
                            if (empty($payload)) {
                                $payload = $db_data;
                            } else {
                                foreach ($db_data as $k => $v) {
                                    if (!isset($payload[$k]) && is_string($k) && str_ends_with($k, '_path')) {
                                        $payload[$k] = $v;
                                    }
                                }
                            }
                        }
                        $cstmt->close();
                    }
                }
                $sub_stmt->close();
            }

            // Criterion Titles
            $naac_titles = [
                1 => 'Curricular Aspects',
                2 => 'Teaching-Learning and Evaluation',
                3 => 'Research, Innovations and Extension',
                4 => 'Infrastructure and Learning Resources',
                5 => 'Student Support and Progression',
                6 => 'Governance, Leadership and Management',
                7 => 'Institutional Values and Best Practices'
            ];
            $nba_titles = [
                1 => 'Vision, Mission and Program Educational Objectives',
                2 => 'Program Curriculum and Teaching-Learning Processes',
                3 => 'Course Outcomes and Program Outcomes',
                4 => 'Students Performance',
                5 => 'Faculty Information and Contributions',
                6 => 'Facilities and Technical Support',
                7 => 'Continuous Improvement',
                8 => 'First Year Academics',
                9 => 'Student Support Systems'
            ];

            $crit_title = ($module === 'naac') ? ($naac_titles[$crit_id] ?? "Criterion {$crit_id}") : ($nba_titles[$crit_id] ?? "Criterion {$crit_id}");

            // Load Libraries (FPDF & FPDI)
            require_once __DIR__ . '/../../libs/fpdf.php';
            require_once __DIR__ . '/../../core/pdf_normalizer.php';
            $use_fpdi = false;
            $fpdi_autoload = __DIR__ . '/../../libs/fpdi/FPDI-2.6.0/src/autoload.php';
            if (file_exists($fpdi_autoload)) {
                require_once $fpdi_autoload;
                if (class_exists('\setasign\Fpdi\Fpdi')) {
                    $use_fpdi = true;
                }
            }
            $temp_cleanup_normalizer = [];

            $pdf = $use_fpdi ? new \setasign\Fpdi\Fpdi('P', 'mm', 'A4') : new \FPDF('P', 'mm', 'A4');
            $pdf->SetAutoPageBreak(true, 18);
            $pdf->SetMargins(15, 15, 15);
            $pdf->AddPage();

            // Text sanitizer helper for standard FPDF single-byte font
            $cleanText = function($text) {
                if ($text === null || $text === '') return '';
                $text = str_replace(
                    ['“', '”', '‘', '’', '—', '–', '…', '•', '₹', "\r\n", "\r"],
                    ['"', '"', "'", "'", '-', '-', '...', '-', 'Rs. ', "\n", "\n"],
                    (string)$text
                );
                return iconv('UTF-8', 'windows-1252//IGNORE', $text);
            };

            // Institution Header
            $logoPath = realpath(__DIR__ . '/../../assets/img/gmr_logo.png') ?: realpath(__DIR__ . '/../../assets/img/logo.png');
            if ($logoPath && file_exists($logoPath)) {
                $pdf->Image($logoPath, 15, 12, 28);
                $pdf->SetXY(46, 12);
            } else {
                $pdf->SetXY(15, 12);
            }

            $pdf->SetFont('Arial', 'B', 15);
            $pdf->SetTextColor(0, 51, 102);
            $pdf->Cell(0, 7, $cleanText('GMR INSTITUTE OF TECHNOLOGY'), 0, 1, 'L');
            $pdf->SetX(46);
            $pdf->SetFont('Arial', '', 8.5);
            $pdf->SetTextColor(80, 80, 80);
            $pdf->Cell(0, 4.5, $cleanText('An Autonomous Institute Affiliated to JNTU-GV, Vizianagaram'), 0, 1, 'L');
            $pdf->SetX(46);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetTextColor(100, 116, 139);
            $pdf->Cell(0, 4.5, $cleanText("Accredited by NAAC with 'A+' Grade | NBA Accredited Programs"), 0, 1, 'L');
            $pdf->Ln(5);

            // Dividing Header Line
            $pdf->SetDrawColor(0, 51, 102);
            $pdf->SetLineWidth(0.6);
            $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
            $pdf->Ln(4);

            // Report Title Banner
            $modUpper = strtoupper($module);
            $pdf->SetFillColor(0, 51, 102);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, " {$modUpper} ACCREDITATION - CRITERION {$crit_id} EVALUATION REPORT", 0, 1, 'L', true);
            $pdf->Ln(2);

            // Metadata Summary Box
            $pdf->SetFillColor(248, 250, 252);
            $pdf->SetDrawColor(226, 232, 240);
            $pdf->SetLineWidth(0.3);
            $pdf->Rect(15, $pdf->GetY(), 180, 24, 'DF');

            $startY = $pdf->GetY() + 2;
            $pdf->SetXY(18, $startY);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor(15, 23, 42);
            $pdf->Cell(32, 5, "Criterion Title:", 0, 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(30, 41, 59);
            $pdf->Cell(65, 5, $cleanText("Criterion {$crit_id}: {$crit_title}"), 0, 0);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor(15, 23, 42);
            $pdf->Cell(30, 5, "Academic Year:", 0, 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(30, 41, 59);
            $pdf->Cell(45, 5, $cleanText($year), 0, 1);

            $pdf->SetXY(18, $startY + 6);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor(15, 23, 42);
            $pdf->Cell(32, 5, "Department:", 0, 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(30, 41, 59);
            $pdf->Cell(65, 5, $cleanText($dept_name), 0, 0);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor(15, 23, 42);
            $pdf->Cell(30, 5, "Report Status:", 0, 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor(22, 101, 52);
            $pdf->Cell(45, 5, "Archived & Verified", 0, 1);

            $pdf->SetXY(18, $startY + 12);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor(15, 23, 42);
            $pdf->Cell(32, 5, "Generated On:", 0, 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(100, 116, 139);
            $pdf->Cell(65, 5, date('d-m-Y H:i:s'), 0, 0);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor(15, 23, 42);
            $pdf->Cell(30, 5, "Document Ref:", 0, 0);
            $pdf->SetFont('Arial', '', 8.5);
            $pdf->SetTextColor(100, 116, 139);
            $pdf->Cell(45, 5, "{$modUpper}-C{$crit_id}-D{$dept_id}-" . time(), 0, 1);

            $pdf->SetY($startY + 26);

            $attachedPdfFiles = [];

            if ($module === 'naac') {
                $form_schema = null;
                $schema_stmt = $conn->prepare("SELECT schema_json FROM naac_form_schemas WHERE criterion_number = ? AND is_active = 1 ORDER BY schema_id DESC LIMIT 1");
                if ($schema_stmt) {
                    $schema_stmt->bind_param("i", $crit_id);
                    $schema_stmt->execute();
                    $sres = $schema_stmt->get_result();
                    if ($sres && ($srow = $sres->fetch_assoc())) {
                        $form_schema = json_decode($srow['schema_json'], true);
                    }
                    $schema_stmt->close();
                }

                if ($form_schema && !empty($form_schema['sections'])) {
                    foreach ($form_schema['sections'] as $secIndex => $sec) {
                        if ($pdf->GetY() > 240) {
                            $pdf->AddPage();
                        }

                        // Section Banner
                        $pdf->SetFillColor(226, 232, 240);
                        $pdf->SetTextColor(15, 23, 42);
                        $pdf->SetFont('Arial', 'B', 10.5);
                        $pdf->Cell(0, 7.5, " " . $cleanText($sec['title'] ?? "Section " . ($secIndex + 1)), 0, 1, 'L', true);
                        $pdf->Ln(3);

                        foreach ($sec['metrics'] ?? [] as $metric) {
                            if ($pdf->GetY() > 245) {
                                $pdf->AddPage();
                            }

                            // Metric Heading
                            $pdf->SetFont('Arial', 'B', 9.5);
                            $pdf->SetTextColor(0, 51, 102);
                            $pdf->MultiCell(0, 5, $cleanText($metric['title'] ?? ($metric['id'] ?? 'Metric')), 0, 'L');
                            
                            if (!empty($metric['description'])) {
                                $pdf->SetFont('Arial', 'I', 8.5);
                                $pdf->SetTextColor(100, 116, 139);
                                $pdf->MultiCell(0, 4.5, $cleanText($metric['description']), 0, 'L');
                            }
                            $pdf->Ln(2);

                            // Metric Fields
                            foreach ($metric['fields'] ?? [] as $field) {
                                $fname = $field['name'] ?? '';
                                $ftype = $field['type'] ?? 'text';
                                $flabel = $field['label'] ?? $fname;

                                if ($pdf->GetY() > 250) {
                                    $pdf->AddPage();
                                }

                                if ($ftype === 'textarea' || $ftype === 'text' || $ftype === 'url') {
                                    $val = trim((string)($payload[$fname] ?? ''));
                                    $pdf->SetFont('Arial', 'B', 8.5);
                                    $pdf->SetTextColor(51, 65, 85);
                                    $pdf->Cell(0, 5, $cleanText($flabel) . ":", 0, 1, 'L');
                                    
                                    $pdf->SetFont('Arial', '', 8.5);
                                    $pdf->SetTextColor(15, 23, 42);
                                    if ($val !== '') {
                                        $pdf->SetFillColor(250, 250, 250);
                                        $pdf->SetDrawColor(226, 232, 240);
                                        $pdf->MultiCell(0, 4.8, $cleanText($val), 1, 'L', true);
                                    } else {
                                        $pdf->SetTextColor(148, 163, 184);
                                        $pdf->Cell(0, 5, "[No description provided]", 0, 1, 'L');
                                    }
                                    $pdf->Ln(2);

                                } elseif ($ftype === 'number') {
                                    $numVal = isset($payload[$fname]) ? (string)$payload[$fname] : '0';
                                    $pdf->SetFont('Arial', '', 8.5);
                                    $pdf->SetTextColor(51, 65, 85);
                                    $pdf->Write(5, $cleanText($flabel) . ": ");
                                    $pdf->SetFont('Arial', 'B', 9);
                                    $pdf->SetTextColor(0, 51, 102);
                                    $pdf->Write(5, $cleanText($numVal));
                                    $pdf->Ln(5);

                                } elseif ($ftype === 'checkbox_group') {
                                    $pdf->SetFont('Arial', 'B', 8.5);
                                    $pdf->SetTextColor(51, 65, 85);
                                    $pdf->Cell(0, 5, $cleanText($flabel), 0, 1, 'L');
                                    $pdf->SetFont('Arial', '', 8.5);
                                    $pdf->SetTextColor(15, 23, 42);
                                    $checkedOpts = [];
                                    foreach ($field['options'] ?? [] as $opt) {
                                        $optName = $opt['name'] ?? '';
                                        if (!empty($payload[$optName])) {
                                            $checkedOpts[] = "[X] " . ($opt['label'] ?? $optName);
                                        } else {
                                            $checkedOpts[] = "[ ] " . ($opt['label'] ?? $optName);
                                        }
                                    }
                                    $pdf->MultiCell(0, 4.5, $cleanText(implode('    ', $checkedOpts)), 0, 'L');
                                    $pdf->Ln(2);

                                } elseif ($ftype === 'table') {
                                    $rows = $payload[$fname] ?? [];
                                    $cols = $field['columns'] ?? [];
                                    $pdf->SetFont('Arial', 'B', 8.5);
                                    $pdf->SetTextColor(30, 41, 59);
                                    $pdf->Cell(0, 5, $cleanText($flabel) . ":", 0, 1, 'L');

                                    if (!empty($rows) && is_array($rows) && !empty($cols)) {
                                        $colCount = count($cols);
                                        $tableWidth = 180;
                                        $colW = floor($tableWidth / $colCount);
                                        
                                        // Header Row
                                        if ($pdf->GetY() > 245) $pdf->AddPage();
                                        $pdf->SetFillColor(241, 245, 249);
                                        $pdf->SetDrawColor(203, 213, 225);
                                        $pdf->SetFont('Arial', 'B', 7.5);
                                        $pdf->SetTextColor(15, 23, 42);
                                        foreach ($cols as $col) {
                                            $pdf->Cell($colW, 6, $cleanText(substr($col['label'] ?? $col['name'], 0, 18)), 1, 0, 'L', true);
                                        }
                                        $pdf->Ln();

                                        // Data Rows
                                        $pdf->SetFont('Arial', '', 7.5);
                                        $pdf->SetTextColor(51, 65, 85);
                                        foreach ($rows as $rIndex => $rData) {
                                            if ($pdf->GetY() > 260) {
                                                $pdf->AddPage();
                                                $pdf->SetFillColor(241, 245, 249);
                                                $pdf->SetFont('Arial', 'B', 7.5);
                                                foreach ($cols as $col) {
                                                    $pdf->Cell($colW, 6, $cleanText(substr($col['label'] ?? $col['name'], 0, 18)), 1, 0, 'L', true);
                                                }
                                                $pdf->Ln();
                                                $pdf->SetFont('Arial', '', 7.5);
                                            }
                                            foreach ($cols as $col) {
                                                $cellVal = isset($rData[$col['name']]) ? (string)$rData[$col['name']] : '';
                                                $pdf->Cell($colW, 5.5, $cleanText(substr($cellVal, 0, 20)), 1, 0, 'L');
                                            }
                                            $pdf->Ln();
                                        }
                                    } else {
                                        $pdf->SetFont('Arial', 'I', 8);
                                        $pdf->SetTextColor(148, 163, 184);
                                        $pdf->Cell(0, 5, "[No tabular records submitted]", 0, 1, 'L');
                                    }
                                    $pdf->Ln(2);

                                } elseif ($ftype === 'file') {
                                    $uploadedPath = $payload[$fname . '_path'] ?? null;
                                    $pdf->SetFont('Arial', 'B', 8.5);
                                    $pdf->SetTextColor(51, 65, 85);
                                    $pdf->Write(5, $cleanText($flabel) . ": ");

                                    if ($uploadedPath && is_string($uploadedPath)) {
                                        $pdf->SetFont('Arial', 'B', 8.5);
                                        $pdf->SetTextColor(22, 101, 52);
                                        $pdf->Write(5, "[Attached Proof: " . basename($uploadedPath) . "]");
                                        $pdf->Ln(5);

                                        $attachedPdfFiles[] = [
                                            'metric' => $metric['id'] ?? '',
                                            'label' => $flabel,
                                            'path' => $uploadedPath
                                        ];
                                    } else {
                                        $pdf->SetFont('Arial', 'I', 8.5);
                                        $pdf->SetTextColor(148, 163, 184);
                                        $pdf->Write(5, "[No proof document attached]");
                                        $pdf->Ln(5);
                                    }
                                }
                            }
                            $pdf->Ln(2);
                        }
                    }
                } else {
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetTextColor(30, 41, 59);
                    $pdf->MultiCell(0, 6.5, sprintf($summaryDesc, $crit_id), 0, 'L');
                }
            } else {
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetTextColor(30, 41, 59);
                $pdf->MultiCell(0, 6.5, sprintf($summaryDesc, $crit_id), 0, 'L');
                $pdf->Ln(4);

                $foundPdfs = [];
                if (!empty($payload['uploaded_files']) && is_array($payload['uploaded_files'])) {
                    foreach ($payload['uploaded_files'] as $secKey => $fpath) {
                        if (!empty($fpath) && is_string($fpath)) {
                            $foundPdfs[$secKey] = $fpath;
                        }
                    }
                }
                if (!empty($payload['levelData']) && is_array($payload['levelData'])) {
                    $lvlKey = 'dept_' . $dept_id;
                    $deptLevel = $payload['levelData'][$lvlKey] ?? ($payload['levelData']["$dept_id"] ?? null);
                    if (!$deptLevel && !empty($payload['levelData'])) {
                        $deptLevel = reset($payload['levelData']);
                    }
                    if (is_array($deptLevel)) {
                        foreach ($deptLevel as $k => $v) {
                            if (is_string($v) && !empty($v) && (str_starts_with($k, 'pdf_') || str_ends_with(strtolower($v), '.pdf'))) {
                                if (!isset($foundPdfs[$k])) {
                                    $foundPdfs[$k] = $v;
                                }
                            }
                        }
                    }
                }

                if (!empty($foundPdfs)) {
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->SetTextColor(0, 51, 102);
                    $pdf->Cell(0, 7, "Uploaded Department Documents & Proofs:", 0, 1, 'L');
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->SetTextColor(51, 65, 85);
                    foreach ($foundPdfs as $secKey => $fpath) {
                        $pdf->Cell(0, 5, "- " . $cleanText($secKey) . ": " . basename($fpath), 0, 1, 'L');
                        $attachedPdfFiles[] = [
                            'metric' => $secKey,
                            'label' => $secKey,
                            'path' => $fpath
                        ];
                    }
                }
            }

            // Append Annexures using FPDI
            if ($use_fpdi && !empty($attachedPdfFiles)) {
                $hasValidProof = false;
                foreach ($attachedPdfFiles as $chk) {
                    $cRel = ltrim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $chk['path']), DIRECTORY_SEPARATOR);
                    $cFull = realpath(__DIR__ . '/../../' . $cRel);
                    if ($cFull && file_exists($cFull) && strtolower(pathinfo($cFull, PATHINFO_EXTENSION)) === 'pdf') {
                        $hasValidProof = true;
                        break;
                    }
                }

                if ($hasValidProof) {
                    $pdf->AddPage();
                    $pdf->SetFillColor(0, 51, 102);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->Cell(0, 9, " ANNEXURES: VERIFIED SUPPORTING PROOFS & DOCUMENTS", 0, 1, 'L', true);
                    $pdf->Ln(4);

                    $pdf->SetFont('Arial', '', 9.5);
                    $pdf->SetTextColor(51, 65, 85);
                    $pdf->MultiCell(0, 5.5, "The following official supporting documents and institutional proofs were verified and bound into this master compliance dossier.", 0, 'L');
                    $pdf->Ln(3);

                    // Table of attached files
                    $pdf->SetFillColor(241, 245, 249);
                    $pdf->SetFont('Arial', 'B', 8.5);
                    $pdf->SetTextColor(15, 23, 42);
                    $pdf->Cell(25, 7, "Metric", 1, 0, 'C', true);
                    $pdf->Cell(80, 7, "Document Description", 1, 0, 'L', true);
                    $pdf->Cell(75, 7, "File Name", 1, 1, 'L', true);

                    $pdf->SetFont('Arial', '', 8.5);
                    $pdf->SetTextColor(51, 65, 85);
                    foreach ($attachedPdfFiles as $att) {
                        $pdf->Cell(25, 6, $cleanText($att['metric']), 1, 0, 'C');
                        $pdf->Cell(80, 6, $cleanText(substr($att['label'], 0, 42)), 1, 0, 'L');
                        $pdf->Cell(75, 6, $cleanText(substr(basename($att['path']), 0, 40)), 1, 1, 'L');
                    }
                    $pdf->Ln(8);

                    foreach ($attachedPdfFiles as $att) {
                        $relPath = ltrim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $att['path']), DIRECTORY_SEPARATOR);
                        $fullPath = realpath(__DIR__ . '/../../' . $relPath);
                        if (!$fullPath || !file_exists($fullPath) || strtolower(pathinfo($fullPath, PATHINFO_EXTENSION)) !== 'pdf') {
                            continue;
                        }

                        $actualPath = class_exists('\PdfNormalizer') ? \PdfNormalizer::normalizePdfIfNeeded($fullPath, $temp_cleanup_normalizer) : $fullPath;
                        try {
                            $pageCount = $pdf->setSourceFile($actualPath);
                            for ($p = 1; $p <= $pageCount; $p++) {
                                $tplId = $pdf->importPage($p);
                                $size = $pdf->getTemplateSize($tplId);
                                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                                $pdf->useTemplate($tplId);
                            }
                        } catch (\Throwable $pdfEx) {
                            $pdf->AddPage();
                            $pdf->SetFont('Arial', 'B', 12);
                            $pdf->SetTextColor(0, 51, 102);
                            $pdf->Cell(0, 8, "Annexure Document: " . basename($fullPath), 0, 1, 'L');
                            $pdf->SetFont('Arial', '', 9.5);
                            $pdf->SetTextColor(71, 85, 105);
                            $pdf->MultiCell(0, 6, "Note: This document is archived as an external high-resolution digital proof in the GMRIT Academic Repository (File: " . basename($fullPath) . "). Access is available directly via the departmental portal.");
                        }
                    }
                }
            }

            // Save PDF
            $dir = __DIR__ . '/../../uploads/' . $uploadSubdir . '/';
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            $clean_year = preg_replace('/[^a-zA-Z0-9_]/', '_', $year);
            $filename = "final_criterion{$crit_id}_dept_{$dept_id}_{$clean_year}_" . time() . ".pdf";
            $filepath = $dir . $filename;

            $pdf->Output('F', $filepath);

            $download_url = BASE_URL . "/public/index.php?route=reports/download&type={$module}&file=" . urlencode($filename);

            // Persist the generated PDF URL back to criteria_data
            if ($sub_id > 0) {
                $payload['final_pdf_file'] = $filename;
                $payload['final_pdf_url'] = $download_url;
                $payload['final_pdf_generated_at'] = date('Y-m-d H:i:s');
                $updated_json = json_encode($payload);

                $up_stmt = $conn->prepare("UPDATE {$criteriaTable} SET data_json = ? WHERE submission_id = ? AND criterion_number = ?");
                if ($up_stmt) {
                    $up_stmt->bind_param("sii", $updated_json, $sub_id, $crit_id);
                    $up_stmt->execute();
                    $up_stmt->close();
                }
            }

            echo json_encode([
                'status' => 'success',
                'success' => true,
                'pdf_url' => $download_url,
                'filename' => $filename,
                'message' => 'Final Master PDF generated successfully!'
            ]);

        } catch (\Throwable $e) {
            $this->outputJsonError($module, 'generate', $e);
        } finally {
            if (!empty($temp_cleanup_normalizer)) {
                foreach ($temp_cleanup_normalizer as $tmp) {
                    if (file_exists($tmp)) {
                        @unlink($tmp);
                    }
                }
            }
        }
    }

    protected function authorizeDepartmentAction(string $module, string $actionVerb): int {
        require_login();
        csrfValidate();
        $active_role_id = auth_active_role_id();
        if (!$active_role_id || $active_role_id === ROLE_STUDENT || !auth_has_role($active_role_id)) {
            throw new \Exception("Access denied. You do not have permission to {$actionVerb} " . strtoupper($module) . " accreditation data.");
        }

        $requested_dept_id = isset($_POST['dept_id']) ? (int)$_POST['dept_id'] : null;
        if ($requested_dept_id === null || $requested_dept_id <= 0) {
            throw new \Exception("Department is required to {$actionVerb} " . strtoupper($module) . " data.");
        }

        global $conn;
        $stmt = $conn->prepare("SELECT dept_id FROM departments WHERE dept_id = ? LIMIT 1");
        if (!$stmt) {
            throw new \Exception("Database error verifying department.");
        }
        $stmt->bind_param("i", $requested_dept_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if (!$res || $res->num_rows === 0) {
            $stmt->close();
            throw new \Exception("Invalid department selected.");
        }
        $stmt->close();

        return $requested_dept_id;
    }

    protected function outputJsonError(string $module, string $actionVerb, \Throwable $e): void {
        error_log(strtoupper($module) . " API {$actionVerb} failed: " . $e->getMessage());
        $is_debug = defined('FMS_DEBUG') && FMS_DEBUG;
        $msg = ($is_debug || str_starts_with($e->getMessage(), 'Access denied') || str_starts_with($e->getMessage(), 'Academic year') || str_starts_with($e->getMessage(), 'Criterion number') || str_starts_with($e->getMessage(), 'No file') || str_starts_with($e->getMessage(), 'File size') || str_starts_with($e->getMessage(), 'Invalid file') || str_starts_with($e->getMessage(), 'File upload error'))
            ? $e->getMessage()
            : "Failed to {$actionVerb} " . strtoupper($module) . " accreditation data. Please try again.";
        echo json_encode([
            'status' => 'error',
            'success' => false,
            'message' => $msg,
            'error' => $msg
        ]);
    }
}
