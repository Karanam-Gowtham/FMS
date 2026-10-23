<?php
ob_start();
ini_set('display_errors', 0);
ini_set('zlib.output_compression', 'Off');

require_once __DIR__ . '/../core/bootstrap.php';
global $conn;

if (!isset($_GET['file_path']) || trim((string)$_GET['file_path']) === '') {
    while (ob_get_level()) { ob_end_clean(); }
    http_response_code(400);
    $error_message = "No valid file path parameter was provided in the request.";
    if (file_exists(__DIR__ . '/404.php')) {
        include __DIR__ . '/404.php';
    } else {
        echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8');
    }
    exit;
}

$rawPath = (string)$_GET['file_path'];

// Security: reject null bytes and only allow paths containing uploads/
if (strpos($rawPath, "\0") !== false || 
    (strpos($rawPath, 'uploads/') === false && strpos($rawPath, 'uploads\\') === false)) {
    while (ob_get_level()) { ob_end_clean(); }
    http_response_code(403);
    $error_message = "Access denied. Unauthorized file path traversal attempt detected.";
    if (file_exists(__DIR__ . '/403.php')) {
        include __DIR__ . '/403.php';
    } else {
        echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8');
    }
    exit;
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
$canonicalBase = $allowedBaseDir ? (rtrim($allowedBaseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR) : null;
$resolvedPath = null;

foreach ($candidatePaths as $cp) {
    if (file_exists($cp) && is_file($cp)) {
        $realCp = realpath($cp);
        if ($realCp !== false && $canonicalBase !== null && stripos($realCp, $canonicalBase) === 0) {
            $resolvedPath = $realCp;
            break;
        }
    }
}

if ($resolvedPath === null) {
    while (ob_get_level()) { ob_end_clean(); }
    http_response_code(404);
    $error_message = "The requested physical file was not found on the server.";
    if (file_exists(__DIR__ . '/404.php')) {
        include __DIR__ . '/404.php';
    } else {
        echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8');
    }
    exit;
}

// Security: Verify authorization for file access
$projectRoot = realpath(__DIR__ . '/..');
$relPath = str_replace('\\', '/', substr($resolvedPath, strlen($projectRoot) + 1));
$altRelPath = '/' . $relPath;
$bsRelPath = str_replace('/', '\\', $relPath);
$altBsRelPath = '\\' . $bsRelPath;

// Check if file is associated with a document
$stmt = $conn->prepare("
    SELECT d.* FROM documents d 
    LEFT JOIN document_files df ON d.doc_id = df.doc_id 
    WHERE d.file_path = ? OR df.file_path = ? OR d.file_path = ? OR df.file_path = ? OR d.file_path = ? OR df.file_path = ? OR d.file_path = ? OR df.file_path = ?
    LIMIT 1
");
if (!$stmt) {
    while (ob_get_level()) { ob_end_clean(); }
    http_response_code(500);
    $error_message = "Database query error while validating document file permissions.";
    if (file_exists(__DIR__ . '/500.php')) {
        include __DIR__ . '/500.php';
    } else {
        echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8');
    }
    exit;
}
$stmt->bind_param("ssssssss", $relPath, $relPath, $altRelPath, $altRelPath, $bsRelPath, $bsRelPath, $altBsRelPath, $altBsRelPath);
$stmt->execute();
$res = $stmt->get_result();
$doc = $res ? $res->fetch_assoc() : null;
$stmt->close();

if ($doc) {
    $is_public = false;
    if ($doc['status'] === 'accepted') {
        $public_types = [
            'journal', 'conference', 'patent', 'fdp', 'fdp_attended', 'fdp_organised', 
            'conf_organised', 'scholarship', 'placement', 'higher_ed', 'award', 
            'student_event', 'student_body', 'student_journal', 'student_conference',
            'student_activity_file', 'dept_file'
        ];
        $type_stmt = $conn->prepare("SELECT type_code FROM document_types WHERE type_id = ?");
        if ($type_stmt) {
            $type_stmt->bind_param("i", $doc['type_id']);
            $type_stmt->execute();
            $tres = $type_stmt->get_result();
            $trow = $tres ? $tres->fetch_assoc() : null;
            $type_stmt->close();

            if ($trow && in_array($trow['type_code'], $public_types, true)) {
                $is_public = true;
            }
        }
    }

    if (!$is_public) {
        if (!auth_is_logged_in()) {
            while (ob_get_level()) { ob_end_clean(); }
            header("Location: " . BASE_URL . "/public/index.php?route=auth/login");
            exit;
        }
        $auth = auth_context();
        if (!doc_can_view($conn, $auth, $doc)) {
            while (ob_get_level()) { ob_end_clean(); }
            http_response_code(403);
            $error_message = "Access denied. You do not have permission to view this file.";
            if (file_exists(__DIR__ . '/403.php')) {
                include __DIR__ . '/403.php';
            } else {
                echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8');
            }
            exit;
        }
    }
} else {
    // Non-document files (e.g. accreditation drafts, internal reports)
    // Require active authenticated staff role
    if (!auth_is_logged_in()) {
        while (ob_get_level()) { ob_end_clean(); }
        header("Location: " . BASE_URL . "/public/index.php?route=auth/login");
        exit;
    }
    $active_role_id = auth_active_role_id();
    if (!$active_role_id || $active_role_id === ROLE_STUDENT || !auth_has_role($active_role_id)) {
        while (ob_get_level()) { ob_end_clean(); }
        http_response_code(403);
        $error_message = "Access denied. Your active role does not have permission to view internal files.";
        if (file_exists(__DIR__ . '/403.php')) {
            include __DIR__ . '/403.php';
        } else {
            echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8');
        }
        exit;
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
$safe_filename = str_replace(['"', "\r", "\n"], '', basename($resolvedPath));

header("Content-Type: $mime");
header("Content-Disposition: $disposition; filename=\"" . $safe_filename . "\"");
header("Content-Length: " . filesize($resolvedPath));
header("X-Content-Type-Options: nosniff");
header("Cache-Control: private, must-revalidate, max-age=3600");

readfile($resolvedPath);
exit;
