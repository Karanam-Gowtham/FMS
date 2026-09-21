
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Uploads — FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <style>
        .container { max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }
        .filters { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem; align-items: end; }
        .filters .form-group { flex: 1; min-width: 150px; }
        .filters label { display: block; font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 0.2rem; }
        .filters select { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 6px; }
        .btn-filter { padding: 0.5rem 1.2rem; background: #4a90d9; color: white; border: none; border-radius: 6px; cursor: pointer; }
        .btn-filter:hover { background: #357abd; }
        .btn-upload { display: inline-block; padding: 0.6rem 1.5rem; background: #28a745; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; }
        .btn-upload:hover { background: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.7rem; text-align: left; border-bottom: 1px solid #e9ecef; }
        th { background: #f8f9fa; font-weight: 600; color: #495057; font-size: 0.85rem; text-transform: uppercase; }
        tr:hover { background: #f8f9fa; }
        .status-badge { padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-accepted { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .empty-state { text-align: center; padding: 3rem; color: #6c757d; }
        .pagination { display: flex; gap: 0.5rem; justify-content: center; margin-top: 1.5rem; }
        .pagination a { padding: 0.4rem 0.8rem; border: 1px solid #dee2e6; border-radius: 4px; text-decoration: none; color: #495057; }
        .pagination a.active { background: #4a90d9; color: white; border-color: #4a90d9; }
        .header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .breadcrumb-bar { background: #f8f9fa; padding: 0.8rem 2rem; border-bottom: 1px solid #e9ecef; font-size: 0.9rem; color: #6c757d; }
        .breadcrumb-bar a { color: #4a90d9; text-decoration: none; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="breadcrumb-bar">
    <a href="<?= htmlspecialchars(get_role_landing_url(auth_active_role())) ?>">Dashboard</a> &raquo;
    My Uploads
</div>

<div class="container">
    <div class="header-row">
        <h1>My Uploads</h1>
        <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload" class="btn-upload">+ Upload New Document</a>
    </div>

    <!-- Filters -->
    <form method="GET" class="filters" action="<?= BASE_URL ?>/public/index.php">
        <input type="hidden" name="route" value="documents/my_uploads">
        <div class="form-group">
            <label for="filter_type">Document Type</label>
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
                <option value="">All Statuses</option>
                <option value="pending"<?= $filter_status === 'pending' ? ' selected' : '' ?>>Pending</option>
                <option value="accepted"<?= $filter_status === 'accepted' ? ' selected' : '' ?>>Accepted</option>
                <option value="rejected"<?= $filter_status === 'rejected' ? ' selected' : '' ?>>Rejected</option>
            </select>
        </div>
        <div class="form-group">
            <label for="filter_year">Academic Year</label>
            <select name="year" id="filter_year">
                <option value="">All Years</option>
                <?php foreach ($academic_years as $yr): ?>
                    <option value="<?= $yr['year_id'] ?>"<?= $filter_year === (int)$yr['year_id'] ? ' selected' : '' ?>>
                        <?= htmlspecialchars($yr['year_label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn-filter">Filter</button>
    </form>

    <!-- Results -->
    <p style="color: #6c757d; font-size: 0.9rem;">Showing <?= count($documents) ?> of <?= $total ?> documents</p>

    <?php if (empty($documents)): ?>
        <div class="empty-state">
            <p>No documents found matching your filters.</p>
            <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload" class="btn-upload" style="margin-top: 1rem;">Upload Your First Document</a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Department</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Uploaded</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                <tr>
                    <td><a href="<?= BASE_URL ?>/public/index.php?route=documents/view&id=<?= $doc['doc_id'] ?>"><?= htmlspecialchars($doc['title'] ?: $doc['type_label']) ?></a></td>
                    <td><?= htmlspecialchars($doc['type_label']) ?></td>
                    <td><?= htmlspecialchars($doc['dept_name']) ?></td>
                    <td><?= htmlspecialchars(str_replace(' ', '-', $doc['year_label'] ?? '—')) ?></td>
                    <td>
                        <span class="status-badge status-<?= htmlspecialchars($doc['status']) ?>">
                            <?= ucfirst(htmlspecialchars($doc['status'])) ?>
                        </span>
                    </td>
                    <td><?= date('d M Y', strtotime($doc['created_at'])) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&id=<?= $doc['doc_id'] ?>">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                <a href="?page=<?= $p ?>&type=<?= urlencode($filter_type) ?>&status=<?= urlencode($filter_status) ?>&year=<?= $filter_year ?>"
                   class="<?= $p === $page_num ? 'active' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
