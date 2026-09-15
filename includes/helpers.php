<?php
/**
 * Helper functions for the FMS application.
 * Works with the normalized 'master' database schema.
 */

include_once __DIR__ . '/session.php';

include_once __DIR__ . '/../core/constants.php';


/**
 * Normalize role labels coming from the database or legacy pages.
 */
function normalizeRoleName(string $role_name): string
{
    $normalized = strtolower(trim(str_replace(['-', ' '], '_', $role_name)));

    $map = [
        'admin' => 'Admin',
        'iqac' => 'IQAC',
        'hod' => 'HOD',
        'faculty' => 'Faculty',
        'coordinator' => 'Coordinator',
        'dept_coordinator' => 'Coordinator',
        'department_coordinator' => 'Coordinator',
        'central_coordinator' => 'Central_Coordinator',
        'criteria_coordinator' => 'IQAC',
        'junior_assistant' => 'Junior_Assistant',
        'rnd_dean' => 'RnD_Dean'
    ];

    return $map[$normalized] ?? $role_name;
}

/**
 * Resolves a file path string from the database into an absolute path on disk.
 * Used by legacy dashboard download scripts to locate files.
 */
function fms_resolve_file_path(string $dbPath, string $scriptDir): string
{
    if (empty($dbPath)) {
        return '';
    }
    // If it's a modern upload, it starts with uploads/
    if (strpos($dbPath, 'uploads/') === 0) {
        $candidate = $scriptDir . '/../../' . $dbPath;
        if (file_exists($candidate) && is_file($candidate)) {
            return $candidate;
        }
    }
    // If it's a legacy upload, it usually starts with ../../uploads/
    if (strpos($dbPath, '../../') === 0) {
        $candidate = $scriptDir . '/' . $dbPath;
        if (file_exists($candidate) && is_file($candidate)) {
            return $candidate;
        }
    }
    // Fallback: check if the path works directly relative to script
    if (file_exists($dbPath) && is_file($dbPath)) {
        return $dbPath;
    }
    // Return original if not found, to let subsequent code handle the error
    return $dbPath;
}

/**
 * Clear all legacy role-specific session keys before activating a role.
 */
function clearLegacyRoleSessions(): void
{
    unset(
        $_SESSION['username'],
        $_SESSION['a_username'],
        $_SESSION['h_username'],
        $_SESSION['j_username'],
        $_SESSION['admin'],
        $_SESSION['c_username'],
        $_SESSION['c_cord'],
        $_SESSION['cri_username'],
        $_SESSION['dept']
    );
}

/**
 * Sync the active role into both modern and legacy session formats.
 */
function setActiveRoleContext(array $role, ?string $identity = null): void
{
    $role_id = (int) ($role['role_id'] ?? 0);
    $role_name = normalizeRoleName((string) ($role['role_name'] ?? ''));
    $dept_id = (int) ($role['dept_id'] ?? 0);
    $dept_name = (string) ($role['dept_name'] ?? '');
    $identity = trim((string) ($identity ?? ($_SESSION['email'] ?? '')));

    $_SESSION['role_id'] = $role_id;
    $_SESSION['role_name'] = $role_name;
    $_SESSION['dept_id'] = $dept_id;
    $_SESSION['dept_name'] = $dept_name;
    $_SESSION['active_role'] = [
        'role_id' => $role_id,
        'role_name' => $role_name,
        'dept_id' => $dept_id,
        'dept_name' => $dept_name,
    ];

    clearLegacyRoleSessions();

    switch ($role_id) {
        case ROLE_FACULTY:
            $_SESSION['username'] = $identity;
            break;
        case ROLE_COORDINATOR:
            $_SESSION['a_username'] = $identity;
            $_SESSION['dept'] = $dept_name;
            break;
        case ROLE_HOD:
            $_SESSION['h_username'] = $identity;
            $_SESSION['dept'] = $dept_name;
            break;
        case ROLE_JUNIOR_ASSISTANT:
            $_SESSION['j_username'] = $identity;
            $_SESSION['dept'] = $dept_name;
            break;
        case ROLE_ADMIN:
            $_SESSION['admin'] = $identity;
            break;
        case ROLE_CENTRAL_COORDINATOR:
            $_SESSION['c_username'] = $identity;
            $_SESSION['c_cord'] = $identity;
            break;
        case ROLE_IQAC:
            $_SESSION['cri_username'] = $identity;
            break;
    }
}


