<?php
require 'includes/connection.php';
$res = $conn->query('DESCRIBE workflows');
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
