<?php
$conn = new mysqli('localhost', 'root', '', 'project-fms');
if ($conn->connect_error) die("Conn fail");

$dept = 'CSE';
// Exact results for CSE branch and Accepted status
$tables = ['fdps_tab', 'fdps_org_tab', 'published_tab', 'conference_tab', 'patents_table'];
foreach ($tables as $t) {
    if ($res = $conn->query("SELECT branch, HEX(branch) as hb, status, HEX(status) as hs, COUNT(*) as c FROM `$t` WHERE branch = '$dept' AND status = 'Accepted'")) {
        $row = $res->fetch_assoc();
        echo "$t: Count=" . $row['c'] . " (Branch=[".$row['branch']."] HEX=".$row['hb'].", Status=[".$row['status']."] HEX=".$row['hs'].")" . PHP_EOL;
    }
}

// User department check
$res = $conn->query("SELECT department FROM reg_hod WHERE userid = 'cse-hod'");
if ($row = $res->fetch_assoc()) {
    echo "HOD/cse-hod Department: [" . $row['department'] . "]" . PHP_EOL;
}
?>