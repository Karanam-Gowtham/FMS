<?php
require 'includes/connection.php';
$res = $conn->query("SHOW CREATE TABLE fdps_tab");
echo $res->fetch_row()[1];
?>
