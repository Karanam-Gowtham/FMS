<?php
namespace App\Controllers;

use mysqli;

class UserController {

    /**
     * Ensure current user has ROLE_ADMIN.
     */
    private function checkAdmin(): array {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();

        $active_role_id = (int)auth_active_role_id();
        if ($active_role_id !== ROLE_ADMIN) {
            http_response_code(403);
            echo "<!DOCTYPE html><html><head><title>403 Forbidden</title><style>body{font-family:sans-serif;padding:50px;text-align:center;background:#f8fafc;}h1{color:#dc2626;}</style></head><body><h1>403 — Access Denied</h1><p>Institutional User Management is restricted to System Administrators only.</p><a href='" . BASE_URL . "/public/index.php?route=dashboard'>Return to Dashboard</a></body></html>";
            exit;
        }

        return auth_context();
    }

    /**
     * Display User Management List.
     */
    public function list(): void {
        $auth = $this->checkAdmin();
        global $conn;

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $filter_dept = isset($_GET['dept_id']) && is_numeric($_GET['dept_id']) ? (int)$_GET['dept_id'] : 0;
        $filter_role = isset($_GET['role_id']) && is_numeric($_GET['role_id']) ? (int)$_GET['role_id'] : 0;
        $filter_status = isset($_GET['status']) ? trim($_GET['status']) : 'all';

        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $per_page_raw = isset($_GET['per_page']) ? trim($_GET['per_page']) : '25';
        $per_page = ($per_page_raw === 'all') ? 1000 : max(10, min(100, (int)$per_page_raw));

        // Fetch lookups: Departments & Roles
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

        // Build filtering conditions
        $where = ["1=1"];
        $types = "";
        $params = [];

        if ($search !== '') {
            $where[] = "(u.full_name LIKE ? OR u.email LIKE ?)";
            $types .= "ss";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($filter_status === 'active' || $filter_status === 'inactive') {
            $where[] = "u.status = ?";
            $types .= "s";
            $params[] = $filter_status;
        }

        if ($filter_dept > 0) {
            $where[] = "EXISTS (SELECT 1 FROM user_roles ur_d WHERE ur_d.user_id = u.user_id AND ur_d.dept_id = ?)";
            $types .= "i";
            $params[] = $filter_dept;
        }

        if ($filter_role > 0) {
            $where[] = "EXISTS (SELECT 1 FROM user_roles ur_r WHERE ur_r.user_id = u.user_id AND ur_r.role_id = ?)";
            $types .= "i";
            $params[] = $filter_role;
        }

        $where_sql = implode(" AND ", $where);

        // Count total matching users
        $count_stmt = $conn->prepare("SELECT COUNT(DISTINCT u.user_id) as total FROM users u WHERE {$where_sql}");
        if (!empty($types)) {
            $count_stmt->bind_param($types, ...$params);
        }
        $count_stmt->execute();
        $total_users = (int)($count_stmt->get_result()->fetch_assoc()['total'] ?? 0);
        $count_stmt->close();

        $total_pages = ceil($total_users / $per_page);
        $offset = ($page - 1) * $per_page;

        // Query users with document count
        $query = "
            SELECT 
                u.user_id,
                u.full_name,
                u.email,
                u.status,
                u.created_at,
                u.last_login,
                (SELECT COUNT(*) FROM documents d WHERE d.uploaded_by = u.user_id) as doc_count,
                (SELECT COUNT(*) FROM action_history ah WHERE ah.user_id = u.user_id) as action_count
            FROM users u
            WHERE {$where_sql}
            ORDER BY u.user_id ASC
            LIMIT ? OFFSET ?
        ";

        $types_limit = $types . "ii";
        $params_limit = array_merge($params, [$per_page, $offset]);

        $stmt = $conn->prepare($query);
        $stmt->bind_param($types_limit, ...$params_limit);
        $stmt->execute();
        $users_res = $stmt->get_result();

        $users = [];
        $user_ids = [];
        while ($row = $users_res->fetch_assoc()) {
            $row['roles'] = [];
            $users[$row['user_id']] = $row;
            $user_ids[] = (int)$row['user_id'];
        }
        $stmt->close();

        // Load roles for fetched users
        if (!empty($user_ids)) {
            $in_clause = implode(',', $user_ids);
            $roles_sql = "
                SELECT 
                    ur.user_id,
                    ur.role_id,
                    ur.dept_id,
                    r.role_name,
                    d.dept_name
                FROM user_roles ur
                JOIN roles r ON ur.role_id = r.role_id
                LEFT JOIN departments d ON ur.dept_id = d.dept_id
                WHERE ur.user_id IN ({$in_clause})
                ORDER BY ur.role_id ASC
            ";
            $roles_res = $conn->query($roles_sql);
            if ($roles_res) {
                while ($rrow = $roles_res->fetch_assoc()) {
                    $uid = (int)$rrow['user_id'];
                    if (isset($users[$uid])) {
                        $users[$uid]['roles'][] = $rrow;
                    }
                }
            }
        }

        // Flash messages
        $success_msg = $_SESSION['success_msg'] ?? null;
        $error_msg   = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        require __DIR__ . '/../Views/users/list.php';
    }

    /**
     * Create a new user account.
     */
    public function create(): void {
        $this->checkAdmin();
        csrfValidate();
        global $conn;

        $full_name = trim($_POST['full_name'] ?? '');
        $email     = strtolower(trim($_POST['email'] ?? ''));
        $password  = trim($_POST['password'] ?? '');
        $dept_id   = isset($_POST['dept_id']) && is_numeric($_POST['dept_id']) ? (int)$_POST['dept_id'] : 0;
        $role_ids  = isset($_POST['role_ids']) && is_array($_POST['role_ids']) ? array_map('intval', $_POST['role_ids']) : [];

        if (empty($full_name) || empty($email)) {
            $_SESSION['error_msg'] = "Full name and email are required fields.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }

        // Auto-append domain if missing
        if (strpos($email, '@') === false) {
            $email .= '@gmrit.edu.in';
        }

        // Verify email unique
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        if ($check_stmt->get_result()->fetch_assoc()) {
            $check_stmt->close();
            $_SESSION['error_msg'] = "An account with the email '{$email}' already exists.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }
        $check_stmt->close();

        // Default password if blank
        $raw_pass = !empty($password) ? $password : 'Admin@123';
        $hashed_pass = password_hash($raw_pass, PASSWORD_BCRYPT);

        // Insert into users
        $ins = $conn->prepare("INSERT INTO users (full_name, email, password, status, created_at) VALUES (?, ?, ?, 'active', NOW())");
        $ins->bind_param("sss", $full_name, $email, $hashed_pass);
        if (!$ins->execute()) {
            $_SESSION['error_msg'] = "Database error creating user: " . $conn->error;
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }
        $new_user_id = $conn->insert_id;
        $ins->close();

        // Assign default Faculty role if no role was checked
        if (empty($role_ids)) {
            $role_ids = [ROLE_FACULTY];
        }

        // If HOD is assigned, enforce strictly 1 HOD rule for that department
        if (in_array(ROLE_HOD, $role_ids, true) && $dept_id > 0) {
            $this->transferHodRole($conn, $dept_id, $new_user_id, $full_name);
        }

        // Insert roles
        $ur_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, ?)");
        foreach ($role_ids as $r_id) {
            $assign_dept = in_array($r_id, [ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_RND_DEAN], true) ? null : ($dept_id > 0 ? $dept_id : null);
            $ur_stmt->bind_param("iii", $new_user_id, $r_id, $assign_dept);
            $ur_stmt->execute();
        }
        $ur_stmt->close();

        $_SESSION['success_msg'] = "User '{$full_name}' created successfully with initial password '{$raw_pass}'.";
        header("Location: " . BASE_URL . "/public/index.php?route=users/list");
        exit;
    }

    /**
     * Update Roles & Department for an existing user.
     */
    public function updateRoles(): void {
        $this->checkAdmin();
        csrfValidate();
        global $conn;

        $user_id   = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $dept_id   = isset($_POST['dept_id']) && is_numeric($_POST['dept_id']) ? (int)$_POST['dept_id'] : 0;
        $role_ids  = isset($_POST['role_ids']) && is_array($_POST['role_ids']) ? array_map('intval', $_POST['role_ids']) : [];

        if ($user_id <= 0) {
            $_SESSION['error_msg'] = "Invalid user specified.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }

        // Verify user exists
        $u_stmt = $conn->prepare("SELECT full_name, email FROM users WHERE user_id = ? LIMIT 1");
        $u_stmt->bind_param("i", $user_id);
        $u_stmt->execute();
        $user_data = $u_stmt->get_result()->fetch_assoc();
        $u_stmt->close();

        if (!$user_data) {
            $_SESSION['error_msg'] = "User not found.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }

        if (empty($role_ids)) {
            $_SESSION['error_msg'] = "A user must have at least one active role.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }

        $hod_transfer_notice = "";
        // If HOD is assigned, enforce strictly 1 HOD rule for that department
        if (in_array(ROLE_HOD, $role_ids, true) && $dept_id > 0) {
            $transferred_from = $this->transferHodRole($conn, $dept_id, $user_id, $user_data['full_name']);
            if ($transferred_from) {
                $hod_transfer_notice = " (Transferred HOD title from '{$transferred_from}')";
            }
        }

        // Delete existing roles
        $del_stmt = $conn->prepare("DELETE FROM user_roles WHERE user_id = ?");
        $del_stmt->bind_param("i", $user_id);
        $del_stmt->execute();
        $del_stmt->close();

        // Insert updated roles
        $ins_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, ?)");
        foreach ($role_ids as $r_id) {
            $assign_dept = in_array($r_id, [ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_RND_DEAN], true) ? null : ($dept_id > 0 ? $dept_id : null);
            $ins_stmt->bind_param("iii", $user_id, $r_id, $assign_dept);
            $ins_stmt->execute();
        }
        $ins_stmt->close();

        $_SESSION['success_msg'] = "Roles updated successfully for '{$user_data['full_name']}'{$hod_transfer_notice}.";
        header("Location: " . BASE_URL . "/public/index.php?route=users/list");
        exit;
    }

    /**
     * Enforce Strictly 1 HOD per Department.
     * Demotes previous HOD back to Faculty and promotes new HOD.
     */
    private function transferHodRole(mysqli $conn, int $dept_id, int $new_hod_user_id, string $new_hod_name): ?string {
        // Find existing HOD for this department
        $stmt = $conn->prepare("
            SELECT u.user_id, u.full_name 
            FROM user_roles ur
            JOIN users u ON ur.user_id = u.user_id
            WHERE ur.role_id = ? AND ur.dept_id = ? AND ur.user_id != ?
            LIMIT 1
        ");
        $hod_role = ROLE_HOD;
        $stmt->bind_param("iii", $hod_role, $dept_id, $new_hod_user_id);
        $stmt->execute();
        $prev_hod = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($prev_hod) {
            $old_user_id = (int)$prev_hod['user_id'];
            $old_name = $prev_hod['full_name'];

            // Remove HOD role from previous HOD
            $rem_stmt = $conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id = ?");
            $rem_stmt->bind_param("iii", $old_user_id, $hod_role, $dept_id);
            $rem_stmt->execute();
            $rem_stmt->close();

            // Ensure previous HOD retains at least Faculty role
            $fac_role = ROLE_FACULTY;
            $chk_fac = $conn->prepare("SELECT user_role_id FROM user_roles WHERE user_id = ? AND role_id = ?");
            $chk_fac->bind_param("ii", $old_user_id, $fac_role);
            $chk_fac->execute();
            if (!$chk_fac->get_result()->fetch_assoc()) {
                $ins_fac = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, ?)");
                $ins_fac->bind_param("iii", $old_user_id, $fac_role, $dept_id);
                $ins_fac->execute();
                $ins_fac->close();
            }
            $chk_fac->close();

            return $old_name;
        }

        return null;
    }

    /**
     * Toggle User Status between Active and Inactive (Relieved).
     */
    public function toggleStatus(): void {
        $auth = $this->checkAdmin();
        csrfValidate();
        global $conn;

        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        if ($user_id <= 0) {
            $_SESSION['error_msg'] = "Invalid user specified.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }

        // Prevent admin from deactivating their own currently logged-in account
        if ($user_id === (int)$auth['user_id']) {
            $_SESSION['error_msg'] = "Security protection: You cannot deactivate your own active Administrator account.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }

        $stmt = $conn->prepare("SELECT full_name, status FROM users WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $_SESSION['error_msg'] = "User not found.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }

        $new_status = ($user['status'] === 'active') ? 'inactive' : 'active';
        $upd = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
        $upd->bind_param("si", $new_status, $user_id);
        $upd->execute();
        $upd->close();

        if ($new_status === 'inactive') {
            $_SESSION['success_msg'] = "User '{$user['full_name']}' marked as Relieved / Inactive. Login access revoked, but all historical uploaded documents remain safe and intact.";
        } else {
            $_SESSION['success_msg'] = "User '{$user['full_name']}' has been reactivated. Login access restored.";
        }

        header("Location: " . BASE_URL . "/public/index.php?route=users/list");
        exit;
    }

    /**
     * Reset User Password to default Admin@123.
     */
    public function resetPassword(): void {
        $this->checkAdmin();
        csrfValidate();
        global $conn;

        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        if ($user_id <= 0) {
            $_SESSION['error_msg'] = "Invalid user specified.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }

        $stmt = $conn->prepare("SELECT full_name FROM users WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $_SESSION['error_msg'] = "User not found.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }

        $default_pass = 'Admin@123';
        $hashed = password_hash($default_pass, PASSWORD_BCRYPT);

        $upd = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $upd->bind_param("si", $hashed, $user_id);
        $upd->execute();
        $upd->close();

        $_SESSION['success_msg'] = "Password for '{$user['full_name']}' has been reset to default '{$default_pass}'.";
        header("Location: " . BASE_URL . "/public/index.php?route=users/list");
        exit;
    }

    /**
     * Delete User Account (strictly allowed ONLY if document count and audit count are 0).
     */
    public function delete(): void {
        $auth = $this->checkAdmin();
        csrfValidate();
        global $conn;

        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        if ($user_id <= 0) {
            $_SESSION['error_msg'] = "Invalid user specified.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }

        // Prevent admin from deleting themselves
        if ($user_id === (int)$auth['user_id']) {
            $_SESSION['error_msg'] = "Security protection: You cannot delete your own Administrator account.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }

        $stmt = $conn->prepare("SELECT full_name FROM users WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $_SESSION['error_msg'] = "User not found.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }

        // Safety verification: count documents and actions
        $doc_stmt = $conn->prepare("SELECT COUNT(*) as doc_count FROM documents WHERE uploaded_by = ?");
        $doc_stmt->bind_param("i", $user_id);
        $doc_stmt->execute();
        $doc_count = (int)($doc_stmt->get_result()->fetch_assoc()['doc_count'] ?? 0);
        $doc_stmt->close();

        $act_stmt = $conn->prepare("SELECT COUNT(*) as act_count FROM action_history WHERE user_id = ?");
        $act_stmt->bind_param("i", $user_id);
        $act_stmt->execute();
        $act_count = (int)($act_stmt->get_result()->fetch_assoc()['act_count'] ?? 0);
        $act_stmt->close();

        if ($doc_count > 0 || $act_count > 0) {
            $_SESSION['error_msg'] = "Institutional Safety Rule: Cannot delete user '{$user['full_name']}' because they have {$doc_count} uploaded document(s) and {$act_count} audit action(s). Please mark them as 'Relieved / Inactive' instead to preserve accreditation history.";
            header("Location: " . BASE_URL . "/public/index.php?route=users/list");
            exit;
        }

        // Clean user_roles and user_profiles
        $del_r = $conn->prepare("DELETE FROM user_roles WHERE user_id = ?");
        $del_r->bind_param("i", $user_id);
        $del_r->execute();
        $del_r->close();

        $del_p = $conn->prepare("DELETE FROM user_profiles WHERE user_id = ?");
        $del_p->bind_param("i", $user_id);
        $del_p->execute();
        $del_p->close();

        // Delete user
        $del_u = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $del_u->bind_param("i", $user_id);
        $del_u->execute();
        $del_u->close();

        $_SESSION['success_msg'] = "Test user account '{$user['full_name']}' permanently deleted.";
        header("Location: " . BASE_URL . "/public/index.php?route=users/list");
        exit;
    }
}
