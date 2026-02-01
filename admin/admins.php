<?php
ob_start(); // Start output buffering at the very top
session_start();
include 'header_admin.php';
include '../includes/connection.php';

$dept = isset($_GET['dept']) ? $_GET['dept'] : '';
$error_message = ""; // Error message for popup

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
} elseif (isset($_SESSION['c_cord'])) {
    $loggedInRole = 'central_coordinator';
}

// Logic to check if logged in user matches the current requested department
$matchDept = false;
if ($loggedInRole && $dept) {
    if ($loggedInRole == 'faculty' && isset($_SESSION['username'])) {
        $check = $conn->prepare("SELECT dept FROM reg_tab WHERE userid = ?");
        $check->bind_param("s", $_SESSION['username']);
        $check->execute();
        $res = $check->get_result();
        if ($r = $res->fetch_assoc()) {
            if (strcasecmp($r['dept'], $dept) == 0) $matchDept = true;
        }
    } elseif ($loggedInRole == 'dept_coordinator' && isset($_SESSION['a_username'])) {
        $check = $conn->prepare("SELECT department FROM reg_dept_cord WHERE userid = ?");
        $check->bind_param("s", $_SESSION['a_username']);
        $check->execute();
        $res = $check->get_result();
        if ($r = $res->fetch_assoc()) {
            if (strcasecmp($r['department'], $dept) == 0) $matchDept = true;
        }
    } elseif ($loggedInRole == 'hod' && isset($_SESSION['dept'])) {
        // HOD often has dept in session, but let's trust session if set
        if (strcasecmp($_SESSION['dept'], $dept) == 0) $matchDept = true;
    } elseif ($loggedInRole == 'admin') {
        // Admin can access all, so theoretically true, but admin usually doesn't switch depts this way?
        // Admin link usually has specific params. Let's allow for now.
        $matchDept = true; 
    }
    // Central Coordinator? Usually global.
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['signIn'])) {
        $userid = trim($_POST['userid']);
        $password = trim($_POST['password']);
        $designation = trim($_POST['designation']);

        if ($designation == "faculty") {
            // Fix: Check if already logged in (as anyone)
            if ($loggedInRole) {
                $error_message = "You are already logged in as " . str_replace('_', ' ', $loggedInRole) . ". Please logout first.";
            } else {
                $stmt = $conn->prepare("SELECT * FROM reg_tab WHERE userid = ? AND password = ? AND dept = ?");
                $stmt->bind_param("sss", $userid, $password, $dept);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $login_stmt = $conn->prepare("INSERT INTO login_pg (userid, password) VALUES (?, ?)");
                    $login_stmt->bind_param("ss", $userid, $password);
                    if ($login_stmt->execute() === TRUE) {
                        $_SESSION['username'] = $userid;
                        ob_end_clean(); // Clear buffer before redirect
                        header("Location: ../modules/faculty/acd_year.php?dept=$dept");
                        exit();
                    }
                    $login_stmt->close();
                } else {
                    $error_message = "Invalid username, password, or department mismatch.";
                }
                $stmt->close();
            }
        } else {
            if ($loggedInRole) {
                 $error_message = "You are already logged in as " . str_replace('_', ' ', $loggedInRole) . ". Please logout first.";
            } else {
                if ($designation == "dept_coordinator") {
                $stmt = $conn->prepare("SELECT * FROM reg_dept_cord WHERE userid = ? AND password = ? AND department = ?");
                $stmt->bind_param("sss", $userid, $password, $dept);
                $stmt->execute();
                $result = $stmt->get_result();
            
                if ($result->num_rows > 0) {
                    $_SESSION['a_username'] = $userid;
                    $stmt->close();
                    ob_end_clean();
            
                    // Redirect only after successful login
                    header("Location: ../modules/dept_coordinator/dc_acd_year.php?dept=$dept");
                    exit();
                } else {
                    $error_message = "Invalid username, password, or department mismatch.";
                }
            
            
            } elseif ($designation == "hod") {
                $stmt = $conn->prepare("SELECT * FROM reg_hod WHERE userid = ? AND password = ?");
                $stmt->bind_param("ss", $userid, $password);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    // Validate that the HOD belongs to the selected department
                    if (strtoupper($row['department']) === strtoupper($dept)) {
                        $_SESSION['h_username'] = $userid;
                        $_SESSION['dept'] = $dept;
                        ob_end_clean();
                        header("Location: ../modules/central/cc_acd_year.php?dept=" . urlencode($dept) . "&designation=" . urlencode("HOD"));
                        exit();
                    } else {
                        $error_message = "Invalid login. You are not the HOD of " . htmlspecialchars($dept) . ".";
                    }
                } else {
                    $error_message = "Invalid username or password for HOD.";
                }
                $stmt->close();
            } elseif ($designation == "admin" && $userid == "admin" && $password == "123") {
                $_SESSION['admin'] = $userid;
                ob_end_clean();
                header("Location: ../hod/acd_year_aa.php?dept=" . urlencode($dept) . "&designation=" . urlencode($designation));
                exit();
            } else {
                $error_message = "Invalid username or password.";
            }
            }
        }
    }
}
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
            font-size: larger;
        }

        .nav-container {
            background-color: white;
            width:150vw;
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
    </style>
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-items">
            <a href="../index.php" class="home-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </a>
            <span>&nbsp; >> &nbsp; </span>
            <span class="main"> <a href="#" class="main-a">Department(<?php echo "$dept" ?>)</a></span>
            <span>&nbsp; >> &nbsp; </span>
        </div>
    </div>
</nav>

<div class="container11">
                <?php
                    // Check if any user is logged in
                    if ($loggedInRole) {
                        // Logout logic processed at top, but we need form here
                         if (isset($_POST['logout'])) {
                            // This part inside HTML body will be executed if post logout is sent to self
                            // But usually PHP logic is at top. 
                            // We should move logic to top or just have form submit to self.
                            // Logic is handled? No, logic is missing in PHP block at top except for Login.
                            // I should add logout handling at the top of the file as well or here if it works.
                            // Let's rely on standard logic: submit -> page reloads -> check POST -> logout -> redirect.
                        }
                        
                        // echo '<form method="POST" style="position: absolute; top: 100px; right: 50px; z-index: 10;">
                        //        <button type="submit" name="logout" class="logout-btn11" style="background: transparent; border: 2px solid #dc3545; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; backdrop-filter: blur(5px); cursor: pointer;">Logout</button>
                        //      </form>';
                        // Removed logout button from body as it is now in header.
                        // Note: Admin/HOD don't really have "Edit Profile" in this system, only Faculty does. 
                        // User asked "in the same way i want log out for all the other roles".
                    }
                ?>
    <div class="login-container">
        <h2>Please select your designation for</h2>
        <h2>LOGIN</h2>
        <select id="designation">
            <option value="" selected disabled>Choose...</option>
            <option value="faculty">Faculty</option>
            <option value="dept_coordinator">Dept Coordinator</option>
            <option value="hod">HOD</option>

            <option value="admin">Admin</option>
        </select><br>
        <button class="btnl" onclick="showLogin()">Submit</button>
    </div>

    <div id="loginForm" style="display: none;">
        <h2 id="welcomeMessage"></h2>
        <h4>Please login</h4>
        <form method="POST">
            <input type="hidden" name="designation" id="designationHidden">
            <input type="text" placeholder="Username" name="userid" required>
            <input type="password" placeholder="Password" name="password" required>
            <button class="btnl" type="submit" name="signIn">Login</button>
        </form>
        <p id="register" class="register" style="display: none;">Don't have an account? <a href="../modules/auth/reg.php" class="reg">Register here</a>...</p>
    </div>
</div>

<script>
    function showLogin() {
        let designation = document.getElementById("designation").value;
        let loggedInRole = "<?php echo $loggedInRole; ?>";
        // Use PHP urlencode to ensure the department string is safe and correct for URLs
        let currentDept = "<?php echo urlencode($dept); ?>"; 

        if (loggedInRole) {
            // We need to know the department the user is logged into to safely auto-redirect.
            // Since PHP session variable for dept might be 'dept' or implicitly associated with user, 
            // we should ideally check it.
            // However, the JS only knows 'currentDept' (from GET param).
            // Let's pass the session department to JS as well.
            let sessionDept = "<?php echo isset($_SESSION['dept']) ? urlencode($_SESSION['dept']) : ''; ?>";
            // For Dept Coordinator, dept is fetched from DB on load in dashboard, but maybe not in session unless we put it there.
            // For Faculty, dept is usually not strictly in session 'dept' key in all flows, but let's see.
            // If sessionDept is empty, we might not be able to strict check, but usually role+cookie implies access.
            // BUT user said: "if i choose some other department also it is getting logged in" -> This is BAD.
            // We must ensure the user is redirected ONLY if the 'currentDept' matches their logged-in dept.
            
            // Wait, for Faculty, 'dept' might not be in $_SESSION['dept']. checking admins.php login logic:
            // Faculty login: header("Location: ...?dept=$dept"); $_SESSION['username'] = $userid; (No $_SESSION['dept'] set!)
            // Dept Coord: header("Location: ...?dept=$dept"); $_SESSION['a_username'] = $userid; (No $_SESSION['dept'] set!)
            // HOD: $_SESSION['dept'] = $dept; (SET!)
            
            // So for Faculty/Dept Coord, we don't strictly know their dept from SESSION in this file easily without querying DB.
            // BUT, if they are logged in, they are logged in as a specific user who belongs to a dept.
            // If I am logged in as 'faculty_cse', and I go to 'admins.php?dept=ECE', and click 'Faculty' -> 'Submit'.
            // The code redirects me to 'acd_year.php?dept=ECE'.
            // 'acd_year.php' likely uses the GET param 'dept'.
            // Does 'acd_year.php' verify if $_SESSION['username'] belongs to $_GET['dept']?
            // If NO, then that is a security flaw in 'acd_year.php'.
            // The user is asking to prevent this auto-login if the dept is different.
            
            // Since we can't easily fix 'acd_year.php' from here without editing it, 
            // we should at least try to prevent the auto-redirect if we suspect a mismatch.
            // BUT we don't have the user's dept here for Faculty/DC.
            
            // Alternative: The user says "if i choose the same dept again ... it should be logged in".
            // "if i choose some other department ... it is getting logged in".
            // This implies the previous auto-redirect was too aggressive.
            
            // Basic fix: Alert the user if they try to access a page while logged in, asking them to logout if they want to switch.
            // OR allow auto-redirect ONLY if we are sure.
            // Since we can't be sure for Faculty/DC without DB check, maybe checking URL dept vs nothing?
            // Actually, best to revert to ALERT if we can't confirm Dept.
            // BUT user *wanted* auto-login for same dept.
            
            // Let's rely on HOD's session dept since that one is set.
            // For others, we might need to set it on login.
            
            // Quick fix for now:
            // Only auto-redirect if HOD (where we know dept) OR just Alert for everyone to be safe?
            // User wants convenience AND security.
            
            // Let's try to match sessionDept if available.
            // If sessionDept is NOT available (Faculty/DC), we risk redirecting to wrong dept if we blindly redirect.
            // HOWEVER, the user said "if i choose some other department also it is getting logged in".
            // This means they visited `admins.php?dept=ECE` while logged in as CSE.
            // My JS redirected them to `...php?dept=ECE`.
            // And presumably the destination page didn't block them. (Major issue there).
            
            // Correct approach here:
            // 1. Alert user "You are already logged in...".
            // 2. Do NOT auto-redirect.
            // 3. IF user wants Same Dept Auto-Login:
            //    We strictly need to know the User's Dept.
            
            // Since I cannot easily get Faculty Dept here without SQL (which is technically possible in PHP block above), 
            // I will implement a check.
            
            // ... Logic moved to PHP ...
            
            // Updated JS to use a flag from PHP.
            // If PHP determines "SafeToAutoRedirect", we do it. Else, Alert.
            
            let safeToRedirect = "<?php echo $matchDept ? 'yes' : 'no'; ?>";
            
            if (loggedInRole === designation && safeToRedirect === 'yes') {
                 if (designation === 'faculty') {
                    window.location.href = "../modules/faculty/acd_year.php?dept=" + currentDept;
                } else if (designation === 'dept_coordinator') {
                    window.location.href = "../modules/dept_coordinator/dc_acd_year.php?dept=" + currentDept;
                } else if (designation === 'hod') {
                    window.location.href = "../modules/central/cc_acd_year.php?dept=" + currentDept + "&designation=HOD";
                } else if (designation === 'admin') {
                    window.location.href = "../hod/acd_year_aa.php?dept=" + currentDept + "&designation=admin";
                }
                return;
            }
            
            alert("You are already logged in as " + loggedInRole.replace('_', ' ') + ". Please logout first to switch roles or departments.");
            return;
        }

        if (designation) {
            document.getElementById("welcomeMessage").innerText = "Welcome " + designation.replace("_", " ");
            document.getElementById("loginForm").style.display = "block";
            document.getElementById("register").style.display = (designation === "faculty") ? "block" : "none";
            document.getElementById("designationHidden").value = designation;
        }
    }
</script>

<?php if (!empty($error_message)): ?>
<script>
    window.onload = function () {
        alert("<?php echo $error_message; ?>");
    };
</script>
<?php endif; ?>

</body>
</html>
