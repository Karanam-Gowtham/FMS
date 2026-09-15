<?php
require 'includes/connection.php';
require 'core/constants.php';
require 'core/auth.php';
require 'core/document_service.php';

$auth = [
    'user_id' => 5,
    'full_name' => 'Dean',
    'email' => 'dean@gmrit.edu.in',
    'roles' => [],
    'active_role' => [
        'role_id' => ROLE_RND_DEAN,
        'role_name' => 'RnD Dean',
        'dept_id' => 0,
        'dept_name' => 'All'
    ]
];

$res = doc_list_pending_for_user($conn, $auth, 10, 0);
echo "Pending total: " . $res['total'] . "\n";
print_r($res['rows']);
