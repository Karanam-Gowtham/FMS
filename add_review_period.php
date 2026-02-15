<?php
include "includes/connection.php";
$sql = "ALTER TABLE dept_files ADD COLUMN review_period VARCHAR(50) DEFAULT NULL AFTER semester";
if ($conn->query($sql)) {
    echo "Column review_period added successfully.\n";
} else {
    echo "Error adding column: " . $conn->error . "\n";
}
?>
