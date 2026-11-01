<?php
/**
 * Administrator Dashboard Section (System Administration, Governance, Accreditation, Master Repository & Live Audit Trail)
 */
?>
<!-- 1. ADMIN HERO GOVERNANCE HEADER & SYSTEM METRICS -->
<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; border-radius: 12px; padding: 2.2rem 2.5rem; margin-top: 1.5rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.08);">
    <div style="position: absolute; top: -50px; right: -50px; width: 220px; height: 220px; background: rgba(2, 132, 199, 0.08); border-radius: 50%;"></div>
    <div style="position: absolute; bottom: -80px; left: 15%; width: 320px; height: 320px; background: rgba(99, 102, 241, 0.05); border-radius: 50%;"></div>
    
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; position: relative; z-index: 2; gap: 2rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 0.5rem; flex-wrap: wrap;">
                <h1 style="font-size: 2rem; font-weight: 800; margin: 0; letter-spacing: -0.5px;">System Administration &amp; Governance</h1>
                <span style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 4px 14px; border-radius: 999px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-shield-alt"></i> Super Administrator
                </span>
            </div>
            <p style="font-size: 1.05rem; color: #94a3b8; margin: 0; max-width: 650px;">
                Centralized institutional management of user accounts, leadership appointments, academic cycles, departments, and college-wide security audit trails.
            </p>
        </div>
        
        <!-- Admin Core Telemetry Metrics -->
        <div style="display: flex; gap: 1.25rem; background: rgba(0,0,0,0.3); padding: 1.25rem 1.5rem; border-radius: 12px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.08); flex-wrap: wrap;">
            <a href="<?= BASE_URL ?>/public/index.php?route=admin/users" style="text-align: center; text-decoration: none; color: inherit;">
                <div style="font-size: 2rem; font-weight: 800; color: #60a5fa; line-height: 1;"><?= (int)($admin_metrics['active_users'] ?? 0) ?></div>
                <div style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-top: 0.4rem;">Active Users</div>
            </a>
            <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
            <a href="<?= BASE_URL ?>/public/index.php?route=admin/departments" style="text-align: center; text-decoration: none; color: inherit;">
                <div style="font-size: 2rem; font-weight: 800; color: #34d399; line-height: 1;"><?= (int)($admin_metrics['total_depts'] ?? 0) ?></div>
                <div style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-top: 0.4rem;">Depts (<?= (int)($admin_metrics['academic_depts'] ?? 0) ?> Acad)</div>
            </a>
            <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
            <a href="<?= BASE_URL ?>/public/index.php?route=academic_years/list" style="text-align: center; text-decoration: none; color: inherit;">
                <div style="font-size: 1.6rem; font-weight: 800; color: #fbbf24; line-height: 1.25;"><?= htmlspecialchars($admin_metrics['active_year'] ?? 'Not Set') ?></div>
                <div style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-top: 0.4rem;">Active Cycle</div>
            </a>
            <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
            <a href="<?= BASE_URL ?>/public/index.php?route=documents/list" style="text-align: center; text-decoration: none; color: inherit;">
                <div style="font-size: 2rem; font-weight: 800; color: #a78bfa; line-height: 1;"><?= (int)($admin_metrics['total_documents'] ?? 0) ?></div>
                <div style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-top: 0.4rem;">Documents</div>
            </a>
            <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
            <a href="<?= BASE_URL ?>/public/index.php?route=admin/audit_logs" style="text-align: center; text-decoration: none; color: inherit;">
                <div style="font-size: 2rem; font-weight: 800; color: #f472b6; line-height: 1;"><?= (int)($admin_metrics['audit_events'] ?? 0) ?></div>
                <div style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-top: 0.4rem;">Audit Traces</div>
            </a>
        </div>
    </div>
</div>

<!-- SECTION 1: GOVERNANCE & SYSTEM ADMINISTRATION -->
<div class="header-title" style="margin-top: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 1.25rem;">
    <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-sliders-h" style="color: #0284c7;"></i> System Administration &amp; Governance
    </h2>
