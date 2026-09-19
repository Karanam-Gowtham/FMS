<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — FMS</title>
    <!-- Use the new external stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/dashboard.css">
</head>
<body>
    <div class="topbar">
        <div class="brand">FMS</div>
        <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($auth['full_name']) ?></span>
            <?php if ($active_role): ?>
                <span class="user-role"><?= $role_icon ?> <?= htmlspecialchars($active_role['role_name']) ?></span>
            <?php endif; ?>
            <?php if (count($user_roles) > 1): ?>
                <a href="<?= BASE_URL ?>/pages/select_role.php" class="btn-switch"
                   onclick="event.preventDefault(); switchRole('<?= BASE_URL ?>');">Switch Role</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/pages/logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="main">
        <div class="welcome-card">
            <h1><?= $role_icon ?> Welcome, <?= htmlspecialchars($auth['full_name']) ?></h1>
            <?php if ($active_role): ?>
            <p class="greeting">You are logged in as <strong><?= htmlspecialchars($active_role['role_name']) ?></strong>
            <?php if ($active_role['dept_name']): ?>
                — <?= htmlspecialchars($active_role['dept_name']) ?> Department
            <?php endif; ?>
            </p>
            <?php else: ?>
            <p class="greeting">You have no active role selected.</p>
            <?php endif; ?>
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
            <?php if ($active_role): ?>
            <div class="info-tile">
                <div class="label">Active Role</div>
                <div class="value"><?= htmlspecialchars($active_role['role_name']) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <div class="nav-section">
            <h2>Quick Actions</h2>
            <div class="nav-grid">
                <a href="<?= BASE_URL ?>/public/index.php?route=profile/edit" class="nav-link" style="border-color: rgba(59, 130, 246, 0.4); background: rgba(59, 130, 246, 0.05);">
                    <span class="icon">👤</span>
                    Edit Profile
                </a>
                
                <?php if ($active_role && in_array($active_role['role_id'], [ROLE_ADMIN, ROLE_HOD, ROLE_FACULTY, ROLE_DEPT_COORDINATOR, ROLE_JUNIOR_ASSISTANT])): ?>
                <a href="<?= BASE_URL ?>/public/index.php?route=academic_years/list" class="nav-link" style="border-color: rgba(16, 185, 129, 0.4); background: rgba(16, 185, 129, 0.05);">
                    <span class="icon">🎓</span>
                    Academic Years / NBA
                </a>
                <?php endif; ?>
                
                <a href="<?= BASE_URL ?>/pages/documents/list.php" class="nav-link">
                    <span class="icon">📁</span>
                    My Documents
                </a>

                <?php if ($active_role && $active_role['role_id'] == ROLE_ADMIN): ?>
                <a href="<?= BASE_URL ?>/admin/manage_users.php" class="nav-link">
                    <span class="icon">👥</span>
                    Manage Users
                </a>
                <?php endif; ?>

                <?php if (count($user_roles) > 1): ?>
                    <?php foreach ($user_roles as $r): ?>
                        <?php if (!$active_role || $r['user_role_id'] !== $active_role['user_role_id']): ?>
                            <a href="#" onclick="event.preventDefault(); activateRole(<?= $r['user_role_id'] ?>, '<?= BASE_URL ?>', '<?= csrfToken() ?>');" class="nav-link">
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

    <!-- External JS for the dashboard interactions -->
    <script src="<?= BASE_URL ?>/public/assets/js/dashboard.js"></script>
</body>
</html>
