<?php
declare(strict_types=1);

namespace App\Controllers;

use Throwable;

/**
 * Controller for Managing Institutional Departments & Operating Units
 */
class DepartmentController extends BaseController {

    public function index(): void {
        $this->requireRole([ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_HOD]);

        $auth = $this->auth;
        $active_role = $this->activeRole;
        $role_id = $this->activeRoleId;
        $is_admin = ($role_id === ROLE_ADMIN);

        $search = $this->stringParam('search', '', 'get');
        $type_filter = $this->stringParam('type', 'all', 'get');

        // Summary Statistics
        $stats = [
            'total_depts'    => 0,
            'academic'       => 0,
            'non_academic'   => 0,
            'total_faculty'  => 0,
            'total_docs'     => 0
        ];

        $stat_res = $this->conn->query("
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

        $fac_res = $this->conn->query("SELECT COUNT(DISTINCT user_id) AS total_fac FROM user_roles WHERE dept_id IS NOT NULL");
        if ($fac_res && $row = $fac_res->fetch_assoc()) {
            $stats['total_faculty'] = (int)($row['total_fac'] ?? 0);
        }

        $doc_res = $this->conn->query("SELECT COUNT(*) AS total_docs FROM documents WHERE dept_id IS NOT NULL");
        if ($doc_res && $row = $doc_res->fetch_assoc()) {
            $stats['total_docs'] = (int)($row['total_docs'] ?? 0);
        }

        // Build list query with faculty & document counts
        $where = ["1=1"];
        $params = [];
        $types = "";

        if ($search !== '') {
            $where[] = "d.dept_name LIKE ?";
            $params[] = '%' . $search . '%';
            $types .= "s";
        }

        if ($type_filter === 'academic') {
            $where[] = "d.is_academic = 1";
        } elseif ($type_filter === 'non_academic') {
            $where[] = "d.is_academic = 0";
        }

        $where_sql = implode(" AND ", $where);

        $query = "
            SELECT 
                d.dept_id,
                d.dept_name,
                d.is_academic,
                (SELECT COUNT(DISTINCT ur.user_id) FROM user_roles ur WHERE ur.dept_id = d.dept_id) AS faculty_count,
                (SELECT COUNT(*) FROM documents doc WHERE doc.dept_id = d.dept_id) AS document_count
            FROM departments d
            WHERE {$where_sql}
            ORDER BY 
                CASE WHEN d.dept_name = 'Complete University' THEN 0 ELSE 1 END,
                d.is_academic DESC,
                d.dept_name ASC
        ";

        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            $departments = [];
            while ($row = $res->fetch_assoc()) {
                $departments[] = $row;
            }
            $stmt->close();
        } else {
            $departments = [];
        }

        $flash = $this->getFlashMessages();
        $success_msg = $flash['success'];
        $error_msg   = $flash['error'];

        $this->renderView('admin/departments', get_defined_vars());
    }

    /**
     * Create or edit a department
     */
    public function save(): void {
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->redirect(BASE_URL . "/public/index.php?route=admin/departments");
        }

        $this->validateCsrf();

        $dept_id = $this->intParam('dept_id', 0, 'post');
        $dept_name = $this->stringParam('dept_name', '', 'post');
        $is_academic = !empty($_POST['is_academic']) ? 1 : 0;

        if (empty($dept_name)) {
            $this->flashError("Department name cannot be empty.");
            $this->redirect(BASE_URL . "/public/index.php?route=admin/departments");
        }

        if (strlen($dept_name) > 100) {
            $this->flashError("Department name cannot exceed 100 characters.");
            $this->redirect(BASE_URL . "/public/index.php?route=admin/departments");
        }

        // Uniqueness check
        $chk = $this->conn->prepare("SELECT dept_id FROM departments WHERE LOWER(dept_name) = LOWER(?) AND dept_id != ?");
        $chk->bind_param("si", $dept_name, $dept_id);
        $chk->execute();
        $existing = $chk->get_result()->fetch_assoc();
        $chk->close();

        if ($existing) {
            $this->flashError("A department with the name '{$dept_name}' already exists.");
            $this->redirect(BASE_URL . "/public/index.php?route=admin/departments");
        }

        $actor_uid = $this->userId;

