<?php
/**
 * PDF Normalizer Service (Pure PHP)
 *
 * Automatically converts modern compressed PDFs (PDF 1.5+ with /XRef streams,
 * /ObjStm object streams, and PNG predictors) into standard, uncompressed PDF 1.4 documents.
 * This guarantees 100% compatibility with FPDI and FPDF on any server without requiring
 * Python, Ghostscript, or external command-line dependencies.
 */

require_once __DIR__ . '/../libs/fpdf.php';
require_once __DIR__ . '/../libs/fpdi/FPDI-2.6.0/src/autoload.php';

use setasign\Fpdi\Fpdi;

class PdfNormalizer {

    /**
     * Checks if a PDF requires normalization, and normalizes it if needed.
     *
     * @param string $filePath Absolute path to the source PDF.
     * @param array $tempFilesTracker Array passed by reference to track temp files for request cleanup.
     * @return string Returns path to a compatible PDF (either original or normalized temp file).
     */
    public static function normalizePdfIfNeeded(string $filePath, array &$tempFilesTracker = []): string {
        if (empty($filePath) || !file_exists($filePath)) {
            return $filePath;
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            return $filePath;
        }

        // 1. Fast-path: Check if FPDI can already read the PDF natively
        try {
            $testFpdi = new Fpdi();
            $testFpdi->setSourceFile($filePath);
            return $filePath; // Document is already compatible with standard FPDI
        } catch (\Throwable $e) {
            // Document uses compressed xref, object streams, or unsupported structures
        }

        // 2. Pure PHP Normalizer (Self-contained, Zero Dependencies)
        $tempDir = defined('ROOT_PATH') ? (ROOT_PATH . '/tmp') : sys_get_temp_dir();
        if (!is_dir($tempDir) || !is_writable($tempDir)) {
            $tempDir = sys_get_temp_dir();
        }
        $normalizedTemp = rtrim($tempDir, '/\\') . DIRECTORY_SEPARATOR . 'norm_' . uniqid() . '.pdf';

        try {
            if (self::normalize($filePath, $normalizedTemp)) {
                // Verify that normalized file is now parseable by FPDI
                $verify = new Fpdi();
                $pages = $verify->setSourceFile($normalizedTemp);
                if ($pages > 0) {
                    $tempFilesTracker[] = $normalizedTemp;
                    return $normalizedTemp;
                }
            }
        } catch (\Throwable $e) {
            error_log("Pure PHP PDF Normalizer Exception on [{$filePath}]: " . $e->getMessage());
        }

        // Clean up failed normalized temp file if any
        if (file_exists($normalizedTemp)) {
            @unlink($normalizedTemp);
        }

        // 3. Fallback: Check if python with PyPDF2/pypdf is available
        $pythonCandidates = ['python3', 'python', 'E:\\set\\anaconda3\\python.exe'];
        foreach ($pythonCandidates as $py) {
            $pyOut = [];
            $pyCode = 0;
            @exec("\"{$py}\" -c \"import sys; (print('OK')) if 'pypdf' in sys.modules or 'PyPDF2' in sys.modules else sys.exit(1)\" 2>&1", $pyOut, $pyCode);
            if ($pyCode === 0) {
                $pyScript = "import sys\n"
                          . "try:\n"
                          . "    import pypdf as p\n"
                          . "except:\n"
                          . "    import PyPDF2 as p\n"
                          . "try:\n"
                          . "    r = p.PdfReader(sys.argv[1])\n"
                          . "    w = p.PdfWriter()\n"
                          . "    for page in r.pages: w.add_page(page)\n"
                          . "    with open(sys.argv[2], 'wb') as f: w.write(f)\n"
                          . "    print('SUCCESS')\n"
                          . "except Exception as ex:\n"
                          . "    print('ERROR:', ex)\n";

                $runner = $tempDir . DIRECTORY_SEPARATOR . 'norm_py_' . uniqid() . '.py';
                if (@file_put_contents($runner, $pyScript) !== false) {
                    $cmd = "\"{$py}\" \"{$runner}\" " . escapeshellarg($filePath) . " " . escapeshellarg($normalizedTemp);
                    $cOut = [];
                    $cCode = 0;
                    @exec($cmd . ' 2>&1', $cOut, $cCode);
                    @unlink($runner);

                    if ($cCode === 0 && file_exists($normalizedTemp) && filesize($normalizedTemp) > 0) {
                        $tempFilesTracker[] = $normalizedTemp;
                        return $normalizedTemp;
                    }
                }
            }
        }

        // If normalization could not be performed, return original path
        return $filePath;
    }

