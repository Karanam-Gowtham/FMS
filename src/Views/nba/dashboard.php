<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> &mdash; FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/nba.css">
</head>

<body class="bg-light">

    <?php include __DIR__ . '/../../../includes/header.php'; ?>

    <div class="container">

        <div class="header-row">
            <h1>NBA Criteria Management</h1>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div style="background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; padding: 0.85rem 1.25rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.95rem; display: flex; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <span style="font-weight: 500;">✓ <?= htmlspecialchars($success_msg) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 0.85rem 1.25rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.95rem; display: flex; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <span style="font-weight: 500;">⚠ <?= htmlspecialchars($error_msg) ?></span>
            </div>
        <?php endif; ?>

        <?php include __DIR__ . '/../partials/pending_review_table.php'; ?>

        <!-- Filters -->
        <div class="filters">
            <div class="form-group">
                <label for="year">Academic Year</label>
                <select name="year" id="year" onchange="window.location.href='<?= BASE_URL ?>/public/index.php?route=nba/dashboard&year=' + encodeURIComponent(this.value)">
                    <?php foreach ($academic_years as $y): ?>
                        <option value="<?= htmlspecialchars($y) ?>" <?= $y === $filter_year ? 'selected' : '' ?>><?= htmlspecialchars($y) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Criteria Grid -->
        <div class="criteria-grid">
            <?php foreach ($nba_criteria as $num => $title): ?>
                <?php
                // Currently Criteria 1 through 9 are built in the new system.
                // Others will show as pending configuration.
                $is_built = in_array($num, [1, 2, 3, 4, 5, 6, 7, 8, 9]);
                $link = $is_built ? BASE_URL . "/public/index.php?route=nba/criterion&id=" . (int)$num . "&year=" . urlencode($filter_year) : "#";
                $status_class = $is_built ? "in-progress" : "pending";
                $status_text = $is_built ? "In Progress" : "Pending Config";
                ?>
                <a href="<?= htmlspecialchars($link, ENT_QUOTES, 'UTF-8') ?>" class="criteria-card" <?= !$is_built ? 'onclick="alert(\'This criterion module is pending configuration.\'); return false;"' : '' ?>>
                    <div class="criteria-number">Criterion <?= $num ?></div>
                    <h3 class="criteria-title"><?= htmlspecialchars($title) ?></h3>
                    <div class="criteria-status">
                        Status: <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

</body>

</html>