<?php
$c = mysqli_connect('localhost', 'root', '', 'project-fms');
$tables = ['criteria', 'criteria1', 'criteria2'];
foreach ($tables as $t) {
    echo "--- $t ---\n";
    $r = mysqli_query($c, "SHOW CREATE TABLE $t");
    if ($r) {
        $row = mysqli_fetch_row($r);
        echo $row[1] . "\n\n";
    }
}
?>
