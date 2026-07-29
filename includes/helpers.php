<?php
/**
 * Helper functions for the FMS application.
 * Works with the normalized 'master' database schema.
 */

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
 * Get current logged-in user info from session.
 * Returns associative array or null if not logged in.
 */
function getCurrentUser($conn) {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $user_id = (int) $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM Users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if ($user) {
        $user['roles'] = $_SESSION['roles'] ?? [];
    }
    return $user;
}

/**
 * Check if user is logged in.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['roles']);
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

    return $count;
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
