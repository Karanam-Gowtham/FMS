<?php
/**
 * FMS File Download Handler
 *
 * Secure file download. Validates that the requesting user has permission
 * to access the document that owns the file.
 *
 * URL: pages/documents/download.php?id={file_id}
 */
require_once __DIR__ . '/../../core/bootstrap.php';

require_login();
$auth = auth_context();

$file_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($file_id <= 0) {
    http_response_code(400);
    echo 'Invalid file ID.';
    exit;
}

// Look up the file to get its parent document
$stmt = $conn->prepare("SELECT doc_id FROM document_files WHERE file_id = ?");
$stmt->bind_param('i', $file_id);
$stmt->execute();
$file_row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$file_row) {
    http_response_code(404);
    echo 'File not found.';
    exit;
}

// Load the parent document to check authorization
$document = doc_get($conn, (int)$file_row['doc_id']);
if (!$document) {
    http_response_code(404);
    echo 'Document not found.';
    exit;
}

// Authorization check: same logic as view.php
$can_view = false;
$user_id = (int)$auth['user_id'];

if ((int)$document['uploaded_by'] === $user_id) {
    $can_view = true;
}

if (!$can_view) {
    foreach ($auth['roles'] as $role) {
        $rid = (int)$role['role_id'];
        $rdept = (int)$role['dept_id'];

        if (in_array($rid, [ROLE_ADMIN, ROLE_RND_DEAN, ROLE_IQAC], true)) {
            $can_view = true;
            break;
        }
        if (in_array($rid, [ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_JUNIOR_ASSISTANT], true)
            && $rdept === (int)$document['dept_id']) {
            $can_view = true;
            break;
        }
        if ($rid === ROLE_CENTRAL_COORDINATOR && $document['category'] === 'central') {
            $can_view = true;
            break;
        }
    }
}

if (!$can_view) {
    http_response_code(403);
    echo 'You do not have permission to download this file.';
    exit;
}

// Determine if inline viewing was requested
$inline = isset($_GET['inline']) && $_GET['inline'] === '1';

// Stream the file
file_download($conn, $file_id, $inline);
