<?php
require 'includes/connection.php';
$res = $conn->query("UPDATE user_roles SET role_id = 8 WHERE user_id = 49 AND role_id = 6");
echo "Update result: " . ($res ? 'success' : $conn->error) . "\n";
