<?php
ob_start(); // Start output buffering at the very top
session_start();
include 'header_admin.php';
include '../includes/connection.php';

$dept = isset($_GET['dept']) ? $_GET['dept'] : '';
$error_message = ""; // Error message for popup

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['signIn'])) {
        $userid = trim($_POST['userid']);
        $password = trim($_POST['password']);
        $designation = trim($_POST['designation']);

        if ($designation == "faculty") {
            $stmt = $conn->prepare("SELECT * FROM reg_tab WHERE userid = ? AND password = ? AND dept = ?");
            $stmt->bind_param("sss", $userid, $password, $dept);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Check if already logged in logic can be here or just proceed
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
            } elseif ($designation == "central_coordinator" && $userid == "central" && $password == "123") {
                $_SESSION['h_username'] = $userid;
                ob_end_clean();
                header("Location: ../modules/central/cc_acd_year.php?dept=" . urlencode($dept) . "&designation=" . urlencode($designation));
                exit();
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
    <div class="login-container">
        <h2>Please select your designation for</h2>
        <h2>LOGIN</h2>
        <select id="designation">
            <option value="" selected disabled>Choose...</option>
            <option value="faculty">Faculty</option>
            <option value="dept_coordinator">Dept Coordinator</option>
            <option value="hod">HOD</option>
            <option value="central_coordinator">Central Coordinator</option>
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

        if (designation) {
            if (designation === "faculty" && "<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>") {
                window.location.href = "../modules/faculty/acd_year.php?dept=<?php echo $dept; ?>";
                return;
            }

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
