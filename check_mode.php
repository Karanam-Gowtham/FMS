<?php
$conn = mysqli_connect("localhost", "root", "", "project-fms");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$result = mysqli_query($conn, "SHOW COLUMNS FROM fdps_org_tab");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . "\n";
    }
} else {
    echo "Query failed: " . mysqli_error($conn);
}
?>
