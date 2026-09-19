<?php
require 'includes/connection.php';
require 'includes/dept_scope.php';

$conn->query("SET profiling = 1;");

$id = 1;
$role = 'Department Coordinator';
$user_id = 'dc_cse';
$dept = 'CSE';

echo "Result: " . (fms_dashboard_row_in_scope($conn, 'fdps_tab', $id, $role, $user_id, $dept) ? 'YES' : 'NO') . "\n";

$res = $conn->query("SHOW PROFILES;");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
