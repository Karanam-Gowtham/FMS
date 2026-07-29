<?php
include_once '../../config.php';
include_once CONNECTION_PATH;
include_once INCLUDES_PATH . '/helpers.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: " . BASE_URL . "/dashboard.php");
    exit();
}

$message = '';
$msg_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signIn'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("
        SELECT * FROM Users
        WHERE email = ? AND status = 'active'
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // For now using plain text password comparison (as in FMS)
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;

            // Fetch all roles for this user
            $role_stmt = $conn->prepare("
                SELECT ur.role_id, ur.dept_id, r.role_name, d.dept_name
                FROM User_Roles ur
                JOIN Roles r ON ur.role_id = r.role_id
                JOIN Dept d ON ur.dept_id = d.dept_id
                WHERE ur.user_id = ?
            ");
            $role_stmt->bind_param("i", $user['user_id']);
            $role_stmt->execute();
            $roles_result = $role_stmt->get_result();

            $roles = [];
            while ($row = $roles_result->fetch_assoc()) {
                $roles[] = [
                    'role_id' => (int) $row['role_id'],
                    'role_name' => $row['role_name'],
                    'dept_id' => (int) $row['dept_id'],
                    'dept_name' => $row['dept_name']
                ];
            }
            $role_stmt->close();

            $_SESSION['roles'] = $roles;

            // For backwards compatibility, set the first role as primary
            if (!empty($roles)) {
                $_SESSION['role_id'] = $roles[0]['role_id'];
                $_SESSION['role_name'] = $roles[0]['role_name'];
                $_SESSION['dept_id'] = $roles[0]['dept_id'];
                $_SESSION['dept_name'] = $roles[0]['dept_name'];
            }

            // Update last login
            $update = $conn->prepare("UPDATE Users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?");
            $update->bind_param("i", $user['user_id']);
            $update->execute();
            $update->close();

            header("Location: " . BASE_URL . "/dashboard.php");
            exit();
        } else {
            $message = "Invalid password!";
            $msg_type = "alert-danger";
        }
    } else {
        $message = "User does not exist!";
        $msg_type = "alert-danger";
    }
    $stmt->close();
}

include_once HEADER;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FMS</title>
    <link rel="stylesheet" href="<?php echo CSS_PATH . '/auth.css'; ?>">
</head>

<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Welcome Back</h1>
            <h2>Sign in to FMS</h2>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $msg_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" placeholder="name@gmrit.edu.in" required
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>

                <button type="submit" name="signIn" class="auth-btn">Sign In</button>
            </form>

            <a href="reg.php" class="auth-link">Don't have an account? Register here</a>
        </div>
    </div>
</body>

</html>