        if ($dept_id > 0) {
            // Update
            $u_stmt = $this->conn->prepare("UPDATE departments SET dept_name = ?, is_academic = ? WHERE dept_id = ?");
            $u_stmt->bind_param("sii", $dept_name, $is_academic, $dept_id);
            $u_stmt->execute();
            $u_stmt->close();

            system_audit_log($this->conn, 'DEPARTMENT_UPDATE', "Updated department #{$dept_id} ('{$dept_name}', academic={$is_academic})", 'department', $dept_id, $actor_uid);
            $this->flashSuccess("Department '{$dept_name}' updated successfully.");
        } else {
            // Create
            $i_stmt = $this->conn->prepare("INSERT INTO departments (dept_name, is_academic) VALUES (?, ?)");
            $i_stmt->bind_param("si", $dept_name, $is_academic);
            $i_stmt->execute();
            $new_id = $this->conn->insert_id;
            $i_stmt->close();

            system_audit_log($this->conn, 'DEPARTMENT_CREATE', "Created department '{$dept_name}' (ID #{$new_id}, academic={$is_academic})", 'department', $new_id, $actor_uid);
            $this->flashSuccess("Department '{$dept_name}' created successfully.");
        }

        $this->redirect(BASE_URL . "/public/index.php?route=admin/departments");
    }

    /**
     * Quick toggle for Academic vs Non-Academic classification.
     */
    public function toggleAcademic(): void {
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->redirect(BASE_URL . "/public/index.php?route=admin/departments");
        }

        $this->validateCsrf();
        $dept_id = $this->intParam('dept_id', 0, 'post');

        if ($dept_id <= 0) {
            $this->flashError("Invalid department ID.");
            $this->redirect(BASE_URL . "/public/index.php?route=admin/departments");
        }

        $t_stmt = $this->conn->prepare("UPDATE departments SET is_academic = IF(is_academic = 1, 0, 1) WHERE dept_id = ?");
        $t_stmt->bind_param("i", $dept_id);
        $t_stmt->execute();
        $t_stmt->close();

        // Query new status
        $r_stmt = $this->conn->prepare("SELECT dept_name, is_academic FROM departments WHERE dept_id = ?");
        $r_stmt->bind_param("i", $dept_id);
        $r_stmt->execute();
        $dept = $r_stmt->get_result()->fetch_assoc();
        $r_stmt->close();

        if ($dept) {
            $class_label = ((int)$dept['is_academic'] === 1) ? 'Academic Teaching Department' : 'Administrative / Support Unit';
            system_audit_log($this->conn, 'DEPARTMENT_TOGGLE_ACADEMIC', "Changed classification of '{$dept['dept_name']}' to {$class_label}", 'department', $dept_id, $this->userId);
            $this->flashSuccess("'{$dept['dept_name']}' is now classified as: {$class_label}.");
        }

        $this->redirect(BASE_URL . "/public/index.php?route=admin/departments");
    }

    /**
     * Delete department with strict zero-faculty and zero-document protection.
     */
    public function delete(): void {
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->redirect(BASE_URL . "/public/index.php?route=admin/departments");
        }

        $this->validateCsrf();
        $dept_id = $this->intParam('dept_id', 0, 'post');

        if ($dept_id <= 0) {
            $this->flashError("Invalid department ID.");
            $this->redirect(BASE_URL . "/public/index.php?route=admin/departments");
        }

        // Fetch department info
        $stmt = $this->conn->prepare("SELECT dept_name FROM departments WHERE dept_id = ?");
        $stmt->bind_param("i", $dept_id);
        $stmt->execute();
        $dept = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$dept) {
            $this->flashError("Department not found.");
            $this->redirect(BASE_URL . "/public/index.php?route=admin/departments");
        }

        // Integrity check via doc_can_delete_department
        $can_delete = doc_can_delete_department($this->conn, $dept_id);
        if (!$can_delete['allowed']) {
            $this->flashError($can_delete['reason']);
            $this->redirect(BASE_URL . "/public/index.php?route=admin/departments");
        }

        try {
            $del_stmt = $this->conn->prepare("DELETE FROM departments WHERE dept_id = ?");
            $del_stmt->bind_param("i", $dept_id);
            $del_stmt->execute();
            $del_stmt->close();

            system_audit_log($this->conn, 'DEPARTMENT_DELETE', "Deleted empty department '{$dept['dept_name']}' (ID #{$dept_id})", 'department', $dept_id, $this->userId);
            $this->flashSuccess("Department '{$dept['dept_name']}' was deleted successfully.");
        } catch (Throwable $e) {
            error_log("Failed to delete department: " . $e->getMessage());
            $this->flashError("Database error while deleting department: " . $e->getMessage());
        }

        $this->redirect(BASE_URL . "/public/index.php?route=admin/departments");
    }
}
