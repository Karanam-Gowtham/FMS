<?php
include_once '../../config.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy the session and log out the user
session_unset();
session_destroy();

// Redirect back to the index/landing page
header("Location: " . BASE_URL . "/index.php");
exit();
?>
