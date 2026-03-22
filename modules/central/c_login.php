<?php
require_once "../../includes/session.php";
require_once "../../includes/csrf.php";
include "../../includes/connection.php";

$dept = isset($_GET['event']) ? $_GET['event'] : '';
$event = $_GET['event'] ?? 'Unknown';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
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
        $error = "Invalid email or password for the $event event.";
        echo "<script>
            alert('$error');
        </script>";
    }
}

$extra_head = "
    <style>
        body {
            background-image: url('../../assets/img/gmr_landing_page.jpg');
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
        
        .container11{
            margin-top: -80px;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
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
            background: linear-gradient(135deg,rgb(30, 114, 54),rgb(25, 186, 11));
            color: white;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .custom-button:hover {
            background: linear-gradient(135deg,rgb(39, 109, 61),rgb(6, 162, 45));
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        }
            /* Navigation */
            .navbar {
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 80px;
            border-bottom: 1px solid #eee;
 
        font-size: larger;
    }

    .nav-container {
        background-color: white;
        width:150vw;
         /* margin-top moved to .navbar */
        padding: 0 1rem;
    }

    .nav-items {
        margin-left: 70px;
        display: flex;
        align-items: center;
        height: 4rem;
    }

    .sid{
        color: rgb(48, 30, 138);
        font-weight: 500;
    }

    .main-a {
        color: rgb(138, 30, 113);
        font-weight: 500;
    }
    .main-a:hover{
        color:rgb(182, 64, 211);
    }

    .home-icon {
        color: rgb(30, 58, 138);
        transition: color 0.2s;
    }

    .home-icon:hover {
        color: rgb(29, 78, 216);
    }
    </style>
";

include '../../includes/header.php';
?>

<nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span>&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a">Central(<?php echo htmlspecialchars((string)$dept); ?>)   </a></span>
                <span>&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>

    <div class="container11">

    <div class="login-container">
        <h1>Login to <?php echo htmlspecialchars($event); ?></h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <?php echo csrf_field(); ?>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
    
    
    </div>
</body>
</html>
