<?php
include("../includes/connection.php");
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Ensure the user is logged in
if (!isset($_SESSION['username']) && !isset($_SESSION['h_username']) && !isset($_SESSION['admin'])) {
    http_response_code(403);
    die("Access denied. Please log in to view files.");
}

// Ensure there's a file path present
if (!isset($_GET['file_path'])) {
    http_response_code(400);
    die("Invalid file path.");
}

$rawPath = urldecode($_GET['file_path']);

// Security: reject paths that don't reference uploads/
if (strpos($rawPath, 'uploads/') === false && strpos($rawPath, 'uploads\\') === false) {
    http_response_code(403);
    die("Access denied. Unauthorized file path.");
}

// Security: strip directory traversal sequences
$rawPath = str_replace('\\', '/', $rawPath);
$rawPath = str_replace('../', '', $rawPath);
$rawPath = str_replace('./', '', $rawPath);

// Extract the uploads-relative portion
$foundPath = '';
if (preg_match('/uploads\/.*/', $rawPath, $matches)) {
    $foundPath = $matches[0];
} else {
    http_response_code(403);
    die("Access denied. Invalid file path.");
}

// Resolve against known candidate directories
$projectRoot = realpath(__DIR__ . '/..');
$candidatePaths = [
    $projectRoot . '/' . $foundPath,
    $projectRoot . '/modules/faculty/' . $foundPath,
    $projectRoot . '/modules/dept_coordinator/' . $foundPath,
    $projectRoot . '/admin/' . $foundPath,
];

$resolvedPath = null;
foreach ($candidatePaths as $cp) {
    $real = realpath($cp);
    if ($real !== false && is_file($real)) {
        // Security: verify the resolved path is inside the project directory
        if (strpos($real, $projectRoot) === 0) {
            $resolvedPath = $real;
            break;
        }
    }
}

if ($resolvedPath === null) {
    http_response_code(404);
    die("File not found.");
}

// Serve the file
$mimeType = mime_content_type($resolvedPath);
if (!$mimeType) {
    $mimeType = 'application/octet-stream';
}

$disposition = (strpos($mimeType, 'pdf') !== false || strpos($mimeType, 'image') !== false) ? 'inline' : 'attachment';

header('Content-Type: ' . $mimeType);
header('Content-Disposition: ' . $disposition . '; filename="' . basename($resolvedPath) . '"');
header('Content-Length: ' . filesize($resolvedPath));

while (ob_get_level()) { ob_end_clean(); }
readfile($resolvedPath);
exit;
?>
