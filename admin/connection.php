<?php

    $conn=mysqli_connect("localhost","root","","project-fms");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>