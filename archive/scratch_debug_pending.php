<?php
require 'includes/connection.php';
require 'core/constants.php';
require 'core/auth.php';

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

$role_sql = "ws.responsible_role_id = " . (int)$auth['active_role']['role_id'];
if ($auth['active_role']['dept_id'] > 0) {
    $role_sql .= " AND d.dept_id = " . (int)$auth['active_role']['dept_id'];
}

$count_sql = "SELECT COUNT(*) as total FROM documents d
              JOIN document_types dt ON dt.type_id = d.type_id
              JOIN workflow_steps ws ON ws.step_id = d.current_step
              WHERE d.status = 'pending' AND $role_sql";

echo $count_sql . "\n";
$res = $conn->query($count_sql);
if (!$res) echo $conn->error;
print_r($res->fetch_assoc());
