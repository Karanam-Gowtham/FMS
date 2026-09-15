<?php
include_once '../../config.php';
include_once CONNECTION_PATH;
include_once INCLUDES_PATH . '/session.php';
requireLogin();

header('Content-Type: application/json');

$role_id = $_SESSION['role_id'] ?? 0;
if (!$role_id && isset($_SESSION['roles'])) {
    foreach ($_SESSION['roles'] as $r) {
        if ($r['role_id'] == ROLE_RND_DEAN) { $role_id = ROLE_RND_DEAN; break; }
    }
}
if ($role_id != 8) { // 8 = ROLE_RND_DEAN
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

$table = $_POST['table'] ?? '';
$id = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';
$reason = $_POST['reason'] ?? '';

$allowed_tables = ['published_tab', 'conference_tab', 'patents_table'];
if (!in_array($table, $allowed_tables) || !$id || !in_array($action, ['accept', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

if ($action === 'accept') {
    $new_status = 'Approved by Dean';
    $reason = '';
} else {
    $new_status = 'Rejected by Dean';
}

$stmt = $conn->prepare("UPDATE $table SET status = ?, rejection_reason = ? WHERE id = ?");
$stmt->bind_param("ssi", $new_status, $reason, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
