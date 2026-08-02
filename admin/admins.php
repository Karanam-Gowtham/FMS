<?php
ob_start(); // Start output buffering at the very top
require_once '../includes/session.php';
require_once '../includes/csrf.php';
include_once '../includes/connection.php';

$dept = isset($_GET['dept']) ? $_GET['dept'] : '';
$error_message = ""; // Error message for popup

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: admins.php?dept=" . urlencode($dept));
    exit();
}

// Determine logged in role
$loggedInRole = '';
if (isset($_SESSION['username'])) {
    $loggedInRole = 'faculty';
} elseif (isset($_SESSION['a_username'])) {
    $loggedInRole = 'dept_coordinator';
} elseif (isset($_SESSION['h_username'])) {
    $loggedInRole = 'hod';
} elseif (isset($_SESSION['admin'])) {
    $loggedInRole = 'admin';
} elseif (isset($_SESSION['j_username'])) {
    $loggedInRole = 'jr_assistant';
} elseif (isset($_SESSION['c_cord'])) {
    $loggedInRole = 'central_coordinator';
}

// Logic to check if logged in user matches the current requested department
$matchDept = false;

if ($loggedInRole && $dept) {
    // If they have the modern session format with roles, just check that
    if (!empty($_SESSION['roles'])) {
        foreach ($_SESSION['roles'] as $r) {
            if (strcasecmp($r['dept_name'], $dept) == 0) {
                $matchDept = true;
                break;
            }
        }
    } else {
        // Fallback for legacy sessions: check the normalized Users / User_Roles tables
        // Find their email based on session variable
        $email_to_check = $_SESSION['username'] ?? $_SESSION['a_username'] ?? $_SESSION['h_username'] ?? $_SESSION['j_username'] ?? $_SESSION['admin'] ?? null;
        
        if ($email_to_check) {
            $check = $conn->prepare("
                SELECT d.dept_name 
                FROM Users u
                JOIN User_Roles ur ON u.user_id = ur.user_id
                JOIN Dept d ON ur.dept_id = d.dept_id
                WHERE u.email = ?
            ");
            $check->bind_param("s", $email_to_check);
            $check->execute();
            $res = $check->get_result();
            while ($r = $res->fetch_assoc()) {
                if (strcasecmp($r['dept_name'], $dept) == 0) {
                    $matchDept = true;
                    break;
                }
            }
            $check->close();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signIn'])) {
    $email = trim($_POST['userid']);
    $password = trim($_POST['password']);

    if ($loggedInRole) {
        $error_message = "You are already logged in as " . str_replace('_', ' ', $loggedInRole) . ". Please logout first.";
    } else {
        if ($email == "admin" && $password == "123") {
            session_regenerate_id(true);
            $_SESSION['admin'] = $email;
            ob_end_clean();
            header("Location: ../HOD/acd_year_aa.php?designation=admin");
            exit();
        } else {
            // Check the normalized database for the user's roles
            $stmt = $conn->prepare("
                SELECT u.user_id, r.role_id, r.role_name, d.dept_id, d.dept_name 
                FROM Users u
                JOIN User_Roles ur ON u.user_id = ur.user_id
                JOIN Roles r ON ur.role_id = r.role_id
                JOIN Dept d ON ur.dept_id = d.dept_id
                WHERE u.email = ? AND u.password = ?
            ");
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                session_regenerate_id(true);
                $assigned_role = '';
                $all_roles = [];
                
                while ($row = $result->fetch_assoc()) {
                    // Populate session with all their roles across all depts for the modern dashboard gateway
                    $all_roles[] = $row;
                    
                    if (strcasecmp($row['dept_name'], $dept) == 0) {
                        $db_role = $row['role_name'];
                        if (!$assigned_role) $assigned_role = $db_role;
                        
                        if ($db_role == 'Faculty') {
                            $_SESSION['username'] = $email;
                        } elseif ($db_role == 'Dept Coordinator') {
                            $_SESSION['a_username'] = $email;
                        } elseif ($db_role == 'HOD') {
                            $_SESSION['h_username'] = $email;
                            $_SESSION['dept'] = $dept;
                        } elseif (strpos($db_role, 'Assistant') !== false) {
                            $_SESSION['j_username'] = $email;
                            $_SESSION['dept'] = $dept;
                        } elseif ($db_role == 'Admin') {
                            $_SESSION['admin'] = $email;
                        } elseif (strpos($db_role, 'Coordinator') !== false) {
                            $_SESSION['c_cord'] = $email;
                        }
                    }
                }
                
                if (count($all_roles) > 0) {
                    $_SESSION['user_id'] = $all_roles[0]['user_id'];
                    $_SESSION['email'] = $email;
                    $_SESSION['roles'] = $all_roles;
                }
                
                ob_end_clean();
                if ($assigned_role == 'Faculty') {
                    header("Location: ../modules/faculty/acd_year.php?dept=" . urlencode($dept));
                } elseif ($assigned_role == 'Dept Coordinator') {
                    header("Location: ../modules/dept_coordinator/dc_acd_year.php?dept=" . urlencode($dept));
                } elseif ($assigned_role == 'HOD') {
                    header("Location: ../HOD/see_uploads.php?dept=" . urlencode($dept) . "&designation=HOD");
                } elseif (strpos($assigned_role, 'Assistant') !== false) {
                    header("Location: ../modules/jr_assistant/jr_acd_year.php?dept=" . urlencode($dept));
                } elseif ($assigned_role == 'Admin') {
                    header("Location: ../HOD/acd_year_aa.php?designation=admin");
                } elseif (strpos($assigned_role, 'Coordinator') !== false) {
                    header("Location: ../modules/central/c_aqar_files.php?designation=central_coordinator&event=" . urlencode($dept));
                } elseif ($assigned_role) {
                    header("Location: ../dashboard.php");
                } else {
                    // If they successfully logged in but don't have a role in the specific department they tried to access,
                    // bounce them directly to the main dashboard gateway where they can see ALL their roles.
                    header("Location: ../dashboard.php");
                }
                exit();
            } else {
                $error_message = "Invalid email, password, or account does not exist.";
            }
            $stmt->close();
        }
    }
}
// Include header ONLY after all possible redirects
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FMS</title>
    <style>
        body {
            background-image: url('../assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            justify-content: center;
            height: 100%;
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

        .navbar {
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 0;
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

        .sid {
            color: rgb(48, 30, 138);
            font-weight: 500;
        }

        .main-a {
            color: rgb(138, 30, 113);
            font-weight: 500;
        }

        .main-a:hover {
            color:rgb(182, 64, 211);
        }

        .home-icon {
            color: rgb(30, 58, 138);
            transition: color 0.2s;
        }

        .home-icon:hover {
            color: rgb(29, 78, 216);
        }

        .container11 {
            margin-top: -100px;
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
            width: 400px;
        }

        #loginForm {
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 10px;
            color: white;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
            width: 400px;
            margin-left: 50px;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input, select {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            border: none;
            font-size: 1em;
        }

        select {
            width:80%;
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

        .register {
            margin-top: 10px;
        }
        .reg{
            color:aqua;
        }
        .sp {
            color: blue;
        }
    </style>
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-items">
            <a href="../index.php" class="home-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </a>
            <span class="sp">&nbsp; >> &nbsp;</span>
            <span class="main"><span class="main-a">Department(<?php echo htmlspecialchars((string)$dept); ?>)</span></span>
        </div>
    </div>
</nav>

<div class="container11">
                <?php
                    // Check if any user is logged in
                    if ($loggedInRole) {
                        // Logout logic processed at top
                    }
                ?>
    <div id="loginForm">
        <h2 id="welcomeMessage">Login - <?php echo htmlspecialchars((string)$dept); ?></h2>
        <h4>Please enter your credentials</h4>
        <form method="POST">
            <?php echo csrfField(); ?>
            <input type="email" placeholder="Email Address" name="userid" required>
            <input type="password" placeholder="Password" name="password" required>
            <button class="btnl" type="submit" name="signIn">Login</button>
        </form>
        <p id="register" class="register">Don't have an account? <a href="../modules/auth/reg.php" class="reg">Register here</a>...</p>
    </div>
</div>

<script>
    window.onload = function() {
        let loggedInRole = "<?php echo $loggedInRole; ?>";
        let currentDept = "<?php echo urlencode($dept); ?>";
        let safeToRedirect = "<?php echo $matchDept ? 'yes' : 'no'; ?>";

        if (loggedInRole && safeToRedirect === 'yes') {
            if (loggedInRole === 'faculty') {
                window.location.href = "../modules/faculty/acd_year.php?dept=" + currentDept;
            } else if (loggedInRole === 'dept_coordinator') {
                window.location.href = "../modules/dept_coordinator/dc_acd_year.php?dept=" + currentDept;
            } else if (loggedInRole === 'jr_assistant') {
                window.location.href = "../modules/jr_assistant/jr_acd_year.php?dept=" + currentDept;
            } else if (loggedInRole === 'hod') {
                window.location.href = "../HOD/see_uploads.php?dept=" + currentDept + "&designation=HOD";
            } else if (loggedInRole === 'admin') {
                window.location.href = "../HOD/acd_year_aa.php?designation=admin";
            }
        }
    }
</script>

<?php if (!empty($error_message)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        showToast("<?php echo addslashes($error_message); ?>", "error");
    });
</script>
<?php endif; ?>

</body>
</html>
