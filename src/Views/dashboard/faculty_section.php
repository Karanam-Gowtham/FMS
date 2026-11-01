<?php
/**
 * Faculty Dashboard Section (IRINS Style Profile, Research Output & Quick Submissions)
 */
?>
<!-- 1. HERO PROFILE SECTION -->
<div style="background: linear-gradient(135deg, #0b1c3b 0%, #1e3a8a 100%); color: white; border-radius: 12px; padding: 2.5rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); position: relative; overflow: hidden;">
    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
    <div style="position: absolute; bottom: -80px; right: 10%; width: 300px; height: 300px; background: rgba(255,255,255,0.03); border-radius: 50%;"></div>
    
    <div style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: center; position: relative; z-index: 2;">
        <!-- Avatar -->
        <div style="width: 130px; height: 130px; border-radius: 50%; background: white; padding: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
            <?php if (!empty($profile_photo)): ?>
                <img src="<?= BASE_URL . '/' . htmlspecialchars($profile_photo) ?>" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" alt="Profile Picture">
            <?php else: ?>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($auth['full_name'] ?? 'Faculty') ?>&amp;background=random&amp;size=120" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" alt="Profile Picture">
            <?php endif; ?>
        </div>
        
        <!-- Info -->
        <div style="flex-grow: 1;">
            <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0 0 0.5rem 0; letter-spacing: -0.5px;"><?= htmlspecialchars($auth['full_name'] ?? 'Faculty Name') ?></h1>
            <h3 style="font-size: 1.1rem; font-weight: 400; margin: 0 0 0.2rem 0; color: #93c5fd;"><i class="fas fa-chalkboard-teacher" style="margin-right: 8px;"></i><?= htmlspecialchars($role_name_display) ?></h3>
            <p style="font-size: 1rem; margin: 0 0 1.2rem 0; color: #cbd5e1;"><i class="fas fa-building" style="margin-right: 8px;"></i>Department of <?= htmlspecialchars($dept_name_display) ?></p>
            
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/list" style="background: rgba(255,255,255,0.15); color: white; padding: 0.5rem 1.2rem; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 0.9rem; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    <i class="fas fa-list" style="margin-right: 8px;"></i> View Publications &amp; Achievements
                </a>
                <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&amp;context=dept_file" style="background: rgba(255,255,255,0.15); color: white; padding: 0.5rem 1.2rem; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 0.9rem; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
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
            <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700; display: flex; align-items: center;"><i class="fas fa-chart-pie me-2" style="color: #3b82f6;"></i> Publications &amp; Achievements by Category</h3>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colors = ['#1e40af', '#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#64748b'];

    // Category Chart
    const catData = <?= json_encode($chart_categories ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    if (Object.keys(catData).length > 0 && document.getElementById('facultyCategoryChart')) {
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
    const timeData = <?= json_encode($chart_timeline ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    if (Object.keys(timeData).length > 0 && document.getElementById('facultyTimelineChart')) {
        const ctxTime = document.getElementById('facultyTimelineChart').getContext('2d');
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
<?php include __DIR__ . '/../partials/achievement_upload_cards.php'; ?>

<!-- 4. SUBMIT DEPARTMENT FILES (For all faculty/staff) -->
<div class="header-title" style="margin-top: 2rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
    <h1 style="font-size: 1.4rem;">Submit Department Files</h1>
</div>
<?php include __DIR__ . '/../partials/dept_upload_cards.php'; ?>
