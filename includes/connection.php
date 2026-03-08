<?php

$conn = mysqli_connect("localhost", "root", "", "project-fms");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$base_url = "http://localhost/mini/FMS/";

// CSRF Protection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>