<?php
require 'core/bootstrap.php';
global $conn;
$res = $conn->query("SHOW COLUMNS FROM document_types");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
