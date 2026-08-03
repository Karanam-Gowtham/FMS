<?php
$conn = mysqli_connect("localhost", "root", "", "project-fms");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$result = mysqli_query($conn, "SHOW COLUMNS FROM fdps_tab");
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
