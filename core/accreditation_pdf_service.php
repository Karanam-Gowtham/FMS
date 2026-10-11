<?php
/**
 * Accreditation PDF Service
 * Generates NAAC/NBA Accreditation Reports with institutional header,
 * configurable hierarchy headings, dynamic table of contents, auto-calculated page indexing,
 * and sequential merged document appendices with stamped page numbers.
 */

require_once __DIR__ . '/../libs/fpdf.php';
require_once __DIR__ . '/../libs/fpdi/FPDI-2.6.0/src/autoload.php';

use setasign\Fpdi\Fpdi;

class AccreditationFpdi extends Fpdi {
    public $headerConfig = [];
    public $selectedColumns = [];
    public $tableWidths = [];
    public $isIndexPage = false;

    // Helper: calculate number of lines a text will occupy in a cell of width $w
    public function NbLines($w, $txt) {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', (string)$txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c] ?? 500;
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }
}

class AccreditationPdfService {

    /**
     * Sanitize UTF-8 strings for standard FPDF Latin-1 encoding
     */
    public static function sanitizeText(?string $str): string {
        if ($str === null || $str === '') {
            return '';
        }
        $replacements = [
            "\xE2\x80\x98" => "'", // ‘
            "\xE2\x80\x99" => "'", // ’
            "\xE2\x80\x9A" => "'", // ‚
            "\xE2\x80\x9C" => '"', // “
            "\xE2\x80\x9D" => '"', // ”
            "\xE2\x80\x9E" => '"', // „
            "\xE2\x80\x93" => "-", // – (en dash)
            "\xE2\x80\x94" => "-", // — (em dash)
            "\xE2\x80\xA6" => "...", // … (ellipsis)
            "\xE2\x80\xA2" => "*", // • (bullet)
            "\xC2\xA0"     => " ", // non-breaking space
            "\xE2\x88\x92" => "-", // minus sign
        ];
        $str = strtr((string)$str, $replacements);
        if (function_exists('iconv')) {
            $conv = @iconv('UTF-8', 'windows-1252//TRANSLIT', $str);
            if ($conv !== false) {
                return $conv;
            }
        }
        return utf8_decode($str);
    }

    /**
     * Standard Column Definitions
     */
    public static function getAvailableColumns(): array {
        return [
            'sno' => [
                'key' => 'sno',
                'label' => 'S.No',
                'align' => 'C',
                'weight' => 12,
                'fixed' => true
            ],
            'event_name' => [
                'key' => 'event_name',
                'label' => 'Name of the Capacity Development and Skill Enhancement programme',
                'align' => 'L',
                'weight' => 55,
                'fixed' => false
            ],
            'year' => [
                'key' => 'year',
                'label' => 'Year of implementation',
                'align' => 'C',
                'weight' => 22,
                'fixed' => false
            ],
            'event_date' => [
                'key' => 'event_date',
                'label' => 'Date of event',
                'align' => 'C',
                'weight' => 22,
                'fixed' => false
            ],
            'student_count' => [
                'key' => 'student_count',
                'label' => 'Number of students enrolled',
                'align' => 'C',
                'weight' => 24,
                'fixed' => false
            ],
            'agency' => [
                'key' => 'agency',
                'label' => 'Name of the agencies/consultants involved with contact details, if any',
                'align' => 'L',
                'weight' => 55,
                'fixed' => false
            ],
            'page_no' => [
                'key' => 'page_no',
                'label' => 'Page No.',
                'align' => 'C',
                'weight' => 16,
                'fixed' => true
            ]
        ];
    }

