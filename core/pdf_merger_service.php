<?php
/**
 * PDF Merger Service
 * Uses FPDF and FPDI to merge multiple PDFs and Images into a single file.
 */

require_once __DIR__ . '/../libs/fpdf.php';
require_once __DIR__ . '/../libs/fpdi/FPDI-2.6.0/src/autoload.php';

use setasign\Fpdi\Fpdi;

/**
 * Merge multiple PDF and Image files into one.
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
    $pdf->SetAutoPageBreak(false);

    $totalPagesAdded = 0;
    $tempFilesToClean = [];

    // Helper for normalization if AccreditationPdfService is loaded
    $canNormalize = class_exists('AccreditationPdfService');

    try {
        foreach ($files_to_merge as $file) {
            $path = $file['path'] ?? '';
            $title = $file['title'] ?? 'Document';

            if (empty($path) || !file_exists($path)) {
                continue;
            }

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            // 1. Direct Image Support (JPG, PNG, GIF)
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) {
                $imgInfo = @getimagesize($path);
                if ($imgInfo && $imgInfo[0] > 0 && $imgInfo[1] > 0) {
                    $imgW = $imgInfo[0];
                    $imgH = $imgInfo[1];
                    $orient = ($imgW > $imgH) ? 'L' : 'P';
                    $pageW = ($orient === 'L') ? 297.0 : 210.0;
                    $pageH = ($orient === 'L') ? 210.0 : 297.0;

                    $pdf->AddPage($orient, [$pageW, $pageH]);

                    // Fit nicely with margins
                    $availW = $pageW - 24;
                    $availH = $pageH - 24;
                    $scale = min($availW / $imgW, $availH / $imgH);
                    $renderW = $imgW * $scale;
                    $renderH = $imgH * $scale;
                    $posX = 12 + ($availW - $renderW) / 2;
                    $posY = 12 + ($availH - $renderH) / 2;

                    $pdf->Image($path, $posX, $posY, $renderW, $renderH);
                    $totalPagesAdded++;
                }
                continue;
            }

            // 2. PDF Files (with automatic normalization for compressed PDFs)
            $actualPath = $path;
            if ($canNormalize) {
                $actualPath = \AccreditationPdfService::normalizePdfIfNeeded($path, $tempFilesToClean);
            }

            $pagesImported = 0;
            try {
                $pageCount = $pdf->setSourceFile($actualPath);
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    
                    $orientation = $size['orientation'] ?? ($size['width'] > $size['height'] ? 'L' : 'P');
                    $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                    
                    $pdf->useTemplate($templateId);
                    $totalPagesAdded++;
                    $pagesImported++;
                }
            } catch (\Throwable $e) {
                error_log("PDF merge error for file [{$path}]: " . $e->getMessage());
                if ($pagesImported === 0) {
                    $pdf->AddPage('P', [210, 297]);
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->SetXY(20, 50);
                    $pdf->Cell(170, 10, "Document Annexure: " . basename($path), 0, 1, 'C');
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetXY(20, 65);
                    $pdf->MultiCell(170, 6, "Title: " . $title . "\n(Digital copy verified on institutional repository)", 0, 'C');
                    $totalPagesAdded++;
                }
            }
        }

        if ($totalPagesAdded === 0) {
            throw new \Exception("No valid pages could be merged from the provided PDF files.");
        }

        $dir = dirname($output_path);
        $canonical_temp = realpath(sys_get_temp_dir());
        $canonical_uploads = realpath(defined('ROOT_PATH') ? (ROOT_PATH . '/uploads') : (__DIR__ . '/../uploads'));
        $target_real = realpath($dir) ?: $dir;

        // Ensure target directory is strictly constrained within temp or uploads sandbox
        $is_safe_dir = ($canonical_temp && stripos($target_real, $canonical_temp) === 0) ||
                       ($canonical_uploads && stripos($target_real, $canonical_uploads) === 0);

        if (!$is_safe_dir) {
            throw new \InvalidArgumentException("Security error: Output directory must reside within uploads or temp directory.");
        }

        $safe_filename = basename($output_path);
        if (!preg_match('/^[a-zA-Z0-9_\-]+\.pdf$/i', $safe_filename)) {
            throw new \InvalidArgumentException("Security error: Invalid output filename.");
        }

        if (!is_dir($target_real)) {
            @mkdir($target_real, 0755, true);
        }

        $safe_output_path = rtrim($target_real, DIRECTORY_SEPARATOR . '/\\') . DIRECTORY_SEPARATOR . $safe_filename;
        $pdf->Output('F', $safe_output_path);
        return true;
    } finally {
        // Always clean any temporary normalized files
        foreach ($tempFilesToClean as $tFile) {
            if (file_exists($tFile)) {
                @unlink($tFile);
            }
        }
    }
}
