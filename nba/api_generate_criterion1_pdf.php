<?php
/**
 * API: Generate Master PDF for NBA Criterion 1
 */
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../includes/tcpdf/tcpdf.php';
require_once __DIR__ . '/../includes/PDFMerger.php';

header('Content-Type: application/json');

try {
    require_login();
    $auth = auth_context();
    $active_role = auth_active_role();

    $dept_id = (int)$active_role['dept_id'];
    if ($dept_id <= 0) {
        throw new Exception("You must have a department assigned to generate reports.");
    }

    $year = trim($_POST['year'] ?? '');
    if (empty($year)) {
        throw new Exception("Academic year is required.");
    }

    $json_raw = $_POST['json_data'] ?? '{}';
    $payload = json_decode($json_raw, true) ?: [];
    
    $levelData = $payload['levelData']["dept_$dept_id"] ?? [];
    $peos = $payload['peos'] ?? [];
    $matrix = $payload['matrix'] ?? [];

    // --- 1. GENERATE BASE HTML REPORT ---
    $html = '
    <style>
        h1 { color: #2c3e50; text-align: center; font-size: 20pt; }
        h2 { color: #2c3e50; text-align: center; font-size: 16pt; margin-bottom: 20px; }
        h3 { color: #34495e; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-top: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 15px; font-size: 10pt; }
        th, td { border: 1px solid #999; padding: 5px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .left-align { text-align: left; }
    </style>
    <h1>NBA Accreditation Report</h1>
    <h2>Criterion 1: Vision, Mission and Program Educational Objectives</h2>
    <p style="text-align:center;"><strong>Department ID:</strong> ' . $dept_id . ' &nbsp;&nbsp;|&nbsp;&nbsp; <strong>Academic Year:</strong> ' . htmlspecialchars($year) . '</p>
    ';

    // 1.1 State the Vision and Mission
    $html .= '<h3>1.1 State the Vision and Mission</h3>';
    $html .= '<p><strong>University Mission:</strong> ' . htmlspecialchars($levelData['text_1_1_1'] ?? 'N/A') . '</p>';
    $html .= '<p><strong>School Mission:</strong> ' . htmlspecialchars($levelData['text_1_1_2'] ?? 'N/A') . '</p>';

    // 1.1.5 Matrix
    $html .= '<h3>1.1.5 Mapping of PEOs with Mission</h3>';
    if (!empty($peos)) {
        $html .= '<table><thead><tr><th style="width: 70%;">PEO \ Mission</th><th style="width: 30%;">Mappings (Correlation)</th></tr></thead><tbody>';
        foreach ($peos as $peo) {
            $html .= '<tr><td class="left-align">' . htmlspecialchars($peo['id']) . ': ' . htmlspecialchars($peo['text']) . '</td><td>';
            $mappings = [];
            foreach ($matrix as $k => $v) {
                if (strpos($k, $peo['id'] . '_') === 0 && !empty($v) && $v !== '-') {
                    $m_id = str_replace($peo['id'] . '_', '', $k);
                    $mappings[] = "$m_id ($v)";
                }
            }
            $html .= implode("<br>", $mappings) . '</td></tr>';
        }
        $html .= '</tbody></table>';
    }

    // 1.2 Curriculum
    $html .= '<h3>1.2 Curriculum Structure</h3>';
    if (!empty($levelData['curriculum'])) {
        $html .= '<table><thead><tr><th style="width: 15%;">Course Code</th><th style="width: 45%;">Course Title</th><th style="width: 10%;">L</th><th style="width: 10%;">T</th><th style="width: 10%;">P</th><th style="width: 10%;">Credits</th></tr></thead><tbody>';
        foreach ($levelData['curriculum'] as $c) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($c['code'] ?? '') . '</td>';
            $html .= '<td class="left-align">' . htmlspecialchars($c['title'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($c['l'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($c['t'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($c['p'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($c['credits'] ?? '') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
    } else {
        $html .= '<p>No curriculum data entered.</p>';
    }

    // Add a note about attached proofs
    $html .= '<br><br><h3>Attached Proofs</h3>';
    $html .= '<p>Any PDF proofs uploaded for sections (e.g., 1.1.3, 1.1.4, 1.2.1, 1.2.4) have been sequentially merged at the end of this document.</p>';

    // --- 2. RENDER TCPDF BASE DOCUMENT ---
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('FMS System');
    $pdf->SetTitle('Criterion 1 Report');
    
    // set margins
    $pdf->SetMargins(15, 20, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(true);
    
    $pdf->AddPage();
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Save base PDF temporarily
    $reports_dir = __DIR__ . '/../uploads/nba_reports';
    if (!is_dir($reports_dir)) mkdir($reports_dir, 0777, true);
    
    $base_pdf_path = $reports_dir . '/base_' . time() . '.pdf';
    $pdf->Output($base_pdf_path, 'F');

    // --- 3. MERGE UPLOADED PROOFS ---
    $merger = new \PDFMerger\PDFMerger;
    $merger->addPDF($base_pdf_path, 'all');

    // Check for uploaded files in the levelData (which stores paths from previous saves)
    $proof_keys = ['pdf_1_1_3', 'pdf_1_1_4', 'pdf_1_2_1', 'pdf_1_2_4'];
    foreach ($proof_keys as $key) {
        if (!empty($levelData[$key])) {
            $abs_path = realpath(__DIR__ . '/../' . ltrim($levelData[$key], '/'));
            if ($abs_path && file_exists($abs_path)) {
                try {
                    $merger->addPDF($abs_path, 'all');
                } catch (Exception $e) {
                    error_log("Failed to merge PDF $abs_path: " . $e->getMessage());
                }
            }
        }
    }

    // Execute Merge
    $final_filename = 'Criterion_1_' . $dept_id . '_' . preg_replace('/[^a-zA-Z0-9-]/', '_', $year) . '_' . time() . '.pdf';
    $final_pdf_path = $reports_dir . '/' . $final_filename;
    
    $merger->merge('file', $final_pdf_path);

    // Clean up temporary base PDF
    @unlink($base_pdf_path);

    // Return the URL
    $pdf_url = BASE_URL . '/uploads/nba_reports/' . $final_filename;

    echo json_encode([
        'success' => true,
        'pdf_url' => $pdf_url
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
