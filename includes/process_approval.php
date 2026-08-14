<?php
session_start();
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/helpers.php';

if (!isset($_SESSION['user_id']) && !isset($_SESSION['h_username'])) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$action = $_POST['action'] ?? '';
$doc_id = (int)($_POST['document_id'] ?? 0);
$source_table = $_POST['source_table'] ?? 'Documents';
$reason = $_POST['reason'] ?? '';

if (!$doc_id || !in_array($action, ['Accept', 'Reject'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid request']));
}

if ($source_table === 'Documents') {
    // Modern documents
    $new_status = ($action === 'Accept') ? 'Approved' : 'Rejected';
    $stmt = $conn->prepare("UPDATE Documents SET status = ? WHERE document_id = ?");
    $stmt->bind_param("si", $new_status, $doc_id);
    $success = $stmt->execute();
    $stmt->close();
} else {
    // Legacy documents
    $new_status = ($action === 'Accept') ? 'Accepted' : 'Rejected';
    // Validate table name
    $valid_tables = ['patents_table', 'published_tab', 'conference_tab', 'fdps_tab', 'conf_org_tab', 'fdps_org_tab', 'dept_files', 's_journal_tab', 's_conference_tab', 's_bodies', 's_events'];
    if (in_array($source_table, $valid_tables)) {
        $stmt = $conn->prepare("UPDATE `$source_table` SET status = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $new_status, $doc_id);
            $success = $stmt->execute();
            $stmt->close();
        } else {
            $success = false;
        }
    } else {
        $success = false;
    }
}

if ($success) {
    // Redirect back to the referrer
    $ref = $_SERVER['HTTP_REFERER'] ?? '../dashboard.php';
    header("Location: " . $ref);
    exit;
} else {
    echo "Error processing request.";
}
