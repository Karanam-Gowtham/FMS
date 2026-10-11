<?php
ob_start();
ini_set('display_errors', 0);
ini_set('zlib.output_compression', 'Off');

require_once __DIR__ . '/../core/bootstrap.php';
global $conn;

if (!isset($_GET['file_path'])) {
    while (ob_get_level()) { ob_end_clean(); }
    header("HTTP/1.1 400 Bad Request");
    exit("No file path provided.");
}

$rawPath = $_GET['file_path'];

// Security: reject null bytes and only allow paths containing uploads/
if (strpos($rawPath, "\0") !== false || 
    (strpos($rawPath, 'uploads/') === false && strpos($rawPath, 'uploads\\') === false)) {
    while (ob_get_level()) { ob_end_clean(); }
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied. Unauthorized file path.");
}

$tempPath = str_replace('\\', '/', $rawPath);

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
    $foundPath,
];

$allowedBaseDir = realpath(__DIR__ . "/../uploads");
$resolvedPath = null;

foreach ($candidatePaths as $cp) {
    if (file_exists($cp) && is_file($cp)) {
        $realCp = realpath($cp);
        if ($realCp !== false && $allowedBaseDir !== false && strpos($realCp, $allowedBaseDir) === 0) {
            $resolvedPath = $realCp;
            break;
        }
    }
}

if ($resolvedPath === null) {
    while (ob_get_level()) { ob_end_clean(); }
    header("HTTP/1.1 404 Not Found");
    exit("File not found.");
}

// Security: Verify authorization for file access
$projectRoot = realpath(__DIR__ . '/..');
$relPath = str_replace('\\', '/', substr($resolvedPath, strlen($projectRoot) + 1));
$altRelPath = '/' . $relPath;

// Check if file is associated with a document
$stmt = $conn->prepare("
    SELECT d.* FROM documents d 
    LEFT JOIN document_files df ON d.doc_id = df.doc_id 
    WHERE d.file_path = ? OR df.file_path = ? OR d.file_path = ? OR df.file_path = ?
    LIMIT 1
");
$stmt->bind_param("ssss", $relPath, $relPath, $altRelPath, $altRelPath);
$stmt->execute();
$res = $stmt->get_result();
$doc = $res->fetch_assoc();
$stmt->close();

if ($doc) {
    $is_public = false;
    if ($doc['status'] === 'accepted') {
        $public_types = [
            'journal', 'conference', 'patent', 'fdp_attended', 'fdp_organised', 
            'conf_organised', 'scholarship', 'placement', 'higher_ed', 'award', 
            'student_event', 'student_body', 'student_journal', 'student_conference'
        ];
        $type_stmt = $conn->prepare("SELECT type_code FROM document_types WHERE type_id = ?");
        $type_stmt->bind_param("i", $doc['type_id']);
        $type_stmt->execute();
        $trow = $type_stmt->get_result()->fetch_assoc();
        $type_stmt->close();

        if ($trow && in_array($trow['type_code'], $public_types, true)) {
            $is_public = true;
        }
    }

    if (!$is_public) {
        if (!auth_is_logged_in()) {
            while (ob_get_level()) { ob_end_clean(); }
            header("HTTP/1.1 401 Unauthorized");
            exit("Access denied. Authentication required.");
        }
        $auth = auth_context();
        if (!doc_can_view($conn, $auth, $doc)) {
            while (ob_get_level()) { ob_end_clean(); }
            header("HTTP/1.1 403 Forbidden");
            exit("Access denied. You do not have permission to view this file.");
        }
    }
} else {
    // Non-document files (e.g. accreditation drafts, internal reports)
    // Require active authenticated staff role
    if (!auth_is_logged_in()) {
        while (ob_get_level()) { ob_end_clean(); }
        header("HTTP/1.1 401 Unauthorized");
        exit("Access denied. Authentication required.");
    }
    $active_role_id = auth_active_role_id();
    if (!$active_role_id || $active_role_id === ROLE_STUDENT || !auth_has_role($active_role_id)) {
        while (ob_get_level()) { ob_end_clean(); }
        header("HTTP/1.1 403 Forbidden");
        exit("Access denied. You do not have permission to view this file.");
    }
}

// Clear output buffers and stream file
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
header("Cache-Control: private, must-revalidate, max-age=3600");

readfile($resolvedPath);
exit;
