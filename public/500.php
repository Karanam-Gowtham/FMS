<?php
/**
 * 500 Internal Server Error - FMS
 */
http_response_code(500);
$base_url = defined('BASE_URL') ? BASE_URL : '/mini/FMS';
$desc = isset($error_message) && is_string($error_message) && !empty($error_message)
    ? $error_message
    : 'An internal error occurred while processing your request. The issue has been logged for review by system administrators.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Server Error | File Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0b353d;
            --secondary: #0e454f;
            --danger: #dc2626;
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
            border-top: 4px solid var(--danger);
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
            background: rgba(220, 38, 38, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--danger);
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
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="error-code">500</div>
        <h1 class="error-title">Unexpected Server Error</h1>
        <p class="error-desc"><?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?></p>
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
