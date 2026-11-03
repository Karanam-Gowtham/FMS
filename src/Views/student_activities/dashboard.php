<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Activities Dashboard &mdash; FMS</title>
    <link rel="icon" href="data:,">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        body { padding-top: 0; background-color: #f8f9fa; }
        .public-sa-nav { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.03); }
        .public-sa-brand { font-size: 1.05rem; font-weight: 700; color: #0b353d; text-decoration: none; display: flex; align-items: center; gap: 10px; }
    </style>
</head>
<body>
<nav class="public-sa-nav">
    <a href="<?= BASE_URL ?>/index.php" class="public-sa-brand">
        <i class="fas fa-university" style="color: #17a2b8;"></i>
        <span>GMRIT Research & Analytics Portal</span>
    </a>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= BASE_URL ?>/index.php#student-activities" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Main Portal
        </a>
        <?php if (auth_is_logged_in()): ?>
            <a href="<?= BASE_URL ?>/public/index.php?route=dashboard" class="btn btn-sm btn-primary">
                <i class="fas fa-columns me-1"></i> Go to Dashboard
            </a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/public/index.php?route=auth/login" class="btn btn-sm btn-primary">
                <i class="fas fa-lock me-1"></i> Login
            </a>
        <?php endif; ?>
    </div>
</nav>

<main class="container-fluid mt-3 mb-5 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1"><i class="fas fa-users-cog text-primary me-2"></i>Student Activities Dashboard</h2>
            <p class="text-muted mb-0">Analytics and engagement tracking for all student-driven initiatives and events.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body bg-light rounded">
            <form method="GET" action="<?= BASE_URL ?>/public/index.php" class="row g-3 align-items-end">
                <input type="hidden" name="route" value="student_activities/dashboard">
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label for="filter_year_id" class="form-label fw-bold">Academic Year</label>
                    <select name="year_id" id="filter_year_id" class="form-select">
                        <option value="all" <?= ($year_filter === 'all') ? 'selected' : '' ?>>All Years</option>
                        <?php foreach($years as $yr): ?>
                            <option value="<?= (int)$yr['year_id'] ?>" <?= ($year_filter !== 'all' && (int)$year_filter === (int)$yr['year_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($yr['year_label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label for="filter_dept_id" class="form-label fw-bold">Department</label>
                    <select name="dept_id" id="filter_dept_id" class="form-select">
                        <option value="all" <?= ($dept_filter === 'all' || $dept_filter === 'authorized') ? 'selected' : '' ?>>All Departments</option>
                        <?php foreach($depts as $d): ?>
                            <option value="<?= (int)$d['dept_id'] ?>" <?= ($dept_filter !== 'all' && $dept_filter !== 'authorized' && (int)$dept_filter === (int)$d['dept_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['dept_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label for="filter_scope" class="form-label fw-bold">Event Scope</label>
                    <select name="scope" id="filter_scope" class="form-select">
                        <option value="all" <?= ($scope_filter === 'all') ? 'selected' : '' ?>>All Scopes</option>
                        <option value="department" <?= ($scope_filter === 'department') ? 'selected' : '' ?>>Department Wise</option>
                        <option value="central" <?= ($scope_filter === 'central') ? 'selected' : '' ?>>Central / Campus Wide</option>
                        <option value="external" <?= ($scope_filter === 'external') ? 'selected' : '' ?>>Inter-College / External</option>
                    </select>
                </div>

                <!-- Global Attributes Dropdowns -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <span class="form-label fw-bold d-block">Event Type</span>
                    <div class="dropdown d-grid">
                        <button class="btn btn-outline-secondary dropdown-toggle text-start bg-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <?= (!empty($filter_global_event_types) && is_array($filter_global_event_types)) ? count($filter_global_event_types).' Selected' : 'All Types' ?>
                        </button>
                        <ul class="dropdown-menu w-100 p-2 shadow-sm" style="max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                            <?php foreach(($global_filter_options['event_types'] ?? []) as $et): ?>
                            <li>
                                <label class="dropdown-item d-flex align-items-start gap-2 px-2 py-1 text-wrap" style="cursor: pointer;">
                                    <input type="checkbox" class="form-check-input mt-1" name="global_event_types[]" value="<?= htmlspecialchars($et) ?>" <?= in_array($et, is_array($filter_global_event_types ?? null) ? $filter_global_event_types : [], true) ? 'checked' : '' ?>>
                                    <span style="word-break: break-word;"><?= htmlspecialchars($et) ?></span>
                                </label>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6">
                    <span class="form-label fw-bold d-block">Domain / Topic</span>
                    <div class="dropdown d-grid">
                        <button class="btn btn-outline-secondary dropdown-toggle text-start bg-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <?= (!empty($filter_global_domains) && is_array($filter_global_domains)) ? count($filter_global_domains).' Selected' : 'All Domains' ?>
                        </button>
                        <ul class="dropdown-menu w-100 p-2 shadow-sm" style="max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                            <?php foreach(($global_filter_options['domains'] ?? []) as $dom): ?>
                            <li>
                                <label class="dropdown-item d-flex align-items-start gap-2 px-2 py-1 text-wrap" style="cursor: pointer;">
                                    <input type="checkbox" class="form-check-input mt-1" name="global_domains[]" value="<?= htmlspecialchars($dom) ?>" <?= in_array($dom, is_array($filter_global_domains ?? null) ? $filter_global_domains : [], true) ? 'checked' : '' ?>>
                                    <span style="word-break: break-word;"><?= htmlspecialchars($dom) ?></span>
                                </label>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6">
                    <span class="form-label fw-bold d-block">Event Mode</span>
                    <div class="dropdown d-grid">
                        <button class="btn btn-outline-secondary dropdown-toggle text-start bg-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <?= (!empty($filter_global_modes) && is_array($filter_global_modes)) ? count($filter_global_modes).' Selected' : 'All Modes' ?>
                        </button>
                        <ul class="dropdown-menu w-100 p-2 shadow-sm" style="max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                            <?php foreach(($global_filter_options['modes'] ?? []) as $md): ?>
                            <li>
                                <label class="dropdown-item d-flex align-items-start gap-2 px-2 py-1 text-wrap" style="cursor: pointer;">
                                    <input type="checkbox" class="form-check-input mt-1" name="global_modes[]" value="<?= htmlspecialchars($md) ?>" <?= in_array($md, is_array($filter_global_modes ?? null) ? $filter_global_modes : [], true) ? 'checked' : '' ?>>
                                    <span style="word-break: break-word;"><?= htmlspecialchars($md) ?></span>
                                </label>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="col-12 mt-3 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-filter me-1"></i> Apply Filters</button>
                    <a href="<?= BASE_URL ?>/public/index.php?route=student_activities/dashboard" class="btn btn-outline-secondary px-4"><i class="fas fa-undo me-1"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Top KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card text-white bg-primary h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="display-5 me-3"><i class="fas fa-calendar-check"></i></div>
                    <div>
                        <h6 class="card-title text-white-50 text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Events</h6>
                        <h2 class="mb-0 fw-bold"><?= number_format($stats['total_events']) ?></h2>
                        <small class="text-white-50">All registered events</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card text-white h-100 shadow-sm border-0" style="background: #4f46e5;">
                <div class="card-body d-flex align-items-center">
                    <div class="display-5 me-3"><i class="fas fa-building-columns"></i></div>
                    <div>
                        <h6 class="card-title text-white-50 text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Department-Wise</h6>
                        <h2 class="mb-0 fw-bold"><?= number_format($stats['department_events']) ?></h2>
                        <small class="text-white-50">Branch-specific initiatives</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card text-white h-100 shadow-sm border-0" style="background: #0284c7;">
                <div class="card-body d-flex align-items-center">
                    <div class="display-5 me-3"><i class="fas fa-users-rays"></i></div>
                    <div>
                        <h6 class="card-title text-white-50 text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Central / Campus</h6>
                        <h2 class="mb-0 fw-bold"><?= number_format($stats['central_events']) ?></h2>
                        <small class="text-white-50">Clubs, SAC & Stepcone</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card text-white bg-success h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="display-5 me-3"><i class="fas fa-user-graduate"></i></div>
                    <div>
                        <h6 class="card-title text-white-50 text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Participation</h6>
                        <h2 class="mb-0 fw-bold"><?= number_format($stats['total_participants']) ?></h2>
                        <small class="text-white-50">Student attendees recorded</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h5 class="mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Participation Engagement Timeline</h5>
                    <small class="text-muted">Total students participating across months</small>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 350px; width: 100%;"><canvas id="timelineChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h5 class="mb-0"><i class="fas fa-chart-pie text-info me-2"></i>Event Categories</h5>
                    <small class="text-muted">Distribution across student activity streams</small>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="position: relative; height: 350px; width: 100%;"><canvas id="categoryChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h5 class="mb-0"><i class="fas fa-layer-group text-primary me-2"></i>Event Scope Breakdown</h5>
                    <small class="text-muted">Departmental vs Campus vs External</small>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="position: relative; height: 300px; width: 100%;"><canvas id="scopeChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h5 class="mb-0"><i class="fas fa-laptop-house text-warning me-2"></i>Delivery Mode</h5>
                    <small class="text-muted">Online, Offline and Hybrid distribution</small>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="position: relative; height: 300px; width: 100%;"><canvas id="modeChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h5 class="mb-0"><i class="fas fa-crown text-secondary me-2"></i>Most Active Clubs</h5>
                    <small class="text-muted">Top campus-wide clubs by events</small>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush mt-3">
                        <?php 
                        $clubs = isset($stats['club_activity']) && is_array($stats['club_activity']) ? $stats['club_activity'] : [];
                        if (!empty($clubs)) {
                            arsort($clubs);
                        }
                        $top_clubs = array_slice($clubs, 0, 5, true);
                        if(empty($top_clubs)): ?>
                            <li class="list-group-item text-muted border-0">No club data available.</li>
                        <?php else: 
                            foreach($top_clubs as $club => $count): ?>
                            <li class="list-group-item border-0 d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-shield-alt text-primary me-2"></i><?= htmlspecialchars($club) ?></span>
                                <span class="badge bg-primary rounded-pill"><?= (int)$count === 1 ? '1 Event' : (int)$count . ' Events' ?></span>
                            </li>
                        <?php endforeach; endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 3 -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h5 class="mb-0"><i class="fas fa-bullseye text-danger me-2"></i>Topic Focus Area</h5>
                    <small class="text-muted">Technical domain and skillset tracks</small>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="position: relative; height: 350px; width: 100%;"><canvas id="topicRadarChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h5 class="mb-0"><i class="fas fa-university text-primary me-2"></i>Department-Wise Initiatives Comparison</h5>
                        <small class="text-muted">Comparing branch-specific events and participant engagement across academic departments (central clubs & campus-wide fests excluded for fair ranking)</small>
                    </div>
                    <span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-filter me-1 text-primary"></i>Department Scope Only</span>
                </div>
                <div class="card-body">
                    <?php if (empty($stats['dept_compare'])): ?>
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-info-circle fa-2x mb-2 text-secondary"></i>
                            <p class="mb-0">No department-specific events found for the active filter selection.</p>
                            <small>Central club and campus-wide events are tracked in their dedicated campus metrics above.</small>
                        </div>
                    <?php else: ?>
                        <div style="position: relative; height: 350px; width: 100%;"><canvas id="deptCompareChart"></canvas></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script id="sa-stats-data" type="application/json">
<?= json_encode($stats, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js" crossorigin="anonymous"></script>
<script src="<?= BASE_URL ?>/assets/js/student_activities_charts.js"></script>
</body>
</html>