    /**
     * Presets for NAAC Criterion Headings
     */
    public static function getHeadingPresets(string $ay_year = ''): array {
        $year_str = !empty($ay_year) ? $ay_year : (date('Y') . '-' . substr(date('Y') + 1, -2));
        
        return [
            '5.1.3_all' => [
                'label' => '5.1.3 Capacity Development & Skill Enhancement (General)',
                'criterion' => 'Criterion V - Student Support and Progression',
                'key_indicator' => 'Key Indicator - 5.1 Student Support',
                'metric' => "5.1.3. Following Capacity development and skills enhancement activities are organised for improving student's capability during AY {$year_str}",
                'sub_heading' => 'Capacity Building and Skills Enhancement Initiatives'
            ],
            '5.1.3.4_tech' => [
                'label' => '5.1.3.4 Awareness of Trends in Technology',
                'criterion' => 'Criterion V - Student Support and Progression',
                'key_indicator' => 'Key Indicator - 5.1 Student Support',
                'metric' => "5.1.3. Following Capacity development and skills enhancement activities are organised for improving student's capability during AY {$year_str}",
                'sub_heading' => '5.1.3.4 Awareness of trends in technology'
            ],
            '5.1.3.1_soft' => [
                'label' => '5.1.3.1 Soft Skills',
                'criterion' => 'Criterion V - Student Support and Progression',
                'key_indicator' => 'Key Indicator - 5.1 Student Support',
                'metric' => "5.1.3. Following Capacity development and skills enhancement activities are organised for improving student's capability during AY {$year_str}",
                'sub_heading' => '5.1.3.1 Soft Skills activities organized during AY ' . $year_str
            ],
            '5.1.3.2_lang' => [
                'label' => '5.1.3.2 Language and Communication Skills',
                'criterion' => 'Criterion V - Student Support and Progression',
                'key_indicator' => 'Key Indicator - 5.1 Student Support',
                'metric' => "5.1.3. Following Capacity development and skills enhancement activities are organised for improving student's capability during AY {$year_str}",
                'sub_heading' => '5.1.3.2 Language and Communication Skills activities organized during AY ' . $year_str
            ],
            '5.1.3.1_2_combined' => [
                'label' => '5.1.3.1 & 5.1.3.2 Soft Skills & Language Skills (Combined)',
                'criterion' => 'Criterion V - Student Support and Progression',
                'key_indicator' => 'Key Indicator - 5.1 Student Support',
                'metric' => 'Key Indicator - 5.1 Student Support',
                'sub_heading' => "5.1.3.1 & 5.1.3.2 Soft Skills & Language and Communication Skills activities organized during the academic year {$year_str}"
            ],
            '5.1.3.3_life' => [
                'label' => '5.1.3.3 Life Skills (Yoga, Health & Hygiene)',
                'criterion' => 'Criterion V - Student Support and Progression',
                'key_indicator' => 'Key Indicator - 5.1 Student Support',
                'metric' => "5.1.3. Following Capacity development and skills enhancement activities are organised for improving student's capability during AY {$year_str}",
                'sub_heading' => '5.1.3.3 Life skills activities organized during AY ' . $year_str
            ],
            '5.1.4_career' => [
                'label' => '5.1.4 Guidance for Competitive Exams & Career Counselling',
                'criterion' => 'Criterion V - Student Support and Progression',
                'key_indicator' => 'Key Indicator - 5.1 Student Support',
                'metric' => "5.1.4. Average percentage of students benefited by guidance for competitive examinations and career counseling during AY {$year_str}",
                'sub_heading' => 'Guidance for Competitive Examinations and Career Counselling'
            ],
            '5.3.3_events' => [
                'label' => '5.3.3 Sports and Cultural Events / Competitions Organised',
                'criterion' => 'Criterion V - Student Support and Progression',
                'key_indicator' => 'Key Indicator - 5.3 Student Participation and Activities',
                'metric' => "5.3.3. Number of sports and cultural events / competitions organised by the institution during AY {$year_str}",
                'sub_heading' => 'Sports and Cultural Events Organised'
            ]
        ];
    }

