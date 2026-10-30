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

<div class="main-container" style="max-width: 1380px; margin: 30px auto; padding: 0 20px;">
    
    <!-- Top Header Banner -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px 28px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <span style="background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.8rem; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">Institutional Control</span>
            </div>
            <h1 style="margin: 0; font-size: 1.65rem; color: #0f172a; font-weight: 800; letter-spacing: -0.5px;">Faculty & Leadership Management</h1>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 0.92rem;">
                Manage departmental leadership, appoint HODs, assign coordinators, and manage faculty account lifecycles.
            </p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <button type="button" onclick="openAddUserModal()" style="background: #0284c7; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 0.92rem; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2); transition: background 0.2s;">
                <i class="fas fa-user-plus"></i> Add New Faculty
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($disp_success)): ?>
        <div style="background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 0.95rem; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle" style="color: #10b981; font-size: 1.1rem;"></i>
            <span><?= htmlspecialchars($disp_success) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($disp_error)): ?>
        <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 0.95rem; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-circle" style="color: #ef4444; font-size: 1.1rem;"></i>
            <span><?= htmlspecialchars($disp_error) ?></span>
        </div>
    <?php endif; ?>

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

                                    <!-- Leadership Appointment Dropdown Button -->
                                    <button type="button" onclick="openAssignModal(<?= (int)$u['user_id'] ?>, '<?= htmlspecialchars(addslashes($u['full_name'])) ?>')" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #1e293b; padding: 6px 10px; border-radius: 6px; font-size: 0.82rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;" title="Appoint as HOD or Coordinator">
                                        <i class="fas fa-crown" style="color: #d97706;"></i> Roles
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
                <label style="display: block; font-weight: 600; font-size: 0.88rem; color: #334155; margin-bottom: 6px;">Initial Leadership Designation</label>
                <select name="initial_role" style="width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; box-sizing: border-box; background: white;">
                    <option value="faculty">Regular Faculty Member</option>
                    <option value="hod">Head of Department (HOD)</option>
                    <option value="coordinator">Department Coordinator</option>
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
     MODAL 2: APPOINT LEADERSHIP (HOD / COORDINATOR)
============================================================== -->
<div id="assignModal" style="display: none; position: fixed; z-index: 9999; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 12px; max-width: 520px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; border: 1px solid #e2e8f0;">
        <div style="background: #1e293b; color: white; padding: 18px 22px; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 700; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-crown" style="color: #f59e0b;"></i> Manage Leadership Roles
            </div>
            <button type="button" onclick="closeAssignModal()" style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 22px;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 14px; margin-bottom: 20px;">
                <div style="font-size: 0.85rem; color: #64748b;">Selected Faculty Member:</div>
                <div id="modalUserName" style="font-size: 1.05rem; font-weight: 700; color: #0f172a;"></div>
            </div>

            <!-- Action Tab 1: Appoint HOD -->
            <div style="border: 1px solid #fde68a; background: #fffbeb; border-radius: 8px; padding: 16px; margin-bottom: 18px;">
                <h4 style="margin: 0 0 8px 0; color: #92400e; font-size: 0.96rem; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-crown"></i> Appoint as Head of Department (HOD)
                </h4>
                <p style="margin: 0 0 12px 0; color: #78350f; font-size: 0.83rem;">
                    Institutional Rule: Assigning this faculty member as HOD will automatically relieve the department's existing HOD back to regular faculty.
                </p>
                <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/users/set_hod" style="display: flex; gap: 8px; align-items: center;">
                    <?= csrfField() ?>
                    <input type="hidden" name="user_id" id="hodUserId" value="">
                    <select name="dept_id" required style="flex: 1; padding: 8px 10px; border: 1px solid #d97706; border-radius: 6px; font-size: 0.88rem; background: white;">
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

            <!-- Action Tab 2: Manage Coordinator -->
            <div style="border: 1px solid #bae6fd; background: #f0f9ff; border-radius: 8px; padding: 16px;">
                <h4 style="margin: 0 0 8px 0; color: #075985; font-size: 0.96rem; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-tasks"></i> Department Coordinator Role
                </h4>
                <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/users/toggle_coordinator" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <?= csrfField() ?>
                    <input type="hidden" name="user_id" id="coordUserId" value="">
                    <select name="dept_id" required style="flex: 1; min-width: 180px; padding: 8px 10px; border: 1px solid #0284c7; border-radius: 6px; font-size: 0.88rem; background: white;">
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
    </div>
</div>

<script>
function openAddUserModal() {
    const m = document.getElementById('addUserModal');
    if (m) m.style.display = 'flex';
}
function closeAddUserModal() {
    const m = document.getElementById('addUserModal');
    if (m) m.style.display = 'none';
}
function openAssignModal(userId, userName) {
    document.getElementById('hodUserId').value = userId;
    document.getElementById('coordUserId').value = userId;
    document.getElementById('modalUserName').textContent = userName;
    const m = document.getElementById('assignModal');
    if (m) m.style.display = 'flex';
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
