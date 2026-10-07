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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        <?php if (!empty($user_roles) && count($user_roles) > 1): ?>
        <div style="margin-bottom: 2rem;">
            <p style="font-weight: 600; margin-bottom: 10px; color: #475569;">Switch Role:</p>
            <?php foreach ($user_roles as $ur): ?>
                <?php if (!$active_role || (int)$ur['user_role_id'] !== (int)$active_role['user_role_id']): ?>
                    <button class="switch-role-btn" onclick="activateRole(<?= $ur['user_role_id'] ?>, '<?= BASE_URL ?>', '<?= csrfToken() ?>')">
                        Switch to <?= htmlspecialchars($ur['role_name']) ?> (<?= htmlspecialchars($ur['dept_name']) ?>)
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php
        $role_id = $active_role ? (int)$active_role['role_id'] : 0;
        $is_faculty = ($role_id === ROLE_FACULTY);
        $is_reviewer = in_array($role_id, [ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR]);
        $is_dept_manager = in_array($role_id, [ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_JUNIOR_ASSISTANT, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR]);
        ?>
        
        <?php if ($is_faculty): ?>
            <!-- IRINS-Style Premium Faculty Profile & Dashboard -->
            
            <!-- 1. HERO PROFILE SECTION -->
            <div style="background: linear-gradient(135deg, #0b1c3b 0%, #1e3a8a 100%); color: white; border-radius: 12px; padding: 2.5rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); position: relative; overflow: hidden;">
                <!-- Decorative background elements -->
                <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -80px; right: 10%; width: 300px; height: 300px; background: rgba(255,255,255,0.03); border-radius: 50%;"></div>
                
                <div style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: center; position: relative; z-index: 2;">
                    <!-- Avatar -->
                    <div style="width: 130px; height: 130px; border-radius: 50%; background: white; padding: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                        <?php if (!empty($profile_photo)): ?>
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($profile_photo) ?>" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" alt="Profile Picture">
                        <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($auth['full_name'] ?? 'Faculty') ?>&background=random&size=120" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" alt="Profile Picture">
                        <?php endif; ?>
                    </div>
                    
                    <!-- Info -->
                    <div style="flex-grow: 1;">
                        <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0 0 0.5rem 0; letter-spacing: -0.5px;"><?= htmlspecialchars($auth['full_name'] ?? 'Faculty Name') ?></h1>
                        <h3 style="font-size: 1.1rem; font-weight: 400; margin: 0 0 0.2rem 0; color: #93c5fd;"><i class="fas fa-chalkboard-teacher" style="margin-right: 8px;"></i><?= htmlspecialchars($role_name_display) ?></h3>
                        <p style="font-size: 1rem; margin: 0 0 1.2rem 0; color: #cbd5e1;"><i class="fas fa-building" style="margin-right: 8px;"></i>Department of <?= htmlspecialchars($dept_name_display) ?></p>
                        
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <a href="<?= BASE_URL ?>/public/index.php?route=documents/list" style="background: rgba(255,255,255,0.15); color: white; padding: 0.5rem 1.2rem; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 0.9rem; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                                <i class="fas fa-list" style="margin-right: 8px;"></i> View Publications & Achievements
                            </a>
                            <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&context=dept_file" style="background: rgba(255,255,255,0.15); color: white; padding: 0.5rem 1.2rem; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 0.9rem; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                                <i class="fas fa-folder-open" style="margin-right: 8px;"></i> My Department Files
                            </a>
                        </div>
                    </div>
                    
                    <!-- Hero Stats -->
                    <div style="display: flex; gap: 1.5rem; background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 12px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.05);">
                        <div style="text-align: center;">
                            <div style="font-size: 2.5rem; font-weight: 800; color: #60a5fa; line-height: 1;"><?= array_sum($stats) ?></div>
                            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 0.5rem;">Total Works</div>
                        </div>
                        <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
                        <div style="text-align: center;">
                            <div style="font-size: 2.5rem; font-weight: 800; color: #34d399; line-height: 1;"><?= $stats['accepted'] ?? 0 ?></div>
                            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 0.5rem;">Verified</div>
                        </div>
                        <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
                        <div style="text-align: center;">
                            <div style="font-size: 2.5rem; font-weight: 800; color: #fbbf24; line-height: 1;"><?= $stats['pending'] ?? 0 ?></div>
                            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 0.5rem;">Pending</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. MAIN DASHBOARD CONTENT -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
                
                <!-- Chart 1: Categories -->
                <div style="background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0;">
                    <div style="background: #f8fafc; padding: 1.2rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
                        <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700; display: flex; align-items: center;"><i class="fas fa-chart-pie me-2" style="color: #3b82f6;"></i> Publications & Achievements by Category</h3>
                    </div>
                    <div style="padding: 1.5rem; height: 350px; display: flex; justify-content: center; align-items: center;">
                        <?php if (empty($chart_categories)): ?>
                            <div style="text-align: center; color: #94a2b8;">
                                <i class="fas fa-folder-open mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                <p>No data available to visualize.</p>
                            </div>
                        <?php else: ?>
                            <canvas id="facultyCategoryChart"></canvas>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Chart 2: Timeline -->
                <div style="background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0;">
                    <div style="background: #f8fafc; padding: 1.2rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
                        <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700; display: flex; align-items: center;"><i class="fas fa-chart-bar me-2" style="color: #3b82f6;"></i> Research Output Timeline</h3>
                    </div>
                    <div style="padding: 1.5rem; height: 350px;">
                        <?php if (empty($chart_timeline)): ?>
                            <div style="text-align: center; color: #94a2b8; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                <i class="fas fa-chart-line mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                <p>No data available to visualize.</p>
                            </div>
                        <?php else: ?>
                            <canvas id="facultyTimelineChart"></canvas>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Chart.js and FontAwesome -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Modern color palette for IRINS style
                    const colors = ['#1e40af', '#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#64748b'];

                    // Category Chart
                    const catData = <?= json_encode($chart_categories ?? []) ?>;
                    if (Object.keys(catData).length > 0) {
                        const ctxCat = document.getElementById('facultyCategoryChart').getContext('2d');
                        new Chart(ctxCat, {
                            type: 'doughnut',
                            data: {
                                labels: Object.keys(catData),
                                datasets: [{
                                    data: Object.values(catData),
                                    backgroundColor: colors,
                                    borderWidth: 2,
                                    borderColor: '#ffffff',
                                    hoverOffset: 8
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { 
                                        position: 'right', 
                                        labels: { 
                                            font: { family: "'Segoe UI', sans-serif", size: 13 },
                                            padding: 20,
                                            usePointStyle: true,
                                            pointStyle: 'circle'
                                        } 
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                        titleFont: { size: 14 },
                                        bodyFont: { size: 14 },
                                        padding: 12,
                                        cornerRadius: 8
                                    }
                                },
                                cutout: '65%',
                                animation: { animateScale: true, animateRotate: true }
                            }
                        });
                    }

                    // Timeline Chart
                    const timeData = <?= json_encode($chart_timeline ?? []) ?>;
                    if (Object.keys(timeData).length > 0) {
                        const ctxTime = document.getElementById('facultyTimelineChart').getContext('2d');
                        
                        // Create gradient for bars
                        let gradient = ctxTime.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, '#3b82f6');
                        gradient.addColorStop(1, '#1e3a8a');

                        new Chart(ctxTime, {
                            type: 'bar',
                            data: {
                                labels: Object.keys(timeData).map(d => {
                                    const [y, m] = d.split('-');
                                    const date = new Date(y, m-1);
                                    return date.toLocaleString('default', { month: 'short', year: 'numeric' });
                                }),
                                datasets: [{
                                    label: 'Documents Uploaded',
                                    data: Object.values(timeData),
                                    backgroundColor: gradient,
                                    borderRadius: 6,
                                    barThickness: 'flex',
                                    maxBarThickness: 40
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                        padding: 12,
                                        cornerRadius: 8
                                    }
                                },
                                scales: {
                                    y: { 
                                        beginAtZero: true, 
                                        ticks: { stepSize: 1, font: { family: "'Segoe UI', sans-serif" } },
                                        grid: { color: '#f1f5f9', drawBorder: false }
                                    },
                                    x: {
                                        grid: { display: false, drawBorder: false },
                                        ticks: { font: { family: "'Segoe UI', sans-serif" } }
                                    }
                                }
                            }
                        });
                    }
                });
            </script>

            <!-- 3. SUBMIT NEW ACHIEVEMENTS (Quick Actions) -->
            <div class="header-title" style="margin-top: 1rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
                <h1 style="font-size: 1.4rem;">Submit New Achievement</h1>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=journal" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                    <div style="background: #eff6ff; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                        <i class="fas fa-book-open" style="color: #3b82f6; font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-weight: 600; font-size: 0.95rem;">Research Papers Published</div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=conference" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #8b5cf6; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(139, 92, 246, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                    <div style="background: #f5f3ff; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                        <i class="fas fa-users" style="color: #8b5cf6; font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-weight: 600; font-size: 0.95rem;">Conference Proceedings</div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=patent" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #f59e0b; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(245, 158, 11, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                    <div style="background: #fffbeb; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                        <i class="fas fa-certificate" style="color: #f59e0b; font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-weight: 600; font-size: 0.95rem;">Patents</div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=fdp_attended" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #10b981; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                    <div style="background: #ecfdf5; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                        <i class="fas fa-chalkboard-teacher" style="color: #10b981; font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-weight: 600; font-size: 0.95rem;">FDP Attended</div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=fdp_organised" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #ec4899; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(236, 72, 153, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                    <div style="background: #fdf2f8; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                        <i class="fas fa-bullhorn" style="color: #ec4899; font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-weight: 600; font-size: 0.95rem;">FDP Organized</div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=conf_organised" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #06b6d4; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(6, 182, 212, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                    <div style="background: #ecfeff; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                        <i class="fas fa-microphone" style="color: #06b6d4; font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-weight: 600; font-size: 0.95rem;">Conference Organized</div>
                </a>
            </div>

            <!-- 4. SUBMIT DEPARTMENT FILES (For all faculty/staff) -->
            <div class="header-title" style="margin-top: 2rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
                <h1 style="font-size: 1.4rem;">Submit Department Files</h1>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Admin Files" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #64748b; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(100, 116, 139, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                    <div style="background: #f8fafc; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                        <i class="fas fa-user-tie" style="color: #64748b; font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-weight: 600; font-size: 0.95rem;">Admin Files</div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Faculty Files" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #64748b; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(100, 116, 139, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                    <div style="background: #f8fafc; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                        <i class="fas fa-chalkboard" style="color: #64748b; font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-weight: 600; font-size: 0.95rem;">Faculty Files</div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Student Related Files" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #64748b; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(100, 116, 139, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                    <div style="background: #f8fafc; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                        <i class="fas fa-user-graduate" style="color: #64748b; font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-weight: 600; font-size: 0.95rem;">Student Related Files</div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Exam Section Files" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #64748b; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(100, 116, 139, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                    <div style="background: #f8fafc; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                        <i class="fas fa-file-alt" style="color: #64748b; font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-weight: 600; font-size: 0.95rem;">Exam Section Files</div>
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Student Activities Files" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #64748b; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(100, 116, 139, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                    <div style="background: #f8fafc; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                        <i class="fas fa-futbol" style="color: #64748b; font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-weight: 600; font-size: 0.95rem;">Student Activities Files</div>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($is_reviewer || $is_dept_manager): ?>
            <!-- HOD / REVIEWER DASHBOARD (Viewing & Approving) -->
            <?php 
            $hod_view = $_GET['view'] ?? ''; 
            ?>

            <?php if ($hod_view === 'upload_dept_files' && $is_dept_manager): ?>

                <div class="header-title d-flex justify-content-between align-items-center" style="margin-top: 1rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 1.5rem;">
                    <h1 style="font-size: 1.4rem; margin: 0;">Upload Department Files</h1>
                    <a href="<?= BASE_URL ?>/public/index.php?route=dashboard" class="my-achievements-btn" style="margin: 0; padding: 0.4rem 1rem; font-size: 0.85rem; background: #64748b;">&larr; Back to Dashboard</a>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Admin Files" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #64748b; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(100, 116, 139, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                        <div style="background: #f8fafc; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                            <i class="fas fa-user-tie" style="color: #64748b; font-size: 1.2rem;"></i>
                        </div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Admin Files</div>
                    </a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Faculty Files" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #64748b; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(100, 116, 139, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                        <div style="background: #f8fafc; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                            <i class="fas fa-chalkboard" style="color: #64748b; font-size: 1.2rem;"></i>
                        </div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Faculty Files</div>
                    </a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Student Related Files" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #64748b; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(100, 116, 139, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                        <div style="background: #f8fafc; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                            <i class="fas fa-user-graduate" style="color: #64748b; font-size: 1.2rem;"></i>
                        </div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Student Related Files</div>
                    </a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Exam Section Files" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #64748b; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(100, 116, 139, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                        <div style="background: #f8fafc; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                            <i class="fas fa-file-alt" style="color: #64748b; font-size: 1.2rem;"></i>
                        </div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Exam Section Files</div>
                    </a>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&type=dept_file&sub_type=Student Activities Files" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #64748b; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(100, 116, 139, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                        <div style="background: #f8fafc; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                            <i class="fas fa-futbol" style="color: #64748b; font-size: 1.2rem;"></i>
                        </div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Student Activities Files</div>
                    </a>
                </div>

            <?php else: ?>

                <!-- PREMIUM REVIEWER OVERVIEW -->
                <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border-radius: 12px; padding: 2.5rem; margin-top: 2rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); position: relative; overflow: hidden;">
                    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.03); border-radius: 50%;"></div>
                    <div style="position: absolute; bottom: -80px; left: 10%; width: 300px; height: 300px; background: rgba(255,255,255,0.02); border-radius: 50%;"></div>
                    
                    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; position: relative; z-index: 2; gap: 2rem;">
                        <div>
                            <h1 style="font-size: 2rem; font-weight: 800; margin: 0 0 0.5rem 0; letter-spacing: -0.5px;"><?= in_array((int)$active_role['role_id'], [ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC]) ? 'College Overview' : 'Department Overview' ?></h1>
                            <p style="font-size: 1.1rem; color: #94a3b8; margin: 0;">Analytics & Activity Summary</p>
                        </div>
                        
                        <div style="display: flex; gap: 1.5rem; background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 12px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.05);">
                            <div style="text-align: center;">
                                <div style="font-size: 2.2rem; font-weight: 800; color: #60a5fa; line-height: 1;"><?= array_sum($dept_stats) ?></div>
                                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 0.5rem;">Total Submissions</div>
                            </div>
                            <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
                            <div style="text-align: center;">
                                <div style="font-size: 2.2rem; font-weight: 800; color: #34d399; line-height: 1;"><?= $dept_stats['accepted'] ?? 0 ?></div>
                                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 0.5rem;">Approved</div>
                            </div>
                            <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
                            <div style="text-align: center;">
                                <div style="font-size: 2.2rem; font-weight: 800; color: #fbbf24; line-height: 1;"><?= $dept_stats['pending'] ?? 0 ?></div>
                                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 0.5rem;">Pending Review</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
                    <!-- Categories -->
                    <div style="background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0;">
                        <div style="background: #f8fafc; padding: 1.2rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
                            <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700; display: flex; align-items: center;"><i class="fas fa-chart-pie me-2" style="color: #6366f1;"></i> Submissions by Category</h3>
                        </div>
                        <div style="padding: 1.5rem; height: 350px; display: flex; justify-content: center; align-items: center;">
                            <?php if (empty($dept_categories)): ?>
                                <p style="color: #94a2b8;">No data available.</p>
                            <?php else: ?>
                                <canvas id="deptCategoryChart"></canvas>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div style="background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0;">
                        <div style="background: #f8fafc; padding: 1.2rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
                            <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700; display: flex; align-items: center;"><i class="fas fa-chart-bar me-2" style="color: #6366f1;"></i> Submission Timeline</h3>
                        </div>
                        <div style="padding: 1.5rem; height: 350px;">
                            <?php if (empty($dept_timeline)): ?>
                                <p style="color: #94a2b8; text-align: center; margin-top: 150px;">No data available.</p>
                            <?php else: ?>
                                <canvas id="deptTimelineChart"></canvas>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const deptColors = ['#6366f1', '#14b8a6', '#f59e0b', '#ec4899', '#8b5cf6', '#0ea5e9'];

                        // Categories Chart
                        const dCatData = <?= json_encode($dept_categories ?? []) ?>;
                        if (Object.keys(dCatData).length > 0 && document.getElementById('deptCategoryChart')) {
                            const ctxCat = document.getElementById('deptCategoryChart').getContext('2d');
                            new Chart(ctxCat, {
                                type: 'doughnut',
                                data: {
                                    labels: Object.keys(dCatData),
                                    datasets: [{
                                        data: Object.values(dCatData),
                                        backgroundColor: deptColors,
                                        borderWidth: 2, borderColor: '#fff', hoverOffset: 8
                                    }]
                                },
                                options: {
                                    responsive: true, maintainAspectRatio: false,
                                    plugins: { legend: { position: 'right', labels: { usePointStyle: true, pointStyle: 'circle' } } },
                                    cutout: '65%'
                                }
                            });
                        }

                        // Timeline Chart
                        const dTimeData = <?= json_encode($dept_timeline ?? []) ?>;
                        if (Object.keys(dTimeData).length > 0 && document.getElementById('deptTimelineChart')) {
                            const ctxTime = document.getElementById('deptTimelineChart').getContext('2d');
                            let grad = ctxTime.createLinearGradient(0, 0, 0, 400);
                            grad.addColorStop(0, '#6366f1'); grad.addColorStop(1, '#312e81');

                            new Chart(ctxTime, {
                                type: 'bar',
                                data: {
                                    labels: Object.keys(dTimeData).map(d => {
                                        const [y, m] = d.split('-');
                                        return new Date(y, m-1).toLocaleString('default', { month: 'short', year: 'numeric' });
                                    }),
                                    datasets: [{
                                        label: 'Submissions',
                                        data: Object.values(dTimeData),
                                        backgroundColor: grad,
                                        borderRadius: 6, maxBarThickness: 40
                                    }]
                                },
                                options: {
                                    responsive: true, maintainAspectRatio: false,
                                    plugins: { legend: { display: false } },
                                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { drawBorder: false } }, x: { grid: { display: false } } }
                                }
                            });
                        }
                    });
                </script>
                
                <div class="header-title" style="margin-top: 1rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 1.5rem;">
                    <h1 style="font-size: 1.4rem; margin: 0;">Management Actions</h1>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                    <?php if ($is_reviewer): ?>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #8b5cf6; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(139, 92, 246, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                        <div style="background: #f5f3ff; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                            <i class="fas fa-trophy" style="color: #8b5cf6; font-size: 1.2rem;"></i>
                        </div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Department Achievements</div>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($is_dept_manager): ?>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&context=dept_file" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #06b6d4; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(6, 182, 212, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                        <div style="background: #ecfeff; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                            <i class="fas fa-folder-open" style="color: #06b6d4; font-size: 1.2rem;"></i>
                        </div>
                        <div style="font-weight: 600; font-size: 0.95rem;">View Department Files</div>
                    </a>

                    <a href="<?= BASE_URL ?>/public/index.php?route=dashboard&view=upload_dept_files" style="background: white; padding: 1.2rem; border: 1px solid #e2e8f0; border-left: 4px solid #10b981; border-radius: 8px; text-decoration: none; color: #1e293b; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">
                        <div style="background: #ecfdf5; width: 40px; height: 40px; border-radius: 8px; display: flex; justify-content: center; align-items: center; margin-right: 1rem;">
                            <i class="fas fa-cloud-upload-alt" style="color: #10b981; font-size: 1.2rem;"></i>
                        </div>
                        <div style="font-weight: 600; font-size: 0.95rem;">Upload Department Files</div>
                    </a>
                    <?php endif; ?>
                </div>

            <?php endif; ?>
        <?php endif; ?>

        <?php if ($is_reviewer && empty($_GET['view'])): ?>
        <!-- PENDING APPROVALS -->
        <div class="header-title" style="margin-top: 2rem;">
            <h1>Pending My Approval <span style="background: <?= !empty($pending_approvals) ? '#ef4444' : '#64748b' ?>; color:white; padding:2px 8px; border-radius:999px; font-size:0.8rem; margin-left:10px;"><?= count($pending_approvals) ?></span></h1>
        </div>
        
        <?php if (!empty($pending_approvals)): ?>
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
        <?php else: ?>
        <div style="background: white; border-radius: 8px; border: 1px dashed #cbd5e1; padding: 3rem 1rem; text-align: center; margin-bottom: 2rem;">
            <i class="fas fa-check-circle" style="font-size: 3rem; color: #10b981; opacity: 0.5; margin-bottom: 1rem;"></i>
            <h3 style="color: #475569; font-size: 1.1rem; margin-bottom: 0.5rem;">All caught up!</h3>
            <p style="color: #94a3b8; margin: 0;">You have no pending documents to review right now.</p>
        </div>
        <?php endif; ?>
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
