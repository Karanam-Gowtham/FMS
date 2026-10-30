<?php
/**
 * College-wide Audit Trail & Activity Monitoring View
 */
require_once __DIR__ . '/../../../core/bootstrap.php';
include __DIR__ . '/../../../includes/header.php';

$auth = auth_context();
$active_role = $auth['active_role'] ?? [];
$is_admin = ((int)($active_role['role_id'] ?? 0) === ROLE_ADMIN);
?>

<div class="container" style="max-width: 1400px; margin: 2rem auto; padding: 0 1.25rem;">

    <!-- Breadcrumb & Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 6px;">
                <a href="<?= BASE_URL ?>/public/index.php?route=dashboard" style="color: #64748b; text-decoration: none;">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i>
                <span style="color: #0f172a; font-weight: 600;">College-wide Audit Trail</span>
            </div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-history" style="color: #0284c7;"></i>
                Institutional Audit &amp; Activity Log
            </h1>
            <p style="margin: 0.3rem 0 0; color: #64748b; font-size: 0.92rem;">
                Immutable event stream of all document submissions, approvals, rejections, leadership assignments, and administrative security changes.
            </p>
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="<?= BASE_URL ?>/public/index.php?<?= htmlspecialchars(http_build_query(array_merge($_GET, ['export' => 'csv'])), ENT_QUOTES, 'UTF-8') ?>" 
               style="background: #10b981; color: white; padding: 0.55rem 1.15rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);">
                <i class="fas fa-file-csv"></i> Export Filtered CSV
            </a>
            <?php if ($is_admin): ?>
                <a href="<?= BASE_URL ?>/public/index.php?route=admin/users" 
                   style="background: #003366; color: white; padding: 0.55rem 1.15rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-users-cog"></i> User Management
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Metric Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 1.8rem;">
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(2, 132, 199, 0.12); color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fas fa-stream"></i>
            </div>
            <div>
                <div style="font-size: 1.55rem; font-weight: 800; color: #0f172a; line-height: 1.1;"><?= number_format($stats['total_events']) ?></div>
                <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; margin-top: 3px;">Total Audit Events</div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(16, 185, 129, 0.12); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <div style="font-size: 1.55rem; font-weight: 800; color: #0f172a; line-height: 1.1;"><?= number_format($stats['approved']) ?></div>
                <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; margin-top: 3px;">Workflow Approvals</div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(239, 68, 68, 0.12); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fas fa-ban"></i>
            </div>
            <div>
                <div style="font-size: 1.55rem; font-weight: 800; color: #0f172a; line-height: 1.1;"><?= number_format($stats['rejected']) ?></div>
                <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; margin-top: 3px;">Workflow Rejections</div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(139, 92, 246, 0.12); color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <div style="font-size: 1.55rem; font-weight: 800; color: #0f172a; line-height: 1.1;"><?= number_format($stats['sys_events']) ?></div>
                <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; margin-top: 3px;">Security &amp; Admin Logs</div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; margin-bottom: 1.8rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <form method="GET" action="<?= BASE_URL ?>/public/index.php" style="margin: 0;">
            <input type="hidden" name="route" value="admin/audit_logs">
            <input type="hidden" name="page" value="1">

            <!-- Stream Switcher Tabs -->
            <div style="display: flex; gap: 8px; margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.85rem; flex-wrap: wrap;">
                <button type="submit" name="type" value="all" 
                        style="border: none; padding: 6px 14px; border-radius: 6px; font-weight: 600; font-size: 0.88rem; cursor: pointer; transition: all 0.2s; <?= ($source_type === 'all') ? 'background: #003366; color: white;' : 'background: #f1f5f9; color: #475569;' ?>">
                    <i class="fas fa-layer-group me-1"></i> All Activities
                </button>
                <button type="submit" name="type" value="document" 
                        style="border: none; padding: 6px 14px; border-radius: 6px; font-weight: 600; font-size: 0.88rem; cursor: pointer; transition: all 0.2s; <?= ($source_type === 'document') ? 'background: #003366; color: white;' : 'background: #f1f5f9; color: #475569;' ?>">
                    <i class="fas fa-file-alt me-1"></i> Document Lifecycle
                </button>
                <button type="submit" name="type" value="system" 
                        style="border: none; padding: 6px 14px; border-radius: 6px; font-weight: 600; font-size: 0.88rem; cursor: pointer; transition: all 0.2s; <?= ($source_type === 'system') ? 'background: #003366; color: white;' : 'background: #f1f5f9; color: #475569;' ?>">
                    <i class="fas fa-user-shield me-1"></i> Security &amp; Administrative Actions
                </button>
            </div>

            <!-- Filter Controls Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: flex-end;">
                
                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; text-transform: uppercase;">Action</label>
                    <select name="action" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; background: #fff;">
                        <option value="">All Actions</option>
                        <optgroup label="Document Workflows">
                            <?php foreach ($available_actions['document'] as $k => $v): ?>
                                <option value="<?= htmlspecialchars($k) ?>" <?= ($filter_action === $k) ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="System &amp; Security">
                            <?php foreach ($available_actions['system'] as $k => $v): ?>
                                <option value="<?= htmlspecialchars($k) ?>" <?= ($filter_action === $k) ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; text-transform: uppercase;">Department</label>
                    <select name="dept_id" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; background: #fff;">
                        <option value="0">All Departments</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= (int)$d['dept_id'] ?>" <?= ($filter_dept === (int)$d['dept_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['dept_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; text-transform: uppercase;">From Date</label>
                    <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>" 
                           style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; text-transform: uppercase;">To Date</label>
                    <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>" 
                           style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; text-transform: uppercase;">Search Query</label>
                    <input type="text" name="search" placeholder="Actor, title, details..." value="<?= htmlspecialchars($search) ?>" 
                           style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem;">
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" style="flex: 1; background: #003366; color: white; border: none; padding: 0.55rem 1rem; border-radius: 6px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                        <i class="fas fa-filter me-1"></i> Apply
                    </button>
                    <a href="<?= BASE_URL ?>/public/index.php?route=admin/audit_logs" 
                       style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 0.55rem 0.85rem; border-radius: 6px; font-weight: 600; font-size: 0.88rem; text-decoration: none; display: inline-flex; align-items: center;" title="Reset filters">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>

            </div>
        </form>
    </div>

    <!-- Results Table Card -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 2rem;">
        
        <div style="padding: 1rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">
                Showing <?= count($logs) ?> of <?= number_format($total_records) ?> recorded audit event(s)
            </div>
            <div style="font-size: 0.82rem; color: #64748b;">
                Page <?= $page ?> of <?= max(1, $total_pages) ?>
            </div>
        </div>

        <?php if (empty($logs)): ?>
            <div style="text-align: center; padding: 3.5rem 1rem; color: #64748b;">
                <i class="fas fa-clipboard-check" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.75rem;"></i>
                <h3 style="margin: 0; font-size: 1.15rem; color: #334155;">No audit events found</h3>
                <p style="margin: 0.35rem 0 0; font-size: 0.9rem;">Try adjusting the filter criteria or date range.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <th style="padding: 0.75rem 1rem; width: 55px; text-align: center;">S.No.</th>
                            <th style="padding: 0.75rem 1rem; width: 140px;">Timestamp</th>
                            <th style="padding: 0.75rem 1rem; width: 100px;">Stream</th>
                            <th style="padding: 0.75rem 1rem; width: 140px;">Action</th>
                            <th style="padding: 0.75rem 1rem; width: 180px;">Actor</th>
                            <th style="padding: 0.75rem 1rem; width: 120px;">Department</th>
                            <th style="padding: 0.75rem 1rem;">Subject / Entity</th>
                            <th style="padding: 0.75rem 1rem;">Stage / Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sno = $offset + 1;
                        foreach ($logs as $log): 
                            $act = strtolower((string)$log['action_name']);
                            
                            // Badge color styling
                            if (in_array($act, ['approved', 'coordinator_appoint', 'hod_assign'], true)) {
                                $badge_style = 'background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;';
                                $act_icon = 'fa-check-circle';
                            } elseif (in_array($act, ['rejected', 'document_purge', 'user_delete', 'coordinator_revoke'], true)) {
                                $badge_style = 'background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;';
                                $act_icon = 'fa-times-circle';
                            } elseif (in_array($act, ['user_status_change', 'password_reset', 'resubmitted'], true)) {
                                $badge_style = 'background: #fef3c7; color: #92400e; border: 1px solid #fde68a;';
                                $act_icon = 'fa-exclamation-circle';
                            } else {
                                $badge_style = 'background: #e0f2fe; color: #075985; border: 1px solid #bae6fd;';
                                $act_icon = 'fa-info-circle';
                            }

                            $is_doc = ($log['source_type'] === 'document');
                            $event_ts = strtotime($log['event_time']);
                        ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            
                            <!-- Contiguous S.No. (1, 2, 3...) -->
                            <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: #64748b;">
                                <?= $sno++ ?>
                            </td>

                            <!-- Timestamp -->
                            <td style="padding: 0.75rem 1rem; white-space: nowrap;">
                                <div style="font-weight: 600; color: #0f172a;"><?= $event_ts ? date('d M Y', $event_ts) : '—' ?></div>
                                <div style="font-size: 0.75rem; color: #64748b;"><?= $event_ts ? date('h:i A', $event_ts) : '' ?></div>
                            </td>

                            <!-- Stream Badge -->
                            <td style="padding: 0.75rem 1rem;">
                                <?php if ($is_doc): ?>
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background: #f1f5f9; color: #475569;">
                                        <i class="fas fa-file-alt" style="color: #0284c7;"></i> Document
                                    </span>
                                <?php else: ?>
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background: #fdf4ff; color: #86198f; border: 1px solid #f5d0fe;">
                                        <i class="fas fa-shield-alt" style="color: #a855f7;"></i> Security
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Action Badge -->
                            <td style="padding: 0.75rem 1rem;">
                                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; <?= $badge_style ?>">
                                    <i class="fas <?= $act_icon ?>"></i>
                                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', (string)$log['action_name']))) ?>
                                </span>
                            </td>

                            <!-- Actor -->
                            <td style="padding: 0.75rem 1rem;">
                                <div style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($log['actor_name']) ?></div>
                                <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($log['actor_email']) ?></div>
                            </td>

                            <!-- Department -->
                            <td style="padding: 0.75rem 1rem;">
                                <span style="font-weight: 600; color: #334155; font-size: 0.82rem; background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">
                                    <?= htmlspecialchars($log['dept_name']) ?>
                                </span>
                            </td>

                            <!-- Subject / Entity -->
                            <td style="padding: 0.75rem 1rem;">
                                <?php if ($is_doc && !empty($log['target_id'])): ?>
                                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&amp;id=<?= (int)$log['target_id'] ?>" 
                                       style="font-weight: 600; color: #0284c7; text-decoration: none;" title="View Document #<?= (int)$log['target_id'] ?>">
                                        <?= htmlspecialchars($log['target_title'] ?: 'Document #' . $log['target_id']) ?>
                                    </a>
                                    <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($log['target_type_label']) ?></div>
                                <?php else: ?>
                                    <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($log['target_title']) ?></div>
                                    <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($log['target_type_label']) ?></div>
                                <?php endif; ?>
                            </td>

                            <!-- Remarks / Details -->
                            <td style="padding: 0.75rem 1rem; max-width: 280px;">
                                <?php if (!empty($log['stage_label']) && $is_doc): ?>
                                    <div style="font-weight: 600; font-size: 0.78rem; color: #475569; margin-bottom: 2px;">
                                        <i class="fas fa-sitemap" style="color: #94a3b8; font-size: 0.72rem;"></i> <?= htmlspecialchars($log['stage_label']) ?>
                                    </div>
                                <?php endif; ?>
                                <div style="color: #334155; font-size: 0.82rem; line-height: 1.4;">
                                    <?= !empty($log['event_details']) ? htmlspecialchars($log['event_details']) : '<span style="color: #94a3b8; font-style: italic;">No additional remarks</span>' ?>
                                </div>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <?php if ($total_pages > 1): ?>
                <div style="display: flex; justify-content: center; align-items: center; gap: 6px; padding: 1.25rem; background: #f8fafc; border-top: 1px solid #e2e8f0;">
                    <?php if ($page > 1): ?>
                        <a href="<?= BASE_URL ?>/public/index.php?<?= htmlspecialchars(http_build_query(array_merge($_GET, ['page' => $page - 1]))) ?>" 
                           style="padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; color: #475569; font-weight: 600; font-size: 0.85rem; background: white;">
                            &laquo; Previous
                        </a>
                    <?php endif; ?>

                    <?php 
                    $start_p = max(1, $page - 3);
                    $end_p   = min($total_pages, $page + 3);
                    for ($p = $start_p; $p <= $end_p; $p++): 
                    ?>
                        <a href="<?= BASE_URL ?>/public/index.php?<?= htmlspecialchars(http_build_query(array_merge($_GET, ['page' => $p]))) ?>" 
                           style="padding: 6px 12px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; <?= ($p === $page) ? 'background: #003366; color: white; border: 1px solid #003366;' : 'background: white; color: #475569; border: 1px solid #cbd5e1;' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="<?= BASE_URL ?>/public/index.php?<?= htmlspecialchars(http_build_query(array_merge($_GET, ['page' => $page + 1]))) ?>" 
                           style="padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; color: #475569; font-weight: 600; font-size: 0.85rem; background: white;">
                            Next &raquo;
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

</main>
</body>
</html>
