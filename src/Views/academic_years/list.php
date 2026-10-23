<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Years — File Management System (FMS)</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/dashboard.css">
</head>
<body>
    <div class="topbar">
        <div class="brand">FMS <span style="font-size: 0.75rem; font-weight: normal; opacity: 0.8;">(File Management System)</span></div>
        <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($auth['full_name']) ?></span>
            <a href="<?= htmlspecialchars(is_array($auth['active_role'] ?? null) ? get_role_landing_url($auth['active_role']) : BASE_URL . '/public/index.php?route=dashboard') ?>" class="btn-switch">Back to Dashboard</a>
            <a href="<?= BASE_URL ?>/public/index.php?route=auth/logout" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="main">
        <div class="welcome-card" style="margin-bottom: 2rem;">
            <h1>🎓 Academic Years</h1>
            <p class="greeting">Select an academic year to manage NBA data and criteria.</p>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 0.85rem 1.2rem; border-radius: 6px; margin-bottom: 1.2rem; font-weight: 500;">
                ✓ <?= htmlspecialchars($success_msg) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 0.85rem 1.2rem; border-radius: 6px; margin-bottom: 1.2rem; font-weight: 500;">
                ⚠ <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>

        <?php if ($can_manage): ?>
        <div style="margin-bottom: 2rem;">
            <form action="" method="POST" id="addYearForm" style="display: flex; gap: 1rem; align-items: center; background: white; padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <?= csrfField() ?>
                <label for="new_year"><strong>Add Academic Year:</strong></label>
                <input type="text" name="academic_year" id="new_year" placeholder="e.g. 2026-27" required style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                <button type="submit" style="padding: 0.5rem 1rem; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer;">Add Year</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="nav-grid">
            <?php foreach ($academic_years as $year): ?>
            <a href="<?= BASE_URL ?>/public/index.php?route=nba/dashboard&amp;year=<?= urlencode($year['year_label']) ?>" class="nav-link" style="justify-content: center; text-align: center; font-size: 1.1rem; padding: 2rem;">
                <strong><?= htmlspecialchars($year['year_label']) ?></strong>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const addYearForm = document.getElementById('addYearForm');
        if (addYearForm) {
            let isSubmitting = false;
            addYearForm.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return false;
                }
                if (addYearForm.checkValidity()) {
                    isSubmitting = true;
                    const btn = addYearForm.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerText = 'Adding...';
                    }
                }
            });
        }
    });
    </script>
</body>
</html>
