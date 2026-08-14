<?php
/**
 * Helper functions for the FMS application.
 * Works with the normalized 'master' database schema.
 */

include_once __DIR__ . '/session.php';

// Role ID constants (match Roles table)
define('ROLE_ADMIN', 1);
define('ROLE_IQAC', 2);
define('ROLE_HOD', 3);
define('ROLE_FACULTY', 4);
define('ROLE_COORDINATOR', 5);
define('ROLE_CENTRAL_COORDINATOR', 6);
define('ROLE_JUNIOR_ASSISTANT', 7);

// Document status constants
define('DOC_PENDING', 'Pending');
define('DOC_APPROVED', 'Approved');
define('DOC_REJECTED', 'Rejected');

/**
 * Normalize role labels coming from the database or legacy pages.
 */
function normalizeRoleName(string $role_name): string {
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
    ];

    return $map[$normalized] ?? $role_name;
}

/**
 * Clear all legacy role-specific session keys before activating a role.
 */
function clearLegacyRoleSessions(): void {
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
function setActiveRoleContext(array $role, ?string $identity = null): void {
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

/**
 * Resolve the landing URL for a selected role.
 */
function getRoleLandingUrl(array $role): string {
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
        default:
            return BASE_URL . "/dashboard.php";
    }
}

/**
 * Get current logged-in user info from session.
 * Returns associative array or null if not logged in.
 */
function getCurrentUser($conn) {
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
function isLoggedIn() {
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
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/modules/auth/login.php");
        exit();
    }
}

/**
 * Require a specific role. Redirects to index if role doesn't match.
 */
function requireRole($allowed_roles) {
    requireLogin();
    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }
    $has_role = false;
    foreach ($_SESSION['roles'] as $role) {
        if (in_array((int)$role['role_id'], $allowed_roles)) {
            $has_role = true;
            break;
        }
    }
    if (!$has_role) {
        header("Location: " . BASE_URL . "/index.php");
        exit();
    }
}

/**
 * Get role name from role_id.
 */
function getRoleName($conn, $role_id) {
    $stmt = $conn->prepare("SELECT role_name FROM Roles WHERE role_id = ?");
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? $row['role_name'] : 'Unknown';
}

/**
 * Get all departments.
 */
function getDepartments($conn) {
    $result = $conn->query("SELECT * FROM Dept ORDER BY dept_name");
    $depts = [];
    while ($row = $result->fetch_assoc()) {
        $depts[] = $row;
    }
    return $depts;
}

/**
 * Get document types by category.
 */
function getDocumentTypes($conn, $category_id = null) {
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
    $result = $conn->query("SELECT * FROM Academic_Years ORDER BY year_name DESC");
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
    $result = $conn->query("SELECT * FROM Academic_Years WHERE is_active = 1 LIMIT 1");
    return $result->fetch_assoc();
}

/**
 * Process document approval.
 * Looks up Approval_Flow to find the next step.
 * Returns the new status and next_role_id.
 */
