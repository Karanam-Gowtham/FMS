<?php
include "includes/connection.php";

$tables = ['criteria', 'criteria1', 'criteria2'];
foreach ($tables as $t) {
    $nba_t = "nba_" . $t;
    // Check if destination table is empty
    $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM $nba_t");
    $row = mysqli_fetch_assoc($res);
    if ($row['cnt'] == 0) {
        $ok = mysqli_query($conn, "INSERT INTO $nba_t SELECT * FROM $t");
        if ($ok) {
            $count = mysqli_affected_rows($conn);
            echo "Copied $count rows from $t to $nba_t\n";
        } else {
            echo "Error copying $t to $nba_t: " . mysqli_error($conn) . "\n";
        }
    } else {
        echo "$nba_t already has {$row['cnt']} rows, skipping.\n";
    }
}

echo "Done!\n";
?>
