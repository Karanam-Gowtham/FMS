<?php
include_once "includes/connection.php";
$res = $conn->query("SHOW COLUMNS FROM dept_files");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
