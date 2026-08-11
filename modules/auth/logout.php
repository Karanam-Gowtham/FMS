<?php
ob_start();
include_once '../../config.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Unset all session variables
$_SESSION = array();

// If session cookie exists, destroy cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_unset();
session_destroy();

$redirect_url = defined('BASE_URL') ? BASE_URL . '/index.php' : '/mini/FMS/index.php';

// Clear output buffer and redirect
if (ob_get_length()) {
    ob_clean();
}

header("Location: " . $redirect_url);
echo "<script>window.location.href = '" . addslashes($redirect_url) . "';</script>";
exit();
?>
