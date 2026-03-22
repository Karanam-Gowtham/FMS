<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug script to check view_file_hod.php logic
require 'includes/connection.php';
$res = $conn->query('SELECT certificate FROM fdps_tab WHERE certificate IS NOT NULL LIMIT 1');
if ($row = $res->fetch_assoc()) {
    $filePath = $row['certificate'];
    echo "Original: " . $filePath . "\n";
    if (preg_match('/uploads[\/\\\\].*/', $filePath, $matches)) {
        $testPath = 'HOD/../' . $matches[0];
        echo "Trying: " . $testPath . "\n";
        if (file_exists($testPath)) {
            echo "SUCCESS: File exists!\n";
            echo "Mime: " . mime_content_type($testPath) . "\n";
        } else {
            echo "FAIL: File not found.\n";
        }
    } else {
        echo "No regex match.\n";
    }
}
