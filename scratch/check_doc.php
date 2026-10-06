<?php
require_once __DIR__ . '/../core/bootstrap.php';
global $conn;
$res = $conn->query("SELECT * FROM documents WHERE doc_id = 23");
while($row = $res->fetch_assoc()) { print_r($row); }
