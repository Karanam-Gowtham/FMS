<?php
include 'includes/connection.php';

$tables = [
    'files',
    'files5_1_1and2',
    'files5_1_3',
    'files5_1_4',
    'fdps_tab',
    'fdps_org_tab',
    'conference_tab',
    'published_tab',
    'patents_table' // based on patents.php
];

foreach ($tables as $table) {
    // Check if table exists to avoid errors
    $check_table = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check_table->num_rows > 0) {
        // Add status column
        $check_col = $conn->query("SHOW COLUMNS FROM $table LIKE 'status'");
        if ($check_col->num_rows == 0) {
            $sql = "ALTER TABLE $table ADD COLUMN status VARCHAR(50) DEFAULT 'Pending HOD'";
            if ($conn->query($sql) === TRUE) {
                echo "Added 'status' to $table.<br>";
            } else {
                echo "Error adding 'status' to $table: " . $conn->error . "<br>";
            }
        }

        // Add rejection_reason column
        $check_col = $conn->query("SHOW COLUMNS FROM $table LIKE 'rejection_reason'");
        if ($check_col->num_rows == 0) {
            $sql = "ALTER TABLE $table ADD COLUMN rejection_reason TEXT";
            if ($conn->query($sql) === TRUE) {
                echo "Added 'rejection_reason' to $table.<br>";
            } else {
                echo "Error adding 'rejection_reason' to $table: " . $conn->error . "<br>";
            }
        }
    } else {
        echo "Table $table does not exist.<br>";
    }
}

$conn->close();
?>