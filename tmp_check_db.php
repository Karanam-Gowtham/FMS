<?php
include "includes/connection.php";
$result = $conn->query("DESC fdps_org_tab");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
?>