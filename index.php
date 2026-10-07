<?php
require_once __DIR__ . '/core/bootstrap.php';
$isLoggedIn = auth_is_logged_in();
global $conn;

// Initial Data for dropdowns
$depts = [];
$d_res = $conn->query("SELECT dept_id, dept_name FROM departments WHERE is_academic = 1 ORDER BY dept_name");
if($d_res) { while($row = $d_res->fetch_assoc()) { $depts[] = $row; } }

$years = [];
$y_res = $conn->query("SELECT year_label as yr FROM academic_years ORDER BY year_label DESC");
if($y_res) { while($row = $y_res->fetch_assoc()) { $years[] = $row['yr']; } }

$doc_types = [];
$whitelist = "'journal', 'conference', 'patent', 'fdp_attended', 'fdp_organised', 'conf_organised'";
$dt_res = $conn->query("SELECT type_code, label FROM document_types WHERE type_code IN ($whitelist) ORDER BY label");
if($dt_res) { while($row = $dt_res->fetch_assoc()) { $doc_types[] = $row; } }

// 1. Initial Hero Stats (All Time, All Depts)
$stats = ['researchers' => 0, 'depts' => 0, 'patents' => 0, 'pubs' => 0, 'fdps' => 0, 'total_docs' => 0];
$s_res = $conn->query("SELECT count(*) as cnt FROM users WHERE status='active' AND user_id IN (SELECT user_id FROM user_roles WHERE role_id IN (SELECT role_id FROM roles WHERE role_name IN ('Faculty', 'HOD', 'R&D Dean')))");
if($s_res) $stats['researchers'] = $s_res->fetch_assoc()['cnt'];

$stats['depts'] = count($depts);

$p_res = $conn->query("SELECT count(*) as cnt FROM documents d JOIN document_types dt ON d.type_id=dt.type_id WHERE d.status='accepted' AND dt.type_code = 'patent'");
if($p_res) $stats['patents'] = $p_res->fetch_assoc()['cnt'];

$pub_res = $conn->query("SELECT count(*) as cnt FROM documents d JOIN document_types dt ON d.type_id=dt.type_id WHERE d.status='accepted' AND dt.type_code IN ('journal', 'conference')");
if($pub_res) $stats['pubs'] = $pub_res->fetch_assoc()['cnt'];

$f_res = $conn->query("SELECT count(*) as cnt FROM documents d JOIN document_types dt ON d.type_id=dt.type_id WHERE d.status='accepted' AND dt.type_code LIKE '%fdp%'");
if($f_res) $stats['fdps'] = $f_res->fetch_assoc()['cnt'];

$all_res = $conn->query("SELECT count(*) as cnt FROM documents d JOIN document_types dt ON d.type_id=dt.type_id WHERE d.status='accepted' AND dt.type_code IN ($whitelist)");
if($all_res) $stats['total_docs'] = $all_res->fetch_assoc()['cnt'];

