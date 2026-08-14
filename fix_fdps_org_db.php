<?php
require 'includes/connection.php';

$queries = [
    "ALTER TABLE fdps_org_tab ADD COLUMN mode VARCHAR(50) DEFAULT NULL AFTER title",
    "ALTER TABLE fdps_org_tab ADD COLUMN funded_by VARCHAR(50) DEFAULT NULL AFTER location",
    "ALTER TABLE fdps_org_tab ADD COLUMN external_funder_name VARCHAR(200) DEFAULT NULL AFTER funded_by",
    "ALTER TABLE fdps_org_tab MODIFY COLUMN certificate VARCHAR(255) DEFAULT NULL"
];

foreach ($queries as $q) {
    if ($conn->query($q)) {
        echo "Successfully executed: $q\n";
    } else {
        echo "Error or already exists: " . $conn->error . "\n";
    }
}
?>
