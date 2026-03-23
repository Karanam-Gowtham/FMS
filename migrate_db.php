<?php
include_once 'includes/connection.php';

// Add status column
$sql = "ALTER TABLE files ADD COLUMN status VARCHAR(50) DEFAULT 'Pending Dept Cord'";
if ($conn->query($sql) === true) {
    echo "Column 'status' added successfully.<br>";
} else {
    echo "Error adding column 'status': " . $conn->error . "<br>";
}

// Add rejection_reason column
$sql = "ALTER TABLE files ADD COLUMN rejection_reason TEXT";
if ($conn->query($sql) === true) {
    echo "Column 'rejection_reason' added successfully.<br>";
} else {
    echo "Error adding column 'rejection_reason': " . $conn->error . "<br>";
}

// Add current_reviewer column (optional but helpful)
// Let's stick to status for now.

$conn->close();

