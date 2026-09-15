<?php
require 'includes/connection.php';
$sql_drop = "DROP TABLE IF EXISTS document_actions;";
$conn->query($sql_drop);

$sql_create = "CREATE TABLE document_actions (
    action_id  INT AUTO_INCREMENT PRIMARY KEY,
    doc_id     INT NOT NULL,
    acted_by   INT NOT NULL,
    action     ENUM('uploaded','approved','rejected','resubmitted') NOT NULL,
    step_id    INT DEFAULT NULL,
    remarks    TEXT DEFAULT NULL,
    acted_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE,
    FOREIGN KEY (acted_by) REFERENCES users(user_id),
    FOREIGN KEY (step_id) REFERENCES workflow_steps(step_id),
    INDEX idx_doc (doc_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql_create)) {
    echo "document_actions created successfully\n";
} else {
    echo "Error creating document_actions: " . $conn->error . "\n";
}
