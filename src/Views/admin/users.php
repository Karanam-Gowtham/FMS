<?php
/**
 * Admin User Management View
 * 
 * Clean, structured table with dynamic sequential numbering (S.No.),
 * role badges, leadership assignment tools, status toggles, and safe deletion.
 */
$page_title = "User Management | GMRIT FMS";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" href="data:,">
</head>
<body>
<?php require_once __DIR__ . '/../../../includes/header.php'; ?>

<main class="fms-container">
    
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
                <label for="adminUserSearch" class="visually-hidden">Search users</label>
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></i>
                <input type="text" id="adminUserSearch" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name, email, or personnel ID..." aria-label="Search by name, email, or personnel ID" autocomplete="off" style="width: 100%; padding: 9px 12px 9px 36px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; outline: none; transition: border 0.2s;">
            </div>

            <!-- Department filter -->
            <div style="flex: 1; min-width: 160px;">
                <label for="adminUserDeptFilter" class="visually-hidden">Filter by department</label>
                <select id="adminUserDeptFilter" name="dept_id" aria-label="Filter by department" style="width: 100%; padding: 9px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; background: white;">
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
                <label for="adminUserRoleFilter" class="visually-hidden">Filter by role</label>
                <select id="adminUserRoleFilter" name="role_id" aria-label="Filter by role" style="width: 100%; padding: 9px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; background: white;">
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
                <label for="adminUserStatusFilter" class="visually-hidden">Filter by account status</label>
                <select id="adminUserStatusFilter" name="status" aria-label="Filter by account status" style="width: 100%; padding: 9px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; background: white;">
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
</main>

<!-- ==============================================================
     MODAL 1: ADD NEW FACULTY
============================================================== -->
<?php include __DIR__ . '/users/add_user_modal.php'; ?>

<!-- ==============================================================
     MODAL 2: COMPREHENSIVE ROLE & DEPARTMENT MANAGER
============================================================== -->
<?php include __DIR__ . '/users/assign_roles_modal.php'; ?>

<script>
window.USER_ROLES_CONFIG = {
    ROLE_IQAC: <?= (int)ROLE_IQAC ?>,
    ROLE_HOD: <?= (int)ROLE_HOD ?>,
    ROLE_FACULTY: <?= (int)ROLE_FACULTY ?>,
    ROLE_DEPT_COORDINATOR: <?= (int)ROLE_DEPT_COORDINATOR ?>,
    ROLE_CENTRAL_COORDINATOR: <?= (int)ROLE_CENTRAL_COORDINATOR ?>,
    ROLE_RND_DEAN: <?= (int)ROLE_RND_DEAN ?>
};
</script>
<script src="<?= BASE_URL ?>/assets/js/admin_users.js"></script>
</body>
</html>
