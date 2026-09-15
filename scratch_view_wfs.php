<?php
require 'includes/connection.php';
$res = $conn->query("SELECT * FROM workflow_steps");
if (!$res) die($conn->error);
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
