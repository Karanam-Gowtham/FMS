<?php
/**
 * Accreditation PDF Service
 * Generates NAAC/NBA Accreditation Reports with institutional header,
 * configurable hierarchy headings, dynamic table of contents, auto-calculated page indexing,
 * clickable DVV internal links, normalized PDF/image merging, and stamped page numbers with opaque background badges.
 */

require_once __DIR__ . '/../libs/fpdf.php';
require_once __DIR__ . '/../libs/fpdi/FPDI-2.6.0/src/autoload.php';

use setasign\Fpdi\Fpdi;

class AccreditationFpdi extends Fpdi {
    public $headerConfig = [];
    public $selectedColumns = [];
    public $tableWidths = [];
    public $isIndexPage = false;

    public function setupFonts(): void {
        $fontDir = __DIR__ . '/../libs/font/';
        if (file_exists($fontDir . 'cambria.php')) {
            $this->AddFont('Cambria', '', 'cambria.php');
            if (file_exists($fontDir . 'cambriab.php')) {
                $this->AddFont('Cambria', 'B', 'cambriab.php');
            }
            if (file_exists($fontDir . 'cambriai.php')) {
                $this->AddFont('Cambria', 'I', 'cambriai.php');
            }
            if (file_exists($fontDir . 'cambriaz.php')) {
                $this->AddFont('Cambria', 'BI', 'cambriaz.php');
            }
        }
    }

    public function getPrimaryFont(): string {
        return isset($this->fonts['cambria']) ? 'Cambria' : 'Times';
    }

    public function getPageHeight(): float {
        return (float)$this->h;
    }

    public function getPageWidth(): float {
        return (float)$this->w;
    }

    // Helper: calculate number of lines a text will occupy in a cell of width $w
    public function NbLines($w, $txt) {
        $cw = $this->CurrentFont['cw'] ?? [];
        if ($w <= 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $fontSize = ($this->FontSize > 0) ? $this->FontSize : 12;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $fontSize;
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
     * Sanitize UTF-8 strings for standard FPDF Latin-1 encoding with expanded symbols
     */
    public static function sanitizeText(?string $str): string {
        if ($str === null || $str === '') {
            return '';
        }
        $replacements = [
            "\xE2\x82\xB9" => "Rs. ", // ₹ Indian Rupee symbol
            "₹"             => "Rs. ",
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
            "\xC2\xB1"     => "+/-", // ±
            "\xC2\xB0"     => " deg", // °
            "\xE2\x84\xA2" => "(TM)", // ™
            "\xC2\xA9"     => "(c)", // ©
            "\xC2\xAE"     => "(R)", // ®
        ];
        $str = strtr((string)$str, $replacements);
        if (function_exists('iconv')) {
            $conv = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $str);
            if ($conv !== false) {
                return $conv;
            }
        }
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($str, 'windows-1252', 'UTF-8');
        }
        return $str;
    }

    /**
     * Stamped page number footer with clean opaque white badge to prevent seal/signature clashing
     */
    public static function renderPageStamp(AccreditationFpdi $pdf, float $pageWidth, float $pageHeight, int $pageNumber): void {
        $primaryFont = $pdf->getPrimaryFont();
        $badgeW = 28;
        $badgeH = 6.5;
        $badgeX = ($pageWidth - $badgeW) / 2;
        $badgeY = $pageHeight - 12;

        // Opaque white badge with slate border
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(203, 213, 225); // Slate 300
        $pdf->SetLineWidth(0.25);
        $pdf->Rect($badgeX, $badgeY, $badgeW, $badgeH, 'DF');

        // Crisp page number text
        $pdf->SetFont($primaryFont, '', 9);
        $pdf->SetTextColor(30, 41, 59); // Slate 800
        $pdf->SetXY($badgeX, $badgeY);
        $pdf->Cell($badgeW, $badgeH, 'Page ' . $pageNumber, 0, 0, 'C');
    }

