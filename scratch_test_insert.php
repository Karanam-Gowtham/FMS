<?php
require 'includes/connection.php';
$stmt = $conn->prepare("INSERT INTO document_files (doc_id, file_label, original_name, stored_name, file_path, mime_type, file_size) VALUES (1, 'Paper File', 'test.pdf', 'test_abc.pdf', 'uploads/test.pdf', 'application/pdf', 12345)");
if (!$stmt) {
    echo 'Prepare failed: ' . $conn->error;
} else {
    if (!$stmt->execute()) {
        echo 'Execute failed: ' . $stmt->error;
    } else {
        echo 'Success';
    }
}
