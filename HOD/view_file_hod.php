<?php
ob_start(); // Capture any accidental output early

ini_set('display_errors', 0);
ini_set('zlib.output_compression', 'Off');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/connection.php';
require_once __DIR__ . '/../includes/dept_scope.php';

// Admin: full access. HOD: must have dept and not central; file must belong to that department.
if (isset($_SESSION['admin'])) {
    // proceed
} elseif (
    isset($_SESSION['h_username'])
    && $_SESSION['h_username'] !== 'central'
    && !empty($_SESSION['dept'])
) {
    // verified after path resolution below
} else {
    ob_end_clean();
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied. Please log in.');
}

// Ensure there's a file path present
if (!isset($_GET['file_path'])) {
    ob_end_clean();
    header("HTTP/1.1 400 Bad Request");
    exit("No file path provided.");
}

$rawPath = $_GET['file_path'];

// Security: only allow paths inside uploads/
if (strpos($rawPath, 'uploads/') === false && strpos($rawPath, 'uploads\\') === false) {
    ob_end_clean();
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied. Unauthorized file path.");
}

// Normalise and resolve path — this file lives in HOD/
$tempPath = str_replace('\\', '/', $rawPath);

$resolvedPath = null;
if (preg_match('/uploads\/.*/', $tempPath, $matches)) {
    $foundPath = $matches[0];

    // Try multiple depths relative to HOD/
    if (file_exists("../" . $foundPath)) {
        $resolvedPath = "../" . $foundPath;
    } elseif (file_exists($foundPath)) {
        $resolvedPath = $foundPath;
    } elseif (file_exists("../../" . $foundPath)) {
        $resolvedPath = "../../" . $foundPath;
    }
}

if ($resolvedPath === null) {
    ob_end_clean();
    header("Content-Type: text/plain");
    exit("ERROR: File not found. Original path: " . htmlspecialchars($rawPath));
}

if (!isset($_SESSION['admin'])) {
    $relForDb = str_replace('\\', '/', $foundPath);
    $ok = fms_verify_file_path_access(
        $conn,
        $relForDb,
        'HOD',
        (string) $_SESSION['h_username'],
        (string) $_SESSION['dept']
    );
    if (!$ok) {
        ob_end_clean();
        header('HTTP/1.1 403 Forbidden');
        exit('Access denied.');
    }
}

// Clear all buffered output before streaming file
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

