<?php
/**
 * FMS Document Approval Handler
 *
 * POST-only endpoint that processes approve/reject/resubmit actions.
 * Uses the workflow engine to validate authorization and execute transitions.
 * No hard-coded role names or step sequences.
 *
 * URL: pages/documents/approve.php (POST only)
 */
require_once __DIR__ . '/../../core/bootstrap.php';

// POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed.';
    exit;
}

// Require authentication
require_login();
$auth = auth_context();

// CSRF validation — csrfValidate() dies on failure, returns void on success
csrfValidate();

$doc_id     = (int)($_POST['doc_id'] ?? 0);
$action_key = trim($_POST['action'] ?? '');
$remarks    = trim($_POST['remarks'] ?? '');

// Validate inputs
if ($doc_id <= 0) {
    http_response_code(400);
    echo 'Invalid document ID.';
    exit;
}

$valid_actions = ['approve', 'reject', 'resubmit'];
if (!in_array($action_key, $valid_actions, true)) {
    http_response_code(400);
    echo 'Invalid action.';
    exit;
}

// Rejection requires remarks
if ($action_key === 'reject' && $remarks === '') {
    // Redirect back with error
    $ref = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/pages/documents/view.php?id=' . $doc_id;
    header('Location: ' . $ref . (strpos($ref, '?') !== false ? '&' : '?') . 'error=rejection_remarks_required');
    exit;
}

// Load document to check authorization
$document = doc_get($conn, $doc_id);
if (!$document) {
    http_response_code(404);
    echo 'Document not found.';
    exit;
}

// Check if user is allowed to perform this action
$allowed = wf_get_allowed_actions($conn, $document, $auth);
if (!in_array($action_key, $allowed, true)) {
    http_response_code(403);
    echo 'You are not authorized to perform this action on this document.';
    exit;
}

// Execute the action via the workflow engine
$result = wf_execute_action($conn, $doc_id, (int)$auth['user_id'], $action_key, $remarks);

if ($result['success']) {
    // Redirect back to the document view
    $redirect = BASE_URL . '/pages/documents/view.php?id=' . $doc_id . '&action_result=success&action=' . urlencode($action_key);
    header('Location: ' . $redirect);
    exit;
} else {
    http_response_code(500);
    echo 'Error: ' . htmlspecialchars($result['error']);
    exit;
}
