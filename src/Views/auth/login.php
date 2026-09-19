
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — FMS</title>
    <meta name="description" content="Faculty Management System - Login">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-image: url('<?= BASE_URL ?>/assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding-top: 120px;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 0;
        }

        .login-card {
            position: relative;
            z-index: 1;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px 36px;
            border-radius: 16px;
            color: #f1f5f9;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            width: 380px;
            max-width: 90vw;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card h1 {
            font-size: 1.6em;
            font-weight: 600;
            margin-bottom: 6px;
            color: #e2e8f0;
        }

        .login-card .subtitle {
            color: #94a3b8;
            font-size: 0.85em;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 0.82em;
            color: #94a3b8;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.06);
            color: #f1f5f9;
            font-size: 0.95em;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .form-group input::placeholder {
            color: #64748b;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            letter-spacing: 0.02em;
            transition: transform 0.15s, box-shadow 0.2s;
            margin-top: 6px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.85em;
            margin-bottom: 18px;
        }

        .footer-links {
            margin-top: 20px;
            color: #64748b;
            font-size: 0.85em;
        }

        .footer-links a {
            color: #60a5fa;
            text-decoration: none;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <?php include_once HEADER; ?>
    <div class="login-card">
        <h1>FMS Login</h1>
        <p class="subtitle">Faculty Management System</p>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=auth/login" id="loginForm">
            <?= csrfField() ?>

            <div class="form-group">
                <label for="identifier">User ID or Email</label>
                <input type="text" id="identifier" name="identifier" placeholder="e.g. cse-hod or user@gmrit.edu.in"
                    value="<?= htmlspecialchars($_POST['identifier'] ?? '') ?>" required autocomplete="username"
                    autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required
                    autocomplete="current-password">
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="footer-links">
            New faculty? <a href="<?= BASE_URL ?>/public/index.php?route=auth/register">Register here</a>
        </div>
    </div>
</body>

</html>