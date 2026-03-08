<?php
$p = "../uploads/fdps_org/1772729393_merged_fdp.pdf";
echo "Testing $p from HOD simulating path...\n";
chdir("e:/set/xampp/htdocs/mini/FMS/HOD");
if (file_exists($p))
    echo "Exists!\n";
else {
    echo "Doesn't exist.\n";
    echo "CWD is " . getcwd() . "\n";
    echo "Scan of ../: " . implode(", ", scandir("../")) . "\n";
}
?>