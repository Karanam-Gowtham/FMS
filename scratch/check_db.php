<?php
require_once __DIR__ . '/../core/bootstrap.php';
global $conn;

echo "Workflows:\n";
$res = $conn->query("SELECT * FROM workflows");
while($row = $res->fetch_assoc()) { print_r($row); }

echo "\nDocument Types:\n";
$res = $conn->query("SELECT type_id, type_code, workflow_id FROM document_types");
while($row = $res->fetch_assoc()) { print_r($row); }

echo "\nSteps:\n";
$res = $conn->query("SELECT * FROM workflow_steps");
while($row = $res->fetch_assoc()) { print_r($row); }
