<?php
/**
 * FMS Core Authentication Library
 * 
 * Provides centralized authentication and authorization functions.
 * All authentication decisions are made here — never in individual pages.
 * 
 * Canonical session structure ($_SESSION[SESSION_AUTH_KEY]):
 *   user_id        int    - users.user_id
 *   full_name      string - users.full_name
 *   email          string - users.email
 *   roles          array  - all user_roles assignments
 *   active_role    array  - currently selected role {role_id, role_name, dept_id, dept_name}
 *   login_time     int    - timestamp of authentication
 */

require_once __DIR__ . '/constants.php';

// ──────────────────────────────────────────────
// Authentication Context
// ──────────────────────────────────────────────

/**
 * Authenticate a user by email/userid and password.
 * Returns user array on success, false on failure.
 * Uses password_verify() — never plaintext comparison.
 */
function auth_authenticate(mysqli $conn, string $identifier, string $password): array|false
{
    // Normalise: if no @ symbol, append default domain
    $email = (strpos($identifier, '@') !== false) ? $identifier : ($identifier . '@gmrit.edu.in');

    // Query users table by email, full_name, or personnel number (per_no)
    $stmt = $conn->prepare("
        SELECT u.user_id, u.full_name, u.email, u.password, u.status
        FROM users u
        LEFT JOIN user_profiles up ON u.user_id = up.user_id
        WHERE (u.email = ? OR u.full_name = ? OR up.per_no = ?) AND u.status = 'active'
        LIMIT 1
    ");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("sss", $email, $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        // Run dummy password_verify to match execution timing and mitigate user enumeration
        password_verify($password, '$2y$10$abcdefghijklmnopqrstuuABCDEFGHIJKLMNOPQRSTUVWXYZ012345');
        return false;
    }

    // Verify password using bcrypt
    if (!password_verify($password, $user['password'])) {
        return false;
    }

    // Load all role assignments
    $roles = auth_load_user_roles($conn, (int)$user['user_id']);

    return [
        'user_id'   => (int)$user['user_id'],
        'full_name' => $user['full_name'],
        'email'     => $user['email'],
        'roles'     => $roles,
    ];
}

/**
 * Load all role assignments for a user from user_roles.
 */
function auth_load_user_roles(mysqli $conn, int $user_id): array
{
    $stmt = $conn->prepare("
        SELECT ur.user_role_id, ur.role_id, ur.dept_id, r.role_name, d.dept_name
        FROM user_roles ur
        JOIN roles r ON r.role_id = ur.role_id
        LEFT JOIN departments d ON d.dept_id = ur.dept_id
        WHERE ur.user_id = ?
        ORDER BY ur.role_id, ur.dept_id
    ");
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $roles = [];
    while ($row = $result->fetch_assoc()) {
        $roles[] = [
            'user_role_id' => (int)$row['user_role_id'],
            'role_id'      => (int)$row['role_id'],
            'role_name'    => $row['role_name'],
            'dept_id'      => (int)$row['dept_id'],
            'dept_name'    => $row['dept_name'] ?? '',
        ];
    }
    $stmt->close();

    return $roles;
}

/**
 * Create the canonical session after successful authentication.
 * Regenerates session ID and sets the auth context.
 */
function auth_create_session(array $user, array $active_role): void
{
    // Regenerate session ID to prevent session fixation
    if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
        session_regenerate_id(true);
    }

    $_SESSION[SESSION_AUTH_KEY] = [
        'user_id'       => $user['user_id'],
        'full_name'     => $user['full_name'],
        'email'         => $user['email'],
        'roles'         => $user['roles'],
        'active_role'   => $active_role,
        'login_time'    => time(),
        'last_activity' => time(),
    ];
}

/**
 * Update the active role in the session.
 * The role MUST be validated against the user's actual role assignments.
 */
function auth_switch_role(int $user_role_id): bool
{
    $auth = auth_context();
    if (!$auth) {
        return false;
    }

    $roles = isset($auth['roles']) && is_array($auth['roles']) ? $auth['roles'] : [];
    foreach ($roles as $role) {
        if (is_array($role) && (int)($role['user_role_id'] ?? 0) === $user_role_id) {
            if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
                session_regenerate_id(true);
            }
            $_SESSION[SESSION_AUTH_KEY]['active_role'] = $role;
            return true;
        }
    }

    return false; // Requested role not found in user's assignments
}

/**
 * Get the current auth context from session.
 * Returns the auth array or null if not authenticated.
 */
function auth_context(): ?array
{
    return $_SESSION[SESSION_AUTH_KEY] ?? null;
}

/**
 * Check if a user is currently authenticated.
 */
function auth_is_logged_in(): bool
{
    $auth = auth_context();
    if ($auth === null || empty($auth['user_id'])) {
        return false;
    }

    // Inactivity timeout defense for unattended sessions
    if (defined('SESSION_TIMEOUT') && SESSION_TIMEOUT > 0) {
        $last_act = $auth['last_activity'] ?? $auth['login_time'] ?? 0;
        if ($last_act > 0 && (time() - $last_act) > SESSION_TIMEOUT) {
            auth_logout();
            return false;
        }
        $_SESSION[SESSION_AUTH_KEY]['last_activity'] = time();
    }

    return true;
}

/**
 * Get the authenticated user's ID.
 */
function auth_user_id(): ?int
{
    $auth = auth_context();
    return $auth ? (int)$auth['user_id'] : null;
}

/**
 * Get the active role array.
 */
