<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Activities Dashboard &mdash; FMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        body { padding-top: 90px; background-color: #f8f9fa; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../../../includes/header.php'; ?>

<div class="container-fluid mt-4 mb-5 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1"><i class="fas fa-users-cog text-primary me-2"></i>Student Activities Dashboard</h2>
            <p class="text-muted mb-0">Analytics and engagement tracking for all student-driven initiatives and events.</p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/public/index.php?route=documents/list&amp;type_keys[]=student_activity_file" class="btn text-white fw-semibold shadow-sm" style="background: #003366;">
                <i class="fas fa-file-pdf me-2"></i> Generate NAAC Report PDF
            </a>
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
                        <option value="all">All Years</option>
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
                        <option value="all">All Departments</option>
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
                    <label class="form-label fw-bold">Event Type</label>
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
                    <label class="form-label fw-bold">Domain / Topic</label>
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
                    <label class="form-label fw-bold">Event Mode</label>
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
                                <span class="badge bg-primary rounded-pill"><?= $count ?> Events</span>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js" crossorigin="anonymous"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#718096';
    const palette = ['#1A365D', '#2B6CB0', '#2F855A', '#C05621', '#805AD5', '#319795', '#D69E2E', '#E53E3E'];

    // 1. Timeline Chart
    const tlData = <?= json_encode($stats['timeline'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const ctxTL = document.getElementById('timelineChart')?.getContext('2d');
    
    if (ctxTL) {
        new Chart(ctxTL, {
            type: 'line',
            data: {
                labels: Object.keys(tlData).map(d => {
                    const parts = d.split('-');
                    return new Date(parts[0], parts[1]-1).toLocaleString('default', { month: 'short', year: 'numeric' });
                }),
                datasets: [{
                    label: 'Student Participants',
                    data: Object.values(tlData),
                    borderColor: '#2B6CB0',
                    backgroundColor: 'rgba(43, 108, 176, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#2B6CB0',
                    pointBorderWidth: 2,
                    borderWidth: 2
                }]
            },
            options: { 
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { grid: { display: false, drawBorder: false } },
                    y: { grid: { borderDash: [4, 4], color: '#E2E8F0', drawBorder: false }, beginAtZero: true, ticks: { precision: 0 } } 
                }
            }
        });
    }

    // 2. Category Doughnut Chart
    const catData = <?= json_encode($stats['category_dist'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const catEl = document.getElementById('categoryChart');
    if (catEl) {
        new Chart(catEl, {
            type: 'doughnut',
            data: {
                labels: Object.keys(catData),
                datasets: [{
                    data: Object.values(catData),
                    backgroundColor: palette,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: { 
                responsive: true, maintainAspectRatio: false,
                cutout: '72%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 12 } } } }
            }
        });
    }

    // 3. Event Scope Doughnut Chart
    const scopeData = <?= json_encode($stats['scope_dist'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const scopeEl = document.getElementById('scopeChart');
    if (scopeEl) {
        new Chart(scopeEl, {
            type: 'doughnut',
            data: {
                labels: Object.keys(scopeData),
                datasets: [{
                    data: Object.values(scopeData),
                    backgroundColor: ['#4F46E5', '#0284C7', '#10B981'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: { 
                responsive: true, maintainAspectRatio: false,
                cutout: '72%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 12 } } } }
            }
        });
    }

    // 4. Delivery Mode Doughnut
    const modeData = <?= json_encode($stats['mode_dist'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const modeEl = document.getElementById('modeChart');
    if (modeEl) {
        new Chart(modeEl, {
            type: 'doughnut',
            data: {
                labels: Object.keys(modeData),
                datasets: [{
                    data: Object.values(modeData),
                    backgroundColor: ['#1A365D', '#2B6CB0', '#2F855A', '#D69E2E'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: { 
                responsive: true, maintainAspectRatio: false,
                cutout: '72%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 12 } } } }
            }
        });
    }

    // 5. Topic Bar Chart
    const topicData = <?= json_encode($stats['topic_dist'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const topicEl = document.getElementById('topicRadarChart');
    if (topicEl) {
        new Chart(topicEl, {
            type: 'bar',
            data: {
                labels: Object.keys(topicData),
                datasets: [{
                    label: 'Events Count',
                    data: Object.values(topicData),
                    backgroundColor: '#2B6CB0',
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: { 
                indexAxis: 'y',
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { grid: { borderDash: [4, 4], color: '#E2E8F0' }, beginAtZero: true, ticks: { stepSize: 1, precision: 0 } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    // 6. Department Comparison Bar Chart
    const deptData = <?= json_encode($stats['dept_compare'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const deptEl = document.getElementById('deptCompareChart');
    if (deptEl) {
        const deptLabels = Object.keys(deptData);
        const deptEvents = deptLabels.map(dept => deptData[dept].events);
        const deptParticipants = deptLabels.map(dept => deptData[dept].participants);

        new Chart(deptEl, {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [
                    {
                        label: 'Departmental Events',
                        data: deptEvents,
                        backgroundColor: '#4F46E5',
                        borderRadius: 4,
                        borderSkipped: false
                    },
                    {
                        label: 'Student Participants',
                        data: deptParticipants,
                        backgroundColor: '#10B981',
                        borderRadius: 4,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { usePointStyle: true } } },
                scales: { 
                    x: { grid: { display: false } },
                    y: { grid: { borderDash: [4, 4], color: '#E2E8F0' }, beginAtZero: true, ticks: { precision: 0 } } 
                }
            }
        });
    }
});
</script>

</body>
</html>
