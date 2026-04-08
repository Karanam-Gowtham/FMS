<?php

$conn = mysqli_connect("localhost", "root", "", "project-fms");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

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
