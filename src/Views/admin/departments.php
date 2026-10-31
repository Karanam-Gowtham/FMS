<?php
/**
 * Institutional Department & Operating Unit Management View
 */
require_once __DIR__ . '/../../../core/bootstrap.php';
include __DIR__ . '/../../../includes/header.php';

$auth = auth_context();
$active_role = $auth['active_role'] ?? [];
$is_admin = ((int)($active_role['role_id'] ?? 0) === ROLE_ADMIN);
$can_manage = in_array((int)($active_role['role_id'] ?? 0), [ROLE_ADMIN, ROLE_IQAC], true);
?>

<div class="container" style="max-width: 1400px; margin: 2rem auto; padding: 0 1.25rem;">

    <!-- Breadcrumb & Header Title -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 6px;">
                <a href="<?= BASE_URL ?>/public/index.php?route=dashboard" style="color: #64748b; text-decoration: none;">Dashboard</a>
                <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i>
                <span style="color: #0f172a; font-weight: 600;">Department Settings</span>
            </div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-sitemap" style="color: #0284c7;"></i>
                Departments &amp; Operating Units
            </h1>
            <p style="margin: 0.3rem 0 0; color: #64748b; font-size: 0.92rem;">
                Manage institutional academic departments, administrative cells, committees, and accreditation classification.
            </p>
        </div>

        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <?php if ($is_admin): ?>
                <button type="button" onclick="openDeptModal()"
                        style="background: #003366; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(0,51,102,0.2);">
                    <i class="fas fa-plus-circle"></i> Add Department / Unit
                </button>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/public/index.php?route=academic_years/list" 
               style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 0.58rem 1.1rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-calendar-alt"></i> Academic Years
            </a>
            <a href="<?= BASE_URL ?>/public/index.php?route=admin/users" 
               style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 0.58rem 1.1rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-users-cog"></i> User Management
            </a>
        </div>
    </div>

    <!-- Flash Alerts -->
    <?php include __DIR__ . '/../partials/alerts.php'; ?>


    <!-- Summary Metrics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(2, 132, 199, 0.12); color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fas fa-sitemap"></i>
            </div>
            <div>
                <div style="font-size: 1.55rem; font-weight: 800; color: #0f172a; line-height: 1.1;"><?= number_format($stats['total_depts']) ?></div>
                <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; margin-top: 3px;">Total Units / Depts</div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(16, 185, 129, 0.12); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <div style="font-size: 1.55rem; font-weight: 800; color: #0f172a; line-height: 1.1;"><?= number_format($stats['academic']) ?></div>
                <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; margin-top: 3px;">Academic Teaching</div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(100, 116, 139, 0.12); color: #475569; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <div style="font-size: 1.55rem; font-weight: 800; color: #0f172a; line-height: 1.1;"><?= number_format($stats['non_academic']) ?></div>
                <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; margin-top: 3px;">Administrative / Support</div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(99, 102, 241, 0.12); color: #6366f1; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div style="font-size: 1.55rem; font-weight: 800; color: #0f172a; line-height: 1.1;"><?= number_format($stats['total_faculty']) ?></div>
                <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; margin-top: 3px;">Faculty Mapped</div>
            </div>
        </div>

    </div>

    <!-- Filter Bar Card -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <form method="GET" action="<?= BASE_URL ?>/public/index.php" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <input type="hidden" name="route" value="admin/departments">

            <!-- Search input -->
            <div style="flex: 1; min-width: 250px;">
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" 
                           placeholder="Search department or unit name..." 
                           style="width: 100%; padding: 0.55rem 0.85rem 0.55rem 2.2rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem;">
                </div>
            </div>

            <!-- Classification Filter -->
            <div style="display: flex; gap: 6px; align-items: center;">
                <a href="<?= BASE_URL ?>/public/index.php?route=admin/departments&amp;type=all<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                   style="padding: 0.5rem 0.9rem; border-radius: 6px; font-size: 0.82rem; font-weight: 600; text-decoration: none; <?= $type_filter === 'all' ? 'background: #003366; color: white;' : 'background: #f1f5f9; color: #475569;' ?>">
                    All Units (<?= $stats['total_depts'] ?>)
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=admin/departments&amp;type=academic<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                   style="padding: 0.5rem 0.9rem; border-radius: 6px; font-size: 0.82rem; font-weight: 600; text-decoration: none; <?= $type_filter === 'academic' ? 'background: #10b981; color: white;' : 'background: #f1f5f9; color: #475569;' ?>">
                    Academic (<?= $stats['academic'] ?>)
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=admin/departments&amp;type=non_academic<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                   style="padding: 0.5rem 0.9rem; border-radius: 6px; font-size: 0.82rem; font-weight: 600; text-decoration: none; <?= $type_filter === 'non_academic' ? 'background: #475569; color: white;' : 'background: #f1f5f9; color: #475569;' ?>">
                    Admin / Cells (<?= $stats['non_academic'] ?>)
                </a>
            </div>

            <button type="submit" style="background: #003366; color: white; border: none; padding: 0.55rem 1rem; border-radius: 6px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                <i class="fas fa-filter me-1"></i> Search
            </button>
            <?php if (!empty($search) || $type_filter !== 'all'): ?>
                <a href="<?= BASE_URL ?>/public/index.php?route=admin/departments" 
                   style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 0.55rem 0.85rem; border-radius: 6px; font-weight: 600; font-size: 0.88rem; text-decoration: none;" title="Reset filters">
                    <i class="fas fa-undo"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Departments Table Card -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 2rem;">
        
        <div style="padding: 1rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">
                Showing <?= count($departments) ?> of <?= number_format($stats['total_depts']) ?> department(s)
            </div>
            <div style="font-size: 0.82rem; color: #64748b;">
                Active Academic Classification Engine
            </div>
        </div>

        <?php if (empty($departments)): ?>
            <div style="text-align: center; padding: 3.5rem 1rem; color: #64748b;">
                <i class="fas fa-building" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.75rem;"></i>
                <h3 style="margin: 0; font-size: 1.15rem; color: #334155;">No departments found</h3>
                <p style="margin: 0.35rem 0 0; font-size: 0.9rem;">Try adjusting the search query or classification filter.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <th style="padding: 0.85rem 1rem; width: 60px; text-align: center;">S.No.</th>
                            <th style="padding: 0.85rem 1rem;">Department / Unit Name</th>
                            <th style="padding: 0.85rem 1rem; width: 220px;">Unit Classification</th>
                            <th style="padding: 0.85rem 1rem; width: 200px;">Head of Dept (HOD)</th>
                            <th style="padding: 0.85rem 1rem; width: 130px; text-align: center;">Faculty Count</th>
                            <th style="padding: 0.85rem 1rem; width: 140px; text-align: center;">Documents</th>
                            <th style="padding: 0.85rem 1rem; width: 180px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sno = 1;
                        foreach ($departments as $d): 
                            $is_acad = ((int)($d['is_academic'] ?? 0) === 1);
                            $fac_cnt = (int)($d['faculty_count'] ?? 0);
                            $doc_cnt = (int)($d['doc_count'] ?? 0);
                            $hod_name = $d['hod_name'] ?? null;
                        ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            
                            <!-- Contiguous S.No. (1, 2, 3...) -->
                            <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 700; color: #64748b;">
                                <?= $sno++ ?>
                            </td>

                            <!-- Department Name -->
                            <td style="padding: 0.85rem 1rem;">
                                <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a;">
                                    <?= htmlspecialchars($d['dept_name'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">
                                    Unit ID #<?= (int)$d['dept_id'] ?>
                                </div>
                            </td>

                            <!-- Classification Badge -->
                            <td style="padding: 0.85rem 1rem;">
                                <?php if ($is_acad): ?>
                                    <span style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;">
                                        <i class="fas fa-graduation-cap"></i> Academic Department
                                    </span>
                                <?php else: ?>
                                    <span style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">
                                        <i class="fas fa-building"></i> Administrative / Cell
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Current HOD -->
                            <td style="padding: 0.85rem 1rem;">
                                <?php if (!empty($hod_name)): ?>
                                    <div style="font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-user-tie" style="color: #0284c7;"></i>
                                        <?= htmlspecialchars($hod_name, ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php else: ?>
                                    <?php if ($is_acad): ?>
                                        <a href="<?= BASE_URL ?>/public/index.php?route=admin/users&amp;dept_id=<?= (int)$d['dept_id'] ?>" 
                                           style="font-size: 0.8rem; color: #f59e0b; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;" title="Assign HOD in User Management">
                                            <i class="fas fa-user-plus"></i> Not Appointed
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 0.82rem;">N/A (Central Cell)</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>

                            <!-- Faculty Count -->
                            <td style="padding: 0.85rem 1rem; text-align: center;">
                                <?php if ($fac_cnt > 0): ?>
                                    <a href="<?= BASE_URL ?>/public/index.php?route=admin/users&amp;dept_id=<?= (int)$d['dept_id'] ?>" 
                                       style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 4px; background: #f1f5f9; color: #334155; text-decoration: none; font-weight: 700; font-size: 0.84rem;">
                                        <i class="fas fa-users" style="color: #6366f1;"></i> <?= $fac_cnt ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 0.84rem;">0</span>
                                <?php endif; ?>
                            </td>

                            <!-- Documents Count -->
                            <td style="padding: 0.85rem 1rem; text-align: center;">
                                <?php if ($doc_cnt > 0): ?>
                                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&amp;dept_id=<?= (int)$d['dept_id'] ?>" 
                                       style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 4px; background: #e0f2fe; color: #0369a1; text-decoration: none; font-weight: 700; font-size: 0.84rem;">
                                        <i class="fas fa-file-alt"></i> <?= number_format($doc_cnt) ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 0.84rem;">0</span>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <td style="padding: 0.85rem 1rem; text-align: right;">
                                <div style="display: inline-flex; gap: 6px; align-items: center;">
                                    
                                    <?php if ($is_admin): ?>
                                        <!-- Edit Modal Trigger -->
                                        <button type="button" 
                                                onclick="openDeptModal(<?= (int)$d['dept_id'] ?>, '<?= htmlspecialchars(addslashes($d['dept_name']), ENT_QUOTES, 'UTF-8') ?>', <?= $is_acad ? 1 : 0 ?>)"
                                                style="background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; padding: 0.35rem 0.65rem; border-radius: 5px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
                                                title="Edit Department Name & Classification">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <!-- Toggle Academic Classification -->
                                        <form action="<?= BASE_URL ?>/public/index.php?route=admin/departments/toggle_academic" method="POST" style="margin: 0;">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="dept_id" value="<?= (int)$d['dept_id'] ?>">
                                            <button type="submit" 
                                                    style="background: <?= $is_acad ? '#fef3c7' : '#dcfce7' ?>; color: <?= $is_acad ? '#92400e' : '#166534' ?>; border: 1px solid <?= $is_acad ? '#fde68a' : '#bbf7d0' ?>; padding: 0.35rem 0.65rem; border-radius: 5px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
                                                    title="<?= $is_acad ? 'Switch to Administrative / Support Cell' : 'Switch to Academic Department' ?>">
                                                <i class="fas fa-exchange-alt"></i> <?= $is_acad ? 'Make Cell' : 'Make Academic' ?>
                                            </button>
                                        </form>

                                        <!-- Safe Delete Trigger -->
                                        <?php if ($fac_cnt > 0 || $doc_cnt > 0): ?>
                                            <button type="button" disabled 
                                                    style="background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; padding: 0.35rem 0.65rem; border-radius: 5px; font-size: 0.8rem; cursor: not-allowed;" 
                                                    title="Protected: Contains <?= $fac_cnt ?> faculty and <?= $doc_cnt ?> document(s)">
                                                <i class="fas fa-shield-alt"></i> Protected
                                            </button>
                                        <?php else: ?>
                                            <button type="button" 
                                                    onclick="openDeleteDeptModal(<?= (int)$d['dept_id'] ?>, '<?= htmlspecialchars(addslashes($d['dept_name']), ENT_QUOTES, 'UTF-8') ?>')"
                                                    style="background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; padding: 0.35rem 0.65rem; border-radius: 5px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
                                                    title="Delete unused department">
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
        <?php endif; ?>

    </div>

</div>

<!-- Modal 1: Add/Edit Department -->
<div id="deptModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1050; align-items: center; justify-content: center; backdrop-filter: blur(2px);">
    <div style="background: white; border-radius: 12px; width: 100%; max-width: 480px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; margin: 1.5rem;">
        
        <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="deptModalTitle" style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-sitemap" style="color: #0284c7;"></i>
                Add Department / Unit
            </h3>
            <button type="button" onclick="closeDeptModal()" style="background: none; border: none; color: #64748b; font-size: 1.25rem; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="<?= BASE_URL ?>/public/index.php?route=admin/departments/save" method="POST" style="padding: 1.5rem;">
            <?= csrfField() ?>
            <input type="hidden" name="dept_id" id="deptModalId" value="0">

            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Department / Unit Name <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" name="dept_name" id="deptModalName" required placeholder="e.g. Data Science or Examination Cell" maxlength="100"
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; font-weight: 600; color: #0f172a;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Classification
                </label>
                <select name="is_academic" id="deptModalAcademic" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: white;">
                    <option value="1">Academic Teaching Department (Included in Accreditation &amp; NAAC/NBA)</option>
                    <option value="0">Administrative / Support Unit (Central Cell, Committee, Club, or Dean Office)</option>
                </select>
                <div style="font-size: 0.78rem; color: #64748b; margin-top: 5px;">
                    Academic departments can have an appointed Head of Department (HOD) and appear in public department portals.
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeDeptModal()" 
                        style="padding: 0.6rem 1.1rem; border-radius: 6px; border: 1px solid #cbd5e1; background: white; color: #475569; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" 
                        style="padding: 0.6rem 1.3rem; border-radius: 6px; border: none; background: #003366; color: white; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                    <i class="fas fa-check me-1"></i> Save Unit
                </button>
            </div>
        </form>

    </div>
</div>

<!-- Modal 2: Delete Department Confirmation -->
<div id="deleteDeptModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1050; align-items: center; justify-content: center; backdrop-filter: blur(2px);">
    <div style="background: white; border-radius: 12px; width: 100%; max-width: 440px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; margin: 1.5rem;">
        
        <div style="padding: 1.25rem 1.5rem; background: #fef2f2; border-bottom: 1px solid #fee2e2; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #991b1b; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-trash-alt"></i> Delete Department
            </h3>
            <button type="button" onclick="closeDeleteDeptModal()" style="background: none; border: none; color: #991b1b; font-size: 1.25rem; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="<?= BASE_URL ?>/public/index.php?route=admin/departments/delete" method="POST" style="padding: 1.5rem;">
            <?= csrfField() ?>
            <input type="hidden" name="dept_id" id="deleteDeptId" value="">

            <p style="margin: 0 0 1rem; color: #334155; font-size: 0.95rem; line-height: 1.5;">
                Are you sure you want to delete department <strong id="deleteDeptNameText" style="color: #0f172a;"></strong>?
            </p>

            <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 6px; padding: 0.75rem; margin-bottom: 1.5rem; font-size: 0.8rem; color: #991b1b;">
                <i class="fas fa-exclamation-triangle me-1"></i>
                This action is permanent and will be logged in the college-wide audit trail. Only departments with 0 faculty and 0 documents can be deleted.
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeDeleteDeptModal()" 
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
function openDeptModal(deptId = 0, deptName = '', isAcademic = 1) {
    document.getElementById('deptModalId').value = deptId;
    document.getElementById('deptModalName').value = deptName;
    document.getElementById('deptModalAcademic').value = isAcademic;
    
    const title = document.getElementById('deptModalTitle');
    if (title) {
        title.innerHTML = deptId > 0 
            ? '<i class="fas fa-edit" style="color: #0284c7;"></i> Edit Department / Unit'
            : '<i class="fas fa-plus-circle" style="color: #0284c7;"></i> Add Department / Unit';
    }

    const modal = document.getElementById('deptModal');
    if (modal) modal.style.display = 'flex';
}
function closeDeptModal() {
    const modal = document.getElementById('deptModal');
    if (modal) modal.style.display = 'none';
}
function openDeleteDeptModal(deptId, deptName) {
    document.getElementById('deleteDeptId').value = deptId;
    document.getElementById('deleteDeptNameText').innerText = deptName;
    const modal = document.getElementById('deleteDeptModal');
    if (modal) modal.style.display = 'flex';
}
function closeDeleteDeptModal() {
    const modal = document.getElementById('deleteDeptModal');
    if (modal) modal.style.display = 'none';
}

window.addEventListener('click', function(e) {
    const m1 = document.getElementById('deptModal');
    const m2 = document.getElementById('deleteDeptModal');
    if (e.target === m1) closeDeptModal();
    if (e.target === m2) closeDeleteDeptModal();
});
</script>

</main>
</body>
</html>
