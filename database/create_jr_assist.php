<?php
include '../includes/connection.php';
$conn->query("CREATE TABLE IF NOT EXISTS reg_jr_assistant (
    userid VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    department VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "Table created.";
?>
