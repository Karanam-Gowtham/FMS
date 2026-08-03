<?php
$conn = mysqli_connect('localhost', 'root', '');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "CREATE DATABASE `project-fms`";
if (mysqli_query($conn, $query)) {
    echo "Created database successfully.\n";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?>
