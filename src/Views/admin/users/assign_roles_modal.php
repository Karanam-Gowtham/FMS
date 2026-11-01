<?php
/**
 * Modal: Comprehensive Role & Department Leadership Manager
 */
?>
<div id="assignModal" style="display: none; position: fixed; z-index: 9999; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(5px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 14px; max-width: 680px; width: 100%; box-shadow: 0 25px 30px -5px rgba(0,0,0,0.3); overflow: hidden; border: 1px solid #e2e8f0; animation: modalPop 0.2s ease-out;">
        <div style="background: #0f172a; color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 700; font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-user-shield" style="color: #38bdf8;"></i> Manage Faculty Roles &amp; Department
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
                                <i class="fas fa-flask"></i> Dean of R&amp;D
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
                        <label for="hodDeptSelect" class="visually-hidden">Select department to appoint as HOD</label>
                        <select name="dept_id" id="hodDeptSelect" required aria-label="Select department to appoint as HOD" style="flex: 1; min-width: 200px; padding: 8px 10px; border: 1.5px solid #d97706; border-radius: 6px; font-size: 0.88rem; background: white;">
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
                        <label for="coordDeptSelect" class="visually-hidden">Select department for Coordinator role</label>
                        <select name="dept_id" id="coordDeptSelect" required aria-label="Select department for Coordinator role" style="flex: 1; min-width: 200px; padding: 8px 10px; border: 1.5px solid #0284c7; border-radius: 6px; font-size: 0.88rem; background: white;">
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
                    <label for="transferDeptSelect" class="visually-hidden">Select target department for transfer</label>
                    <select name="dept_id" id="transferDeptSelect" required aria-label="Select target department for transfer" style="flex: 1; min-width: 220px; padding: 8px 10px; border: 1.5px solid #64748b; border-radius: 6px; font-size: 0.88rem; background: white;">
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
