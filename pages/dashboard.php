<?php
/**
 * FMS Dashboard
 * 
 * Authentication/role-aware landing page.
 * Proves: user is authenticated, active role resolved, department resolved.
 * Does NOT implement the full document workflow yet (that's later phases).
 */
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/legacy_bridge.php';

require_login();

// Sync legacy session for any legacy includes
legacy_bridge_sync();

$auth = auth_context();
$active_role = auth_active_role();

// Automatically redirect from this generic placeholder to the specific role dashboard
if ($active_role) {
    header("Location: " . get_role_landing_url($active_role));
    exit();
}

$user_roles = $auth['roles'];

// Role display name mapping
$role_icons = [
    ROLE_ADMIN               => '🛡️',
    ROLE_IQAC                => '📋',
    ROLE_HOD                 => '👔',
    ROLE_FACULTY             => '📚',
    ROLE_DEPT_COORDINATOR    => '📂',
    ROLE_CENTRAL_COORDINATOR => '🏛️',
    ROLE_JUNIOR_ASSISTANT    => '📝',
    ROLE_RND_DEAN            => '🎓',
];

$role_icon = $role_icons[$active_role['role_id']] ?? '👤';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — FMS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
        }

        .topbar {
            background: rgba(15, 23, 42, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 14px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(8px);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .topbar .brand {
            font-size: 1.2em;
            font-weight: 700;
            color: #3b82f6;
            letter-spacing: 0.03em;
        }

        .topbar .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 0.88em;
        }

        .topbar .user-name { color: #cbd5e1; }
        .topbar .user-role {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.82em;
            font-weight: 600;
        }

        .btn-logout, .btn-switch {
            padding: 6px 16px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 6px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.82em;
            transition: all 0.2s;
            background: none;
            cursor: pointer;
        }

        .btn-logout:hover { border-color: #ef4444; color: #ef4444; }
        .btn-switch:hover { border-color: #3b82f6; color: #3b82f6; }

        .main {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 24px;
        }

        .welcome-card {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(16, 185, 129, 0.08));
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 32px;
        }

        .welcome-card h1 {
            font-size: 1.6em;
            margin-bottom: 8px;
        }

        .welcome-card .greeting { color: #94a3b8; font-size: 0.95em; }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .info-tile {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 20px;
        }

        .info-tile .label {
            font-size: 0.75em;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 6px;
        }

        .info-tile .value {
            font-size: 1.1em;
            font-weight: 600;
            color: #f1f5f9;
        }

        .nav-section h2 {
            font-size: 1.1em;
            color: #94a3b8;
            margin-bottom: 16px;
            font-weight: 500;
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        .nav-link {
            display: block;
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 18px;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.92em;
        }

        .nav-link:hover {
            border-color: rgba(59, 130, 246, 0.4);
            background: rgba(59, 130, 246, 0.08);
            transform: translateY(-2px);
        }

        .nav-link .icon { font-size: 1.4em; margin-bottom: 6px; display: block; }

        .debug-section {
            margin-top: 40px;
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
        }

        .debug-section summary {
            color: #64748b;
            font-size: 0.82em;
            cursor: pointer;
            user-select: none;
        }

        .debug-section pre {
            margin-top: 12px;
            color: #94a3b8;
            font-size: 0.78em;
            white-space: pre-wrap;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="brand">FMS</div>
        <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($auth['full_name']) ?></span>
            <span class="user-role"><?= $role_icon ?> <?= htmlspecialchars($active_role['role_name']) ?></span>
            <?php if (count($user_roles) > 1): ?>
                <a href="<?= BASE_URL ?>/pages/select_role.php" class="btn-switch"
                   onclick="event.preventDefault(); switchRole();">Switch Role</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/pages/logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="main">
        <div class="welcome-card">
            <h1><?= $role_icon ?> Welcome, <?= htmlspecialchars($auth['full_name']) ?></h1>
            <p class="greeting">You are logged in as <strong><?= htmlspecialchars($active_role['role_name']) ?></strong>
            <?php if ($active_role['dept_name']): ?>
                — <?= htmlspecialchars($active_role['dept_name']) ?> Department
            <?php endif; ?>
            </p>
        </div>

        <div class="info-grid">
            <div class="info-tile">
                <div class="label">User ID</div>
                <div class="value"><?= $auth['user_id'] ?></div>
            </div>
            <div class="info-tile">
                <div class="label">Email</div>
                <div class="value"><?= htmlspecialchars($auth['email']) ?></div>
            </div>
            <div class="info-tile">
                <div class="label">Active Role</div>
                <div class="value"><?= htmlspecialchars($active_role['role_name']) ?> (ID: <?= $active_role['role_id'] ?>)</div>
            </div>
            <div class="info-tile">
                <div class="label">Department</div>
                <div class="value"><?= $active_role['dept_name'] ? htmlspecialchars($active_role['dept_name']) . ' (ID: ' . $active_role['dept_id'] . ')' : 'N/A' ?></div>
            </div>
        </div>

        <?php
        // Get document stats for this user
        $pending_for_me = doc_list_pending_for_user($conn, $auth, 1, 0);
        $pending_count = $pending_for_me['total'];
        $my_docs = doc_list($conn, ['uploaded_by' => (int)$auth['user_id']], 1, 0);
        $my_doc_count = $my_docs['total'];
        ?>

        <div class="nav-section">
            <h2>Document Management</h2>
            <div class="nav-grid">
                <a href="<?= BASE_URL ?>/pages/documents/upload.php" class="nav-link">
                    <span class="icon">📤</span>
                    Upload Document
                </a>
                <a href="<?= BASE_URL ?>/pages/documents/my_uploads.php" class="nav-link">
                    <span class="icon">📄</span>
                    My Uploads<?= $my_doc_count > 0 ? " ($my_doc_count)" : '' ?>
                </a>
                <a href="<?= BASE_URL ?>/pages/documents/list.php" class="nav-link">
                    <span class="icon">📋</span>
                    Browse Documents
                </a>
                <?php if ($pending_count > 0): ?>
                <a href="<?= BASE_URL ?>/pages/documents/list.php?mode=pending_approval" class="nav-link" style="border-color: rgba(234, 179, 8, 0.4); background: rgba(234, 179, 8, 0.06);">
                    <span class="icon">⏳</span>
                    Pending My Approval <strong style="color: #eab308;">(<?= $pending_count ?>)</strong>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="nav-section">
            <h2>Quick Navigation</h2>
            <div class="nav-grid">
                <a href="<?= get_role_landing_url($active_role) ?>" class="nav-link">
                    <span class="icon">📁</span>
                    Legacy <?= htmlspecialchars($active_role['role_name']) ?> Portal
                </a>
                <?php if ((int)$active_role['role_id'] === ROLE_ADMIN): ?>
                <a href="<?= BASE_URL ?>/pages/admin/users.php" class="nav-link">
                    <span class="icon">👥</span>
                    Manage Users
                </a>
                <?php endif; ?>
                <?php if (count($user_roles) > 1): ?>
                    <?php foreach ($user_roles as $r): ?>
                        <?php if ($r['user_role_id'] !== $active_role['user_role_id']): ?>
                            <a href="#" onclick="event.preventDefault(); activateRole(<?= $r['user_role_id'] ?>);" class="nav-link">
                                <span class="icon"><?= $role_icons[$r['role_id']] ?? '👤' ?></span>
                                Switch to <?= htmlspecialchars($r['role_name']) ?>
                                <?= $r['dept_name'] ? '(' . htmlspecialchars($r['dept_name']) . ')' : '' ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <details class="debug-section">
            <summary>🔍 Session Debug Info (development only)</summary>
            <pre><?php
                $debug = [
                    'auth_context' => $auth,
                    'session_id' => session_id(),
                    'login_time' => date('Y-m-d H:i:s', $auth['login_time'] ?? 0),
                ];
                // Redact password info
                echo htmlspecialchars(json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            ?></pre>
        </details>
    </div>

    <script>
    function switchRole() {
        // Clear auth and go to role selection
        window.location.href = '<?= BASE_URL ?>/pages/select_role.php';
    }

    function activateRole(userRoleId) {
        // Use a form POST to switch role securely
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= BASE_URL ?>/pages/switch_role.php';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_role_id';
        input.value = userRoleId;
        form.appendChild(input);

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_csrf_token';
        csrf.value = '<?= csrfToken() ?>';
        form.appendChild(csrf);

        document.body.appendChild(form);
        form.submit();
    }
    </script>
</body>
</html>