    /**
     * Generate the complete merged accreditation document
     *
     * @param array $headings ['criterion' => ..., 'key_indicator' => ..., 'metric' => ..., 'sub_heading' => ...]
     * @param array $selected_column_keys e.g. ['sno', 'event_name', 'year', 'student_count', 'agency', 'page_no']
     * @param array $documents List of document data rows with attached file paths
     * @param string $output_filepath Path where the final PDF will be saved
     * @return bool
     */
    public function generateReport(array $headings, array $selected_column_keys, array $documents, string $output_filepath): bool {
        if (empty($documents)) {
            throw new \Exception("No documents provided for accreditation report.");
        }

        // Always ensure sno and page_no are present
        if (!in_array('sno', $selected_column_keys)) {
            array_unshift($selected_column_keys, 'sno');
        }
        if (!in_array('page_no', $selected_column_keys)) {
            $selected_column_keys[] = 'page_no';
        }

        // Determine printable widths on A4 Portrait (210mm - 20mm margins = 190mm)
        $allCols = self::getAvailableColumns();
        $colsToRender = [];
        $totalWeight = 0;
        foreach ($selected_column_keys as $k) {
            if (isset($allCols[$k])) {
                $colsToRender[$k] = $allCols[$k];
                $totalWeight += $allCols[$k]['weight'];
            }
        }

        $printableWidth = 190; // mm
        $colWidths = [];
        $runningWidth = 0;
        $keys = array_keys($colsToRender);
        foreach ($keys as $idx => $k) {
            if ($idx === count($keys) - 1) {
                // Last column takes remainder to perfectly fit 190mm
                $colWidths[$k] = $printableWidth - $runningWidth;
            } else {
                $w = round(($colsToRender[$k]['weight'] / $totalWeight) * $printableWidth, 1);
                $colWidths[$k] = $w;
                $runningWidth += $w;
            }
        }

        // Phase 1: Count pages in attached PDFs and calculate starting page numbers
        // We first simulate rendering the index table to get exact index page count
        $tempPdf = new AccreditationFpdi('P', 'mm', 'A4');
        $tempPdf->SetMargins(10, 10, 10);
        $tempPdf->SetAutoPageBreak(true, 15);
        $tempPdf->AddPage();
        
        $this->renderHeaderAndHeadings($tempPdf, $headings);
        $this->renderTableHeader($tempPdf, $colsToRender, $colWidths);
        foreach ($documents as $doc) {
            $this->renderTableRow($tempPdf, $colsToRender, $colWidths, $doc, 1, false);
        }
        $indexPageCount = $tempPdf->PageNo();
        unset($tempPdf);

        // Now calculate start pages for each document
        $counterPdf = new Fpdi();
        $docPageCounts = [];
        $currentPage = $indexPageCount + 1;

        foreach ($documents as $idx => &$doc) {
            $filePath = $doc['file_path'] ?? '';
            $pageCount = 0;
            if (!empty($filePath) && file_exists($filePath)) {
                try {
                    $pageCount = $counterPdf->setSourceFile($filePath);
                } catch (\Throwable $e) {
                    $pageCount = 1; // Fallback placeholder page
                }
            } else {
                $pageCount = 1; // Fallback placeholder page
            }
            $docPageCounts[$idx] = $pageCount;
            $doc['start_page'] = $currentPage;
            $currentPage += $pageCount;
        }
        unset($doc);

        // Phase 2: Create real Index page(s)
        $pdf = new AccreditationFpdi('P', 'mm', 'A4');
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $this->renderHeaderAndHeadings($pdf, $headings);
        $this->renderTableHeader($pdf, $colsToRender, $colWidths);

        foreach ($documents as $idx => $doc) {
            $this->renderTableRow($pdf, $colsToRender, $colWidths, $doc, $idx + 1, true);
        }

        // Phase 3: Merge attached document PDFs and stamp page numbers
        $pdf->SetAutoPageBreak(false); // Crucial for accurate bottom stamping without extra blank pages
        $finalPageNumber = $indexPageCount + 1;

        foreach ($documents as $idx => $doc) {
            $filePath = $doc['file_path'] ?? '';
            $eventTitle = $doc['event_name'] ?? ($doc['title'] ?? 'Event Document');
            $hasValidPdf = false;

            if (!empty($filePath) && file_exists($filePath)) {
                try {
                    $pageCount = $pdf->setSourceFile($filePath);
                    $hasValidPdf = true;

                    for ($p = 1; $p <= $pageCount; $p++) {
                        $templateId = $pdf->importPage($p);
                        $size = $pdf->getTemplateSize($templateId);

                        $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                        $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                        $pdf->useTemplate($templateId);

                        // Stamp page number at bottom center
                        $pdf->SetFont('Times', '', 9);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetXY(0, $size['height'] - 12);
                        $pdf->Cell($size['width'], 8, 'Page ' . $finalPageNumber, 0, 0, 'C');

                        $finalPageNumber++;
                    }
                } catch (\Throwable $e) {
                    $hasValidPdf = false;
                }
            }

            // Fallback: If no valid PDF could be read, create clean notice page
            if (!$hasValidPdf) {
                $pdf->AddPage('P', [210, 297]);
                $pdf->SetFont('Times', 'B', 14);
                $pdf->SetTextColor(0, 51, 102);
                $pdf->SetXY(15, 60);
                $pdf->Cell(180, 10, 'Annexure Document', 0, 1, 'C');
                $pdf->SetFont('Times', '', 12);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY(15, 75);
                $pdf->MultiCell(180, 6, self::sanitizeText($eventTitle), 0, 'C');
                $pdf->Ln(5);
                $pdf->SetFont('Times', 'I', 10);
                $pdf->SetTextColor(120, 120, 120);
                $pdf->Cell(180, 8, '(Digital copy on file in GMRIT Academic Repository)', 0, 1, 'C');

                $pdf->SetFont('Times', '', 9);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY(0, 297 - 12);
                $pdf->Cell(210, 8, 'Page ' . $finalPageNumber, 0, 0, 'C');

                $finalPageNumber++;
            }
        }

        // Output to file
        $pdf->Output('F', $output_filepath);
        return true;
    }

