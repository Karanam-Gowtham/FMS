<?php
require_once '../includes/session.php';
require_once '../includes/connection.php';

if (!isset($_SESSION['h_username']) && !isset($_SESSION['admin'])) {
    die("You need to log in to view this page.");
}
$dept = isset($_GET['dept']) ? $_GET['dept'] : (isset($_SESSION['dept']) ? $_SESSION['dept'] : '');

// ─── Fetch achievement counts per faculty ─────────────────────────────────────
function queryCount($conn, $table, $dept_col, $dept, $user_col = 'username')
{
    $sql = "SELECT `$user_col` as username, COUNT(*) as cnt FROM `$table` WHERE `$dept_col` = ? AND status = 'Accepted' GROUP BY `$user_col` ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dept);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$fdps_att = queryCount($conn, 'fdps_tab', 'branch', $dept);
$fdps_org = queryCount($conn, 'fdps_org_tab', 'branch', $dept);
$published = queryCount($conn, 'published_tab', 'branch', $dept);
$conference = queryCount($conn, 'conference_tab', 'branch', $dept);
$patents = queryCount($conn, 'patents_table', 'branch', $dept, 'Username');

function buildMap($rows)
{
    $m = [];
    foreach ($rows as $r)
        $m[$r['username']] = (int) $r['cnt'];
    return $m;
}

$mapA = buildMap($fdps_att);
$mapO = buildMap($fdps_org);
$mapP = buildMap($published);
$mapC = buildMap($conference);
$mapPat = buildMap($patents);

// All unique faculty
$allFaculty = array_values(array_unique(array_merge(
    array_keys($mapA),
    array_keys($mapO),
    array_keys($mapP),
    array_keys($mapC),
    array_keys($mapPat)
)));

sort($allFaculty);

// Totals
$totA = array_sum($mapA);
$totO = array_sum($mapO);
$totP = array_sum($mapP);
$totC = array_sum($mapC);
$totPat = array_sum($mapPat);
$grandTotal = $totA + $totO + $totP + $totC + $totPat;

// Per-faculty arrays for charts
$fa = [];
$fo = [];
$fp = [];
$fc = [];
$fpat = [];
foreach ($allFaculty as $f) {
    $fa[] = $mapA[$f] ?? 0;
    $fo[] = $mapO[$f] ?? 0;
    $fp[] = $mapP[$f] ?? 0;
    $fc[] = $mapC[$f] ?? 0;
    $fpat[] = $mapPat[$f] ?? 0;
}

$facultyJson = json_encode($allFaculty, JSON_UNESCAPED_UNICODE);
$faJson = json_encode($fa);
$foJson = json_encode($fo);
$fpJson = json_encode($fp);
$fcJson = json_encode($fc);
$fpatJson = json_encode($fpat);

// Short labels for display
$shortLabels = array_map(fn($u) => explode('@', $u)[0], $allFaculty);
$shortJson = json_encode($shortLabels, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f8;
            color: #1e293b;
            line-height: 1.5;
            padding-top: 66px;
        }

        /* ── Nav ───────────────────────────────────── */
        .navbar {
            background-color: white;
            font-size: 1.1rem;
            position: sticky;
            top: 66px;
            z-index: 98;
            width: 100%;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .nav-container {
            width: 100%;
            padding: 0 1.5rem;
        }

        .nav-items {
            display: flex;
            align-items: center;
            height: 3.5rem;
        }

        .sid {
            color: rgb(48, 30, 138);
            font-weight: 500;
        }

        .main-a {
            color: rgb(138, 30, 113);
            font-weight: 500;
        }

        .main-a:hover {
            color: rgb(182, 64, 211);
        }

        .home-icon {
            color: rgb(30, 58, 138);
            transition: color .2s;
        }

        .home-icon:hover {
            color: rgb(29, 78, 216);
        }

        #sp {
            color: blue;
        }

        /* ── Analytics Panel ────────────────────────── */
        .analytics-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            padding: 3rem 2rem 3rem;
            position: relative;
            overflow: hidden;
        }

        .analytics-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 20% 50%, rgba(59, 130, 246, .15) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(139, 92, 246, .12) 0%, transparent 50%);
            pointer-events: none;
        }

        .analytics-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .analytics-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.02em;
        }

        .analytics-header .dept-badge {
            background: rgba(59, 130, 246, .3);
            border: 1px solid rgba(59, 130, 246, .5);
            color: #93c5fd;
            padding: .25rem .75rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: .05em;
        }

        .analytics-header .pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, .4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, .4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }

        /* ── KPI Cards ──────────────────────────────── */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .kpi-card {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 16px;
            padding: 1.25rem 1rem;
            backdrop-filter: blur(10px);
            cursor: pointer;
            transition: transform .2s, background .2s, box-shadow .2s;
            position: relative;
            overflow: hidden;
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 16px 16px 0 0;
        }

        .kpi-card.att::before {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }

        .kpi-card.org::before {
            background: linear-gradient(90deg, #8b5cf6, #a78bfa);
        }

        .kpi-card.pub::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .kpi-card.conf::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

        .kpi-card.pat::before {
            background: linear-gradient(90deg, #ef4444, #f87171);
        }

        .kpi-card.total::before {
            background: linear-gradient(90deg, #06b6d4, #67e8f9);
        }

        .kpi-card:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, .12);
            box-shadow: 0 20px 40px rgba(0, 0, 0, .3);
        }

        .kpi-label {
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .5);
            margin-bottom: .4rem;
        }

        .kpi-value {
            font-size: 2.4rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            margin-bottom: .25rem;
            counter-reset: count attr(data-target);
        }

        .kpi-sub {
            font-size: .72rem;
            color: rgba(255, 255, 255, .4);
        }

        .kpi-icon {
            position: absolute;
            right: 1rem;
            top: 1rem;
            font-size: 1.8rem;
            opacity: .15;
        }

        /* ── Charts Grid ─────────────────────────────── */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        @media (max-width: 900px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-card {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 16px;
            padding: 1.25rem;
            backdrop-filter: blur(10px);
            transition: box-shadow .2s;
        }

        .chart-card:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, .25);
        }

        .chart-card.wide {
            grid-column: span 2;
        }

        @media (max-width: 900px) {
            .chart-card.wide {
                grid-column: span 1;
            }
        }

        .chart-title {
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .6);
            margin-bottom: 1rem;
        }

        .chart-wrap {
            position: relative;
        }

        .chart-wrap canvas {
            width: 100% !important;
        }

        /* ── Tooltip overlay ─────────────────────────── */
        .bi-tooltip {
            position: fixed;
            background: rgba(15, 23, 42, .97);
            border: 1px solid rgba(99, 102, 241, .5);
            border-radius: 12px;
            padding: .9rem 1.1rem;
            color: #e2e8f0;
            font-size: .8rem;
            pointer-events: none;
            z-index: 9999;
            display: none;
            min-width: 220px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
        }

        .bi-tooltip .tt-title {
            font-weight: 700;
            font-size: .9rem;
            color: #fff;
            margin-bottom: .5rem;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
            padding-bottom: .4rem;
        }

        .bi-tooltip .tt-row {
            display: flex;
            justify-content: space-between;
            gap: 1.5rem;
            padding: .15rem 0;
            color: rgba(255, 255, 255, .8);
        }

        .bi-tooltip .tt-val {
            font-weight: 700;
            color: #60a5fa;
        }

        .bi-tooltip .tt-bar {
            height: 4px;
            border-radius: 2px;
            margin-top: .4rem;
            background: rgba(255, 255, 255, .1);
        }

        .bi-tooltip .tt-bar-fill {
            height: 100%;
            border-radius: 2px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        }

        /* ── Main page content ────────────────────────── */
        .main-content {
            padding: 2rem 1rem;
        }

        .container {
            max-width: 80rem;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .header {
            margin-bottom: 1.5rem;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #111827;
        }

        .feedback-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.5rem;
            margin-bottom: 50px;
        }

        @media (min-width: 768px) {
            .feedback-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .feedback-card {
            text-decoration: none;
            display: block;
            transition: transform .2s;
            background: #fff;
            border: none;
            padding: 0;
            cursor: pointer;
            width: 100%;
            text-align: left;
            border-radius: .5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, .1), 0 1px 2px 0 rgba(0, 0, 0, .06);
        }

        .feedback-card:hover {
            transform: scale(1.05);
        }

        .card-content {
            background: linear-gradient(to right, rgb(30, 64, 175), rgb(37, 99, 235));
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .card-content h3 {
            color: white;
            font-size: 1.25rem;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <?php include 'header_hod.php'; ?>

    <!-- Global Tooltip -->
    <div class="bi-tooltip" id="biTooltip"></div>

    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span>&nbsp; >> &nbsp; </span><span class="sid"><a
                        href="../admin/admins.php?dept=<?php echo urlencode($dept); ?>"
                        class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span id="sp">&nbsp; >> &nbsp;</span><span class="main"> <a href="#" class="main-a"> HOD </a></span>
            </div>
        </div>
    </nav>

    <!-- ═══ ANALYTICS DASHBOARD ══════════════════════════════════════════════ -->
    <div class="analytics-section">
        <div class="analytics-header">
            <div class="pulse"></div>
            <h2>📊 Department Achievement Analytics</h2>
            <span class="dept-badge"><?= htmlspecialchars($dept) ?></span>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-row">
            <div class="kpi-card att" onclick="highlightCategory(0)">
                <div class="kpi-icon">🎓</div>
                <div class="kpi-label">FDPs Attended</div>
                <div class="kpi-value" data-target="<?= $totA ?>">0</div>
                <div class="kpi-sub">Accepted records</div>
            </div>
            <div class="kpi-card org" onclick="highlightCategory(1)">
                <div class="kpi-icon">🏫</div>
                <div class="kpi-label">FDPs Organised</div>
                <div class="kpi-value" data-target="<?= $totO ?>">0</div>
                <div class="kpi-sub">Accepted records</div>
            </div>
            <div class="kpi-card pub" onclick="highlightCategory(2)">
                <div class="kpi-icon">📄</div>
                <div class="kpi-label">Published Papers</div>
                <div class="kpi-value" data-target="<?= $totP ?>">0</div>
                <div class="kpi-sub">Accepted records</div>
            </div>
            <div class="kpi-card conf" onclick="highlightCategory(3)">
                <div class="kpi-icon">🎤</div>
                <div class="kpi-label">Conferences</div>
                <div class="kpi-value" data-target="<?= $totC ?>">0</div>
                <div class="kpi-sub">Accepted records</div>
            </div>
            <div class="kpi-card pat" onclick="highlightCategory(4)">
                <div class="kpi-icon">💡</div>
                <div class="kpi-label">Patents</div>
                <div class="kpi-value" data-target="<?= $totPat ?>">0</div>
                <div class="kpi-sub">Accepted records</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <!-- Donut: Category distribution -->
            <div class="chart-card">
                <div class="chart-title">📌 Category Distribution</div>
                <div class="chart-wrap" style="height:240px;">
                    <canvas id="donutChart"></canvas>
                </div>
            </div>

            <!-- Radar: Faculty performance -->
            <div class="chart-card">
                <div class="chart-title">🔷 Category Balance</div>
                <div class="chart-wrap" style="height:240px;">
                    <canvas id="radarChart"></canvas>
                </div>
            </div>

            <!-- Stacked bar: Per faculty -->
            <div class="chart-card wide">
                <div class="chart-title">👤 Per-Faculty Achievement Breakdown</div>
                <div class="chart-wrap" style="height:280px;">
                    <canvas id="stackedBar"></canvas>
                </div>
            </div>

            <!-- Horizontal bar: Total per category -->
            <div class="chart-card wide">
                <div class="chart-title">📊 Total by Category</div>
                <div class="chart-wrap" style="height:220px;">
                    <canvas id="hBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- ═══ END ANALYTICS ══════════════════════════════════════════════════════ -->


    <main class="main-content">
        <div class="container">
            <!-- Achievements Section -->
            <div class="header">
                <h1>Achievements</h1>
            </div>
            <div class="feedback-grid">
                <a href="hod_down_fdps_files.php?action_name=fdps" class="feedback-card">
                    <div class="card-content">
                        <h3>View FDPS Attended Files</h3>
                    </div>
                </a>
                <a href="hod_down_fdps_files.php?action_name=fdps_org" class="feedback-card">
                    <div class="card-content">
                        <h3>View FDPS Organized Files</h3>
                    </div>
                </a>
                <a href="hod_down_fdps_files.php?action_name=published" class="feedback-card">
                    <div class="card-content">
                        <h3>View Papers Published Files</h3>
                    </div>
                </a>
                <a href="hod_down_fdps_files.php?action_name=conference" class="feedback-card">
                    <div class="card-content">
                        <h3>View Conferences Published Files</h3>
                    </div>
                </a>
                <a href="hod_down_fdps_files.php?action_name=patents" class="feedback-card">
                    <div class="card-content">
                        <h3>View Patents Files</h3>
                    </div>
                </a>
            </div>

            <!-- Department Files Section -->
            <div class="header">
                <h1>Department Files</h1>
            </div>
            <div class="feedback-grid">
                <a href="hod_down_dept_files.php?event=admin" class="feedback-card">
                    <div class="card-content">
                        <h3>Admin Files</h3>
                    </div>
                </a>
                <a href="hod_down_dept_files.php?event=faculty" class="feedback-card">
                    <div class="card-content">
                        <h3>Faculty Files</h3>
                    </div>
                </a>
                <a href="hod_down_dept_files.php?event=student" class="feedback-card">
                    <div class="card-content">
                        <h3>Student Related Files</h3>
                    </div>
                </a>
                <a href="hod_down_dept_files.php?event=exam" class="feedback-card">
                    <div class="card-content">
                        <h3>Exam Section Files</h3>
                    </div>
                </a>
                <a href="hod_down_dept_files.php?event=calendar" class="feedback-card">
                    <div class="card-content">
                        <h3>Academic Calendar</h3>
                    </div>
                </a>
                <a href="hod_down_st_act_files.php" class="feedback-card">
                    <div class="card-content">
                        <h3>Student Activity Files</h3>
                    </div>
                </a>
            </div>

            <!-- Meeting Minutes Section -->
            <div class="header">
                <h1>Meeting Minutes</h1>
            </div>
            <div class="feedback-grid">
                <a href="hod_down_dept_files.php?event=Dept Meeting Minutes" class="feedback-card">
                    <div class="card-content">
                        <h3>Department Meeting Minutes</h3>
                    </div>
                </a>
                <a href="hod_down_dept_files.php?event=AMC Meeting Minutes" class="feedback-card">
                    <div class="card-content">
                        <h3>AMC Meeting Minutes</h3>
                    </div>
                </a>
                <a href="hod_down_dept_files.php?event=Board Of Studies" class="feedback-card">
                    <div class="card-content">
                        <h3>Board Of Studies</h3>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <script>
        // ─── Data from PHP ────────────────────────────────────────────────────────
        const faculty = <?= $facultyJson ?>;
        const shortNames = <?= $shortJson ?>;
        const dataAtt = <?= $faJson ?>;
        const dataOrg = <?= $foJson ?>;
        const dataPub = <?= $fpJson ?>;
        const dataConf = <?= $fcJson ?>;
        const dataPat = <?= $fpatJson ?>;
        const totals = [<?= $totA ?>, <?= $totO ?>, <?= $totP ?>, <?= $totC ?>, <?= $totPat ?>];
        const grandTotal = <?= $grandTotal ?>;

        const CATS = ['FDPs Attended', 'FDPs Organised', 'Published Papers', 'Conferences', 'Patents'];
        const COLORS = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444'];
        const COLORS_T = ['rgba(59,130,246,.7)', 'rgba(139,92,246,.7)', 'rgba(16,185,129,.7)', 'rgba(245,158,11,.7)', 'rgba(239,68,68,.7)'];

        const tooltip = document.getElementById('biTooltip');

        function showTooltip(e, html) {
            tooltip.innerHTML = html;
            tooltip.style.display = 'block';
            posTooltip(e);
        }
        function posTooltip(e) {
            let x = e.clientX + 16, y = e.clientY - 10;
            if (x + 240 > window.innerWidth) x = e.clientX - 256;
            tooltip.style.left = x + 'px';
            tooltip.style.top = y + 'px';
        }
        function hideTooltip() { tooltip.style.display = 'none'; }

        document.addEventListener('mousemove', posTooltip);

        // ─── KPI Counter animation ────────────────────────────────────────────────
        document.querySelectorAll('.kpi-value').forEach(el => {
            const target = parseInt(el.dataset.target) || 0;
            let current = 0, step = Math.max(1, Math.ceil(target / 60));
            const timer = setInterval(() => {
                current = Math.min(current + step, target);
                el.textContent = current;
                if (current >= target) clearInterval(timer);
            }, 16);
        });

        // KPI card hover tooltips
        const kpiData = [
            { label: 'FDPs Attended', total: <?= $totA ?>, datasets: dataAtt },
            { label: 'FDPs Organised', total: <?= $totO ?>, datasets: dataOrg },
            { label: 'Published Papers', total: <?= $totP ?>, datasets: dataPub },
            { label: 'Conferences', total: <?= $totC ?>, datasets: dataConf },
            { label: 'Patents', total: <?= $totPat ?>, datasets: dataPat },
        ];
        document.querySelectorAll('.kpi-card').forEach((card, i) => {
            card.addEventListener('mouseenter', e => {
                const d = kpiData[i];
                let rows = '';
                if (d.datasets) {
                    const sorted = faculty.map((f, j) => ({ name: f, val: d.datasets[j] }))
                        .filter(x => x.val > 0)
                        .sort((a, b) => b.val - a.val)
                        .slice(0, 6);
                    rows = sorted.map(x => {
                        const pct = d.total ? Math.round(x.val / d.total * 100) : 0;
                        return `<div class="tt-row"><span>${x.name.split('@')[0]}</span><span class="tt-val">${x.val} (${pct}%)</span></div>`;
                    }).join('');
                }
                showTooltip(e, `<div class="tt-title">📌 ${d.label}</div>${rows}<div class="tt-bar"><div class="tt-bar-fill" style="width:100%"></div></div><div style="margin-top:.5rem;color:rgba(255,255,255,.5);font-size:.7rem;">Click to highlight in charts</div>`);
            });
            card.addEventListener('mouseleave', hideTooltip);
        });

        // ─── Chart.js defaults ───────────────────────────────────────────────────
        Chart.defaults.color = 'rgba(255,255,255,.6)';
        Chart.defaults.font.family = 'Inter';

        const tooltipPlugin = {
            plugins: {
                datalabels: { display: false },
                tooltip: {
                    enabled: false,
                    external: ({ chart, tooltip: t }) => {
                        if (t.opacity === 0) { hideTooltip(); return; }
                        const items = t.dataPoints || [];
                        if (!items.length) return;
                        const dp = items[0];
                        const cat = dp.dataset.label || CATS[dp.dataIndex] || '';
                        const val = dp.raw;
                        const idx = dp.dataIndex;
                        const e = {
                            clientX: t.caretX + chart.canvas.getBoundingClientRect().left,
                            clientY: t.caretY + chart.canvas.getBoundingClientRect().top
                        };
                        let html = `<div class="tt-title">${cat}</div>`;
                        if (dp.dataset._catIdx !== undefined) {
                            const ci = dp.dataset._catIdx;
                            html += `<div class="tt-row"><span>Faculty</span><span class="tt-val">${faculty[idx]?.split('@')[0] ?? idx}</span></div>`;
                            html += `<div class="tt-row"><span>Count</span><span class="tt-val">${val}</span></div>`;
                            const total = [dataAtt, dataOrg, dataPub, dataConf, dataPat][ci].reduce((a, b) => a + b, 0);
                            const pct = total ? Math.round(val / total * 100) : 0;
                            html += `<div class="tt-row"><span>Share</span><span class="tt-val">${pct}%</span></div>`;
                            html += `<div class="tt-bar"><div class="tt-bar-fill" style="width:${pct}%"></div></div>`;
                        } else {
                            html += `<div class="tt-row"><span>Count</span><span class="tt-val">${val}</span></div>`;
                            const pct = grandTotal ? Math.round(val / grandTotal * 100) : 0;
                            html += `<div class="tt-row"><span>Share of Total</span><span class="tt-val">${pct}%</span></div>`;
                            html += `<div class="tt-bar"><div class="tt-bar-fill" style="width:${pct}%"></div></div>`;
                        }
                        showTooltip(e, html);
                    }
                }
            }
        };

        // ─── 1. Donut Chart ───────────────────────────────────────────────────────
        const donutCtx = document.getElementById('donutChart').getContext('2d');
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: CATS,
                datasets: [{
                    data: totals,
                    backgroundColor: COLORS,
                    borderColor: 'rgba(0,0,0,.3)',
                    borderWidth: 2,
                    hoverOffset: 12,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '65%',
                ...tooltipPlugin,
                plugins: {
                    ...tooltipPlugin.plugins,
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12, padding: 12, font: { size: 11 } }
                    },
                    datalabels: {
                        display: ctx => ctx.dataset.data[ctx.dataIndex] > 0,
                        color: '#fff', font: { weight: 600, size: 11 },
                        formatter: (val, ctx) => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            return total ? Math.round(val / total * 100) + '%' : '';
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // ─── 2. Radar Chart ───────────────────────────────────────────────────────
        const radarCtx = document.getElementById('radarChart').getContext('2d');
        // Normalise totals 0-100 for radar
        const maxT = Math.max(...totals, 1);
        new Chart(radarCtx, {
            type: 'radar',
            data: {
                labels: CATS.map(c => c.split(' ').slice(0, 2).join('\n')),
                datasets: [{
                    label: '<?= htmlspecialchars($dept, ENT_QUOTES) ?>',
                    data: totals.map(t => Math.round(t / maxT * 100)),
                    backgroundColor: 'rgba(99,102,241,.25)',
                    borderColor: '#818cf8',
                    pointBackgroundColor: COLORS,
                    pointRadius: 5, borderWidth: 2,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                ...tooltipPlugin,
                plugins: { ...tooltipPlugin.plugins, legend: { display: false }, datalabels: { display: false } },
                scales: {
                    r: {
                        beginAtZero: true, max: 100,
                        grid: { color: 'rgba(255,255,255,.1)' },
                        angleLines: { color: 'rgba(255,255,255,.1)' },
                        ticks: { display: false }
                    }
                }
            }
        });

        // ─── 3. Stacked Bar (per faculty) ────────────────────────────────────────
        const sbCtx = document.getElementById('stackedBar').getContext('2d');
        const sbDatasets = [dataAtt, dataOrg, dataPub, dataConf, dataPat].map((d, i) => ({
            label: CATS[i],
            data: d,
            backgroundColor: COLORS_T[i],
            borderColor: COLORS[i],
            borderWidth: 1,
            borderRadius: 3,
            _catIdx: i,
        }));
        new Chart(sbCtx, {
            type: 'bar',
            data: { labels: shortNames, datasets: sbDatasets },
            options: {
                responsive: true, maintainAspectRatio: false,
                ...tooltipPlugin,
                plugins: {
                    ...tooltipPlugin.plugins, datalabels: { display: false },
                    legend: { position: 'top', labels: { boxWidth: 10, padding: 8, font: { size: 10 } } }
                },
                scales: {
                    x: { stacked: true, grid: { color: 'rgba(255,255,255,.05)' }, ticks: { font: { size: 10 } } },
                    y: { stacked: true, grid: { color: 'rgba(255,255,255,.07)' }, ticks: { precision: 0 } }
                }
            }
        });

        // ─── 4. Horizontal Bar ───────────────────────────────────────────────────
        const hbCtx = document.getElementById('hBarChart').getContext('2d');
        new Chart(hbCtx, {
            type: 'bar',
            data: {
                labels: CATS,
                datasets: [{
                    label: 'Total',
                    data: totals,
                    backgroundColor: COLORS_T,
                    borderColor: COLORS,
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true, maintainAspectRatio: false,
                ...tooltipPlugin,
                plugins: {
                    ...tooltipPlugin.plugins,
                    datalabels: {
                        display: true, anchor: 'end', align: 'end',
                        color: '#fff', font: { weight: 700, size: 12 },
                        formatter: v => v || ''
                    },
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { color: 'rgba(255,255,255,.07)' }, ticks: { precision: 0 } },
                    y: { grid: { display: false } }
                }
            },
            plugins: [ChartDataLabels]
        });

        // ─── Highlight category in stacked bar ───────────────────────────────────
        function highlightCategory(catIdx) {
            const chart = Chart.getChart('stackedBar');
            if (!chart) return;
            chart.data.datasets.forEach((ds, i) => {
                ds.backgroundColor = i === catIdx ? COLORS[i] : 'rgba(255,255,255,.08)';
                ds.borderWidth = i === catIdx ? 2 : 0;
            });
            chart.update();
            setTimeout(() => {
                chart.data.datasets.forEach((ds, i) => {
                    ds.backgroundColor = COLORS_T[i];
                    ds.borderWidth = 1;
                });
                chart.update();
            }, 1800);
        }
    </script>
</body>

</html>