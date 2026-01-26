<?php
session_start();
include 'header_hod.php';

$dept = isset($_GET['dept']) ? htmlspecialchars($_GET['dept']) : '';

if (isset($_POST['signIn'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($dept)) {
        // Department specific validation
        $expected_user = strtolower($dept) . '-hod';
        
        if ($username === $expected_user && $password === '123') {
            $_SESSION['h_username'] = $username;
            // You might want to store the dept in session if needed later
            $_SESSION['dept'] = $dept; 
            header("Location: see_uploads.php");
            exit();
        } else {
            echo "<script>alert('Invalid login for " . $dept . " department. Expected user: " . $expected_user . "');</script>";
        }
    } else {
        // Fallback if no department selected (or direct access)
        // Check against any valid department pattern if strict mode is not required, 
        // but user asked for authentication based on dept clicked.
        // We will allow login if the username format is correct (e.g. cse-hod)
        
        if (preg_match('/^([a-zA-Z0-9]+)-hod$/', $username, $matches) && $password === '123') {
             $_SESSION['h_username'] = $username;
             $_SESSION['dept'] = strtoupper($matches[1]);
             header("Location: see_uploads.php");
             exit();
        } else {
             echo "<script>alert('Invalid login. Please select a department or use correct credentials.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background-image: url('../assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            height: 100vh;
            margin: 0;
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

        .login-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 10px;
            color: white;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
            width: 300px;
            margin-top: 200px;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: scale(0.9);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

        h1 {
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            border: none;
            font-size: 1em;
        }

        .button1 {
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        .button1:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>
    <div class="container11">
        <div class="login-container">
            <form action="" method="POST">
                <h1 id="hav">HOD<br>Log In <?php if($dept) echo "($dept)"; ?></h1>
                <input type="text" name="username" placeholder="User Id" id="id" required />
                <input type="password" name="password" placeholder="Password" id="pass" required />
                <button type="submit" name="signIn" class="button1">Log In</button>
            </form>
        </div>
    </div>
    </body>
</html>
