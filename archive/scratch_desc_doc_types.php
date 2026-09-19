<?php
require 'includes/connection.php';
$res = $conn->query("DESCRIBE document_types");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
