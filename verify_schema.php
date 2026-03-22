<?php
include "includes/connection.php";
$res = $conn->query("DESCRIBE fdps_org_tab");
echo "Column | Type | Null | Default\n";
echo "---------------------------------\n";
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Default'] . "\n";
}
?>