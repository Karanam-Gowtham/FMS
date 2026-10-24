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
        require_once __DIR__ . '/../../core/document_service.php';

        require_login();
        $auth = auth_context();
        $active_role = auth_active_role();

        global $conn;

        $active_ay = doc_get_active_year($conn);
        $default_year = $active_ay ? $active_ay['year_label'] : '2025-26';
        $raw_year = $_GET['year'] ?? ($_GET['amp;year'] ?? null);
        $year = (!empty($raw_year) && is_string($raw_year)) ? trim($raw_year) : $default_year;

        if ($crit_id < 1 || $crit_id > $max_crit_id) {
            include __DIR__ . '/../../public/404.php';
            exit;
        }

        if (!$active_role) {
            $_SESSION['error_msg'] = "You must select an active role to access " . strtoupper($module) . " criteria.";
            header("Location: " . BASE_URL . "/public/index.php?route=auth/select_role");
            exit();
        }

        // Central Accreditation Modules (NBA & NAAC):
        // Allow faculty and coordinators to switch between their department, any other department, or Complete University
        $excluded_depts = "'Antiragging', 'Clubs', 'Exam_Section', 'IIC', 'IQAC', 'NAAC', 'NBA', 'NCC', 'NSS', 'PASH', 'PE', 'PG', 'R&D', 'SAC', 'Sports', 'Women_Empowerment'";
        $dept_sql = "SELECT dept_id, dept_name FROM departments 
                     WHERE dept_name NOT IN ($excluded_depts)
                     ORDER BY CASE WHEN dept_name = 'Complete University' THEN 0 ELSE 1 END, dept_name ASC";
        $dept_result = $conn->query($dept_sql);
        $departments = [];
        $valid_dept_map = [];
        if ($dept_result instanceof \mysqli_result) {
            while ($row = $dept_result->fetch_assoc()) {
                $departments[] = $row;
                $valid_dept_map[(int)$row['dept_id']] = $row['dept_name'];
            }
            $dept_result->close();
        }

        // Determine active department: any faculty can view their own department, switch to any other, or select Complete University
        $raw_dept_id = $_GET['dept_id'] ?? ($_GET['amp;dept_id'] ?? null);
        $requested_dept_id = $raw_dept_id !== null ? (int)$raw_dept_id : null;

        if ($requested_dept_id !== null && isset($valid_dept_map[$requested_dept_id])) {
            $dept_id = $requested_dept_id;
        } else {
            $active_dept = auth_active_dept_id();
            if ($active_dept !== null && isset($valid_dept_map[$active_dept])) {
                $dept_id = (int)$active_dept;
            } else {
                $dept_id = !empty($departments) ? (int)$departments[0]['dept_id'] : 1;
            }
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
