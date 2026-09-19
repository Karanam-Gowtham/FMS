<?php
require 'includes/connection.php';
$sql = "CREATE TABLE IF NOT EXISTS document_files (
    file_id       INT AUTO_INCREMENT PRIMARY KEY,
    doc_id        INT NOT NULL,
    file_label    VARCHAR(100) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name   VARCHAR(255) NOT NULL,
    file_path     VARCHAR(500) NOT NULL,
    mime_type     VARCHAR(100) DEFAULT NULL,
    file_size     INT DEFAULT NULL,
    uploaded_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE,
    INDEX idx_doc (doc_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql)) {
    echo "document_files created successfully\n";
} else {
    echo "Error creating document_files: " . $conn->error . "\n";
}

$sql2 = "CREATE TABLE IF NOT EXISTS document_actions (
    action_id     INT AUTO_INCREMENT PRIMARY KEY,
    doc_id        INT NOT NULL,
    user_id       INT NOT NULL,
    action_type   VARCHAR(50) NOT NULL,
    comment       TEXT,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql2)) {
    echo "document_actions created successfully\n";
} else {
    echo "Error creating document_actions: " . $conn->error . "\n";
}
