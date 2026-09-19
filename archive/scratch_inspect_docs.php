<?php
require 'includes/connection.php';
$res = $conn->query("SELECT d.*, ws.step_label, ws.responsible_role_id FROM documents d LEFT JOIN workflow_steps ws ON d.current_step = ws.step_id");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
echo "--- Actions ---\n";
$res2 = $conn->query("SELECT * FROM document_actions");
while ($row2 = $res2->fetch_assoc()) {
    print_r($row2);
}
