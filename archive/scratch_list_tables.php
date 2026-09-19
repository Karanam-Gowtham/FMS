<?php
require 'includes/connection.php';
$res = $conn->query('SHOW TABLES');
while ($row = $res->fetch_row()) {
    echo $row[0] . "\n";
}
