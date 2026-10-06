<?php
require_once __DIR__ . '/../core/bootstrap.php';
global $conn;
$res = $conn->query("SELECT * FROM workflow_steps WHERE workflow_id = 5");
while($row = $res->fetch_assoc()) { print_r($row); }
