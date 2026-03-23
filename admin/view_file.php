<?php
require_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/connection.php';

$logged = isset($_SESSION['username'])
    || isset($_SESSION['a_username'])
    || isset($_SESSION['j_username'])
    || isset($_SESSION['h_username'])
    || isset($_SESSION['admin'])
    || isset($_SESSION['c_cord'])
    || isset($_SESSION['c_username'])
    || isset($_SESSION['cri_username']);
if (!$logged) {
    http_response_code(403);
    exit('Access denied');
}


if (isset($_GET['file_path'])) {
    $filePath = $_GET['file_path'];
    if (preg_match('/uploads[\/\\\\].*/', $filePath, $matches)) {
        $filePath = "../" . $matches[0];
    }
} elseif (isset($_GET['id'])) {
    $fileId = intval($_GET['id']);
    $sql = "SELECT file_path FROM a_files WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $fileId);
    $stmt->execute();
    $result = $stmt->get_result();
    $file = $result->fetch_assoc();

    if ($file) {
        $filePath = $file['file_path'];
        if (preg_match('/uploads[\/\\\\].*/', $filePath, $matches)) {
            $filePath = "../" . $matches[0];
        }
    } else {
        die("Invalid file ID.");
    }
} else {
    die("Invalid request. Missing ID or file path.");
}

if (file_exists($filePath)) {
    // Serve the file
    $mimeType = mime_content_type($filePath);
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
    readfile($filePath);
    exit;
} else {
    echo "File does not exist.";
}


$conn->close();