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
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/public/index.php">Dashboard</a> &raquo; NBA Module
    </div>

    <div class="header-row">
        <h1>NBA Criteria Management</h1>
    </div>

    <!-- Filters -->
    <div class="filters">
        <div class="form-group">
            <label for="year">Academic Year</label>
            <select name="year" id="year" onchange="window.location.href='?route=nba/dashboard&year=' + this.value">
                <?php foreach ($academic_years as $y): ?>
                    <option value="<?= $y ?>" <?= $y === $filter_year ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Criteria Grid -->
    <div class="criteria-grid">
        <?php foreach ($nba_criteria as $num => $title): ?>
            <?php 
                // Currently Criteria 1, 2, 3, and 4 are built in the new system.
                // Others will show as pending configuration.
                $is_built = in_array($num, [1, 2, 3, 4]); 
                $link = $is_built ? "?route=nba/criterion&id={$num}&year={$filter_year}" : "#";
                $status_class = $is_built ? "in-progress" : "pending";
                $status_text = $is_built ? "In Progress" : "Pending Config";
            ?>
            <a href="<?= $link ?>" class="criteria-card" <?= !$is_built ? 'onclick="alert(\'This criterion module is pending configuration.\'); return false;"' : '' ?>>
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
