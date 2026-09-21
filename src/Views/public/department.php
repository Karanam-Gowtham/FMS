<?php
include_once HEADER;
?>
<link rel="stylesheet" href="<?= CSS_PATH ?>/portal.css">
<link rel="stylesheet" href="<?= CSS_PATH ?>/dashboard.css">
<style>
    .dept-hero {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.92), rgba(30, 41, 59, 0.95));
        background-size: cover;
        background-position: center;
        padding: 50px 30px;
        text-align: center;
        color: white;
        margin-top: 60px;
    }
    .dept-hero h1 { font-size: 2.5rem; margin-bottom: 10px; }
    .dept-hero p { font-size: 1.1rem; color: #94a3b8; }
    
    .stats-container {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
        flex-wrap: wrap;
    }
    .stat-card {
        background: rgba(255,255,255,0.1);
        padding: 20px 30px;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        min-width: 150px;
    }
    .stat-number { font-size: 2rem; font-weight: bold; color: #60a5fa; margin-bottom: 5px; }
    .stat-label { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #cbd5e1; }

    .dept-content { max-width: 1200px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 300px 1fr; gap: 30px; }
    @media(max-width: 900px) { .dept-content { grid-template-columns: 1fr; } }
    
    .faculty-list { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); align-self: start; }
    .faculty-list h3 { font-size: 1.2rem; color: #1e293b; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; }
    .faculty-item { display: flex; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
    .faculty-item:last-child { border-bottom: none; }
    .faculty-avatar { width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; }
    .faculty-info { flex: 1; }
    .faculty-name { font-weight: 600; color: #334155; }
    .faculty-role { font-size: 0.8rem; color: #64748b; margin-top: 3px; }

    .docs-section { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .docs-section h3 { font-size: 1.2rem; color: #1e293b; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; }
    
    .public-table { width: 100%; border-collapse: collapse; }
    .public-table th, .public-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
    .public-table th { background: #f8fafc; font-weight: 600; color: #475569; font-size: 0.9rem; }
    .public-table td { font-size: 0.95rem; color: #334155; }
    .public-table tr:hover { background: #f1f5f9; }
    .type-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 500; background: #e0f2fe; color: #0284c7; }
    .download-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f1f5f9; color: #475569; border-radius: 6px; text-decoration: none; font-size: 0.85rem; transition: all 0.2s; border: 1px solid #cbd5e1; }
    .download-btn:hover { background: #3b82f6; color: white; border-color: #3b82f6; }
</style>

<div class="dept-hero">
    <h1><?= htmlspecialchars($dept_name) ?> Department</h1>
    <p>Public Research & Document Repository</p>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-number"><?= $papers_count ?></div>
            <div class="stat-label">Papers</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $patents_count ?></div>
            <div class="stat-label">Patents</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $fdps_count ?></div>
            <div class="stat-label">FDPs</div>
        </div>
    </div>
</div>

<div class="dept-content">
    <div class="faculty-list">
        <h3>Department Faculty</h3>
        <?php if (empty($faculty)): ?>
            <p style="color:#64748b; font-size:0.9rem;">No active faculty found.</p>
        <?php else: ?>
            <?php foreach ($faculty as $fac): ?>
                <div class="faculty-item">
                    <div class="faculty-avatar">
                        <?= strtoupper(substr($fac['full_name'], 0, 1)) ?>
                    </div>
                    <div class="faculty-info">
                        <div class="faculty-name"><?= htmlspecialchars($fac['full_name']) ?></div>
                        <div class="faculty-role"><?= htmlspecialchars($fac['role_name']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="docs-section">
        <h3>Public Documents</h3>
        <?php if (empty($public_docs)): ?>
            <div class="empty-state" style="padding: 40px; text-align: center; color: #64748b;">
                <p>No public documents have been accepted for this department yet.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="public-table">
                    <thead>
                        <tr>
                            <th>Document Title</th>
                            <th>Type</th>
                            <th>Author / Uploader</th>
                            <th>Academic Year</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($public_docs as $doc): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($doc['original_file_name'] ?: 'Untitled Document') ?></strong>
                                </td>
                                <td><span class="type-badge"><?= htmlspecialchars($doc['type_name'] ?: 'Document') ?></span></td>
                                <td><?= htmlspecialchars($doc['uploader_name'] ?: 'System') ?></td>
                                <td><?= htmlspecialchars($doc['year_name'] ?: 'N/A') ?></td>
                                <td>
                                    <?php if ($doc['file_path'] !== '#'): ?>
                                        <a href="<?= BASE_URL ?>/<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="download-btn">
                                            View File
                                        </a>
                                    <?php else: ?>
                                        <span style="color:#94a3b8; font-size:0.85rem;">No File</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
