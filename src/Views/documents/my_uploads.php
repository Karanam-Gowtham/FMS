
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

<div class="container" style="max-width: 1400px; display: flex; gap: 2rem; align-items: flex-start; padding: 2rem 1rem;">
    <!-- LEFT SIDEBAR -->
    <aside style="width: 280px; flex-shrink: 0; background: #fff; padding: 1.5rem; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <h3 style="font-size: 1.1rem; margin-top: 0; margin-bottom: 1.2rem; color: #111827; border-bottom: 2px solid #f3f4f6; padding-bottom: 0.5rem;">Filters</h3>
        <form method="GET" id="sidebar-filters" action="<?= BASE_URL ?>/public/index.php">
            <input type="hidden" name="route" value="documents/my_uploads">

            <!-- Types Checkboxes -->
            <div style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">Document Type</label>
                <div style="max-height: 180px; overflow-y: auto; padding-right: 5px; font-size: 0.9rem; color: #374151;">
                    <?php foreach ($all_types as $t): ?>
                        <div style="margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="type_keys[]" value="<?= htmlspecialchars($t['type_key']) ?>" id="my_tk_<?= $t['type_key'] ?>" <?= in_array($t['type_key'], $filter_type_keys) ? 'checked' : '' ?>>
                            <label for="my_tk_<?= $t['type_key'] ?>" style="margin: 0; font-weight: normal; cursor: pointer;"><?= htmlspecialchars($t['type_label']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Status Checkboxes -->
            <div style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">Status</label>
                <div style="font-size: 0.9rem; color: #374151;">
                    <?php foreach (['pending', 'accepted', 'rejected'] as $st): ?>
                        <div style="margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="statuses[]" value="<?= $st ?>" id="my_status_<?= $st ?>" <?= in_array($st, $filter_statuses) ? 'checked' : '' ?>>
                            <label for="my_status_<?= $st ?>" style="margin: 0; font-weight: normal; cursor: pointer;"><?= ucfirst($st) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Year Checkboxes -->
            <div style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">Academic Year</label>
                <div style="max-height: 180px; overflow-y: auto; padding-right: 5px; font-size: 0.9rem; color: #374151;">
                    <?php foreach ($academic_years as $yr): ?>
                        <div style="margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="year_ids[]" value="<?= $yr['year_id'] ?>" id="my_yr_<?= $yr['year_id'] ?>" <?= in_array($yr['year_id'], $filter_year_ids) ? 'checked' : '' ?>>
                            <label for="my_yr_<?= $yr['year_id'] ?>" style="margin: 0; font-weight: normal; cursor: pointer;"><?= htmlspecialchars($yr['year_label']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn-filter" style="width: 100%; padding: 0.75rem; font-size: 1rem; border-radius: 4px; border: none; cursor: pointer; background: #2563eb; color: white; font-weight: bold; transition: background 0.2s;">Apply Filters</button>
        </form>
    </aside>

    <!-- MAIN CONTENT -->
    <main style="flex-grow: 1; min-width: 0; background: #fff; padding: 1.5rem; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <div class="header-row" style="margin-bottom: 1.5rem; border-bottom: 2px solid #f3f4f6; padding-bottom: 0.5rem;">
            <h1 style="margin: 0; font-size: 1.5rem; color: #111827;">My Uploads</h1>
            <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload" class="btn-upload" style="background: #28a745; color: white; padding: 0.6rem 1.2rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">+ Upload New Document</a>
        </div>
        <p style="margin: 0 0 1.5rem 0; color: #6b7280; font-size: 0.9rem; font-weight: 500;">Showing <?= count($documents) ?> of <?= $total ?> documents</p>

    <?php if (empty($documents)): ?>
        <div class="empty-state">
            <p>No documents found matching your filters.</p>
            <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload" class="btn-upload" style="margin-top: 1rem;">Upload Your First Document</a>
        </div>
    <?php else: ?>
        <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=documents/bulk_action" id="bulkActionFormMyUploads">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; align-items: center;">
                <span style="font-size: 0.85rem; color: #555; font-weight: 600;">Bulk Actions:</span>
                <button type="submit" name="action_type" value="zip" class="btn-filter" style="background: #6c757d; color: white;">Download as ZIP</button>
                <button type="submit" name="action_type" value="merge_pdf" class="btn-filter" style="background: #28a745; color: white;">Merge to Single PDF</button>
            </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAllMyDocs"></th>
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
                    <td style="text-align: center;"><input type="checkbox" name="doc_ids[]" value="<?= $doc['doc_id'] ?>" class="mydoc-checkbox"></td>
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
        </form>

        <script>
            document.getElementById('selectAllMyDocs').addEventListener('change', function() {
                let checkboxes = document.querySelectorAll('.mydoc-checkbox');
                for (let cb of checkboxes) {
                    cb.checked = this.checked;
                }
            });
        </script>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php 
            $q = $_GET;
            unset($q['page']);
            $page_url_base = "?" . http_build_query($q);
            ?>
            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                <a href="<?= $page_url_base ?>&page=<?= $p ?>"
                   class="<?= $p === $page_num ? 'active' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
    </main>
</div>

</body>
</html>
