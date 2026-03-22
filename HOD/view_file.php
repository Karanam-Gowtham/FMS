<?php
ob_start();

ini_set('display_errors', '0');
ini_set('zlib.output_compression', 'Off');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/connection.php';

// Faculty-only (same policy as original script)
if (!isset($_SESSION['username'])) {
    ob_end_clean();
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied. Please log in.');
}

if (!isset($_GET['file_path'])) {
    ob_end_clean();
    header('HTTP/1.1 400 Bad Request');
    exit('No file path provided.');
}

$rawPath = $_GET['file_path'];

if (strpos($rawPath, 'uploads/') === false && strpos($rawPath, 'uploads\\') === false) {
    ob_end_clean();
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied. Unauthorized file path.');
}

$tempPath = str_replace('\\', '/', $rawPath);

$resolvedPath = null;
if (preg_match('/uploads\/.*/', $tempPath, $matches)) {
    $foundPath = $matches[0];

    if (file_exists('../' . $foundPath)) {
        $resolvedPath = '../' . $foundPath;
    } elseif (file_exists($foundPath)) {
        $resolvedPath = $foundPath;
    } elseif (file_exists('../../' . $foundPath)) {
        $resolvedPath = '../../' . $foundPath;
    }
}

if ($resolvedPath === null) {
    ob_end_clean();
    header('Content-Type: text/plain');
    exit('ERROR: File not found. Original path: ' . htmlspecialchars($rawPath, ENT_QUOTES, 'UTF-8'));
}

while (ob_get_level()) {
    ob_end_clean();
}

$mime = @mime_content_type($resolvedPath);
if (!$mime) {
    $mime = 'application/octet-stream';
}

$disposition = (strpos($mime, 'pdf') !== false || strpos($mime, 'image') !== false) ? 'inline' : 'attachment';

header('Content-Type: ' . $mime);
header('Content-Disposition: ' . $disposition . '; filename="' . basename($resolvedPath) . '"');
header('Content-Length: ' . filesize($resolvedPath));
header('Cache-Control: public, must-revalidate, max-age=3600');

readfile($resolvedPath);
exit;
