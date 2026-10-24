<?php

// Include config.php first for paths and DB constants
require_once __DIR__ . '/../config.php';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT);

if (!$conn) {
    error_log("FMS Database Connection Error: " . mysqli_connect_error());
    while (ob_get_level()) {
        ob_end_clean();
    }
    http_response_code(500);

    $is_api = function_exists('is_api_request')
        ? is_api_request()
        : ((isset($_GET['route']) && str_starts_with($_GET['route'], 'api/'))
           || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
           || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'));

    if ($is_api) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'status' => 'error',
            'error' => 'A database connection error occurred. Please contact the administrator.',
            'code' => 'DB_CONNECTION_ERROR'
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        exit(1);
    }

    $error_500 = __DIR__ . '/../public/500.php';
    if (file_exists($error_500)) {
        include $error_500;
    } else {
        echo "A database connection error occurred. Please contact the administrator.";
    }
    exit(1);
}

// Enforce standard utf8mb4 encoding for full unicode support and injection defense
if (!mysqli_set_charset($conn, 'utf8mb4')) {
    error_log("FMS Warning: Error setting character set utf8mb4: " . mysqli_error($conn));
}