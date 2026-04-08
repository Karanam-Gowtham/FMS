<?php

// Database configuration using Environment Variables for Cloud (Railway/Render)
// Local fallback for XAMPP
$db_host = getenv('MYSQLHOST') ?: "localhost";
$db_user = getenv('MYSQLUSER') ?: "root";
$db_pass = getenv('MYSQLPASSWORD') ?: "";
$db_name = getenv('MYSQLDATABASE') ?: "project-fms";
$db_port = getenv('MYSQLPORT') ?: "3306";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set base_url dynamically (Local vs Cloud)
if (getenv('RAILWAY_STATIC_URL')) {
    $base_url = "https://" . getenv('RAILWAY_STATIC_URL') . "/";
} else {
    $base_url = "http://localhost/mini/FMS/";
}

// Centralized Session Initialization
$session_file = __DIR__ . '/session.php';
if (file_exists($session_file)) {
    include_once $session_file;
} else {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
