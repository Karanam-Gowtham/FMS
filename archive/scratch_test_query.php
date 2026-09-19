<?php
require 'includes/connection.php';
$stmt = $conn->prepare("
            SELECT d.*, dt.label as type_name, dep.dept_name, ay.year_label as year_name,
                   u.full_name as uploader_name, d.title
            FROM documents d
            LEFT JOIN document_types dt ON d.type_id = dt.type_id
            LEFT JOIN departments dep ON d.dept_id = dep.dept_id
            LEFT JOIN academic_years ay ON d.academic_year_id = ay.year_id
            LEFT JOIN users u ON d.uploaded_by = u.user_id
            LEFT JOIN workflow_steps ws ON d.current_step = ws.step_id
            WHERE ws.approver_role_id = ? AND d.dept_id = ? AND d.status = 'pending'
            ORDER BY d.updated_at DESC
        ");
if (!$stmt) {
    echo 'Error: ' . $conn->error;
} else {
    echo 'Success';
}
