<?php
include_once HEADER;
?>
<link rel="stylesheet" href="<?= CSS_PATH ?>/portal.css">
<link rel="stylesheet" href="<?= CSS_PATH ?>/dashboard.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    :root { --primary: #0b353d; --secondary: #0e454f; --accent: #17a2b8; --light: #f4f7f6; --text: #333; }
    * { box-sizing: border-box; }
    
    .dept-hero {
        background: linear-gradient(rgba(14, 69, 79, 0.9), rgba(8, 40, 46, 0.9)), url('https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
        padding: 60px 40px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 40px;
    }
    .dept-hero-left { flex: 1; text-align: left; }
    .dept-hero-left .tagline { color: var(--accent); font-weight: bold; letter-spacing: 2px; font-size: 0.85rem; display: block; margin-bottom: 15px; text-transform: uppercase; }
    .dept-hero-left h1 { font-size: 2.8rem; margin: 0 0 10px 0; line-height: 1.2; }
    .dept-hero-left p { font-size: 1.1rem; color: #cbd5e1; max-width: 600px; }

    .dept-hero-right { flex: 1; display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
    
    .hero-stat-card {
        background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); 
        border-radius: 8px; padding: 20px; text-align: center; color: #fff; transition: all 0.3s;
    }
    .hero-stat-card:hover { transform: translateY(-5px); background: rgba(23, 162, 184, 0.15); border-color: rgba(23, 162, 184, 0.5); }
    .hero-stat-card i { font-size: 1.8rem; color: var(--accent); margin-bottom: 10px; }
    .hero-stat-card h3 { font-size: 2rem; margin: 0; }
    .hero-stat-card p { margin: 5px 0 0 0; font-size: 0.75rem; letter-spacing: 1px; color: #cbd5e1; text-transform: uppercase; }

    .dept-content { max-width: 1400px; margin: 40px auto; padding: 0 40px; display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 30px; }
    @media(max-width: 900px) { .dept-content { grid-template-columns: 1fr; } }
    
    .sidebar-col { display: flex; flex-direction: column; gap: 30px; }
    
    .faculty-list, .chart-card { background: white; border-radius: 8px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
    .faculty-list h3, .chart-card h3 { font-size: 1.2rem; color: var(--primary); margin: 0 0 15px 0; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9; font-weight: 700; }
    
    .faculty-item { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9; transition: transform 0.2s; }
    .faculty-item:hover { transform: translateX(5px); }
    .faculty-item:last-child { border-bottom: none; }
    .faculty-avatar { width: 45px; height: 45px; border-radius: 50%; background: #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: bold; margin-right: 15px; }
    .faculty-info { flex: 1; }
    .faculty-name { font-weight: 700; color: var(--primary); font-size: 0.95rem; }
    .faculty-role { font-size: 0.8rem; color: #64748b; margin-top: 3px; }

    .docs-section { background: white; border-radius: 8px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
    .docs-section h3 { font-size: 1.2rem; color: var(--primary); margin: 0 0 20px 0; font-weight: 700; display: flex; align-items: center; gap: 10px; }
    
    .public-table { width: 100%; border-collapse: collapse; }
    .public-table th, .public-table td { padding: 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
    .public-table th { background: #f8fafc; font-weight: 700; color: var(--secondary); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .public-table td { font-size: 0.95rem; color: #334155; }
    .public-table tr:hover { background: #f8fafc; }
    .type-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; background: rgba(23, 162, 184, 0.1); color: var(--accent); border: 1px solid rgba(23, 162, 184, 0.2); }
    
    .download-btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: #fff; color: var(--primary); border-radius: 20px; text-decoration: none; font-size: 0.85rem; font-weight: bold; transition: all 0.3s; border: 1px solid #cbd5e1; }
    .download-btn:hover { background: var(--accent); color: white; border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3); }

    .chart-wrapper { width: 100%; height: 250px; position: relative; }
</style>

<div class="dept-hero">
    <div class="dept-hero-left">
        <span class="tagline">Department Public Profile</span>
        <h1><?= htmlspecialchars($dept_name) ?></h1>
        <p>Explore the complete research and publication repository for the Department of <?= htmlspecialchars($dept_name) ?>.</p>
    </div>
    
    <div class="dept-hero-right">
        <div class="hero-stat-card">
            <i class="fas fa-file-alt"></i>
            <div class="stat-number"><h3><?= $papers_count ?></h3></div>
            <p>Published Papers</p>
        </div>
        <div class="hero-stat-card">
            <i class="fas fa-lightbulb"></i>
            <div class="stat-number"><h3><?= $patents_count ?></h3></div>
            <p>Patents & IPR</p>
        </div>
        <div class="hero-stat-card">
            <i class="fas fa-chalkboard-teacher"></i>
            <div class="stat-number"><h3><?= $fdps_count ?></h3></div>
            <p>FDPs & Workshops</p>
        </div>
    </div>
</div>

<div class="dept-content">
    <div class="sidebar-col">
        <div class="chart-card">
            <h3><i class="fas fa-chart-pie" style="color: var(--accent);"></i> Output Distribution</h3>
            <div class="chart-wrapper"><canvas id="deptChart"></canvas></div>
        </div>

        <div class="faculty-list">
            <h3><i class="fas fa-trophy" style="color: #cfa85c;"></i> Top Faculty Podium</h3>
        <?php if (empty($top_faculty)): ?>
            <p style="color:#64748b; font-size:0.9rem;">No data available.</p>
        <?php else: ?>
            <?php foreach ($top_faculty as $index => $fac): ?>
                <div class="faculty-item" style="<?= $index === 0 ? 'background: rgba(207, 168, 92, 0.1); border-left: 4px solid #cfa85c;' : '' ?>">
                    <div class="faculty-avatar" style="<?= $index === 0 ? 'background: #cfa85c; color: white;' : '' ?>">
                        #<?= $index + 1 ?>
                    </div>
                    <div class="faculty-info">
                        <div class="faculty-name"><?= htmlspecialchars($fac['name']) ?></div>
                        <div class="faculty-role">Total Points: <strong><?= $fac['points'] ?></strong></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
    </div>
    
    <div style="display: flex; flex-direction: column; gap: 30px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div class="docs-section">
                <h3><i class="fas fa-spider" style="color: var(--accent);"></i> Top Faculty Focus</h3>
                <div class="chart-wrapper"><canvas id="radarChart"></canvas></div>
            </div>
            <div class="docs-section">
                <h3><i class="fas fa-chart-bar" style="color: var(--accent);"></i> Department Contributions</h3>
                <div class="chart-wrapper"><canvas id="barChart"></canvas></div>
            </div>
        </div>

    <div class="docs-section">
        <h3>Public Documents</h3>
        <?php if (empty($public_docs)): ?>
            <div class="empty-state" style="padding: 40px; text-align: center; color: #64748b;">
                <p>No public documents have been accepted for this department yet.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="public-table">
                    <thead>
                        <tr>
                            <th>Document Title</th>
                            <th>Type</th>
                            <th>Author / Uploader</th>
                            <th>Academic Year</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($public_docs as $doc): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($doc['original_file_name'] ?: 'Untitled Document') ?></strong>
                                </td>
                                <td><span class="type-badge"><?= htmlspecialchars($doc['type_name'] ?: 'Document') ?></span></td>
                                <td><?= htmlspecialchars($doc['uploader_name'] ?: 'System') ?></td>
                                <td><?= htmlspecialchars($doc['year_name'] ?: 'N/A') ?></td>
                                <td>
                                    <?php if ($doc['file_path'] !== '#'): ?>
                                        <a href="<?= BASE_URL ?>/public/index.php?route=public/download&doc_id=<?= $doc['doc_id'] ?>" target="_blank" class="download-btn">
                                            View File
                                        </a>
                                    <?php else: ?>
                                        <span style="color:#94a3b8; font-size:0.85rem;">No File</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$type_counts = [];
foreach($public_docs as $doc) {
    $t = $doc['type_name'] ?: 'Other';
    if(!isset($type_counts[$t])) $type_counts[$t] = 0;
    $type_counts[$t]++;
}

$bar_data = $faculty_performance;
usort($bar_data, function($a, $b) {
    return $b['total'] <=> $a['total'];
});
$fac_names = array_column($bar_data, 'name');
$fac_totals = array_column($bar_data, 'total');

$top_3_names = array_column($top_faculty, 'name');
$top_3_papers = array_column($top_faculty, 'papers');
$top_3_patents = array_column($top_faculty, 'patents');
$top_3_fdps = array_column($top_faculty, 'fdps');
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const commonOptions = { responsive: true, maintainAspectRatio: false };
    
    // Output Distribution (Doughnut)
    const ctxDept = document.getElementById('deptChart');
    if(ctxDept) {
        new Chart(ctxDept, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_keys($type_counts)) ?>,
                datasets: [{ data: <?= json_encode(array_values($type_counts)) ?>, backgroundColor: ['#0b353d', '#17a2b8', '#cfa85c', '#475569', '#64748b', '#94a3b8'], borderWidth: 0 }]
            },
            options: { ...commonOptions, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10, font: {size: 11} } } } }
        });
    }

    // Faculty Contribution (Bar)
    const ctxBar = document.getElementById('barChart');
    if(ctxBar) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?= json_encode($fac_names) ?>,
                datasets: [{ label: 'Total Outputs', data: <?= json_encode($fac_totals) ?>, backgroundColor: '#17a2b8', borderRadius: 4 }]
            },
            options: { ...commonOptions, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { ticks: { maxRotation: 90, minRotation: 45 } } } }
        });
    }

    // Top Faculty Focus (Radar)
    const ctxRadar = document.getElementById('radarChart');
    if(ctxRadar) {
        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: ['Papers', 'Patents', 'FDPs'],
                datasets: [
                    <?php foreach($top_faculty as $index => $fac): ?>
                    {
                        label: '<?= addslashes($fac['name']) ?>',
                        data: [<?= $fac['papers'] ?>, <?= $fac['patents'] ?>, <?= $fac['fdps'] ?>],
                        backgroundColor: '<?= $index === 0 ? "rgba(207, 168, 92, 0.2)" : ($index === 1 ? "rgba(23, 162, 184, 0.2)" : "rgba(11, 53, 61, 0.2)") ?>',
                        borderColor: '<?= $index === 0 ? "#cfa85c" : ($index === 1 ? "#17a2b8" : "#0b353d") ?>',
                        pointBackgroundColor: '<?= $index === 0 ? "#cfa85c" : ($index === 1 ? "#17a2b8" : "#0b353d") ?>',
                    },
                    <?php endforeach; ?>
                ]
            },
            options: { ...commonOptions, scales: { r: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }
});
</script>
