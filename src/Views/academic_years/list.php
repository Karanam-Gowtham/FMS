<?php
/**
 * Institutional Academic Year Management View
 */
require_once __DIR__ . '/../../../core/bootstrap.php';
include __DIR__ . '/../../../includes/header.php';

$auth = auth_context();
$active_role = $auth['active_role'] ?? [];
$is_admin = ((int)($active_role['role_id'] ?? 0) === ROLE_ADMIN);
$can_manage = in_array((int)($active_role['role_id'] ?? 0), [ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_HOD], true);
?>

<div class="container" style="max-width: 1300px; margin: 2rem auto; padding: 0 1.25rem;">

    <!-- Breadcrumb & Header Title -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 6px;">
                <a href="<?= BASE_URL ?>/public/index.php?route=dashboard" style="color: #64748b; text-decoration: none;">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i>
                <span style="color: #0f172a; font-weight: 600;">Academic Years</span>
            </div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-calendar-alt" style="color: #f59e0b;"></i>
                Academic Year Management
            </h1>
            <p style="margin: 0.3rem 0 0; color: #64748b; font-size: 0.92rem;">
                Configure college-wide academic calendars, designate active current submission cycles, and audit document distribution.
            </p>
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
            <?php if ($can_manage): ?>
                <button type="button" onclick="openAddYearModal()"
                        style="background: #003366; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(0,51,102,0.2);">
                    <i class="fas fa-plus-circle"></i> Add Academic Year
                </button>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/public/index.php?route=admin/departments" 
               style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 0.58rem 1.1rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-sitemap"></i> Departments
            </a>
        </div>
    </div>

    <!-- Flash Alerts -->
    <?php include __DIR__ . '/../partials/alerts.php'; ?>


    <!-- Summary Metrics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="width: 50px; height: 50px; border-radius: 10px; background: rgba(245, 158, 11, 0.12); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1.1;"><?= htmlspecialchars($stats['active_year']) ?></div>
                <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; margin-top: 3px;">Active Current Cycle</div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="width: 50px; height: 50px; border-radius: 10px; background: rgba(2, 132, 199, 0.12); color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1.1;"><?= number_format($stats['total_years']) ?></div>
                <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; margin-top: 3px;">Total Academic Years</div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="width: 50px; height: 50px; border-radius: 10px; background: rgba(16, 185, 129, 0.12); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-file-contract"></i>
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1.1;"><?= number_format($stats['total_docs']) ?></div>
                <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; margin-top: 3px;">Documents Linked</div>
            </div>
        </div>

    </div>

    <!-- Academic Years Table Card -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 2rem;">
        
        <div style="padding: 1rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">
                <i class="fas fa-list-ol text-primary me-2"></i> All Academic Years
            </div>
            <div style="font-size: 0.82rem; color: #64748b;">
                Showing <?= count($academic_years) ?> year(s)
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 0.85rem 1rem; width: 60px; text-align: center;">S.No.</th>
                        <th style="padding: 0.85rem 1rem; width: 180px;">Academic Year</th>
                        <th style="padding: 0.85rem 1rem; width: 220px;">Current Active Status</th>
                        <th style="padding: 0.85rem 1rem; width: 160px; text-align: center;">Linked Documents</th>
                        <th style="padding: 0.85rem 1rem; width: 180px;">Created Date</th>
                        <th style="padding: 0.85rem 1rem; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sno = 1;
                    foreach ($academic_years as $ay): 
                        $is_current = ((int)($ay['is_current'] ?? 0) === 1);
                        $doc_count = (int)($ay['doc_count'] ?? 0);
                        $created_ts = !empty($ay['created_at']) ? strtotime($ay['created_at']) : null;
                    ?>
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                        
                        <!-- Contiguous S.No. (1, 2, 3...) -->
                        <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 700; color: #64748b;">
                            <?= $sno++ ?>
                        </td>

                        <!-- Year Label -->
                        <td style="padding: 0.85rem 1rem;">
                            <div style="font-weight: 700; font-size: 1.05rem; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-graduation-cap" style="color: <?= $is_current ? '#10b981' : '#94a3b8' ?>;"></i>
                                <?= htmlspecialchars($ay['year_label'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td style="padding: 0.85rem 1rem;">
                            <?php if ($is_current): ?>
                                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">
                                    <i class="fas fa-check-circle"></i> ACTIVE CURRENT YEAR
                                </span>
                            <?php else: ?>
                                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;">
                                    <i class="fas fa-archive"></i> Archived / Standard
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- Documents Count -->
                        <td style="padding: 0.85rem 1rem; text-align: center;">
                            <?php if ($doc_count > 0): ?>
                                <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&amp;academic_year=<?= urlencode($ay['year_label']) ?>" 
                                   style="display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 6px; background: #e0f2fe; color: #0369a1; text-decoration: none; font-weight: 700; font-size: 0.85rem;" title="View linked documents">
                                    <i class="fas fa-file-alt"></i> <?= number_format($doc_count) ?> document(s)
                                </a>
                            <?php else: ?>
                                <span style="color: #94a3b8; font-size: 0.85rem;">0 documents</span>
                            <?php endif; ?>
                        </td>

                        <!-- Created Timestamp -->
                        <td style="padding: 0.85rem 1rem; color: #64748b; font-size: 0.84rem;">
                            <?= $created_ts ? date('d M Y, h:i A', $created_ts) : 'System Initialized' ?>
                        </td>

                        <!-- Actions -->
                        <td style="padding: 0.85rem 1rem; text-align: right;">
                            <div style="display: inline-flex; gap: 6px; align-items: center;">
                                
                                <!-- NBA / Accreditation Direct Link -->
                                <a href="<?= BASE_URL ?>/public/index.php?route=nba/dashboard&amp;year=<?= urlencode($ay['year_label']) ?>" 
                                   style="background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; padding: 0.35rem 0.65rem; border-radius: 5px; text-decoration: none; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;" title="View in NBA Module">
                                    <i class="fas fa-chart-line" style="color: #6366f1;"></i> NBA
                                </a>

                                <?php if ($can_manage && !$is_current): ?>
                                    <!-- Set as Active Current Year -->
                                    <form action="<?= BASE_URL ?>/public/index.php?route=academic_years/list" method="POST" style="margin: 0;">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="subaction" value="set_current">
                                        <input type="hidden" name="year_id" value="<?= (int)$ay['year_id'] ?>">
                                        <button type="submit" 
                                                style="background: #10b981; color: white; border: none; padding: 0.35rem 0.65rem; border-radius: 5px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
                                                title="Set as Default Active Year for New Uploads">
                                            <i class="fas fa-check"></i> Make Active
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($is_admin): ?>
                                    <?php if ($is_current): ?>
                                        <button type="button" disabled 
                                                style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; padding: 0.35rem 0.65rem; border-radius: 5px; font-size: 0.8rem; cursor: not-allowed;" 
                                                title="Active academic year cannot be deleted">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    <?php elseif ($doc_count > 0): ?>
                                        <button type="button" disabled 
                                                style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; padding: 0.35rem 0.65rem; border-radius: 5px; font-size: 0.8rem; cursor: not-allowed;" 
                                                title="Protected: referenced by <?= $doc_count ?> document(s)">
                                            <i class="fas fa-shield-alt"></i> Protected
                                        </button>
                                    <?php else: ?>
                                        <!-- Safe Delete Trigger -->
                                        <button type="button" 
                                                onclick="openDeleteYearModal(<?= (int)$ay['year_id'] ?>, '<?= htmlspecialchars(addslashes($ay['year_label']), ENT_QUOTES, 'UTF-8') ?>')"
                                                style="background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; padding: 0.35rem 0.65rem; border-radius: 5px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
                                                title="Delete unused academic year">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>

                            </div>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- Modal 1: Add Academic Year -->
<div id="addYearModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1050; align-items: center; justify-content: center; backdrop-filter: blur(2px);">
    <div style="background: white; border-radius: 12px; width: 100%; max-width: 480px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; margin: 1.5rem;">
        
        <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-calendar-plus" style="color: #f59e0b;"></i>
                Add Academic Year
            </h3>
            <button type="button" onclick="closeAddYearModal()" style="background: none; border: none; color: #64748b; font-size: 1.25rem; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="<?= BASE_URL ?>/public/index.php?route=academic_years/list" method="POST" style="padding: 1.5rem;">
            <?= csrfField() ?>
            <input type="hidden" name="subaction" value="create">

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Academic Year Label <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" name="academic_year" required placeholder="e.g. 2027-28" pattern="\d{4}-\d{2}"
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; font-weight: 600; color: #0f172a;"
                       title="Format must be YYYY-YY (e.g. 2027-28)">
                <div style="font-size: 0.78rem; color: #64748b; margin-top: 5px;">
                    Must follow consecutive year format <strong>YYYY-YY</strong> (e.g., 2027-28).
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.85rem; margin-bottom: 1.5rem; font-size: 0.82rem; color: #475569;">
                <i class="fas fa-info-circle text-primary me-1"></i>
                New academic years are created in standard archived status. You can designate it as the active current cycle at any time.
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeAddYearModal()" 
                        style="padding: 0.6rem 1.1rem; border-radius: 6px; border: 1px solid #cbd5e1; background: white; color: #475569; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" 
                        style="padding: 0.6rem 1.3rem; border-radius: 6px; border: none; background: #003366; color: white; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                    <i class="fas fa-check me-1"></i> Save Academic Year
                </button>
            </div>
        </form>

    </div>
</div>

<!-- Modal 2: Delete Year Confirmation -->
<div id="deleteYearModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1050; align-items: center; justify-content: center; backdrop-filter: blur(2px);">
    <div style="background: white; border-radius: 12px; width: 100%; max-width: 440px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; margin: 1.5rem;">
        
        <div style="padding: 1.25rem 1.5rem; background: #fef2f2; border-bottom: 1px solid #fee2e2; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #991b1b; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-trash-alt"></i> Delete Academic Year
            </h3>
            <button type="button" onclick="closeDeleteYearModal()" style="background: none; border: none; color: #991b1b; font-size: 1.25rem; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="<?= BASE_URL ?>/public/index.php?route=academic_years/list" method="POST" style="padding: 1.5rem;">
            <?= csrfField() ?>
            <input type="hidden" name="subaction" value="delete">
            <input type="hidden" name="year_id" id="deleteYearId" value="">

            <p style="margin: 0 0 1rem; color: #334155; font-size: 0.95rem; line-height: 1.5;">
                Are you sure you want to delete unused academic year <strong id="deleteYearLabelText" style="color: #0f172a;"></strong>?
            </p>

            <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 6px; padding: 0.75rem; margin-bottom: 1.5rem; font-size: 0.8rem; color: #991b1b;">
                <i class="fas fa-exclamation-triangle me-1"></i>
                This action is permanent and will be logged in the college-wide audit trail. Only academic years with 0 linked documents can be deleted.
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeDeleteYearModal()" 
                        style="padding: 0.6rem 1.1rem; border-radius: 6px; border: 1px solid #cbd5e1; background: white; color: #475569; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" 
                        style="padding: 0.6rem 1.3rem; border-radius: 6px; border: none; background: #dc2626; color: white; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                    <i class="fas fa-trash-alt me-1"></i> Permanently Delete
                </button>
            </div>
        </form>

    </div>
</div>

<script>
function openAddYearModal() {
    const modal = document.getElementById('addYearModal');
    if (modal) modal.style.display = 'flex';
}
function closeAddYearModal() {
    const modal = document.getElementById('addYearModal');
    if (modal) modal.style.display = 'none';
}
function openDeleteYearModal(yearId, yearLabel) {
    document.getElementById('deleteYearId').value = yearId;
    document.getElementById('deleteYearLabelText').innerText = yearLabel;
    const modal = document.getElementById('deleteYearModal');
    if (modal) modal.style.display = 'flex';
}
function closeDeleteYearModal() {
    const modal = document.getElementById('deleteYearModal');
    if (modal) modal.style.display = 'none';
}

window.addEventListener('click', function(e) {
    const m1 = document.getElementById('addYearModal');
    const m2 = document.getElementById('deleteYearModal');
    if (e.target === m1) closeAddYearModal();
    if (e.target === m2) closeDeleteYearModal();
});
</script>

</main>
</body>
</html>
