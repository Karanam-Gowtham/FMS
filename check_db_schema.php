<?php
require 'includes/connection.php';
$res = $conn->query("SELECT id, username, branch, title, status FROM fdps_tab ORDER BY id DESC LIMIT 5");
if ($res) {
    while($row = $res->fetch_assoc()) {
        print_r($row);
    }
}
?>
