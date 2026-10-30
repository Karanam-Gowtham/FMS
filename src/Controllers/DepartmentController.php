<?php
namespace App\Controllers;

use mysqli;

/**
 * Controller for Managing Institutional Departments & Operating Units
 */
class DepartmentController {

    public function index(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();

        $auth = auth_context();
        $active_role = auth_active_role();

        if (!$active_role) {
            header("Location: " . BASE_URL . "/public/index.php?route=auth/select_role");
            exit();
        }

        global $conn;

        $role_id = (int)$active_role['role_id'];
        $can_view = in_array($role_id, [ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_HOD], true);
        $is_admin = ($role_id === ROLE_ADMIN);

        if (!$can_view) {
            $_SESSION['error_msg'] = "You do not have permission to access Department Settings.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=dashboard");
            return;
        }

        $search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
        $type_filter = isset($_GET['type']) ? trim((string)$_GET['type']) : 'all';

        // Summary Statistics
        $stats = [
            'total_depts'    => 0,
            'academic'       => 0,
            'non_academic'   => 0,
            'total_faculty'  => 0,
            'total_docs'     => 0
        ];

        $stat_res = $conn->query("
            SELECT 
                COUNT(*) AS total_depts,
                SUM(CASE WHEN is_academic = 1 THEN 1 ELSE 0 END) AS academic_cnt,
                SUM(CASE WHEN is_academic = 0 THEN 1 ELSE 0 END) AS non_academic_cnt
            FROM departments
        ");
        if ($stat_res && $row = $stat_res->fetch_assoc()) {
            $stats['total_depts']   = (int)($row['total_depts'] ?? 0);
            $stats['academic']      = (int)($row['academic_cnt'] ?? 0);
            $stats['non_academic']  = (int)($row['non_academic_cnt'] ?? 0);
        }

        $fac_res = $conn->query("SELECT COUNT(DISTINCT user_id) AS total_fac FROM user_roles WHERE dept_id IS NOT NULL");
        if ($fac_res && $row = $fac_res->fetch_assoc()) {
            $stats['total_faculty'] = (int)($row['total_fac'] ?? 0);
        }

        $doc_res = $conn->query("SELECT COUNT(*) AS total_docs FROM documents WHERE dept_id IS NOT NULL");
        if ($doc_res && $row = $doc_res->fetch_assoc()) {
            $stats['total_docs'] = (int)($row['total_docs'] ?? 0);
        }

        // Build filtered query
        $where = ["1=1"];
        $params = [];
        $types = "";

        if ($type_filter === 'academic') {
            $where[] = "dep.is_academic = 1";
        } elseif ($type_filter === 'non_academic') {
            $where[] = "dep.is_academic = 0";
        }

        if (!empty($search)) {
            $where[] = "dep.dept_name LIKE ?";
            $params[] = '%' . $search . '%';
            $types .= "s";
        }

        $where_sql = implode(" AND ", $where);
        $sql = "
            SELECT 
                dep.dept_id, 
                dep.dept_name, 
                dep.is_academic, 
                dep.created_at,
                (SELECT COUNT(DISTINCT ur.user_id) FROM user_roles ur WHERE ur.dept_id = dep.dept_id) AS faculty_count,
                (SELECT COUNT(*) FROM documents d WHERE d.dept_id = dep.dept_id) AS doc_count,
                (SELECT u.full_name 
                 FROM user_roles ur2 
                 JOIN users u ON ur2.user_id = u.user_id 
                 WHERE ur2.dept_id = dep.dept_id AND ur2.role_id = " . ROLE_HOD . " 
                 LIMIT 1) AS hod_name
            FROM departments dep
            WHERE {$where_sql}
            ORDER BY dep.is_academic DESC, dep.dept_name ASC
        ";

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $departments = [];
        while ($r = $res->fetch_assoc()) {
            $departments[] = $r;
        }
        $stmt->close();

        $raw_error   = $_SESSION['error_msg']   ?? ($_GET['err'] ?? '');
        $raw_success = $_SESSION['success_msg'] ?? ($_GET['msg'] ?? '');
        $error_msg   = is_string($raw_error)   ? $raw_error   : '';
        $success_msg = is_string($raw_success) ? $raw_success : '';
        unset($_SESSION['error_msg'], $_SESSION['success_msg']);

        require __DIR__ . '/../Views/admin/departments.php';
    }

