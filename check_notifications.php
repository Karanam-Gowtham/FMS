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

// Fallback for legacy hybrid logins where $_SESSION['roles'] is not set
if (empty($roles)) {
    $inferred_roles = [];
    if (isset($_SESSION['username'])) {
        $inferred_roles[] = ['role_id' => ROLE_FACULTY, 'dept_id' => 0]; // Faculty
    }
    if (isset($_SESSION['h_username'])) {
        // Find dept_id if possible
        $dept_id = 0;
        if (isset($_SESSION['dept'])) {
            $stmt = $conn->prepare("SELECT dept_id FROM Dept WHERE dept_name = ?");
            $stmt->bind_param("s", $_SESSION['dept']);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) $dept_id = $row['dept_id'];
            $stmt->close();
        }
        $inferred_roles[] = ['role_id' => ROLE_HOD, 'dept_id' => $dept_id]; 
    }
    if (isset($_SESSION['a_username'])) {
        $inferred_roles[] = ['role_id' => ROLE_COORDINATOR, 'dept_id' => 0]; 
    }
    if (isset($_SESSION['c_username'])) {
        $inferred_roles[] = ['role_id' => ROLE_CENTRAL_COORDINATOR, 'dept_id' => 0]; 
    }
    if (isset($_SESSION['admin'])) {
        $inferred_roles[] = ['role_id' => ROLE_ADMIN, 'dept_id' => 0]; 
    }
    $roles = $inferred_roles;
}

$count = getPendingCount($conn, $user_id, $roles);

// Email notification logic can be added here if needed, utilizing the same throttle.

header('Content-Type: application/json');
echo json_encode(['count' => $count]);
exit();
?>
