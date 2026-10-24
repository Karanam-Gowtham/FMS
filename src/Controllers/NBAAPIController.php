<?php
namespace App\Controllers;

require_once __DIR__ . '/AbstractAccreditationAPIController.php';

class NBAAPIController extends AbstractAccreditationAPIController {
    public function save() {
        $hook = function(array $payload, int $crit_id, int $dept_id): array {
            // CSV Parser specific to Criterion 1
            if ($crit_id === 1 && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
                $csv_tmp = $_FILES['csv_file']['tmp_name'];
                if (($handle = fopen($csv_tmp, "r")) !== FALSE) {
                    $parsed_courses = [];
                    $current_course = '';

                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        if (empty(array_filter($row))) continue;
                        $col0 = trim($row[0] ?? '');
                        $col1 = trim($row[1] ?? '');

                        if (stripos($col0, 'Course') !== false || stripos($col0, 'Code') !== false) {
                            continue;
                        }

                        if (!empty($col0) && strlen($col0) < 10 && !preg_match('/^CO\d/i', $col0)) {
                            $current_course = $col0;
                            $parsed_courses[$current_course] = [];
                        } elseif (preg_match('/^CO\d/i', $col0) && !empty($current_course)) {
                            $parsed_courses[$current_course][] = [
                                'co' => $col0,
                                'desc' => $col1
                            ];
                        }
                    }
                    fclose($handle);
                    $payload['parsed_csv_data'] = $parsed_courses;
                }
            }

            // Generic PDF File Uploader for Criteria 5-9
            $upload_dir = __DIR__ . '/../../uploads/nba_pdfs/';
            if (!is_dir($upload_dir)) {
                @mkdir($upload_dir, 0755, true);
            }

            foreach ($_FILES as $input_name => $file) {
                if ($input_name === 'csv_file' || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) continue;
                if (empty($file['tmp_name']) || !file_exists($file['tmp_name']) || !is_readable($file['tmp_name'])) continue;
                if (($file['size'] ?? 0) > 10 * 1024 * 1024) continue;
                $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
                if ($ext !== 'pdf') continue;

                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
                if ($mime === 'application/pdf') {
                    $section = preg_replace('/[^a-zA-Z0-9_]/', '', $input_name);
                    $new_filename = sprintf('dept_%d_crit%d_%s_%s.pdf', $dept_id, $crit_id, $section, uniqid());
                    $destination = $upload_dir . $new_filename;
                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        if (!isset($payload['uploaded_files'])) {
                            $payload['uploaded_files'] = [];
                        }
                        $payload['uploaded_files'][$input_name] = 'uploads/nba_pdfs/' . $new_filename;
                    }
                }
            }

            return $payload;
        };

        $this->processSave('nba', 'nba_submissions', 'nba_criteria_data', $hook);
    }

    public function upload_pdf() {
        $this->processUploadPdf('nba', 'nba_pdfs');
    }

    public function generate_pdf() {
        $summary = "This is an auto-generated compliance report summary for NBA Criterion %d. All mapped course outcomes, faculty data, and department records are archived in the GMRIT Academic Repository.";
        $this->processGeneratePdf('nba', 'nba_pdfs', $summary);
    }
}
