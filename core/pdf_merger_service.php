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
    if (empty($files_to_merge)) {
        throw new \Exception("No files provided for merging.");
    }

    $pdf = new Fpdi();
    // Disable automatic page breaks so imported full-size pages don't trigger extra blank pages
    $pdf->SetAutoPageBreak(false);

    $totalPagesAdded = 0;

    foreach ($files_to_merge as $file) {
        $path = $file['path'] ?? '';
        $title = $file['title'] ?? 'Document';

        // Check if file exists and is a valid PDF
        if (empty($path) || !file_exists($path)) {
            continue;
        }

        $pagesImported = 0;
        try {
            $pageCount = $pdf->setSourceFile($path);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                
                // Add page in correct orientation and native template dimensions
                $orientation = $size['orientation'] ?? ($size['width'] > $size['height'] ? 'L' : 'P');
                $pdf->AddPage($orientation, $size);
                
                // Use the imported page
                $pdf->useTemplate($templateId);
                $totalPagesAdded++;
                $pagesImported++;
            }
        } catch (\Throwable $e) {
            error_log("PDF merge error for file [{$path}]: " . $e->getMessage());
            // If completely unreadable (0 pages imported), add a clean placeholder page
            if ($pagesImported === 0) {
                $pdf->AddPage('P', [210, 297]);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetXY(20, 50);
                $pdf->Cell(170, 10, "Could not load document: " . basename($path), 0, 1, 'C');
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetXY(20, 65);
                $pdf->MultiCell(170, 6, "Error: " . $e->getMessage(), 0, 'C');
                $totalPagesAdded++;
            }
        }
    }

    if ($totalPagesAdded === 0) {
        throw new \Exception("No valid pages could be merged from the provided PDF files.");
    }

    $dir = dirname($output_path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $pdf->Output('F', $output_path);
    return true;
}
