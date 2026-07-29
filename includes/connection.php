<?php

// Include config.php first for paths
require_once __DIR__ . '/../config.php';

// Database configuration for Local XAMPP Environment
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "master"; // Changed from project-fms to normalized master schema
$db_port = "3306";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ensure session is started natively since we dropped the separate session.php logic
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
