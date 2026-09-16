<?php
/**
 * R&D Dean Dashboard
 *
 * Dedicated dashboard for the R&D Dean role. Provides:
 * - Summary statistics
 * - Department R&D document browsing (filtered by dept/year/category)
 * - Pending R&D Dean review queue with inline approve/reject
 * - Central R&D document listing with upload link
 *
 * All data comes from existing services: document_service, workflow_engine.
 * View/download links go to existing view.php, download.php.
 * Approve/reject forms POST to existing approve.php.
 * Upload links go to existing upload.php.
 *
 * URL: pages/rnd/dashboard.php
 */
require_once __DIR__ . '/../../core/bootstrap.php';

// â”€â”€ Authentication & Authorization â”€â”€
require_login();
$auth = auth_context();
$active_role = auth_active_role();
$active_role_id = $active_role ? (int)$active_role['role_id'] : 0;

if ($active_role_id !== ROLE_RND_DEAN) {
    http_response_code(403);
    echo '<p>Access denied. This page is restricted to the R&D Dean role.</p>';
    exit;
}

// â”€â”€ Filters from GET â”€â”€
$filter_dept_id = isset($_GET['dept']) ? (int)$_GET['dept'] : 0;
$filter_year_id = isset($_GET['year']) ? (int)$_GET['year'] : 0;
$filter_type    = isset($_GET['type']) ? trim($_GET['type']) : '';
$active_tab     = isset($_GET['tab'])  ? trim($_GET['tab'])  : 'dept_rnd';

// Server-side dept authorization (R&D Dean has global scope, validate_dept_filter handles this)
if ($filter_dept_id > 0) {
    $validated_dept = validate_dept_filter($filter_dept_id);
    if ($validated_dept === null) $filter_dept_id = 0;
}

// â”€â”€ Load lookup data â”€â”€
$all_depts       = doc_get_departments($conn);
$academic_years  = doc_get_academic_years($conn);
$active_year     = doc_get_active_year($conn);
$research_types  = doc_get_types($conn);   // Business classification: category='research'

// Default to active year if none selected
if ($filter_year_id === 0 && $active_year) {
    $filter_year_id = (int)$active_year['year_id'];
}

// â”€â”€ Build filter URL helper â”€â”€
function rnd_url(array $overrides = []): string {
    global $filter_dept_id, $filter_year_id, $filter_type, $active_tab;
    $params = [
        'dept' => $overrides['dept'] ?? $filter_dept_id,
        'year' => $overrides['year'] ?? $filter_year_id,
        'type' => $overrides['type'] ?? $filter_type,
        'tab'  => $overrides['tab']  ?? $active_tab,
    ];
    // Remove empty params
    $params = array_filter($params, fn($v) => $v !== '' && $v !== 0 && $v !== null);
    return BASE_URL . '/pages/rnd/dashboard.php' . ($params ? '?' . http_build_query($params) : '');
}

// â”€â”€ Summary Stats â”€â”€
$dept_rnd_filters = ['type_keys' => ['journal', 'conference', 'patent', 'fdp_attended', 'fdp_organised', 'conf_organised']];
if ($filter_dept_id > 0) $dept_rnd_filters['dept_id'] = $filter_dept_id;
if ($filter_year_id > 0) $dept_rnd_filters['year_id'] = $filter_year_id;

$all_rnd = doc_list($conn, $dept_rnd_filters, 0, 0);
$total_rnd = $all_rnd['total'];

$accepted_rnd = doc_list($conn, array_merge($dept_rnd_filters, ['status' => 'accepted']), 0, 0);
$rejected_rnd = doc_list($conn, array_merge($dept_rnd_filters, ['status' => 'rejected']), 0, 0);

// Pending R&D Dean review
$pending_result = doc_list_pending_for_user($conn, $auth, 200, 0);
$pending_count = $pending_result['total'];
$pending_docs = $pending_result['rows'];

// Central R&D documents
$central_filters = ['type_key' => 'central_rnd'];
if ($filter_year_id > 0) $central_filters['year_id'] = $filter_year_id;
$central_result = doc_list($conn, $central_filters, 200, 0);
$central_count = $central_result['total'];
$central_docs = $central_result['rows'];