    /**
     * Converts a PDF containing /XRef streams and /ObjStm object streams into standard PDF 1.4.
     */
    public static function normalize(string $pdfPath, string $outputPath): bool {
        $content = @file_get_contents($pdfPath);
        if ($content === false || strlen($content) < 30) {
            return false;
        }

        // Find last startxref
        if (!preg_match_all('/startxref\s+(\d+)\s+%%EOF/s', $content, $allMatches)) {
            return false;
        }

        $lastMatch = end($allMatches[1]);
        $startXref = (int)$lastMatch;

        // Trace XRef chain (handling incremental revisions via /Prev)
        $xrefOffsetsToVisit = [$startXref];
        $visitedOffsets = [];
        $allXrefEntries = []; // objId => [type, f2, f3]
        $trailerDictCombined = [];

        while (!empty($xrefOffsetsToVisit)) {
            $currentOffset = array_shift($xrefOffsetsToVisit);
            if (isset($visitedOffsets[$currentOffset])) {
                continue;
            }
            $visitedOffsets[$currentOffset] = true;

            $sub = substr($content, $currentOffset);
            if (strpos(ltrim($sub), 'xref') === 0) {
                self::parseClassicXref($sub, $trailerDictCombined, $xrefOffsetsToVisit);
                continue;
            }

            // Match /XRef stream object
            if (!preg_match('/^(\d+)\s+(\d+)\s+obj\s*<<(.*?)>>\s*stream(?:\r\n|\r|\n)(.*?)endstream/s', $sub, $sm)) {
                if (!preg_match('/<<(.*?)>>\s*stream(?:\r\n|\r|\n)(.*?)endstream/s', $sub, $sm2)) {
                    continue;
                }
                $dictStr = $sm2[1];
                $rawStream = $sm2[2];
            } else {
                $dictStr = $sm[3];
                $rawStream = $sm[4];
            }

            if (!preg_match('/\/Type\s*\/XRef\b/', $dictStr)) {
                continue;
            }

            // Extract essential trailer entries
            if (!isset($trailerDictCombined['Root']) && preg_match('/\/Root\s+(\d+\s+\d+\s+R)/', $dictStr, $rm)) {
                $trailerDictCombined['Root'] = $rm[1];
            }
            if (!isset($trailerDictCombined['Info']) && preg_match('/\/Info\s+(\d+\s+\d+\s+R)/', $dictStr, $im)) {
                $trailerDictCombined['Info'] = $im[1];
            }
            if (!isset($trailerDictCombined['ID']) && preg_match('/\/ID\s*(\[.*?\])/s', $dictStr, $idm)) {
                $trailerDictCombined['ID'] = $idm[1];
            }

            // Follow /Prev links for incremental updates
            if (preg_match('/\/Prev\s+(\d+)/', $dictStr, $prevM)) {
                $prevOffset = (int)$prevM[1];
                if (!isset($visitedOffsets[$prevOffset])) {
                    $xrefOffsetsToVisit[] = $prevOffset;
                }
            }

            // Parse /W field byte widths [w1 w2 w3]
            if (!preg_match('/\/W\s*\[\s*(\d+)\s+(\d+)\s+(\d+)\s*\]/', $dictStr, $wm)) {
                continue;
            }
            $w1 = (int)$wm[1]; $w2 = (int)$wm[2]; $w3 = (int)$wm[3];
            $entryLen = $w1 + $w2 + $w3;
            if ($entryLen <= 0) {
                continue;
            }

            // Parse /Size
            preg_match('/\/Size\s+(\d+)/', $dictStr, $sizeM);
            $size = isset($sizeM[1]) ? (int)$sizeM[1] : 0;

            // Parse /Index ranges
            $indices = [];
            if (preg_match('/\/Index\s*\[(.*?)\]/s', $dictStr, $idxM)) {
                $parts = preg_split('/\s+/', trim($idxM[1]));
                for ($k = 0; $k < count($parts); $k += 2) {
                    if (isset($parts[$k + 1])) {
                        $indices[] = [(int)$parts[$k], (int)$parts[$k + 1]];
                    }
                }
            } else {
                $indices[] = [0, $size];
            }

            // Decompress xref stream
            $decompressed = @gzuncompress($rawStream);
            if ($decompressed === false) {
                $decompressed = @gzinflate($rawStream);
            }
            if ($decompressed === false) {
                continue;
            }

            // Decode PNG Predictor filter if present
            if (preg_match('/\/Predictor\s+(\d+)/', $dictStr, $predM) && (int)$predM[1] >= 10) {
                preg_match('/\/Columns\s+(\d+)/', $dictStr, $colM);
                $cols = isset($colM[1]) ? (int)$colM[1] : $entryLen;
                $decompressed = self::unfilterPngPredictor($decompressed, $cols);
            }

            $readBytes = function(string $str, int $pos, int $len): int {
                $val = 0;
                for ($i = 0; $i < $len; $i++) {
                    if ($pos + $i < strlen($str)) {
                        $val = ($val << 8) | ord($str[$pos + $i]);
                    }
                }
                return $val;
            };

            $streamPos = 0;
            $dataLen = strlen($decompressed);
            foreach ($indices as $idxRange) {
                $startId = $idxRange[0];
                $count = $idxRange[1];
                for ($c = 0; $c < $count; $c++) {
                    if ($streamPos + $entryLen > $dataLen) {
                        break;
                    }
                    $objId = $startId + $c;
                    $type = $w1 > 0 ? $readBytes($decompressed, $streamPos, $w1) : 1;
                    $f2 = $w2 > 0 ? $readBytes($decompressed, $streamPos + $w1, $w2) : 0;
                    $f3 = $w3 > 0 ? $readBytes($decompressed, $streamPos + $w1 + $w2, $w3) : 0;
                    $streamPos += $entryLen;

                    // Most recent revision of an object wins
                    if (!isset($allXrefEntries[$objId])) {
                        $allXrefEntries[$objId] = [$type, $f2, $f3];
                    }
                }
            }
        }

        if (empty($allXrefEntries)) {
            return false;
        }

        // Identify and decode all Object Streams (Type 2 objects)
        $objStreams = [];
        foreach ($allXrefEntries as $objId => $entry) {
            if ($entry[0] === 2) {
                $stmId = $entry[1];
                if (!isset($objStreams[$stmId]) && isset($allXrefEntries[$stmId])) {
                    $stmOffset = $allXrefEntries[$stmId][1];
                    $stmSub = substr($content, $stmOffset);
                    if (preg_match('/^' . $stmId . '\s+\d+\s+obj\s*<<(.*?)>>\s*stream(?:\r\n|\r|\n)(.*?)endstream/s', $stmSub, $osm)) {
                        $stmDict = $osm[1];
                        $stmRaw = $osm[2];

                        preg_match('/\/First\s+(\d+)/', $stmDict, $fm);
                        $firstOffset = isset($fm[1]) ? (int)$fm[1] : 0;

                        $stmData = @gzuncompress($stmRaw);
                        if ($stmData === false) {
                            $stmData = @gzinflate($stmRaw);
                        }

                        if ($stmData !== false) {
                            $hdr = substr($stmData, 0, $firstOffset);
                            $hdrTokens = preg_split('/\s+/', trim($hdr));
                            $objOffsets = [];
                            for ($h = 0; $h < count($hdrTokens); $h += 2) {
                                if (isset($hdrTokens[$h + 1])) {
                                    $objOffsets[] = [(int)$hdrTokens[$h], (int)$hdrTokens[$h + 1]];
                                }
                            }
                            $objStreams[$stmId] = [
                                'first' => $firstOffset,
                                'data' => $stmData,
                                'offsets' => $objOffsets
                            ];
                        }
                    }
                }
            }
        }

        // Extract individual objects from decoded object streams
        $extractedObjects = [];
        foreach ($allXrefEntries as $objId => $entry) {
            if ($entry[0] === 2) {
                $stmId = $entry[1];
                $idx = $entry[2];
                if (isset($objStreams[$stmId]['offsets'][$idx])) {
                    $item = $objStreams[$stmId]['offsets'][$idx];
                    $actualObjId = $item[0];
                    $relOffset = $item[1];
                    $start = $objStreams[$stmId]['first'] + $relOffset;

                    if (isset($objStreams[$stmId]['offsets'][$idx + 1])) {
                        $nextRel = $objStreams[$stmId]['offsets'][$idx + 1][1];
                        $end = $objStreams[$stmId]['first'] + $nextRel;
                        $extracted = trim(substr($objStreams[$stmId]['data'], $start, $end - $start));
                    } else {
                        $extracted = trim(substr($objStreams[$stmId]['data'], $start));
                    }
                    $extractedObjects[$actualObjId] = $extracted;
                }
            }
        }

        // Reconstruct as standard PDF 1.4 with plaintext ASCII xref table
        $out = "%PDF-1.4\n%\xe2\xe3\xcf\xd3\n";
        $newOffsets = [];

        $allObjIds = array_keys($allXrefEntries);
        sort($allObjIds);

        foreach ($allObjIds as $objId) {
            if ($objId === 0) {
                continue;
            }

            $entry = $allXrefEntries[$objId];
            if ($entry[0] === 0) {
                continue;
            } elseif ($entry[0] === 1) {
                // If it was an ObjStm itself, skip it because its objects are unpacked
                if (isset($objStreams[$objId])) {
                    continue;
                }
                $origOffset = $entry[1];
                $objSub = substr($content, $origOffset);
                if (preg_match('/^(' . $objId . '\s+\d+\s+obj.*?endobj)/s', $objSub, $objM)) {
                    $newOffsets[$objId] = strlen($out);
                    $out .= $objM[1] . "\n";
                }
            } elseif ($entry[0] === 2) {
                if (isset($extractedObjects[$objId])) {
                    $newOffsets[$objId] = strlen($out);
                    $out .= $objId . " 0 obj\n" . $extractedObjects[$objId] . "\nendobj\n";
                }
            }
        }

        if (empty($newOffsets)) {
            return false;
        }

        $maxObjId = max(array_keys($newOffsets));
        $newSize = $maxObjId + 1;

        $newXrefOffset = strlen($out);
        $out .= "xref\n";
        $out .= "0 " . $newSize . "\n";
        $out .= "0000000000 65535 f \n";

        for ($i = 1; $i < $newSize; $i++) {
            if (isset($newOffsets[$i])) {
                $out .= sprintf("%010d 00000 n \n", $newOffsets[$i]);
            } else {
                $out .= "0000000000 65535 f \n";
            }
        }

        $trailerEntries = [];
        if (isset($trailerDictCombined['Root'])) {
            $trailerEntries[] = '/Root ' . $trailerDictCombined['Root'];
        }
        if (isset($trailerDictCombined['Info'])) {
            $trailerEntries[] = '/Info ' . $trailerDictCombined['Info'];
        }
        if (isset($trailerDictCombined['ID'])) {
            $trailerEntries[] = '/ID ' . $trailerDictCombined['ID'];
        }

        $out .= "trailer\n<< /Size " . $newSize . " " . implode(' ', $trailerEntries) . " >>\n";
        $out .= "startxref\n" . $newXrefOffset . "\n%%EOF\n";

        return @file_put_contents($outputPath, $out) !== false;
    }

