<?php
include("includes/connection.php");
$res = $conn->query("SHOW COLUMNS FROM fdps_org_tab");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
?>