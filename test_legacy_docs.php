<?php
session_start();
include_once 'config.php';
include_once CONNECTION_PATH;
include_once INCLUDES_PATH . '/helpers.php';

$_SESSION['h_username'] = 'hod_cse';
$_SESSION['dept'] = 'CSE';
$_SESSION['roles'] = [['role_id' => 2, 'dept_id' => 1]];

echo "Testing HOD Pending count:\n";
$count = getPendingCount($conn, 1, $_SESSION['roles']);
echo "Count: $count\n";

echo "Testing Faculty Pending count:\n";
$_SESSION['username'] = 'faculty_1';
$_SESSION['roles'] = [['role_id' => 3, 'dept_id' => 1]];
$count = getPendingCount($conn, 2, $_SESSION['roles']);
echo "Count: $count\n";
