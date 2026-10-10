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

<div class="container" style="max-width: 1400px; display: flex; gap: 2rem; align-items: flex-start; padding: 2rem 1rem;">
    
    <!-- LEFT SIDEBAR -->
    <aside style="width: 280px; flex-shrink: 0; background: #fff; padding: 1.5rem; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <h3 style="font-size: 1.1rem; margin-top: 0; margin-bottom: 1.2rem; color: #111827; border-bottom: 2px solid #f3f4f6; padding-bottom: 0.5rem;">Filters</h3>
        <form method="GET" id="sidebar-filters" action="<?= BASE_URL ?>/public/index.php">
            <input type="hidden" name="route" value="documents/list">
            <?php if (!empty($_GET['context'])): ?>
                <input type="hidden" name="context" value="<?= htmlspecialchars($_GET['context']) ?>">
            <?php endif; ?>

            <!-- Search -->
            <div style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">Search</label>
                <input type="text" name="search" placeholder="Search title..." value="<?= htmlspecialchars($filter_search) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
            </div>

            <!-- Types Checkboxes -->
            <?php if (empty($_GET['context'])): ?>
            <div style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">Type</label>
                <div style="max-height: 180px; overflow-y: auto; padding-right: 5px; font-size: 0.9rem; color: #374151;">
                    <?php foreach ($all_types as $t): ?>
                        <div style="margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="type_keys[]" value="<?= htmlspecialchars($t['type_key']) ?>" id="tk_<?= $t['type_key'] ?>" <?= in_array($t['type_key'], $filter_type_keys) ? 'checked' : '' ?>>
                            <label for="tk_<?= $t['type_key'] ?>" style="margin: 0; font-weight: normal; cursor: pointer;"><?= htmlspecialchars($t['type_label']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Department Checkboxes -->
            <?php if (!empty($all_departments)): ?>
            <div style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">Department</label>
                <div style="max-height: 180px; overflow-y: auto; padding-right: 5px; font-size: 0.9rem; color: #374151;">
                    <?php foreach ($all_departments as $d): ?>
                        <div style="margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="dept_ids[]" value="<?= $d['dept_id'] ?>" id="d_<?= $d['dept_id'] ?>" <?= in_array($d['dept_id'], $filter_dept_ids) ? 'checked' : '' ?>>
                            <label for="d_<?= $d['dept_id'] ?>" style="margin: 0; font-weight: normal; cursor: pointer;"><?= htmlspecialchars($d['dept_name']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Merged Types & Categories -->
            <?php if (!empty($_GET['context']) && $_GET['context'] === 'dept_file'): ?>
            <div style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">Categories</label>
                
                <input type="text" id="category-search" placeholder="Filter categories..." style="width: 100%; padding: 0.4rem; margin-bottom: 0.8rem; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box; font-size: 0.85rem;">

                <div id="category-tree" style="max-height: 400px; overflow-y: auto; padding-right: 5px; font-size: 0.9rem; color: #374151;">
                    <?php 
                    $sub_types = ['Admin Files', 'Faculty Files', 'Student Related Files', 'Exam Section Files', 'Student Activities Files'];
                    foreach ($sub_types as $st): 
                        $has_children = isset($available_sub_file_types[$st]) && !empty($available_sub_file_types[$st]);
                        $st_id = md5($st);
                        $is_checked = in_array($st, $filter_sub_types);
                    ?>
                        <div class="category-parent-node" style="margin-bottom: 0.6rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="checkbox" name="sub_types[]" value="<?= htmlspecialchars($st) ?>" id="st_<?= $st_id ?>" class="parent-checkbox" <?= $is_checked ? 'checked' : '' ?>>
                                <label for="st_<?= $st_id ?>" class="parent-label" style="margin: 0; font-weight: 600; cursor: pointer; color: #111827;"><?= htmlspecialchars($st) ?></label>
                            </div>
                            
                            <?php if ($has_children): ?>
                            <div class="category-children-node" data-parent="<?= htmlspecialchars($st) ?>" style="margin-left: 1.2rem; margin-top: 0.3rem; padding-left: 0.6rem; border-left: 2px solid #e5e7eb; display: <?= $is_checked ? 'block' : 'none' ?>; max-height: 180px; overflow-y: auto;">
                                <?php foreach ($available_sub_file_types[$st] as $sft): ?>
                                    <div class="child-item" style="margin-bottom: 0.3rem; display: flex; align-items: center; gap: 0.5rem;">
                                        <input type="checkbox" name="sub_file_types[]" value="<?= htmlspecialchars($sft) ?>" id="sft_<?= md5($sft) ?>" class="child-checkbox" <?= in_array($sft, $filter_sub_file_types) ? 'checked' : '' ?>>
                                        <label for="sft_<?= md5($sft) ?>" class="child-label" style="margin: 0; font-weight: normal; cursor: pointer; font-size: 0.85rem;"><?= htmlspecialchars($sft) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const parentCheckboxes = document.querySelectorAll('.parent-checkbox');
                    const searchInput = document.getElementById('category-search');
                    
                    // Toggle visibility of children based on parent checkbox
                    parentCheckboxes.forEach(cb => {
                        cb.addEventListener('change', function() {
                            const parentNode = this.closest('.category-parent-node');
                            const childrenNode = parentNode.querySelector('.category-children-node');
                            if (childrenNode) {
                                if (this.checked) {
                                    childrenNode.style.display = 'block';
                                } else {
                                    childrenNode.style.display = 'none';
                                }
                            }
                        });
                    });

                    // Search filtering
                    if (searchInput) {
                        searchInput.addEventListener('input', function() {
                            const term = this.value.toLowerCase().trim();
                            const parentNodes = document.querySelectorAll('.category-parent-node');
                            
                            parentNodes.forEach(parentNode => {
                                const parentLabel = parentNode.querySelector('.parent-label').textContent.toLowerCase();
                                const childrenNode = parentNode.querySelector('.category-children-node');
                                const childItems = parentNode.querySelectorAll('.child-item');
                                const parentCheckbox = parentNode.querySelector('.parent-checkbox');
                                
                                let parentMatches = parentLabel.includes(term);
                                let anyChildMatches = false;
                                
                                childItems.forEach(child => {
                                    const childLabel = child.querySelector('.child-label').textContent.toLowerCase();
                                    if (childLabel.includes(term) || parentMatches) {
                                        child.style.display = 'flex';
                                        anyChildMatches = true;
                                    } else {
                                        child.style.display = 'none';
                                    }
                                });
                                
                                if (parentMatches || anyChildMatches) {
                                    parentNode.style.display = 'block';
                                    if (term !== '' && anyChildMatches && childrenNode) {
                                        childrenNode.style.display = 'block';
                                    } else if (term === '' && childrenNode && !parentCheckbox.checked) {
                                        childrenNode.style.display = 'none';
                                    }
                                } else {
                                    parentNode.style.display = 'none';
                                }
                            });
                        });
                    }
                });
            </script>
            <?php endif; ?>

            <!-- Global Event Filters -->
            <?php if (!empty($_GET['context']) && $_GET['context'] === 'dept_file'): ?>
            <div style="margin-bottom: 1.5rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                <h3 style="font-size: 0.95rem; margin-top: 0; margin-bottom: 1rem; color: #111827; text-transform: uppercase; letter-spacing: 0.05em;">Global Attributes</h3>

                <?php if (!empty($global_filter_options['event_types'])): ?>
                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem; color: #4b5563;">Event Type</label>
                    <div style="max-height: 120px; overflow-y: auto; padding-right: 5px; font-size: 0.85rem; color: #374151;">
                        <?php foreach ($global_filter_options['event_types'] as $et): ?>
                            <div style="margin-bottom: 0.3rem; display: flex; align-items: center; gap: 0.5rem;">
                                <input type="checkbox" name="global_event_types[]" value="<?= htmlspecialchars($et) ?>" id="get_<?= md5($et) ?>" <?= in_array($et, $filter_global_event_types ?? []) ? 'checked' : '' ?>>
                                <label for="get_<?= md5($et) ?>" style="margin: 0; cursor: pointer;"><?= htmlspecialchars($et) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($global_filter_options['domains'])): ?>
                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem; color: #4b5563;">Domain / Topic</label>
                    <div style="max-height: 120px; overflow-y: auto; padding-right: 5px; font-size: 0.85rem; color: #374151;">
                        <?php foreach ($global_filter_options['domains'] as $dom): ?>
                            <div style="margin-bottom: 0.3rem; display: flex; align-items: center; gap: 0.5rem;">
                                <input type="checkbox" name="global_domains[]" value="<?= htmlspecialchars($dom) ?>" id="gdom_<?= md5($dom) ?>" <?= in_array($dom, $filter_global_domains ?? []) ? 'checked' : '' ?>>
                                <label for="gdom_<?= md5($dom) ?>" style="margin: 0; cursor: pointer;"><?= htmlspecialchars($dom) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($global_filter_options['modes'])): ?>
                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem; color: #4b5563;">Mode of Conduct</label>
                    <div style="max-height: 120px; overflow-y: auto; padding-right: 5px; font-size: 0.85rem; color: #374151;">
                        <?php foreach ($global_filter_options['modes'] as $md): ?>
                            <div style="margin-bottom: 0.3rem; display: flex; align-items: center; gap: 0.5rem;">
                                <input type="checkbox" name="global_modes[]" value="<?= htmlspecialchars($md) ?>" id="gmd_<?= md5($md) ?>" <?= in_array($md, $filter_global_modes ?? []) ? 'checked' : '' ?>>
                                <label for="gmd_<?= md5($md) ?>" style="margin: 0; cursor: pointer;"><?= htmlspecialchars($md) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Status Checkboxes -->
            <div style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">Status</label>
                <div style="font-size: 0.9rem; color: #374151;">
                    <?php foreach (['pending', 'accepted', 'rejected'] as $st): ?>
                        <div style="margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="statuses[]" value="<?= $st ?>" id="status_<?= $st ?>" <?= in_array($st, $filter_statuses) ? 'checked' : '' ?>>
                            <label for="status_<?= $st ?>" style="margin: 0; font-weight: normal; cursor: pointer;"><?= ucfirst($st) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Year Checkboxes -->
            <div style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">Year</label>
                <div style="max-height: 180px; overflow-y: auto; padding-right: 5px; font-size: 0.9rem; color: #374151;">
                    <?php foreach ($academic_years as $yr): ?>
                        <div style="margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="year_ids[]" value="<?= $yr['year_id'] ?>" id="yr_<?= $yr['year_id'] ?>" <?= in_array($yr['year_id'], $filter_year_ids) ? 'checked' : '' ?>>
                            <label for="yr_<?= $yr['year_id'] ?>" style="margin: 0; font-weight: normal; cursor: pointer;"><?= htmlspecialchars($yr['year_label']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem; border-radius: 4px; border: none; cursor: pointer; background: #2563eb; color: white; font-weight: bold; transition: background 0.2s;">Apply Filters</button>
        </form>
    </aside>

    <!-- MAIN CONTENT -->
    <main style="flex-grow: 1; min-width: 0; background: #fff; padding: 1.5rem; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <div class="header-row" style="margin-bottom: 1.5rem; border-bottom: 2px solid #f3f4f6; padding-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <h1 style="margin: 0; font-size: 1.5rem; color: #111827;"><?= $scope_label ?></h1>
                <p style="margin: 0; color: #6b7280; font-size: 0.9rem; font-weight: 500;"><?= count($documents) ?> of <?= $total ?> documents</p>
            </div>
            <a href="<?= BASE_URL ?>/public/index.php?<?= http_build_query(array_merge($_GET, ['export' => 'csv'])) ?>" style="background: #10b981; color: white; padding: 0.5rem 1rem; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s;">
                <i class="fas fa-file-csv"></i> Export Filtered to CSV
            </a>
        </div>

    <?php if (empty($documents)): ?>
        <div class="empty-state">
            <p>No documents found.</p>
        </div>
    <?php else: ?>
        <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=documents/bulk_action" id="bulkActionForm">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; align-items: center;">
                <span style="font-size: 0.85rem; color: #555; font-weight: 600;">Bulk Actions:</span>
                <button type="submit" name="action_type" value="zip" class="btn-sm" style="background: #6c757d; color: white;">Download as ZIP</button>
                <button type="submit" name="action_type" value="merge_pdf" class="btn-sm btn-primary">Merge to Single PDF</button>
            </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAllDocs"></th>
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
                    <td style="text-align: center;"><input type="checkbox" name="doc_ids[]" value="<?= $doc['doc_id'] ?>" class="doc-checkbox"></td>
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
        </form>

        <script>
            document.getElementById('selectAllDocs').addEventListener('change', function() {
                let checkboxes = document.querySelectorAll('.doc-checkbox');
                for (let cb of checkboxes) {
                    cb.checked = this.checked;
                }
            });
        </script>

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
