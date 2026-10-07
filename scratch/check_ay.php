<?php
require 'e:/set/xampp/htdocs/mini/FMS/core/bootstrap.php';
global $conn;
$res = $conn->query("SELECT * FROM academic_years");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
