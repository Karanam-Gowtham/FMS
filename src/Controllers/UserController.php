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
class UserController extends BaseController {

    public function index(): void {
        $this->requireAdmin();

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

        // Determine primary department and format roles for each user
        foreach ($users as $uid => &$u_data) {
            $u_data['primary_dept_id'] = 0;
            $u_data['primary_dept_name'] = '';
            foreach ($u_data['roles'] as $r) {
                if (!empty($r['dept_id'])) {
                    $u_data['primary_dept_id'] = (int)$r['dept_id'];
                    $u_data['primary_dept_name'] = $r['dept_name'] ?? '';
                    break;
                }
            }
        }
        unset($u_data);

        // Fetch current single-holder institutional leadership
        $institutional_leaders = [
            ROLE_IQAC => null,
            ROLE_CENTRAL_COORDINATOR => null,
            ROLE_RND_DEAN => null,
        ];
        $ldr_res = $conn->query("
            SELECT ur.role_id, u.user_id, u.full_name, u.email
            FROM user_roles ur
            JOIN users u ON ur.user_id = u.user_id
            WHERE ur.role_id IN (2, 6, 8) AND ur.dept_id IS NULL
        ");
        if ($ldr_res) {
            while ($lrow = $ldr_res->fetch_assoc()) {
                $rid = (int)$lrow['role_id'];
                if (isset($institutional_leaders[$rid]) && !$institutional_leaders[$rid]) {
                    $institutional_leaders[$rid] = $lrow;
                }
            }
        }

        $this->renderView('admin/users', get_defined_vars());
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

        // Mutual Exclusivity: Institutional Leaders cannot hold HOD simultaneously
        $inst_roles = [ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_RND_DEAN];
        $in_clause = implode(',', $inst_roles);
        $chk_inst = $conn->query("
            SELECT ur.role_id, r.role_name 
            FROM user_roles ur 
            JOIN roles r ON ur.role_id = r.role_id 
            WHERE ur.user_id = {$user_id} AND ur.role_id IN ({$in_clause})
        ");
        $relieved_inst_msgs = [];
        if ($chk_inst && $chk_inst->num_rows > 0) {
            while ($inst_row = $chk_inst->fetch_assoc()) {
                $rel_inst = $conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id IS NULL");
                $rel_inst->bind_param("ii", $user_id, $inst_row['role_id']);
                $rel_inst->execute();
                $rel_inst->close();

                $relieved_inst_msgs[] = $inst_row['role_name'];
                if (function_exists('system_audit_log')) {
                    system_audit_log($conn, 'INSTITUTIONAL_RELIEVE', "Relieved {$user['full_name']} from {$inst_row['role_name']} upon appointment as HOD.", 'user', $user_id);
                }
            }
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

        $extra_note = !empty($relieved_inst_msgs) ? " (Stepped down from: " . implode(', ', $relieved_inst_msgs) . ")." : "";
        if (!empty($prev_hod_name)) {
            $_SESSION['success_msg'] = "HOD role for {$dept['dept_name']} successfully transferred from {$prev_hod_name} to {$user['full_name']}{$extra_note}.";
        } else {
            $_SESSION['success_msg'] = "{$user['full_name']} is now appointed as HOD of {$dept['dept_name']}{$extra_note}.";
        }

        if (function_exists('system_audit_log')) {
            $audit_desc = !empty($prev_hod_name) ? "Transferred HOD role for {$dept['dept_name']} from {$prev_hod_name} to {$user['full_name']}." : "Appointed {$user['full_name']} as HOD of {$dept['dept_name']}.";
            system_audit_log($conn, 'HOD_ASSIGN', $audit_desc, 'user', $user_id);
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

        if (function_exists('system_audit_log')) {
            $coord_act = ($action === 'revoke') ? 'COORDINATOR_REVOKE' : 'COORDINATOR_APPOINT';
            $coord_msg = ($action === 'revoke') ? "Revoked Coordinator role from {$user['full_name']} for {$dept['dept_name']}." : "Appointed {$user['full_name']} as Coordinator for {$dept['dept_name']}.";
            system_audit_log($conn, $coord_act, $coord_msg, 'user', $user_id);
        }

        $this->redirectBack();
    }

    /**
     * Appoint or Revoke Institutional Leadership role (IQAC, Central Coordinator, RnD Dean)
     * Strict Institutional Rule: Single active holder college-wide.
     * Mutual Exclusivity: Institutional Leaders cannot hold HOD simultaneously.
     */
    public function setInstitutionalRole(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        $this->ensureAdminAccess();
        csrfValidate();

        global $conn;

        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $role_id = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 0;
        $action = isset($_POST['action']) ? trim($_POST['action']) : 'appoint';

        $valid_roles = [
            ROLE_IQAC => 'IQAC Coordinator',
            ROLE_CENTRAL_COORDINATOR => 'Central Coordinator',
            ROLE_RND_DEAN => 'Dean of R&D'
        ];

        if ($user_id <= 0 || !isset($valid_roles[$role_id])) {
            $_SESSION['error_msg'] = "Invalid institutional role request.";
            $this->redirectBack();
        }

        $role_name = $valid_roles[$role_id];
        $user = $this->getUserBasic($conn, $user_id);

        if (!$user) {
            $_SESSION['error_msg'] = "Faculty member not found.";
            $this->redirectBack();
        }

        if ($action === 'revoke') {
            $del = $conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id IS NULL");
            $del->bind_param("ii", $user_id, $role_id);
            $del->execute();
            $del->close();

            $_SESSION['success_msg'] = "Relieved {$user['full_name']} from {$role_name} role.";
            if (function_exists('system_audit_log')) {
                system_audit_log($conn, 'INSTITUTIONAL_RELIEVE', "Relieved {$user['full_name']} ({$user['email']}) from {$role_name}.", 'user', $user_id);
            }
            $this->redirectBack();
        }

        // Action: APPOINT
        // 1. Mutual Exclusivity: Relieve any active HOD role held by this user
        $hod_role = ROLE_HOD;
        $chk_hod = $conn->prepare("
            SELECT ur.dept_id, d.dept_name 
            FROM user_roles ur 
            LEFT JOIN departments d ON ur.dept_id = d.dept_id 
            WHERE ur.user_id = ? AND ur.role_id = ?
        ");
        $chk_hod->bind_param("ii", $user_id, $hod_role);
        $chk_hod->execute();
        $hod_res = $chk_hod->get_result();
        $relieved_hod_depts = [];
        while ($hod_row = $hod_res->fetch_assoc()) {
            $old_dept_id = (int)$hod_row['dept_id'];
            $rel_h = $conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id = ?");
            $rel_h->bind_param("iii", $user_id, $hod_role, $old_dept_id);
            $rel_h->execute();
            $rel_h->close();

            $relieved_hod_depts[] = $hod_row['dept_name'] ?? "Dept #{$old_dept_id}";
            if (function_exists('system_audit_log')) {
                system_audit_log($conn, 'HOD_RELIEVE', "Relieved {$user['full_name']} from HOD of {$hod_row['dept_name']} upon appointment as {$role_name}.", 'user', $user_id);
            }
        }
        $chk_hod->close();

        // 2. Single-Holder Rule: Identify and relieve previous holder(s) of this institutional role
        $prev_stmt = $conn->prepare("SELECT user_id FROM user_roles WHERE role_id = ? AND dept_id IS NULL");
        $prev_stmt->bind_param("i", $role_id);
        $prev_stmt->execute();
        $pres = $prev_stmt->get_result();
        $prev_holders = [];
        while ($prow = $pres->fetch_assoc()) {
            $pid = (int)$prow['user_id'];
            if ($pid !== $user_id) {
                $prev_holders[] = $pid;
            }
        }
        $prev_stmt->close();

        $prev_holder_names = [];
        foreach ($prev_holders as $pid) {
            $del_p = $conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id IS NULL");
            $del_p->bind_param("ii", $pid, $role_id);
            $del_p->execute();
            $del_p->close();

            $p_user = $this->getUserBasic($conn, $pid);
            if ($p_user) {
                $prev_holder_names[] = $p_user['full_name'];
            }
        }

        // 3. Grant institutional role to new user
        $this->ensureRoleExists($conn, $user_id, $role_id, null);

        // Ensure user maintains their base faculty role in their parent department
        $dept_stmt = $conn->prepare("SELECT dept_id FROM user_roles WHERE user_id = ? AND dept_id IS NOT NULL LIMIT 1");
        $dept_stmt->bind_param("i", $user_id);
        $dept_stmt->execute();
        $dres = $dept_stmt->get_result()->fetch_assoc();
        $dept_stmt->close();
        if ($dres && !empty($dres['dept_id'])) {
            $this->ensureRoleExists($conn, $user_id, ROLE_FACULTY, (int)$dres['dept_id']);
        }

        $trans_msg = !empty($prev_holder_names) ? " (transferred from " . implode(', ', $prev_holder_names) . ")" : "";
        $hod_note = !empty($relieved_hod_depts) ? " Stepped down from HOD of " . implode(', ', $relieved_hod_depts) . "." : "";
        $_SESSION['success_msg'] = "{$user['full_name']} appointed as {$role_name}{$trans_msg}.{$hod_note}";

        if (function_exists('system_audit_log')) {
            $prev_str = !empty($prev_holder_names) ? "from " . implode(', ', $prev_holder_names) . " " : "";
            system_audit_log($conn, 'INSTITUTIONAL_ASSIGN', "Appointed {$user['full_name']} as {$role_name} {$prev_str}college-wide.", 'user', $user_id);
        }

        $this->redirectBack();
    }

    /**
     * Transfer a Faculty member to a new department
     * Strict Rule: Faculty member belongs to strictly one parent department.
     */
    public function transferDepartment(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        $this->ensureAdminAccess();
        csrfValidate();

        global $conn;

        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $new_dept_id = isset($_POST['dept_id']) ? (int)$_POST['dept_id'] : 0;

        if ($user_id <= 0 || $new_dept_id <= 0) {
            $_SESSION['error_msg'] = "Invalid user or target department.";
            $this->redirectBack();
        }

        $user = $this->getUserBasic($conn, $user_id);
        $new_dept = $this->getDeptBasic($conn, $new_dept_id);

        if (!$user || !$new_dept) {
            $_SESSION['error_msg'] = "User or target department not found.";
            $this->redirectBack();
        }

        // Identify current department
        $old_dept_stmt = $conn->prepare("
            SELECT d.dept_id, d.dept_name 
            FROM user_roles ur 
            JOIN departments d ON ur.dept_id = d.dept_id 
            WHERE ur.user_id = ? AND ur.dept_id IS NOT NULL 
            LIMIT 1
        ");
        $old_dept_stmt->bind_param("i", $user_id);
        $old_dept_stmt->execute();
        $old_dept = $old_dept_stmt->get_result()->fetch_assoc();
        $old_dept_stmt->close();

        if ($old_dept && (int)$old_dept['dept_id'] === $new_dept_id) {
            $_SESSION['success_msg'] = "{$user['full_name']} is already assigned to {$new_dept['dept_name']}.";
            $this->redirectBack();
        }

        $old_name = $old_dept ? $old_dept['dept_name'] : 'Unassigned';

        // 1. Relieve any HOD or Coordinator role in old department
        $del_lead = $conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id IN (?, ?) AND dept_id IS NOT NULL");
        $hod = ROLE_HOD;
        $coord = ROLE_DEPT_COORDINATOR;
        $del_lead->bind_param("iii", $user_id, $hod, $coord);
        $del_lead->execute();
        $del_lead->close();

        // 2. Remove all previous department-bound faculty roles
        $del_fac = $conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ?");
        $fac = ROLE_FACULTY;
        $del_fac->bind_param("ii", $user_id, $fac);
        $del_fac->execute();
        $del_fac->close();

        // 3. Ensure single Faculty role in new department
        $this->ensureRoleExists($conn, $user_id, ROLE_FACULTY, $new_dept_id);

        $_SESSION['success_msg'] = "{$user['full_name']} successfully transferred from {$old_name} to {$new_dept['dept_name']}.";

        if (function_exists('system_audit_log')) {
            system_audit_log($conn, 'DEPARTMENT_TRANSFER', "Transferred faculty member {$user['full_name']} from {$old_name} to {$new_dept['dept_name']}.", 'user', $user_id);
        }

        $this->redirectBack();
    }

    /**
     * Revoke a specific leadership or institutional role
     */
    public function revokeRole(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        $this->ensureAdminAccess();
        csrfValidate();

        global $conn;

        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $role_id = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 0;
        $dept_id = isset($_POST['dept_id']) && is_numeric($_POST['dept_id']) && (int)$_POST['dept_id'] > 0 ? (int)$_POST['dept_id'] : null;

        if ($user_id <= 0 || $role_id <= 0) {
            $_SESSION['error_msg'] = "Invalid parameters to revoke role.";
            $this->redirectBack();
        }

        if ($role_id === ROLE_FACULTY) {
            $_SESSION['error_msg'] = "Every faculty member must maintain base Faculty status. Use Department Transfer or Inactive Status instead of removing base faculty status.";
            $this->redirectBack();
        }

        if ($role_id === ROLE_ADMIN) {
            $_SESSION['error_msg'] = "Administrator roles cannot be revoked via this interface.";
            $this->redirectBack();
        }

        $user = $this->getUserBasic($conn, $user_id);
        if (!$user) {
            $_SESSION['error_msg'] = "User not found.";
            $this->redirectBack();
        }

        if ($dept_id === null) {
            $del = $conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id IS NULL");
            $del->bind_param("ii", $user_id, $role_id);
        } else {
            $del = $conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id = ?");
            $del->bind_param("iii", $user_id, $role_id, $dept_id);
        }
        $del->execute();
        $del->close();

        // Fetch role name for notification
        $r_stmt = $conn->prepare("SELECT role_name FROM roles WHERE role_id = ?");
        $r_stmt->bind_param("i", $role_id);
        $r_stmt->execute();
        $r_name = $r_stmt->get_result()->fetch_assoc()['role_name'] ?? "Role #{$role_id}";
        $r_stmt->close();

        $_SESSION['success_msg'] = "Revoked {$r_name} role from {$user['full_name']}.";

        if (function_exists('system_audit_log')) {
            system_audit_log($conn, 'ROLE_REVOKE', "Revoked {$r_name} role from {$user['full_name']} ({$user['email']}).", 'user', $user_id);
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

        if (function_exists('system_audit_log')) {
            system_audit_log($conn, 'USER_STATUS_CHANGE', "Changed account status for {$user['full_name']} ({$user['email']}) to {$new_status}.", 'user', $user_id);
        }

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

        if (function_exists('system_audit_log')) {
            system_audit_log($conn, 'PASSWORD_RESET', "Reset password for {$user['full_name']} ({$user['email']}) to default.", 'user', $user_id);
        }

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

        if (function_exists('system_audit_log')) {
            system_audit_log($conn, 'USER_DELETE', "Permanently deleted test user {$user['full_name']} ({$user['email']}).", 'user', $user_id);
        }

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
        } elseif (in_array($role_key, ['iqac', 'central_coordinator', 'rnd_dean'], true)) {
            $target_inst_role = match ($role_key) {
                'iqac' => ROLE_IQAC,
                'central_coordinator' => ROLE_CENTRAL_COORDINATOR,
                'rnd_dean' => ROLE_RND_DEAN,
            };

            // Single-holder institutional transfer
            $prev_inst = $conn->prepare("SELECT user_id FROM user_roles WHERE role_id = ? AND dept_id IS NULL");
            $prev_inst->bind_param("i", $target_inst_role);
            $prev_inst->execute();
            $ires = $prev_inst->get_result();
            while ($irow = $ires->fetch_assoc()) {
                $ipid = (int)$irow['user_id'];
                if ($ipid !== $new_user_id) {
                    $del_i = $conn->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id IS NULL");
                    $del_i->bind_param("ii", $ipid, $target_inst_role);
                    $del_i->execute();
                    $del_i->close();
                }
            }
            $prev_inst->close();

            $this->ensureRoleExists($conn, $new_user_id, $target_inst_role, null);
        }

        $_SESSION['success_msg'] = "Faculty account for {$full_name} created successfully with default password Admin@123.";

        if (function_exists('system_audit_log')) {
            system_audit_log($conn, 'USER_CREATE', "Created faculty account for {$full_name} ({$email}) in department #{$dept_id} with initial role {$role_key}.", 'user', $new_user_id);
        }

        $this->redirectBack();
    }

    private function ensureAdminAccess(): void {
        $this->requireAdmin();
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
        if ($dept_id === null) {
            $check = $conn->prepare("SELECT user_role_id FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id IS NULL");
            $check->bind_param("ii", $user_id, $role_id);
        } else {
            $check = $conn->prepare("SELECT user_role_id FROM user_roles WHERE user_id = ? AND role_id = ? AND dept_id = ?");
            $check->bind_param("iii", $user_id, $role_id, $dept_id);
        }
        $check->execute();
        $has_role = $check->get_result()->fetch_assoc();
        $check->close();

        if (!$has_role) {
            if ($dept_id === null) {
                $ins = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, NULL)");
                $ins->bind_param("ii", $user_id, $role_id);
            } else {
                $ins = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, ?)");
                $ins->bind_param("iii", $user_id, $role_id, $dept_id);
            }
            $ins->execute();
            $ins->close();
        }
    }
}
