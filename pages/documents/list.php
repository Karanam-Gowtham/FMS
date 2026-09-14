<?php
/**
 * FMS Document List Page
 *
 * Role-scoped document listing. What a user sees depends on their active role:
 * - Faculty: own uploads only
 * - HOD/Dept Coordinator: own department's documents
 * - Admin/RnD Dean: all documents
 * - Central Coordinator: central category documents
 *
 * Authorization is always derived from the authenticated user's roles, never from URL params.
 * URL params (?dept=, ?type=, ?status=) are validated filters only.
 *
 * URL: pages/documents/list.php
 */
require_once __DIR__ . '/../../core/bootstrap.php';

$auth = auth_require_login();
$user_id = (int)$auth['user_id'];
$active_role = $auth['active_role'] ?? null;
$active_role_id = $active_role ? (int)$active_role['role_id'] : 0;
$active_dept_id = $active_role ? (int)$active_role['dept_id'] : 0;

// Determine scope based on active role
$scope_label = 'Documents';
$forced_filters = [];

switch ($active_role_id) {
    case ROLE_ADMIN:
    case ROLE_RND_DEAN:
        // See all documents
        $scope_label = 'All Documents';
        break;

    case ROLE_IQAC:
        // See all documents (IQAC has cross-department visibility)
        $scope_label = 'All Documents (IQAC)';
        break;

    case ROLE_HOD:
    case ROLE_DEPT_COORDINATOR:
    case ROLE_JUNIOR_ASSISTANT:
        // See own department's documents
        $forced_filters['dept_id'] = $active_dept_id;
        $dept_stmt = $conn->prepare("SELECT dept_name FROM dept WHERE dept_id = ?");
        $dept_stmt->bind_param('i', $active_dept_id);
        $dept_stmt->execute();
        $dept_name = $dept_stmt->get_result()->fetch_assoc()['dept_name'] ?? 'Unknown';
        $dept_stmt->close();
        $scope_label = htmlspecialchars($dept_name) . ' Documents';
        break;

    case ROLE_CENTRAL_COORDINATOR:
        // See central category documents
        $forced_filters['category'] = 'central';
        $scope_label = 'Central Documents';
        break;

    case ROLE_FACULTY:
    default:
        // Faculty sees own uploads only
        $forced_filters['uploaded_by'] = $user_id;
        $scope_label = 'My Documents';
        break;
}

// Optional user filters from GET params (these are validated filters, not authorization)
$filter_type   = isset($_GET['type'])   ? trim($_GET['type'])   : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
$filter_year   = isset($_GET['year'])   ? (int)$_GET['year']   : 0;
$filter_search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page_num      = max(1, (int)($_GET['page'] ?? 1));
$per_page      = 25;
$offset        = ($page_num - 1) * $per_page;

// Merge forced + optional filters
$filters = $forced_filters;
if ($filter_type !== '')   $filters['type_key'] = $filter_type;
if ($filter_status !== '') $filters['status'] = $filter_status;
if ($filter_year > 0)     $filters['year_id'] = $filter_year;
if ($filter_search !== '') $filters['search'] = $filter_search;

$result = doc_list($conn, $filters, $per_page, $offset);
$documents = $result['rows'];
$total = $result['total'];
$total_pages = (int)ceil($total / $per_page);

// Lookup data for filters
$all_types = doc_get_types($conn);
$academic_years = doc_get_academic_years($conn);

