<?php
require 'includes/connection.php';
$res = $conn->query('SELECT * FROM reg_cri_cord');
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
