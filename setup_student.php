<?php
require_once __DIR__ . '/config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Ensure document type exists
$res = $conn->query("SELECT type_id FROM document_types WHERE type_code = 'stu_act'");
if ($res->num_rows == 0) {
    $conn->query("INSERT INTO document_types (type_code, label, workflow_id) VALUES ('stu_act', 'Student Activity', NULL)");
    echo "Inserted Student Activity document type\n";
} else {
    echo "Student Activity document type already exists\n";
}

// 1. Create Workflow
$conn->query("INSERT IGNORE INTO workflows (workflow_id, workflow_key, label) VALUES (4, 'student_activity_wf', 'Student Activity Workflow')");

// 2. Map Workflow to Document Type
$conn->query("UPDATE document_types SET workflow_id = 4 WHERE type_code = 'stu_act'");

// 3. Create Workflow Step for cri_cord5
$conn->query("INSERT IGNORE INTO workflow_steps (step_id, workflow_id, step_order, step_label, responsible_role_id, scope) VALUES (10, 4, 1, 'Coordinator Review', 5, 'department')");

// 4. Create actions (if they don't exist)
// approve and reject usually exist, let's just create transitions

// 5. Create Transitions for Step 10
// Need to find action_id for 'approve' and 'reject'
$res = $conn->query("SELECT action_id, action_key FROM workflow_actions WHERE action_key IN ('approve', 'reject')");
$actions = [];
while ($row = $res->fetch_assoc()) {
    $actions[$row['action_key']] = $row['action_id'];
}

if (isset($actions['approve'])) {
    $conn->query("INSERT IGNORE INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES (10, " . $actions['approve'] . ", NULL, 'accepted')");
}
if (isset($actions['reject'])) {
    $conn->query("INSERT IGNORE INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES (10, " . $actions['reject'] . ", NULL, 'rejected')");
}

echo "Workflow setup complete.\n";
