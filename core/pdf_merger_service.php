<?php
/**
 * PDF Merger Service
 * Uses FPDF and FPDI to merge multiple PDFs into a single file with headings.
 */

require_once __DIR__ . '/../libs/fpdf.php';
require_once __DIR__ . '/../libs/fpdi/FPDI-2.6.0/src/autoload.php';

use setasign\Fpdi\Fpdi;

/**
 * Merge multiple PDF files into one, adding a title page before each file.
 *
 * @param array $files_to_merge Array of associative arrays, e.g. [['path' => '/path/to/a.pdf', 'title' => 'Brochure']]
 * @param string $output_path The absolute path where the merged PDF should be saved
 * @return bool True on success, throws exception on failure
 */
function merge_pdfs_with_headings(array $files_to_merge, string $output_path): bool {
    $pdf = new Fpdi();
    $pdf->SetAutoPageBreak(true, 15);

    foreach ($files_to_merge as $file) {
        $path = $file['path'];
        $title = $file['title'];

        // Add a title page
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 24);
        
        // Vertically center the text
        $pageHeight = $pdf->GetPageHeight();
        $pdf->SetY($pageHeight / 2 - 10);
        $pdf->Cell(0, 20, $title, 0, 1, 'C');

        // Check if file exists and is a valid PDF
        if (!file_exists($path)) {
            continue;
        }

        try {
            $pageCount = $pdf->setSourceFile($path);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                
                // Add page in correct orientation
                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                
                // Use the imported page
                $pdf->useTemplate($templateId);
            }
        } catch (Exception $e) {
            // If a file cannot be parsed (e.g. invalid PDF, encrypted), add a placeholder page
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetY(50);
            $pdf->Cell(0, 10, "Could not merge this document: " . $e->getMessage(), 0, 1, 'C');
        }
    }

    $pdf->Output('F', $output_path);
    return true;
}
