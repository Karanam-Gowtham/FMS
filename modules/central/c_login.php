<?php
include "../../includes/connection.php";

$dept = isset($_GET['event']) ? $_GET['event'] : '';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$event = $_REQUEST['event'] ?? 'Unknown';

// Event-specific login validation credentials
$credentials = [
    'NCC' => ['email' => ['ncc@gmail.com', 'test@gmail.com'], 'password' => ['123', '123']],
    'Sports' => ['email' => ['sports@gmail.com', 'test@gmail.com'], 'password' => ['123', '123']],
    'Clubs' => ['email' => ['clubs@gmail.com', 'test@gmail.com'], 'password' => ['123', '123']],
    'NSS' => ['email' => ['nss@gmail.com', 'test@gmail.com'], 'password' => ['123', '123']],
    'Women_Empowerment' => ['email' => ['women@gmail.com', 'test@gmail.com'], 'password' => ['123', '123']],
    'IIC' => ['email' => ['iic@gmail.com', 'test@gmail.com'], 'password' => ['123', '123']],
    'PASH' => ['email' => ['pash@gmail.com', 'test@gmail.com'], 'password' => ['123', '123']],
    'Antiragging' => ['email' => ['antiragging@gmail.com', 'test@gmail.com'], 'password' => ['123', '123']],
    'SAC' => ['email' => ['sac@gmail.com', 'test@gmail.com'], 'password' => ['123', '123']],
    'R&D' => ['email' => ['rnd@gmail.com', 'test@gmail.com'], 'password' => ['123', '123']],
    'IQAC' => ['email' => ['iqac@gmail.com', 'test@gmail.com'], 'password' => ['123', '123']],
    'Exam_Section' => ['email' => ['exam@gmail.com', 'test@gmail.com'], 'password' => ['123', '123']]
];

// Check if user is already logged in with valid session
$activeUser = $_SESSION['c_cord'] ?? $_SESSION['username'] ?? $_SESSION['email'] ?? null;
$isAdmin = isset($_SESSION['admin']) || isset($_SESSION['h_username']);

if (!empty($activeUser) || $isAdmin) {
    // If admin/HOD OR if activeUser email is permitted for this event, auto-bypass login
    if ($isAdmin || (isset($credentials[$event]) && in_array($activeUser, $credentials[$event]['email']))) {
        $_SESSION['c_cord'] = $activeUser ?? $_SESSION['admin'] ?? 'coordinator';
        header("Location: c_upload.php?event=" . urlencode($event));
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $authenticated = false;

    // Check Users & User_Roles table
    $stmt = $conn->prepare("
        SELECT u.* FROM users u
        JOIN user_roles ur ON u.user_id = ur.user_id
        JOIN roles r ON ur.role_id = r.role_id
        WHERE (u.email = ? OR u.full_name = ?) AND u.password = ? AND u.status = 'active'
    ");
    $stmt->bind_param("sss", $email, $email, $password);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $authenticated = true;
    }
    $stmt->close();

    // Also check event credentials list or reg_central_cord
    if (!$authenticated && isset($credentials[$event])) {
        if (in_array($email, $credentials[$event]['email']) && in_array($password, $credentials[$event]['password'])) {
            $authenticated = true;
        }
    }

    if ($authenticated) {
        $_SESSION['c_cord'] = $email;
        $_SESSION['logged_in'] = true;
        echo "<script>
            alert('Login successful!');
            window.location.href = 'c_upload.php?event=" . urlencode($event) . "';
        </script>";
        exit();
    } else {
        $error = "Invalid email or password for the $event event.";
        echo "<script>
            alert('$error');
        </script>";
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
            background-image: url('./stuff/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;

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
            height: 110vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .container11 {
            margin-top: -80px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 10px;
            color: white;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
            width: 300px;
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

        button {
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .custom-button {
            margin-bottom: 400px;
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, rgb(30, 114, 54), rgb(25, 186, 11));
            color: white;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .custom-button:hover {
            background: linear-gradient(135deg, rgb(39, 109, 61), rgb(6, 162, 45));
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        }

        /* Navigation */
        .navbar {
            font-size: larger;
        }

        .nav-container {
            background-color: white;
            width: 150vw;
            margin-top: 80px;
            padding: 0 1rem;
        }

        .nav-items {
            margin-left: 70px;
            display: flex;
            align-items: center;
            height: 4rem;
        }

        .sid {
            color: rgb(48, 30, 138);
            font-weight: 500;
        }

        .main-a {
            color: rgb(138, 30, 113);
            font-weight: 500;
        }

        .main-a:hover {
            color: rgb(182, 64, 211);
        }

        .home-icon {
            color: rgb(30, 58, 138);
            transition: color 0.2s;
        }

        .home-icon:hover {
            color: rgb(29, 78, 216);
        }
    </style>
</head>

<body>
    <?php include "../../includes/header.php"; ?>


    <div class="container11">

        <div class="login-container">
            <h1>Login to
                <?php echo htmlspecialchars($event === 'Clubs' ? 'Clubs & Professional Bodies' : str_replace('_', ' ', $event)); ?>
            </h1>
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="hidden" name="event" value="<?php echo htmlspecialchars($event); ?>">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>


    </div>
</body>

</html>