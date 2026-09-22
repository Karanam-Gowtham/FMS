
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> — FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <style>
        .container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .breadcrumb { margin-bottom: 1rem; color: #6c757d; font-size: 0.9rem; }
        .breadcrumb a { color: #4a90d9; text-decoration: none; }
        .doc-header { display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
        .doc-header h1 { font-size: 1.5rem; margin: 0; flex: 1; }
        .status-badge { padding: 0.35rem 0.8rem; border-radius: 12px; font-size: 0.85rem; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-accepted { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .section { background: #f8f9fa; padding: 1.2rem; border-radius: 8px; margin-bottom: 1.2rem; border: 1px solid #e9ecef; }
        .section h3 { margin-top: 0; font-size: 1.05rem; color: #495057; border-bottom: 1px solid #dee2e6; padding-bottom: 0.5rem; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; }
        .detail-item { }
        .detail-label { font-size: 0.8rem; color: #6c757d; font-weight: 600; text-transform: uppercase; }
        .detail-value { font-size: 0.95rem; color: #333; }
        .files-list { list-style: none; padding: 0; }
        .files-list li { padding: 0.5rem 0; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center; }
        .files-list li:last-child { border-bottom: none; }
        .btn { padding: 0.5rem 1.2rem; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem; font-weight: 600; }
        .btn-approve { background: #28a745; color: white; }
        .btn-approve:hover { background: #218838; }
        .btn-reject { background: #dc3545; color: white; }
        .btn-reject:hover { background: #c82333; }
        .btn-resubmit { background: #ffc107; color: #333; }
        .btn-resubmit:hover { background: #e0a800; }
        .btn-download { background: #17a2b8; color: white; text-decoration: none; padding: 0.3rem 0.8rem; border-radius: 4px; font-size: 0.85rem; }
        .action-section { background: #e8f4fd; padding: 1.2rem; border-radius: 8px; border: 1px solid #b8daff; margin-bottom: 1.2rem; }
        .action-section h3 { margin-top: 0; color: #004085; }
        .rejection-box { background: #f8d7da; padding: 1rem; border-radius: 6px; margin-bottom: 1rem; border: 1px solid #f5c6cb; }
        .history-table { width: 100%; border-collapse: collapse; }
        .history-table th, .history-table td { padding: 0.5rem; text-align: left; border-bottom: 1px solid #dee2e6; font-size: 0.85rem; }
        .history-table th { font-weight: 600; color: #495057; }
        .remarks-input { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 6px; min-height: 60px; resize: vertical; margin-bottom: 0.8rem; }
        .breadcrumb-bar { background: #f8f9fa; padding: 0.8rem 2rem; border-bottom: 1px solid #e9ecef; font-size: 0.9rem; color: #6c757d; }
        .breadcrumb-bar a { color: #4a90d9; text-decoration: none; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="breadcrumb-bar">
    <a href="<?= htmlspecialchars(get_role_landing_url(auth_active_role())) ?>">Dashboard</a> &raquo;
    <?php if (isset($_GET['mode']) && $_GET['mode'] === 'review'): ?>
        <a href="<?= BASE_URL ?>/public/index.php?route=dashboard">Pending Approvals</a> &raquo;
    <?php else: ?>
        <a href="<?= BASE_URL ?>/public/index.php?route=documents/list">Documents</a> &raquo;
    <?php endif; ?>
    <?= htmlspecialchars($document['title'] ?: 'View Document') ?>
</div>

<div class="container">

    <div class="doc-header">
        <h1><?= $page_title ?></h1>
        <span class="status-badge status-<?= htmlspecialchars($document['status']) ?>">
            <?= htmlspecialchars($status_label) ?>
        </span>
    </div>

    <?php if ($document['status'] === 'rejected' && $document['rejection_reason']): ?>
        <div class="rejection-box">
            <strong>Rejection Reason:</strong> <?= htmlspecialchars($document['rejection_reason']) ?>
        </div>
    <?php endif; ?>

    <!-- Action Section (only if user can act and came from dashboard review mode) -->
    <?php if (!empty($allowed_actions) && isset($_GET['mode']) && $_GET['mode'] === 'review'): ?>
    <div class="action-section">
        <h3>Actions Available</h3>
        <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=documents/approve" id="actionForm">
            <?php if (function_exists('csrfField')) echo csrfField(); ?>
            <input type="hidden" name="doc_id" value="<?= $doc_id ?>">

            <?php if (in_array('approve', $allowed_actions) || in_array('reject', $allowed_actions)): ?>
                <label for="remarks" style="font-weight: 600; display: block; margin-bottom: 0.3rem;">Remarks (required for rejection):</label>
                <textarea name="remarks" id="remarks" class="remarks-input" placeholder="Enter remarks..."></textarea>
            <?php endif; ?>

            <div style="display: flex; gap: 0.8rem; flex-wrap: wrap;">
                <?php if (in_array('approve', $allowed_actions)): ?>
                    <button type="submit" name="action" value="approve" class="btn btn-approve"
                            onclick="return confirm('Are you sure you want to approve this document?')">
                        ✓ Approve
                    </button>
                <?php endif; ?>
                <?php if (in_array('reject', $allowed_actions)): ?>
                    <button type="submit" name="action" value="reject" class="btn btn-reject"
                            onclick="if(!document.getElementById('remarks').value.trim()){alert('Remarks are required for rejection.');return false;}return confirm('Are you sure you want to reject this document?')">
                        ✕ Reject
                    </button>
                <?php endif; ?>
                <?php if (in_array('resubmit', $allowed_actions)): ?>
                    <button type="submit" name="action" value="resubmit" class="btn btn-resubmit"
                            onclick="return confirm('Are you sure you want to resubmit this document for review?')">
                        ↻ Resubmit for Review
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Document Details -->
    <div class="section">
        <h3>Document Information</h3>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Type</div>
                <div class="detail-value"><?= htmlspecialchars($document['type_label']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Category</div>
                <div class="detail-value"><?= ucfirst(htmlspecialchars($document['category'])) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Uploaded By</div>
                <div class="detail-value"><?= htmlspecialchars($document['uploader_name']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Department</div>
                <div class="detail-value"><?= htmlspecialchars($document['dept_name']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Academic Year</div>
                <div class="detail-value"><?= htmlspecialchars($document['year_label'] ?? '—') ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Uploaded On</div>
                <div class="detail-value"><?= date('d M Y, h:i A', strtotime($document['created_at'])) ?></div>
            </div>
        </div>
    </div>

    <!-- Meta Data -->
    <?php if (!empty($meta_data) && !empty($meta_fields)): ?>
    <div class="section">
        <h3><?= htmlspecialchars($document['type_label']) ?> Details</h3>
        <div class="detail-grid">
            <?php foreach ($meta_fields as $field): ?>
                <?php
                $val = $meta_data[$field['name']] ?? null;
                if ($val === null || $val === '') continue;
                ?>
                <div class="detail-item">
                    <div class="detail-label"><?= htmlspecialchars($field['label']) ?></div>
                    <div class="detail-value"><?= htmlspecialchars((string)$val) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Files -->
    <?php if (!empty($files)): ?>
    <div class="section">
        <h3>Attached Files</h3>
        <ul class="files-list">
            <?php foreach ($files as $f): ?>
            <li>
                <div>
                    <strong><?= htmlspecialchars($f['file_label']) ?></strong>
                    <span style="color: #6c757d; font-size: 0.85rem;">(<?= htmlspecialchars($f['original_name']) ?>)</span>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/download&file_id=<?= $f['file_id'] ?>&inline=1" class="btn-download" style="background: #28a745; margin-right: 5px;" target="_blank">View File</a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/download&file_id=<?= $f['file_id'] ?>" class="btn-download">Download</a>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Action History -->
    <?php if (!empty($action_history)): ?>
    <div class="section">
        <h3>Action History</h3>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>By</th>
                    <th>Step</th>
                    <th>Remarks</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($action_history as $h): ?>
                <tr>
                    <td><?= ucfirst(htmlspecialchars($h['action'])) ?></td>
                    <td><?= htmlspecialchars($h['actor_name']) ?></td>
                    <td><?= htmlspecialchars($h['step_label'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($h['remarks'] ?? '—') ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($h['acted_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
