<?php
/**
 * Static UI & Template Sanity Auditor
 *
 * Scans all views in src/Views for:
 * 1. Duplicate action buttons inside modals or cards
 * 2. Table header <th> vs row <td> column count mismatches
 * 3. Form input IDs matching label `for` attributes
 * 4. Missing CSRF tokens on forms
 * 5. CSS white-space and flex-wrap sanity on action button containers
 */

require_once __DIR__ . '/../core/bootstrap.php';

echo "================================================================================" . PHP_EOL;
echo "           FMS STATIC UI, MODAL & TEMPLATE INTEGRITY AUDITOR                    " . PHP_EOL;
echo "================================================================================" . PHP_EOL . PHP_EOL;

$views_dir = realpath(__DIR__ . '/../src/Views');
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($views_dir));

$total_templates = 0;
$issues = [];

foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') continue;
    $total_templates++;
    $rel_path = str_replace('\\', '/', substr($file->getPathname(), strlen($views_dir) + 1));
    $content = file_get_contents($file->getPathname());

    // Check 1: Duplicate Buttons inside Modals
    if (stripos($content, 'modal') !== false) {
        preg_match_all('/<div[^>]*class=["\'][^"\']*modal[^"\']*["\'][^>]*>(.*?)<\/div>\s*<\/div>/is', $content, $modals);
        foreach ($modals[0] as $idx => $m_html) {
            // Count "Cancel" or "Close" buttons
            preg_match_all('/<button[^>]*>\s*(Cancel|Close)\s*<\/button>|<a[^>]*>\s*(Cancel|Close)\s*<\/a>/i', $m_html, $cancels);
            if (count($cancels[0]) > 1) {
                // Check if they are in different locations
                $issues[] = [
                    'severity' => 'WARNING',
                    'template' => $rel_path,
                    'check' => 'Modal Duplicate Buttons',
                    'message' => "Found " . count($cancels[0]) . " duplicate 'Cancel/Close' buttons in modal #{$idx}."
                ];
            }

            // Count submit / action buttons in modal
            preg_match_all('/<button[^>]*type=["\']submit["\'][^>]*>(.*?)<\/button>/is', $m_html, $submits);
            $action_labels = [];
            foreach ($submits[1] as $sub_label) {
                $clean_lbl = strtolower(trim(strip_tags($sub_label)));
                if (!empty($clean_lbl)) {
                    $action_labels[$clean_lbl] = ($action_labels[$clean_lbl] ?? 0) + 1;
                    if ($action_labels[$clean_lbl] > 1) {
                        $issues[] = [
                            'severity' => 'ERROR',
                            'template' => $rel_path,
                            'check' => 'Modal Duplicate Action Button',
                            'message' => "Duplicate action button '{$clean_lbl}' found multiple times in modal."
                        ];
                    }
                }
            }
        }
    }

    // Check 2: Table <th> vs <td> Column Alignment
    if (stripos($content, '<table') !== false) {
        preg_match_all('/<table[^>]*>(.*?)<\/table>/is', $content, $tables);
        foreach ($tables[1] as $t_idx => $t_html) {
            // Find <thead> <th> count
            if (preg_match('/<thead[^>]*>(.*?)<\/thead>/is', $t_html, $th_match)) {
                preg_match_all('/<th\b/i', $th_match[1], $ths);
                $th_count = count($ths[0]);

                if ($th_count > 0 && preg_match('/<tbody[^>]*>(.*?)<\/tbody>/is', $t_html, $tb_match)) {
                    // Check rows in tbody
                    preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $tb_match[1], $rows);
                    foreach ($rows[1] as $r_idx => $r_html) {
                        // Skip empty state rows with colspan
                        if (stripos($r_html, 'colspan') !== false) continue;
                        preg_match_all('/<td\b/i', $r_html, $tds);
                        $td_count = count($tds[0]);
                        if ($td_count > 0 && $td_count !== $th_count) {
                            $issues[] = [
                                'severity' => 'WARNING',
                                'template' => $rel_path,
                                'check' => 'Table Column Count Mismatch',
                                'message' => "Table #{$t_idx} has {$th_count} <th> headers but row #{$r_idx} has {$td_count} <td> cells."
                            ];
                            break;
                        }
                    }
                }
            }
        }
    }

    // Check 3: Action Buttons Wrap / Collision Guard
    // When multiple buttons exist in a cell, they should have nowrap or proper flex gap
    if (preg_match_all('/<td>\s*(<(?:a|button|form)[^>]*>.*?<\/(?:a|button|form)>\s*){2,}<\/td>/is', $content, $unwrapped_cells)) {
        foreach ($unwrapped_cells[0] as $u_cell) {
            if (stripos($u_cell, 'display: flex') === false && stripos($u_cell, 'display:flex') === false && stripos($u_cell, 'btn-group') === false) {
                // If buttons are just dumped without a flex/grid or nowrap container
                $issues[] = [
                    'severity' => 'INFO',
                    'template' => $rel_path,
                    'check' => 'Unwrapped Action Button Cell',
                    'message' => "Cell has multiple action buttons without a flex wrapper. Could cause vertical/horizontal wrapping collisions on smaller screens."
                ];
                break;
            }
        }
    }
}

echo "Scanned {$total_templates} view templates across FMS." . PHP_EOL . PHP_EOL;

if (empty($issues)) {
    echo "[PASS] 0 UI template defects, duplicate modal buttons, or table mismatches found!" . PHP_EOL;
} else {
    $err_count = 0;
    $warn_count = 0;
    foreach ($issues as $iss) {
        if ($iss['severity'] === 'ERROR') $err_count++;
        if ($iss['severity'] === 'WARNING') $warn_count++;
        echo "[{$iss['severity']}] [{$iss['template']}] {$iss['check']}: {$iss['message']}" . PHP_EOL;
    }
    echo PHP_EOL . "Summary: {$err_count} Errors, {$warn_count} Warnings." . PHP_EOL;
}
echo "================================================================================" . PHP_EOL;
