<?php
require_once "../../includes/session.php";
require_once "../../includes/csrf.php";
include_once "../../includes/connection.php";

$dept = isset($_GET['event']) ? $_GET['event'] : '';
$event = $_GET['event'] ?? 'Unknown';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Event-specific login validation
    $credentials = [
        'NCC' => ['email' => 'ncc@gmail.com', 'password' => '123'],
        'Sports' => ['email' => 'sports@gmail.com', 'password' => '123'],
        'Clubs' => ['email' => 'clubs@gmail.com', 'password' => '123'],
        'NSS' => ['email' => 'nss@gmail.com', 'password' => '123'],
        'Women_Empowerment' => ['email' => 'women@gmail.com', 'password' => '123'],
        'IIC' => ['email' => 'iic@gmail.com', 'password' => '123'],
        'PASH' => ['email' => 'pash@gmail.com', 'password' => '123'],
        'Antiragging' => ['email' => 'antiragging@gmail.com', 'password' => '123'],
        'SAC' => ['email' => 'sac@gmail.com', 'password' => '123']
    ];

    if (isset($credentials[$event]) &&
        $credentials[$event]['email'] === $email &&
        $credentials[$event]['password'] === $password) {
        // Redirect to the dashboard with the event value
        session_regenerate_id(true);
        $_SESSION['c_cord'] = $email;
        echo "<script>
            alert('Login successful! ');

            window.location.href = 'c_upload.php?event=" . urlencode($event) . "';
        </script>";
        exit();
    } else {
        $error = "Invalid email or password for the " . htmlspecialchars($event) . " event.";
        echo "<script>
            alert(" . json_encode($error) . ");
        </script>";
    }
}

$extra_head = '
    <link rel="stylesheet" href="../../assets/css/auth.css">
    <style>
        .navbar-top {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
            background: white;
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            color: #1e293b;
            text-decoration: none;
        }
        .navbar-top:hover {
            color: #2563eb;
        }
        .dept-title {
            color: #2563eb;
            font-weight: 700;
        }
    </style>
';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Login - <?php echo htmlspecialchars((string)$dept); ?></title>
    <?php include_once '../../includes/header.php'; ?>
</head>
<body class="auth-page">
    <a href="../../index.php" class="navbar-top">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Back to Home | <span class="dept-title">Central (<?php echo htmlspecialchars((string)$dept); ?>)</span>
    </a>

    <div class="auth-container">
        <div class="auth-card">
            <h1><?php echo htmlspecialchars($event); ?> Login</h1>
            <h2>Enter your credentials</h2>
            
            <form method="POST" action="">
                <?php echo csrfField(); ?>
                
                <label for="email">Email Address *</label>
                <input type="email" name="email" id="email" placeholder="name@gmrit.edu.in" required>
                
                <label for="password">Password *</label>
                <input type="password" name="password" id="password" placeholder="Password" required>
                
                <button type="submit" class="auth-btn">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>

