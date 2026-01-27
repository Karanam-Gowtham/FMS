<?php
include "../../includes/connection.php";
session_start();

if (empty($_SESSION['username'])) {
    echo "<script>
            alert('You need to login to logout.');
            window.location.href = 'login.php'; // Redirect to the login page
          </script>";
} else {
    // Destroy the session and log out the user
    session_unset();  // Unset all session variables
    session_destroy(); // Destroy the session

    echo "<script>
            alert('You have been logged out successfully.');
            window.location.href = '../../index.php'; // Redirect to the homepage or any page you prefer
          </script>";
}
?>