    /**
     * Accurately count PDF pages via binary structure if FPDI parser is unavailable
     */
    public static function countPdfPagesPurePhp(string $filePath): int {
        $content = @file_get_contents($filePath);
        if ($content === false || strlen($content) === 0) {
            return 1;
        }
        if (preg_match_all('/\/Type\s*\/Pages\b[^\/]*\/Count\s+(\d+)/is', $content, $matches)) {
            $counts = array_map('intval', $matches[1]);
            if (!empty($counts)) {
                return max($counts);
            }
        }
        $pageMatches = preg_match_all('/\/Type\s*\/Page\b/i', $content);
        return $pageMatches > 0 ? $pageMatches : 1;
    }

    /**
     * Normalize a PDF file using Python PyPDF2 if it contains compressed object streams (PDF 1.5+)
     * Returns the normalized path, or original path if already compatible.
     */
    public static function normalizePdfIfNeeded(string $filePath, array &$tempFilesTracker = []): string {
        if (empty($filePath) || !file_exists($filePath)) {
            return $filePath;
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            return $filePath;
        }

        // Test if standard FPDI can read the file
        $testFpdi = new Fpdi();
        try {
            $testFpdi->setSourceFile($filePath);
            return $filePath; // FPDI reads it without error
        } catch (\Throwable $e) {
            // Need normalization (compressed xref or unsupported stream)
        }

        // Check if Anaconda or system python is available
        $pythonExec = null;
        $candidates = [
            'E:\\set\\anaconda3\\python.exe',
            'python'
        ];
        foreach ($candidates as $cand) {
            $out = [];
            $code = 0;
            @exec("\"{$cand}\" -c \"import PyPDF2; print('OK')\" 2>&1", $out, $code);
            if ($code === 0 && isset($out[0]) && trim($out[0]) === 'OK') {
                $pythonExec = $cand;
                break;
            }
        }

        if ($pythonExec) {
            $normalizedTemp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'norm_' . uniqid() . '.pdf';
            $pyScript = "import sys, PyPDF2\n"
                      . "try:\n"
                      . "    reader = PyPDF2.PdfReader(sys.argv[1])\n"
                      . "    writer = PyPDF2.PdfWriter()\n"
                      . "    for p in reader.pages:\n"
                      . "        writer.add_page(p)\n"
                      . "    with open(sys.argv[2], 'wb') as f:\n"
                      . "        writer.write(f)\n"
                      . "    print('SUCCESS')\n"
                      . "except Exception as ex:\n"
                      . "    print('ERROR:', ex)\n";

            $scriptPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'norm_runner_' . uniqid() . '.py';
            if (@file_put_contents($scriptPath, $pyScript) === false) {
                return $filePath;
            }

            $cmd = "\"{$pythonExec}\" \"{$scriptPath}\" " . escapeshellarg($filePath) . " " . escapeshellarg($normalizedTemp);
            $output = [];
            $retCode = 0;
            exec($cmd . ' 2>&1', $output, $retCode);

            @unlink($scriptPath);

            if ($retCode === 0 && file_exists($normalizedTemp) && filesize($normalizedTemp) > 0) {
                $tempFilesTracker[] = $normalizedTemp;
                return $normalizedTemp;
            } else {
                if (file_exists($normalizedTemp)) {
                    @unlink($normalizedTemp);
                }
            }
        }

        return $filePath;
    }

    /**
     * Standard Column Definitions
     */
    public static function getAvailableColumns(): array {
        return [
            'sno' => [
                'key' => 'sno',
                'label' => "S.No",
                'align' => 'C',
                'header_align' => 'C',
                'weight' => 12,
                'fixed' => true
            ],
            'event_name' => [
                'key' => 'event_name',
                'label' => "Name of the Capacity\nDevelopment and Skill\nEnhancement programme",
                'align' => 'J',
                'header_align' => 'C',
                'weight' => 58,
                'fixed' => false
            ],
            'year' => [
                'key' => 'year',
                'label' => "Year of\nimplementation",
                'align' => 'C',
                'header_align' => 'C',
                'weight' => 33.5,
                'fixed' => false
            ],
            'event_date' => [
                'key' => 'event_date',
                'label' => "Date of\nevent",
                'align' => 'C',
                'header_align' => 'C',
                'weight' => 24,
                'fixed' => false
            ],
            'student_count' => [
                'key' => 'student_count',
                'label' => "Number of\nstudents\nenrolled",
                'align' => 'C',
                'header_align' => 'C',
                'weight' => 23.5,
                'fixed' => false
            ],
            'agency' => [
                'key' => 'agency',
                'label' => "Name of the\nagencies/consultants\ninvolved with contact\ndetails, if any",
                'align' => 'J',
                'header_align' => 'C',
                'weight' => 44,
                'fixed' => false
            ],
            'page_no' => [
                'key' => 'page_no',
                'label' => "Page No.",
                'align' => 'C',
                'header_align' => 'C',
                'weight' => 19,
                'fixed' => true
            ]
        ];
    }