// â”€â”€ Tab 1: Department R&D with type filter â”€â”€
$dept_rnd_list_filters = $dept_rnd_filters;
if ($filter_type !== '') $dept_rnd_list_filters['type_key'] = $filter_type;
$dept_rnd_result = doc_list($conn, $dept_rnd_list_filters, 50, 0);
$dept_rnd_docs = $dept_rnd_result['rows'];
$dept_rnd_total = $dept_rnd_result['total'];

// Count per research type for category cards
$type_counts = [];
foreach ($research_types as $rt) {
    $tc_filters = ['type_key' => $rt['type_key']];
    if ($filter_dept_id > 0) $tc_filters['dept_id'] = $filter_dept_id;
    if ($filter_year_id > 0) $tc_filters['year_id'] = $filter_year_id;
    $tc = doc_list($conn, $tc_filters, 0, 0);
    $type_counts[$rt['type_key']] = $tc['total'];
}

// â”€â”€ Find dept name for display â”€â”€
$selected_dept_name = 'All Departments';
if ($filter_dept_id > 0) {
    foreach ($all_depts as $d) {
        if ((int)$d['dept_id'] === $filter_dept_id) {
            $selected_dept_name = $d['dept_name'];
            break;
        }
    }
}

$selected_year_label = 'All Years';
if ($filter_year_id > 0) {
    foreach ($academic_years as $y) {
        if ((int)$y['year_id'] === $filter_year_id) {
            $selected_year_label = $y['year_label'];
            break;
        }
    }
}

