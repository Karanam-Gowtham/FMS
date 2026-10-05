<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Research Analytics Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0b353d; --secondary: #0e454f; --accent: #17a2b8; --light: #f4f7f6; --text: #333; }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: linear-gradient(rgba(244, 247, 246, 0.9), rgba(226, 232, 240, 0.9)), url('https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Top Nav inside Login to match */
        .main-nav { background: #fff; padding: 15px 40px; display: flex; align-items: center; justify-content: space-between; position: fixed; top: 0; width: 100%; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 100; }
        .nav-brand { font-size: 1.1rem; font-weight: bold; color: var(--primary); display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .nav-brand span { font-size: 0.75rem; color: #64748b; font-weight: normal; }

        .login-card {
            background: #ffffff;
            border-top: 4px solid var(--primary);
            padding: 40px;
            border-radius: 8px;
            color: #333;
            width: 400px;
            max-width: 90vw;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-out;
            margin-top: 60px; /* Offset for nav */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card h1 { font-size: 1.8rem; margin-bottom: 5px; color: var(--primary); text-align: center; }
        .login-card .subtitle { color: #64748b; font-size: 0.9rem; margin-bottom: 30px; text-align: center; font-weight: 500; letter-spacing: 1px; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 0.85rem; color: var(--primary); margin-bottom: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            background: #fff;
            color: #333;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
        }
        
        .form-group input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.15); }
        .form-group input::placeholder { color: #94a3b8; }

        .btn-submit {
            width: 100%;
            background: var(--accent);
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .btn-submit:hover { background: #138496; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(23,162,184,0.3); }
        .btn-submit:active { transform: translateY(0); }

        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            color: #64748b;
        }
        .register-link a { color: var(--accent); text-decoration: none; font-weight: bold; }
        .register-link a:hover { text-decoration: underline; }

        .error-msg {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
            padding: 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    
    <nav class="main-nav">
        <a href="<?= BASE_URL ?>/" class="nav-brand">
            <i class="fas fa-university"></i>
            <div>
                GMR Institute of Technology<br>
                <span>Return to Analytics Portal</span>
            </div>
        </a>
    </nav>

    <div class="login-card">
        <h1>Institutional Login</h1>
        <div class="subtitle">FILE MANAGEMENT SYSTEM</div>

        <?php if ($error): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
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

            <button type="submit" class="btn-submit">Secure Sign In</button>
        </form>

        <div class="register-link">
            New faculty member? <a href="<?= BASE_URL ?>/public/index.php?route=auth/register">Register here</a>
        </div>
    </div>
</body>
</html>