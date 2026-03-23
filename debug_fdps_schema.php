<?php
include_once "includes/connection.php";
$tables = ['fdps_tab', 'fdps_org_tab', 'published_tab', 'conference_tab', 'patents_table'];
foreach ($tables as $t) {
    echo "Table: $t\n";
    $result = $conn->query("SHOW COLUMNS FROM $t");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo " - " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    }
}