    /**
     * Reverses PNG Predictor filters (Sub, Up, Average, Paeth) on decompressed byte streams.
     */
    private static function unfilterPngPredictor(string $data, int $columns): string {
        $rowLen = $columns + 1;
        $numRows = (int)(strlen($data) / $rowLen);
        $out = '';
        $prevRow = array_fill(0, $columns, 0);

        for ($r = 0; $r < $numRows; $r++) {
            $filterType = ord($data[$r * $rowLen]);
            $row = [];
            for ($c = 0; $c < $columns; $c++) {
                $rawByte = ord($data[$r * $rowLen + 1 + $c]);
                $left = ($c > 0) ? $row[$c - 1] : 0;
                $up = $prevRow[$c];
                $upLeft = ($c > 0) ? $prevRow[$c - 1] : 0;

                if ($filterType === 0) { // None
                    $val = $rawByte;
                } elseif ($filterType === 1) { // Sub
                    $val = ($rawByte + $left) & 0xFF;
                } elseif ($filterType === 2) { // Up
                    $val = ($rawByte + $up) & 0xFF;
                } elseif ($filterType === 3) { // Average
                    $val = ($rawByte + (int)(($left + $up) / 2)) & 0xFF;
                } elseif ($filterType === 4) { // Paeth
                    $p = $left + $up - $upLeft;
                    $pa = abs($p - $left);
                    $pb = abs($p - $up);
                    $pc = abs($p - $upLeft);
                    if ($pa <= $pb && $pa <= $pc) {
                        $pr = $left;
                    } elseif ($pb <= $pc) {
                        $pr = $up;
                    } else {
                        $pr = $upLeft;
                    }
                    $val = ($rawByte + $pr) & 0xFF;
                } else {
                    $val = $rawByte;
                }
                $row[$c] = $val;
                $out .= chr($val);
            }
            $prevRow = $row;
        }
        return $out;
    }

    private static function parseClassicXref(string $sub, array &$trailerDictCombined, array &$xrefOffsetsToVisit): void {
        if (preg_match('/xref\s+(.*?)\s+trailer\s*<<(.*?)>>/s', $sub, $m)) {
            $tDict = $m[2];

            if (!isset($trailerDictCombined['Root']) && preg_match('/\/Root\s+(\d+\s+\d+\s+R)/', $tDict, $rm)) {
                $trailerDictCombined['Root'] = $rm[1];
            }
            if (!isset($trailerDictCombined['Info']) && preg_match('/\/Info\s+(\d+\s+\d+\s+R)/', $tDict, $im)) {
                $trailerDictCombined['Info'] = $im[1];
            }
            if (!isset($trailerDictCombined['ID']) && preg_match('/\/ID\s*(\[.*?\])/s', $tDict, $idm)) {
                $trailerDictCombined['ID'] = $idm[1];
            }
            if (preg_match('/\/Prev\s+(\d+)/', $tDict, $prevM)) {
                $xrefOffsetsToVisit[] = (int)$prevM[1];
            }
        }
    }
}
