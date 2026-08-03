<?php
$c = mysqli_connect('localhost', 'root', '', 'project-fms');
$r = mysqli_query($c, 'DESCRIBE published_tab');
while ($row = mysqli_fetch_assoc($r)) {
    echo $row['Field'] . "\n";
}
?>
