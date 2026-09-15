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

    // Query users table by email OR full_name
    $stmt = $conn->prepare("
        SELECT user_id, full_name, email, password, status
        FROM users
        WHERE (email = ? OR full_name = ?) AND status = 'active'
        LIMIT 1
    ");
    $stmt->bind_param("ss", $email, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
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
    session_regenerate_id(true);

    $_SESSION[SESSION_AUTH_KEY] = [
        'user_id'     => $user['user_id'],
        'full_name'   => $user['full_name'],
        'email'       => $user['email'],
        'roles'       => $user['roles'],
        'active_role' => $active_role,
        'login_time'  => time(),
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

    foreach ($auth['roles'] as $role) {
        if ((int)$role['user_role_id'] === $user_role_id) {
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
    return $auth !== null && !empty($auth['user_id']);
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

    if (ini_get('session.use_cookies')) {
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

    session_destroy();
}

/**
 * Update last_login timestamp for a user.
 */
function auth_update_last_login(mysqli $conn, int $user_id): void
{
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
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
        header("Location: " . BASE_URL . "/pages/login.php");
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
        die("Access denied. Your current role does not have permission to access this page.");
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

    foreach ($auth['roles'] as $role) {
        if ((int)$role['role_id'] === $role_id) {
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
    foreach ($auth['roles'] as $role) {
        $dept_id = (int)$role['dept_id'];
        if ($dept_id > 0 && !isset($depts[$dept_id])) {
            $depts[$dept_id] = $role['dept_name'];
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
    if ($requested_dept_id === null) {
        return auth_active_dept_id();
    }

    // Admin and RnD_Dean can see all departments
    $active_role_id = auth_active_role_id();
    if (in_array($active_role_id, [ROLE_ADMIN, ROLE_RND_DEAN], true)) {
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
    $role_id = (int)$role['role_id'];
    $dept = urlencode($role['dept_name'] ?? '');

    switch ($role_id) {
        case ROLE_FACULTY:
            return BASE_URL . "/modules/faculty/acd_year.php?dept={$dept}";
        case ROLE_DEPT_COORDINATOR:
            return BASE_URL . "/modules/dept_coordinator/dc_acd_year.php?dept={$dept}";
        case ROLE_HOD:
            return BASE_URL . "/HOD/hod_acd_year.php?dept={$dept}&designation=HOD";
        case ROLE_JUNIOR_ASSISTANT:
            return BASE_URL . "/modules/jr_assistant/jr_acd_year.php?dept={$dept}";
        case ROLE_ADMIN:
            return BASE_URL . "/HOD/acd_year_aa.php?designation=admin";
        case ROLE_CENTRAL_COORDINATOR:
            if ($role['dept_name'] === 'NAAC' || $role['dept_name'] === 'NBA') {
                return BASE_URL . "/modules/central/c_aqar_files.php?designation=criteria_coordinator&event={$dept}";
            } else {
                return BASE_URL . "/modules/central/c_upload.php?event={$dept}";
            }
        case ROLE_IQAC:
            return BASE_URL . "/modules/central/c_aqar_files.php?designation=criteria_coordinator&event=IQAC";
        case ROLE_RND_DEAN:
            return BASE_URL . "/pages/rnd/dashboard.php";
        default:
            return BASE_URL . "/pages/dashboard.php";
    }
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
        $key = $role['role_id'] . '_' . $role['dept_id'];
        $grouped[$key] = $role;
    }
    return array_values($grouped);
}