function processApproval($conn, $document_id, $approver_user_id, $approver_role_id) {
    // Get document info
    $stmt = $conn->prepare("SELECT * FROM Documents WHERE document_id = ?");
    $stmt->bind_param("i", $document_id);
    $stmt->execute();
    $doc = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$doc) return false;

    // Find the current step in approval flow
    $stmt = $conn->prepare("
        SELECT * FROM Approval_Flow
        WHERE type_id = ? AND current_role_id = ?
        ORDER BY sequence_no ASC LIMIT 1
    ");
    $stmt->bind_param("ii", $doc['type_id'], $doc['current_role_id']);
    $stmt->execute();
    $current_step = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$current_step) {
        // No flow step found - mark as approved
        $update = $conn->prepare("UPDATE Documents SET status = 'Approved', current_role_id = ? WHERE document_id = ?");
        $update->bind_param("ii", $approver_role_id, $document_id);
        $update->execute();
        $update->close();
    } else {
        // Find next step
        $stmt = $conn->prepare("
            SELECT * FROM Approval_Flow
            WHERE type_id = ? AND sequence_no > ?
            ORDER BY sequence_no ASC LIMIT 1
        ");
        $stmt->bind_param("ii", $doc['type_id'], $current_step['sequence_no']);
        $stmt->execute();
        $next_step = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($next_step) {
            // Move to next approval step
            $update = $conn->prepare("UPDATE Documents SET current_role_id = ? WHERE document_id = ?");
            $update->bind_param("ii", $next_step['current_role_id'], $document_id);
            $update->execute();
            $update->close();
        } else {
            // No more steps - approved!
            $update = $conn->prepare("UPDATE Documents SET status = 'Approved' WHERE document_id = ?");
            $update->bind_param("i", $document_id);
            $update->execute();
            $update->close();
        }
    }

    // Log the action
    $action = $conn->prepare("
        INSERT INTO Document_Actions (document_id, user_id, role_id, action_type)
        VALUES (?, ?, ?, 'Approved')
    ");
    $action->bind_param("iii", $document_id, $approver_user_id, $approver_role_id);
    $action->execute();
    $action->close();

    return true;
}

/**
 * Process document rejection.
 */
function processRejection($conn, $document_id, $rejector_user_id, $rejector_role_id, $remarks = '') {
    $update = $conn->prepare("UPDATE Documents SET status = 'Rejected' WHERE document_id = ?");
    $update->bind_param("i", $document_id);
    $update->execute();
    $update->close();

    $action = $conn->prepare("
        INSERT INTO Document_Actions (document_id, user_id, role_id, action_type, remarks)
        VALUES (?, ?, ?, 'Rejected', ?)
    ");
    $action->bind_param("iiis", $document_id, $rejector_user_id, $rejector_role_id, $remarks);
    $action->execute();
    $action->close();

    return true;
}

/**
 * Process document resubmission (after rejection).
 */
function processResubmission($conn, $document_id, $user_id, $role_id) {
    // Get document type to find the first approval step
    $stmt = $conn->prepare("SELECT type_id FROM Documents WHERE document_id = ?");
    $stmt->bind_param("i", $document_id);
    $stmt->execute();
    $doc = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$doc) return false;

    // Find first approval step
    $stmt = $conn->prepare("
        SELECT current_role_id FROM Approval_Flow
        WHERE type_id = ? ORDER BY sequence_no ASC LIMIT 1
    ");
    $stmt->bind_param("i", $doc['type_id']);
    $stmt->execute();
    $first_step = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $first_role = $first_step ? $first_step['current_role_id'] : ROLE_HOD;

    $update = $conn->prepare("UPDATE Documents SET status = 'Pending', current_role_id = ? WHERE document_id = ?");
    $update->bind_param("ii", $first_role, $document_id);
    $update->execute();
    $update->close();

    $action = $conn->prepare("
        INSERT INTO Document_Actions (document_id, user_id, role_id, action_type)
        VALUES (?, ?, ?, 'Resubmitted')
    ");
    $action->bind_param("iii", $document_id, $user_id, $role_id);
    $action->execute();
    $action->close();

    return true;
}

/**
 * Get count of pending documents for a user's role and department.
 */
function getPendingCount($conn, $user_id, $roles = []) {
    if (empty($roles)) return 0;
    
    $where_clauses = [];
    $params = [];
    $types = "";

    foreach ($roles as $role) {
        $rid = (int)$role['role_id'];
        $did = (int)$role['dept_id'];
        
        if ($rid == ROLE_FACULTY) {
            $where_clauses[] = "(uploaded_by = ? AND status IN ('Pending', 'Rejected'))";
            $types .= "i";
            $params[] = $user_id;
        } elseif ($rid == ROLE_HOD || $rid == ROLE_COORDINATOR) {
            $where_clauses[] = "(current_role_id = ? AND dept_id = ? AND status = 'Pending')";
            $types .= "ii";
            $params[] = $rid;
            $params[] = $did;
        } elseif ($rid == ROLE_IQAC || $rid == ROLE_ADMIN) {
            $where_clauses[] = "(current_role_id = ? AND status = 'Pending')";
            $types .= "i";
            $params[] = $rid;
        }
    }

    if (empty($where_clauses)) return 0;

    $query = "SELECT COUNT(*) as cnt FROM Documents WHERE " . implode(" OR ", $where_clauses);
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $count = (int)$result['cnt'];
    $stmt->close();
    
    // Add Legacy Table Counts
    foreach ($roles as $role) {
        $rid = (int)$role['role_id'];
        $d_name = '';
        if ($rid == ROLE_HOD || $rid == ROLE_COORDINATOR) {
            $stmt = $conn->prepare("SELECT dept_name FROM Dept WHERE dept_id = ?");
            $stmt->bind_param("i", $role['dept_id']);
            $stmt->execute();
            $dres = $stmt->get_result()->fetch_assoc();
            if ($dres) {
                $d_name = $dres['dept_name'];
            }
            $stmt->close();
        }
        
        $email = $_SESSION['email'] ?? '';
        $username = $_SESSION['username'] ?? $_SESSION['h_username'] ?? $_SESSION['a_username'] ?? '';
        
        $legacy_docs = getLegacyPendingDocs($conn, $rid, $d_name, $email, $username);
        $count += count($legacy_docs);
    }

    return $count;
}

/**
 * Fetch pending documents from legacy tables to support previous workflow dashboards.
 */
function getLegacyPendingDocs($conn, $role_id, $dept_name = '', $user_email = '', $user_identifier = '') {
    $docs = [];
    $legacy_status = '';
    
    if ($role_id == ROLE_HOD) {
        $legacy_status = 'Pending HOD';
    } elseif ($role_id == ROLE_COORDINATOR) {
        $legacy_status = 'Pending Dept Coordinator';
    } elseif ($role_id == ROLE_CENTRAL_COORDINATOR) {
        $legacy_status = 'Pending Central Coordinator';
    } elseif ($role_id == ROLE_FACULTY) {
        $legacy_status = 'Pending'; // For faculty, they want to see their own pending/rejected docs
    } else {
        return [];
    }

    $tables = [
        ['table' => 'patents_table', 'title_col' => 'patent_title', 'branch_col' => 'branch', 'user_col' => 'Username', 'date_col' => 'submission_time', 'file_col' => 'patent_file', 'type' => 'Patent'],
        ['table' => 'published_tab', 'title_col' => 'paper_title', 'branch_col' => 'branch', 'user_col' => 'username', 'date_col' => 'submission_time', 'file_col' => 'paper_file', 'type' => 'Published Paper'],
        ['table' => 'conference_tab', 'title_col' => 'paper_title', 'branch_col' => 'branch', 'user_col' => 'username', 'date_col' => 'submission_time', 'file_col' => 'paper_file_path', 'type' => 'Conference Paper'],
        ['table' => 'fdps_tab', 'title_col' => 'title', 'branch_col' => 'branch', 'user_col' => 'username', 'date_col' => 'submission_time', 'file_col' => 'certificate', 'type' => 'FDP Attended'],
        ['table' => 'conf_org_tab', 'title_col' => 'title', 'branch_col' => 'branch', 'user_col' => 'username', 'date_col' => 'submission_time', 'file_col' => 'brochure', 'type' => 'Conference Organised'],
        ['table' => 'fdps_org_tab', 'title_col' => 'title', 'branch_col' => 'branch', 'user_col' => 'username', 'date_col' => 'submission_time', 'file_col' => 'merged_file', 'type' => 'FDP Organised'],
        ['table' => 'dept_files', 'title_col' => 'file_name', 'branch_col' => 'dept', 'user_col' => 'username', 'date_col' => 'uploaded_at', 'file_col' => 'file_path', 'type' => 'Dept File']
    ];
    
    // Legacy branches sometimes don't have underscores
    $branch_legacy = str_replace('_', '', $dept_name);

    foreach ($tables as $t) {
        $query = "SELECT id as document_id, '{$t['table']}' as source_table, 
                         {$t['title_col']} as original_file_name, '{$t['type']}' as type_name, 
                         {$t['user_col']} as uploader_email, {$t['date_col']} as updated_at, 
                         status, {$t['file_col']} as file_path 
                  FROM {$t['table']} 
                  WHERE ";
                  
        $params = [];
        $types = "";
        
        if ($role_id == ROLE_FACULTY) {
            $query .= "({$t['user_col']} = ? OR {$t['user_col']} = ?)";
            $params[] = $user_email;
            $params[] = $user_identifier;
            $types .= "ss";
        } else {
            $query .= "({$t['branch_col']} = ? OR {$t['branch_col']} = ?) AND status = ?";
            $params[] = $dept_name;
            $params[] = $branch_legacy;
            $params[] = $legacy_status;
            $types .= "sss";
        }
        
        $stmt = $conn->prepare($query);
        if ($stmt) {
            if(!empty($params)) $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
            while($row = $res->fetch_assoc()) {
                // Fetch uploader name for the dashboard
                $row['uploader_name'] = $row['uploader_email']; // default
                $u_stmt = $conn->prepare("SELECT full_name FROM users WHERE email = ? LIMIT 1");
                if ($u_stmt) {
                    $u_stmt->bind_param("s", $row['uploader_email']);
                    $u_stmt->execute();
                    $u_res = $u_stmt->get_result();
                    if ($u_row = $u_res->fetch_assoc()) {
                        $row['uploader_name'] = $u_row['full_name'];
                    }
                    $u_stmt->close();
                }
                $docs[] = $row;
            }
            $stmt->close();
        }
    }
    return $docs;
}

/**
 * Generate a unique stored filename.
 */
function generateStoredFileName($original_name) {
    $ext = pathinfo($original_name, PATHINFO_EXTENSION);
    return uniqid('doc_', true) . '.' . $ext;
}

/**
 * Handle file upload to the uploads directory.
 * Returns stored filename on success, false on failure.
 */
function handleFileUpload($file, $subfolder = '') {
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
