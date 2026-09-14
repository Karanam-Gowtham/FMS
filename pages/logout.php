<?php
/**
 * FMS Logout
 * Destroys the session, invalidates cookies, redirects to login.
 */
require_once __DIR__ . '/../core/bootstrap.php';

auth_logout();

// Start a new session just to set a flash message
session_start();
$_SESSION['_flash_logout'] = true;

header("Location: " . BASE_URL . "/pages/login.php");
exit();
