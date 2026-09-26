<?php
include_once HEADER;

$dept_name_display = $active_role ? $active_role['dept_name'] : 'Unknown';
$role_name_display = $active_role ? $active_role['role_name'] : 'Unknown';

// Determine breadcrumb based on role
if ($active_role && in_array($active_role['role_id'], [ROLE_FACULTY])) {
    $breadcrumb = "Faculty Dashboard ($dept_name_display)";
} else if ($active_role && in_array($active_role['role_id'], [ROLE_HOD])) {
    $breadcrumb = "HOD Dashboard ($dept_name_display)";
} else if ($active_role) {
    $breadcrumb = "$role_name_display Dashboard ($dept_name_display)";
} else {
    $breadcrumb = "Dashboard";
}

?>
<style>
    /* Reset and base styles from legacy */
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background-color: rgb(249, 250, 251);
        color: rgb(55, 65, 81);
        line-height: 1.5;
        margin: 0;
        padding: 0;
    }

    /* Navigation Breadcrumb */
    .navbar-breadcrumb {
        background-color: white;
        font-size: larger;
        border-bottom: 1px solid #e5e7eb;
    }

    .nav-container-breadcrumb {
        margin-left: 100px;
        max-width: 80rem;
        padding: 1rem 1rem;
    }

    .nav-items-breadcrumb {
        display: flex;
        align-items: center;
        height: 2rem;
    }

    .home-icon {
        color: rgb(30, 58, 138);
        transition: color 0.2s;
        display: flex;
        align-items: center;
    }
    .home-icon:hover {
        color: rgb(29, 78, 216);
    }
    
    .main-a {
        color: rgb(138, 30, 113);
        font-weight: 500;
        text-decoration: none;
    }
    .main-a:hover{
        color:rgb(182, 64, 211);
    }

    /* Main content */
    .main-content {
        padding: 2rem 1rem;
        max-width: 80rem;
        margin: 0px auto 100px auto;
    }

    .container12 {
        margin: 0px 100px;
    }

    .header-title {
        margin-bottom: 1.5rem;
    }

    .header-title h1 {
        font-size: 1.5rem;
        font-weight: bold;
        color: rgb(17, 24, 39);
    }

    .my-achievements-btn {
        display: inline-block;
        background-color: #2563eb;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 30px;
        transition: background-color 0.2s;
    }
    .my-achievements-btn:hover {
        background-color: #1d4ed8;
    }

    /* Feedback Grid */
    .feedback-grid {
        margin-bottom: 50px;
        display: grid;
        grid-template-columns: 1fr;
        gap: 2.5rem;
    }

    @media (min-width: 768px) {
        .feedback-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .feedback-card {
        text-decoration: none;
        display: block;
        transition: transform 0.2s;
        border: none;
        padding: 0;
        cursor: pointer;
        width: 100%;
        text-align: left;
    }

    .feedback-card:hover {
        transform: scale(1.05);
    }

    .card-content {
        background: linear-gradient(to right, rgb(30, 64, 175), rgb(37, 99, 235));
        padding: 1.5rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .card-content h3 {
        color: white;
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }

    .switch-role-btn {
        background: #f1f5f9;
        color: #334155;
        padding: 10px 15px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
        border: 1px solid #cbd5e1;
    }
    .switch-role-btn:hover {
        background: #e2e8f0;
    }

    .debug-section { margin-top: 50px; background: #1e293b; color: #a5b4fc; padding: 15px; border-radius: 8px; font-size: 0.85rem; }
</style>

<!-- Breadcrumb Navigation -->
<nav class="navbar-breadcrumb">
    <div class="nav-container-breadcrumb">
        <div class="nav-items-breadcrumb">
            <a href="<?= BASE_URL ?>/public/index.php?route=dashboard" class="home-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </a>
            <span style="color: #9ca3af; margin: 0 10px;"> &gt; </span>
            <span class="main"><a href="#" class="main-a"><?= htmlspecialchars($breadcrumb) ?></a></span>
        </div>
    </div>
</nav>

<main class="main-content">
    <div class="container12">
        
        <?php
        $role_id = $active_role ? (int)$active_role['role_id'] : 0;
        $is_faculty = ($role_id === ROLE_FACULTY);
        $is_reviewer = in_array($role_id, [ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR]);
        $is_dept_manager = in_array($role_id, [ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_JUNIOR_ASSISTANT, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR]);
        ?>
        
        <?php if ($is_faculty): ?>
            <!-- FACULTY DASHBOARD (Uploads) -->
            <div class="header-title">
                <h1>Achievements</h1>
            </div>
            
            <a href="<?= BASE_URL ?>/public/index.php?route=documents/list" class="my-achievements-btn">My Achievements</a>
            
            <div class="feedback-grid">
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=fdp_attended" class="feedback-card">
                    <div class="card-content"><h3>FDPS Attended</h3></div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=fdp_organised" class="feedback-card">
                    <div class="card-content"><h3>FDPS Organized</h3></div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=conf_organised" class="feedback-card">
                    <div class="card-content"><h3>Conference Organised</h3></div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=journal" class="feedback-card">
                    <div class="card-content"><h3>Research Papers Published</h3></div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=conference" class="feedback-card">
                    <div class="card-content"><h3>Conference Proceedings Published</h3></div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=patent" class="feedback-card">
                    <div class="card-content"><h3>Patents</h3></div>
                </a>
            </div>

            <div class="header-title" style="margin-top: 2rem;">
                <h1>Department Files</h1>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&context=dept_file" class="my-achievements-btn" style="margin-top:-10px;">My Dept Files</a>
            </div>
            <div class="feedback-grid">
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Admin Files" class="feedback-card">
                    <div class="card-content"><h3>Admin Files</h3></div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Faculty Files" class="feedback-card">
                    <div class="card-content"><h3>Faculty Files</h3></div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Student Related Files" class="feedback-card">
                    <div class="card-content"><h3>Student Related Files</h3></div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Exam Section Files" class="feedback-card">
                    <div class="card-content"><h3>Exam Section Files</h3></div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Student Activities Files" class="feedback-card">
                    <div class="card-content"><h3>Student Activities Files</h3></div>
                </a>
            </div>


        <?php endif; ?>

        <?php if ($is_reviewer || $is_dept_manager): ?>
            <!-- HOD / REVIEWER DASHBOARD (Viewing & Approving) -->
            <?php 
            $hod_view = $_GET['view'] ?? ''; 
            ?>

            <?php if ($hod_view === 'achievements' && $is_reviewer): ?>
                
                <div class="header-title">
                    <h1>Department Achievements</h1>
                    <a href="<?= BASE_URL ?>/public/index.php?route=dashboard" class="my-achievements-btn" style="margin-top:-10px; background: #6b7280;">&larr; Back to Dashboard</a>
                </div>
                
                <div class="feedback-grid">
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&type=fdp_attended" class="feedback-card">
                        <div class="card-content"><h3>View FDPS Attended Files</h3></div>
                    </a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&type=fdp_organised" class="feedback-card">
                        <div class="card-content"><h3>View FDPS Organized Files</h3></div>
                    </a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&type=conf_organised" class="feedback-card">
                        <div class="card-content"><h3>View Conference Organised Files</h3></div>
                    </a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&type=journal" class="feedback-card">
                        <div class="card-content"><h3>View Papers Published Files</h3></div>
                    </a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&type=conference" class="feedback-card">
                        <div class="card-content"><h3>View Conferences Published Files</h3></div>
                    </a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&type=patent" class="feedback-card">
                        <div class="card-content"><h3>View Patents Files</h3></div>
                    </a>
                </div>

            <?php elseif ($hod_view === 'dept_files' && $is_dept_manager): ?>

                <div class="header-title">
                    <h1>Department Files</h1>
                    <a href="<?= BASE_URL ?>/public/index.php?route=dashboard" class="my-achievements-btn" style="margin-top:-10px; background: #6b7280;">&larr; Back to Dashboard</a>
                </div>
                
                <div class="feedback-grid">
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&context=dept_file&sub_type=Admin Files" class="feedback-card">
                        <div class="card-content"><h3>Admin Files</h3></div>
                    </a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&context=dept_file&sub_type=Faculty Files" class="feedback-card">
                        <div class="card-content"><h3>Faculty Files</h3></div>
                    </a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&context=dept_file&sub_type=Student Related Files" class="feedback-card">
                        <div class="card-content"><h3>Student Related Files</h3></div>
                    </a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&context=dept_file&sub_type=Exam Section Files" class="feedback-card">
                        <div class="card-content"><h3>Exam Section Files</h3></div>
                    </a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&context=dept_file&sub_type=Student Activities Files" class="feedback-card">
                        <div class="card-content"><h3>Student Activities Files</h3></div>
                    </a>
                </div>

            <?php else: ?>

                <div class="header-title">
                    <h1>Department Management</h1>
                </div>
                
                <div class="feedback-grid">
                    <?php if ($is_reviewer): ?>
                    <a href="<?= BASE_URL ?>/public/index.php?route=dashboard&view=achievements" class="feedback-card">
                        <div class="card-content"><h3>Department Achievements</h3></div>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($is_dept_manager): ?>
                    <a href="<?= BASE_URL ?>/public/index.php?route=dashboard&view=dept_files" class="feedback-card">
                        <div class="card-content"><h3>Department Files List</h3></div>
                    </a>
                    <?php endif; ?>
                </div>

            <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($pending_approvals) && $is_reviewer && empty($_GET['view'])): ?>
        <!-- PENDING APPROVALS -->
        <div class="header-title" style="margin-top: 2rem;">
            <h1>Pending My Approval <span style="background:#ef4444; color:white; padding:2px 8px; border-radius:999px; font-size:0.8rem; margin-left:10px;"><?= count($pending_approvals) ?></span></h1>
        </div>
        
        <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x: auto; margin-bottom: 2rem;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #e5e7eb; background: #f8fafc;">
                        <th style="padding: 12px 16px; font-weight: 600; color: #475569;">File Title</th>
                        <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Type</th>
                        <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Uploaded By</th>
                        <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Date</th>
                        <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_approvals as $doc): ?>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 12px 16px; color: #2563eb; font-weight: 500;">
                            <?= htmlspecialchars($doc['title']) ?>
                        </td>
                        <td style="padding: 12px 16px; color: #64748b;">
                            <?= htmlspecialchars($doc['type_label'] ?? $doc['type_id']) ?>
                        </td>
                        <td style="padding: 12px 16px; color: #64748b;">
                            <?= htmlspecialchars($doc['uploader_name'] ?? 'Unknown') ?>
                        </td>
                        <td style="padding: 12px 16px; color: #64748b;">
                            <?= date('M d, Y', strtotime($doc['created_at'])) ?>
                        </td>
                        <td style="padding: 12px 16px;">
                            <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&id=<?= $doc['doc_id'] ?>&mode=review" style="background: #10b981; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.875rem;">Review</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>


        <?php if (!$is_faculty && !$is_reviewer && !$is_dept_manager): ?>
            <div class="header-title">
                <h1>Quick Actions</h1>
            </div>
            <div class="feedback-grid">
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/list" class="feedback-card">
                    <div class="card-content"><h3>My Documents</h3></div>
                </a>
            </div>
        <?php endif; ?>

        <?php if (empty($_GET['view'])): ?>
        <?php if ($is_faculty || !empty($recent_uploads)): ?>
        <!-- RECENT UPLOADS & PENDING ACTIONS -->
        <div class="header-title" style="margin-top: 2rem;">
            <h1>Recent Uploads & Pending Actions</h1>
        </div>
        
        <?php if (!empty($recent_uploads)): ?>
        <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x: auto; margin-bottom: 2rem;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #e5e7eb; background: #f8fafc;">
                        <th style="padding: 12px 16px; font-weight: 600; color: #475569;">File Title</th>
                        <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Type</th>
                        <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Date</th>
                        <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Status</th>
                        <th style="padding: 12px 16px; font-weight: 600; color: #475569;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_uploads as $doc): ?>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 12px 16px; color: #2563eb; font-weight: 500;">
                            <?= htmlspecialchars($doc['title']) ?>
                        </td>
                        <td style="padding: 12px 16px; color: #64748b;">
                            <?= htmlspecialchars($doc['type_label']) ?>
                        </td>
                        <td style="padding: 12px 16px; color: #64748b;">
                            <?= date('M d, Y', strtotime($doc['created_at'])) ?>
                        </td>
                        <td style="padding: 12px 16px;">
                            <?php
                            $status_lower = strtolower($doc['status']);
                            if ($status_lower === 'accepted') {
                                $bg = '#dcfce7'; $color = '#166534'; $label = 'Accepted';
                            } elseif ($status_lower === 'rejected') {
                                $bg = '#fee2e2'; $color = '#991b1b'; $label = 'Rejected';
                            } else {
                                $bg = '#fef9c3'; $color = '#854d0e'; $label = 'Pending ' . $doc['step_label'];
                            }
                            ?>
                            <span style="background: <?= $bg ?>; color: <?= $color ?>; padding: 4px 10px; border-radius: 9999px; font-size: 0.85rem; font-weight: 500;">
                                <?= htmlspecialchars($label) ?>
                            </span>
                        </td>
                        <td style="padding: 12px 16px;">
                            <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&id=<?= $doc['doc_id'] ?>" style="background: #3b82f6; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.875rem;">View</a>
                            <?php if ($status_lower === 'pending' || $status_lower === 'rejected'): ?>
                            <a href="<?= BASE_URL ?>/public/index.php?route=documents/edit&id=<?= $doc['doc_id'] ?>" style="background: #eab308; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.875rem; margin-left: 5px;">Update</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p style="color: #64748b; margin-bottom: 2rem;">No recent uploads found.</p>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Debug Info -->
        <div style="margin-top: 2rem; padding: 1rem; background: #fff3cd; border-radius: 8px;">
            <strong>Debug:</strong> 
            Your current Role ID is: <code><?= $role_id ?></code> (<?= htmlspecialchars($role_name_display) ?>). 
            If you expect to see Department Files, your Role ID must be 3 (HOD) or 5 (Dept Coordinator). 
            If it is 4 (Faculty), you will only see Achievements.
        </div>
        <?php endif; ?>
        
    </div>
</main>

<script>
function activateRole(userRoleId, baseUrl, csrfToken) {
    fetch(baseUrl + '/public/index.php?route=auth/select_role', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'user_role_id=' + encodeURIComponent(userRoleId) + '&csrf_token=' + encodeURIComponent(csrfToken)
    }).then(response => {
        if(response.redirected) {
            window.location.href = response.url;
        } else {
            window.location.href = baseUrl + '/public/index.php?route=dashboard';
        }
    });
}
</script>
</body>
</html>
