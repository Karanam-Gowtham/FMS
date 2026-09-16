<?php
require 'includes/connection.php';
$res = $conn->query("SELECT * FROM document_types");
if (!$res) die($conn->error);
while ($row = $res->fetch_assoc()) {
    echo $row['type_code'] . " - " . $row['label'] . "\n";
}
