<?php
ob_start(); // Capture any accidental output early

ini_set('display_errors', 0);
ini_set('zlib.output_compression', 'Off');

// Start session without including connection.php (avoids output corruption)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is logged in (check all possible session roles)
if (
    !isset($_SESSION['username']) &&
    !isset($_SESSION['a_username']) &&
    !isset($_SESSION['j_username']) &&
    !isset($_SESSION['h_username']) &&
    !isset($_SESSION['admin'])
) {
    ob_end_clean();
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied. Please log in.");
}

// Ensure there's a file path present
if (!isset($_GET['file_path'])) {
    ob_end_clean();
    header("HTTP/1.1 400 Bad Request");
    exit("No file path provided.");
}

$filePath = $_GET['file_path'];

// Security: only allow paths that include uploads/
if (strpos($filePath, 'uploads/') === false && strpos($filePath, 'uploads\\') === false) {
    ob_end_clean();
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied. Unauthorized file path.");
}

// Fix and normalise the path
$tempPath = str_replace('\\', '/', $filePath);

$resolvedPath = null;
if (preg_match('/uploads\/.*/', $tempPath, $matches)) {
    $foundPath = $matches[0];

    // Try multiple depths — this file lives in modules/common/
    if (file_exists("../../" . $foundPath)) {
        $resolvedPath = "../../" . $foundPath;
    } elseif (file_exists("../" . $foundPath)) {
        $resolvedPath = "../" . $foundPath;
    } elseif (file_exists($foundPath)) {
        $resolvedPath = $foundPath;
    }
}

if ($resolvedPath === null) {
    ob_end_clean();
    header("Content-Type: text/plain");
    exit("ERROR: File not found. Original path: " . htmlspecialchars($filePath));
}

require_once __DIR__ . '/../../includes/connection.php';
require_once __DIR__ . '/../../includes/dept_scope.php';

if (!isset($_SESSION['admin'])) {
    $ctx = fms_session_role_context($conn);
    if (
        $ctx === null
        || !fms_verify_file_path_access($conn, $filePath, $ctx['role'], $ctx['user_id'], $ctx['dept'])
    ) {
        ob_end_clean();
        header('HTTP/1.1 403 Forbidden');
        exit('Access denied.');
    }
}

// All good — clear any buffered output and stream the file
while (ob_get_level()) {
    ob_end_clean();
}

$mime = @mime_content_type($resolvedPath);
if (!$mime) {
    $mime = 'application/octet-stream';
}

// Serve inline for PDFs and images, attachment for everything else
$disposition = (strpos($mime, 'pdf') !== false || strpos($mime, 'image') !== false) ? 'inline' : 'attachment';

header("Content-Type: $mime");
header("Content-Disposition: $disposition; filename=\"" . basename($resolvedPath) . "\"");
header("Content-Length: " . filesize($resolvedPath));
header("Cache-Control: public, must-revalidate, max-age=3600");

readfile($resolvedPath);
exit;
