<?php
/**
 * Recent User Submissions Table
 */
?>
<?php if (empty($current_view)): ?>
<?php if (($is_faculty || !empty($recent_uploads)) && !$is_admin): ?>
<!-- MY SUBMISSIONS -->
<div class="header-title" style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 10px;">
    <div>
        <h1 style="margin-bottom: 0.25rem;">My Submissions</h1>
        <p style="color: #64748b; margin: 0; font-size: 0.9rem;">Documents you have uploaded and their current workflow status.</p>
    </div>
    <a href="<?= BASE_URL ?>/public/index.php?route=documents/my_uploads" style="color: #2563eb; font-size: 0.9rem; font-weight: 600; text-decoration: none;">View All My Uploads &rarr;</a>
</div>

<?php if (!empty($recent_uploads)): ?>
<div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x: auto; margin-bottom: 2rem;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="border-bottom: 2px solid #e5e7eb; background: #f8fafc;">
                <th style="padding: 12px 16px; font-weight: 600; color: #475569; width: 60px; text-align: center;">S.No.</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #475569;">File Title</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Type</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Date</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Status</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sno_r = 1;
            foreach ($recent_uploads as $doc): 
                $event_dt = (!empty($doc['event_date']) && $doc['event_date'] !== '0000-00-00') ? $doc['event_date'] : ($doc['created_at'] ?? '');
                $event_ts = !empty($event_dt) ? strtotime($event_dt) : false;
                $created_ts = !empty($doc['created_at']) ? strtotime($doc['created_at']) : false;
            ?>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px; text-align: center; font-weight: 700; color: #64748b;">
                    <?= $sno_r++ ?>
                </td>
                <td style="padding: 12px 16px; color: #2563eb; font-weight: 500;">
                    <?= htmlspecialchars($doc['title']) ?>
                </td>
                <td style="padding: 12px 16px; color: #64748b;">
                    <?= htmlspecialchars($doc['type_label']) ?>
                </td>
                <td style="padding: 12px 16px; color: #64748b;">
                    <div style="font-weight: 600; color: #1e293b;"><?= $event_ts ? date('M d, Y', $event_ts) : '—' ?></div>
                    <?php if (!empty($doc['event_date']) && $event_ts && $created_ts && date('Y-m-d', $event_ts) !== date('Y-m-d', $created_ts)): ?>
                        <div style="font-size: 0.72rem; color: #94a3b8;" title="Uploaded on <?= date('M d, Y', $created_ts) ?>">Up: <?= date('M d, Y', $created_ts) ?></div>
                    <?php endif; ?>
                </td>
                <td style="padding: 12px 16px;">
                    <?php
                    $status_lower = strtolower($doc['status']);
                    if ($status_lower === 'accepted') {
                        $bg = '#dcfce7'; $color = '#166534'; $label = 'Accepted';
                    } elseif ($status_lower === 'rejected') {
                        $bg = '#fee2e2'; $color = '#991b1b'; $label = 'Rejected';
                    } else {
                        $bg = '#fef9c3'; $color = '#854d0e'; $label = 'Pending ' . $doc['step_label'];
                    }
                    ?>
                    <span style="background: <?= $bg ?>; color: <?= $color ?>; padding: 4px 10px; border-radius: 9999px; font-size: 0.85rem; font-weight: 500;">
                        <?= htmlspecialchars($label) ?>
                    </span>
                </td>
                <td style="padding: 12px 16px;">
                    <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                        <?php 
                        $can_user_approve_this = doc_can_approve($conn, $auth, $doc);
                        if ($can_user_approve_this): 
                        ?>
                            <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&amp;id=<?= (int)$doc['doc_id'] ?>&amp;mode=review" style="background: #10b981; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.875rem; display: inline-block; white-space: nowrap; font-weight: 500;">Review</a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&amp;id=<?= (int)$doc['doc_id'] ?>" style="background: #3b82f6; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.875rem; display: inline-block; white-space: nowrap; font-weight: 500;">View</a>
                        <a href="<?= BASE_URL ?>/public/index.php?route=documents/download&amp;doc_id=<?= (int)$doc['doc_id'] ?>&amp;inline=1" target="_blank" style="background: #059669; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; font-weight: 500;" title="View Merged / Primary PDF in new tab">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        <?php if (doc_can_edit($conn, $auth, $doc)): ?>
                        <a href="<?= BASE_URL ?>/public/index.php?route=documents/edit&amp;id=<?= (int)$doc['doc_id'] ?>" style="background: #eab308; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.875rem; display: inline-block; white-space: nowrap; font-weight: 500;">Update</a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
    <p style="color: #64748b; margin-bottom: 2rem;">No recent uploads found.</p>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
