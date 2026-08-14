<?php
ob_start(); // Start output buffering at the very top
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'header_admin.php';
include "../includes/connection.php";

$dept = isset($_GET['dept']) ? $_GET['dept'] : '';
$error_message = ""; // Error message for popup

// Session Auto-Pass: Auto redirect if already logged in when switching departments
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    if (isset($_SESSION['admin'])) {
        ob_end_clean();
        header("Location: ../HOD/acd_year_aa.php?dept=" . urlencode($dept) . "&designation=admin");
        exit();
    } elseif (isset($_SESSION['h_username'])) {
        ob_end_clean();
        header("Location: ../HOD/hod_acd_year.php?dept=" . urlencode($dept) . "&designation=HOD");
        exit();
    } elseif (isset($_SESSION['a_username'])) {
        ob_end_clean();
        header("Location: ../modules/dept_coordinator/dc_acd_year.php?dept=" . urlencode($dept));
        exit();
    } elseif (isset($_SESSION['username'])) {
        ob_end_clean();
        header("Location: ../modules/faculty/acd_year.php?dept=" . urlencode($dept));
        exit();
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['signIn'])) {
        $userid = trim($_POST['userid']);
        $password = trim($_POST['password']);
        $designation = trim($_POST['designation']);

        $email = (strpos($userid, '@') !== false) ? $userid : ($userid . "@gmrit.edu.in");

        // Map form designation to Role name
        $role_map = [
            'faculty' => 'Faculty',
            'dept_coordinator' => 'Dept Coordinator',
            'hod' => 'HOD',
            'central_coordinator' => 'Central Coordinator',
            'admin' => 'Admin'
        ];

        $target_role = $role_map[$designation] ?? '';

        // Query users & user_roles table
        $stmt = $conn->prepare("
            SELECT u.*, d.dept_name, r.role_name
            FROM users u
            JOIN user_roles ur ON u.user_id = ur.user_id
            JOIN roles r ON ur.role_id = r.role_id
            LEFT JOIN dept d ON ur.dept_id = d.dept_id
            WHERE (u.email = ? OR u.full_name = ?) AND u.password = ? AND r.role_name = ? AND u.status = 'active'
        ");
        $stmt->bind_param("ssss", $email, $userid, $password, $target_role);
        $stmt->execute();
        $res = $stmt->get_result();

        $authenticated = false;
        if ($res->num_rows > 0) {
            $u_data = $res->fetch_assoc();
            $authenticated = true;
            if (empty($dept) && !empty($u_data['dept_name'])) {
                $dept = $u_data['dept_name'];
            }
        }
        $stmt->close();

        // If not found in users table, fallback to legacy tables
        if (!$authenticated) {
            if ($designation === "faculty") {
                $stmt = $conn->prepare("SELECT * FROM reg_tab WHERE userid = ? AND password = ?");
                $stmt->bind_param("ss", $userid, $password);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) { $authenticated = true; }
                $stmt->close();
            } elseif ($designation === "dept_coordinator") {
                $stmt = $conn->prepare("SELECT * FROM reg_dept_cord WHERE userid = ? AND password = ?");
                $stmt->bind_param("ss", $userid, $password);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) { $authenticated = true; }
                $stmt->close();
            } elseif ($designation === "hod") {
                $stmt = $conn->prepare("SELECT * FROM reg_hod WHERE userid = ? AND password = ?");
                $stmt->bind_param("ss", $userid, $password);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) { $authenticated = true; }
                $stmt->close();
            } elseif ($designation === "admin") {
                $stmt = $conn->prepare("SELECT * FROM admin_reg WHERE Username = ? AND Password = ?");
                $stmt->bind_param("ss", $userid, $password);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) { $authenticated = true; }
                $stmt->close();
            }
        }

        if ($authenticated) {
            $_SESSION['logged_in'] = true;
            if ($designation === "faculty") {
                $_SESSION['username'] = $userid;
                ob_end_clean();
                header("Location: ../modules/faculty/acd_year.php?dept=$dept");
                exit();
            } elseif ($designation === "dept_coordinator") {
                $_SESSION['a_username'] = $userid;
                ob_end_clean();
                header("Location: ../modules/dept_coordinator/dc_acd_year.php?dept=$dept");
                exit();
            } elseif ($designation === "hod") {
                $_SESSION['h_username'] = $userid;
                $_SESSION['dept'] = $dept;
                ob_end_clean();
                header("Location: ../HOD/hod_acd_year.php?dept=" . urlencode($dept) . "&designation=HOD");
                exit();
            } elseif ($designation === "central_coordinator") {
                $_SESSION['c_username'] = $userid;
                $_SESSION['c_cord'] = $userid;
                $_SESSION['dept'] = $dept;
                ob_end_clean();
                header("Location: ../modules/central/cc_acd_year.php?dept=" . urlencode($dept) . "&designation=" . urlencode($designation));
                exit();
            } elseif ($designation === "admin") {
                $_SESSION['admin'] = $userid;
                ob_end_clean();
                header("Location: ../HOD/acd_year_aa.php?dept=" . urlencode($dept) . "&designation=" . urlencode($designation));
                exit();
            }
        } else {
            $error_message = "Invalid username or password for $designation.";
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
            background-image: url('../stuff/gmr_landing_page.jpg');
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
    <?php include "../includes/header.php"; ?>


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
