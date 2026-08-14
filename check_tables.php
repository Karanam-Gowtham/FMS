<?php
include "includes/connection.php";
$r = mysqli_query($conn, "DESCRIBE a_c_files");
echo "a_c_files columns:\n";
while ($row = mysqli_fetch_assoc($r)) {
    echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

$r = mysqli_query($conn, "DESCRIBE a_cri_files");
echo "\na_cri_files columns:\n";
while ($row = mysqli_fetch_assoc($r)) {
    echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

$r = mysqli_query($conn, "DESCRIBE a_files");
echo "\na_files columns:\n";
while ($row = mysqli_fetch_assoc($r)) {
    echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
