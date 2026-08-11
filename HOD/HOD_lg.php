<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once __DIR__ . "/../config.php";
include_once __DIR__ . "/../includes/connection.php";
include_once __DIR__ . "/../includes/helpers.php";

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['signIn'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $email = (strpos($username, '@') !== false) ? $username : ($username . "@gmrit.edu.in");

    // Check users and user_roles table for HOD role
    $stmt = $conn->prepare("
        SELECT u.*, d.dept_name 
        FROM users u
        JOIN user_roles ur ON u.user_id = ur.user_id
        JOIN roles r ON ur.role_id = r.role_id
        LEFT JOIN dept d ON ur.dept_id = d.dept_id
        WHERE (u.email = ? OR u.full_name = ?) AND u.password = ? AND r.role_name = 'HOD' AND u.status = 'active'
    ");
    $stmt->bind_param("sss", $email, $username, $password);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['h_username'] = $username;
        $_SESSION['dept'] = $user['dept_name'] ?? 'CSE';
        $_SESSION['logged_in'] = true;

        ob_end_clean();
        header("Location: see_uploads.php?dept=" . urlencode($_SESSION['dept']) . "&designation=HOD");
        exit();
    } else {
        // Fallback check in reg_hod table
        $stmt2 = $conn->prepare("SELECT * FROM reg_hod WHERE userid = ? AND password = ?");
        $stmt2->bind_param("ss", $username, $password);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        if ($res2->num_rows > 0) {
            $row2 = $res2->fetch_assoc();
            $_SESSION['h_username'] = $username;
            $_SESSION['dept'] = $row2['department'] ?? 'CSE';
            $_SESSION['logged_in'] = true;

            ob_end_clean();
            header("Location: see_uploads.php?dept=" . urlencode($_SESSION['dept']) . "&designation=HOD");
            exit();
        } else {
            $error_msg = "Invalid HOD User ID or password.";
        }
        $stmt2->close();
    }
    $stmt->close();
}

include 'header_hod.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Login</title>
    <style>
        body {
            background-image: url('../stuff/gmr_landing_page.jpg');
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

        .error {
            color: #ff6b6b;
            margin-bottom: 15px;
        }

    </style>
</head>
<body>
    <div class="container11">
        <div class="login-container">
            <?php if (!empty($error_msg)): ?>
                <div class="error"><?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>
            <form action="" method="POST">
                <h1 id="hav">HOD<br>Log In</h1>
                <input type="text" name="username" placeholder="User Id" id="id" required />
                <input type="password" name="password" placeholder="Password" id="pass" required />
                <button type="submit" name="signIn" class="button1">Log In</button>
            </form>
        </div>
    </div>
</body>
</html>