$page_title = 'R&D Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> &mdash; FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <style>
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .breadcrumb { margin-bottom: 1rem; color: #6c757d; font-size: 0.9rem; }
        .breadcrumb a { color: #4a90d9; text-decoration: none; }
        .header-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; }

        /* Filters &mdash; matches list.php / my_uploads.php */
        .filters { display: flex; gap: 0.8rem; flex-wrap: wrap; margin-bottom: 1.5rem; align-items: end; }
        .filters .form-group { flex: 1; min-width: 150px; }
        .filters label { display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 0.2rem; }
        .filters select { width: 100%; padding: 0.45rem; border: 1px solid #ccc; border-radius: 5px; font-size: 0.9rem; }
        .filters select:focus { outline: none; border-color: #4a90d9; }

        /* Summary stat tiles */
        .rnd-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 1.5rem; }
        .rnd-stat-tile { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 16px; text-align: center; }
        .rnd-stat-tile .num { font-size: 1.6em; font-weight: 700; color: #333; }
        .rnd-stat-tile .lbl { font-size: 0.75rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px; }
        .rnd-stat-tile.st-pending .num { color: #856404; }
        .rnd-stat-tile.st-accepted .num { color: #155724; }
        .rnd-stat-tile.st-rejected .num { color: #721c24; }
        .rnd-stat-tile.st-central .num { color: #4a90d9; }

        /* Tabs &mdash; matches list.php */
        .tabs { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
        .tabs a { padding: 0.5rem 1rem; border: 1px solid #dee2e6; border-radius: 6px; text-decoration: none; color: #495057; font-weight: 500; font-size: 0.9rem; }
        .tabs a:hover { border-color: #4a90d9; color: #4a90d9; }
        .tabs a.active { background: #4a90d9; color: white; border-color: #4a90d9; }
        .tabs .badge { background: #dc3545; color: white; padding: 0.15rem 0.5rem; border-radius: 10px; font-size: 0.75rem; margin-left: 0.3rem; }

        /* Category filter cards &mdash; matches upload.php type-card pattern */
        .rnd-cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 0.8rem; margin-bottom: 1.5rem; }
        .rnd-cat-card { padding: 1rem; border: 2px solid #e9ecef; border-radius: 8px; text-decoration: none; color: #333;
                        transition: border-color 0.2s, box-shadow 0.2s; display: block; background: white; }
        .rnd-cat-card:hover { border-color: #4a90d9; box-shadow: 0 2px 8px rgba(74,144,217,0.15); }
        .rnd-cat-card.active { border-color: #28a745; background: #f0fff4; }
        .rnd-cat-card .cat-count { font-size: 1.3em; font-weight: 700; color: #333; }
        .rnd-cat-card .cat-name { font-size: 0.8rem; color: #6c757d; margin-top: 4px; }

        /* Tables &mdash; matches list.php */
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.65rem; text-align: left; border-bottom: 1px solid #e9ecef; font-size: 0.9rem; }
        th { background: #f8f9fa; font-weight: 600; color: #495057; font-size: 0.8rem; text-transform: uppercase; }
        tr:hover { background: #f8f9fa; }
        td a { color: #4a90d9; text-decoration: none; }
        td a:hover { text-decoration: underline; }

        /* Status badges &mdash; matches list.php / view.php / my_uploads.php */
        .status-badge { padding: 0.2rem 0.55rem; border-radius: 10px; font-size: 0.78rem; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-accepted { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }

        /* Buttons &mdash; matches list.php / view.php */
        .btn-sm { padding: 0.45rem 1rem; border: none; border-radius: 5px; cursor: pointer; font-size: 0.85rem; font-weight: 600; }
        .btn-approve { background: #28a745; color: white; }
        .btn-approve:hover { background: #218838; }
        .btn-reject { background: #dc3545; color: white; }
        .btn-reject:hover { background: #c82333; }
        .btn-view { background: #17a2b8; color: white; text-decoration: none; display: inline-block; }
        .btn-view:hover { background: #138496; color: white; text-decoration: none; }
        .btn-success { display: inline-block; padding: 0.5rem 1.2rem; background: #28a745; color: white;
                       border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; }
        .btn-success:hover { background: #218838; }

        /* Section panels &mdash; matches view.php */
        .section { background: #f8f9fa; padding: 1.2rem; border-radius: 8px; margin-bottom: 1.2rem; border: 1px solid #e9ecef; }

        /* Empty state &mdash; matches list.php */
        .empty-state { text-align: center; padding: 3rem; color: #6c757d; }

        /* Inline approve/reject form */
        .action-inline { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
        .remarks-sm { padding: 5px 8px; border: 1px solid #ccc; border-radius: 5px; font-size: 0.85rem; width: 160px; }
        .remarks-sm:focus { outline: none; border-color: #4a90d9; }

        .sub-info { color: #6c757d; font-size: 0.85rem; margin-bottom: 12px; }

        @media (max-width: 768px) {
            .rnd-stats-grid { grid-template-columns: repeat(2, 1fr); }
            .rnd-cat-grid { grid-template-columns: repeat(2, 1fr); }
            .tabs { flex-wrap: wrap; }
            .filters { flex-direction: column; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a> &raquo; R&D Dashboard
    </div>

    <!-- Header -->
    <div class="header-row">
        <h1>R&D Dashboard</h1>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="filters">
        <input type="hidden" name="tab" value="<?= htmlspecialchars($active_tab) ?>">
        <div class="form-group">
            <label for="dept">Department</label>
            <select name="dept" id="dept" onchange="this.form.submit()">
                <option value="0">All Departments</option>
                <?php foreach ($all_depts as $d):
                    // Skip pseudo-departments for the selector
                    $did = (int)$d['dept_id'];
                    if (in_array($d['dept_name'], ['NAAC','NBA','NCC','Sports','Clubs','NSS','IIC','Women_Empowerment','PASH','Anti_Ragging','SAC','R&D'])) continue;
                ?>
                    <option value="<?= $did ?>"<?= $did === $filter_dept_id ? ' selected' : '' ?>>
                        <?= htmlspecialchars($d['dept_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="year">Academic Year</label>
            <select name="year" id="year" onchange="this.form.submit()">
                <option value="0">All Years</option>
                <?php foreach ($academic_years as $yr): ?>
                    <option value="<?= $yr['year_id'] ?>"<?= (int)$yr['year_id'] === $filter_year_id ? ' selected' : '' ?>>
                        <?= htmlspecialchars($yr['year_label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <!-- Summary Stats -->
    <div class="rnd-stats-grid">
        <div class="rnd-stat-tile">
            <div class="num"><?= $total_rnd ?></div>
            <div class="lbl">Dept R&D Total</div>
        </div>
        <div class="rnd-stat-tile st-pending">
            <div class="num"><?= $pending_count ?></div>
            <div class="lbl">Pending Review</div>
        </div>
        <div class="rnd-stat-tile st-accepted">
            <div class="num"><?= $accepted_rnd['total'] ?></div>
            <div class="lbl">Accepted</div>
        </div>
        <div class="rnd-stat-tile st-rejected">
            <div class="num"><?= $rejected_rnd['total'] ?></div>
            <div class="lbl">Rejected</div>
        </div>
        <div class="rnd-stat-tile st-central">
            <div class="num"><?= $central_count ?></div>
            <div class="lbl">Central R&D</div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <a href="<?= rnd_url(['tab' => 'dept_rnd', 'type' => '']) ?>" class="<?= $active_tab === 'dept_rnd' ? 'active' : '' ?>">
            Department R&D
        </a>
        <a href="<?= rnd_url(['tab' => 'pending', 'type' => '']) ?>" class="<?= $active_tab === 'pending' ? 'active' : '' ?>">
            Pending Review<?php if ($pending_count > 0): ?><span class="badge"><?= $pending_count ?></span><?php endif; ?>
        </a>
        <a href="<?= rnd_url(['tab' => 'central', 'type' => '']) ?>" class="<?= $active_tab === 'central' ? 'active' : '' ?>">
            Central R&D
        </a>
    </div>

    <!-- ================================================================ -->
    <!-- TAB 1: DEPARTMENT R&D -->
    <!-- ================================================================ -->
    <?php if ($active_tab === 'dept_rnd'): ?>

        <!-- Category cards -->
        <div class="rnd-cat-grid">
            <a href="<?= rnd_url(['type' => '']) ?>" class="rnd-cat-card <?= $filter_type === '' ? 'active' : '' ?>">
                <div class="cat-count"><?= $total_rnd ?></div>
                <div class="cat-name">All Types</div>
            </a>
            <?php foreach ($research_types as $rt): ?>
                <a href="<?= rnd_url(['type' => $rt['type_key']]) ?>"
                   class="rnd-cat-card <?= $filter_type === $rt['type_key'] ? 'active' : '' ?>">
                    <div class="cat-count"><?= $type_counts[$rt['type_key']] ?? 0 ?></div>
                    <div class="cat-name"><?= htmlspecialchars($rt['type_label']) ?></div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Documents table -->
        <div class="section">
            <p class="sub-info">
                Showing <?= count($dept_rnd_docs) ?> of <?= $dept_rnd_total ?> documents
                &mdash; <?= htmlspecialchars($selected_dept_name) ?> &mdash; <?= htmlspecialchars($selected_year_label) ?>
            </p>

            <?php if (empty($dept_rnd_docs)): ?>
                <div class="empty-state">
                    <p>No department R&D documents found for the selected filters.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead><tr>
                        <th>Title</th><th>Type</th><th>Uploader</th><th>Dept</th><th>Year</th><th>Status</th><th>Date</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($dept_rnd_docs as $doc): ?>
                        <tr>
                            <td><a href="<?= BASE_URL ?>/pages/documents/view.php?id=<?= $doc['doc_id'] ?>"><?= htmlspecialchars($doc['title']) ?></a></td>
                            <td><?= htmlspecialchars($doc['type_label']) ?></td>
                            <td><?= htmlspecialchars($doc['uploader_name']) ?></td>
                            <td><?= htmlspecialchars($doc['dept_name']) ?></td>
                            <td><?= htmlspecialchars($doc['year_label'] ?? '&mdash;') ?></td>
                            <td><span class="status-badge status-<?= htmlspecialchars($doc['status']) ?>"><?= ucfirst($doc['status']) ?></span></td>
                            <td><?= date('d M Y', strtotime($doc['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    <!-- ================================================================ -->
    <!-- TAB 2: PENDING R&D DEAN REVIEW -->
    <!-- ================================================================ -->
    <?php elseif ($active_tab === 'pending'): ?>

        <div class="section">
            <p class="sub-info">
                <?= $pending_count ?> document(s) awaiting your review
            </p>

            <?php if (empty($pending_docs)): ?>
                <div class="empty-state">
                    <p>No documents pending your review.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead><tr>
                        <th>Title</th><th>Type</th><th>Uploader</th><th>Dept</th><th>Step</th><th>Date</th><th style="min-width:260px">Actions</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($pending_docs as $doc):
                        // Load allowed actions for this doc
                        $full_doc = doc_get($conn, (int)$doc['doc_id']);
                        $allowed = $full_doc ? wf_get_allowed_actions($conn, $full_doc, $auth) : [];
                    ?>
                        <tr>
                            <td><a href="<?= BASE_URL ?>/pages/documents/view.php?id=<?= $doc['doc_id'] ?>"><?= htmlspecialchars($doc['title']) ?></a></td>
                            <td><?= htmlspecialchars($doc['type_label']) ?></td>
                            <td><?= htmlspecialchars($doc['uploader_name']) ?></td>
                            <td><?= htmlspecialchars($doc['dept_name']) ?></td>
                            <td><?= htmlspecialchars($doc['step_label'] ?? '&mdash;') ?></td>
                            <td><?= date('d M Y', strtotime($doc['created_at'])) ?></td>
                            <td>
                                <?php if (!empty($allowed)): ?>
                                <form method="POST" action="<?= BASE_URL ?>/pages/documents/approve.php" class="action-inline"
                                      onsubmit="return validateAction(this)">
                                    <?php if (function_exists('csrfField')) echo csrfField(); ?>
                                    <input type="hidden" name="doc_id" value="<?= $doc['doc_id'] ?>">
                                    <input type="text" name="remarks" class="remarks-sm" placeholder="Remarks...">
                                    <?php if (in_array('approve', $allowed)): ?>
                                        <button type="submit" name="action" value="approve" class="btn-sm btn-approve"
                                                title="Approve">&#10003; Approve</button>
                                    <?php endif; ?>
                                    <?php if (in_array('reject', $allowed)): ?>
                                        <button type="submit" name="action" value="reject" class="btn-sm btn-reject"
                                                title="Reject">&#10005; Reject</button>
                                    <?php endif; ?>
                                    <a href="<?= BASE_URL ?>/pages/documents/view.php?id=<?= $doc['doc_id'] ?>"
                                       class="btn-sm btn-view" title="View details">View</a>
                                </form>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>/pages/documents/view.php?id=<?= $doc['doc_id'] ?>"
                                       class="btn-sm btn-view">View</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    <!-- ================================================================ -->
    <!-- TAB 3: CENTRAL R&D DOCUMENTS -->
    <!-- ================================================================ -->
    <?php elseif ($active_tab === 'central'): ?>

        <div class="header-row">
            <p class="sub-info" style="margin-bottom: 0;">
                <?= $central_count ?> Central R&D document(s)
            </p>
            <a href="<?= BASE_URL ?>/pages/documents/upload.php?type=central_rnd" class="btn-success">+ Upload Central R&D Document</a>
        </div>

        <div class="section">
            <?php if (empty($central_docs)): ?>
                <div class="empty-state">
                    <p>No Central R&D documents yet.</p>
                    <a href="<?= BASE_URL ?>/pages/documents/upload.php?type=central_rnd" class="btn-success" style="margin-top: 12px;">
                        Upload Your First Central R&D Document
                    </a>
                </div>
            <?php else: ?>
                <table>
                    <thead><tr>
                        <th>Title</th><th>Category</th><th>Year</th><th>Uploaded</th><th>Status</th><th>Actions</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($central_docs as $doc):
                        // Load meta to get category
                        $meta = doc_get_meta($conn, (int)$doc['doc_id'], 'central_rnd');
                        $cat_id = $meta ? (int)($meta['rnd_category_id'] ?? 0) : 0;
                        $cat_label = '&mdash;';
                    ?>
                        <tr>
                            <td><a href="<?= BASE_URL ?>/pages/documents/view.php?id=<?= $doc['doc_id'] ?>"><?= htmlspecialchars($doc['title']) ?></a></td>
                            <td><?= htmlspecialchars($cat_label) ?></td>
                            <td><?= htmlspecialchars($doc['year_label'] ?? '&mdash;') ?></td>
                            <td><?= date('d M Y', strtotime($doc['created_at'])) ?></td>
                            <td><span class="status-badge status-<?= htmlspecialchars($doc['status']) ?>"><?= ucfirst($doc['status']) ?></span></td>
                            <td>
                                <a href="<?= BASE_URL ?>/pages/documents/view.php?id=<?= $doc['doc_id'] ?>" class="btn-sm btn-view">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</div>

<script>
function validateAction(form) {
    const action = document.activeElement.value;
    const remarks = form.querySelector('[name="remarks"]').value.trim();
    if (action === 'reject' && !remarks) {
        alert('Remarks are required for rejection.');
        return false;
    }
    const verb = action === 'approve' ? 'approve' : 'reject';
    return confirm('Are you sure you want to ' + verb + ' this document?');
}
</script>

</body>
</html>

