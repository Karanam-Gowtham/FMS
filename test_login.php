<?php
$c = new mysqli('localhost','root','','master');
$stmt = $c->prepare("
    SELECT u.email, u.user_id, r.role_id, r.role_name, d.dept_id, d.dept_name 
    FROM Users u
    JOIN User_Roles ur ON u.user_id = ur.user_id
    JOIN Roles r ON ur.role_id = r.role_id
    JOIN Dept d ON ur.dept_id = d.dept_id
    LIMIT 5
");
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
