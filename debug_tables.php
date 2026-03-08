<?php
require 'includes/connection.php';
foreach (['fdps_org_tab', 'files'] as $table) {
    echo "Table: $table\n";
    $res = $conn->query("DESCRIBE $table");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "  Error: " . $conn->error . "\n";
    }
}
