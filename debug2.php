<?php
require_once 'includes/connection.php';
$res = $conn->query('SELECT certificate FROM fdps_tab WHERE certificate IS NOT NULL LIMIT 1');
if ($row = $res->fetch_assoc()) {
    $dbPath = $row['certificate'];
}

echo 'DB Path: ' . $dbPath . PHP_EOL;
chdir('HOD'); // Simulate HOD directory
echo 'Current Dir: ' . getcwd() . PHP_EOL;

if (preg_match('/uploads[\/\\\\].*/', $dbPath, $matches)) {
    $relativeToHod = '../' . $matches[0];
    echo 'Extracted match: ' . $matches[0] . PHP_EOL;
    echo 'Relative to HOD: ' . $relativeToHod . PHP_EOL;

    // Normalize path to match what's checked
    if (file_exists($relativeToHod)) {
        echo 'FILE EXISTS!' . PHP_EOL;
    } else {
        echo 'FILE DOES NOT EXIST!' . PHP_EOL;
    }
}
