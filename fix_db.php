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
    'patents_table'
];

echo "<h2>Database Migration Status</h2>";

foreach ($tables as $table) {
    echo "<h3>Checking table: $table</h3>";
    // Check if table exists to avoid errors
    $check_table = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check_table->num_rows > 0) {
        $check_col = $conn->query("SHOW COLUMNS FROM $table LIKE 'status'");
        if ($check_col->num_rows == 0) {
            if ($conn->query("ALTER TABLE $table ADD COLUMN status VARCHAR(50) DEFAULT 'Pending Dept Coordinator'")) {
                echo " - Added 'status' column.<br>";
            } else {
                echo " - Error adding 'status': " . $conn->error . "<br>";
            }
        } else {
            echo " - 'status' column already exists.<br>";
        }

        $check_col = $conn->query("SHOW COLUMNS FROM $table LIKE 'rejection_reason'");
        if ($check_col->num_rows == 0) {
            if ($conn->query("ALTER TABLE $table ADD COLUMN rejection_reason TEXT")) {
                echo " - Added 'rejection_reason' column.<br>";
            } else {
                echo " - Error adding 'rejection_reason': " . $conn->error . "<br>";
            }
        } else {
            echo " - 'rejection_reason' column already exists.<br>";
        }
    } else {
        echo " - Table DOES NOT EXIST.<br>";
    }
}
?>
