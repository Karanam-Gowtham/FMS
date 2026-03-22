<?php
include "includes/connection.php";
$sql = "ALTER TABLE fdps_org_tab ADD COLUMN merged_file VARCHAR(255) DEFAULT NULL AFTER photo3";
if ($conn->query($sql)) {
    echo "Column added successfully\n";
} else {
    echo "Error: " . $conn->error . "\n";
}
?>