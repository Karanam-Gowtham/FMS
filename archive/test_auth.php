<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['identifier'] = 'cse-hod@gmrit.edu.in';
$_POST['password'] = '123';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// Override header function to prevent actual redirect, just capture it
$redirect = '';
function header_override($str) {
    global $redirect;
    if (strpos($str, 'Location:') === 0) {
        $redirect = $str;
    }
}
// Using runkit or namespace? Let's just include the bootstrap and auth, and simulate the auth flow directly to avoid header() issues.
require_once __DIR__ . '/core/bootstrap.php';
require_once __DIR__ . '/core/legacy_bridge.php';

echo "Testing HOD login:\n";
$user = auth_authenticate($conn, 'cse-hod@gmrit.edu.in', '123');
if ($user) {
    echo "Auth successful for: " . $user['full_name'] . "\n";
    echo "Roles count: " . count($user['roles']) . "\n";
    auth_create_session($user, $user['roles'][0]);
    legacy_bridge_sync();
    
    echo "Canonical Session:\n";
    print_r($_SESSION['_fms_auth']['active_role']);
    
    echo "Legacy Session Keys Set:\n";
    echo "h_username: " . ($_SESSION['h_username'] ?? 'NOT SET') . "\n";
    echo "dept: " . ($_SESSION['dept'] ?? 'NOT SET') . "\n";
    
    echo "Landing URL: " . get_role_landing_url($user['roles'][0]) . "\n";
} else {
    echo "Auth failed.\n";
}

echo "\n--------------------------------\nTesting Multi-role User login (if any):\n";
$stmt = $conn->query("SELECT user_id, email, password FROM users LIMIT 10");
while($row = $stmt->fetch_assoc()) {
    $roles = auth_load_user_roles($conn, $row['user_id']);
    if (count($roles) > 1) {
        echo "Found multi-role user: {$row['email']}\n";
        print_r($roles);
        break;
    }
}

echo "\n--------------------------------\nTesting Legacy Bridge for Central Coordinator:\n";
$user = auth_authenticate($conn, 'central_cord@gmrit.edu.in', '123');
if ($user) {
    auth_create_session($user, $user['roles'][0]);
    legacy_bridge_sync();
    echo "c_username: " . ($_SESSION['c_username'] ?? 'NOT SET') . "\n";
    echo "c_cord: " . ($_SESSION['c_cord'] ?? 'NOT SET') . "\n";
    echo "Landing URL: " . get_role_landing_url($user['roles'][0]) . "\n";
}

