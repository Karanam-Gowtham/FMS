<?php
/**
 * FMS Role Switch Handler (POST only)
 * 
 * Switches the active role for an already-authenticated user.
 * Validates user_role_id server-side against the user's actual assignments.
 */
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/legacy_bridge.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "/pages/dashboard.php");
    exit();
}

csrfValidate();

$user_role_id = (int)($_POST['user_role_id'] ?? 0);

if (auth_switch_role($user_role_id)) {
    legacy_bridge_sync();
    $new_role = auth_active_role();
    header("Location: " . get_role_landing_url($new_role));
} else {
    // Invalid role — back to dashboard
    header("Location: " . BASE_URL . "/pages/dashboard.php");
}
exit();
