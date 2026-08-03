<?php
$conn = mysqli_connect("localhost", "root", "", "", "3306");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$res = $conn->query("SHOW DATABASES");
while ($row = $res->fetch_assoc()) {
    echo $row['Database'] . "\n";
}
$res = $conn->query("USE master");
if ($res) echo "SUCCESSFULLY SELECTED MASTER\n";
else echo "FAILED TO SELECT MASTER: " . $conn->error . "\n";
