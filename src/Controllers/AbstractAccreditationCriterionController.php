<?php
namespace App\Controllers;

abstract class AbstractAccreditationCriterionController {
    /**
     * Resolves common criterion context: departments, submission, criteria_data.
     */
    protected function resolveAccreditationContext(
        string $module,
        int $crit_id,
        int $max_crit_id,
        string $submissionsTable,
        string $criteriaDataTable
    ): array {
        require_once __DIR__ . '/../../core/bootstrap.php';

        require_login();
        $auth = auth_context();
        $active_role = auth_active_role();

        global $conn;

        $active_ay = function_exists('doc_get_active_year') ? doc_get_active_year($conn) : null;
        $default_year = $active_ay ? $active_ay['year_label'] : '2025-26';
        $year = (!empty($_GET['year']) && is_string($_GET['year'])) ? trim($_GET['year']) : $default_year;

        if ($crit_id < 1 || $crit_id > $max_crit_id) {
            include __DIR__ . '/../../public/404.php';
            exit;
        }

        if (!$active_role) {
            $_SESSION['error_msg'] = "You must select an active role to access " . strtoupper($module) . " criteria.";
            header("Location: " . BASE_URL . "/public/index.php?route=auth/select_role");
            exit();
        }

        $requested_dept_id = isset($_GET['dept_id']) ? (int)$_GET['dept_id'] : null;
        $dept_id = validate_dept_filter($requested_dept_id);

        if ($dept_id === null) {
            $_SESSION['error_msg'] = "Access denied. You are not authorized to view data for this department.";
            header("Location: " . BASE_URL . "/public/index.php?route=" . $module . "/dashboard");
            exit();
        }

        // Fetch departments for selector
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
            $stmt = $conn->prepare("SELECT * FROM {$submissionsTable} WHERE dept_id = ? AND academic_year = ?");
            if ($stmt) {
                $stmt->bind_param("is", $dept_id, $year);
                $stmt->execute();
                $sub_res = $stmt->get_result();
                if ($sub_res && $sub_res->num_rows > 0) {
                    $submission = $sub_res->fetch_assoc();

                    $stmt2 = $conn->prepare("SELECT data_json FROM {$criteriaDataTable} WHERE submission_id = ? AND criterion_number = ?");
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

        return [
            'auth' => $auth,
            'active_role' => $active_role,
            'year' => $year,
            'dept_id' => $dept_id,
            'departments' => $departments,
            'submission' => $submission,
            'criteria_data' => $criteria_data,
        ];
    }
}
