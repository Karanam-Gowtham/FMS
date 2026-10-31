<?php
/**
 * Admin User Management View
 * 
 * Clean, structured table with dynamic sequential numbering (S.No.),
 * role badges, leadership assignment tools, status toggles, and safe deletion.
 */
$page_title = "User Management | GMRIT FMS";
require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="fms-container">
    
    <!-- Top Header Banner -->
    <div class="fms-header-banner">
        <div>
            <div class="fms-tags-wrapper">
                <span class="fms-tag fms-tag-blue">Institutional Control</span>
            </div>
            <h1 class="fms-header-title">Faculty &amp; Leadership Management</h1>
            <p class="fms-header-subtitle">
                Manage departmental leadership, appoint HODs, assign coordinators, and manage faculty account lifecycles.
            </p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <button type="button" onclick="openAddUserModal()" class="fms-btn fms-btn-primary">
                <i class="fas fa-user-plus"></i> Add New Faculty
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php include __DIR__ . '/../partials/alerts.php'; ?>

    <!-- Search & Filter Bar -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.03);">
        <form method="GET" action="<?= BASE_URL ?>/public/index.php" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: center;">
            <input type="hidden" name="route" value="admin/users">

            <!-- Search input -->
            <div style="flex: 2; min-width: 220px; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name, email, or personnel ID..." style="width: 100%; padding: 9px 12px 9px 36px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; outline: none; transition: border 0.2s;" onfocus="this.style.borderColor='#0284c7';" onblur="this.style.borderColor='#cbd5e1';">
            </div>

            <!-- Department filter -->
            <div style="flex: 1; min-width: 160px;">
                <select name="dept_id" style="width: 100%; padding: 9px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; background: white;">
                    <option value="0">All Departments</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= (int)$d['dept_id'] ?>" <?= ($filter_dept_id === (int)$d['dept_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['dept_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Role filter -->
            <div style="flex: 1; min-width: 150px;">
                <select name="role_id" style="width: 100%; padding: 9px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; background: white;">
                    <option value="0">All Roles</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= (int)$r['role_id'] ?>" <?= ($filter_role_id === (int)$r['role_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['role_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status filter -->
            <div style="width: 140px;">
                <select name="status" style="width: 100%; padding: 9px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; background: white;">
                    <option value="">All Statuses</option>
                    <option value="active" <?= ($filter_status === 'active') ? 'selected' : '' ?>>Active Only</option>
                    <option value="inactive" <?= ($filter_status === 'inactive') ? 'selected' : '' ?>>Relieved Only</option>
                </select>
            </div>

            <button type="submit" style="background: #1e293b; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.92rem;">
                Filter
            </button>
            <?php if (!empty($search) || $filter_dept_id > 0 || $filter_role_id > 0 || !empty($filter_status)): ?>
                <a href="<?= BASE_URL ?>/public/index.php?route=admin/users" style="color: #64748b; font-size: 0.9rem; text-decoration: none; padding: 8px 12px;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Users Table Card -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 40px;">
        <div style="padding: 16px 22px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 700; color: #1e293b; font-size: 1.05rem;">
                Total Users: <span style="color: #0284c7;"><?= count($users) ?></span>
            </div>
            <div style="font-size: 0.85rem; color: #64748b;">
                <i class="fas fa-info-circle"></i> S.No. is dynamically sequenced with zero database gaps.
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.92rem; text-align: left;">
                <thead>
                    <tr style="background: #f1f5f9; color: #334155; border-bottom: 2px solid #cbd5e1; text-transform: uppercase; font-size: 0.78rem; letter-spacing: 0.5px;">
                        <th style="padding: 12px 14px; width: 55px; text-align: center;">S.No.</th>
                        <th style="padding: 12px 16px;">Faculty Member</th>
                        <th style="padding: 12px 16px;">Assigned Roles & Dept</th>
                        <th style="padding: 12px 14px; text-align: center; width: 95px;">Docs</th>
                        <th style="padding: 12px 14px; text-align: center; width: 105px;">Status</th>
                        <th style="padding: 12px 14px; width: 130px;">Last Login</th>
                        <th style="padding: 12px 18px; text-align: right; width: 220px;">Management Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" style="padding: 35px 20px; text-align: center; color: #64748b; font-size: 1rem;">
                                <i class="fas fa-user-slash" style="font-size: 2rem; color: #cbd5e1; display: block; margin-bottom: 10px;"></i>
                                No users matched your search criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $sno = 1; 
                        foreach ($users as $u): 
                            $is_active = ($u['status'] === 'active');
                            $doc_count = (int)$u['doc_count'];
                            $action_count = (int)$u['action_count'];
                            $can_delete = ($doc_count === 0 && $action_count === 0 && (int)$u['user_id'] !== (int)auth_user_id());
                        ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='white';">
                            <!-- Clean Sequential S.No. (Zero Gaps) -->
                            <td style="padding: 14px 14px; text-align: center; font-weight: 700; color: #64748b;">
                                <?= $sno++ ?>
                            </td>

                            <!-- User Info -->
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 700; color: #0f172a; font-size: 0.96rem;">
                                    <?= htmlspecialchars($u['full_name']) ?>
                                    <?php if (!empty($u['per_no'])): ?>
                                        <span style="font-size: 0.78rem; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">ID: <?= htmlspecialchars($u['per_no']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div style="color: #64748b; font-size: 0.85rem; margin-top: 2px;">
                                    <i class="fas fa-envelope" style="font-size: 0.8rem; margin-right: 4px; color: #94a3b8;"></i> <?= htmlspecialchars($u['email']) ?>
                                </div>
                                <?php if (!empty($u['designation_present'])): ?>
                                    <div style="color: #0369a1; font-size: 0.82rem; margin-top: 1px;">
                                        <?= htmlspecialchars($u['designation_present']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Assigned Roles & Department -->
                            <td style="padding: 14px 16px;">
                                <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                    <?php if (empty($u['roles'])): ?>
                                        <span style="color: #94a3b8; font-size: 0.85rem; font-style: italic;">No roles assigned</span>
                                    <?php else: ?>
                                        <?php foreach ($u['roles'] as $r): 
                                            $rid = (int)$r['role_id'];
                                            $badge_style = "background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;";
                                            if ($rid === ROLE_ADMIN) {
                                                $badge_style = "background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; font-weight: 700;";
                                            } elseif ($rid === ROLE_HOD) {
                                                $badge_style = "background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-weight: 700;";
                                            } elseif ($rid === ROLE_DEPT_COORDINATOR) {
                                                $badge_style = "background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-weight: 600;";
                                            } elseif ($rid === ROLE_IQAC || $rid === ROLE_CENTRAL_COORDINATOR) {
                                                $badge_style = "background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe;";
                                            }
                                        ?>
                                            <span style="<?= $badge_style ?> padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                                                <?php if ($rid === ROLE_HOD): ?>
                                                    <i class="fas fa-crown" style="font-size: 0.75rem; color: #d97706;"></i>
                                                <?php elseif ($rid === ROLE_DEPT_COORDINATOR): ?>
                                                    <i class="fas fa-tasks" style="font-size: 0.75rem;"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($r['role_name']) ?>
                                                <?php if (!empty($r['dept_name'])): ?>
                                                    <strong style="color: #0f172a; margin-left: 2px;">(<?= htmlspecialchars($r['dept_name']) ?>)</strong>
                                                <?php endif; ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Document Count -->
                            <td style="padding: 14px 14px; text-align: center;">
                                <span style="font-weight: 700; color: <?= $doc_count > 0 ? '#0284c7' : '#94a3b8' ?>; font-size: 0.95rem;">
                                    <?= $doc_count ?>
                                </span>
                            </td>

                            <!-- Account Status -->
                            <td style="padding: 14px 14px; text-align: center;">
                                <?php if ($is_active): ?>
                                    <span style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-weight: 700; font-size: 0.78rem; padding: 3px 10px; border-radius: 12px; display: inline-block;">
                                        Active
                                    </span>
                                <?php else: ?>
                                    <span style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; font-weight: 700; font-size: 0.78rem; padding: 3px 10px; border-radius: 12px; display: inline-block;" title="Login disabled. Historical documents preserved.">
                                        Relieved
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Last Login -->
                            <td style="padding: 14px 14px; color: #64748b; font-size: 0.85rem;">
                                <?= !empty($u['last_login']) ? date('d M Y, h:i A', strtotime($u['last_login'])) : '—' ?>
                            </td>

                            <!-- Actions -->
                            <td style="padding: 14px 18px; text-align: right;">
                                <div style="display: inline-flex; align-items: center; gap: 6px; justify-content: flex-end;">

                                    <!-- Comprehensive Role & Department Management Button -->
                                    <button type="button" 
                                            onclick="openAssignModal(<?= (int)$u['user_id'] ?>, <?= htmlspecialchars(json_encode($u['full_name'])) ?>, <?= htmlspecialchars(json_encode($u['email'])) ?>, <?= (int)($u['primary_dept_id'] ?? 0) ?>, <?= htmlspecialchars(json_encode($u['primary_dept_name'] ?? '')) ?>, <?= htmlspecialchars(json_encode($u['roles'] ?? [])) ?>)" 
                                            style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #1e293b; padding: 6px 10px; border-radius: 6px; font-size: 0.82rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;" 
                                            title="Manage Faculty Roles & Department">
                                        <i class="fas fa-user-shield" style="color: #0284c7;"></i> Roles
                                    </button>

                                    <!-- Reset Password -->
                                    <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/users/reset_password" style="display: inline; margin: 0;" onsubmit="return confirm('Reset password for <?= htmlspecialchars(addslashes($u['full_name'])) ?> to Admin@123?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                                        <button type="submit" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; padding: 6px 9px; border-radius: 6px; font-size: 0.82rem; cursor: pointer;" title="Reset password to Admin@123">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    </form>

                                    <!-- Status Toggle (Active vs Relieved) -->
                                    <?php if ((int)$u['user_id'] !== (int)auth_user_id()): ?>
                                        <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/users/toggle_status" style="display: inline; margin: 0;" onsubmit="return confirm('<?= $is_active ? 'Mark ' . htmlspecialchars(addslashes($u['full_name'])) . ' as Relieved/Inactive? (Login will be disabled, but all their uploaded documents will remain intact).' : 'Re-activate account for ' . htmlspecialchars(addslashes($u['full_name'])) . '?' ?>');">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                                            <button type="submit" style="background: <?= $is_active ? '#fff7ed' : '#f0fdf4' ?>; border: 1px solid <?= $is_active ? '#fdba74' : '#86efac' ?>; color: <?= $is_active ? '#c2410c' : '#15803d' ?>; padding: 6px 9px; border-radius: 6px; font-size: 0.82rem; cursor: pointer;" title="<?= $is_active ? 'Mark as Relieved (Disable Login)' : 'Re-activate Account' ?>">
                                                <i class="fas <?= $is_active ? 'fa-user-lock' : 'fa-user-check' ?>"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Safe Delete (Enabled only if 0 documents) -->
                                    <?php if ((int)$u['user_id'] !== (int)auth_user_id()): ?>
                                        <?php if ($can_delete): ?>
                                            <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/users/delete" style="display: inline; margin: 0;" onsubmit="return confirm('Permanently delete test user <?= htmlspecialchars(addslashes($u['full_name'])) ?>? This cannot be undone.');">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                                                <button type="submit" style="background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; padding: 6px 9px; border-radius: 6px; font-size: 0.82rem; cursor: pointer;" title="Delete Test User (0 documents)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="opacity: 0.45; cursor: not-allowed; padding: 6px 9px; display: inline-block; color: #94a3b8;" title="Cannot delete: User has <?= $doc_count ?> documents on record. Use Relieved/Inactive status instead.">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL 1: ADD NEW FACULTY
============================================================== -->
<div id="addUserModal" style="display: none; position: fixed; z-index: 9999; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 12px; max-width: 500px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; border: 1px solid #e2e8f0; animation: modalPop 0.2s ease-out;">
        <div style="background: #003366; color: white; padding: 18px 22px; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 700; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-user-plus" style="color: #f6ad55;"></i> Register New Faculty
            </div>
            <button type="button" onclick="closeAddUserModal()" style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/users/create" style="padding: 22px;">
            <?= csrfField() ?>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; font-size: 0.88rem; color: #334155; margin-bottom: 6px;">Full Name *</label>
                <input type="text" name="full_name" required placeholder="e.g. Dr. K. Satyanarayana" style="width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; font-size: 0.88rem; color: #334155; margin-bottom: 6px;">Institutional Email *</label>
                <input type="email" name="email" required placeholder="e.g. satyanarayana.k@gmrit.edu.in" style="width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; font-size: 0.88rem; color: #334155; margin-bottom: 6px;">Department *</label>
                <select name="dept_id" required style="width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; box-sizing: border-box; background: white;">
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= (int)$d['dept_id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 22px;">
                <label style="display: block; font-weight: 600; font-size: 0.88rem; color: #334155; margin-bottom: 6px;">Initial Designation</label>
                <select name="initial_role" style="width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; box-sizing: border-box; background: white;">
                    <option value="faculty">Regular Faculty Member</option>
                    <option value="hod">Head of Department (HOD)</option>
                    <option value="coordinator">Department Coordinator</option>
                    <option value="iqac">IQAC Coordinator (College-Wide)</option>
                    <option value="central_coordinator">Central Coordinator (College-Wide)</option>
                    <option value="rnd_dean">Dean of R&D (College-Wide)</option>
                </select>
                <small style="color: #64748b; font-size: 0.8rem; display: block; margin-top: 4px;">
                    Note: Default login password will be set to <strong>Admin@123</strong>.
                </small>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeAddUserModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 10px 18px; border-radius: 6px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" style="background: #0284c7; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer;">Create Account</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================================
     MODAL 2: COMPREHENSIVE ROLE & DEPARTMENT MANAGER
============================================================== -->
<div id="assignModal" style="display: none; position: fixed; z-index: 9999; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(5px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 14px; max-width: 680px; width: 100%; box-shadow: 0 25px 30px -5px rgba(0,0,0,0.3); overflow: hidden; border: 1px solid #e2e8f0; animation: modalPop 0.2s ease-out;">
        <div style="background: #0f172a; color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 700; font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-user-shield" style="color: #38bdf8;"></i> Manage Faculty Roles & Department
            </div>
            <button type="button" onclick="closeAssignModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.4rem; cursor: pointer;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#94a3b8'">&times;</button>
        </div>

        <div style="padding: 24px; max-height: 78vh; overflow-y: auto;">
            
            <!-- User Summary Banner -->
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 8px;">
                    <div>
                        <div id="modalUserName" style="font-size: 1.15rem; font-weight: 700; color: #0f172a;"></div>
                        <div id="modalUserEmail" style="font-size: 0.85rem; color: #64748b; margin-top: 2px;"></div>
                    </div>
                    <div id="modalUserDeptBadge" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-weight: 700; font-size: 0.82rem; padding: 4px 10px; border-radius: 6px;"></div>
                </div>

                <div style="margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 10px;">
                    <div style="font-size: 0.8rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                        Current Active Roles:
                    </div>
                    <div id="modalActiveRolesBadges" style="display: flex; flex-wrap: wrap; gap: 6px;"></div>
                </div>
            </div>

            <!-- SECTION 1: INSTITUTIONAL LEADERSHIP (SINGLE-HOLDER, COLLEGE-WIDE) -->
            <div style="border: 1px solid #e9d5ff; background: #faf5ff; border-radius: 10px; padding: 16px 18px; margin-bottom: 18px;">
                <h4 style="margin: 0 0 6px 0; color: #6b21a8; font-size: 0.98rem; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-university"></i> Institutional Leadership (College-Wide)
                </h4>
                <p style="margin: 0 0 14px 0; color: #581c87; font-size: 0.82rem; line-height: 1.4;">
                    Strict Institutional Rules: <strong>Strictly 1 holder college-wide</strong>. Appointing this member transfers leadership from the previous holder. Institutional leaders <strong>cannot simultaneously hold HOD</strong> (any active HOD post will be automatically stepped down).
                </p>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    
                    <!-- IQAC Coordinator -->
                    <div style="background: white; border: 1px solid #d8b4fe; border-radius: 8px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <div style="font-weight: 700; color: #6b21a8; font-size: 0.92rem;">
                                <i class="fas fa-award"></i> IQAC Coordinator
                            </div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                                Current Holder: <strong style="color: #1e293b;"><?= !empty($institutional_leaders[ROLE_IQAC]) ? htmlspecialchars($institutional_leaders[ROLE_IQAC]['full_name'] . ' (' . $institutional_leaders[ROLE_IQAC]['email'] . ')') : 'None assigned' ?></strong>
                            </div>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/users/set_institutional_role" style="margin: 0;">
                            <?= csrfField() ?>
                            <input type="hidden" name="user_id" class="modalFormUserId" value="">
                            <input type="hidden" name="role_id" value="<?= ROLE_IQAC ?>">
                            <div id="iqacActionBtnContainer"></div>
                        </form>
                    </div>

                    <!-- Central Coordinator -->
                    <div style="background: white; border: 1px solid #c7d2fe; border-radius: 8px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <div style="font-weight: 700; color: #3730a3; font-size: 0.92rem;">
                                <i class="fas fa-sitemap"></i> Central Coordinator
                            </div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                                Current Holder: <strong style="color: #1e293b;"><?= !empty($institutional_leaders[ROLE_CENTRAL_COORDINATOR]) ? htmlspecialchars($institutional_leaders[ROLE_CENTRAL_COORDINATOR]['full_name'] . ' (' . $institutional_leaders[ROLE_CENTRAL_COORDINATOR]['email'] . ')') : 'None assigned' ?></strong>
                            </div>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/users/set_institutional_role" style="margin: 0;">
                            <?= csrfField() ?>
                            <input type="hidden" name="user_id" class="modalFormUserId" value="">
                            <input type="hidden" name="role_id" value="<?= ROLE_CENTRAL_COORDINATOR ?>">
                            <div id="centralActionBtnContainer"></div>
                        </form>
                    </div>

                    <!-- Dean R&D -->
                    <div style="background: white; border: 1px solid #99f6e4; border-radius: 8px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <div style="font-weight: 700; color: #0f766e; font-size: 0.92rem;">
                                <i class="fas fa-flask"></i> Dean of R&D
                            </div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                                Current Holder: <strong style="color: #1e293b;"><?= !empty($institutional_leaders[ROLE_RND_DEAN]) ? htmlspecialchars($institutional_leaders[ROLE_RND_DEAN]['full_name'] . ' (' . $institutional_leaders[ROLE_RND_DEAN]['email'] . ')') : 'None assigned' ?></strong>
                            </div>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/users/set_institutional_role" style="margin: 0;">
                            <?= csrfField() ?>
                            <input type="hidden" name="user_id" class="modalFormUserId" value="">
                            <input type="hidden" name="role_id" value="<?= ROLE_RND_DEAN ?>">
                            <div id="rndActionBtnContainer"></div>
                        </form>
                    </div>

                </div>
            </div>

            <!-- SECTION 2: DEPARTMENT LEADERSHIP (HOD / COORDINATOR) -->
            <div style="border: 1px solid #fde68a; background: #fffbeb; border-radius: 10px; padding: 16px 18px; margin-bottom: 18px;">
                <h4 style="margin: 0 0 6px 0; color: #92400e; font-size: 0.98rem; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-crown"></i> Department Leadership
                </h4>
                <p style="margin: 0 0 14px 0; color: #78350f; font-size: 0.82rem; line-height: 1.4;">
                    Strict Institutional Rule: <strong>Strictly 1 HOD active per department</strong> (auto-transfers previous HOD). Appointing as HOD will automatically step down any active Institutional Leadership post.
                </p>

                <!-- Sub-Item A: Head of Department -->
                <div style="background: white; border: 1px solid #fcd34d; border-radius: 8px; padding: 12px 14px; margin-bottom: 12px;">
                    <div style="font-weight: 700; color: #b45309; font-size: 0.9rem; margin-bottom: 8px;">
                        Appoint as Head of Department (HOD)
                    </div>
                    <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/users/set_hod" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                        <?= csrfField() ?>
                        <input type="hidden" name="user_id" class="modalFormUserId" value="">
                        <select name="dept_id" id="hodDeptSelect" required style="flex: 1; min-width: 200px; padding: 8px 10px; border: 1.5px solid #d97706; border-radius: 6px; font-size: 0.88rem; background: white;">
                            <option value="">-- Select Department --</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= (int)$d['dept_id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" style="background: #d97706; color: white; border: none; padding: 8px 14px; border-radius: 6px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                            Set as HOD
                        </button>
                    </form>
                </div>

                <!-- Sub-Item B: Department Coordinator -->
                <div style="background: white; border: 1px solid #bae6fd; border-radius: 8px; padding: 12px 14px;">
                    <div style="font-weight: 700; color: #0369a1; font-size: 0.9rem; margin-bottom: 8px;">
                        Department Coordinator Role
                    </div>
                    <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/users/toggle_coordinator" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                        <?= csrfField() ?>
                        <input type="hidden" name="user_id" class="modalFormUserId" value="">
                        <select name="dept_id" id="coordDeptSelect" required style="flex: 1; min-width: 200px; padding: 8px 10px; border: 1.5px solid #0284c7; border-radius: 6px; font-size: 0.88rem; background: white;">
                            <option value="">-- Select Department --</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= (int)$d['dept_id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="action" value="appoint" style="background: #0284c7; color: white; border: none; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                            Appoint
                        </button>
                        <button type="submit" name="action" value="revoke" style="background: #ef4444; color: white; border: none; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 0.88rem; cursor: pointer;" onclick="return confirm('Revoke coordinator role from this user for selected department?');">
                            Revoke
                        </button>
                    </form>
                </div>
            </div>

            <!-- SECTION 3: DEPARTMENT TRANSFER (SINGLE-DEPARTMENT ENFORCEMENT) -->
            <div style="border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 10px; padding: 16px 18px;">
                <h4 style="margin: 0 0 6px 0; color: #334155; font-size: 0.98rem; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-exchange-alt"></i> Transfer Faculty Member Department
                </h4>
                <p style="margin: 0 0 12px 0; color: #64748b; font-size: 0.82rem; line-height: 1.4;">
                    Strict Institutional Rule: Every faculty member belongs to <strong>strictly one department</strong>. Transferring reassigns their base Faculty role and relieves any department-specific leadership (HOD or Coordinator) from their previous department.
                </p>
                <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/users/transfer_department" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;" onsubmit="return confirm('Transfer this faculty member to the selected department? Any departmental leadership in their old department will be relieved.');">
                    <?= csrfField() ?>
                    <input type="hidden" name="user_id" class="modalFormUserId" value="">
                    <select name="dept_id" id="transferDeptSelect" required style="flex: 1; min-width: 220px; padding: 8px 10px; border: 1.5px solid #64748b; border-radius: 6px; font-size: 0.88rem; background: white;">
                        <option value="">-- Select Target Department --</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= (int)$d['dept_id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" style="background: #334155; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                        Transfer Department
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Hidden Quick Role Revoke Form -->
<form id="hiddenRevokeForm" method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/users/revoke_role" style="display: none;">
    <?= csrfField() ?>
    <input type="hidden" name="user_id" id="revokeUserId" value="">
    <input type="hidden" name="role_id" id="revokeRoleId" value="">
    <input type="hidden" name="dept_id" id="revokeDeptId" value="">
</form>

<script>
function openAddUserModal() {
    const m = document.getElementById('addUserModal');
    if (m) m.style.display = 'flex';
}
function closeAddUserModal() {
    const m = document.getElementById('addUserModal');
    if (m) m.style.display = 'none';
}

function openAssignModal(userId, userName, userEmail, primaryDeptId, primaryDeptName, roles) {
    document.getElementById('modalUserName').textContent = userName;
    document.getElementById('modalUserEmail').textContent = userEmail;
    document.getElementById('modalUserDeptBadge').textContent = primaryDeptName ? ('Department: ' + primaryDeptName) : 'No Department Assigned';

    // Populate all user ID hidden inputs in the modal
    const userInputs = document.querySelectorAll('.modalFormUserId');
    userInputs.forEach(input => input.value = userId);

    // Pre-select primary department in dropdowns
    if (primaryDeptId > 0) {
        const hodSelect = document.getElementById('hodDeptSelect');
        const coordSelect = document.getElementById('coordDeptSelect');
        const transferSelect = document.getElementById('transferDeptSelect');
        if (hodSelect) hodSelect.value = primaryDeptId;
        if (coordSelect) coordSelect.value = primaryDeptId;
        if (transferSelect) transferSelect.value = primaryDeptId;
    }

    // Role ID map
    const ROLE_IQAC = <?= ROLE_IQAC ?>;
    const ROLE_HOD = <?= ROLE_HOD ?>;
    const ROLE_FACULTY = <?= ROLE_FACULTY ?>;
    const ROLE_DEPT_COORDINATOR = <?= ROLE_DEPT_COORDINATOR ?>;
    const ROLE_CENTRAL_COORDINATOR = <?= ROLE_CENTRAL_COORDINATOR ?>;
    const ROLE_RND_DEAN = <?= ROLE_RND_DEAN ?>;

    let hasIqac = false;
    let hasCentral = false;
    let hasRnd = false;

    // Render active roles badges
    const badgesContainer = document.getElementById('modalActiveRolesBadges');
    badgesContainer.innerHTML = '';

    if (!roles || roles.length === 0) {
        badgesContainer.innerHTML = '<span style="color: #94a3b8; font-size: 0.85rem; font-style: italic;">No active roles assigned.</span>';
    } else {
        roles.forEach(r => {
            const roleId = parseInt(r.role_id, 10);
            const deptId = r.dept_id ? parseInt(r.dept_id, 10) : 0;
            const deptName = r.dept_name || '';

            if (roleId === ROLE_IQAC) hasIqac = true;
            if (roleId === ROLE_CENTRAL_COORDINATOR) hasCentral = true;
            if (roleId === ROLE_RND_DEAN) hasRnd = true;

            const badge = document.createElement('div');
            badge.style.display = 'inline-flex';
            badge.style.alignItems = 'center';
            badge.style.gap = '6px';
            badge.style.padding = '4px 10px';
            badge.style.borderRadius = '6px';
            badge.style.fontSize = '0.82rem';
            badge.style.fontWeight = '600';

            let label = r.role_name + (deptName ? (' (' + deptName + ')') : '');

            if (roleId === ROLE_HOD) {
                badge.style.background = '#fef3c7';
                badge.style.color = '#92400e';
                badge.style.border = '1px solid #fde68a';
                badge.innerHTML = '<span><i class="fas fa-crown"></i> ' + escapeHtml(label) + '</span>' +
                    '<button type="button" onclick="triggerRevoke(' + userId + ', ' + roleId + ', ' + deptId + ', \'HOD\')" style="background: none; border: none; color: #dc2626; cursor: pointer; font-weight: 700; font-size: 0.95rem; margin-left: 4px;" title="Step down from HOD">&times;</button>';
            } else if (roleId === ROLE_DEPT_COORDINATOR) {
                badge.style.background = '#e0f2fe';
                badge.style.color = '#0369a1';
                badge.style.border = '1px solid #bae6fd';
                badge.innerHTML = '<span><i class="fas fa-tasks"></i> ' + escapeHtml(label) + '</span>' +
                    '<button type="button" onclick="triggerRevoke(' + userId + ', ' + roleId + ', ' + deptId + ', \'Coordinator\')" style="background: none; border: none; color: #dc2626; cursor: pointer; font-weight: 700; font-size: 0.95rem; margin-left: 4px;" title="Revoke Coordinator role">&times;</button>';
            } else if (roleId === ROLE_IQAC) {
                badge.style.background = '#f3e8ff';
                badge.style.color = '#6b21a8';
                badge.style.border = '1px solid #d8b4fe';
                badge.innerHTML = '<span><i class="fas fa-award"></i> ' + escapeHtml(label) + '</span>' +
                    '<button type="button" onclick="triggerRevoke(' + userId + ', ' + roleId + ', null, \'IQAC Coordinator\')" style="background: none; border: none; color: #dc2626; cursor: pointer; font-weight: 700; font-size: 0.95rem; margin-left: 4px;" title="Step down as IQAC Coordinator">&times;</button>';
            } else if (roleId === ROLE_CENTRAL_COORDINATOR) {
                badge.style.background = '#e0e7ff';
                badge.style.color = '#3730a3';
                badge.style.border = '1px solid #c7d2fe';
                badge.innerHTML = '<span><i class="fas fa-sitemap"></i> ' + escapeHtml(label) + '</span>' +
                    '<button type="button" onclick="triggerRevoke(' + userId + ', ' + roleId + ', null, \'Central Coordinator\')" style="background: none; border: none; color: #dc2626; cursor: pointer; font-weight: 700; font-size: 0.95rem; margin-left: 4px;" title="Step down as Central Coordinator">&times;</button>';
            } else if (roleId === ROLE_RND_DEAN) {
                badge.style.background = '#ccfbf1';
                badge.style.color = '#0f766e';
                badge.style.border = '1px solid #99f6e4';
                badge.innerHTML = '<span><i class="fas fa-flask"></i> ' + escapeHtml(label) + '</span>' +
                    '<button type="button" onclick="triggerRevoke(' + userId + ', ' + roleId + ', null, \'Dean of R&D\')" style="background: none; border: none; color: #dc2626; cursor: pointer; font-weight: 700; font-size: 0.95rem; margin-left: 4px;" title="Step down as Dean of R&D">&times;</button>';
            } else {
                badge.style.background = '#dcfce7';
                badge.style.color = '#15803d';
                badge.style.border = '1px solid #bbf7d0';
                badge.innerHTML = '<span><i class="fas fa-chalkboard-teacher"></i> ' + escapeHtml(label) + ' <small style="opacity: 0.8; font-weight: 500;">(Base Role)</small></span>';
            }

            badgesContainer.appendChild(badge);
        });
    }

    // Update Action Buttons for Institutional Leadership
    updateInstButton('iqacActionBtnContainer', hasIqac, 'IQAC Coordinator', '#6b21a8', '#f3e8ff');
    updateInstButton('centralActionBtnContainer', hasCentral, 'Central Coordinator', '#3730a3', '#e0e7ff');
    updateInstButton('rndActionBtnContainer', hasRnd, 'Dean of R&D', '#0f766e', '#ccfbf1');

    const m = document.getElementById('assignModal');
    if (m) m.style.display = 'flex';
}

function updateInstButton(containerId, isHolder, roleName, mainColor, lightBg) {
    const c = document.getElementById(containerId);
    if (!c) return;

    if (isHolder) {
        c.innerHTML = '<span style="display: inline-flex; align-items: center; gap: 8px;">' +
            '<span style="background: ' + lightBg + '; color: ' + mainColor + '; font-weight: 700; font-size: 0.78rem; padding: 4px 8px; border-radius: 4px;">Active Holder</span>' +
            '<button type="submit" name="action" value="revoke" style="background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.82rem; cursor: pointer;" onclick="return confirm(\'Relieve this member from ' + roleName + '?\');">' +
                '<i class="fas fa-user-minus"></i> Step Down' +
            '</button>' +
        '</span>';
    } else {
        c.innerHTML = '<button type="submit" name="action" value="appoint" style="background: ' + mainColor + '; color: white; border: none; padding: 7px 14px; border-radius: 6px; font-weight: 600; font-size: 0.82rem; cursor: pointer;" onclick="return confirm(\'Appoint as ' + roleName + '? This will transfer the position from the current holder and step down any active HOD role.\');">' +
            '<i class="fas fa-crown"></i> Appoint as ' + roleName +
        '</button>';
    }
}

function triggerRevoke(userId, roleId, deptId, roleName) {
    if (!confirm('Step down / revoke ' + roleName + ' from this faculty member?')) {
        return;
    }
    document.getElementById('revokeUserId').value = userId;
    document.getElementById('revokeRoleId').value = roleId;
    document.getElementById('revokeDeptId').value = deptId || '';
    document.getElementById('hiddenRevokeForm').submit();
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function closeAssignModal() {
    const m = document.getElementById('assignModal');
    if (m) m.style.display = 'none';
}

window.addEventListener('click', function(e) {
    const m1 = document.getElementById('addUserModal');
    const m2 = document.getElementById('assignModal');
    if (e.target === m1) closeAddUserModal();
    if (e.target === m2) closeAssignModal();
});
</script>

</main>
</body>
</html>