</div>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 2.2rem;">
    <a href="<?= BASE_URL ?>/public/index.php?route=admin/users" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #0284c7; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(2, 132, 199, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
        <div style="background: #e0f2fe; width: 42px; height: 42px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem; flex-shrink: 0;">
            <i class="fas fa-users-cog" style="color: #0284c7; font-size: 1.2rem;"></i>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a;">Users &amp; Leadership</div>
            <div style="font-size: 0.78rem; color: #64748b;">Manage accounts, assign HODs &amp; roles</div>
        </div>
    </a>

    <a href="<?= BASE_URL ?>/public/index.php?route=admin/workflows" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #6366f1; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(99, 102, 241, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
        <div style="background: #eef2ff; width: 42px; height: 42px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem; flex-shrink: 0;">
            <i class="fas fa-project-diagram" style="color: #6366f1; font-size: 1.2rem;"></i>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a;">Workflow Management</div>
            <div style="font-size: 0.78rem; color: #64748b;">Approval state machine &amp; doc routing</div>
        </div>
    </a>

    <a href="<?= BASE_URL ?>/public/index.php?route=admin/departments" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #0284c7; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(2, 132, 199, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
        <div style="background: #f0f9ff; width: 42px; height: 42px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem; flex-shrink: 0;">
            <i class="fas fa-sitemap" style="color: #0284c7; font-size: 1.2rem;"></i>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a;">Departments &amp; Units</div>
            <div style="font-size: 0.78rem; color: #64748b;">Academic branches &amp; administrative cells</div>
        </div>
    </a>

    <a href="<?= BASE_URL ?>/public/index.php?route=academic_years/list" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #f59e0b; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(245, 158, 11, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
        <div style="background: #fffbeb; width: 42px; height: 42px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem; flex-shrink: 0;">
            <i class="fas fa-calendar-alt" style="color: #f59e0b; font-size: 1.2rem;"></i>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a;">Academic Years</div>
            <div style="font-size: 0.78rem; color: #64748b;">Configure cycles &amp; active calendar</div>
        </div>
    </a>

    <a href="<?= BASE_URL ?>/public/index.php?route=admin/audit_logs" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #0284c7; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(2, 132, 199, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
        <div style="background: #f0f9ff; width: 42px; height: 42px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem; flex-shrink: 0;">
            <i class="fas fa-history" style="color: #0284c7; font-size: 1.2rem;"></i>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a;">Audit Trail &amp; Logs</div>
            <div style="font-size: 0.78rem; color: #64748b;">College-wide security &amp; event trace</div>
        </div>
    </a>
</div>

<!-- SECTION 2: INSTITUTIONAL ACCREDITATION & PORTALS -->
<div class="header-title" style="margin-top: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 1.25rem;">
    <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-university" style="color: #3b82f6;"></i> Institutional Accreditation &amp; Campus Portals
    </h2>
</div>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 2.2rem;">
    <a href="<?= BASE_URL ?>/public/index.php?route=nba/dashboard" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
        <div style="background: #eff6ff; width: 42px; height: 42px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem; flex-shrink: 0;">
            <i class="fas fa-award" style="color: #3b82f6; font-size: 1.2rem;"></i>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a;">NBA Accreditation Portal</div>
            <div style="font-size: 0.78rem; color: #64748b;">Inspect &amp; evaluate NBA criteria</div>
        </div>
    </a>

    <a href="<?= BASE_URL ?>/public/index.php?route=naac/dashboard" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #ec4899; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(236, 72, 153, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
        <div style="background: #fdf2f8; width: 42px; height: 42px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem; flex-shrink: 0;">
            <i class="fas fa-certificate" style="color: #ec4899; font-size: 1.2rem;"></i>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a;">NAAC Accreditation Portal</div>
            <div style="font-size: 0.78rem; color: #64748b;">Monitor institutional NAAC metrics</div>
        </div>
    </a>

    <a href="<?= BASE_URL ?>/public/index.php?route=student_activities/dashboard" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #14b8a6; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(20, 184, 166, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
        <div style="background: #f0fdfa; width: 42px; height: 42px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem; flex-shrink: 0;">
            <i class="fas fa-user-graduate" style="color: #14b8a6; font-size: 1.2rem;"></i>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a;">Student Activities &amp; Clubs</div>
            <div style="font-size: 0.78rem; color: #64748b;">Campus-wide student achievements</div>
        </div>
    </a>
</div>

<!-- SECTION 3: MASTER DOCUMENT REPOSITORY -->
<div class="header-title" style="margin-top: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 1.25rem;">
    <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-folder-open" style="color: #8b5cf6;"></i> College-Wide Master Document Repository
    </h2>
</div>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 2.2rem;">
    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #8b5cf6; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(139, 92, 246, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
        <div style="background: #f5f3ff; width: 42px; height: 42px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem; flex-shrink: 0;">
            <i class="fas fa-file-invoice" style="color: #8b5cf6; font-size: 1.2rem;"></i>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a;">All College Documents</div>
            <div style="font-size: 0.78rem; color: #64748b;">Search, view, download or purge faculty records</div>
        </div>
    </a>

    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&amp;context=dept_file" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #06b6d4; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(6, 182, 212, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
        <div style="background: #ecfeff; width: 42px; height: 42px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem; flex-shrink: 0;">
            <i class="fas fa-folder" style="color: #06b6d4; font-size: 1.2rem;"></i>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a;">All Department Files</div>
            <div style="font-size: 0.78rem; color: #64748b;">Departmental accreditation proofs &amp; archives</div>
        </div>
    </a>
