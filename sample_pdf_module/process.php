<?php
// Load FPDF and FPDI libraries from your existing libs folder
require_once '../libs/fpdf.php';
require_once '../libs/fpdi/FPDI-2.6.0/src/autoload.php';

use setasign\Fpdi\Fpdi;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_files'])) {
    
    $files = $_FILES['pdf_files'];
    $events = [];
    $totalFiles = count($files['name']);
    
    if ($totalFiles == 0 || empty($files['name'][0])) {
        die("No files uploaded.");
    }

    // --- PHASE 1: INITIALIZE AND CALCULATE PAGE NUMBERS ---
    
    // We create a temporary FPDI instance just to count pages
    $counterPdf = new Fpdi();
    
    // Assuming the Index table itself will take up 1 page
    // (In a real scenario, you'd calculate this dynamically if it spans multiple pages)
    $currentPage = 2; // Event 1 starts on page 2

    for ($i = 0; $i < $totalFiles; $i++) {
        $tmpName = $files['tmp_name'][$i];
        $fileName = $files['name'][$i];
        $error = $files['error'][$i];

        if ($error === UPLOAD_ERR_OK && pathinfo($fileName, PATHINFO_EXTENSION) === 'pdf') {
            try {
                // Get the number of pages in this specific PDF
                $pageCount = $counterPdf->setSourceFile($tmpName);
                
                // Use the filename (without .pdf) as the dummy event name
                $eventName = strtoupper(str_replace('.pdf', '', $fileName));
                $eventName = str_replace(['_', '-'], ' ', $eventName);

                $events[] = [
                    'name' => $eventName,
                    'path' => $tmpName,
                    'pages' => $pageCount,
                    'start_page' => $currentPage
                ];

                // Increment for the next event
                $currentPage += $pageCount;
            } catch (Exception $e) {
                // Skip if there's an issue reading the PDF
                continue;
            }
        }
    }
    
    if (empty($events)) {
        die("No valid PDFs were uploaded.");
    }

    // --- PHASE 2: GENERATE THE INDEX AND MERGE ---

    $pdf = new Fpdi();
    
    // 1. Create the Index Page (Page 1)
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'GMR Institute of Technology', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Criterion V - Student Support and Progression', 0, 1, 'C');
    $pdf->Cell(0, 10, 'Key Indicator - 5.3 Student Participation and Activities', 0, 1, 'C');
    $pdf->Ln(10);
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 10, '5.3.3. Number of sports and cultural events / competitions organised', 0, 1, 'L');
    $pdf->Ln(5);

    // Table Header
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 10, 'S. No.', 1, 0, 'C');
    $pdf->Cell(95, 10, 'Name of the event/competition', 1, 0, 'C');
    $pdf->Cell(45, 10, 'Date of event', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Page No.', 1, 1, 'C');

    // Table Rows
    $pdf->SetFont('Arial', '', 10);
    foreach ($events as $index => $event) {
        $pdf->Cell(15, 10, $index + 1, 1, 0, 'C');
        $pdf->Cell(95, 10, '  ' . $event['name'], 1, 0, 'L');
        $pdf->Cell(45, 10, date('d.m.Y'), 1, 0, 'C'); // Dummy date for sample
        $pdf->Cell(30, 10, $event['start_page'], 1, 1, 'C');
    }

    // 2. Loop through events and merge their PDFs
    $finalPageCounter = 2; // We are now on page 2

    foreach ($events as $event) {
        $pdf->setSourceFile($event['path']);
        
        for ($pageNo = 1; $pageNo <= $event['pages']; $pageNo++) {
            // Import the page
            $templateId = $pdf->importPage($pageNo);
            
            // Get the size of the imported page
            $size = $pdf->getTemplateSize($templateId);
            
            // Add a new page to the final PDF with the imported page's orientation and size
            $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));
            
            // Apply the imported page
            $pdf->useTemplate($templateId);

            // Stamp the page number at the bottom center
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetXY(0, $size['height'] - 15);
            $pdf->Cell($size['width'], 10, 'Page ' . $finalPageCounter, 0, 0, 'C');
            
            $finalPageCounter++;
        }
    }

    // Output the generated PDF to the browser
    $pdf->Output('I', 'Merged_Events.pdf');

} else {
    echo "Invalid Request.";
}
