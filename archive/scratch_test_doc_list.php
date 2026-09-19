<?php
require 'includes/connection.php';
require 'core/auth.php';

// I will mock the auth context to test doc_list_pending_for_user
$_SESSION[SESSION_AUTH_KEY] = [
    'user_id' => 5,
    'full_name' => 'Dean',
    'email' => 'dean@gmrit.edu.in',
    'roles' => [],
    'active_role' => [
        'role_id' => ROLE_RND_DEAN,
        'role_name' => 'RnD Dean',
        'dept_id' => 0,
        'dept_name' => 'All'
    ],
    'login_time' => time(),
];

// include the document service
require 'core/document_service.php';

echo "Testing doc_list...\n";
$res1 = doc_list($conn, ['category' => 'research']);
echo "Total doc_list: " . $res1['total'] . "\n";

echo "Testing doc_list_pending_for_user...\n";
$res2 = doc_list_pending_for_user($conn, auth_context());
echo "Total pending: " . $res2['total'] . "\n";

