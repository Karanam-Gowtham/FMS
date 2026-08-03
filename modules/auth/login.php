<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once '../../includes/connection.php';

// Check if user is already logged in as faculty
if (isset($_SESSION['username'])) {
    header("Location: ../faculty/acd_year.php");
    exit();
}

$login_error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signIn'])) {
    $userid = trim($_POST['userid']);
    $password = trim($_POST['password']);

    // Query reg_tab (legacy table)
    $stmt = $conn->prepare("SELECT * FROM reg_tab WHERE userid = ? AND password = ?");
    $stmt->bind_param("ss", $userid, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $login_stmt = $conn->prepare("INSERT INTO login_pg (userid, password) VALUES (?, ?)");
        $login_stmt->bind_param("ss", $userid, $password);
        if ($login_stmt->execute() === TRUE) {
            $_SESSION['username'] = $userid;
            ob_end_clean();
            header("Location: ../faculty/acd_year.php");
            exit();
        }
        $login_stmt->close();
    } else {
        $login_error = true;
    }
    $stmt->close();
}

include_once '../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FMS</title>
    <style>
        body {
            background-image: url('../../assets/img/gmr_landing_page.jpg');
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
            margin-top: 150px;
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

        .register-link {
            margin-top: 15px;
            color: #ccc;
        }

        .register-link a {
            color: #60a5fa;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container11">
        <div class="login-container">
            <form action="" method="POST">
                <h1 id="hav">Faculty<br>Log In</h1>
                <input type="text" name="userid" placeholder="User Id" id="id" required />
                <input type="password" name="password" placeholder="Password" id="pass" required />
                <button type="submit" name="signIn" class="button1">Log In</button>
            </form>
            <div class="register-link">
                Don't have an account? <a href="reg.php">Register here</a>
            </div>
        </div>
    </div>

    <?php if ($login_error): ?>
    <script>
        alert("Wrong User ID or password!");
    </script>
    <?php endif; ?>
</body>
</html>