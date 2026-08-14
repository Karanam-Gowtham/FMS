<?php
require 'includes/connection.php';

$query = "
    SELECT u.user_id, u.full_name, u.email, GROUP_CONCAT(CONCAT(r.role_name, ' (', d.dept_name, ')') SEPARATOR ', ') as roles
    FROM users u
    LEFT JOIN user_roles ur ON u.user_id = ur.user_id
    LEFT JOIN roles r ON ur.role_id = r.role_id
    LEFT JOIN dept d ON ur.dept_id = d.dept_id
    GROUP BY u.user_id
    ORDER BY u.user_id ASC
";

$result = $conn->query($query);
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

file_put_contents('users_report2.json', json_encode($users, JSON_PRETTY_PRINT));
$conn->close();
?>