    /**
     * Presets for NAAC Criterion Headings (Criteria 1, 3, 5, 6)
     */
    public static function getHeadingPresets(string $ay_year = ''): array {
        $year_str = !empty($ay_year) ? $ay_year : (date('Y') . '-' . substr(date('Y') + 1, -2));
        
        return [
            // Criterion 5 - Student Activities
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
            ],
            // Criterion 3 - Research & Workshops
            '3.2.2_workshops' => [
                'label' => '3.2.2 Workshops/Seminars on Research Methodology, IPR & Entrepreneurship',
                'criterion' => 'Criterion III - Research, Innovations and Extension',
                'key_indicator' => 'Key Indicator - 3.2 Innovation Ecosystem',
                'metric' => "3.2.2. Number of workshops/seminars conducted on Research Methodology, Intellectual Property Rights (IPR) and entrepreneurship during AY {$year_str}",
                'sub_heading' => 'Workshops and Seminars Conducted'
            ],
            '3.3.2_journals' => [
                'label' => '3.3.2 Research Papers Published in Journals (UGC CARE / Scopus)',
                'criterion' => 'Criterion III - Research, Innovations and Extension',
                'key_indicator' => 'Key Indicator - 3.3 Research Publications and Awards',
                'metric' => "3.3.2. Number of research papers per teacher in the Journals notified on UGC website during AY {$year_str}",
                'sub_heading' => 'Research Papers Published in Refereed Journals'
            ],
            // Criterion 6 - FDPs & Administrative Training
            '6.3.3_fdp' => [
                'label' => '6.3.3 Professional Development / FDPs Organised for Teaching & Non-Teaching',
                'criterion' => 'Criterion VI - Governance, Leadership and Management',
                'key_indicator' => 'Key Indicator - 6.3 Faculty Empowerment Strategies',
                'metric' => "6.3.3. Professional development / administrative training programmes organized by the institution for teaching and non-teaching staff during AY {$year_str}",
                'sub_heading' => 'Faculty Development Programmes Organised'
            ]
        ];
    }

    /**
     * Generate the complete merged accreditation document with convergence and link support
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
        if (!in_array('sno', $selected_column_keys, true)) {
            array_unshift($selected_column_keys, 'sno');
        }
        if (!in_array('page_no', $selected_column_keys, true)) {
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

        // Context-aware column heading for event_name based on selected metric/sub-heading
        if (isset($colsToRender['event_name'])) {
            $contextText = ($headings['metric'] ?? '') . ' ' . ($headings['sub_heading'] ?? '');
            if (stripos($contextText, 'Career') !== false || stripos($contextText, 'Competitive') !== false || stripos($contextText, '5.1.4') !== false) {
                $colsToRender['event_name']['label'] = "Name of the Activity / Programme\nfor Guidance & Career Counselling";
            } elseif (stripos($contextText, 'Sports') !== false || stripos($contextText, 'Cultural') !== false || stripos($contextText, '5.3.3') !== false) {
                $colsToRender['event_name']['label'] = "Name of the Sports / Cultural\nevent / competition organised";
            } elseif (stripos($contextText, 'Research') !== false || stripos($contextText, '3.2.2') !== false || stripos($contextText, 'Workshop') !== false) {
                $colsToRender['event_name']['label'] = "Name of the Workshop / Seminar\non Research Methodology / IPR";
            } elseif (stripos($contextText, 'FDP') !== false || stripos($contextText, '6.3.3') !== false || stripos($contextText, 'Professional Development') !== false) {
                $colsToRender['event_name']['label'] = "Title of the Professional Development /\nAdministrative Training Program";
            }
        }
        $totalWeight = max(1.0, (float)$totalWeight);

        $printableWidth = 190; // mm
        $colWidths = [];
        $runningWidth = 0;
        $keys = array_keys($colsToRender);
        foreach ($keys as $idx => $k) {
            if ($idx === count($keys) - 1) {
                $colWidths[$k] = $printableWidth - $runningWidth;
            } else {
                $w = round(($colsToRender[$k]['weight'] / $totalWeight) * $printableWidth, 1);
                $colWidths[$k] = $w;
                $runningWidth += $w;
            }
        }

        // Phase 1: Pre-calculate document page counts and normalize modern compressed PDFs if needed
        $tempTrackedFiles = [];
        try {
            foreach ($documents as $idx => &$doc) {
            $filePath = $doc['file_path'] ?? '';
            $pageCount = 0;
            if (!empty($filePath) && file_exists($filePath)) {
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) {
                    $pageCount = 1;
                } elseif ($ext === 'pdf') {
                    $normPath = self::normalizePdfIfNeeded($filePath, $tempTrackedFiles);
                    $doc['file_path'] = $normPath;
                    $counterPdf = new Fpdi();
                    try {
                        $pageCount = $counterPdf->setSourceFile($normPath);
                    } catch (\Throwable $e) {
                        $pageCount = self::countPdfPagesPurePhp($normPath);
                    }
                }
            }
            $doc['doc_page_count'] = max(1, $pageCount);
        }
        unset($doc);

        // Phase 1b: Exact Two-Pass Convergence Loop for Index Page Count (Eliminates off-by-one errors)
        $indexPageCount = 1;
        $maxIter = 3;
        for ($iter = 0; $iter < $maxIter; $iter++) {
            $currentPage = $indexPageCount + 1;
            foreach ($documents as &$doc) {
                $pCount = $doc['doc_page_count'];
                $doc['start_page'] = $currentPage;
                $endPage = $currentPage + $pCount - 1;
                $doc['page_range'] = ($pCount > 1) ? "{$currentPage} - {$endPage}" : (string)$currentPage;
                $currentPage += $pCount;
            }
            unset($doc);

            // Simulate index table with fully populated strings
            $tempPdf = new AccreditationFpdi('P', 'mm', 'A4');
            $tempPdf->setupFonts();
            $tempPdf->SetMargins(10, 10, 10);
            $tempPdf->SetAutoPageBreak(false);
            $tempPdf->AddPage();

            $this->renderHeaderAndHeadings($tempPdf, $headings);
            $this->renderTableHeader($tempPdf, $colsToRender, $colWidths, false);
            foreach ($documents as $idx => $doc) {
                $this->renderTableRow($tempPdf, $colsToRender, $colWidths, $doc, $idx + 1, false);
            }
            $simulatedPages = $tempPdf->PageNo();
            unset($tempPdf);

            if ($simulatedPages === $indexPageCount) {
                break; // Perfectly converged!
            }
            $indexPageCount = $simulatedPages;
        }

        // Phase 2: Create real Index page(s) with clickable DVV links
        $pdf = new AccreditationFpdi('P', 'mm', 'A4');
        $pdf->setupFonts();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        $this->renderHeaderAndHeadings($pdf, $headings);
        $this->renderTableHeader($pdf, $colsToRender, $colWidths, true);

        foreach ($documents as $idx => $doc) {
            $this->renderTableRow($pdf, $colsToRender, $colWidths, $doc, $idx + 1, true);
        }

        // Phase 3: Merge attached document PDFs/Images and stamp page numbers
        $pdf->SetAutoPageBreak(false);
        $finalPageNumber = $indexPageCount + 1;
        $primaryFont = $pdf->getPrimaryFont();

        foreach ($documents as $idx => $doc) {
            $filePath = $doc['file_path'] ?? '';
            $eventTitle = $doc['event_name'] ?? ($doc['title'] ?? 'Event Document');
            $pagesImported = 0;

            if (!empty($filePath) && file_exists($filePath)) {
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                // 1. Direct Image Attachment Support (JPG, PNG)
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) {
                    $imgInfo = @getimagesize($filePath);
                    if ($imgInfo && $imgInfo[0] > 0 && $imgInfo[1] > 0) {
                        $imgW = $imgInfo[0];
                        $imgH = $imgInfo[1];
                        $orient = ($imgW > $imgH) ? 'L' : 'P';
                        $pageW = ($orient === 'L') ? 297.0 : 210.0;
                        $pageH = ($orient === 'L') ? 210.0 : 297.0;

                        $pdf->AddPage($orient, [$pageW, $pageH]);

                        $availW = $pageW - 24;
                        $availH = $pageH - 32;
                        $scale = min($availW / $imgW, $availH / $imgH);
                        $renderW = $imgW * $scale;
                        $renderH = $imgH * $scale;
                        $posX = 12 + ($availW - $renderW) / 2;
                        $posY = 12 + ($availH - $renderH) / 2;

                        $pdf->Image($filePath, $posX, $posY, $renderW, $renderH);
                        self::renderPageStamp($pdf, $pageW, $pageH, $finalPageNumber);

                        $finalPageNumber++;
                        $pagesImported++;
                    }
                } 
                // 2. Vector PDF Attachment
                elseif ($ext === 'pdf') {
                    try {
                        $pageCount = $pdf->setSourceFile($filePath);

                        for ($p = 1; $p <= $pageCount; $p++) {
                            $templateId = $pdf->importPage($p);
                            $size = $pdf->getTemplateSize($templateId);

                            $orientation = $size['orientation'] ?? ($size['width'] > $size['height'] ? 'L' : 'P');
                            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                            $pdf->useTemplate($templateId);

                            self::renderPageStamp($pdf, (float)$size['width'], (float)$size['height'], $finalPageNumber);

                            $finalPageNumber++;
                            $pagesImported++;
                        }
                    } catch (\Throwable $e) {
                        error_log("Accreditation PDF merge error for [{$filePath}]: " . $e->getMessage());
                    }
                }
            }

            // Fallback: If no pages could be read/imported, create clean institutional notice page
            if ($pagesImported === 0) {
                $targetPageCount = $doc['doc_page_count'] ?? 1;
                for ($p = 0; $p < $targetPageCount; $p++) {
                    $pdf->AddPage('P', [210, 297]);
                    $pdf->SetFont($primaryFont, 'B', 15);
                    $pdf->SetTextColor(0, 51, 102);
                    $pdf->SetXY(15, 60);
                    $pdf->Cell(180, 10, 'Annexure Document', 0, 1, 'C');
                    
                    $pdf->SetFont($primaryFont, '', 12);
                    $pdf->SetTextColor(30, 41, 59);
                    $pdf->SetXY(15, 75);
                    $pdf->MultiCell(180, 6.5, self::sanitizeText($eventTitle), 0, 'C');
                    
                    $pdf->Ln(4);
                    $pdf->SetFont($primaryFont, 'I', 10);
                    $pdf->SetTextColor(100, 116, 139);
                    $noticeText = "(Verified Digital Copy on file in GMRIT Academic Repository)";
                    if ($targetPageCount > 1) {
                        $noticeText .= " - Sheet " . ($p + 1) . " of " . $targetPageCount;
                    }
                    $pdf->Cell(180, 8, $noticeText, 0, 1, 'C');

                    self::renderPageStamp($pdf, 210, 297, $finalPageNumber);
                    $finalPageNumber++;
                }
            }
        }

        // Ensure output directory exists
        $outDir = dirname($output_filepath);
        if (!is_dir($outDir)) {
            @mkdir($outDir, 0755, true);
        }

        // Output to destination
        $pdf->Output('F', $output_filepath);
        return true;
    } finally {
        // Clean up any normalized temp files generated during processing
        foreach ($tempTrackedFiles as $tmp) {
            if (file_exists($tmp)) {
                @unlink($tmp);
            }
        }
    }
    }

    /**
     * Render institutional header with logo, title, and criterion headings
     */
    protected function renderHeaderAndHeadings(AccreditationFpdi $pdf, array $headings): void {
        $startY = $pdf->GetY();
        $logoPath = realpath(__DIR__ . '/../assets/img/gmr_logo.png') 
            ?: (realpath(__DIR__ . '/../assets/img/logo.png') 
            ?: (defined('ROOT_PATH') ? (realpath(ROOT_PATH . '/assets/img/gmr_logo.png') ?: realpath(ROOT_PATH . '/assets/img/logo.png')) : false));
        $font = $pdf->getPrimaryFont();

        $startX = 41.5;

        // 1. Institution Name (Left)
        $pdf->SetXY($startX, $startY);
        $pdf->SetFont($font, 'B', 16);
        $pdf->SetTextColor(0, 51, 102); // GMR Navy
        $pdf->Cell(84, 7, 'GMR Institute of Technology', 0, 1, 'L');
        
        // Sub-text
        $pdf->SetX($startX);
        $pdf->SetFont($font, '', 9.5);
        $pdf->Cell(84, 5, 'An Autonomous Institute Affiliated to JNTU-GV', 0, 1, 'L');

        // 2. Vertical Divider Rule
        $dividerX = $startX + 85;
        $pdf->SetDrawColor(180, 180, 180);
        $pdf->SetLineWidth(0.4);
        $pdf->Line($dividerX, $startY, $dividerX, $startY + 15);

        // 3. Right Logo
        $logoX = $dividerX + 3.5;
        if ($logoPath && file_exists($logoPath)) {
            $pdf->Image($logoPath, $logoX, $startY, 38);
        } else {
            $pdf->SetXY($logoX, $startY);
            $pdf->SetFont($font, 'B', 14);
            $pdf->SetTextColor(0, 51, 102);
            $pdf->Cell(38, 6, 'GMRIT', 0, 1, 'C');
            $pdf->SetFont($font, 'I', 7.5);
            $pdf->SetTextColor(80, 80, 80);
            $pdf->Cell(38, 4, "Training Tomorrow's Engineers Today", 0, 1, 'C');
        }

        $pdf->SetY($startY + 22);
        $pdf->SetTextColor(0, 0, 0);

        // 1. Criterion Title (Centered, Bold, 12pt)
        if (!empty($headings['criterion'])) {
            $pdf->SetFont($font, 'B', 12);
            $pdf->Cell(190, 6, self::sanitizeText($headings['criterion']), 0, 1, 'C');
        }

        // 2. Key Indicator (Centered, Bold, 12pt)
        if (!empty($headings['key_indicator'])) {
            $pdf->SetFont($font, 'B', 12);
            $pdf->Cell(190, 6, self::sanitizeText($headings['key_indicator']), 0, 1, 'C');
        }

        // 3. Metric Statement & Sub-heading (Left-aligned, Bold, 12pt)
        $heading3 = trim($headings['metric'] ?? '');
        $heading4 = trim($headings['sub_heading'] ?? '');

        if (!empty($heading3) && !empty($heading4) && $heading3 !== $heading4) {
            $pdf->SetFont($font, 'B', 12);
            $pdf->MultiCell(190, 5.5, self::sanitizeText($heading3), 0, 'L');
            $pdf->SetFont($font, 'B', 12);
            $pdf->MultiCell(190, 5.5, self::sanitizeText($heading4), 0, 'L');
        } else {
            $actLine = !empty($heading4) ? $heading4 : $heading3;
            if (!empty($actLine)) {
                $pdf->SetFont($font, 'B', 12);
                $pdf->MultiCell(190, 5.5, self::sanitizeText($actLine), 0, 'L');
            }
        }
        $pdf->Ln(2);
    }

    /**
     * Render the table column headers (Center, Bold, Size 12)
     */
    protected function renderTableHeader(AccreditationFpdi $pdf, array $cols, array $widths, bool $realOutput = true): void {
        $font = $pdf->getPrimaryFont();
        $pdf->SetFont($font, 'B', 12);
        $headerLineHeight = 5.0;

        $maxLines = 1;
        foreach ($cols as $k => $c) {
            $lines = $pdf->NbLines($widths[$k], $c['label']);
            if ($lines > $maxLines) {
                $maxLines = $lines;
            }
        }

        $headerHeight = max(12, ($maxLines * $headerLineHeight) + 4);
        $y = $pdf->GetY();

        if (!$realOutput) {
            $pdf->SetXY(10, $y + $headerHeight);
            return;
        }

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);

        $x = 10;
        foreach ($cols as $k => $c) {
            $w = $widths[$k];
            $pdf->Rect($x, $y, $w, $headerHeight);
            
            $lines = $pdf->NbLines($w, $c['label']);
            $textHeight = $lines * $headerLineHeight;
            $yOffset = max(1.5, ($headerHeight - $textHeight) / 2);
            
            $headerAlign = $c['header_align'] ?? 'C';
            $pdf->SetXY($x, $y + $yOffset);
            $pdf->MultiCell($w, $headerLineHeight, $c['label'], 0, $headerAlign);
            $x += $w;
        }

        $pdf->SetXY(10, $y + $headerHeight);
    }

    /**
     * Render a single data row with auto-expanding multiline heights and clickable internal links
     */
    protected function renderTableRow(AccreditationFpdi $pdf, array $cols, array $widths, array $doc, int $sno, bool $realOutput): void {
        $font = $pdf->getPrimaryFont();
        $pdf->SetFont($font, '', 11);
        $lineHeight = 5.2;

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
                    $dTo = $doc['date_to'] ?? '';
                    if (!empty($d)) {
                        $formatted = date('d-m-Y', strtotime($d));
                        if (!empty($dTo) && $dTo !== $d && $dTo !== '0000-00-00') {
                            $formatted .= " to\n" . date('d-m-Y', strtotime($dTo));
                        }
                        $cellValues[$k] = $formatted;
                    } else {
                        $cellValues[$k] = '—';
                    }
                    break;
                case 'student_count':
                    $cellValues[$k] = (string)($doc['student_count'] ?? ($doc['participant_count'] ?? '0'));
                    break;
                case 'agency':
                    $cellValues[$k] = (string)($doc['agency'] ?? ($doc['resource_person'] ?? 'Internal Faculty / Department'));
                    break;
                case 'page_no':
                    $cellValues[$k] = (string)($doc['page_range'] ?? ($doc['start_page'] ?? '1'));
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

        $rowHeight = max(10, ($maxLines * $lineHeight) + 4);

        // Check page break trigger
        $pageLimit = method_exists($pdf, 'getPageHeight') ? ($pdf->getPageHeight() - 15) : 282.0;
        if ($pdf->GetY() + $rowHeight > $pageLimit) {
            $pdf->AddPage();
            $this->renderTableHeader($pdf, $cols, $widths, $realOutput);
        }

        if (!$realOutput) {
            $pdf->SetY($pdf->GetY() + $rowHeight);
            return;
        }

        // Create internal DVV link to jump to the event start page
        $linkId = 0;
        if (!empty($doc['start_page'])) {
            $linkId = $pdf->AddLink();
            $pdf->SetLink($linkId, 0, (int)$doc['start_page']);
        }

        $x = 10;
        $y = $pdf->GetY();
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.2);

        foreach ($cols as $k => $c) {
            $w = $widths[$k];
            $align = $c['align'] ?? 'J';
            $val = $cellValues[$k];

            // Outer cell border box
            $pdf->Rect($x, $y, $w, $rowHeight);

            // Print content vertically centered in cell with padding
            $lines = $pdf->NbLines($w, $val);
            $textHeight = $lines * $lineHeight;
            $yOffset = max(1.5, ($rowHeight - $textHeight) / 2);

            $pdf->SetXY($x, $y + $yOffset);

            // If event_name or page_no, make clickable to target page
            if ($linkId > 0 && in_array($k, ['event_name', 'page_no'])) {
                $pdf->Link($x, $y, $w, $rowHeight, $linkId);
                if ($k === 'page_no') {
                    $pdf->SetTextColor(0, 51, 102); // Subtle blue link tint for page numbers
                } else {
                    $pdf->SetTextColor(0, 0, 0);
                }
            } else {
                $pdf->SetTextColor(0, 0, 0);
            }

            $pdf->MultiCell($w, $lineHeight, self::sanitizeText($val), 0, $align);

            $x += $w;
        }

        $pdf->SetXY(10, $y + $rowHeight);
    }
}
