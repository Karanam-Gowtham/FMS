<?php
require_once __DIR__ . '/includes/connection.php';
$c = $conn;
$r = mysqli_query($c, 'SHOW TABLES');
while($t = mysqli_fetch_row($r)) { echo $t[0] . PHP_EOL; }

// Check for RBAC tables
echo "\n--- RBAC Tables Check ---\n";
$rbac_tables = ['Users','Roles','User_Roles','Dept','Approval_Flow','Document_Types','Documents','Document_Actions','Academic_Years'];
foreach ($rbac_tables as $tbl) {
    $res = mysqli_query($c, "SHOW TABLES LIKE '$tbl'");
    echo "$tbl: " . (mysqli_num_rows($res) > 0 ? "EXISTS" : "MISSING") . "\n";
}

// Check legacy auth tables
echo "\n--- Legacy Auth Tables Check ---\n";
$legacy_tables = ['reg_tab','login_pg','reg_hod','reg_dept_cord','admin_login','admin_reg','reg_central_cord','reg_cri_cord','reg_jr_assistant','approval_roles','document_role_flow','role_flow_logs'];
foreach ($legacy_tables as $tbl) {
    $res = mysqli_query($c, "SHOW TABLES LIKE '$tbl'");
    echo "$tbl: " . (mysqli_num_rows($res) > 0 ? "EXISTS" : "MISSING") . "\n";
}
