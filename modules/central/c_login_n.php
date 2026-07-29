<?php
ob_start(); // Start output buffering at the very top
session_start();
include_once '../../includes/connection.php';
require_once '../../includes/csrf.php';

$dept = isset($_GET['event']) ? $_GET['event'] : '';
$login_error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signIn'])) {
    csrfValidate();
    $email = trim($_POST['userid']);
    $password = trim($_POST['password']);

    if ($email == "hod" && $password == "123") {
        $_SESSION['h_username'] = $email;
        ob_end_clean();
        header(LOC_C_AQAR_FILES . urlencode("hod") . PARAM_EVENT . urlencode($dept));
        exit();
    } elseif ($email == "admin" && $password == "123") {
        $_SESSION['admin'] = $email;
        ob_end_clean();
        header("Location: ../../HOD/acd_year_aa.php?designation=admin" . PARAM_EVENT . urlencode($dept));
        exit();
    } else {
        $stmt = $conn->prepare("
            SELECT u.user_id, r.role_name 
            FROM Users u
            JOIN User_Roles ur ON u.user_id = ur.user_id
            JOIN Roles r ON ur.role_id = r.role_id
            WHERE u.email = ? AND u.password = ? 
        ");
        if (!$stmt) {
            die("Database Prepare Error: " . $conn->error);
        }
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $assigned_role = '';
            $db_role_name = '';
            
            while ($row = $result->fetch_assoc()) {
                $db_role_name = $row['role_name'];
                
                if ($db_role_name == 'Faculty') {
                    $_SESSION['username'] = $email;
                    if (!$assigned_role) $assigned_role = 'faculty';
                } elseif ($db_role_name == 'Dept Coordinator') {
                    $_SESSION['a_username'] = $email;
                    if (!$assigned_role) $assigned_role = 'dept_coordinator';
                } elseif ($db_role_name == 'Central Coordinator') {
                    $_SESSION['c_username'] = $email;
                    if (!$assigned_role) $assigned_role = 'central_coordinator';
                } elseif ($db_role_name == 'Criteria Coordinator') {
                    $_SESSION['cri_username'] = $email;
                    if (!$assigned_role) $assigned_role = 'criteria_coordinator';
                }
            }
            
            if (!$assigned_role) {
                // If they have a role but it doesn't match the central system ones, default to faculty
                $_SESSION['username'] = $email;
                $assigned_role = 'faculty';
            }
            
            $stmt->close();
            ob_end_clean();
            header(LOC_C_AQAR_FILES . urlencode($assigned_role) . PARAM_EVENT . urlencode($dept));
            exit();
        } else {
            $login_error = true;
        }
        if(isset($stmt)) $stmt->close();
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
        <!-- Login Form -->
        <div class="auth-card" id="loginForm">
            <h1 id="welcomeMessage">Login - Central (<?php echo htmlspecialchars((string)$dept); ?>)</h1>
            <h2>Enter your credentials</h2>
            
            <form method="POST">
                <?php echo csrfField(); ?>
                
                <label for="userid">Email Address *</label>
                <input type="email" placeholder="name@gmrit.edu.in" name="userid" id="userid" required>
                
                <label for="password">Password *</label>
                <input type="password" placeholder="Password" name="password" id="password" required>
                
                <button class="auth-btn" type="submit" name="signIn">Sign In</button>
            </form>
            
            <div id="register">
                <a href="./reg.php" class="auth-link">Don't have an account? Register here</a>
            </div>
        </div>
    </div>

    <?php if ($login_error): ?>
    <script>
        window.onload = function () {
            alert("Invalid username, password, or you do not have permission for this central department.");
        };
    </script>
    <?php endif; ?>

</body>
</html>
