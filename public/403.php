<?php
/**
 * 403 Forbidden - Access Denied - FMS
 */
http_response_code(403);
$base_url = defined('BASE_URL') ? BASE_URL : '/mini/FMS';
$desc = isset($error_message) && is_string($error_message) && !empty($error_message)
    ? $error_message
    : 'You do not have permission to access this page with your current active role.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Denied | File Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0b353d;
            --secondary: #0e454f;
            --accent: #e74c3c;
            --bg-start: #f8fafc;
            --bg-end: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, var(--bg-start), var(--bg-end));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            padding: 20px;
        }
        .error-card {
            background: #ffffff;
            border-top: 4px solid var(--accent);
            border-radius: 12px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
            max-width: 520px;
            width: 100%;
            padding: 48px 40px;
            text-align: center;
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .icon-wrap {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: rgba(231, 76, 60, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 36px;
        }
        .error-code {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1;
            color: var(--primary);
            letter-spacing: -0.05em;
            margin-bottom: 8px;
        }
        .error-title {
            font-size: 1.35rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 12px;
        }
        .error-desc {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            font-size: 0.92rem;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            border: 1px solid var(--primary);
        }
        .btn-primary:hover {
            background: var(--secondary);
            border-color: var(--secondary);
            transform: translateY(-1px);
        }
        .btn-outline {
            background: #ffffff;
            color: var(--text-dark);
            border: 1px solid #cbd5e1;
        }
        .btn-outline:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }
        .footer-note {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            font-size: 0.8rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-wrap">
            <i class="fas fa-lock"></i>
        </div>
        <div class="error-code">403</div>
        <h1 class="error-title">Access Denied</h1>
        <p class="error-desc"><?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?></p>
        <div class="btn-group">
            <a href="<?= htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8') ?>/public/index.php?route=auth/select_role" class="btn btn-primary">
                <i class="fas fa-user-tag"></i> Switch Role
            </a>
            <a href="<?= htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8') ?>/public/index.php?route=dashboard" class="btn btn-outline">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>
        <div class="footer-note">
            GMR Institute of Technology &bull; File Management System
        </div>
    </div>
</body>
</html>