// Check if user can see a "Pending My Approval" tab
$pending_result = doc_list_pending_for_user($conn, $auth, 100, 0);
$pending_count = $pending_result['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $scope_label ?> — FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <style>
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .header-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .tabs { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
        .tabs a { padding: 0.5rem 1rem; border: 1px solid #dee2e6; border-radius: 6px; text-decoration: none; color: #495057; font-weight: 500; }
        .tabs a.active { background: #4a90d9; color: white; border-color: #4a90d9; }
        .tabs .badge { background: #dc3545; color: white; padding: 0.15rem 0.5rem; border-radius: 10px; font-size: 0.75rem; margin-left: 0.3rem; }
        .filters { display: flex; gap: 0.8rem; flex-wrap: wrap; margin-bottom: 1.2rem; align-items: end; }
        .filters .form-group { flex: 1; min-width: 140px; }
        .filters label { display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 0.2rem; }
        .filters select, .filters input[type="text"] { width: 100%; padding: 0.45rem; border: 1px solid #ccc; border-radius: 5px; font-size: 0.9rem; }
        .btn-sm { padding: 0.45rem 1rem; border: none; border-radius: 5px; cursor: pointer; font-size: 0.9rem; }
        .btn-primary { background: #4a90d9; color: white; }
        .btn-success { background: #28a745; color: white; text-decoration: none; display: inline-block; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.65rem; text-align: left; border-bottom: 1px solid #e9ecef; font-size: 0.9rem; }
        th { background: #f8f9fa; font-weight: 600; color: #495057; font-size: 0.8rem; text-transform: uppercase; }
        tr:hover { background: #f8f9fa; }
        .status-badge { padding: 0.2rem 0.55rem; border-radius: 10px; font-size: 0.78rem; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-accepted { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .empty-state { text-align: center; padding: 3rem; color: #6c757d; }
        .pagination { display: flex; gap: 0.4rem; justify-content: center; margin-top: 1.5rem; }
        .pagination a { padding: 0.35rem 0.7rem; border: 1px solid #dee2e6; border-radius: 4px; text-decoration: none; color: #495057; font-size: 0.9rem; }
        .pagination a.active { background: #4a90d9; color: white; border-color: #4a90d9; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container">
    <div class="header-row">
        <h1><?= $scope_label ?></h1>
        <a href="<?= BASE_URL ?>/pages/documents/upload.php" class="btn-sm btn-success">+ Upload</a>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <a href="<?= BASE_URL ?>/pages/documents/list.php" class="active">All <?= $scope_label ?></a>
        <?php if ($pending_count > 0): ?>
            <a href="<?= BASE_URL ?>/pages/documents/list.php?mode=pending_approval">
                Pending My Approval <span class="badge"><?= $pending_count ?></span>
            </a>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <form method="GET" class="filters">
        <div class="form-group">
            <label for="search">Search</label>
            <input type="text" name="search" id="search" placeholder="Search title..."
                   value="<?= htmlspecialchars($filter_search) ?>">
        </div>
        <div class="form-group">
            <label for="filter_type">Type</label>
            <select name="type" id="filter_type">
                <option value="">All Types</option>
                <?php foreach ($all_types as $t): ?>
                    <option value="<?= htmlspecialchars($t['type_key']) ?>"<?= $filter_type === $t['type_key'] ? ' selected' : '' ?>>
                        <?= htmlspecialchars($t['type_label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="filter_status">Status</label>
            <select name="status" id="filter_status">
                <option value="">All</option>
                <option value="pending"<?= $filter_status === 'pending' ? ' selected' : '' ?>>Pending</option>
                <option value="accepted"<?= $filter_status === 'accepted' ? ' selected' : '' ?>>Accepted</option>
                <option value="rejected"<?= $filter_status === 'rejected' ? ' selected' : '' ?>>Rejected</option>
            </select>
        </div>
        <div class="form-group">
            <label for="filter_year">Year</label>
            <select name="year" id="filter_year">
                <option value="">All Years</option>
                <?php foreach ($academic_years as $yr): ?>
                    <option value="<?= $yr['year_id'] ?>"<?= $filter_year === (int)$yr['year_id'] ? ' selected' : '' ?>>
                        <?= htmlspecialchars($yr['year_label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn-sm btn-primary">Filter</button>
    </form>

    <p style="color: #6c757d; font-size: 0.85rem;"><?= count($documents) ?> of <?= $total ?> documents</p>

    <?php if (empty($documents)): ?>
        <div class="empty-state">
            <p>No documents found.</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Uploaded By</th>
                    <th>Department</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                <tr>
                    <td><a href="<?= BASE_URL ?>/pages/documents/view.php?id=<?= $doc['doc_id'] ?>"><?= htmlspecialchars($doc['title']) ?></a></td>
                    <td><?= htmlspecialchars($doc['type_label']) ?></td>
                    <td><?= htmlspecialchars($doc['uploader_name']) ?></td>
                    <td><?= htmlspecialchars($doc['dept_name']) ?></td>
                    <td><?= htmlspecialchars($doc['year_label'] ?? '—') ?></td>
                    <td><span class="status-badge status-<?= htmlspecialchars($doc['status']) ?>"><?= ucfirst(htmlspecialchars($doc['status'])) ?></span></td>
                    <td><?= date('d M Y', strtotime($doc['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                <a href="?page=<?= $p ?>&type=<?= urlencode($filter_type) ?>&status=<?= urlencode($filter_status) ?>&year=<?= $filter_year ?>&search=<?= urlencode($filter_search) ?>"
                   class="<?= $p === $page_num ? 'active' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
