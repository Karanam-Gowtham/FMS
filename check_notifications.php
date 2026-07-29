<?php
include_once 'config.php';
include_once CONNECTION_PATH;
include_once INCLUDES_PATH . '/helpers.php';

if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['count' => 0]);
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$roles = $_SESSION['roles'] ?? [];

$count = getPendingCount($conn, $user_id, $roles);

// Email notification logic can be added here if needed, utilizing the same throttle.

header('Content-Type: application/json');
echo json_encode(['count' => $count]);
exit();
?>
