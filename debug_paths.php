<?php
session_start();
include_once "e:/set/xampp/htdocs/mini/FMS/includes/connection.php";

$res = $conn->query("SELECT id, certificate as path, 'FDP' as cat FROM fdps_tab WHERE branch = 'CSE' AND status = 'Accepted' LIMIT 3");
if (!$res) {
    die($conn->error);
}
while ($row = $res->fetch_assoc()) {
    $p = $row['path'];
    $fixed = $p;
    if (preg_match('/uploads[\/\\\\].*/', $p, $matches)) {
        $fixed = "../" . $matches[0];
    }
    echo "ID: " . $row['id'] . " | DB: " . $p . " | Fixed: " . $fixed . " | Exists: " . (file_exists("HOD/" . $fixed) ? "YES" : "NO") . "\n";
}

$res = $conn->query("SELECT id, merged_file as path, 'FDP ORG' as cat FROM fdps_org_tab WHERE branch = 'CSE' AND status = 'Accepted' LIMIT 3");
while ($row = $res->fetch_assoc()) {
    $p = $row['path'];
    $fixed = $p;
    if (preg_match('/uploads[\/\\\\].*/', $p, $matches)) {
        $fixed = "../" . $matches[0];
    }
    echo "ID: " . $row['id'] . " | DB: " . $p . " | Fixed: " . $fixed . " | Exists: " . (file_exists("HOD/" . $fixed) ? "YES" : "NO") . "\n";
}