<?php
include_once "../../includes/connection.php";
include_once '../../includes/session.php';

if (empty($_SESSION['username']) && empty($_SESSION['a_username']) && empty($_SESSION['j_username']) && empty($_SESSION['h_username']) && empty($_SESSION['admin']) && empty($_SESSION['c_cord'])) {
    echo "<script>
            alert('You need to login to logout.');
            window.location.href = '../../index.php'; // Redirect to home
          </script>";
} else {
    // Destroy the session and log out the user
    session_unset();  // Unset all session variables
    session_destroy(); // Destroy the session

    echo "<script>
            alert('You have been logged out successfully.');
            window.location.href = '../../index.php'; // Redirect to the homepage
          </script>";
}

