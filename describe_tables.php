<?php
$c = mysqli_connect('localhost', 'root', '', 'project-fms');
$tables = ['conference_tab', 'fdps_tab', 'fdps_org_tab'];
foreach ($tables as $t) {
    echo "--- $t ---\n";
    $r = mysqli_query($c, "DESCRIBE $t");
    if ($r) {
        while ($row = mysqli_fetch_assoc($r)) {
            echo $row['Field'] . "\n";
        }
    } else {
        echo "Table does not exist.\n";
    }
}
?>
