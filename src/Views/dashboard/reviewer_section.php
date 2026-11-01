<?php
/**
 * Reviewer & Department Manager Dashboard Section (HOD, Coordinator, Dean, Assistant)
 */
?>
<?php if ($current_view === 'upload_dept_files'): ?>
    <div class="header-title d-flex justify-content-between align-items-center" style="margin-top: 1rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 1.5rem;">
        <h1 style="font-size: 1.4rem; margin: 0;">Upload Department Files</h1>
        <a href="<?= BASE_URL ?>/public/index.php?route=dashboard" class="my-achievements-btn" style="margin: 0; padding: 0.4rem 1rem; font-size: 0.85rem; background: #64748b;">&larr; Back to Dashboard</a>
    </div>
    <?php include __DIR__ . '/../partials/dept_upload_cards.php'; ?>
<?php else: ?>
    <!-- PREMIUM REVIEWER OVERVIEW -->
    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border-radius: 12px; padding: 2.5rem; margin-top: 2rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); position: relative; overflow: hidden;">
        <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.03); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -80px; left: 10%; width: 300px; height: 300px; background: rgba(255,255,255,0.02); border-radius: 50%;"></div>
        
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; position: relative; z-index: 2; gap: 2rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 0.5rem; flex-wrap: wrap;">
                    <h1 style="font-size: 2rem; font-weight: 800; margin: 0; letter-spacing: -0.5px;"><?= in_array((int)$active_role['role_id'], [ROLE_RND_DEAN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true) ? 'College Overview' : 'Department Overview' ?></h1>
                </div>
                <p style="font-size: 1.1rem; color: #94a3b8; margin: 0;">Analytics &amp; Activity Summary</p>
            </div>
            
            <div style="display: flex; gap: 1.5rem; background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 12px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.05);">
                <div style="text-align: center;">
                    <div style="font-size: 2.2rem; font-weight: 800; color: #60a5fa; line-height: 1;"><?= !empty($dept_stats) && is_array($dept_stats) ? array_sum($dept_stats) : 0 ?></div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 0.5rem;">Total Submissions</div>
                </div>
                <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
                <div style="text-align: center;">
                    <div style="font-size: 2.2rem; font-weight: 800; color: #34d399; line-height: 1;"><?= $dept_stats['accepted'] ?? 0 ?></div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 0.5rem;">Approved</div>
                </div>
                <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
                <div style="text-align: center;">
                    <div style="font-size: 2.2rem; font-weight: 800; color: #fbbf24; line-height: 1;"><?= $dept_stats['pending'] ?? 0 ?></div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 0.5rem;">Pending Review</div>
                </div>
            </div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
        <!-- Categories -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0;">
            <div style="background: #f8fafc; padding: 1.2rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
                <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700; display: flex; align-items: center;"><i class="fas fa-chart-pie me-2" style="color: #6366f1;"></i> Submissions by Category</h3>
            </div>
            <div style="padding: 1.5rem; height: 350px; display: flex; justify-content: center; align-items: center;">
                <?php if (empty($dept_categories)): ?>
                    <p style="color: #94a2b8;">No data available.</p>
                <?php else: ?>
                    <canvas id="deptCategoryChart"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <!-- Timeline -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0;">
            <div style="background: #f8fafc; padding: 1.2rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
                <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700; display: flex; align-items: center;"><i class="fas fa-chart-bar me-2" style="color: #6366f1;"></i> Submission Timeline</h3>
            </div>
            <div style="padding: 1.5rem; height: 350px;">
                <?php if (empty($dept_timeline)): ?>
                    <p style="color: #94a2b8; text-align: center; margin-top: 150px;">No data available.</p>
                <?php else: ?>
                    <canvas id="deptTimelineChart"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const deptColors = ['#6366f1', '#14b8a6', '#f59e0b', '#ec4899', '#8b5cf6', '#0ea5e9'];

        // Categories Chart
        const dCatData = <?= json_encode($dept_categories ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        if (Object.keys(dCatData).length > 0 && document.getElementById('deptCategoryChart')) {
            const ctxCat = document.getElementById('deptCategoryChart').getContext('2d');
            new Chart(ctxCat, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(dCatData),
                    datasets: [{
                        data: Object.values(dCatData),
                        backgroundColor: deptColors,
                        borderWidth: 2, borderColor: '#fff', hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'right', labels: { usePointStyle: true, pointStyle: 'circle' } } },
                    cutout: '65%'
                }
            });
        }

        // Timeline Chart
        const dTimeData = <?= json_encode($dept_timeline ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        if (Object.keys(dTimeData).length > 0 && document.getElementById('deptTimelineChart')) {
            const ctxTime = document.getElementById('deptTimelineChart').getContext('2d');
            let grad = ctxTime.createLinearGradient(0, 0, 0, 400);
            grad.addColorStop(0, '#6366f1'); grad.addColorStop(1, '#312e81');

            new Chart(ctxTime, {
                type: 'bar',
                data: {
                    labels: Object.keys(dTimeData).map(d => {
                        const [y, m] = d.split('-');
                        return new Date(y, m-1).toLocaleString('default', { month: 'short', year: 'numeric' });
                    }),
                    datasets: [{
                        label: 'Submissions',
                        data: Object.values(dTimeData),
                        backgroundColor: grad,
                        borderRadius: 6, maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { drawBorder: false } }, x: { grid: { display: false } } }
                }
            });
        }
    });
    </script>
    
    <div class="header-title" style="margin-top: 1rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 1.5rem;">
        <h1 style="font-size: 1.4rem; margin: 0;">Management Actions</h1>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <?php if ($is_reviewer): ?>
        <a href="<?= BASE_URL ?>/public/index.php?route=documents/list" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #8b5cf6; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(139, 92, 246, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
            <div style="background: #f5f3ff; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                <i class="fas fa-trophy" style="color: #8b5cf6; font-size: 1.2rem;"></i>
            </div>
            <div style="font-weight: 600; font-size: 0.95rem;">Department Achievements</div>
        </a>
        <?php endif; ?>
        
        <?php if ($is_dept_manager): ?>
        <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&amp;context=dept_file" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #06b6d4; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(6, 182, 212, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
            <div style="background: #ecfeff; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                <i class="fas fa-folder-open" style="color: #06b6d4; font-size: 1.2rem;"></i>
            </div>
            <div style="font-weight: 600; font-size: 0.95rem;">View Department Files</div>
        </a>

        <a href="<?= BASE_URL ?>/public/index.php?route=dashboard&amp;view=upload_dept_files" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #10b981; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
            <div style="background: #ecfdf5; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                <i class="fas fa-cloud-upload-alt" style="color: #10b981; font-size: 1.2rem;"></i>
            </div>
            <div style="font-weight: 600; font-size: 0.95rem;">Upload Department Files</div>
        </a>
        <?php endif; ?>

        <?php if (in_array($role_id, [ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_HOD], true)): ?>
        <a href="<?= BASE_URL ?>/public/index.php?route=academic_years/list" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #f59e0b; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(245, 158, 11, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
            <div style="background: #fffbeb; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                <i class="fas fa-calendar-alt" style="color: #f59e0b; font-size: 1.2rem;"></i>
            </div>
            <div style="font-weight: 600; font-size: 0.95rem;">Academic Years</div>
        </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($is_reviewer && empty($current_view)): ?>
