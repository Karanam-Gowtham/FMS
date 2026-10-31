<?php
/**
 * Reusable NBA Criterion Page Header Partial
 *
 * Variables required:
 * - $page_title (string)
 * - $criterion_num (int|string)
 * - $criterion_title (string)
 * - $year (string)
 * - $departments (array)
 * - $dept_id (int)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'NBA Criterion', ENT_QUOTES, 'UTF-8') ?> &mdash; FMS</title>
    <link href="<?= BASE_URL ?>/assets/css/layout.css?v=2.1" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/nba_module.css?v=2.2" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="container">
    <div style="margin-bottom: 1rem;">
        <a href="<?= BASE_URL ?>/public/index.php?route=nba/dashboard&amp;year=<?= urlencode($year) ?>" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; color: #2563eb; font-weight: 500; font-size: 0.9rem;">
            &larr; Back to NBA Dashboard (<?= htmlspecialchars($year) ?>)
        </a>
    </div>

    <div class="header-row">
        <h1>Criterion <?= htmlspecialchars($criterion_num) ?>: <?= htmlspecialchars($criterion_title) ?></h1>
        <p>Academic Year: <strong><?= htmlspecialchars($year) ?></strong></p>
    </div>

    <div style="margin-bottom: 2rem; background: #fff; padding: 1rem; border-radius: 8px; border: 1px solid #dee2e6; display: flex; align-items: center; gap: 15px;">
        <label for="level-select" style="font-weight: 600; color: #495057;">Select Scope / Department:</label>
        <select id="level-select" style="padding: 8px; border: 1px solid #ced4da; border-radius: 4px; min-width: 250px;" onchange="changeDepartment()">
            <?php foreach ($departments as $d): ?>
                <option value="<?= (int)$d['dept_id'] ?>" <?= (int)$d['dept_id'] === (int)$dept_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['dept_name'] === 'Complete University' ? 'Complete University (Institutional)' : 'Department: ' . $d['dept_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
