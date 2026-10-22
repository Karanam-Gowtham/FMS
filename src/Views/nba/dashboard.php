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

        <?php if (!empty($pending_approvals)): ?>
        <!-- PENDING APPROVALS -->
        <div style="margin-top: 1rem; margin-bottom: 2rem;">
            <h2 style="font-size: 1.25rem; margin-bottom: 1rem; color: #1f2937;">Pending Review Queue <span style="background:#ef4444; color:white; padding:2px 8px; border-radius:999px; font-size:0.8rem; margin-left:10px;"><?= count($pending_approvals) ?></span></h2>
            <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e5e7eb; background: #f8fafc;">
                            <th style="padding: 12px 16px; font-weight: 600; color: #475569;">File Title</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Type</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Uploaded By</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Date</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_approvals as $doc): ?>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px 16px; color: #2563eb; font-weight: 500;">
                                <?= htmlspecialchars($doc['title']) ?>
                            </td>
                            <td style="padding: 12px 16px; color: #64748b;">
                                <?= htmlspecialchars($doc['type_label'] ?? $doc['type_id']) ?>
                            </td>
                            <td style="padding: 12px 16px; color: #64748b;">
                                <?= htmlspecialchars($doc['uploader_name'] ?? 'Unknown') ?>
                            </td>
                            <?php 
                            $event_dt = (!empty($doc['event_date']) && $doc['event_date'] !== '0000-00-00') ? $doc['event_date'] : $doc['created_at'];
                            ?>
                            <td style="padding: 12px 16px; color: #64748b;">
                                <div style="font-weight: 600; color: #1e293b;"><?= date('M d, Y', strtotime($event_dt)) ?></div>
                                <?php if (!empty($doc['event_date']) && date('Y-m-d', strtotime($doc['event_date'])) !== date('Y-m-d', strtotime($doc['created_at']))): ?>
                                    <div style="font-size: 0.72rem; color: #94a3b8;" title="Uploaded on <?= date('M d, Y', strtotime($doc['created_at'])) ?>">Up: <?= date('M d, Y', strtotime($doc['created_at'])) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px 16px;">
                                <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&id=<?= $doc['doc_id'] ?>&mode=review" style="background: #10b981; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.875rem;">Review</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filters">
            <div class="form-group">
                <label for="year">Academic Year</label>
                <select name="year" id="year" onchange="window.location.href='<?= BASE_URL ?>/public/index.php?route=nba/dashboard&year=' + encodeURIComponent(this.value)">
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
                // Currently Criteria 1 through 9 are built in the new system.
                // Others will show as pending configuration.
                $is_built = in_array($num, [1, 2, 3, 4, 5, 6, 7, 8, 9]);
                $link = $is_built ? BASE_URL . "/public/index.php?route=nba/criterion&id=" . (int)$num . "&year=" . urlencode($filter_year) : "#";
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