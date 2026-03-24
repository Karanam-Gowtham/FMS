<?php
include_once "includes/connection.php";
$res = $conn->query("SHOW COLUMNS FROM fdps_org_tab");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
