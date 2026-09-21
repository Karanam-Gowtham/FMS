<?php
namespace App\Controllers;

class NBACriterionController {
    public function show() {
        require_once __DIR__ . '/../../core/bootstrap.php';

        require_login();
        $auth = auth_context();
        $active_role = auth_active_role();

        $year = isset($_GET['year']) ? trim($_GET['year']) : '2025-26';
        $crit_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

        if (!$active_role) {
            die("You must have an active role to access NBA criteria.");
        }

        $dept_id = $active_role['dept_id'];
        global $conn;

        // Fetch departments for the selector (Exclude central/admin wings)
        $excluded_depts = "'Antiragging', 'Clubs', 'Exam_Section', 'IIC', 'IQAC', 'NAAC', 'NBA', 'NCC', 'NSS', 'PASH', 'PE', 'PG', 'R&D', 'SAC', 'Sports', 'Women_Empowerment'";
        $dept_result = $conn->query("SELECT dept_id, dept_name FROM departments WHERE dept_name NOT IN ($excluded_depts) ORDER BY dept_name");
        $departments = [];
        while ($row = $dept_result->fetch_assoc()) {
            $departments[] = $row;
        }

        $submission = null;
        $criteria_data = [];

        if ($dept_id > 0) {
            // Check if submission exists
            $stmt = $conn->prepare("SELECT * FROM nba_submissions WHERE dept_id = ? AND academic_year = ?");
            $stmt->bind_param("is", $dept_id, $year);
            $stmt->execute();
            $sub_res = $stmt->get_result();
            if ($sub_res->num_rows > 0) {
                $submission = $sub_res->fetch_assoc();
                
                // Fetch specific Criterion data
                $stmt2 = $conn->prepare("SELECT data_json FROM nba_criteria_data WHERE submission_id = ? AND criterion_number = ?");
                $stmt2->bind_param("ii", $submission['submission_id'], $crit_id);
                $stmt2->execute();
                $data_res = $stmt2->get_result();
                if ($data_res->num_rows > 0) {
                    $row = $data_res->fetch_assoc();
                    $criteria_data = json_decode($row['data_json'], true) ?: [];
                }
                $stmt2->close();
            }
            $stmt->close();
        }

        // Title mapping
        $titles = [
            1 => 'Criterion 1: Outcome-Based Curriculum',
            2 => 'Criterion 2: Outcome-Based Teaching Learning',
            3 => 'Criterion 3: Outcome-Based Assessment',
            4 => 'Criterion 4: Students’ Performance',
        ];
        $page_title = $titles[$crit_id] ?? "Criterion {$crit_id}";

        // Include the view
        $view_file = __DIR__ . "/../Views/nba/criterion{$crit_id}.php";
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            echo "<h2>Pending Configuration</h2><p>Criterion {$crit_id} is not yet implemented.</p>";
        }
    }
}