</div>

<!-- SECTION 4: LIVE SYSTEM ACTIVITY & AUDIT TRAIL FEED -->
<div class="header-title" style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 10px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 1.25rem;">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-shield-alt" style="color: #0284c7;"></i> Live System Activity &amp; Audit Trail
        </h2>
        <p style="color: #64748b; margin: 4px 0 0 0; font-size: 0.85rem;">Recent security events, role appointments, logins, and system changes.</p>
    </div>
    <a href="<?= BASE_URL ?>/public/index.php?route=admin/audit_logs" style="color: #0284c7; font-size: 0.88rem; font-weight: 600; text-decoration: none;">View All System Logs &rarr;</a>
</div>

<?php if (!empty($recent_audits)): ?>
<div style="background: white; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); overflow-x: auto; margin-bottom: 2.5rem;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
        <thead>
            <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                <th style="padding: 10px 14px; font-weight: 700; color: #475569; width: 50px; text-align: center;">#</th>
                <th style="padding: 10px 14px; font-weight: 700; color: #475569;">Timestamp</th>
                <th style="padding: 10px 14px; font-weight: 700; color: #475569;">Stream</th>
                <th style="padding: 10px 14px; font-weight: 700; color: #475569;">Action</th>
                <th style="padding: 10px 14px; font-weight: 700; color: #475569;">Actor</th>
                <th style="padding: 10px 14px; font-weight: 700; color: #475569;">Target / Department</th>
                <th style="padding: 10px 14px; font-weight: 700; color: #475569;">Details</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $a_idx = 1;
            foreach ($recent_audits as $audit): 
                $act = strtoupper((string)($audit['action'] ?? ''));
                $is_good = str_contains($act, 'CREATE') || str_contains($act, 'APPOINT') || str_contains($act, 'APPROVE') || str_contains($act, 'ACTIVE');
                $is_bad  = str_contains($act, 'DELETE') || str_contains($act, 'REVOKE') || str_contains($act, 'REJECT') || str_contains($act, 'INACTIVE');
                $badge_bg = $is_good ? '#dcfce7' : ($is_bad ? '#fee2e2' : '#e0f2fe');
                $badge_fg = $is_good ? '#166534' : ($is_bad ? '#991b1b' : '#0369a1');
                $badge_bd = $is_good ? '#bbf7d0' : ($is_bad ? '#fecaca' : '#bae6fd');
            ?>
            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                <td style="padding: 10px 14px; text-align: center; font-weight: 700; color: #94a3b8;"><?= $a_idx++ ?></td>
                <td style="padding: 10px 14px; white-space: nowrap; color: #0f172a; font-weight: 600;">
                    <?= date('d M Y, h:i A', strtotime($audit['created_at'])) ?>
                </td>
                <td style="padding: 10px 14px;">
                    <span style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                        <?= htmlspecialchars($audit['stream'] ?? 'System') ?>
                    </span>
                </td>
                <td style="padding: 10px 14px;">
                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; background: <?= $badge_bg ?>; color: <?= $badge_fg ?>; border: 1px solid <?= $badge_bd ?>;">
                        <?= htmlspecialchars($audit['action'] ?? '') ?>
                    </span>
                </td>
                <td style="padding: 10px 14px;">
                    <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($audit['actor_name'] ?? 'System') ?></div>
                    <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($audit['actor_email'] ?? '') ?></div>
                </td>
                <td style="padding: 10px 14px;">
                    <span style="font-weight: 600; color: #334155; font-size: 0.8rem; background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 4px;">
                        <?= htmlspecialchars($audit['target_dept'] ?: ($audit['target_entity'] ?: '—')) ?>
                    </span>
                </td>
                <td style="padding: 10px 14px; color: #475569; font-size: 0.82rem; max-width: 320px;">
                    <?= htmlspecialchars($audit['details'] ?? '') ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div style="background: white; border-radius: 8px; border: 1px dashed #cbd5e1; padding: 2.5rem 1rem; text-align: center; margin-bottom: 2rem;">
    <i class="fas fa-history" style="font-size: 2.5rem; color: #94a3b8; opacity: 0.5; margin-bottom: 0.75rem;"></i>
    <p style="color: #64748b; margin: 0;">No audit events recorded yet.</p>
</div>
<?php endif; ?>