<!-- PENDING APPROVALS -->
<div class="header-title" style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 10px;">
    <div>
        <h1 style="margin-bottom: 0.25rem;">Pending My Approval <span style="background: <?= !empty($pending_approvals) ? '#ef4444' : '#64748b' ?>; color:white; padding:2px 8px; border-radius:999px; font-size:0.8rem; margin-left:10px;"><?= count($pending_approvals) ?></span></h1>
        <p style="color: #64748b; margin: 0; font-size: 0.9rem;">Departmental documents submitted by faculty and students awaiting your review.</p>
    </div>
    <?php if (!empty($pending_approvals)): ?>
        <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&amp;mode=pending_approval" style="color: #2563eb; font-size: 0.9rem; font-weight: 600; text-decoration: none;">View All Pending &rarr;</a>
    <?php endif; ?>
</div>

<?php if (!empty($pending_approvals)): ?>
<div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x: auto; margin-bottom: 2rem;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="border-bottom: 2px solid #e5e7eb; background: #f8fafc;">
                <th style="padding: 12px 16px; font-weight: 600; color: #475569; width: 60px; text-align: center;">S.No.</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #475569;">File Title</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Type</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Uploaded By</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Date</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sno_p = 1;
            foreach ($pending_approvals as $doc): 
                $event_dt = (!empty($doc['event_date']) && $doc['event_date'] !== '0000-00-00') ? $doc['event_date'] : ($doc['created_at'] ?? '');
                $event_ts = !empty($event_dt) ? strtotime($event_dt) : false;
                $created_ts = !empty($doc['created_at']) ? strtotime($doc['created_at']) : false;
            ?>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px 16px; text-align: center; font-weight: 700; color: #64748b;"><?= $sno_p++ ?></td>
                <td style="padding: 12px 16px; color: #2563eb; font-weight: 500;"><?= htmlspecialchars($doc['title']) ?></td>
                <td style="padding: 12px 16px; color: #64748b;"><?= htmlspecialchars($doc['type_label'] ?? $doc['type_id']) ?></td>
                <td style="padding: 12px 16px; color: #64748b;"><?= htmlspecialchars($doc['uploader_name'] ?? 'Unknown') ?></td>
                <td style="padding: 12px 16px; color: #64748b;">
                    <div style="font-weight: 600; color: #1e293b;"><?= $event_ts ? date('M d, Y', $event_ts) : '—' ?></div>
                    <?php if (!empty($doc['event_date']) && $event_ts && $created_ts && date('Y-m-d', $event_ts) !== date('Y-m-d', $created_ts)): ?>
                        <div style="font-size: 0.72rem; color: #94a3b8;" title="Uploaded on <?= date('M d, Y', $created_ts) ?>">Up: <?= date('M d, Y', $created_ts) ?></div>
                    <?php endif; ?>
                </td>
                <td style="padding: 12px 16px;">
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&amp;id=<?= (int)$doc['doc_id'] ?>&amp;mode=review" style="background: #10b981; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.875rem; font-weight: 500;">Review</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div style="background: white; border-radius: 8px; border: 1px dashed #cbd5e1; padding: 3rem 1rem; text-align: center; margin-bottom: 2rem;">
    <i class="fas fa-check-circle" style="font-size: 3rem; color: #10b981; opacity: 0.5; margin-bottom: 1rem;"></i>
    <h3 style="color: #475569; font-size: 1.1rem; margin-bottom: 0.5rem;">All caught up!</h3>
    <p style="color: #94a3b8; margin: 0;">You have no pending documents to review right now.</p>
</div>
<?php endif; ?>
<?php endif; ?>
