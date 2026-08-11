<?php

// Include config.php first for paths
require_once __DIR__ . '/../config.php';

// Database configuration 
// For InfinityFree, replace these with credentials from your InfinityFree Control Panel -> MySQL Databases
$db_host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: "localhost");
$db_user = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: "root");
$db_pass = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') ?: "");
$db_name = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: "project-fms");
$db_port = defined('DB_PORT') ? DB_PORT : (getenv('DB_PORT') ?: "3306");

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ensure session is started natively since we dropped the separate session.php logic
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
