<?php
$page_title = "User Management — Institutional Administration";
require __DIR__ . '/../../../includes/header.php';

$app_url = BASE_URL;
?>

<style>
/* Modern User Management Styles */
:root {
    --brand-navy: #003366;
    --brand-navy-dark: #002244;
    --brand-gold: #d97706;
    --bg-light: #f8fafc;
    --border-color: #e2e8f0;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
}

body {
    background-color: var(--bg-light);
    color: var(--text-primary);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

.um-container {
    max-width: 1400px;
    margin: 30px auto;
    padding: 0 20px;
}

.um-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
}

.um-title-box h1 {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--brand-navy);
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.um-title-box p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.95rem;
}

.btn-add-user {
    background: linear-gradient(135deg, #0284c7, #0369a1);
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 4px 6px -1px rgba(2, 132, 199, 0.25);
    transition: all 0.2s ease;
}

.btn-add-user:hover {
    background: linear-gradient(135deg, #0369a1, #075985);
    transform: translateY(-1px);
    box-shadow: 0 6px 8px -1px rgba(2, 132, 199, 0.35);
}

/* Filter Card */
.um-filter-card {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.um-filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: flex-end;
}

.um-form-group {
    flex: 1 1 200px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.um-form-group label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-primary);
}

.um-input, .um-select {
    padding: 9px 13px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.92rem;
    color: var(--text-primary);
    background: #ffffff;
    transition: border-color 0.2s;
}

.um-input:focus, .um-select:focus {
    outline: none;
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

.um-filter-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.btn-filter-apply {
    background: var(--brand-navy);
    color: #ffffff;
    border: none;
    padding: 9px 18px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.92rem;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-filter-apply:hover {
    background: var(--brand-navy-dark);
}

.btn-filter-reset {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    padding: 9px 14px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.92rem;
    text-decoration: none;
    transition: background 0.2s;
}

.btn-filter-reset:hover {
    background: #e2e8f0;
}

/* User Table */
.um-table-card {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.um-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.92rem;
}

.um-table thead th {
    background: #f8fafc;
    color: #475569;
    font-weight: 700;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 13px 16px;
    border-bottom: 1px solid var(--border-color);
    text-align: left;
}

.um-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}

.um-table tbody tr:hover {
    background: #f8fafc;
}

.um-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
}

/* Role Badges */
.badge-role {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 5px;
    font-size: 0.75rem;
    font-weight: 700;
    margin-right: 4px;
    margin-bottom: 4px;
    white-space: nowrap;
}

.badge-role-admin { background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe; }
.badge-role-hod { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-weight: 800; }
.badge-role-coord { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
.badge-role-faculty { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
.badge-role-iqac { background: #fae8ff; color: #86198f; border: 1px solid #f5d0fe; }
.badge-role-student { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
.badge-role-other { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }

/* Status Badges */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 700;
}

.status-pill-active {
    background: #dcfce7;
    color: #166534;
}

.status-pill-active::before {
    content: '';
    display: inline-block;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #22c55e;
}

.status-pill-inactive {
    background: #f1f5f9;
    color: #64748b;
}

.status-pill-inactive::before {
    content: '';
    display: inline-block;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #94a3b8;
}

/* Action Dropdown / Button Group */
.um-actions-group {
    display: flex;
    gap: 6px;
    align-items: center;
}

.btn-icon-action {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 10px;
    color: #475569;
    cursor: pointer;
    font-size: 0.82rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s ease;
}

.btn-icon-action:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: var(--brand-navy);
}

.btn-icon-danger {
    color: #dc2626;
    border-color: #fecaca;
}

.btn-icon-danger:hover {
    background: #fef2f2;
    border-color: #f87171;
    color: #b91c1c;
}

.btn-icon-disabled {
    opacity: 0.4;
    cursor: not-allowed !important;
    background: #f8fafc !important;
}

/* Modals */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(4px);
    z-index: 99999;
    align-items: center;
    justify-content: center;
    padding: 20px;
    box-sizing: border-box;
}

.modal-card {
    background: #ffffff;
    width: 100%;
    max-width: 580px;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);
    overflow: hidden;
    animation: modalSlide 0.2s ease-out;
}

@keyframes modalSlide {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

.modal-header {
    background: var(--brand-navy);
    color: #ffffff;
    padding: 16px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 700;
}

.modal-close {
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 1.4rem;
    cursor: pointer;
    line-height: 1;
}

.modal-body {
    padding: 22px;
}

.modal-footer {
    background: #f8fafc;
    border-top: 1px solid var(--border-color);
    padding: 14px 22px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}
</style>

<div class="um-container">

    <!-- Flash Messages -->
    <?php if (!empty($success_msg)): ?>
        <div style="background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle" style="font-size: 1.2rem; color: #10b981;"></i>
            <span><?= htmlspecialchars($success_msg) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-triangle" style="font-size: 1.2rem; color: #ef4444;"></i>
            <span><?= htmlspecialchars($error_msg) ?></span>
        </div>
    <?php endif; ?>

    <!-- Header Box -->
    <div class="um-header">
        <div class="um-title-box">
            <h1><i class="fas fa-users-cog"></i> User Management</h1>
            <p>Administer institutional accounts, assign department HODs, delegate coordinators, and manage credentials.</p>
        </div>
        <button type="button" class="btn-add-user" onclick="openAddUserModal()">
            <i class="fas fa-user-plus"></i> Add New User
        </button>
    </div>

    <!-- Filters Bar -->
    <div class="um-filter-card">
        <form method="GET" action="<?= $app_url ?>/public/index.php" class="um-filter-form">
            <input type="hidden" name="route" value="users/list">

            <div class="um-form-group" style="flex: 2 1 240px;">
                <label for="um_search"><i class="fas fa-search"></i> Search User</label>
                <input type="text" id="um_search" name="search" class="um-input" placeholder="Search by full name or email..." value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="um-form-group">
                <label for="um_dept"><i class="fas fa-building"></i> Department</label>
                <select id="um_dept" name="dept_id" class="um-select">
                    <option value="0">All Departments</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= (int)$d['dept_id'] ?>" <?= $filter_dept === (int)$d['dept_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['dept_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="um-form-group">
                <label for="um_role"><i class="fas fa-user-tag"></i> Role</label>
                <select id="um_role" name="role_id" class="um-select">
                    <option value="0">All Roles</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= (int)$r['role_id'] ?>" <?= $filter_role === (int)$r['role_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['role_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="um-form-group" style="flex: 1 1 140px;">
                <label for="um_status"><i class="fas fa-toggle-on"></i> Status</label>
                <select id="um_status" name="status" class="um-select">
                    <option value="all" <?= $filter_status === 'all' ? 'selected' : '' ?>>All Status</option>
                    <option value="active" <?= $filter_status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $filter_status === 'inactive' ? 'selected' : '' ?>>Relieved / Inactive</option>
                </select>
            </div>

            <div class="um-filter-actions">
                <button type="submit" class="btn-filter-apply"><i class="fas fa-filter"></i> Apply</button>
                <a href="<?= $app_url ?>/public/index.php?route=users/list" class="btn-filter-reset">Reset</a>
            </div>
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="um-table-card">
        <table class="um-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">S.No</th>
                    <th>User & Credentials</th>
                    <th>Primary Dept</th>
                    <th>Assigned Roles</th>
                    <th style="text-align: center;">Documents</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: right;">Administrative Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                            <i class="fas fa-user-slash" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                            No users found matching the selected search and filter criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $sno = $offset + 1; 
                    foreach ($users as $u): 
                        // Find primary department
                        $primary_dept = '—';
                        $dept_id_val = 0;
                        foreach ($u['roles'] as $ur) {
                            if (!empty($ur['dept_name'])) {
                                $primary_dept = $ur['dept_name'];
                                $dept_id_val = (int)$ur['dept_id'];
                                break;
                            }
                        }
                    ?>
                    <tr>
                        <!-- Sequential Human S.No (No gaps!) -->
                        <td style="text-align: center; font-weight: 700; color: #64748b;"><?= $sno++ ?></td>

                        <!-- User Info -->
                        <td>
                            <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-user-circle" style="color: #94a3b8; font-size: 1.1rem;"></i>
                                <?= htmlspecialchars($u['full_name']) ?>
                            </div>
                            <div style="font-size: 0.82rem; color: #64748b; margin-top: 2px;">
                                <?= htmlspecialchars($u['email']) ?>
                                <?php if (!empty($u['last_login'])): ?>
                                    <span style="color: #94a3b8; margin-left: 8px;">• Last login: <?= date('d M Y, h:i A', strtotime($u['last_login'])) ?></span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Department -->
                        <td>
                            <span style="background: #f1f5f9; color: #334155; padding: 4px 9px; border-radius: 6px; font-weight: 600; font-size: 0.82rem; border: 1px solid #e2e8f0;">
                                <?= htmlspecialchars($primary_dept) ?>
                            </span>
                        </td>

                        <!-- Roles -->
                        <td>
                            <?php if (empty($u['roles'])): ?>
                                <span style="color: #94a3b8; font-style: italic; font-size: 0.8rem;">No roles assigned</span>
                            <?php else: ?>
                                <?php foreach ($u['roles'] as $r): 
                                    $rid = (int)$r['role_id'];
                                    $badge_class = 'badge-role-other';
                                    $icon = 'fa-tag';
                                    if ($rid === ROLE_ADMIN) { $badge_class = 'badge-role-admin'; $icon = 'fa-shield-alt'; }
                                    elseif ($rid === ROLE_HOD) { $badge_class = 'badge-role-hod'; $icon = 'fa-crown'; }
                                    elseif ($rid === ROLE_DEPT_COORDINATOR) { $badge_class = 'badge-role-coord'; $icon = 'fa-tasks'; }
                                    elseif ($rid === ROLE_FACULTY) { $badge_class = 'badge-role-faculty'; $icon = 'fa-chalkboard-teacher'; }
                                    elseif ($rid === ROLE_IQAC) { $badge_class = 'badge-role-iqac'; $icon = 'fa-award'; }
                                    elseif ($rid === ROLE_STUDENT) { $badge_class = 'badge-role-student'; $icon = 'fa-graduation-cap'; }
                                ?>
                                    <span class="badge-role <?= $badge_class ?>" title="<?= htmlspecialchars($r['role_name']) ?>">
                                        <i class="fas <?= $icon ?>" style="font-size: 0.7rem;"></i>
                                        <?= htmlspecialchars($r['role_name']) ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>

                        <!-- Document Count -->
                        <td style="text-align: center;">
                            <?php if ($u['doc_count'] > 0): ?>
                                <span style="background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 9999px; font-size: 0.78rem; font-weight: 700;">
                                    <?= (int)$u['doc_count'] ?> docs
                                </span>
                            <?php else: ?>
                                <span style="color: #94a3b8; font-size: 0.8rem;">0 docs</span>
                            <?php endif; ?>
                        </td>

                        <!-- Status -->
                        <td style="text-align: center;">
                            <?php if ($u['status'] === 'active'): ?>
                                <span class="status-pill status-pill-active">Active</span>
                            <?php else: ?>
                                <span class="status-pill status-pill-inactive" title="Relieved / Suspended — cannot log in, documents preserved">Relieved</span>
                            <?php endif; ?>
                        </td>

                        <!-- Actions -->
                        <td style="text-align: right;">
                            <div class="um-actions-group" style="justify-content: flex-end;">
                                <!-- Edit Roles & Dept -->
                                <button type="button" class="btn-icon-action" 
                                        onclick="openRolesModal(<?= (int)$u['user_id'] ?>, '<?= htmlspecialchars(addslashes($u['full_name'])) ?>', <?= (int)$dept_id_val ?>, <?= htmlspecialchars(json_encode(array_column($u['roles'], 'role_id'))) ?>)"
                                        title="Manage Roles & Department">
                                    <i class="fas fa-user-tag" style="color: #0284c7;"></i> Roles
                                </button>

                                <!-- Reset Password -->
                                <button type="button" class="btn-icon-action" 
                                        onclick="openResetModal(<?= (int)$u['user_id'] ?>, '<?= htmlspecialchars(addslashes($u['full_name'])) ?>')"
                                        title="Reset Password to Default">
                                    <i class="fas fa-key" style="color: #d97706;"></i> Reset
                                </button>

                                <!-- Relieve / Reactivate Status Toggle -->
                                <?php if ($u['user_id'] != $auth['user_id']): ?>
                                    <form method="POST" action="<?= $app_url ?>/public/index.php?route=users/toggle_status" style="margin: 0;" onsubmit="return confirm('<?= ($u['status'] === 'active') ? 'Mark ' . addslashes($u['full_name']) . ' as Relieved / Inactive? They will not be able to log in, but all their uploaded documents will remain intact.' : 'Reactivate ' . addslashes($u['full_name']) . '?' ?>');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                                        <?php if ($u['status'] === 'active'): ?>
                                            <button type="submit" class="btn-icon-action" style="color: #d97706;" title="Mark as Relieved / Inactive">
                                                <i class="fas fa-pause-circle"></i> Relieve
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="btn-icon-action" style="color: #166534;" title="Reactivate Account">
                                                <i class="fas fa-play-circle"></i> Activate
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>

                                <!-- Safe Delete Button -->
                                <?php if ($u['user_id'] != $auth['user_id']): ?>
                                    <?php if ($u['doc_count'] == 0 && $u['action_count'] == 0): ?>
                                        <button type="button" class="btn-icon-action btn-icon-danger" 
                                                onclick="openDeleteModal(<?= (int)$u['user_id'] ?>, '<?= htmlspecialchars(addslashes($u['full_name'])) ?>')"
                                                title="Permanently Delete Test Account">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn-icon-action btn-icon-disabled" 
                                                title="Institutional Safety Rule: Cannot delete accounts with existing documents or audit history. Use 'Relieve' instead." disabled>
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination Bar -->
        <?php if ($total_pages > 1): ?>
        <div style="padding: 16px 20px; background: #ffffff; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div style="font-size: 0.88rem; color: #64748b;">
                Showing <?= $offset + 1 ?> to <?= min($offset + $per_page, $total_users) ?> of <?= $total_users ?> users
            </div>
            <div style="display: flex; gap: 6px;">
                <?php 
                $query_params = $_GET;
                unset($query_params['page']);
                for ($p = 1; $p <= $total_pages; $p++): 
                    $page_url = "?" . htmlspecialchars(http_build_query(array_merge($query_params, ['page' => $p])), ENT_QUOTES, 'UTF-8');
                ?>
                    <a href="<?= $page_url ?>" style="padding: 6px 12px; border-radius: 6px; font-weight: 600; text-decoration: none; font-size: 0.85rem; <?= ($page === $p) ? 'background: #003366; color: white;' : 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ==============================================================
     MODAL 1: ADD NEW USER
============================================================== -->
<div id="modalAddUser" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Add New User</h3>
            <button type="button" class="modal-close" onclick="closeModal('modalAddUser')">&times;</button>
        </div>
        <form method="POST" action="<?= $app_url ?>/public/index.php?route=users/create">
            <?= csrfField() ?>
            <div class="modal-body">
                <div style="margin-bottom: 14px;">
                    <label style="font-weight: 600; font-size: 0.88rem; display: block; margin-bottom: 5px;">Full Name *</label>
                    <input type="text" name="full_name" required class="um-input" style="width: 100%; box-sizing: border-box;" placeholder="e.g. Dr. K. Venkata Rao">
                </div>
                <div style="margin-bottom: 14px;">
                    <label style="font-weight: 600; font-size: 0.88rem; display: block; margin-bottom: 5px;">Email Address *</label>
                    <input type="email" name="email" required class="um-input" style="width: 100%; box-sizing: border-box;" placeholder="e.g. venkat.k@gmrit.edu.in">
                </div>
                <div style="margin-bottom: 14px;">
                    <label style="font-weight: 600; font-size: 0.88rem; display: block; margin-bottom: 5px;">Department *</label>
                    <select name="dept_id" required class="um-select" style="width: 100%;">
                        <option value="">Select Department...</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= (int)$d['dept_id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="margin-bottom: 14px;">
                    <label style="font-weight: 600; font-size: 0.88rem; display: block; margin-bottom: 5px;">Initial Password (Optional)</label>
                    <input type="text" name="password" class="um-input" style="width: 100%; box-sizing: border-box;" placeholder="Default: Admin@123">
                </div>
                <div style="margin-bottom: 8px;">
                    <label style="font-weight: 600; font-size: 0.88rem; display: block; margin-bottom: 8px;">Assign Initial Role(s):</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <?php foreach ($roles as $r): ?>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; background: #f8fafc; padding: 6px 10px; border-radius: 6px; border: 1px solid #e2e8f0; cursor: pointer;">
                                <input type="checkbox" name="role_ids[]" value="<?= (int)$r['role_id'] ?>" <?= ((int)$r['role_id'] === ROLE_FACULTY) ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($r['role_name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-filter-reset" onclick="closeModal('modalAddUser')">Cancel</button>
                <button type="submit" class="btn-add-user"><i class="fas fa-check"></i> Create User</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================================
     MODAL 2: MANAGE ROLES & DEPARTMENT
============================================================== -->
<div id="modalRoles" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="fas fa-user-tag"></i> Manage Roles & Department</h3>
            <button type="button" class="modal-close" onclick="closeModal('modalRoles')">&times;</button>
        </div>
        <form method="POST" action="<?= $app_url ?>/public/index.php?route=users/update_roles">
            <?= csrfField() ?>
            <input type="hidden" name="user_id" id="edit_roles_user_id">
            
            <div class="modal-body">
                <div style="background: #e0f2fe; border: 1px solid #bae6fd; padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; font-size: 0.9rem; color: #0369a1;">
                    Editing permissions for: <strong id="edit_roles_user_name"></strong>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="font-weight: 600; font-size: 0.88rem; display: block; margin-bottom: 5px;">Department</label>
                    <select name="dept_id" id="edit_roles_dept_id" class="um-select" style="width: 100%;">
                        <option value="0">None / Institutional</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= (int)$d['dept_id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 8px;">
                    <label style="font-weight: 600; font-size: 0.88rem; display: block; margin-bottom: 8px;">Assigned Roles:</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <?php foreach ($roles as $r): ?>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; background: #f8fafc; padding: 7px 10px; border-radius: 6px; border: 1px solid #e2e8f0; cursor: pointer;">
                                <input type="checkbox" name="role_ids[]" value="<?= (int)$r['role_id'] ?>" class="role-chk-input" id="chk_role_<?= (int)$r['role_id'] ?>">
                                <span><?= htmlspecialchars($r['role_name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 1-HOD Policy Notice -->
                <div style="background: #fef3c7; border-left: 3px solid #d97706; padding: 8px 12px; border-radius: 4px; margin-top: 14px; font-size: 0.82rem; color: #92400e;">
                    <i class="fas fa-info-circle"></i> <strong>HOD Policy:</strong> If you assign <strong>HOD</strong>, any existing HOD in this department will automatically be transitioned back to regular Faculty.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-filter-reset" onclick="closeModal('modalRoles')">Cancel</button>
                <button type="submit" class="btn-filter-apply"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================================
     MODAL 3: RESET PASSWORD CONFIRMATION
============================================================== -->
<div id="modalReset" class="modal-overlay">
    <div class="modal-card" style="max-width: 440px;">
        <div class="modal-header" style="background: #d97706;">
            <h3><i class="fas fa-key"></i> Reset Password</h3>
            <button type="button" class="modal-close" onclick="closeModal('modalReset')">&times;</button>
        </div>
        <form method="POST" action="<?= $app_url ?>/public/index.php?route=users/reset_password">
            <?= csrfField() ?>
            <input type="hidden" name="user_id" id="reset_user_id">
            <div class="modal-body" style="text-align: center; padding: 25px 20px;">
                <i class="fas fa-shield-alt" style="font-size: 2.8rem; color: #d97706; margin-bottom: 12px; display: block;"></i>
                <h4 style="margin: 0 0 8px 0; color: #1e293b; font-size: 1.1rem;">Reset Password for <span id="reset_user_name"></span>?</h4>
                <p style="margin: 0; color: #64748b; font-size: 0.9rem;">The password will be reset to the institutional default:</p>
                <div style="background: #fef3c7; color: #92400e; font-family: monospace; font-size: 1.1rem; font-weight: bold; padding: 8px; border-radius: 6px; margin: 12px auto; display: inline-block;">
                    Admin@123
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-filter-reset" onclick="closeModal('modalReset')">Cancel</button>
                <button type="submit" class="btn-filter-apply" style="background: #d97706;"><i class="fas fa-check"></i> Yes, Reset</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================================
     MODAL 4: DELETE USER CONFIRMATION (TEST ACCOUNTS ONLY)
============================================================== -->
<div id="modalDelete" class="modal-overlay">
    <div class="modal-card" style="max-width: 440px;">
        <div class="modal-header" style="background: #dc2626;">
            <h3><i class="fas fa-trash-alt"></i> Delete Test Account</h3>
            <button type="button" class="modal-close" onclick="closeModal('modalDelete')">&times;</button>
        </div>
        <form method="POST" action="<?= $app_url ?>/public/index.php?route=users/delete">
            <?= csrfField() ?>
            <input type="hidden" name="user_id" id="delete_user_id">
            <div class="modal-body" style="text-align: center; padding: 25px 20px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 2.8rem; color: #dc2626; margin-bottom: 12px; display: block;"></i>
                <h4 style="margin: 0 0 8px 0; color: #1e293b; font-size: 1.1rem;">Permanently Delete <span id="delete_user_name"></span>?</h4>
                <p style="margin: 0; color: #64748b; font-size: 0.88rem;">
                    This action is permanent and only allowed because this account has 0 documents and 0 audit actions.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-filter-reset" onclick="closeModal('modalDelete')">Cancel</button>
                <button type="submit" class="btn-filter-apply" style="background: #dc2626;"><i class="fas fa-trash"></i> Yes, Delete Account</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddUserModal() {
    document.getElementById('modalAddUser').style.display = 'flex';
}

function openRolesModal(userId, userName, deptId, activeRoleIds) {
    document.getElementById('edit_roles_user_id').value = userId;
    document.getElementById('edit_roles_user_name').textContent = userName;
    document.getElementById('edit_roles_dept_id').value = deptId || 0;

    // Uncheck all role checkboxes first
    document.querySelectorAll('.role-chk-input').forEach(chk => chk.checked = false);

    // Check currently active roles
    if (Array.isArray(activeRoleIds)) {
        activeRoleIds.forEach(rid => {
            const el = document.getElementById('chk_role_' + rid);
            if (el) el.checked = true;
        });
    }

    document.getElementById('modalRoles').style.display = 'flex';
}

function openResetModal(userId, userName) {
    document.getElementById('reset_user_id').value = userId;
    document.getElementById('reset_user_name').textContent = userName;
    document.getElementById('modalReset').style.display = 'flex';
}

function openDeleteModal(userId, userName) {
    document.getElementById('delete_user_id').value = userId;
    document.getElementById('delete_user_name').textContent = userName;
    document.getElementById('modalDelete').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside of card
window.addEventListener('click', function(e) {
    document.querySelectorAll('.modal-overlay').forEach(m => {
        if (e.target === m) {
            m.style.display = 'none';
        }
    });
});
</script>

</body>
</html>