    /**
     * Render institutional header with logo, title, and criterion headings
     */
    protected function renderHeaderAndHeadings(AccreditationFpdi $pdf, array $headings): void {
        $startY = $pdf->GetY();
        $logoPath = realpath(__DIR__ . '/../assets/img/gmr_logo.png') ?: realpath(__DIR__ . '/../assets/img/logo.png');

        // Left Institution Text
        $pdf->SetXY(10, $startY);
        $pdf->SetFont('Times', 'B', 15);
        $pdf->SetTextColor(0, 51, 102); // GMRIT Navy
        $pdf->Cell(135, 7, 'GMR Institute of Technology', 0, 1, 'L');
        
        $pdf->SetFont('Times', '', 9.5);
        $pdf->Cell(135, 5, 'An Autonomous Institute Affiliated to JNTU-GV', 0, 1, 'L');

        // Vertical divider rule
        $dividerX = 148;
        $pdf->SetDrawColor(180, 180, 180);
        $pdf->SetLineWidth(0.3);
        $pdf->Line($dividerX, $startY, $dividerX, $startY + 15);

        // Right Logo
        if ($logoPath && file_exists($logoPath)) {
            // Place logo image at X=151, Y=startY, width=48mm
            $pdf->Image($logoPath, 151, $startY - 1, 48);
        } else {
            $pdf->SetXY(151, $startY);
            $pdf->SetFont('Times', 'B', 14);
            $pdf->SetTextColor(0, 51, 102);
            $pdf->Cell(48, 6, 'GMRIT', 0, 1, 'C');
            $pdf->SetFont('Times', 'I', 7.5);
            $pdf->SetTextColor(80, 80, 80);
            $pdf->Cell(48, 4, "Training Tomorrow's Engineers Today", 0, 1, 'C');
        }

        $pdf->SetY($startY + 20);

        // Headings Hierarchy
        $pdf->SetTextColor(0, 0, 0);

        // 1. Criterion Title (Centered, Bold, 12pt)
        if (!empty($headings['criterion'])) {
            $pdf->SetFont('Times', 'B', 12);
            $pdf->Cell(190, 6, self::sanitizeText($headings['criterion']), 0, 1, 'C');
        }

        // 2. Key Indicator (Centered, Bold, 11pt)
        if (!empty($headings['key_indicator'])) {
            $pdf->SetFont('Times', 'B', 11);
            $pdf->Cell(190, 6, self::sanitizeText($headings['key_indicator']), 0, 1, 'C');
        }

        $pdf->Ln(2);

        // 3. Metric Statement (Left-aligned, Bold, 10.5pt)
        if (!empty($headings['metric'])) {
            $pdf->SetFont('Times', 'B', 10.5);
            $pdf->MultiCell(190, 5.5, self::sanitizeText($headings['metric']), 0, 'L');
            $pdf->Ln(2);
        }

        // 4. Sub-heading / Specific Domain (Left-aligned, Bold, 10pt)
        if (!empty($headings['sub_heading'])) {
            $pdf->SetFont('Times', 'B', 10);
            $pdf->MultiCell(190, 5, self::sanitizeText($headings['sub_heading']), 0, 'L');
            $pdf->Ln(3);
        }
    }

