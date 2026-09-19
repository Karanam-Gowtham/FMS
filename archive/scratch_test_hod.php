<?php
include "HOD/header_hod.php";
include "includes/connection.php";

session_start();
$_SESSION['dept'] = 'CSE';

$stmt = $conn->prepare("SELECT dept_id FROM departments WHERE dept_name = ?");
if (!$stmt) die("Prepare failed: " . $conn->error);
echo "Prepare successful.";