// 2. Initial Top Researchers
$top_researchers = [];
$tr_res = $conn->query("
    SELECT u.user_id, u.full_name, u.email, d.dept_name, COUNT(doc.doc_id) as total_pubs
    FROM users u
    JOIN user_roles ur ON u.user_id = ur.user_id
    JOIN departments d ON ur.dept_id = d.dept_id
    JOIN documents doc ON u.user_id = doc.uploaded_by
    JOIN document_types dt ON doc.type_id = dt.type_id
    WHERE u.status = 'active' AND doc.status = 'accepted' AND d.is_academic = 1
    AND dt.type_code IN ($whitelist)
    GROUP BY u.user_id
    ORDER BY total_pubs DESC
    LIMIT 4
");
if($tr_res) { while($row = $tr_res->fetch_assoc()) { $top_researchers[] = $row; } }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GMR Institute of Technology - Research Analytics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --primary: #0b353d; --secondary: #0e454f; --accent: #17a2b8; --light: #f4f7f6; --text: #333; }
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 0; background: #fff; color: var(--text); overflow-x: hidden; }
        
        /* Utility Header */
        .utility-bar { background: var(--primary); color: #fff; padding: 8px 40px; display: flex; justify-content: flex-end; font-size: 0.85rem; }
        .utility-bar a { color: #fff; text-decoration: none; margin-left: 20px; transition: color 0.2s; font-weight: bold; }
        .utility-bar a:hover { color: var(--accent); }

        /* Main Navigation */
        .main-nav { background: #fff; padding: 15px 40px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .nav-brand { font-size: 1.1rem; font-weight: bold; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        .nav-links a { margin-left: 25px; text-decoration: none; color: #555; font-weight: 500; font-size: 0.95rem; }
        .nav-links a.active { color: var(--primary); border-bottom: 2px solid var(--accent); padding-bottom: 5px; }
        
        /* Hero Section */
        .hero { background: linear-gradient(rgba(14, 69, 79, 0.9), rgba(8, 40, 46, 0.9)), url('https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover; padding: 60px 40px; display: flex; gap: 40px; align-items: center; }
        .hero-left { flex: 1; color: #fff; }
        .hero-left .tagline { color: var(--accent); font-weight: bold; letter-spacing: 2px; font-size: 0.85rem; display: block; margin-bottom: 15px; }
        .hero-left h1 { font-size: 2.8rem; margin: 0 0 20px 0; line-height: 1.2; }
        .hero-left p { font-size: 1.1rem; color: #cbd5e1; max-width: 600px; margin-bottom: 30px; }
        
        /* Amazon-style Layout */
        .amazon-layout { display: grid; grid-template-columns: 280px minmax(0, 1fr); gap: 30px; max-width: 1400px; margin: 0 auto; padding: 40px; }
        .amazon-main { min-width: 0; }
        .amazon-sidebar { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); height: fit-content; position: sticky; top: 90px; }
        .amazon-sidebar h3 { font-size: 1.2rem; color: var(--primary); margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }
        .facet-group { margin-bottom: 25px; }
        .facet-group h4 { font-size: 0.9rem; color: var(--secondary); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .facet-label { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 0.9rem; color: #475569; cursor: pointer; transition: color 0.2s; }
        .facet-label:hover { color: var(--primary); }
        .facet-label input[type="checkbox"] { accent-color: var(--accent); width: 16px; height: 16px; cursor: pointer; }

        .hero-right { flex: 1.2; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        .hero-stat-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 20px; text-align: center; color: #fff; cursor: pointer; transition: all 0.3s; position: relative; overflow: hidden; }
        .hero-stat-card:hover { transform: translateY(-5px); background: rgba(23, 162, 184, 0.15); border-color: rgba(23, 162, 184, 0.5); }
        .hero-stat-card.active { background: rgba(23, 162, 184, 0.25); border-color: var(--accent); transform: scale(1.02); }
        .hero-stat-card.active::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 4px; background: var(--accent); }
        .hero-stat-card i { font-size: 1.8rem; color: var(--accent); margin-bottom: 10px; transition: 0.3s; }
        .hero-stat-card.active i { transform: scale(1.2); }
        .hero-stat-card h3 { font-size: 1.8rem; margin: 0; }
        .hero-stat-card p { margin: 5px 0 0 0; font-size: 0.75rem; letter-spacing: 1px; color: #cbd5e1; }

        /* General Sections */
        .section { padding: 60px 40px; }
        .section.light { background: var(--light); }
        .section-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .section-header h2 { margin: 0; font-size: 1.8rem; color: var(--primary); }
        
        /* Analytics Area */
        .analytics-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 30px; }
        .chart-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); position: relative; transition: transform 0.3s; display: flex; flex-direction: column; }
        .chart-card:hover { transform: translateY(-5px); }
        .chart-header h3 { margin: 0 0 5px 0; color: var(--primary); font-size: 1.2rem; font-weight: 700; }
        .chart-header p { margin: 0 0 20px 0; color: #64748b; font-size: 0.85rem; }
        .chart-wrapper { flex: 1; height: 350px; width: 100%; position: relative; }

        /* Researchers Grid */
        .researchers-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .r-card { background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; transition: transform 0.2s; }
        .r-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px rgba(0,0,0,0.05); }
        .r-photo { height: 120px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: #94a3b8; }
        .r-info { padding: 20px; }
        .r-name { font-weight: bold; color: var(--primary); font-size: 1.1rem; margin-bottom: 5px; }
        .r-dept { font-size: 0.85rem; color: #64748b; margin-bottom: 15px; height: 35px; overflow: hidden; }
        .r-footer { border-top: 1px solid #e2e8f0; padding: 15px 20px; display: flex; justify-content: space-between; background: #f8fafc; }
        .r-btn { color: var(--primary); text-decoration: none; font-size: 0.85rem; font-weight: bold; }

        /* Loader */
        #loader { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11, 53, 61, 0.8); backdrop-filter: blur(5px); z-index: 1000; display: none; align-items: center; justify-content: center; flex-direction: column; color: var(--accent); }
        .spinner { width: 50px; height: 50px; border: 4px solid rgba(207, 168, 92, 0.2); border-top-color: var(--accent); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px; }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        /* Footer */
        .footer { background: #08242a; color: #94a3b8; text-align: center; padding: 20px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div id="loader">
        <div class="spinner"></div>
        <div style="font-weight: bold; letter-spacing: 2px;">SYNCING ANALYTICS...</div>
    </div>

    <!-- Utility Bar -->
    <div class="utility-bar">
        <div>
            <?php if (!$isLoggedIn): ?>
                <a href="<?= BASE_URL ?>/public/index.php?route=auth/login"><i class="fas fa-lock"></i> Login</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/public/index.php?route=dashboard"><i class="fas fa-columns"></i> Go to Dashboard</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Nav -->
    <nav class="main-nav">
        <div class="nav-brand">
            <i class="fas fa-university" style="font-size: 1.5rem;"></i>
            <div>
                GMR Institute of Technology<br>
                <span style="font-size: 0.75rem; color: #64748b; font-weight: normal;">Research & Analytics Portal</span>
            </div>
        </div>
        <div class="nav-links">
            <a href="#" class="active">Analytics Dashboard</a>
            <a href="#profiles">Top Profiles</a>
        </div>
    </nav>

    <!-- Hero Section with Master Filters -->
    <section class="hero">
        <div class="hero-left">
            <span class="tagline">— DYNAMIC RESEARCH INTELLIGENCE</span>
            <h1>Research & Analytics Portal</h1>
            <p>Select an academic year, isolate a department, or click a metric card to instantly drill down into specific research outputs.</p>
            <div style="margin-top: 15px; font-size: 0.9rem; color: #cbd5e1;"><i class="fas fa-arrow-down"></i> Scroll down to explore the dynamic analytics dashboard.</div>
        </div>
        <div class="hero-right">
            <div class="hero-stat-card active" data-category="all">
                <i class="fas fa-globe"></i>
                <h3 id="val-total"><?= $stats['total_docs'] ?></h3><p>TOTAL DOCUMENTS</p>
            </div>
            <div class="hero-stat-card" data-category="pubs">
                <i class="fas fa-book"></i>
                <h3 id="val-pubs"><?= $stats['pubs'] ?></h3><p>PUBLICATIONS</p>
            </div>
            <div class="hero-stat-card" data-category="patents">
                <i class="fas fa-lightbulb"></i>
                <h3 id="val-patents"><?= $stats['patents'] ?></h3><p>PATENTS</p>
            </div>
            <div class="hero-stat-card" data-category="fdps">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3 id="val-fdps"><?= $stats['fdps'] ?></h3><p>FDPs ORGANIZED</p>
            </div>
            <div class="hero-stat-card" style="cursor: default;">
                <i class="fas fa-users"></i>
                <h3 id="val-researchers"><?= $stats['researchers'] ?></h3><p>ACTIVE RESEARCHERS</p>
            </div>
            <div class="hero-stat-card" style="cursor: default;">
                <i class="fas fa-sitemap"></i>
                <h3><?= $stats['depts'] ?></h3><p>DEPARTMENTS</p>
            </div>
        </div>
    </section>

    <!-- Interactive Analytics -->
    <section class="section light" id="analytics" style="padding: 20px 0;">
        <div class="amazon-layout">
            <aside class="amazon-sidebar">
                <h3><i class="fas fa-filter"></i> Refine Results</h3>
                
                <div class="facet-group">
                    <h4>Analytics Focus</h4>
                    <label class="facet-label" style="font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 10px;"><input type="checkbox" class="cb-select-all" data-target=".cb-type"> Select All</label>
                    <?php foreach($doc_types as $dt): ?>
                        <label class="facet-label"><input type="checkbox" class="cb-type" value="<?= $dt['type_code'] ?>"> <?= htmlspecialchars($dt['label']) ?></label>
                    <?php endforeach; ?>
                </div>

                <div class="facet-group">
                    <h4>Academic Year</h4>
                    <label class="facet-label" style="font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 10px;"><input type="checkbox" class="cb-select-all" data-target=".cb-year"> Select All</label>
                    <?php foreach($years as $yr): ?>
                        <label class="facet-label"><input type="checkbox" class="cb-year" value="<?= htmlspecialchars($yr) ?>"> <?= htmlspecialchars($yr) ?></label>
                    <?php endforeach; ?>
                </div>

                <div class="facet-group">
                    <h4>Department</h4>
                    <label class="facet-label" style="font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 10px;"><input type="checkbox" class="cb-select-all" data-target=".cb-dept"> Select All</label>
                    <?php foreach($depts as $d): ?>
                        <label class="facet-label"><input type="checkbox" class="cb-dept" value="<?= $d['dept_id'] ?>"> <?= htmlspecialchars($d['dept_name']) ?></label>
                    <?php endforeach; ?>
                </div>
            </aside>

            <div class="amazon-main">
                <div class="section-header">
                    <h2><i class="fas fa-chart-line" style="color: var(--accent);"></i> Dynamic Visualization</h2>
                    <span style="color: #64748b; font-weight: 500;" id="current-view-label">Currently Viewing: Filtered Results</span>
                </div>
                <div class="analytics-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-area" style="color: var(--accent);"></i> Institutional Trajectory</h3>
                    <p>Timeline of scholarly outputs based on your active filters</p>
                </div>
                <div class="chart-wrapper"><canvas id="timelineChart"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <h3 id="dist-title"><i class="fas fa-chart-pie" style="color: var(--accent);"></i> Structural Breakdown</h3>
                    <p>Distribution analysis across the institution</p>
                </div>
                <div class="chart-wrapper"><canvas id="distributionChart"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-spider" style="color: var(--accent);"></i> Departmental Radar Focus</h3>
                    <p>Multi-dimensional comparison of current focus areas</p>
                </div>
                <div class="chart-wrapper"><canvas id="radarChart"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-circle-notch" style="color: var(--accent);"></i> Proportional Impact Map</h3>
                    <p>Polar area visualization of density</p>
                </div>
                <div class="chart-wrapper"><canvas id="polarChart"></canvas></div>
            </div>
        </div>
            </div> <!-- End amazon-main -->
        </div> <!-- End amazon-layout -->
    </section>

    <!-- Top Researchers (Static display for initial load) -->
    <section class="section" id="profiles">
        <div class="section-header">
            <h2>Institution Top Profiles</h2>
        </div>
        <div class="researchers-grid">
            <?php foreach($top_researchers as $r): ?>
            <div class="r-card">
                <div class="r-photo"><i class="fas fa-user-circle"></i></div>
                <div class="r-info">
                    <div class="r-name"><?= htmlspecialchars($r['full_name']) ?></div>
                    <div class="r-dept"><?= htmlspecialchars($r['dept_name']) ?></div>
                    <div style="font-weight: bold; color: var(--primary); font-size: 0.85rem;"><i class="fas fa-book"></i> <?= $r['total_pubs'] ?> Contributions</div>
                </div>
                <div class="r-footer">
                    <span style="font-size: 0.8rem; color:#64748b;">ID: <?= $r['user_id'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="footer">Powered by FMS (Internal Document Tracking System)</div>

    <script>
        // Global State
        let state = { year: 'all', dept_id: 'all', category: 'all', type: 'all' };
        
        // Chart Instances
        let timelineChartInst = null;
        let distChartInst = null;
        let radarChartInst = null;
        let polarChartInst = null;

        const cbYears = document.querySelectorAll('.cb-year');
        const cbDepts = document.querySelectorAll('.cb-dept');
        const cbTypes = document.querySelectorAll('.cb-type');
        const metricCards = document.querySelectorAll('.hero-stat-card[data-category]');
        const loader = document.getElementById('loader');
        
        function animateValue(obj, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.innerHTML = Math.floor(progress * (end - start) + start).toLocaleString();
                if (progress < 1) window.requestAnimationFrame(step);
            };
            window.requestAnimationFrame(step);
        }

        async function fetchDashboardData() {
            loader.style.display = 'flex';
            try {
                const url = `<?= BASE_URL ?>/public/api/public_stats.php?year=${state.year}&dept_id=${state.dept_id}&category=${state.category}&type=${state.type}`;
                const response = await fetch(url);
                const json = await response.json();
                if(json.status === 'success') {
                    updateUI(json.data);
                }
            } catch (err) { console.error(err); } 
            finally { setTimeout(() => { loader.style.display = 'none'; }, 300); }
        }

        function updateUI(data) {
            // Update Label
            document.getElementById('current-view-label').innerText = `Currently Viewing: Filtered Results`;

            // Update Metrics
            const mData = data.stats;
            animateValue(document.getElementById('val-total'), 0, mData.total_docs, 800);
            animateValue(document.getElementById('val-pubs'), 0, mData.pubs, 800);
            animateValue(document.getElementById('val-patents'), 0, mData.patents, 800);
            animateValue(document.getElementById('val-fdps'), 0, mData.fdps, 800);
            animateValue(document.getElementById('val-researchers'), 0, mData.researchers, 800);

            // Timeline Chart (Smooth Area)
            const tLabels = Object.keys(data.timeline); // Use academic year string directly
            const tValues = Object.values(data.timeline);
            
            if (timelineChartInst) timelineChartInst.destroy();
            const ctxT = document.getElementById('timelineChart').getContext('2d');
            
            let grad = ctxT.createLinearGradient(0, 0, 0, 400);
            grad.addColorStop(0, 'rgba(23, 162, 184, 0.6)'); // Accent cyan
            grad.addColorStop(1, 'rgba(23, 162, 184, 0.0)');

            timelineChartInst = new Chart(ctxT, {
                type: 'line',
                data: {
                    labels: tLabels,
                    datasets: [{
                        label: 'Activities', data: tValues,
                        borderColor: '#17a2b8', backgroundColor: grad,
                        borderWidth: 3, fill: true, tension: 0.4,
                        pointBackgroundColor: '#fff', pointBorderColor: '#17a2b8', pointBorderWidth: 3, pointRadius: 5, pointHoverRadius: 7
                    }]
                },
                options: { 
                    responsive: true, maintainAspectRatio: false, 
                    plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false, backgroundColor: 'rgba(11, 53, 61, 0.9)', titleFont: { size: 14 }, bodyFont: { size: 14, weight: 'bold' }, padding: 12, cornerRadius: 8 } }, 
                    scales: { 
                        y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#e2e8f0' } },
                        x: { grid: { display: false } }
                    } 
                }
            });

            // Distribution Chart (Bar/Doughnut)
            document.getElementById('dist-title').innerHTML = `<i class="fas fa-chart-pie" style="color: var(--accent);"></i> ` + (state.dept_id !== 'all' ? 'Documents by Category' : 'Documents by Department');
            if (distChartInst) distChartInst.destroy();
            
            const chartType = state.dept_id !== 'all' ? 'doughnut' : 'bar';
            const bgColors = ['#17a2b8', '#0b353d', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#ec4899', '#14b8a6', '#f97316'];
            
            const distData = state.dept_id !== 'all' ? data.category_distribution : data.dept_distribution;

            distChartInst = new Chart(document.getElementById('distributionChart').getContext('2d'), {
                type: chartType,
                data: {
                    labels: Object.keys(distData),
                    datasets: [{
                        data: Object.values(distData),
                        backgroundColor: chartType === 'doughnut' ? bgColors : '#0b353d',
                        borderRadius: chartType === 'bar' ? 6 : 0,
                        borderWidth: chartType === 'doughnut' ? 2 : 0,
                        borderColor: '#ffffff'
                    }]
                },
                options: { 
                    responsive: true, maintainAspectRatio: false, 
                    plugins: { legend: { display: chartType === 'doughnut', position: 'right', labels: { boxWidth: 12, padding: 15, font: { size: 11 } } } },
                    scales: chartType === 'bar' ? { x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 45 } }, y: { beginAtZero: true, grid: { borderDash: [5, 5] } } } : {}
                }
            });

            // Radar Chart (Category Focus)
            if (radarChartInst) radarChartInst.destroy();
            radarChartInst = new Chart(document.getElementById('radarChart').getContext('2d'), {
                type: 'radar',
                data: {
                    labels: Object.keys(data.category_distribution),
                    datasets: [{
                        label: 'Document Focus',
                        data: Object.values(data.category_distribution),
                        backgroundColor: 'rgba(23, 162, 184, 0.2)',
                        borderColor: '#17a2b8',
                        pointBackgroundColor: '#0b353d',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#17a2b8',
                        borderWidth: 2
                    }]
                },
                options: { 
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { r: { min: 0, ticks: { stepSize: 1, precision: 0 }, angleLines: { display: true, color: '#e2e8f0' }, grid: { color: '#e2e8f0' }, pointLabels: { font: { size: 11, family: 'Inter, sans-serif' }, color: '#64748b' } } }
                }
            });

            // Polar Area Chart (Departmental Spread)
            if (polarChartInst) polarChartInst.destroy();
            polarChartInst = new Chart(document.getElementById('polarChart').getContext('2d'), {
                type: 'polarArea',
                data: {
                    labels: Object.keys(data.dept_distribution),
                    datasets: [{
                        data: Object.values(data.dept_distribution),
                        backgroundColor: bgColors.map(c => c + 'CC'), // Add transparency
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: { 
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'right', labels: { boxWidth: 10, padding: 10, font: { size: 10 } } } },
                    scales: { r: { ticks: { display: false }, grid: { color: '#e2e8f0' } } }
                }
            });
        }

        // Listeners
        function syncFiltersAndFetch() {
            state.year = Array.from(cbYears).filter(cb => cb.checked).map(cb => cb.value).join(',') || 'all';
            state.dept_id = Array.from(cbDepts).filter(cb => cb.checked).map(cb => cb.value).join(',') || 'all';
            state.type = Array.from(cbTypes).filter(cb => cb.checked).map(cb => cb.value).join(',') || 'all';
            fetchDashboardData();
        }

        const selectAllBoxes = document.querySelectorAll('.cb-select-all');
        selectAllBoxes.forEach(sa => {
            sa.addEventListener('change', (e) => {
                const targets = document.querySelectorAll(e.target.dataset.target);
                targets.forEach(t => t.checked = e.target.checked);
                syncFiltersAndFetch();
            });
        });

        function enhancedSync(e) {
            const group = e.target.closest('.facet-group');
            if(group) {
                const sa = group.querySelector('.cb-select-all');
                if(sa) {
                    const targets = document.querySelectorAll(sa.dataset.target);
                    const allChecked = Array.from(targets).every(t => t.checked);
                    const someChecked = Array.from(targets).some(t => t.checked);
                    sa.checked = allChecked && targets.length > 0;
                    sa.indeterminate = someChecked && !allChecked;
                }
            }
            syncFiltersAndFetch();
        }

        cbYears.forEach(cb => cb.addEventListener('change', enhancedSync));
        cbDepts.forEach(cb => cb.addEventListener('change', enhancedSync));
        cbTypes.forEach(cb => cb.addEventListener('change', enhancedSync));
        
        metricCards.forEach(card => {
            card.addEventListener('click', () => {
                metricCards.forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                state.category = card.dataset.category;
                fetchDashboardData();
            });
        });

        // Initialize
        fetchDashboardData();
    </script>
</body>
</html>