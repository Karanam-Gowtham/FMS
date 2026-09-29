<?php
$conn = new mysqli('localhost', 'root', '', 'gmritfms');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}
echo "Tables:\n" . implode("\n", $tables) . "\n";