function auth_active_role(): ?array
{
    $auth = auth_context();
    return $auth['active_role'] ?? null;
}

/**
 * Get the active role ID.
 */
function auth_active_role_id(): ?int
{
    $role = auth_active_role();
    return $role ? (int)$role['role_id'] : null;
}

/**
 * Get the active department ID.
 */
function auth_active_dept_id(): ?int
{
    $role = auth_active_role();
    return $role ? (int)$role['dept_id'] : null;
}

/**
 * Get the active department name.
 */
function auth_active_dept_name(): string
{
    $role = auth_active_role();
    return $role['dept_name'] ?? '';
}

/**
 * Destroy the authenticated session completely.
 */
function auth_logout(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies') && !headers_sent()) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/**
 * Update last_login timestamp for a user.
 */
function auth_update_last_login(mysqli $conn, int $user_id): void
{
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

// ──────────────────────────────────────────────
// Authorization Guards
// ──────────────────────────────────────────────

/**
 * Require the user to be logged in. Redirects to login page if not.
 */
function require_login(): void
{
    if (!auth_is_logged_in()) {
        header("Location: " . BASE_URL . "/public/index.php?route=auth/login");
        exit();
    }
}

/**
 * Require the active role to be one of the allowed roles.
 * Sends 403 if the active role doesn't match.
 */
function require_role(array|int $allowed_roles): void
{
    require_login();

    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }

    $active_role_id = auth_active_role_id();
    if (!in_array($active_role_id, $allowed_roles, true)) {
        http_response_code(403);
        $error_message = "Access denied. Your current active role does not have permission to access this page.";
        if (file_exists(__DIR__ . '/../public/403.php')) {
            include __DIR__ . '/../public/403.php';
        } else {
            echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8');
        }
        exit();
    }
}

/**
 * Check if the user has ANY of the specified roles (not just the active one).
 */
function auth_has_role(int $role_id): bool
{
    $auth = auth_context();
    if (!$auth) {
        return false;
    }

    $roles = isset($auth['roles']) && is_array($auth['roles']) ? $auth['roles'] : [];
    foreach ($roles as $role) {
        $r_id = is_array($role) ? (int)($role['role_id'] ?? 0) : (int)$role;
        if ($r_id === $role_id) {
            return true;
        }
    }
    return false;
}

/**
 * Get all departments the user is authorized for (across all their roles).
 */
function get_authorized_depts(): array
{
    $auth = auth_context();
    if (!$auth) {
        return [];
    }

    $depts = [];
    $roles = isset($auth['roles']) && is_array($auth['roles']) ? $auth['roles'] : [];
    foreach ($roles as $role) {
        if (!is_array($role)) {
            continue;
        }
        $dept_id = (int)($role['dept_id'] ?? 0);
        if ($dept_id > 0 && !isset($depts[$dept_id])) {
            $depts[$dept_id] = (string)($role['dept_name'] ?? '');
        }
    }
    return $depts;
}

/**
 * Validate a department filter from user input against authorized departments.
 * Returns the validated dept_id or null if unauthorized.
 * 
 * IMPORTANT: Never trust dept_id from GET/POST directly. Always validate.
 */
function validate_dept_filter(?int $requested_dept_id): ?int
{
    $active_role_id = auth_active_role_id();
    $is_college_wide = in_array($active_role_id, [ROLE_ADMIN, ROLE_RND_DEAN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true);

    if ($requested_dept_id === null || $requested_dept_id <= 0) {
        $active_dept = auth_active_dept_id();
        if ($active_dept !== null && $active_dept > 0) {
            return $active_dept;
        }
        if ($is_college_wide) {
            return 1; // Default to primary academic department (CSE) for college-wide oversight roles
        }
        return null;
    }

    // College-wide oversight roles can see all departments
    if ($is_college_wide) {
        return $requested_dept_id;
    }

    // Other roles: must be in their authorized departments
    $authorized = get_authorized_depts();
    if (isset($authorized[$requested_dept_id])) {
        return $requested_dept_id;
    }

    return null; // Not authorized for this department
}

// ──────────────────────────────────────────────
// Role Landing URLs
// ──────────────────────────────────────────────

/**
 * Get the dashboard/landing URL for a specific role.
 * Used for post-login redirects.
 */
function get_role_landing_url(array $role): string
{
    $role_id = (int)($role['role_id'] ?? 0);

    // Student dashboard
    if ($role_id === ROLE_STUDENT) {
        return BASE_URL . "/public/index.php?route=student/dashboard";
    }

    // NBA/NAAC module redirection
    if ($role_id === ROLE_CENTRAL_COORDINATOR || $role_id === ROLE_IQAC) {
        return BASE_URL . "/public/index.php?route=nba/dashboard";
    }

    // Unified MVC Dashboard for all roles
    return BASE_URL . "/public/index.php?route=dashboard";
}

/**
 * Get a deduplicated list of distinct roles for role selection.
 * Merges same-role assignments across departments for display.
 */
function get_distinct_role_assignments(array $roles): array
{
    // Group by user_role_id (each is unique)
    // But for display, group same role_id entries
    $grouped = [];
    foreach ($roles as $role) {
        if (!is_array($role)) {
            continue;
        }
        $roleId = (int)($role['role_id'] ?? 0);
        $deptId = (int)($role['dept_id'] ?? 0);
        $key = $roleId . '_' . $deptId;
        $grouped[$key] = $role;
    }
    return array_values($grouped);
}
