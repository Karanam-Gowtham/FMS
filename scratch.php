<?php
require 'core/bootstrap.php';
global $conn;
$res = $conn->query("SELECT * FROM document_types");
while($row = $res->fetch_assoc()) {
    echo $row['type_code'] . " - " . $row['label'] . "\n";
}
