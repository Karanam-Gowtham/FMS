<?php
/**
 * FMS Registration Page
 * 
 * CONSERVATIVE implementation:
 *   - Only Faculty (role_id=4) self-registration is allowed.
 *   - All other roles must be assigned by an Admin.
 *   - Passwords are hashed with password_hash().
 *   - Department selection from the database (academic depts only).
 *   - No privilege escalation possible — role is forced server-side.
 */
require_once __DIR__ . '/../core/bootstrap.php';

// Already logged in? Go to dashboard
if (auth_is_logged_in()) {
    $active = auth_active_role();
    if ($active) {
        header("Location: " . get_role_landing_url($active));
    } else {
        header("Location: " . BASE_URL . "/pages/select_role.php");
    }
    exit();
}

$error = '';
$success = '';

// Load academic departments
$dept_result = $conn->query("
    SELECT dept_id, dept_name FROM departments
    ORDER BY dept_name
");
$departments = [];
while ($row = $dept_result->fetch_assoc()) {
    $departments[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';
    $dept_id   = (int)($_POST['dept_id'] ?? 0);

    // Validation
    if (empty($full_name) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif ($dept_id <= 0) {
        $error = 'Please select a department.';
    } else {
        // Validate dept_id exists
        $dept_check = $conn->prepare("SELECT dept_id FROM departments WHERE dept_id = ?");
        $dept_check->bind_param("i", $dept_id);
        $dept_check->execute();
        if ($dept_check->get_result()->num_rows === 0) {
            $error = 'Invalid department selected.';
            $dept_check->close();
        } else {
            $dept_check->close();

            // Check if email already exists
            $email_check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $email_check->bind_param("s", $email);
            $email_check->execute();
            if ($email_check->get_result()->num_rows > 0) {
                $error = 'An account with this email already exists.';
            }
            $email_check->close();
        }
    }

    if (empty($error)) {
        // Hash password
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, status) VALUES (?, ?, ?, 'active')");
        $stmt->bind_param("sss", $full_name, $email, $hashed);

        if ($stmt->execute()) {
            $new_user_id = $conn->insert_id;
            $stmt->close();

            // Assign Faculty role (role_id=4) — server-side, not from form input
            $role_id = ROLE_FACULTY;
            $role_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, ?, ?)");
            $role_stmt->bind_param("iii", $new_user_id, $role_id, $dept_id);
            $role_stmt->execute();
            $role_stmt->close();

            $success = 'Registration successful! You can now log in.';
        } else {
            $error = 'Registration failed. Please try again.';
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — FMS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-image: url('<?= BASE_URL ?>/assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding-top: 60px;
            padding-bottom: 40px;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 0;
        }

        .card {
            position: relative;
            z-index: 1;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 36px;
            border-radius: 16px;
            color: #f1f5f9;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            width: 420px;
            max-width: 90vw;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card h1 { font-size: 1.4em; margin-bottom: 6px; color: #e2e8f0; }
        .card .subtitle { color: #94a3b8; font-size: 0.82em; margin-bottom: 24px; }

        .form-group {
            margin-bottom: 16px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 0.82em;
            color: #94a3b8;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.06);
            color: #f1f5f9;
            font-size: 0.92em;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group select option { background: #1e293b; color: #f1f5f9; }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .form-group input::placeholder { color: #64748b; }

        .btn-register {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            margin-top: 4px;
            transition: transform 0.15s, box-shadow 0.2s;
        }

        .btn-register:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.4);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.85em;
            margin-bottom: 16px;
        }

        .success-msg {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.85em;
            margin-bottom: 16px;
        }

        .footer-links { margin-top: 18px; font-size: 0.85em; color: #64748b; }
        .footer-links a { color: #60a5fa; text-decoration: none; }
        .footer-links a:hover { text-decoration: underline; }

        .role-note {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: #93c5fd;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.78em;
            margin-bottom: 18px;
            text-align: left;
        }
    </style>
</head>
<body>
    <?php include_once HEADER; ?>
    <div class="card">
        <h1>Faculty Registration</h1>
        <p class="subtitle">Create your FMS account</p>

        <div class="role-note">
            ℹ️ This form registers <strong>Faculty</strong> accounts only.
            For other roles (HOD, Coordinator, Admin), contact your administrator.
        </div>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-msg"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="" id="regForm">
            <?= csrfField() ?>

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name"
                       value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                       placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="you@gmrit.edu.in" required>
            </div>

            <div class="form-group">
                <label for="dept_id">Department</label>
                <select id="dept_id" name="dept_id" required>
                    <option value="">— Select Department —</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['dept_id'] ?>"
                            <?= (isset($_POST['dept_id']) && (int)$_POST['dept_id'] === (int)$d['dept_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['dept_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Minimum 6 characters" required minlength="6">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password"
                       placeholder="Re-enter your password" required>
            </div>

            <button type="submit" class="btn-register">Create Account</button>
        </form>

        <div class="footer-links">
            Already have an account? <a href="<?= BASE_URL ?>/pages/login.php">Sign in</a>
        </div>
    </div>
</body>
</html>
