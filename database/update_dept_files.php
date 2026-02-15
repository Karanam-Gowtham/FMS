<?php
include '../includes/connection.php';

$table = 'dept_files';

// Add status
$result = $conn->query("SHOW COLUMNS FROM $table LIKE 'status'");
if ($result->num_rows == 0) {
    if ($conn->query("ALTER TABLE $table ADD COLUMN status VARCHAR(50) DEFAULT 'Pending HOD'") === TRUE) {
        echo "Column 'status' added successfully.\n";
    } else {
        echo "Error adding column 'status': " . $conn->error . "\n";
    }
}

// Add rejection_reason
$result = $conn->query("SHOW COLUMNS FROM $table LIKE 'rejection_reason'");
if ($result->num_rows == 0) {
    if ($conn->query("ALTER TABLE $table ADD COLUMN rejection_reason TEXT") === TRUE) {
        echo "Column 'rejection_reason' added successfully.\n";
    } else {
        echo "Error adding column 'rejection_reason': " . $conn->error . "\n";
    }
}

// Add uploaded_at
$result = $conn->query("SHOW COLUMNS FROM $table LIKE 'uploaded_at'");
if ($result->num_rows == 0) {
    if ($conn->query("ALTER TABLE $table ADD COLUMN uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP") === TRUE) {
        echo "Column 'uploaded_at' added successfully.\n";
    } else {
        echo "Error adding column 'uploaded_at': " . $conn->error . "\n";
    }
}

$conn->close();
?>
