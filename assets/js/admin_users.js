/**
 * Admin Faculty & Leadership Management Client Controller
 */

function openAddUserModal() {
    const m = document.getElementById('addUserModal');
    if (m) m.style.display = 'flex';
}

function closeAddUserModal() {
    const m = document.getElementById('addUserModal');
    if (m) m.style.display = 'none';
}

function openAssignModal(userId, userName, userEmail, primaryDeptId, primaryDeptName, roles) {
    const nameEl = document.getElementById('modalUserName');
    const emailEl = document.getElementById('modalUserEmail');
    const badgeEl = document.getElementById('modalUserDeptBadge');

    if (nameEl) nameEl.textContent = userName;
    if (emailEl) emailEl.textContent = userEmail;
    if (badgeEl) badgeEl.textContent = primaryDeptName ? ('Department: ' + primaryDeptName) : 'No Department Assigned';

    // Populate all user ID hidden inputs in the modal
    const userInputs = document.querySelectorAll('.modalFormUserId');
    userInputs.forEach(input => { input.value = userId; });

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
    const cfg = window.USER_ROLES_CONFIG || {};
    const ROLE_IQAC = cfg.ROLE_IQAC || 2;
    const ROLE_HOD = cfg.ROLE_HOD || 3;
    const ROLE_DEPT_COORDINATOR = cfg.ROLE_DEPT_COORDINATOR || 5;
    const ROLE_CENTRAL_COORDINATOR = cfg.ROLE_CENTRAL_COORDINATOR || 6;
    const ROLE_RND_DEAN = cfg.ROLE_RND_DEAN || 8;

    let hasIqac = false;
    let hasCentral = false;
    let hasRnd = false;

    // Render active roles badges
    const badgesContainer = document.getElementById('modalActiveRolesBadges');
    if (badgesContainer) {
        badgesContainer.innerHTML = '';

        if (!roles || roles.length === 0) {
            badgesContainer.innerHTML = '<span style="color: #94a3b8; font-size: 0.85rem; font-style: italic;">No active roles assigned.</span>';
        } else {
            roles.forEach(r => {
                const roleId = Number.parseInt(r.role_id, 10);
                const deptId = r.dept_id ? Number.parseInt(r.dept_id, 10) : 0;
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
    const uEl = document.getElementById('revokeUserId');
    const rEl = document.getElementById('revokeRoleId');
    const dEl = document.getElementById('revokeDeptId');
    if (uEl) uEl.value = userId;
    if (rEl) rEl.value = roleId;
    if (dEl) dEl.value = deptId || '';
    const form = document.getElementById('hiddenRevokeForm');
    if (form) form.submit();
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
