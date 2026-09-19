<?php
require 'includes/connection.php';
$stmt = $conn->prepare("
         SELECT d.doc_id, d.type_id, d.uploaded_by, d.dept_id, d.academic_year_id,
                d.title, d.status, d.current_step, d.rejection_reason,
                d.created_at, d.updated_at,
                dt.type_code as type_key, dt.label as type_label, dt.workflow_id as workflow_key,
                u.full_name AS uploader_name,
                dep.dept_name,
                ay.year_label
         FROM documents d
         JOIN document_types dt ON dt.type_id = d.type_id
         JOIN users u ON u.user_id = d.uploaded_by
         JOIN departments dep ON dep.dept_id = d.dept_id
         LEFT JOIN academic_years ay ON ay.year_id = d.academic_year_id
         WHERE d.doc_id = ?
");
if (!$stmt) {
    echo 'Error: ' . $conn->error;
} else {
    echo 'Success';
}
