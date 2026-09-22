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
        .breadcrumb-bar { background: #f8f9fa; padding: 0.8rem 2rem; border-bottom: 1px solid #e9ecef; font-size: 0.9rem; color: #6c757d; }
        .breadcrumb-bar a { color: #4a90d9; text-decoration: none; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="breadcrumb-bar">
    <a href="<?= htmlspecialchars(get_role_landing_url(auth_active_role())) ?>">Dashboard</a> &raquo;
    <?= htmlspecialchars($scope_label) ?>
</div>

<div class="container">
    <div class="header-row">
        <h1><?= $scope_label ?></h1>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <?php 
        $base_params = '';
        if (!empty($_GET['context'])) $base_params .= '&context=' . urlencode($_GET['context']);
        if (!empty($_GET['type'])) $base_params .= '&type=' . urlencode($_GET['type']);
        if (!empty($_GET['sub_type'])) $base_params .= '&sub_type=' . urlencode($_GET['sub_type']);
        ?>
        <a href="<?= BASE_URL ?>/public/index.php?route=documents/list<?= $base_params ?>" class="active">All <?= $scope_label ?></a>
    </div>

    <!-- Filters -->
    <form method="GET" class="filters" action="<?= BASE_URL ?>/public/index.php">
        <input type="hidden" name="route" value="documents/list">
        <?php if (!empty($_GET['context'])): ?>
            <input type="hidden" name="context" value="<?= htmlspecialchars($_GET['context']) ?>">
        <?php endif; ?>
        <div class="form-group">
            <label for="search">Search</label>
            <input type="text" name="search" id="search" placeholder="Search title..."
                   value="<?= htmlspecialchars($filter_search) ?>">
        </div>
        
        <?php if (empty($_GET['context'])): ?>
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
        <?php endif; ?>
        
        <?php if (!empty($all_departments)): ?>
        <div class="form-group">
            <label for="filter_dept">Department</label>
            <select name="dept_id" id="filter_dept">
                <option value="">All Departments</option>
                <?php foreach ($all_departments as $d): ?>
                    <option value="<?= $d['dept_id'] ?>"<?= (isset($filter_dept) && $filter_dept === (int)$d['dept_id']) ? ' selected' : '' ?>>
                        <?= htmlspecialchars($d['dept_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($_GET['context']) && $_GET['context'] === 'dept_file'): ?>
        <div class="form-group">
            <label for="filter_subtype">Sub-Type</label>
            <select name="sub_type" id="filter_subtype">
                <option value="">All Sub-Types</option>
                <?php 
                $sub_types = ['Admin Files', 'Faculty Files', 'Student Related Files', 'Exam Section Files', 'Student Activities Files'];
                foreach ($sub_types as $st): 
                ?>
                    <option value="<?= htmlspecialchars($st) ?>"<?= (isset($_GET['sub_type']) && $_GET['sub_type'] === $st) ? ' selected' : '' ?>>
                        <?= htmlspecialchars($st) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
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
                    <?php if (!empty($meta_fields)): ?>
                        <?php foreach ($meta_fields as $mf): ?>
                            <th><?= htmlspecialchars($mf['label']) ?></th>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                <tr>
                    <td><a href="<?= BASE_URL ?>/public/index.php?route=documents/view&id=<?= $doc['doc_id'] ?>"><?= htmlspecialchars($doc['title'] ?: $doc['type_label']) ?></a></td>
                    <td><?= htmlspecialchars($doc['type_label']) ?></td>
                    <td><?= htmlspecialchars($doc['uploader_name']) ?></td>
                    <td><?= htmlspecialchars($doc['dept_name']) ?></td>
                    <td><?= htmlspecialchars(str_replace(' ', '-', $doc['year_label'] ?? '—')) ?></td>
                    <?php if (!empty($meta_fields)): ?>
                        <?php foreach ($meta_fields as $mf): ?>
                            <td><?= htmlspecialchars($doc['meta_' . $mf['name']] ?? '—') ?></td>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <td><span class="status-badge status-<?= htmlspecialchars($doc['status']) ?>"><?= ucfirst(htmlspecialchars($doc['status'])) ?></span></td>
                    <td><?= date('d M Y', strtotime($doc['created_at'])) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&id=<?= $doc['doc_id'] ?>" style="background: #007bff; color: white; border: none; padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 4px; font-weight: 600; text-decoration: none; display: inline-block; cursor: pointer; text-align: center;">View</a>
                    </td>
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
