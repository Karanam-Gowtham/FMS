<?php
require_once __DIR__ . '/../core/bootstrap.php';
global $conn;
$res = $conn->query("SELECT * FROM document_files ORDER BY file_id DESC LIMIT 1");
$row = $res->fetch_assoc();
echo "file_path: " . $row['file_path'] . "\n";
echo "real_path: " . __DIR__ . '/../' . $row['file_path'] . "\n";
echo "file_exists: " . (file_exists(__DIR__ . '/../' . $row['file_path']) ? 'YES' : 'NO') . "\n";
