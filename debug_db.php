<?php
include "includes/connection.php";
$res = $conn->query("SELECT certificate FROM fdps_tab LIMIT 1");
if ($row = $res->fetch_assoc()) {
    echo "FDP Path: " . $row['certificate'] . "\n";
}
$res = $conn->query("SELECT paper_file FROM published_tab LIMIT 1");
if ($row = $res->fetch_assoc()) {
    echo "Publish Path: " . $row['paper_file'] . "\n";
}
$res = $conn->query("SELECT certificate_path FROM conference_tab LIMIT 1");
if ($row = $res->fetch_assoc()) {
    echo "Conf Path: " . $row['certificate_path'] . "\n";
}
?>