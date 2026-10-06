<?php
require_once __DIR__ . '/../core/bootstrap.php';
global $conn;
$doc_id = 23;
// Update the document to be accepted
$stmt = $conn->prepare("UPDATE documents SET status = 'accepted', current_step = NULL WHERE doc_id = ?");
$stmt->bind_param('i', $doc_id);
$stmt->execute();
$stmt->close();

// Insert a log action for the fix
$stmt = $conn->prepare("INSERT INTO document_actions (doc_id, action, acted_by, remarks, acted_at) VALUES (?, 'approve', 1, 'System Administrator: Automatically accepted as per new workflow routing.', NOW())");
$stmt->bind_param('i', $doc_id);
$stmt->execute();
$stmt->close();

echo "Doc 23 fixed.\n";
