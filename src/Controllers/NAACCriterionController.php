<?php
namespace App\Controllers;

require_once __DIR__ . '/AbstractAccreditationCriterionController.php';

class NAACCriterionController extends AbstractAccreditationCriterionController {
    public function show() {
        $raw_id = $_GET['id'] ?? ($_GET['amp;id'] ?? ($_GET['criterion'] ?? 1));
        $crit_id = (int)$raw_id;

        $ctx = $this->resolveAccreditationContext('naac', $crit_id, 7, 'naac_submissions', 'naac_criteria_data');

        $auth = $ctx['auth'];
        $active_role = $ctx['active_role'];
        $year = $ctx['year'];
        $dept_id = $ctx['dept_id'];
        $departments = $ctx['departments'];
        $submission = $ctx['submission'];
        $criteria_data = $ctx['criteria_data'];

        global $conn;
        // Fetch active schema for this criterion
        $form_schema = null;
        $schema_stmt = $conn->prepare("SELECT schema_json FROM naac_form_schemas WHERE criterion_number = ? AND is_active = 1 ORDER BY schema_id DESC LIMIT 1");
        if ($schema_stmt) {
            $schema_stmt->bind_param("i", $crit_id);
            $schema_stmt->execute();
            $schema_res = $schema_stmt->get_result();
            if ($schema_res && $schema_res->num_rows > 0) {
                $form_schema = json_decode($schema_res->fetch_assoc()['schema_json'], true);
            }
            $schema_stmt->close();
        }

        // Check for an existing generated final PDF
        $existing_pdf_url = null;
        if (!empty($criteria_data['final_pdf_url'])) {
            $existing_pdf_url = $criteria_data['final_pdf_url'];
        } else {
            $clean_year = preg_replace('/[^a-zA-Z0-9_]/', '_', $year);
            $pattern = __DIR__ . "/../../uploads/naac_pdfs/final_criterion{$crit_id}_dept_{$dept_id}_{$clean_year}_*.pdf";
            $matches = glob($pattern);
            if (!empty($matches)) {
                usort($matches, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                $latest_file = basename($matches[0]);
                $existing_pdf_url = BASE_URL . "/public/index.php?route=reports/download&type=naac&file=" . urlencode($latest_file);
            }
        }

        $titles = [
            1 => 'Criterion 1: Curricular Aspects',
            2 => 'Criterion 2: Teaching-Learning and Evaluation',
            3 => 'Criterion 3: Research, Innovations and Extension',
            4 => 'Criterion 4: Infrastructure and Learning Resources',
            5 => 'Criterion 5: Student Support and Progression',
            6 => 'Criterion 6: Governance, Leadership and Management',
            7 => 'Criterion 7: Institutional Values and Best Practices'
        ];
        $page_title = $titles[$crit_id] ?? "Criterion {$crit_id}";

        if ($form_schema !== null) {
            $view_file = __DIR__ . "/../Views/naac/criterion_dynamic.php";
        } else {
            $view_file = __DIR__ . "/../Views/naac/criterion{$crit_id}.php";
        }
        
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            include __DIR__ . '/../../public/404.php';
            exit;
        }
    }
}
