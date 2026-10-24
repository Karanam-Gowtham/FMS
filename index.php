<?php
require_once __DIR__ . '/core/bootstrap.php';
$isLoggedIn = auth_is_logged_in();
global $conn;

// Initial Data for research dropdowns
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

require_once __DIR__ . '/public/api/public_stats.php';

// 1. Initial Research Analytics Data (SSR for instant paint & zero loader lag)
$initial_research_data = get_public_stats([]);
$stats = $initial_research_data['stats'];
$stats['depts'] = count($depts);

// 2. Initial Top Researchers
$top_researchers = [];
$tr_res = $conn->query("
    SELECT u.user_id, u.full_name, u.email, d.dept_name, COUNT(DISTINCT doc.doc_id) as total_pubs
    FROM users u
    JOIN user_roles ur ON u.user_id = ur.user_id
    JOIN departments d ON ur.dept_id = d.dept_id
    JOIN documents doc ON u.user_id = doc.uploaded_by
    JOIN document_types dt ON doc.type_id = dt.type_id
    WHERE u.status = 'active' AND doc.status = 'accepted' AND d.is_academic = 1
    AND dt.type_code IN ($whitelist)
    GROUP BY u.user_id, u.full_name, u.email, d.dept_name
    ORDER BY total_pubs DESC
    LIMIT 4
");
if($tr_res) { while($row = $tr_res->fetch_assoc()) { $top_researchers[] = $row; } }

// 3. Initial Student Activity Data (SSR for instant paint)
$sa_filter_data = \App\Controllers\StudentActivityDashboardController::getFilterData();
$sa_years = $sa_filter_data['years'];
$sa_depts = $sa_filter_data['depts'];
$sa_options = $sa_filter_data['options'];
$sa_initial_stats = \App\Controllers\StudentActivityDashboardController::calculateStats([]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GMR Institute of Technology - Research Analytics</title>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="https://cdn.jsdelivr.net/npm/chart.js" crossorigin="anonymous"></script>
    <style>
        :root { --primary: #0b353d; --secondary: #0e454f; --accent: #17a2b8; --light: #f4f7f6; --text: #333; }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 0; background: #fff; color: var(--text); overflow-x: hidden; }
        
        /* Utility Header */
        .utility-bar { background: var(--primary); color: #fff; padding: 8px 40px; display: flex; justify-content: flex-end; align-items: center; font-size: 0.85rem; }
        .utility-bar a { color: #fff; text-decoration: none; margin-left: 20px; transition: color 0.2s; font-weight: bold; }
        .utility-bar a:hover { color: var(--accent); }

        /* Main Navigation */
        .main-nav { background: #fff; padding: 12px 40px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 10px rgba(0,0,0,0.06); position: sticky; top: 0; z-index: 100; }
        .nav-brand { font-size: 1.15rem; font-weight: bold; color: var(--primary); display: flex; align-items: center; gap: 12px; }
        .nav-links { display: flex; align-items: center; gap: 20px; }
        .nav-links a.nav-link-item { text-decoration: none; color: #475569; font-weight: 600; font-size: 0.95rem; padding: 6px 0; transition: color 0.2s; border-bottom: 2px solid transparent; }
        .nav-links a.nav-link-item:hover, .nav-links a.nav-link-item.active { color: var(--primary); border-bottom-color: var(--accent); }

        .nav-btn-dashboard {
            background: linear-gradient(135deg, #0b353d 0%, #17a2b8 100%);
            color: #ffffff !important;
            padding: 9px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(23, 162, 184, 0.4);
            transition: all 0.25s ease;
            text-decoration: none;
        }
        .nav-btn-dashboard:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(23, 162, 184, 0.6); }

        .nav-btn-login {
            background: #0b353d;
            color: #ffffff !important;
            padding: 9px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.25s ease;
        }
        .nav-btn-login:hover { background: #17a2b8; transform: translateY(-2px); }
        
        /* Hero Section */
        .hero { background: linear-gradient(135deg, rgba(14, 69, 79, 0.94), rgba(8, 40, 46, 0.97)), url('<?= BASE_URL ?>/assets/img/Landing.jpg') center/cover; padding: 60px 40px; display: flex; gap: 40px; align-items: center; }
        .hero-left { flex: 1; color: #fff; }
        .hero-left .tagline { color: var(--accent); font-weight: bold; letter-spacing: 2px; font-size: 0.85rem; display: block; margin-bottom: 15px; }
        .hero-left h1 { font-size: 2.8rem; margin: 0 0 20px 0; line-height: 1.2; }
        .hero-left p { font-size: 1.1rem; color: #cbd5e1; max-width: 600px; margin-bottom: 30px; }
        
        /* Layout */
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
        
        /* Analytics Grid (Reused for both Research and Student Activities) */
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
        #loader { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11, 53, 61, 0.4); backdrop-filter: blur(2px); z-index: 1000; display: none; align-items: center; justify-content: center; flex-direction: column; color: var(--accent); }
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

    <!-- Utility Bar (Duplicate Top Go to Dashboard Removed) -->
    <div class="utility-bar">
        <div>
            <?php if (!$isLoggedIn): ?>
                <a href="<?= BASE_URL ?>/public/index.php?route=auth/login"><i class="fas fa-lock"></i> Staff & Student Login</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="main-nav">
        <div class="nav-brand">
            <i class="fas fa-university" style="font-size: 1.5rem;"></i>
            <div>
                GMR Institute of Technology<br>
                <span style="font-size: 0.75rem; color: #64748b; font-weight: normal;">Research & Analytics Portal</span>
            </div>
        </div>
        <div class="nav-links">
            <a href="#analytics" class="nav-link-item active" id="nav-item-analytics">Analytics Dashboard</a>
            <a href="#student-activities" class="nav-link-item" id="nav-item-sa">Student Activities</a>
            <a href="#profiles" class="nav-link-item" id="nav-item-profiles">Top Profiles</a>
            <?php if (!$isLoggedIn): ?>
                <a href="<?= BASE_URL ?>/public/index.php?route=auth/login" class="nav-btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/public/index.php?route=dashboard" class="nav-btn-dashboard">
                    <i class="fas fa-columns"></i> Go to Dashboard
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Section with Master Filters -->
    <section class="hero">
        <div class="hero-left">
            <span class="tagline">— DYNAMIC RESEARCH INTELLIGENCE</span>
            <h1>Research & Analytics Portal</h1>
            <p>Select an academic year, isolate a department, or click a metric card to instantly drill down into specific research outputs.</p>
            <p style="font-size: 0.9rem; color: #94a3b8;"><i class="fas fa-arrow-down"></i> Scroll down to explore the dynamic analytics dashboard.</p>
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

    <!-- Interactive Research Analytics -->
    <section class="section light" id="analytics" style="padding: 20px 0;">
        <div class="amazon-layout">
            <aside class="amazon-sidebar">
                <h3><i class="fas fa-filter"></i> Refine Results</h3>
                
                <div class="facet-group">
                    <h4>Analytics Focus</h4>
                    <label class="facet-label" style="font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 10px;"><input type="checkbox" class="cb-select-all" data-target=".cb-type" checked> Select All</label>
                    <?php foreach($doc_types as $dt): ?>
                        <label class="facet-label"><input type="checkbox" class="cb-type" value="<?= $dt['type_code'] ?>" checked> <?= htmlspecialchars($dt['label']) ?></label>
                    <?php endforeach; ?>
                </div>

                <div class="facet-group">
                    <h4>Academic Year</h4>
                    <label class="facet-label" style="font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 10px;"><input type="checkbox" class="cb-select-all" data-target=".cb-year" checked> Select All</label>
                    <?php foreach($years as $yr): ?>
                        <label class="facet-label"><input type="checkbox" class="cb-year" value="<?= htmlspecialchars($yr) ?>" checked> <?= htmlspecialchars($yr) ?></label>
                    <?php endforeach; ?>
                </div>

                <div class="facet-group">
                    <h4>Department</h4>
                    <label class="facet-label" style="font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 10px;"><input type="checkbox" class="cb-select-all" data-target=".cb-dept" checked> Select All</label>
                    <?php foreach($depts as $d): ?>
                        <label class="facet-label"><input type="checkbox" class="cb-dept" value="<?= $d['dept_id'] ?>" checked> <?= htmlspecialchars($d['dept_name']) ?></label>
                    <?php endforeach; ?>
                </div>
            </aside>

            <div class="amazon-main">
                <div class="section-header">
                    <h2><i class="fas fa-chart-line" style="color: var(--accent);"></i> Dynamic Visualization</h2>
                    <span style="color: #64748b; font-weight: 500;" id="current-view-label">Currently Viewing: All Institutional Data</span>
                </div>

                <div class="analytics-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-area" style="color: var(--accent);"></i> Institutional Trajectory</h3>
                            <p>Continuous progression across academic cycles</p>
                        </div>
                        <div class="chart-wrapper"><canvas id="timelineChart"></canvas></div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 id="dist-title"><i class="fas fa-chart-pie" style="color: var(--accent);"></i> Documents by Department</h3>
                            <p>Proportional breakdown based on active filters</p>
                        </div>
                        <div class="chart-wrapper"><canvas id="distributionChart"></canvas></div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-radar" style="color: var(--accent);"></i> Research Focus Signature</h3>
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
            </div>
        </div>
    </section>

    <!-- Student Activities Dashboard Section (Using Existing Styles) -->
    <section class="section" id="student-activities" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 50px 40px;">
        <div style="max-width: 1400px; margin: 0 auto;">
            <div class="section-header">
                <div>
                    <span style="color: var(--accent); font-weight: bold; letter-spacing: 2px; font-size: 0.85rem;">— CAMPUS ENGAGEMENT INTELLIGENCE</span>
                    <h2 style="margin-top: 5px;"><i class="fas fa-users-cog" style="color: var(--accent);"></i> Student Activities Dashboard</h2>
                </div>
                <span style="color: #64748b; font-weight: 500;">Dynamic Campus Engagement Metrics</span>
            </div>

            <!-- Strictly Horizontal Filter Bar -->
            <div style="display: flex; flex-direction: row; align-items: flex-end; gap: 12px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; margin-bottom: 25px; overflow-x: auto;">
                <div style="flex: 1 1 0; min-width: 125px;">
                    <label for="sa_filter_year" style="display: block; font-size: 0.75rem; font-weight: bold; color: var(--secondary); margin-bottom: 5px;">ACADEMIC YEAR</label>
                    <select id="sa_filter_year" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; font-size: 0.85rem;">
                        <option value="all">All Years</option>
                        <?php foreach($sa_years as $yr): ?>
                            <option value="<?= (int)$yr['year_id'] ?>"><?= htmlspecialchars($yr['year_label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1 1 0; min-width: 125px;">
                    <label for="sa_filter_dept" style="display: block; font-size: 0.75rem; font-weight: bold; color: var(--secondary); margin-bottom: 5px;">DEPARTMENT</label>
                    <select id="sa_filter_dept" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; font-size: 0.85rem;">
                        <option value="all">All Departments</option>
                        <?php foreach($sa_depts as $d): ?>
                            <option value="<?= (int)$d['dept_id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1 1 0; min-width: 125px;">
                    <label for="sa_filter_scope" style="display: block; font-size: 0.75rem; font-weight: bold; color: var(--secondary); margin-bottom: 5px;">EVENT SCOPE</label>
                    <select id="sa_filter_scope" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; font-size: 0.85rem;">
                        <option value="all">All Scopes</option>
                        <option value="department">Department Wise</option>
                        <option value="central">Central / Campus Wide</option>
                        <option value="external">Inter-College / External</option>
                    </select>
                </div>
                <div style="flex: 1 1 0; min-width: 125px;">
                    <label for="sa_filter_mode" style="display: block; font-size: 0.75rem; font-weight: bold; color: var(--secondary); margin-bottom: 5px;">EVENT MODE</label>
                    <select id="sa_filter_mode" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; font-size: 0.85rem;">
                        <option value="all">All Modes</option>
                        <?php foreach(($sa_options['modes'] ?? []) as $m): ?>
                            <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1 1 0; min-width: 125px;">
                    <label for="sa_filter_domain" style="display: block; font-size: 0.75rem; font-weight: bold; color: var(--secondary); margin-bottom: 5px;">DOMAIN / TOPIC</label>
                    <select id="sa_filter_domain" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; font-size: 0.85rem;">
                        <option value="all">All Domains</option>
                        <?php foreach(($sa_options['domains'] ?? []) as $dom): ?>
                            <option value="<?= htmlspecialchars($dom) ?>"><?= htmlspecialchars($dom) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1 1 0; min-width: 125px;">
                    <label for="sa_filter_type" style="display: block; font-size: 0.75rem; font-weight: bold; color: var(--secondary); margin-bottom: 5px;">EVENT TYPE</label>
                    <select id="sa_filter_type" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; font-size: 0.85rem;">
                        <option value="all">All Types</option>
                        <?php foreach(($sa_options['event_types'] ?? []) as $et): ?>
                            <option value="<?= htmlspecialchars($et) ?>"><?= htmlspecialchars($et) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 0 0 auto;">
                    <button type="button" id="sa_filter_reset" style="padding: 8px 16px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #475569; font-size: 0.85rem; font-weight: 600; cursor: pointer; height: 38px; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>

            <!-- KPI Cards using existing .chart-card style -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
                <div class="chart-card" style="padding: 18px 22px; flex-direction: row; align-items: center; gap: 15px; border-left: 4px solid #0284c7;">
                    <i class="fas fa-calendar-check" style="font-size: 2rem; color: #0284c7;"></i>
                    <div>
                        <div style="font-size: 0.75rem; color: #64748b; font-weight: bold; text-transform: uppercase;">Total Events</div>
                        <div id="sa-val-events" style="font-size: 1.6rem; font-weight: bold; color: var(--primary);"><?= number_format($sa_initial_stats['total_events']) ?></div>
                    </div>
                </div>
                <div class="chart-card" style="padding: 18px 22px; flex-direction: row; align-items: center; gap: 15px; border-left: 4px solid #4f46e5;">
                    <i class="fas fa-building-columns" style="font-size: 2rem; color: #4f46e5;"></i>
                    <div>
                        <div style="font-size: 0.75rem; color: #64748b; font-weight: bold; text-transform: uppercase;">Department-Wise</div>
                        <div id="sa-val-dept-events" style="font-size: 1.6rem; font-weight: bold; color: var(--primary);"><?= number_format($sa_initial_stats['department_events']) ?></div>
                    </div>
                </div>
                <div class="chart-card" style="padding: 18px 22px; flex-direction: row; align-items: center; gap: 15px; border-left: 4px solid var(--accent);">
                    <i class="fas fa-users" style="font-size: 2rem; color: var(--accent);"></i>
                    <div>
                        <div style="font-size: 0.75rem; color: #64748b; font-weight: bold; text-transform: uppercase;">Central / Campus</div>
                        <div id="sa-val-central-events" style="font-size: 1.6rem; font-weight: bold; color: var(--primary);"><?= number_format($sa_initial_stats['central_events']) ?></div>
                    </div>
                </div>
                <div class="chart-card" style="padding: 18px 22px; flex-direction: row; align-items: center; gap: 15px; border-left: 4px solid #10b981;">
                    <i class="fas fa-user-graduate" style="font-size: 2rem; color: #10b981;"></i>
                    <div>
                        <div style="font-size: 0.75rem; color: #64748b; font-weight: bold; text-transform: uppercase;">Total Participation</div>
                        <div id="sa-val-participants" style="font-size: 1.6rem; font-weight: bold; color: var(--primary);"><?= number_format($sa_initial_stats['total_participants']) ?></div>
                    </div>
                </div>
            </div>

            <!-- Charts in standard .analytics-grid -->
            <div class="analytics-grid">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-line" style="color: var(--accent);"></i> Participation Engagement Timeline</h3>
                        <p>Total students participating across academic years</p>
                    </div>
                    <div class="chart-wrapper"><canvas id="saTimelineChart"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie" style="color: var(--accent);"></i> Event Categories</h3>
                        <p>Distribution across student activity streams</p>
                    </div>
                    <div class="chart-wrapper"><canvas id="saCategoryChart"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-layer-group" style="color: var(--accent);"></i> Event Scope Breakdown</h3>
                        <p>Departmental vs Campus vs External</p>
                    </div>
                    <div class="chart-wrapper"><canvas id="saScopeChart"></canvas></div>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-laptop-house" style="color: var(--accent);"></i> Delivery Mode</h3>
                        <p>Online, Offline and Hybrid distribution</p>
                    </div>
                    <div class="chart-wrapper"><canvas id="saModeChart"></canvas></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Top Researchers -->
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

    <div class="footer">Powered by FMS (File Management System)</div>

    <script>
        // Research Analytics State & Charts (SSR for instant zero-lag paint)
        const initialResearchData = <?= json_encode($initial_research_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        let state = { year: 'all', dept_id: 'all', category: 'all', type: 'all' };
        let timelineChartInst = null, distChartInst = null, radarChartInst = null, polarChartInst = null;

        const cbYears = document.querySelectorAll('.cb-year');
        const cbDepts = document.querySelectorAll('.cb-dept');
        const cbTypes = document.querySelectorAll('.cb-type');
        const metricCards = document.querySelectorAll('.hero-stat-card[data-category]');
        const loader = document.getElementById('loader');
        
        function animateValue(obj, start, end, duration) {
            if (!obj) return;
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
                const url = `<?= BASE_URL ?>/public/api/public_stats.php?year=${encodeURIComponent(state.year)}&dept_id=${encodeURIComponent(state.dept_id)}&category=${encodeURIComponent(state.category)}&type=${encodeURIComponent(state.type)}`;
                const response = await fetch(url);
                if (!response.ok) throw new Error('HTTP ' + response.status);
                const json = await response.json();
                if(json.status === 'success') updateUI(json.data);
            } catch (err) { console.error('Dashboard error:', err); } 
            finally { setTimeout(() => { loader.style.display = 'none'; }, 200); }
        }

        function updateUI(data) {
            let filterLabel = [];
            if (state.year === 'none' || state.dept_id === 'none' || state.type === 'none') {
                document.getElementById('current-view-label').innerText = `Currently Viewing: 0 Results (Filter Cleared)`;
            } else {
                if (state.year !== 'all') filterLabel.push(`Years: ${state.year}`);
                if (state.dept_id !== 'all') {
                    const names = Array.from(cbDepts).filter(cb => cb.checked).map(cb => cb.parentElement.textContent.trim()).join(', ');
                    filterLabel.push(`Dept: ${names}`);
                }
                if (state.type !== 'all') filterLabel.push(`${Array.from(cbTypes).filter(cb => cb.checked).length} Focus Areas`);
                if (state.category !== 'all') filterLabel.push(`Metric: ${state.category.toUpperCase()}`);
                document.getElementById('current-view-label').innerText = filterLabel.length > 0 ? `Currently Viewing: ${filterLabel.join(' | ')}` : `Currently Viewing: All Institutional Data`;
            }

            const mData = data.stats || { total_docs: 0, pubs: 0, patents: 0, fdps: 0, researchers: 0 };
            animateValue(document.getElementById('val-total'), 0, mData.total_docs || 0, 800);
            animateValue(document.getElementById('val-pubs'), 0, mData.pubs || 0, 800);
            animateValue(document.getElementById('val-patents'), 0, mData.patents || 0, 800);
            animateValue(document.getElementById('val-fdps'), 0, mData.fdps || 0, 800);
            animateValue(document.getElementById('val-researchers'), 0, mData.researchers || 0, 800);

            // Timeline
            const tLabels = data.timeline ? Object.keys(data.timeline) : [];
            const tValues = data.timeline ? Object.values(data.timeline) : [];
            if (timelineChartInst) timelineChartInst.destroy();
            const ctxT = document.getElementById('timelineChart').getContext('2d');
            let grad = ctxT.createLinearGradient(0, 0, 0, 400);
            grad.addColorStop(0, 'rgba(23, 162, 184, 0.6)');
            grad.addColorStop(1, 'rgba(23, 162, 184, 0.0)');
            timelineChartInst = new Chart(ctxT, {
                type: 'line',
                data: {
                    labels: tLabels.length > 0 ? tLabels : ['No Data'],
                    datasets: [{ label: 'Activities', data: tValues.length > 0 ? tValues : [0], borderColor: '#17a2b8', backgroundColor: grad, borderWidth: 3, fill: true, tension: 0.4 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });

            // Department Distribution
            const isSingleDept = state.dept_id !== 'all' && state.dept_id !== 'none' && !state.dept_id.includes(',');
            let distTitle = 'Documents by Department', chartType = 'bar', distData = data.dept_distribution || {};
            if (isSingleDept) {
                const sDept = Array.from(cbDepts).find(cb => cb.checked);
                distTitle = `${sDept ? sDept.parentElement.textContent.trim() : 'Department'} - Category Distribution`;
                chartType = 'doughnut';
                distData = data.category_distribution || {};
            }
            document.getElementById('dist-title').innerHTML = `<i class="fas fa-chart-pie" style="color: var(--accent);"></i> ` + distTitle;
            if (distChartInst) distChartInst.destroy();
            const bgColors = ['#17a2b8', '#0b353d', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#ec4899', '#14b8a6', '#f97316'];
            distChartInst = new Chart(document.getElementById('distributionChart').getContext('2d'), {
                type: chartType,
                data: { labels: Object.keys(distData), datasets: [{ data: Object.values(distData), backgroundColor: chartType === 'doughnut' ? bgColors : '#0b353d' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: chartType === 'doughnut' } } }
            });

            // Radar
            if (radarChartInst) radarChartInst.destroy();
            radarChartInst = new Chart(document.getElementById('radarChart').getContext('2d'), {
                type: 'radar',
                data: { labels: Object.keys(data.category_distribution || {}), datasets: [{ label: 'Focus', data: Object.values(data.category_distribution || {}), backgroundColor: 'rgba(23, 162, 184, 0.2)', borderColor: '#17a2b8' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { r: { min: 0 } } }
            });

            // Polar
            if (polarChartInst) polarChartInst.destroy();
            polarChartInst = new Chart(document.getElementById('polarChart').getContext('2d'), {
                type: 'polarArea',
                data: { labels: Object.keys(data.dept_distribution || {}), datasets: [{ data: Object.values(data.dept_distribution || {}), backgroundColor: bgColors.map(c => c + 'CC') }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });
        }

        function syncFiltersAndFetch() {
            const cY = Array.from(cbYears).filter(cb => cb.checked);
            const cD = Array.from(cbDepts).filter(cb => cb.checked);
            const cT = Array.from(cbTypes).filter(cb => cb.checked);
            state.year = (cY.length === cbYears.length) ? 'all' : (cY.length === 0 ? 'none' : cY.map(cb => cb.value).join(','));
            state.dept_id = (cD.length === cbDepts.length) ? 'all' : (cD.length === 0 ? 'none' : cD.map(cb => cb.value).join(','));
            state.type = (cT.length === cbTypes.length) ? 'all' : (cT.length === 0 ? 'none' : cT.map(cb => cb.value).join(','));
            fetchDashboardData();
        }

        document.querySelectorAll('.cb-select-all').forEach(sa => {
            sa.addEventListener('change', (e) => {
                const group = e.target.closest('.facet-group');
                const targets = group ? group.querySelectorAll(e.target.dataset.target) : document.querySelectorAll(e.target.dataset.target);
                targets.forEach(t => t.checked = e.target.checked);
                syncFiltersAndFetch();
            });
        });

        [...cbYears, ...cbDepts, ...cbTypes].forEach(cb => cb.addEventListener('change', () => syncFiltersAndFetch()));
        metricCards.forEach(card => card.addEventListener('click', () => {
            metricCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            state.category = card.dataset.category;
            fetchDashboardData();
        }));

        // Navigation Active Links
        const navLinks = document.querySelectorAll('.main-nav .nav-link-item');
        navLinks.forEach(link => link.addEventListener('click', function() {
            navLinks.forEach(n => n.classList.remove('active'));
            this.classList.add('active');
        }));

        // Student Activities Dynamics
        const saInitial = <?= json_encode($sa_initial_stats, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        let saCharts = {};

        function renderSACharts(data) {
            const tl = data.timeline || {};
            if (saCharts.tl) saCharts.tl.destroy();
            const ctxTL = document.getElementById('saTimelineChart').getContext('2d');
            let gradTL = ctxTL.createLinearGradient(0, 0, 0, 350);
            gradTL.addColorStop(0, 'rgba(2, 132, 199, 0.4)');
            gradTL.addColorStop(1, 'rgba(2, 132, 199, 0.0)');
            saCharts.tl = new Chart(ctxTL, {
                type: 'line',
                data: {
                    labels: Object.keys(tl).length ? Object.keys(tl) : ['No Data'],
                    datasets: [{ label: 'Participants', data: Object.values(tl).length ? Object.values(tl) : [0], borderColor: '#0284c7', backgroundColor: gradTL, fill: true, tension: 0.35, borderWidth: 3 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });

            const cat = data.category_dist || {};
            if (saCharts.cat) saCharts.cat.destroy();
            saCharts.cat = new Chart(document.getElementById('saCategoryChart'), {
                type: 'doughnut',
                data: { labels: Object.keys(cat), datasets: [{ data: Object.values(cat), backgroundColor: ['#0284c7', '#0d9488', '#4f46e5', '#d97706', '#dc2626', '#8b5cf6', '#ec4899'] }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'right' } } }
            });

            const scp = data.scope_dist || {};
            if (saCharts.scp) saCharts.scp.destroy();
            saCharts.scp = new Chart(document.getElementById('saScopeChart'), {
                type: 'doughnut',
                data: { labels: Object.keys(scp), datasets: [{ data: Object.values(scp), backgroundColor: ['#4f46e5', '#0284c7', '#059669'] }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'right' } } }
            });

            const md = data.mode_dist || {};
            if (saCharts.md) saCharts.md.destroy();
            saCharts.md = new Chart(document.getElementById('saModeChart'), {
                type: 'doughnut',
                data: { labels: Object.keys(md), datasets: [{ data: Object.values(md), backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6'] }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'right' } } }
            });
        }

        async function fetchSA() {
            const yr = document.getElementById('sa_filter_year')?.value || 'all';
            const dp = document.getElementById('sa_filter_dept')?.value || 'all';
            const sc = document.getElementById('sa_filter_scope')?.value || 'all';
            const md = document.getElementById('sa_filter_mode')?.value || 'all';
            const dm = document.getElementById('sa_filter_domain')?.value || 'all';
            const tp = document.getElementById('sa_filter_type')?.value || 'all';
            const params = new URLSearchParams({ year_id: yr, dept_id: dp, scope: sc, mode: md, domain: dm, event_type: tp });

            try {
                const res = await fetch(`<?= BASE_URL ?>/public/api/student_activity_stats.php?${params}`);
                const json = await res.json();
                if (json.status !== 'success' || !json.data) return;
                const s = json.data;

                animateValue(document.getElementById('sa-val-events'), 0, s.total_events || 0, 400);
                animateValue(document.getElementById('sa-val-dept-events'), 0, s.department_events || 0, 400);
                animateValue(document.getElementById('sa-val-central-events'), 0, s.central_events || 0, 400);
                animateValue(document.getElementById('sa-val-participants'), 0, s.total_participants || 0, 400);

                renderSACharts(s);
            } catch (e) { console.error('SA fetch error', e); }
        }

        ['sa_filter_year', 'sa_filter_dept', 'sa_filter_scope', 'sa_filter_mode', 'sa_filter_domain', 'sa_filter_type'].forEach(id => {
            document.getElementById(id)?.addEventListener('change', fetchSA);
        });
        document.getElementById('sa_filter_reset')?.addEventListener('click', () => {
            ['sa_filter_year', 'sa_filter_dept', 'sa_filter_scope', 'sa_filter_mode', 'sa_filter_domain', 'sa_filter_type'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = 'all';
            });
            fetchSA();
        });

        // Initialize on page load instantly without blocking loader or network fetch
        renderSACharts(saInitial);
        updateUI(initialResearchData);
    </script>
</body>
</html>