    /**
     * Create or update a department.
     */
    public function save(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        global $conn;

        $auth = auth_context();
        $active_role = auth_active_role();
        $role_id = $active_role ? (int)$active_role['role_id'] : 0;

        if ($role_id !== ROLE_ADMIN) {
            $_SESSION['error_msg'] = "Only Administrators can create or modify departments.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=admin/departments");
            return;
        }

        csrfValidate();

        $dept_id = (int)($_POST['dept_id'] ?? 0);
        $dept_name = trim((string)($_POST['dept_name'] ?? ''));
        $is_academic = !empty($_POST['is_academic']) ? 1 : 0;

        if (empty($dept_name)) {
            $_SESSION['error_msg'] = "Department name cannot be empty.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=admin/departments");
            return;
        }

        if (strlen($dept_name) > 100) {
            $_SESSION['error_msg'] = "Department name cannot exceed 100 characters.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=admin/departments");
            return;
        }

        // Uniqueness check
        $chk = $conn->prepare("SELECT dept_id FROM departments WHERE LOWER(dept_name) = LOWER(?) AND dept_id != ?");
        $chk->bind_param("si", $dept_name, $dept_id);
        $chk->execute();
        $existing = $chk->get_result()->fetch_assoc();
        $chk->close();

        if ($existing) {
            $_SESSION['error_msg'] = "A department with the name '{$dept_name}' already exists.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=admin/departments");
            return;
        }

        $actor_uid = (int)($auth['user_id'] ?? 0);

        if ($dept_id > 0) {
            // Update
            $u_stmt = $conn->prepare("UPDATE departments SET dept_name = ?, is_academic = ? WHERE dept_id = ?");
            $u_stmt->bind_param("sii", $dept_name, $is_academic, $dept_id);
            $u_stmt->execute();
            $u_stmt->close();

            if (function_exists('system_audit_log')) {
                system_audit_log($conn, 'DEPARTMENT_UPDATE', "Updated department #{$dept_id} ('{$dept_name}', academic={$is_academic})", 'department', $dept_id, $actor_uid);
            }

            $_SESSION['success_msg'] = "Department '{$dept_name}' updated successfully.";
        } else {
            // Create
            $i_stmt = $conn->prepare("INSERT INTO departments (dept_name, is_academic) VALUES (?, ?)");
            $i_stmt->bind_param("si", $dept_name, $is_academic);
            $i_stmt->execute();
            $new_id = $conn->insert_id;
            $i_stmt->close();

            if (function_exists('system_audit_log')) {
                system_audit_log($conn, 'DEPARTMENT_CREATE', "Created department '{$dept_name}' (ID #{$new_id}, academic={$is_academic})", 'department', $new_id, $actor_uid);
            }

            $_SESSION['success_msg'] = "Department '{$dept_name}' created successfully.";
        }

        $this->safeRedirect(BASE_URL . "/public/index.php?route=admin/departments");
    }

    /**
     * Quick toggle for Academic vs Non-Academic classification.
     */
    public function toggleAcademic(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        global $conn;

        $auth = auth_context();
        $active_role = auth_active_role();
        $role_id = $active_role ? (int)$active_role['role_id'] : 0;

        if ($role_id !== ROLE_ADMIN) {
            $_SESSION['error_msg'] = "Only Administrators can modify department classifications.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=admin/departments");
            return;
        }

        csrfValidate();
        $dept_id = (int)($_POST['dept_id'] ?? 0);

        if ($dept_id <= 0) {
            $_SESSION['error_msg'] = "Invalid department ID.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=admin/departments");
            return;
        }

        $t_stmt = $conn->prepare("UPDATE departments SET is_academic = IF(is_academic = 1, 0, 1) WHERE dept_id = ?");
        $t_stmt->bind_param("i", $dept_id);
        $t_stmt->execute();
        $t_stmt->close();

        // Query new status
        $r_stmt = $conn->prepare("SELECT dept_name, is_academic FROM departments WHERE dept_id = ?");
        $r_stmt->bind_param("i", $dept_id);
        $r_stmt->execute();
        $dept = $r_stmt->get_result()->fetch_assoc();
        $r_stmt->close();

        if ($dept) {
            $class_label = ((int)$dept['is_academic'] === 1) ? 'Academic Teaching Department' : 'Administrative / Support Unit';
            if (function_exists('system_audit_log')) {
                $actor_uid = (int)($auth['user_id'] ?? 0);
                system_audit_log($conn, 'DEPARTMENT_TOGGLE_ACADEMIC', "Changed classification of '{$dept['dept_name']}' to {$class_label}", 'department', $dept_id, $actor_uid);
            }
            $_SESSION['success_msg'] = "'{$dept['dept_name']}' is now classified as: {$class_label}.";
        }

        $this->safeRedirect(BASE_URL . "/public/index.php?route=admin/departments");
    }

    /**
     * Delete department with strict zero-faculty and zero-document protection.
     */
    public function delete(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        global $conn;

        $auth = auth_context();
        $active_role = auth_active_role();
        $role_id = $active_role ? (int)$active_role['role_id'] : 0;

        if ($role_id !== ROLE_ADMIN) {
            $_SESSION['error_msg'] = "Only Administrators can delete departments.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=admin/departments");
            return;
        }

        csrfValidate();
        $dept_id = (int)($_POST['dept_id'] ?? 0);

        if ($dept_id <= 0) {
            $_SESSION['error_msg'] = "Invalid department ID.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=admin/departments");
            return;
        }

        // Fetch department info
        $stmt = $conn->prepare("SELECT dept_name FROM departments WHERE dept_id = ?");
        $stmt->bind_param("i", $dept_id);
        $stmt->execute();
        $dept = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$dept) {
            $_SESSION['error_msg'] = "Department not found.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=admin/departments");
            return;
        }

        // Integrity check via doc_can_delete_department
        $can_delete = doc_can_delete_department($conn, $dept_id);
        if (!$can_delete['allowed']) {
            $_SESSION['error_msg'] = $can_delete['reason'];
            $this->safeRedirect(BASE_URL . "/public/index.php?route=admin/departments");
            return;
        }

        try {
            $del_stmt = $conn->prepare("DELETE FROM departments WHERE dept_id = ?");
            $del_stmt->bind_param("i", $dept_id);
            $del_stmt->execute();
            $del_stmt->close();

            if (function_exists('system_audit_log')) {
                $actor_uid = (int)($auth['user_id'] ?? 0);
                system_audit_log($conn, 'DEPARTMENT_DELETE', "Deleted empty department '{$dept['dept_name']}' (ID #{$dept_id})", 'department', $dept_id, $actor_uid);
            }

            $_SESSION['success_msg'] = "Department '{$dept['dept_name']}' was deleted successfully.";
        } catch (\Throwable $e) {
            error_log("Failed to delete department: " . $e->getMessage());
            $_SESSION['error_msg'] = "Database error while deleting department: " . $e->getMessage();
        }

        $this->safeRedirect(BASE_URL . "/public/index.php?route=admin/departments");
    }

    /**
     * Environment-aware redirect that doesn't terminate PHP process under CLI / unit tests.
     */
    protected function safeRedirect(string $url): void {
        if (php_sapi_name() === 'cli') {
            return;
        }
        header("Location: " . $url);
        exit;
    }
}
