<?php
ob_start(); // Start output buffering at the very top
session_start();
include '../../includes/connection.php';
require_once '../../includes/csrf.php';

$dept = isset($_GET['event']) ? $_GET['event'] : '';
$login_error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['signIn'])) {
        csrf_validate();
        $userid = trim($_POST['userid']);
        $password = trim($_POST['password']);
        $designation = trim($_POST['designation']);

        if ($designation == "faculty") {
            if (isset($_SESSION['username'])) {
                header("Location: c_aqar_files.php?designation=" . urlencode($designation) . "&event=" . urlencode($dept));
                exit();
            }

            $stmt = $conn->prepare("SELECT * FROM reg_tab WHERE userid = ? AND password = ?");
            $stmt->bind_param("ss", $userid, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $login_stmt = $conn->prepare("INSERT INTO login_pg (userid, password) VALUES (?, ?)");
                $login_stmt->bind_param("ss", $userid, $password);
                if ($login_stmt->execute() === TRUE) {
                    $_SESSION['username'] = $userid;
                    ob_end_clean();
                    header("Location: c_aqar_files.php?designation=" . urlencode($designation) . "&event=" . urlencode($dept));
                    exit();
                }
                $login_stmt->close();
            } else {
                $login_error = true;
            }
            $stmt->close();
        } else {
            if ($designation == "dept_coordinator") {
                $stmt = $conn->prepare("SELECT * FROM reg_dept_cord WHERE userid = ? AND password = ?");
                if (!$stmt) {
                    die("Database Prepare Error (dept_coordinator): " . $conn->error);
                }
                $stmt->bind_param("ss", $userid, $password);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $_SESSION['a_username'] = $userid;
                    $stmt->close();
                    ob_end_clean();

                    header("Location: c_aqar_files.php?designation=" . urlencode($designation) . "&event=" . urlencode($dept));
                    exit();
                } else {
                    $login_error = true;
                }
            } elseif ($designation == "central_coordinator") {
                $stmt = $conn->prepare("SELECT * FROM reg_central_cord WHERE userid = ? AND password = ?");
                $stmt->bind_param("ss", $userid, $password);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $_SESSION['c_username'] = $userid;
                    $stmt->close();
                    ob_end_clean();

                    header("Location: c_aqar_files.php?designation=" . urlencode($designation) . "&event=" . urlencode($dept));
                    exit();
                } else {
                    $login_error = true;
                }
            } elseif ($designation == "criteria_coordinator") {
                $stmt = $conn->prepare("SELECT * FROM reg_cri_cord WHERE userid = ? AND password = ?");
                $stmt->bind_param("ss", $userid, $password);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $_SESSION['cri_username'] = $userid;
                    $stmt->close();
                    ob_end_clean();

                    header("Location: c_aqar_files.php?designation=" . urlencode($designation) . "&event=" . urlencode($dept));
                    exit();
                } else {
                    $login_error = true;
                }
            } elseif ($designation == "hod" && $userid == "hod" && $password == "123") {
                $_SESSION['h_username'] = $userid;
                ob_end_clean();
                header("Location: c_aqar_files.php?designation=" . urlencode("hod") . "&event=" . urlencode($dept));
                exit();
            } elseif ($designation == "admin" && $userid == "admin" && $password == "123") {
                $_SESSION['admin'] = $userid;
                ob_end_clean();
                header("Location: ./hod/acd_year_aa.php?designation=" . urlencode($designation) . "&event=" . urlencode($dept));
                exit();
            } else {
                $login_error = true;
            }
        }
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
        .container11 {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 70vh;
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
            width: 80%;
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
            width: 150vw;
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
";

include '../../includes/header.php';
?>

    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </a>
                <span>&nbsp;  >> &nbsp; </span><span class="main"><a href="#" class="main-a">Central(<?php echo htmlspecialchars($dept) ?>)</a></span>
                <span>&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>
    <br>

        <div class="container11">
        <div class="login-container">
            <h2>Please select your designation for</h2>
            <h2>LOGIN</h2>
            <select id="designation">
                <option value="" selected disabled>Choose...</option>
                <option value="faculty">Faculty</option>
                <option value="dept_coordinator">Dept Coordinator</option>
                <option value="central_coordinator">Central Coordinator</option>
                <option value="criteria_coordinator">Criteria Coordinator</option>
                <option value="hod">HOD</option>
                <option value="admin">Admin</option>
            </select><br>
            <button class="btnl" onclick="showLogin()">Submit</button>
        </div>

        <div id="loginForm" style="display: none;">
            <h2 id="welcomeMessage"></h2>
            <h4>Please login</h4>
            <form method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="designation" id="designationHidden">
                <input type="text" placeholder="Username" name="userid" required>
                <input type="password" placeholder="Password" name="password" required>
                <button class="btnl" type="submit" name="signIn">Login</button>
            </form>
            <p id="register" class="register" style="display: none;">Don't have an account? <a href="./reg.php">Register here</a>...</p>
        </div>
    </div>

    <script>
        function showLogin() {
            let designation = document.getElementById("designation").value;
            let dept = "<?php echo "$dept" ?>";
            if (designation) {
                const username = "<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>";
                if (designation === "faculty" && username) {
                    window.location.href = "c_aqar_files.php?designation=" + encodeURIComponent(designation) + "&event=" + encodeURIComponent(dept);
                    return;
                }

                document.getElementById("welcomeMessage").innerText = "Welcome " + designation.replace("_", " ");
                document.getElementById("loginForm").style.display = "block";
                document.getElementById("register").style.display = (designation === "faculty") ? "block" : "none";
                document.getElementById("designationHidden").value = designation;
            }
        }
    </script>

    <?php if ($login_error): ?>
    <script>
        alert("Invalid username or password. Please try again.");
        var designation = "<?php echo isset($_POST['designation']) ? htmlspecialchars($_POST['designation']) : ''; ?>";
        if (designation) {
            document.getElementById("designation").value = designation;
            showLogin();
        }
    </script>
    <?php endif; ?>

</body>
</html>
