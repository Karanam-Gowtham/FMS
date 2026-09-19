<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile — FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/dashboard.css">
    <style>
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
        .form-group input { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; }
        .success-msg { background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }
        .error-msg { background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="brand">FMS</div>
        <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($auth['full_name']) ?></span>
            <a href="<?= BASE_URL ?>/public/index.php?route=dashboard" class="btn-switch">Back to Dashboard</a>
            <a href="<?= BASE_URL ?>/public/index.php?route=auth/logout" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="main">
        <div class="welcome-card" style="margin-bottom: 2rem;">
            <h1>👤 Edit Profile</h1>
            <p class="greeting">Update your personal and academic information.</p>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 8px; max-width: 600px;">
            <?php if (!empty($success)): ?>
                <div class="success-msg"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="error-msg"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <?= csrfField() ?>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($profile['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Highest Degree</label>
                    <input type="text" name="highest_degree" value="<?= htmlspecialchars($profile['highest_degree'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>University</label>
                    <input type="text" name="university" value="<?= htmlspecialchars($profile['university'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Specialization</label>
                    <input type="text" name="specialization" value="<?= htmlspecialchars($profile['specialization'] ?? '') ?>">
                </div>
                <button type="submit" style="padding: 0.5rem 1rem; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 1rem;">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>
