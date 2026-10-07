<?php
require 'e:/set/xampp/htdocs/mini/FMS/core/bootstrap.php';
global $conn;

// Find the type_id for student_activity_file
$dt_res = $conn->query("SELECT type_id FROM document_types WHERE type_code = 'student_activity_file'");
$type_id = $dt_res->fetch_assoc()['type_id'];

// Find the documents with this type_id
$d_res = $conn->query("SELECT doc_id FROM documents WHERE type_id = $type_id");

$deleted_count = 0;
while ($row = $d_res->fetch_assoc()) {
    $doc_id = $row['doc_id'];
    
    // Delete files logic (optional: physical files if needed, but DB is priority)
    // $f_res = $conn->query("SELECT file_path FROM document_files WHERE doc_id = $doc_id");
    // while($f = $f_res->fetch_assoc()) { @unlink($f['file_path']); }

    $conn->query("DELETE FROM document_workflow_logs WHERE doc_id = $doc_id");
    $conn->query("DELETE FROM document_metadata WHERE doc_id = $doc_id");
    $conn->query("DELETE FROM document_files WHERE doc_id = $doc_id");
    $conn->query("DELETE FROM documents WHERE doc_id = $doc_id");
    $deleted_count++;
}

echo "Successfully deleted $deleted_count orphaned student activity document(s) and all their associated records.";
