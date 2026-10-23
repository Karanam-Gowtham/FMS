<?php
/**
 * 404 Page Not Found - FMS
 */
http_response_code(404);
$base_url = defined('BASE_URL') ? BASE_URL : '/mini/FMS';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found | File Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0b353d;
            --secondary: #0e454f;
            --accent: #17a2b8;
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
            background: rgba(23, 162, 184, 0.1);
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
            background: transparent;
            color: var(--primary);
            border: 1px solid #cbd5e1;
        }
        .btn-outline:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-wrap">
            <i class="fa-solid fa-compass"></i>
        </div>
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-desc">
            The page or document route you are looking for does not exist or may have been relocated.
        </p>
        <div class="btn-group">
            <a href="<?= htmlspecialchars($base_url) ?>/public/index.php?route=dashboard" class="btn btn-primary">
                <i class="fa-solid fa-gauge-high"></i> Return to Dashboard
            </a>
            <a href="<?= htmlspecialchars($base_url) ?>/index.php" class="btn btn-outline">
                <i class="fa-solid fa-house"></i> Public Portal
            </a>
        </div>
    </div>
</body>
</html>
