<?php
require 'includes/connection.php';
$res = $conn->query("SHOW CREATE TABLE fdps_org_tab");
echo $res->fetch_row()[1];
?>
