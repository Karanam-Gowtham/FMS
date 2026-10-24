<?php
namespace App\Controllers;

require_once __DIR__ . '/AbstractAccreditationCriterionController.php';

class NBACriterionController extends AbstractAccreditationCriterionController {
    public function show() {
        $raw_id = $_GET['id'] ?? ($_GET['amp;id'] ?? ($_GET['criterion'] ?? 1));
        $crit_id = (int)$raw_id;

        $ctx = $this->resolveAccreditationContext('nba', $crit_id, 9, 'nba_submissions', 'nba_criteria_data');

        $auth = $ctx['auth'];
        $active_role = $ctx['active_role'];
        $year = $ctx['year'];
        $dept_id = $ctx['dept_id'];
        $departments = $ctx['departments'];
        $submission = $ctx['submission'];
        $criteria_data = $ctx['criteria_data'];

        $titles = [
            1 => 'Criterion 1: Outcome-Based Curriculum',
            2 => 'Criterion 2: Outcome-Based Teaching Learning',
            3 => 'Criterion 3: Outcome-Based Assessment',
            4 => "Criterion 4: Students' Performance",
            5 => 'Criterion 5: Faculty Information',
            6 => 'Criterion 6: Faculty Contributions',
            7 => 'Criterion 7: Facilities and Technical Support',
            8 => 'Criterion 8: Continuous Improvement',
            9 => 'Criterion 9: Student Support Systems and Governance',
        ];
        $page_title = $titles[$crit_id] ?? "Criterion {$crit_id}";

        $view_file = __DIR__ . "/../Views/nba/criterion{$crit_id}.php";
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            include __DIR__ . '/../../public/404.php';
            exit;
        }
    }
}
