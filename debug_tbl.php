<?php
include 'includes/connection.php';
$res = $conn->query("SHOW CREATE TABLE reg_dept_cord");
if ($res) {
    if ($row = $res->fetch_row()) {
        echo $row[1];
    } else {
        echo "Table reg_dept_cord not found or empty??";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
