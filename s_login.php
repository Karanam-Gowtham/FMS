<?php
include './header.php';

$activity = $_GET['activity'] ?? 'Unknown';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Activity-specific login validation
    $credentials = [
        'Papers' => ['email' => 'papers@gmail.com', 'password' => '123'],
        'Projects' => ['email' => 'projects@gmail.com', 'password' => '123'],
        'Internships' => ['email' => 'internships@gmail.com', 'password' => '123'],
        'SIH' => ['email' => 'sih@gmail.com', 'password' => '123'],
        'GATE' => ['email' => 'gate@gmail.com', 'password' => '123'],
        'Hackathons' => ['email' => 'hackathons@gmail.com', 'password' => '123'],
        'Professional_Bodies' => ['email' => 'bodies@gmail.com', 'password' => '123']
    ];

    $redirectPages = [
        'Papers' => 's_papers.php',
        'Projects' => 's_act_files.php',
        'Internships' => 's_act_files.php',
        'SIH' => 's_act_files.php',
        'GATE' => 's_act_files.php',
        'Hackathons' => 's_act_files.php',
        'Professional_Bodies' => 's_p_bodies.php'
    ];

    if (isset($credentials[$activity]) && 
    $credentials[$activity]['email'] === $email && 
    $credentials[$activity]['password'] === $password) {
    // Redirect to the corresponding page with the activity as a query parameter
    $redirectPage = $redirectPages[$activity] ?? 's_act_files.php';
    $redirectUrl = $redirectPage . "?activity=" . urlencode($activity);
    echo "<script>
        alert('Login successful!');
        window.location.href = '" . $redirectUrl . "';
    </script>";
    exit();
    } else {
        $error = "Invalid email or password for the $activity activity.";
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
            height: 110vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        
        .container11 {
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
    </style>
</head>
<body>
    <div class="container11">
        <div class="login-container">
            <h1>Login to <?php echo htmlspecialchars($activity); ?> Activity</h1>
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
