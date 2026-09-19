<?php
require 'includes/connection.php';
$res = $conn->query("DESCRIBE documents");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
