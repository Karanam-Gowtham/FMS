<?php
namespace App\Controllers;

require_once __DIR__ . '/AbstractAccreditationCriterionController.php';

class NAACCriterionController extends AbstractAccreditationCriterionController {
    public function show() {
        $crit_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

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
