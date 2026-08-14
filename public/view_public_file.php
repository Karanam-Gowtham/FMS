<?php
ob_start();
ini_set('display_errors', 0);
ini_set('zlib.output_compression', 'Off');

if (!isset($_GET['file_path'])) {
    while (ob_get_level()) { ob_end_clean(); }
    header("HTTP/1.1 400 Bad Request");
    exit("No file path provided.");
}

$rawPath = $_GET['file_path'];

// Security: only allow paths containing uploads/
if (strpos($rawPath, 'uploads/') === false && strpos($rawPath, 'uploads\\') === false) {
    while (ob_get_level()) { ob_end_clean(); }
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied. Unauthorized file path.");
}

$tempPath = str_replace('\\', '/', $rawPath);

$resolvedPath = null;
$foundPath = '';
if (preg_match('/uploads\/.*/i', $tempPath, $matches)) {
    $foundPath = $matches[0];
} else {
    $foundPath = 'uploads/' . ltrim($tempPath, '/');
}

$candidatePaths = [
    __DIR__ . "/../" . $foundPath,
    __DIR__ . "/../modules/faculty/" . $foundPath,
    __DIR__ . "/../modules/dept_coordinator/" . $foundPath,
    __DIR__ . "/../admin/" . $foundPath,
    __DIR__ . "/../../" . $foundPath,
    __DIR__ . "/../../modules/faculty/" . $foundPath,
    __DIR__ . "/../../modules/dept_coordinator/" . $foundPath,
    __DIR__ . "/../../admin/" . $foundPath,
    $foundPath,
    "../" . $foundPath,
    "../../" . $foundPath
];

foreach ($candidatePaths as $cp) {
    if (file_exists($cp) && is_file($cp)) {
        $resolvedPath = $cp;
        break;
    }
}

if ($resolvedPath === null) {
    while (ob_get_level()) { ob_end_clean(); }
    header("Content-Type: text/plain");
    exit("ERROR: File not found. Path: " . htmlspecialchars($rawPath));
}

// Clear output buffers and stream file inline
while (ob_get_level()) {
    ob_end_clean();
}

$mime = @mime_content_type($resolvedPath);
if (!$mime) {
    $mime = 'application/octet-stream';
}

$disposition = (strpos($mime, 'pdf') !== false || strpos($mime, 'image') !== false) ? 'inline' : 'attachment';

header("Content-Type: $mime");
header("Content-Disposition: $disposition; filename=\"" . basename($resolvedPath) . "\"");
header("Content-Length: " . filesize($resolvedPath));
header("Cache-Control: public, must-revalidate, max-age=3600");

readfile($resolvedPath);
exit;
?>
