<?php
if (!empty($pending_approvals)):
?>
<!-- PENDING APPROVALS -->
<div style="margin-top: 1rem; margin-bottom: 2rem;">
    <h2 style="font-size: 1.25rem; margin-bottom: 1rem; color: #1f2937;">Pending Review Queue <span style="background:#ef4444; color:white; padding:2px 8px; border-radius:999px; font-size:0.8rem; margin-left:10px;"><?= count($pending_approvals) ?></span></h2>
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb; background: #f8fafc;">
                    <th scope="col" style="padding: 12px 16px; font-weight: 600; color: #475569;">File Title</th>
                    <th scope="col" style="padding: 12px 16px; font-weight: 600; color: #475569;">Type</th>
                    <th scope="col" style="padding: 12px 16px; font-weight: 600; color: #475569;">Uploaded By</th>
                    <th scope="col" style="padding: 12px 16px; font-weight: 600; color: #475569;">Date</th>
                    <th scope="col" style="padding: 12px 16px; font-weight: 600; color: #475569;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_approvals as $doc): ?>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 16px; color: #2563eb; font-weight: 500;">
                        <?= htmlspecialchars($doc['title']) ?>
                    </td>
                    <td style="padding: 12px 16px; color: #64748b;">
                        <?= htmlspecialchars($doc['type_label'] ?? $doc['type_id']) ?>
                    </td>
                    <td style="padding: 12px 16px; color: #64748b;">
                        <?= htmlspecialchars($doc['uploader_name'] ?? 'Unknown') ?>
                    </td>
                    <?php 
                    $event_dt = (!empty($doc['event_date']) && $doc['event_date'] !== '0000-00-00') ? $doc['event_date'] : ($doc['created_at'] ?? '');
                    $event_ts = !empty($event_dt) ? strtotime($event_dt) : false;
                    $created_ts = !empty($doc['created_at']) ? strtotime($doc['created_at']) : false;
                    ?>
                    <td style="padding: 12px 16px; color: #64748b;">
                        <div style="font-weight: 600; color: #1e293b;"><?= $event_ts ? date('M d, Y', $event_ts) : '—' ?></div>
                        <?php if (!empty($doc['event_date']) && $event_ts && $created_ts && date('Y-m-d', $event_ts) !== date('Y-m-d', $created_ts)): ?>
                            <div style="font-size: 0.72rem; color: #94a3b8;" title="Uploaded on <?= date('M d, Y', $created_ts) ?>">Up: <?= date('M d, Y', $created_ts) ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 12px 16px;">
                        <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&amp;id=<?= (int)$doc['doc_id'] ?>&amp;mode=review" style="background: #10b981; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.875rem;">Review</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
