<?php
require_once __DIR__ . '/../core/bootstrap.php';
global $conn;
$sql = file_get_contents(__DIR__ . '/update_workflows.sql');
$conn->multi_query($sql);
do {
    if ($res = $conn->store_result()) {
        $res->free();
    }
} while ($conn->more_results() && $conn->next_result());

echo "Workflow updated successfully.\n";

echo "\nDocument Types Updated:\n";
$res = $conn->query("SELECT type_code, workflow_id FROM document_types WHERE type_code IN ('dept_file', 'student_activity_file')");
while($row = $res->fetch_assoc()) { print_r($row); }
