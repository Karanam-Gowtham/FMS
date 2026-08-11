<?php

// Include config.php first for paths
require_once __DIR__ . '/../config.php';

// Database configuration 
// For InfinityFree, replace these with credentials from your InfinityFree Control Panel -> MySQL Databases
$db_host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: "localhost");
$db_user = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: "root");
$db_pass = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') ?: "");
$db_name = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: "gmritfms");
$db_port = defined('DB_PORT') ? DB_PORT : (getenv('DB_PORT') ?: "3306");

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

if (!$conn) {
    $fallback_dbs = ['gmritfms', 'fms', 'project-fms'];
    foreach ($fallback_dbs as $fb) {
        if ($fb !== $db_name) {
            $conn = @mysqli_connect($db_host, $db_user, $db_pass, $fb, $db_port);
            if ($conn) break;
        }
    }
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ensure session is started natively since we dropped the separate session.php logic
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
