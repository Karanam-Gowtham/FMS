<?php
include "includes/connection.php";
$c = $conn;

$tables = ['criteria', 'criteria1', 'criteria2'];
foreach ($tables as $t) {
    $new_t = "nba_" . $t;
    // Drop it if it already exists just in case
    mysqli_query($c, "DROP TABLE IF EXISTS $new_t");
    $res = mysqli_query($c, "CREATE TABLE $new_t LIKE $t");
    if ($res) {
        echo "Successfully created empty table $new_t\n";
    } else {
        echo "Failed to create $new_t: " . mysqli_error($c) . "\n";
    }
}
?>
