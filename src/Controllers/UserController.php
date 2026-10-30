<?php
namespace App\Controllers;

/**
 * User Management Controller (Admin Only)
 * 
 * Provides centralized administration of faculty, staff, and leadership roles:
 * - Appoint and transfer HOD leadership (strictly 1 active HOD per department)
 * - Appoint and revoke Department Coordinators
 * - Toggle user status (Active vs Inactive/Relieved) without breaking document history
 * - Reset user passwords to institutional default (Admin@123)
 * - Safe user deletion (strictly permitted only for test accounts with 0 uploaded documents)
 * - Direct registration of new faculty and staff
 */
class UserController {

    public function index(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        $this->ensureAdminAccess();

        global $conn;

        $search = isset($_GET['search']) && is_string($_GET['search']) ? trim($_GET['search']) : '';
        $filter_dept_id = isset($_GET['dept_id']) && is_numeric($_GET['dept_id']) ? (int)$_GET['dept_id'] : 0;
        $filter_role_id = isset($_GET['role_id']) && is_numeric($_GET['role_id']) ? (int)$_GET['role_id'] : 0;
        $filter_status = isset($_GET['status']) && in_array($_GET['status'], ['active', 'inactive'], true) ? $_GET['status'] : '';

        // Fetch lookups
        $departments = [];
        $d_res = $conn->query("SELECT dept_id, dept_name, is_academic FROM departments ORDER BY is_academic DESC, dept_name ASC");
        if ($d_res) {
            while ($row = $d_res->fetch_assoc()) {
                $departments[] = $row;
            }
        }

        $roles = [];
        $r_res = $conn->query("SELECT role_id, role_name, description FROM roles ORDER BY role_id ASC");
        if ($r_res) {
            while ($row = $r_res->fetch_assoc()) {
                $roles[] = $row;
            }
        }

        // Build query
        $where_clauses = ["1=1"];
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where_clauses[] = "(u.full_name LIKE ? OR u.email LIKE ? OR up.per_no LIKE ?)";
            $search_param = '%' . $search . '%';
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= "sss";
        }

        if ($filter_dept_id > 0) {
            $where_clauses[] = "EXISTS (SELECT 1 FROM user_roles ur2 WHERE ur2.user_id = u.user_id AND ur2.dept_id = ?)";
            $params[] = $filter_dept_id;
            $types .= "i";
        }

        if ($filter_role_id > 0) {
            $where_clauses[] = "EXISTS (SELECT 1 FROM user_roles ur3 WHERE ur3.user_id = u.user_id AND ur3.role_id = ?)";
            $params[] = $filter_role_id;
            $types .= "i";
        }

        if (!empty($filter_status)) {
            $where_clauses[] = "u.status = ?";
            $params[] = $filter_status;
            $types .= "s";
        }

        $sql = "
            SELECT 
                u.user_id,
                u.full_name,
                u.email,
                u.status,
                u.created_at,
                u.last_login,
                up.per_no,
                up.designation_present,
                (SELECT COUNT(*) FROM documents d WHERE d.uploaded_by = u.user_id) AS doc_count,
                (SELECT COUNT(*) FROM document_actions da WHERE da.acted_by = u.user_id) AS action_count
            FROM users u
            LEFT JOIN user_profiles up ON u.user_id = up.user_id
            WHERE " . implode(" AND ", $where_clauses) . "
            ORDER BY u.status ASC, u.user_id DESC
        ";

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();

        $users = [];
        while ($row = $res->fetch_assoc()) {
            $users[$row['user_id']] = $row;
            $users[$row['user_id']]['roles'] = [];
        }
        $stmt->close();

        // Fetch assigned roles for fetched users
        if (!empty($users)) {
            $user_ids = array_keys($users);
            $in_clause = implode(',', array_map('intval', $user_ids));
            $role_sql = "
                SELECT 
                    ur.user_id,
                    ur.role_id,
                    r.role_name,
                    ur.dept_id,
                    d.dept_name
                FROM user_roles ur
                JOIN roles r ON ur.role_id = r.role_id
                LEFT JOIN departments d ON ur.dept_id = d.dept_id
                WHERE ur.user_id IN ({$in_clause})
                ORDER BY ur.role_id ASC
            ";
            $r_stmt = $conn->query($role_sql);
            if ($r_stmt) {
                while ($r_row = $r_stmt->fetch_assoc()) {
                    $users[$r_row['user_id']]['roles'][] = $r_row;
                }
            }
        }

