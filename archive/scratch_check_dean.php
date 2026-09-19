<?php
require 'includes/connection.php';
$res = $conn->query("SELECT * FROM users WHERE full_name = 'rnd_dean' OR email LIKE '%rnd%' OR full_name LIKE '%dean%'");
if (!$res) die($conn->error);
while ($row = $res->fetch_assoc()) {
    print_r($row);
    $uid = $row['user_id'];
    $rres = $conn->query("SELECT * FROM user_roles WHERE user_id = $uid");
    while ($rrow = $rres->fetch_assoc()) {
        print_r($rrow);
    }
}
