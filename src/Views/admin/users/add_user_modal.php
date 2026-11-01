<?php
/**
 * Modal: Add New Faculty Member
 */
?>
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
                <label for="addUserFullName" style="display: block; font-weight: 600; font-size: 0.88rem; color: #334155; margin-bottom: 6px;">Full Name *</label>
                <input type="text" id="addUserFullName" name="full_name" required placeholder="e.g. Dr. K. Satyanarayana" style="width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 16px;">
                <label for="addUserEmail" style="display: block; font-weight: 600; font-size: 0.88rem; color: #334155; margin-bottom: 6px;">Institutional Email *</label>
                <input type="email" id="addUserEmail" name="email" required placeholder="e.g. satyanarayana.k@gmrit.edu.in" style="width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 16px;">
                <label for="addUserDeptId" style="display: block; font-weight: 600; font-size: 0.88rem; color: #334155; margin-bottom: 6px;">Department *</label>
                <select id="addUserDeptId" name="dept_id" required style="width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; box-sizing: border-box; background: white;">
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= (int)$d['dept_id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 22px;">
                <label for="addUserInitialRole" style="display: block; font-weight: 600; font-size: 0.88rem; color: #334155; margin-bottom: 6px;">Initial Designation</label>
                <select id="addUserInitialRole" name="initial_role" style="width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; box-sizing: border-box; background: white;">
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
