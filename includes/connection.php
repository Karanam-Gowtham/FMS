<?php

// Include config.php first for paths and DB constants
require_once __DIR__ . '/../config.php';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT);

if (!$conn) {
    error_log("FMS Database Connection Error: " . mysqli_connect_error());
    if (ob_get_level()) {
        ob_end_clean();
    }
    header("HTTP/1.1 500 Internal Server Error");
    exit("A database connection error occurred. Please contact the administrator.");
}

// Enforce standard utf8mb4 encoding for full unicode support and injection defense
if (!mysqli_set_charset($conn, 'utf8mb4')) {
    error_log("FMS Warning: Error setting character set utf8mb4: " . mysqli_error($conn));
}