# Role Navigation Tools 

/**
 * Resolve the landing URL for a selected role.
 */
function getRoleLandingUrl(array $role): string
{
    $role_id = (int) ($role['role_id'] ?? 0);
    $dept_name = (string) ($role['dept_name'] ?? '');
    $encoded_dept = urlencode($dept_name);

    switch ($role_id) {
        case ROLE_FACULTY:
            return BASE_URL . "/modules/faculty/acd_year.php?dept={$encoded_dept}";
        case ROLE_COORDINATOR:
            return BASE_URL . "/modules/dept_coordinator/dc_acd_year.php?dept={$encoded_dept}";
        case ROLE_HOD:
            return BASE_URL . "/HOD/hod_acd_year.php?dept={$encoded_dept}&designation=HOD";
        case ROLE_JUNIOR_ASSISTANT:
            return BASE_URL . "/modules/jr_assistant/jr_acd_year.php?dept={$encoded_dept}";
        case ROLE_ADMIN:
            return BASE_URL . "/HOD/acd_year_aa.php?designation=admin";
        case ROLE_CENTRAL_COORDINATOR:
            return BASE_URL . "/modules/central/c_aqar_files.php?designation=central_coordinator&event={$encoded_dept}";
        case ROLE_IQAC:
            return BASE_URL . "/modules/central/c_aqar_files.php?designation=criteria_coordinator&event=IQAC";
        case ROLE_RND_DEAN:
            return BASE_URL . "/pages/rnd/dashboard.php";
        default:
            return BASE_URL . "/dashboard.php";
    }
}

/**
 * Get role name from role_id.
 */
function getRoleName($conn, $role_id)
{
    $stmt = $conn->prepare("SELECT role_name FROM Roles WHERE role_id = ?");
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? $row['role_name'] : 'Unknown';
}


/**
 * Require a specific role. Redirects to index if role doesn't match.
 */
function requireRole($allowed_roles)
{
    requireLogin();
    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }
    $has_role = false;
    foreach ($_SESSION['roles'] as $role) {
        if (in_array((int) $role['role_id'], $allowed_roles)) {
            $has_role = true;
            break;
        }
    }
    if (!$has_role) {
        header("Location: " . BASE_URL . "/index.php");
        exit();
    }
}


# Profile Tools 

/**
 * Get current logged-in user info from session.
 * Returns associative array or null if not logged in.
 */
