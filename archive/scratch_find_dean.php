<?php
require 'includes/connection.php';
$res = $conn->query("SELECT u.* FROM users u JOIN user_roles ur ON u.user_id = ur.user_id WHERE ur.role_id = 8");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