    /**
     * Render the table column headers
     */
    protected function renderTableHeader(AccreditationFpdi $pdf, array $cols, array $widths): void {
        $pdf->SetFont('Times', 'B', 9);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);

        // Measure max lines needed across all headers
        $maxLines = 1;
        foreach ($cols as $k => $c) {
            $lines = $pdf->NbLines($widths[$k], $c['label']);
            if ($lines > $maxLines) {
                $maxLines = $lines;
            }
        }

        $headerHeight = max(10, $maxLines * 4.5);
        $x = 10;
        $y = $pdf->GetY();

        foreach ($cols as $k => $c) {
            $w = $widths[$k];
            $pdf->Rect($x, $y, $w, $headerHeight);
            
            // Vertically center text in cell
            $lines = $pdf->NbLines($w, $c['label']);
            $textHeight = $lines * 4;
            $yOffset = ($headerHeight - $textHeight) / 2;
            
            $pdf->SetXY($x, $y + $yOffset);
            $pdf->MultiCell($w, 4, $c['label'], 0, 'C');
            $x += $w;
        }

        $pdf->SetXY(10, $y + $headerHeight);
    }

    /**
     * Render a single data row with auto-expanding multiline heights
     */
    protected function renderTableRow(AccreditationFpdi $pdf, array $cols, array $widths, array $doc, int $sno, bool $realOutput): void {
        $pdf->SetFont('Times', '', 9.5);
        $lineHeight = 4.5;

        // Map data values
        $cellValues = [];
        foreach ($cols as $k => $c) {
            switch ($k) {
                case 'sno':
                    $cellValues[$k] = (string)$sno;
                    break;
                case 'event_name':
                    $cellValues[$k] = (string)($doc['event_name'] ?? ($doc['title'] ?? ''));
                    break;
                case 'year':
                    $cellValues[$k] = (string)($doc['year'] ?? ($doc['academic_year'] ?? ''));
                    break;
                case 'event_date':
                    $d = $doc['date_from'] ?? ($doc['event_date'] ?? '');
                    $cellValues[$k] = !empty($d) ? date('d-m-Y', strtotime($d)) : '—';
                    break;
                case 'student_count':
                    $cellValues[$k] = (string)($doc['student_count'] ?? ($doc['participant_count'] ?? '0'));
                    break;
                case 'agency':
                    $cellValues[$k] = (string)($doc['agency'] ?? ($doc['resource_person'] ?? 'Internal Faculty / Department'));
                    break;
                case 'page_no':
                    $cellValues[$k] = (string)($doc['start_page'] ?? '1');
                    break;
                default:
                    $cellValues[$k] = (string)($doc[$k] ?? '');
            }
        }

        // Calculate max lines required for this row
        $maxLines = 1;
        foreach ($cols as $k => $c) {
            $lines = $pdf->NbLines($widths[$k], $cellValues[$k]);
            if ($lines > $maxLines) {
                $maxLines = $lines;
            }
        }

        $rowHeight = max(8, $maxLines * $lineHeight);

        // Check page break trigger
        $pageLimit = method_exists($pdf, 'GetPageHeight') ? ($pdf->GetPageHeight() - 15) : ($pdf->h - 15);
        if ($pdf->GetY() + $rowHeight > $pageLimit) {
            $pdf->AddPage();
            if ($realOutput) {
                $this->renderTableHeader($pdf, $cols, $widths);
            }
        }

        if (!$realOutput) {
            // Just advance Y for simulation
            $pdf->SetY($pdf->GetY() + $rowHeight);
            return;
        }

        $x = 10;
        $y = $pdf->GetY();
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.2);

        foreach ($cols as $k => $c) {
            $w = $widths[$k];
            $align = $c['align'] ?? 'L';
            $val = $cellValues[$k];

            // Outer cell border box
            $pdf->Rect($x, $y, $w, $rowHeight);

            // Print content with top padding
            $pdf->SetXY($x + 1, $y + 1.5);
            $pdf->MultiCell($w - 2, $lineHeight, self::sanitizeText($val), 0, $align);

            $x += $w;
        }

        $pdf->SetXY(10, $y + $rowHeight);
    }
}