function getCurrentUser($conn)
{
    if (isset($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        if ($user) {
            $user['roles'] = $_SESSION['roles'] ?? [];
            return $user;
        }
    }

    // Fallback: construct pseudo user object from session variables
    $identifier = $_SESSION['username'] ?? $_SESSION['h_username'] ?? $_SESSION['admin'] ?? $_SESSION['a_username'] ?? $_SESSION['c_username'] ?? $_SESSION['email'] ?? 'User';
    return [
        'user_id' => $_SESSION['user_id'] ?? 0,
        'full_name' => $identifier,
        'email' => $_SESSION['email'] ?? ($identifier . '@gmrit.edu.in'),
        'roles' => $_SESSION['roles'] ?? []
    ];
}

/**
 * Check if user is logged in.
 */
function isLoggedIn()
{
    return (!empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true)
        || isset($_SESSION['user_id'])
        || !empty($_SESSION['username'])
        || !empty($_SESSION['h_username'])
        || !empty($_SESSION['admin'])
        || !empty($_SESSION['a_username'])
        || !empty($_SESSION['c_username'])
        || !empty($_SESSION['c_cord']);
}

/**
 * Require login - redirect to login page if not logged in.
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/pages/login.php");
        exit();
    }
}


# Database Retrievals 

/**
 * Get all departments.
 */
function getDepartments($conn)
{
    $result = $conn->query("SELECT * FROM departments ORDER BY dept_name");
    $depts = [];
    while ($row = $result->fetch_assoc()) {
        $depts[] = $row;
    }
    return $depts;
}

/**
 * Get document types by category.
 */
function getDocumentTypes($conn, $category_id = null)
{
    if ($category_id) {
        $stmt = $conn->prepare("SELECT * FROM Document_Types WHERE category_id = ? ORDER BY type_name");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT * FROM Document_Types ORDER BY category_id, type_name");
    }
    $types = [];
    while ($row = $result->fetch_assoc()) {
        $types[] = $row;
    }
    return $types;
}

/**
 * Get active academic years.
 */
function getAcademicYears($conn) {
    $result = $conn->query("SELECT year_label as year_name FROM academic_years ORDER BY year_label DESC");
    if (!$result) return [];
    $years = [];
    while ($row = $result->fetch_assoc()) {
        $years[] = $row;
    }
    return $years;
}

/**
 * Get the current active academic year.
 */
function getActiveAcademicYear($conn) {
    $result = $conn->query("SELECT year_label as year_name FROM academic_years ORDER BY year_label DESC LIMIT 1");
    if (!$result) return null;
    return $result->fetch_assoc();
}

# Approval System

/**
 * Process document approval.
 * Looks up Approval_Flow to find the next step.
 * Returns the new status and next_role_id.
 */
function processApproval($conn, $document_id, $approver_user_id, $approver_role_id = null)
{
    require_once __DIR__ . '/../core/workflow_engine.php';
    $result = wf_execute_action($conn, $document_id, $approver_user_id, 'approve');
    return $result['success'];
}

/**
 * Process document rejection.
 */
function processRejection($conn, $document_id, $rejector_user_id, $rejector_role_id = null, $remarks = '')
{
    require_once __DIR__ . '/../core/workflow_engine.php';
    $result = wf_execute_action($conn, $document_id, $rejector_user_id, 'reject', $remarks);
    return $result['success'];
}

/**
 * Process document resubmission (after rejection).
 */
function processResubmission($conn, $document_id, $user_id, $role_id = null)
{
    require_once __DIR__ . '/../core/workflow_engine.php';
    $result = wf_execute_action($conn, $document_id, $user_id, 'resubmit');
    return $result['success'];
}

# Pending Documents

/**
 * Get count of pending documents for a user's role and department.
 */
function getPendingCount($conn, $user_id, $roles = [])
{
    if (empty($roles))
        return 0;

    $count = 0;
    
    // Modern Documents count (Data-Driven Workflow Engine)
    // Find all documents currently at a step assigned to one of the user's roles
    // taking scope (department vs global) into account.
    $modern_query = "
        SELECT COUNT(d.doc_id) as cnt 
        FROM Documents d
        JOIN workflow_steps ws ON d.current_step = ws.step_id
        WHERE d.status = 'pending' AND (
    ";
    
    $role_conditions = [];
    foreach ($roles as $role) {
        $rid = (int)$role['role_id'];
        $did = (int)$role['dept_id'];
        
        // Faculty (Uploader) sees their own rejected/pending docs that need resubmission
        if ($rid == ROLE_FACULTY) {
            // Add a subquery for the uploader count
            $uploader_query = "SELECT COUNT(*) as u_cnt FROM Documents WHERE uploaded_by = $user_id AND status = 'rejected'";
            $u_res = $conn->query($uploader_query);
            if ($u_res && $u_row = $u_res->fetch_assoc()) {
                $count += (int)$u_row['u_cnt'];
            }
        }
        
        // Data-driven scope check
        $role_conditions[] = "(ws.responsible_role_id = $rid AND (ws.scope = 'global' OR (ws.scope = 'department' AND d.dept_id = $did)))";
    }
    
    if (!empty($role_conditions)) {
        $modern_query .= implode(" OR ", $role_conditions) . ")";
        $res = $conn->query($modern_query);
        if ($res && $row = $res->fetch_assoc()) {
            $count += (int)$row['cnt'];
        }
    }

    return $count;
}




# File Systems 

/**
 * Generate a unique stored filename.
 */
function generateStoredFileName($original_name)
{
    $ext = pathinfo($original_name, PATHINFO_EXTENSION);
    return uniqid('doc_', true) . '.' . $ext;
}

/**
 * Handle file upload to the uploads directory.
 * Returns stored filename on success, false on failure.
 */
function handleFileUpload($file, $subfolder = '')
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $original_name = basename($file['name']);
    $stored_name = generateStoredFileName($original_name);

    $upload_dir = UPLOADS_PATH;
    if ($subfolder) {
        $upload_dir .= '/' . $subfolder;
    }

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $target_path = $upload_dir . '/' . $stored_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $relative_path = 'uploads/' . ($subfolder ? $subfolder . '/' : '') . $stored_name;
        return [
            'original_name' => $original_name,
            'stored_name' => $stored_name,
            'file_path' => $relative_path
        ];
    }

    return false;
}
?>