<?php
include "includes/connection.php";

$sql = "ALTER TABLE dept_files ADD COLUMN uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";

if ($conn->query($sql) === TRUE) {
    echo "Column uploaded_at added successfully to dept_files";
} else {
    echo "Error adding column: " . $conn->error;
}

$conn->close();
?>
