<?php
namespace App\Controllers;

class NAACCriterionController {
    public function show() {
        require_once __DIR__ . '/../../core/bootstrap.php';

        require_login();
        $auth = auth_context();
        $active_role = auth_active_role();

        global $conn;

        $active_ay = function_exists('doc_get_active_year') ? doc_get_active_year($conn) : null;
        $default_year = $active_ay ? $active_ay['year_label'] : '2025-26';
        $year = !empty($_GET['year']) ? trim($_GET['year']) : $default_year;
        $crit_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

        if (!$active_role) {
            $_SESSION['error_msg'] = "You must select an active role to access NAAC criteria.";
            header("Location: " . BASE_URL . "/public/index.php?route=auth/select_role");
            exit();
        }

        $requested_dept_id = isset($_GET['dept_id']) ? (int)$_GET['dept_id'] : null;
        $dept_id = validate_dept_filter($requested_dept_id);
        
        if ($dept_id === null) {
            $_SESSION['error_msg'] = "Access denied. You are not authorized to view data for this department.";
            header("Location: " . BASE_URL . "/public/index.php?route=naac/dashboard");
            exit();
        }

        // Fetch departments for the selector (Exclude central/admin wings)
        $active_role_id = auth_active_role_id();
        $is_college_wide = in_array($active_role_id, [ROLE_ADMIN, ROLE_RND_DEAN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true);
        $excluded_depts = "'Antiragging', 'Clubs', 'Exam_Section', 'IIC', 'IQAC', 'NAAC', 'NBA', 'NCC', 'NSS', 'PASH', 'PE', 'PG', 'R&D', 'SAC', 'Sports', 'Women_Empowerment'";
        $dept_sql = "SELECT dept_id, dept_name FROM departments WHERE dept_name NOT IN ($excluded_depts)";
        if (!$is_college_wide) {
            $authorized = get_authorized_depts();
            $allowed_ids = array_map('intval', array_keys($authorized));
            if (!empty($allowed_ids)) {
                $dept_sql .= " AND dept_id IN (" . implode(',', $allowed_ids) . ")";
            } else {
                $dept_sql .= " AND 1=0";
            }
        }
        $dept_sql .= " ORDER BY dept_name";
        $dept_result = $conn->query($dept_sql);
        $departments = [];
        if ($dept_result instanceof \mysqli_result) {
            while ($row = $dept_result->fetch_assoc()) {
                $departments[] = $row;
            }
            $dept_result->close();
        }

        $submission = null;
        $criteria_data = [];

        if ($dept_id > 0) {
            $stmt = $conn->prepare("SELECT * FROM naac_submissions WHERE dept_id = ? AND academic_year = ?");
            if ($stmt) {
                $stmt->bind_param("is", $dept_id, $year);
                $stmt->execute();
                $sub_res = $stmt->get_result();
                if ($sub_res && $sub_res->num_rows > 0) {
                    $submission = $sub_res->fetch_assoc();
                    
                    $stmt2 = $conn->prepare("SELECT data_json FROM naac_criteria_data WHERE submission_id = ? AND criterion_number = ?");
                    if ($stmt2) {
                        $sub_id = (int)$submission['submission_id'];
                        $stmt2->bind_param("ii", $sub_id, $crit_id);
                        $stmt2->execute();
                        $data_res = $stmt2->get_result();
                        if ($data_res && $data_res->num_rows > 0) {
                            $row = $data_res->fetch_assoc();
                            $criteria_data = json_decode($row['data_json'], true) ?: [];
                        }
                        $stmt2->close();
                    }
                }
                $stmt->close();
            }
        }

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
            echo "<h2>Pending Configuration</h2><p>Criterion {$crit_id} is not yet implemented.</p>";
        }
    }
}
