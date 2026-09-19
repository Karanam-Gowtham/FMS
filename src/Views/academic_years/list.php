<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Years — FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/dashboard.css">
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
            <h1>🎓 Academic Years</h1>
            <p class="greeting">Select an academic year to manage NBA data and criteria.</p>
        </div>

        <?php if ($can_manage): ?>
        <div style="margin-bottom: 2rem;">
            <form action="" method="POST" style="display: flex; gap: 1rem; align-items: center; background: white; padding: 1rem; border-radius: 8px; shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <?= csrfField() ?>
                <label for="new_year"><strong>Add Academic Year:</strong></label>
                <input type="text" name="academic_year" id="new_year" placeholder="e.g. 2024-2025" required style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                <button type="submit" style="padding: 0.5rem 1rem; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer;">Add Year</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="nav-grid">
            <?php foreach ($academic_years as $year): ?>
            <a href="<?= BASE_URL ?>/nba/dashboard.php?year_id=<?= $year['id'] ?>" class="nav-link" style="justify-content: center; text-align: center; font-size: 1.1rem; padding: 2rem;">
                <strong><?= htmlspecialchars($year['year_range']) ?></strong>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
