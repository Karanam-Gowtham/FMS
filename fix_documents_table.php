<?php
require 'includes/connection.php';

$queries = [
    "ALTER TABLE Documents ADD COLUMN type_id INT(11) DEFAULT NULL",
    "ALTER TABLE Documents ADD COLUMN academic_year_id INT(11) DEFAULT NULL",
    "ALTER TABLE Documents ADD COLUMN uploaded_by INT(11) DEFAULT NULL",
    "ALTER TABLE Documents ADD COLUMN dept_id INT(11) DEFAULT NULL",
    "ALTER TABLE Documents ADD COLUMN current_role_id INT(11) DEFAULT NULL",
    "ALTER TABLE Documents ADD COLUMN original_file_name VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE Documents ADD COLUMN file_path VARCHAR(500) DEFAULT NULL",
    "ALTER TABLE Documents ADD COLUMN status VARCHAR(50) DEFAULT 'Pending'",
    "ALTER TABLE Documents ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
];

foreach ($queries as $query) {
    if ($conn->query($query) === TRUE) {
        echo "Executed: $query\n";
    } else {
        echo "Error or already exists ($query): " . $conn->error . "\n";
    }
}
$conn->close();
?>
