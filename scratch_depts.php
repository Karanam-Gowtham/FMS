<?php
require_once 'e:/set/xampp/htdocs/mini/FMS/core/bootstrap.php';
$res = $conn->query("SELECT dept_name FROM departments ORDER BY dept_name");
while($r = $res->fetch_assoc()) {
    echo $r['dept_name'] . "\n";
}
