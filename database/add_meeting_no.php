<?php
include __DIR__ . '/../includes/connection.php';

$table = 'dept_files';

// Add meeting_no
$result = $conn->query("SHOW COLUMNS FROM $table LIKE 'meeting_no'");
if ($result->num_rows == 0) {
    if ($conn->query("ALTER TABLE $table ADD COLUMN meeting_no INT") === TRUE) {
        echo "Column 'meeting_no' added successfully.\n";
    } else {
        echo "Error adding column 'meeting_no': " . $conn->error . "\n";
    }
} else {
    echo "Column 'meeting_no' already exists.\n";
}

$conn->close();
?>
