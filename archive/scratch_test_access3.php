<?php
require 'includes/connection.php';
require 'includes/dept_scope.php';

$table = 'fdps_tab';
$pk = 'id';
$fileId = 1;
$dept = 'CSE';
$branch_legacy = 'CSE';
$deptCol = 'branch';
$ownerCol = 'username';

$sql = "SELECT t.`$pk` FROM `$table` t WHERE t.`$pk` = ? AND (t.`$deptCol` = ? OR t.`$deptCol` = ? OR EXISTS (
    SELECT 1 FROM users u 
    JOIN user_roles ur ON u.user_id = ur.user_id 
    JOIN departments d ON ur.dept_id = d.dept_id 
    WHERE u.full_name COLLATE utf8mb4_unicode_ci = t.`$ownerCol` COLLATE utf8mb4_unicode_ci AND (d.dept_name = ? OR d.dept_name = ?)
)) LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param('issss', $fileId, $dept, $branch_legacy, $dept, $branch_legacy);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

print_r($row);
