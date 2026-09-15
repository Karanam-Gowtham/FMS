<?php
/**
 * FMS Role Selection Page
 * 
 * Shown when a user has multiple role assignments.
 * The user selects which role to activate for this session.
 * Role and department are validated server-side — never trusted from the browser.
 */
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/legacy_bridge.php';

// If already authenticated with a role, go to dashboard
if (auth_is_logged_in()) {
    header("Location: " . BASE_URL . "/pages/dashboard.php");
    exit();
}

// Must have pending auth data from login
if (!isset($_SESSION['_pending_auth'])) {
    header("Location: " . BASE_URL . "/pages/login.php");
    exit();
}

$user = $_SESSION['_pending_auth'];
$roles = $user['roles'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    $selected_urid = (int)($_POST['user_role_id'] ?? 0);

    // Validate: the selected user_role_id must exist in the user's actual roles
    $valid_role = null;
    foreach ($roles as $role) {
        if ((int)$role['user_role_id'] === $selected_urid) {
            $valid_role = $role;
            break;
        }
    }

    if ($valid_role) {
        // Create canonical session
        auth_create_session($user, $valid_role);
        auth_update_last_login($conn, $user['user_id']);

        // Clear pending auth
        unset($_SESSION['_pending_auth']);

        // Bridge for legacy pages
        legacy_bridge_sync();

        header("Location: " . get_role_landing_url($valid_role));
        exit();
    } else {
        $error = 'Invalid role selection. Please try again.';
    }
}

// Group roles for display
$display_roles = get_distinct_role_assignments($roles);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Role — FMS</title>
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
            padding-top: 80px;
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
            width: 440px;
            max-width: 90vw;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card h1 { font-size: 1.4em; margin-bottom: 6px; color: #e2e8f0; }
        .card .subtitle { color: #94a3b8; font-size: 0.85em; margin-bottom: 24px; }

        .error-msg {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.85em;
            margin-bottom: 18px;
        }

        .role-list { list-style: none; padding: 0; }

        .role-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 14px;
            text-align: left;
        }

        .role-item:hover {
            background: rgba(59, 130, 246, 0.15);
            border-color: rgba(59, 130, 246, 0.4);
            transform: translateX(4px);
        }

        .role-item input[type="radio"] { display: none; }

        .role-item input[type="radio"]:checked + .role-info {
            color: #60a5fa;
        }

        .role-item input[type="radio"]:checked ~ .role-check {
            opacity: 1;
        }

        .role-info { flex: 1; }
        .role-name { font-weight: 600; font-size: 1em; }
        .role-dept { font-size: 0.82em; color: #94a3b8; margin-top: 2px; }

        .role-check {
            opacity: 0;
            color: #3b82f6;
            font-size: 1.2em;
            transition: opacity 0.15s;
        }

        .btn-continue {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            margin-top: 16px;
            transition: transform 0.15s, box-shadow 0.2s;
        }

        .btn-continue:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4);
        }

        .back-link {
            margin-top: 16px;
            font-size: 0.85em;
        }

        .back-link a { color: #60a5fa; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Select Your Role</h1>
        <p class="subtitle">Welcome, <?= htmlspecialchars($user['full_name']) ?>. You have multiple roles.</p>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" id="roleForm">
            <?= csrfField() ?>

            <ul class="role-list">
                <?php foreach ($display_roles as $i => $role): ?>
                    <li class="role-item" onclick="document.getElementById('role_<?= $i ?>').checked = true;">
                        <input type="radio" name="user_role_id"
                               id="role_<?= $i ?>"
                               value="<?= (int)$role['user_role_id'] ?>"
                               <?= $i === 0 ? 'checked' : '' ?>
                               required>
                        <div class="role-info">
                            <div class="role-name"><?= htmlspecialchars($role['role_name']) ?></div>
                            <div class="role-dept">
                                <?= $role['dept_name'] ? htmlspecialchars($role['dept_name']) : 'All Departments' ?>
                            </div>
                        </div>
                        <span class="role-check">✓</span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <button type="submit" class="btn-continue">Continue</button>
        </form>

        <div class="back-link">
            <a href="<?= BASE_URL ?>/pages/login.php">← Back to Login</a>
        </div>
    </div>
</body>
</html>
