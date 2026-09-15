<?php
require 'includes/connection.php';
$sql = "ALTER TABLE documents ADD COLUMN title VARCHAR(255) NULL AFTER academic_year_id;";
if ($conn->query($sql)) {
    echo "Added title column to documents table.\n";
} else {
    echo "Error adding title column: " . $conn->error . "\n";
}
