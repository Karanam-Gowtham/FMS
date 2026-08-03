<?php
$conn = mysqli_connect('localhost', 'root', '');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query1 = "DROP DATABASE IF EXISTS `project-fms`";
if (mysqli_query($conn, $query1)) {
    echo "Dropped database successfully.\n";
} else {
    echo "Error dropping database: " . mysqli_error($conn) . "\n";
}

$query2 = "CREATE DATABASE `project-fms`";
if (mysqli_query($conn, $query2)) {
    echo "Created database successfully.\n";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?>
