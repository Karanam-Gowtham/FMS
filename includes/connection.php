<?php

// Include config.php first for paths
require_once __DIR__ . '/../config.php';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if (!$conn) {
    die("Connection failed:" . mysqli_connect_error());
}