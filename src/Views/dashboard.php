<?php
$dept_name_display = $active_role ? ($active_role['dept_name'] ?? 'Unknown') : 'Unknown';
$role_name_display = $active_role ? ($active_role['role_name'] ?? 'Unknown') : 'Unknown';

// Determine breadcrumb based on role
if ($active_role && in_array((int)$active_role['role_id'], [ROLE_FACULTY], true)) {
    $breadcrumb = "Faculty Dashboard ($dept_name_display)";
} elseif ($active_role && in_array((int)$active_role['role_id'], [ROLE_HOD], true)) {
    $breadcrumb = "HOD Dashboard ($dept_name_display)";
} elseif ($active_role) {
    $breadcrumb = "$role_name_display Dashboard ($dept_name_display)";
} else {
    $breadcrumb = "Dashboard";
}

$current_view = (isset($_GET['view']) && is_string($_GET['view'])) ? trim($_GET['view']) : '';
$role_id = $active_role ? (int)$active_role['role_id'] : 0;
$is_admin = ($role_id === ROLE_ADMIN);
$is_faculty = ($role_id === ROLE_FACULTY);
$is_reviewer = in_array($role_id, [ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_RND_DEAN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true);
$is_dept_manager = in_array($role_id, [ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_JUNIOR_ASSISTANT, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($breadcrumb ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?> - FMS</title>
    <link rel="icon" href="data:,">
    <script src="https://cdn.jsdelivr.net/npm/chart.js" crossorigin="anonymous" defer></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/dashboard.css">
</head>
<body>
<?php
include_once HEADER;
?>

<main class="main-content">
    <div class="container12">
        <?php include __DIR__ . '/partials/alerts.php'; ?>
        <?php include __DIR__ . '/partials/role_switcher.php'; ?>

        <?php if ($is_faculty): ?>
            <?php include __DIR__ . '/dashboard/faculty_section.php'; ?>
        <?php elseif ($is_admin): ?>
            <?php include __DIR__ . '/dashboard/admin_section.php'; ?>
        <?php elseif ($is_reviewer || $is_dept_manager): ?>
            <?php include __DIR__ . '/dashboard/reviewer_section.php'; ?>
        <?php else: ?>
            <div class="header-title">
                <h1>Quick Actions</h1>
            </div>
            <div class="feedback-grid">
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/list" class="feedback-card">
                    <div class="card-content"><h3>My Documents</h3></div>
                </a>
            </div>
        <?php endif; ?>

        <?php include __DIR__ . '/dashboard/submissions_table.php'; ?>
    </div>
</main>
</body>
</html>
