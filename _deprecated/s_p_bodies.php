<?php
    include "connection.php";

    session_start();
    if (!isset($_SESSION['username'])) {
        die("You need to log in to view your uploads.");
    }

    $username = $_SESSION['username'];
    if (isset($_GET['dept'])) {
        $dept = $_GET['dept']; // Get the 'dept' value from the URL
    } else {
        echo "Department not set.";
    }

    if (isset($_GET['activity'])) {
        $activity = urldecode($_GET['activity']); // Decode the activity
    } else {
        $activity = 'Unknown'; // Fallback if no activity is provided
    }
?>
<?php
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Bodies</title>
    <style>
         body {
            background-image: url('./stuff/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height:100vh;
            display: flex;
            justify-content: center;
            color: #fff;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .container {
            margin-top: 150px;
            text-align: center;
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6);
            height: 190px;
        }

        h1 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 2.5em;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        select{
            margin-bottom: 20px;
        }
        select, button {
            padding: 10px;
            font-size: 1em;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        select {
            width: 200px;
        }

        button {
            background: linear-gradient(to right, rgb(139, 10, 130), rgb(229, 129, 225));
            color: white;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            transform: translateY(-3px);
        }

        button:active {
            transform: translateY(0);
        }

        .button1 {
            position: absolute;
            top: 150px;
            right: 300px;
            background: linear-gradient(to right, rgb(2, 26, 48), rgb(129, 187, 229));
            padding: 10px 15px;
            border-radius: 5px;
            color: white;
            text-align: center;
            width: 100px;
            text-decoration: none;
        }

    </style>
    <script>
        function navigateToPage() {
            var select = document.getElementById("dropdown");
            var value = select.value;
            if (value) {
                window.location.href = value;
            }
        }
    </script>
</head>
<body>
<div class="container">
    <h1>Professional Bodies</h1>
    <select id="dropdown">
        <option value="" selected disabled>Select an option</option>
        <option value="s_body_files.php?activity=ISTE">ISTE</option>
        <option value="s_body_files.php?activity=CSI">CSI</option>
        <option value="s_body_files.php?activity=ACM">ACM</option>
        <option value="s_body_files.php?activity=ACMW">ACMW</option>
        <option value="s_body_files.php?activity=Coding_Club">Coding Club</option>
    </select><br>
    <button onclick="navigateToPage()">Submit</button>
</div>
</body>
</html>
