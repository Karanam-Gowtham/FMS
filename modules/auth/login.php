<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once '../../config.php';
include_once '../../includes/connection.php';
include_once '../../includes/helpers.php';

// Check if user is already logged in
if (isLoggedIn()) {
    header("Location: " . BASE_URL . "/dashboard.php");
    exit();
}

$login_error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signIn'])) {
    $userid = trim($_POST['userid']);
    $password = trim($_POST['password']);

    $email = (strpos($userid, '@') !== false) ? $userid : ($userid . "@gmrit.edu.in");

    // Query Users table
    $stmt = $conn->prepare("
        SELECT * FROM users
        WHERE (email = ? OR full_name = ?) AND password = ? AND status = 'active'
    ");
    $stmt->bind_param("sss", $email, $userid, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    $user = null;
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        // Fallback: Check reg_tab, reg_hod, reg_dept_cord, admin_reg
        $legacy_tables = [
            ['table' => 'reg_tab', 'user_col' => 'userid', 'role_id' => 4, 'dept_col' => 'dept'],
            ['table' => 'reg_hod', 'user_col' => 'userid', 'role_id' => 3, 'dept_col' => 'department'],
            ['table' => 'reg_dept_cord', 'user_col' => 'userid', 'role_id' => 5, 'dept_col' => 'department'],
            ['table' => 'reg_cri_cord', 'user_col' => 'userid', 'role_id' => 2, 'dept_col' => NULL],
            ['table' => 'reg_central_cord', 'user_col' => 'userid', 'role_id' => 6, 'dept_col' => NULL],
            ['table' => 'admin_reg', 'user_col' => 'Username', 'role_id' => 1, 'dept_col' => NULL]
        ];

        foreach ($legacy_tables as $lt) {
            $t = $lt['table'];
            $uc = $lt['user_col'];
            $l_stmt = $conn->prepare("SELECT * FROM `$t` WHERE `$uc` = ? AND `password` = ?");
            $l_stmt->bind_param("ss", $userid, $password);
            $l_stmt->execute();
            $l_res = $l_stmt->get_result();
            if ($l_res->num_rows > 0) {
                $l_row = $l_res->fetch_assoc();
                $dept_name = ($lt['dept_col'] && isset($l_row[$lt['dept_col']])) ? $l_row[$lt['dept_col']] : 'CSE';

                // Auto-create user in Users table & User_Roles table
                $u_ins = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
                $u_ins->bind_param("sss", $userid, $email, $password);
                if ($u_ins->execute()) {
                    $new_uid = $conn->insert_id;
                    $d_stmt = $conn->prepare("SELECT dept_id FROM dept WHERE dept_name = ? LIMIT 1");
                    $d_stmt->bind_param("s", $dept_name);
                    $d_stmt->execute();
                    $d_res = $d_stmt->get_result();
                    $dept_id = ($d_row = $d_res->fetch_assoc()) ? (int)$d_row['dept_id'] : 1;
                    $d_stmt->close();

                    $r_ins = $conn->prepare("INSERT IGNORE INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, ?)");
                    $r_ins->bind_param("iii", $new_uid, $lt['role_id'], $dept_id);
                    $r_ins->execute();
                    $r_ins->close();

                    $user = [
                        'user_id' => $new_uid,
                        'full_name' => $userid,
                        'email' => $email
                    ];
                }
                $u_ins->close();
                $l_stmt->close();
                break;
            }
            $l_stmt->close();
        }
    }
    $stmt->close();

    if ($user) {
        // Fetch all assigned roles for this user
        $role_stmt = $conn->prepare("
            SELECT ur.role_id, ur.dept_id, r.role_name, d.dept_name
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.role_id
            LEFT JOIN dept d ON ur.dept_id = d.dept_id
            WHERE ur.user_id = ?
        ");
        $role_stmt->bind_param("i", $user['user_id']);
        $role_stmt->execute();
        $roles_res = $role_stmt->get_result();

        $roles = [];
        while ($row = $roles_res->fetch_assoc()) {
            $roles[] = [
                'role_id' => (int) $row['role_id'],
                'role_name' => $row['role_name'],
                'dept_id' => (int) $row['dept_id'],
                'dept_name' => $row['dept_name']
            ];
        }
        $role_stmt->close();

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $user['full_name'];
        $_SESSION['user_identifier'] = $userid;
        $_SESSION['roles'] = $roles;
        $_SESSION['logged_in'] = true;

        // Log login record in legacy login_pg
        $login_stmt = $conn->prepare("INSERT INTO login_pg (userid, password) VALUES (?, ?)");
        $login_stmt->bind_param("ss", $userid, $password);
        @$login_stmt->execute();
        @$login_stmt->close();

        ob_end_clean();
        header("Location: " . BASE_URL . "/dashboard.php");
        exit();
    } else {
        $login_error = true;
    }
}

include_once '../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FMS</title>
    <style>
        body {
            background-image: url('../../assets/img/gmr_landing_page.jpg');
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
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .login-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 10px;
            color: white;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
            width: 300px;
            margin-top: 150px;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
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

        .button1 {
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        .button1:hover {
            background-color: #0056b3;
        }

        .register-link {
            margin-top: 15px;
            color: #ccc;
        }

        .register-link a {
            color: #60a5fa;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container11">
        <div class="login-container">
            <form action="" method="POST">
                <h1 id="hav">Sign In</h1>
                <input type="text" name="userid" placeholder="User ID or Email" id="id" required />
                <input type="password" name="password" placeholder="Password" id="pass" required />
                <button type="submit" name="signIn" class="button1">Log In</button>
            </form>
            <div class="register-link">
                Don't have an account? <a href="reg.php">Register here</a>
            </div>
        </div>
    </div>

    <?php if ($login_error): ?>
    <script>
        alert("Wrong User ID or password!");
    </script>
    <?php endif; ?>
</body>
</html>

