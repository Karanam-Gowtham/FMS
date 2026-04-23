<?php

// Database configuration for Local XAMPP Environment
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "project-fms";
$db_port = "3306";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set base_url for local environment
$base_url = "http://localhost/mini/FMS/";

// Centralized Session Initialization
$session_file = __DIR__ . '/session.php';
if (file_exists($session_file)) {
    include_once $session_file;
} else {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