        $disp_success = $_SESSION['success_msg'] ?? null;
        $disp_error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        require __DIR__ . '/../Views/admin/users.php';
    }

    /**
     * Appoint or Transfer HOD leadership for a department
     * Strict Institutional Rule: Only 1 HOD active per department at any time.
     */
    public function setHod(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        $this->ensureAdminAccess();
        csrfValidate();

        global $conn;

        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $dept_id = isset($_POST['dept_id']) ? (int)$_POST['dept_id'] : 0;

        if ($user_id <= 0 || $dept_id <= 0) {
            $_SESSION['error_msg'] = "Invalid faculty or department selection.";
            $this->redirectBack();
        }

        // Fetch user & department details
        $user = $this->getUserBasic($conn, $user_id);
        $dept = $this->getDeptBasic($conn, $dept_id);

        if (!$user || !$dept) {
            $_SESSION['error_msg'] = "Faculty member or department does not exist.";
            $this->redirectBack();
        }

        // 1. Identify previous HOD for this department (if any)
        $prev_hod_stmt = $conn->prepare("SELECT user_id FROM user_roles WHERE role_id = ? AND dept_id = ?");
        $hod_role = ROLE_HOD;
        $prev_hod_stmt->bind_param("ii", $hod_role, $dept_id);
        $prev_hod_stmt->execute();
        $prev_res = $prev_hod_stmt->get_result();
        $prev_hod_id = 0;
        if ($prev_row = $prev_res->fetch_assoc()) {
            $prev_hod_id = (int)$prev_row['user_id'];
        }
        $prev_hod_stmt->close();

        // If user is already the HOD, nothing to change
        if ($prev_hod_id === $user_id) {
            $_SESSION['success_msg'] = "{$user['full_name']} is already the active HOD of {$dept['dept_name']}.";
            $this->redirectBack();
        }

        // 2. Remove HOD role from previous HOD (they remain Faculty)
        $prev_hod_name = '';
        if ($prev_hod_id > 0) {
            $prev_user = $this->getUserBasic($conn, $prev_hod_id);
            $prev_hod_name = $prev_user ? $prev_user['full_name'] : "Previous HOD";

            $del_prev = $conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id = ?");
            $del_prev->bind_param("iii", $prev_hod_id, $hod_role, $dept_id);
            $del_prev->execute();
            $del_prev->close();

            // Ensure previous HOD still holds Faculty role
            $this->ensureRoleExists($conn, $prev_hod_id, ROLE_FACULTY, $dept_id);
        }

        // 3. Grant HOD role to new user
        $this->ensureRoleExists($conn, $user_id, ROLE_HOD, $dept_id);
        // Also ensure they have basic Faculty role
        $this->ensureRoleExists($conn, $user_id, ROLE_FACULTY, $dept_id);

        if (!empty($prev_hod_name)) {
            $_SESSION['success_msg'] = "HOD role for {$dept['dept_name']} successfully transferred from {$prev_hod_name} to {$user['full_name']}.";
        } else {
            $_SESSION['success_msg'] = "{$user['full_name']} is now appointed as HOD of {$dept['dept_name']}.";
        }

        $this->redirectBack();
    }

    /**
     * Appoint or Revoke Department Coordinator role
     */
    public function toggleCoordinator(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        $this->ensureAdminAccess();
        csrfValidate();

        global $conn;

        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $dept_id = isset($_POST['dept_id']) ? (int)$_POST['dept_id'] : 0;
        $action = isset($_POST['action']) ? trim($_POST['action']) : '';

        if ($user_id <= 0 || $dept_id <= 0) {
            $_SESSION['error_msg'] = "Invalid request parameters.";
            $this->redirectBack();
        }

        $user = $this->getUserBasic($conn, $user_id);
        $dept = $this->getDeptBasic($conn, $dept_id);

        if (!$user || !$dept) {
            $_SESSION['error_msg'] = "User or Department not found.";
            $this->redirectBack();
        }

        $coord_role = ROLE_DEPT_COORDINATOR;

        if ($action === 'revoke') {
            $del = $conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id = ?");
            $del->bind_param("iii", $user_id, $coord_role, $dept_id);
            $del->execute();
            $del->close();
            $_SESSION['success_msg'] = "Revoked Coordinator role from {$user['full_name']} for {$dept['dept_name']}.";
        } else {
            $this->ensureRoleExists($conn, $user_id, $coord_role, $dept_id);
            $this->ensureRoleExists($conn, $user_id, ROLE_FACULTY, $dept_id);
            $_SESSION['success_msg'] = "{$user['full_name']} appointed as Department Coordinator for {$dept['dept_name']}.";
        }

        $this->redirectBack();
    }

    /**
     * Toggle User Account Status (Active vs Relieved/Inactive)
     */
    public function toggleStatus(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        $this->ensureAdminAccess();
        csrfValidate();

        global $conn;

        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $current_admin_id = auth_user_id();

        if ($user_id <= 0) {
            $_SESSION['error_msg'] = "Invalid user specified.";
            $this->redirectBack();
        }

        if ($user_id === $current_admin_id) {
            $_SESSION['error_msg'] = "Security check: You cannot deactivate your own active Administrator account.";
            $this->redirectBack();
        }

        $user = $this->getUserBasic($conn, $user_id);
        if (!$user) {
            $_SESSION['error_msg'] = "User not found.";
            $this->redirectBack();
        }

        $new_status = ($user['status'] === 'active') ? 'inactive' : 'active';
        $upd = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
        $upd->bind_param("si", $new_status, $user_id);
        $upd->execute();
        $upd->close();

        $status_label = ($new_status === 'active') ? 'Active (login enabled)' : 'Relieved / Inactive (login locked, documents preserved)';
        $_SESSION['success_msg'] = "Account status for {$user['full_name']} changed to: {$status_label}.";
        $this->redirectBack();
    }

    /**
     * Reset user password to default (Admin@123)
     */
    public function resetPassword(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        $this->ensureAdminAccess();
        csrfValidate();

        global $conn;

        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

        if ($user_id <= 0) {
            $_SESSION['error_msg'] = "Invalid user specified.";
            $this->redirectBack();
        }

        $user = $this->getUserBasic($conn, $user_id);
        if (!$user) {
            $_SESSION['error_msg'] = "User not found.";
            $this->redirectBack();
        }

        $default_hash = password_hash('Admin@123', PASSWORD_BCRYPT);
        $upd = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $upd->bind_param("si", $default_hash, $user_id);
        $upd->execute();
        $upd->close();

        $_SESSION['success_msg'] = "Password for {$user['full_name']} ({$user['email']}) has been reset to: Admin@123";
        $this->redirectBack();
    }

    /**
     * Safe User Deletion
     * Strictly blocked if the user has any uploaded documents or workflow action history.
     */
    public function delete(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        $this->ensureAdminAccess();
        csrfValidate();

        global $conn;

        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $current_admin_id = auth_user_id();

        if ($user_id <= 0) {
            $_SESSION['error_msg'] = "Invalid user specified.";
            $this->redirectBack();
        }

        if ($user_id === $current_admin_id) {
            $_SESSION['error_msg'] = "Security check: You cannot delete your own Administrator account.";
            $this->redirectBack();
        }

        $user = $this->getUserBasic($conn, $user_id);
        if (!$user) {
            $_SESSION['error_msg'] = "User not found.";
            $this->redirectBack();
        }

        // Check document count
        $doc_cnt_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM documents WHERE uploaded_by = ?");
        $doc_cnt_stmt->bind_param("i", $user_id);
        $doc_cnt_stmt->execute();
        $doc_cnt = (int)($doc_cnt_stmt->get_result()->fetch_assoc()['total'] ?? 0);
        $doc_cnt_stmt->close();

        // Check action history count
        $act_cnt_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM document_actions WHERE acted_by = ?");
        $act_cnt_stmt->bind_param("i", $user_id);
        $act_cnt_stmt->execute();
        $act_cnt = (int)($act_cnt_stmt->get_result()->fetch_assoc()['total'] ?? 0);
        $act_cnt_stmt->close();

        if ($doc_cnt > 0 || $act_cnt > 0) {
            $_SESSION['error_msg'] = "Integrity Protection: Cannot delete {$user['full_name']} because they have {$doc_cnt} uploaded document(s) and {$act_cnt} audit record(s). To revoke login access while protecting college records, please mark them as 'Relieved / Inactive' instead.";
            $this->redirectBack();
        }

        // Clean up user_roles and user_profiles first, then users
        $del_roles = $conn->prepare("DELETE FROM user_roles WHERE user_id = ?");
        $del_roles->bind_param("i", $user_id);
        $del_roles->execute();
        $del_roles->close();

        $del_prof = $conn->prepare("DELETE FROM user_profiles WHERE user_id = ?");
        $del_prof->bind_param("i", $user_id);
        $del_prof->execute();
        $del_prof->close();

        $del_user = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $del_user->bind_param("i", $user_id);
        $del_user->execute();
        $del_user->close();

        $_SESSION['success_msg'] = "Test account for {$user['full_name']} ({$user['email']}) was permanently deleted.";
        $this->redirectBack();
    }

    /**
     * Fast Direct User Registration by Admin
     */
    public function create(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        $this->ensureAdminAccess();
        csrfValidate();

        global $conn;

        $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $dept_id = isset($_POST['dept_id']) ? (int)$_POST['dept_id'] : 0;
        $role_key = isset($_POST['initial_role']) ? trim($_POST['initial_role']) : 'faculty';

        if (empty($full_name) || empty($email) || $dept_id <= 0) {
            $_SESSION['error_msg'] = "Full Name, Email, and Department are all required.";
            $this->redirectBack();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_msg'] = "Invalid email format.";
            $this->redirectBack();
        }

        // Check duplicate email
        $chk = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $chk->bind_param("s", $email);
        $chk->execute();
        $exists = $chk->get_result()->fetch_assoc();
        $chk->close();

        if ($exists) {
            $_SESSION['error_msg'] = "A user account with email '{$email}' already exists.";
            $this->redirectBack();
        }

        $password_hash = password_hash('Admin@123', PASSWORD_BCRYPT);
        $ins = $conn->prepare("INSERT INTO users (full_name, email, password, status, created_at) VALUES (?, ?, ?, 'active', NOW())");
        $ins->bind_param("sss", $full_name, $email, $password_hash);
        $ins->execute();
        $new_user_id = $conn->insert_id;
        $ins->close();

        if ($new_user_id <= 0) {
            $_SESSION['error_msg'] = "Database error creating user account.";
            $this->redirectBack();
        }

        // Always grant Faculty role for their department
        $this->ensureRoleExists($conn, $new_user_id, ROLE_FACULTY, $dept_id);

        if ($role_key === 'hod') {
            // Appoint as HOD (transferring previous if needed)
            $prev_stmt = $conn->prepare("SELECT user_id FROM user_roles WHERE role_id = ? AND dept_id = ?");
            $hr = ROLE_HOD;
            $prev_stmt->bind_param("ii", $hr, $dept_id);
            $prev_stmt->execute();
            $pres = $prev_stmt->get_result();
            if ($prow = $pres->fetch_assoc()) {
                $pid = (int)$prow['user_id'];
                if ($pid !== $new_user_id) {
                    $del_p = $conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id = ?");
                    $del_p->bind_param("iii", $pid, $hr, $dept_id);
                    $del_p->execute();
                    $del_p->close();
                    $this->ensureRoleExists($conn, $pid, ROLE_FACULTY, $dept_id);
                }
            }
            $prev_stmt->close();
            $this->ensureRoleExists($conn, $new_user_id, ROLE_HOD, $dept_id);
        } elseif ($role_key === 'coordinator') {
            $this->ensureRoleExists($conn, $new_user_id, ROLE_DEPT_COORDINATOR, $dept_id);
        }

        $_SESSION['success_msg'] = "Faculty account for {$full_name} created successfully with default password Admin@123.";
        $this->redirectBack();
    }

    private function ensureAdminAccess(): void {
        $active_role = auth_active_role();
        if (!$active_role || (int)$active_role['role_id'] !== ROLE_ADMIN) {
            $_SESSION['error_msg'] = "Access denied. User Management is restricted to Administrators.";
            header("Location: " . BASE_URL . "/public/index.php?route=dashboard");
            exit();
        }
    }

    private function getUserBasic(\mysqli $conn, int $user_id): ?array {
        $stmt = $conn->prepare("SELECT user_id, full_name, email, status FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res ?: null;
    }

    private function getDeptBasic(\mysqli $conn, int $dept_id): ?array {
        $stmt = $conn->prepare("SELECT dept_id, dept_name, is_academic FROM departments WHERE dept_id = ?");
        $stmt->bind_param("i", $dept_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res ?: null;
    }

    private function ensureRoleExists(\mysqli $conn, int $user_id, int $role_id, ?int $dept_id): void {
        $check = $conn->prepare("SELECT user_role_id FROM user_roles WHERE user_id = ? AND role_id = ? AND (dept_id = ? OR (dept_id IS NULL AND ? IS NULL))");
        $check->bind_param("iiii", $user_id, $role_id, $dept_id, $dept_id);
        $check->execute();
        $has_role = $check->get_result()->fetch_assoc();
        $check->close();

        if (!$has_role) {
            $ins = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, ?)");
            $ins->bind_param("iii", $user_id, $role_id, $dept_id);
            $ins->execute();
            $ins->close();
        }
    }

    private function redirectBack(): void {
        if (php_sapi_name() === 'cli') {
            return;
        }
        $target = BASE_URL . "/public/index.php?route=admin/users";
        if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'admin/users') !== false) {
            $target = $_SERVER['HTTP_REFERER'];
        }
        header("Location: " . $target);
        exit();
    }
}
