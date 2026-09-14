<?php
/**
 * FMS Legacy Bridge
 * 
 * TEMPORARY compatibility layer for unmigrated legacy pages.
 * 
 * This file translates the canonical auth context ($_SESSION[SESSION_AUTH_KEY])
 * into legacy session variables that old pages expect.
 * 
 * It does NOT:
 *   - Authenticate users
 *   - Query legacy password tables
 *   - Create a second session model
 *   - Override canonical user identity
 *   - Allow privilege escalation
 * 
 * Legacy pages that still depend on this bridge:
 *   - modules/faculty/*.php          (expects $_SESSION['username'])
 *   - modules/dept_coordinator/*.php  (expects $_SESSION['a_username'], $_SESSION['dept'])
 *   - HOD/*.php                       (expects $_SESSION['h_username'], $_SESSION['dept'])
 *   - admin/*.php                     (expects $_SESSION['admin'])
 *   - modules/central/*.php           (expects $_SESSION['c_cord'], $_SESSION['c_username'])
 *   - modules/jr_assistant/*.php      (expects $_SESSION['j_username'], $_SESSION['dept'])
 * 
 * REMOVE this file once all legacy pages are migrated to use core/auth.php.
 */

require_once __DIR__ . '/constants.php';

/**
 * Populate legacy session variables from the canonical auth context.
 * Call this ONCE after bootstrap when serving a legacy page.
 */
function legacy_bridge_sync(): void
{
    if (!isset($_SESSION[SESSION_AUTH_KEY])) {
        return; // Not authenticated via new system — nothing to bridge
    }

    $auth = $_SESSION[SESSION_AUTH_KEY];
    $active = $auth['active_role'] ?? null;

    if (!$active) {
        return;
    }

    $identity = $auth['full_name'] ?? $auth['email'] ?? '';
    $email = $auth['email'] ?? '';
    $role_id = (int)($active['role_id'] ?? 0);
    $dept_name = $active['dept_name'] ?? '';

    // Set common session vars that many legacy pages check
    $_SESSION['user_id'] = $auth['user_id'];
    $_SESSION['full_name'] = $auth['full_name'];
    $_SESSION['email'] = $email;
    $_SESSION['logged_in'] = true;
    $_SESSION['roles'] = $auth['roles'];
    $_SESSION['role_id'] = $role_id;
    $_SESSION['role_name'] = $active['role_name'] ?? '';
    $_SESSION['dept_id'] = (int)($active['dept_id'] ?? 0);
    $_SESSION['dept_name'] = $dept_name;
    $_SESSION['user_identifier'] = $identity;

    // Clear all legacy role keys first to prevent stale state
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

    // Set role-specific legacy keys
    switch ($role_id) {
        case ROLE_FACULTY:
            $_SESSION['username'] = $identity;
            break;

        case ROLE_DEPT_COORDINATOR:
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

        case ROLE_RND_DEAN:
            // RnD_Dean is a new role — no legacy session key exists
            // Grant admin-level access for legacy pages that check $_SESSION['admin']
            $_SESSION['admin'] = $identity;
            break;
    }
}

/**
 * Check if the current session was authenticated via the new system.
 */
function legacy_bridge_is_new_auth(): bool
{
    return isset($_SESSION[SESSION_AUTH_KEY]);
}
