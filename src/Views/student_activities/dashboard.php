<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Activities Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { padding-top: 90px; background-color: #f8f9fa; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../../../includes/header.php'; ?>

<div class="container-fluid mt-4 mb-5 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
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
                
                <div class="col-md-2">
                    <label class="form-label fw-bold">Academic Year</label>
                    <select name="year_id" class="form-select">
                        <option value="all">All Years</option>
                        <?php foreach($years as $yr): ?>
                            <option value="<?= $yr['year_id'] ?>" <?= ($year_filter == $yr['year_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($yr['year_label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-bold">Department</label>
                    <select name="dept_id" class="form-select">
                        <option value="all">All Departments</option>
                        <?php foreach($depts as $d): ?>
                            <option value="<?= $d['dept_id'] ?>" <?= ($dept_filter == $d['dept_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['dept_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Global Attributes Dropdowns -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Event Type</label>
                    <div class="dropdown d-grid">
                        <button class="btn btn-outline-secondary dropdown-toggle text-start bg-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <?= !empty($filter_global_event_types) ? count($filter_global_event_types).' Selected' : 'All Types' ?>
                        </button>
                        <ul class="dropdown-menu w-100 p-2 shadow-sm" style="max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                            <?php foreach($global_filter_options['event_types'] as $et): ?>
                            <li>
                                <label class="dropdown-item d-flex align-items-start gap-2 px-2 py-1 text-wrap" style="cursor: pointer;">
                                    <input type="checkbox" class="form-check-input mt-1" name="global_event_types[]" value="<?= htmlspecialchars($et) ?>" <?= in_array($et, $filter_global_event_types) ? 'checked' : '' ?>>
                                    <span style="word-break: break-word;"><?= htmlspecialchars($et) ?></span>
                                </label>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Domain / Topic</label>
                    <div class="dropdown d-grid">
                        <button class="btn btn-outline-secondary dropdown-toggle text-start bg-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <?= !empty($filter_global_domains) ? count($filter_global_domains).' Selected' : 'All Domains' ?>
                        </button>
                        <ul class="dropdown-menu w-100 p-2 shadow-sm" style="max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                            <?php foreach($global_filter_options['domains'] as $dom): ?>
                            <li>
                                <label class="dropdown-item d-flex align-items-start gap-2 px-2 py-1 text-wrap" style="cursor: pointer;">
                                    <input type="checkbox" class="form-check-input mt-1" name="global_domains[]" value="<?= htmlspecialchars($dom) ?>" <?= in_array($dom, $filter_global_domains) ? 'checked' : '' ?>>
                                    <span style="word-break: break-word;"><?= htmlspecialchars($dom) ?></span>
                                </label>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Event Mode</label>
                    <div class="dropdown d-grid">
                        <button class="btn btn-outline-secondary dropdown-toggle text-start bg-white" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <?= !empty($filter_global_modes) ? count($filter_global_modes).' Selected' : 'All Modes' ?>
                        </button>
                        <ul class="dropdown-menu w-100 p-2 shadow-sm" style="max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                            <?php foreach($global_filter_options['modes'] as $md): ?>
                            <li>
                                <label class="dropdown-item d-flex align-items-start gap-2 px-2 py-1 text-wrap" style="cursor: pointer;">
                                    <input type="checkbox" class="form-check-input mt-1" name="global_modes[]" value="<?= htmlspecialchars($md) ?>" <?= in_array($md, $filter_global_modes) ? 'checked' : '' ?>>
                                    <span style="word-break: break-word;"><?= htmlspecialchars($md) ?></span>
                                </label>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold d-none d-md-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-1"></i> Apply</button>
                        <a href="<?= BASE_URL ?>/public/index.php?route=student_activities/dashboard" class="btn btn-outline-secondary"><i class="fas fa-undo me-1"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Top KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-primary h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="display-4 me-3"><i class="fas fa-calendar-check"></i></div>
                    <div>
                        <h5 class="card-title mb-0">Total Events Hosted</h5>
                        <h2 class="mb-0 fw-bold"><?= number_format($stats['total_events']) ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-success h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="display-4 me-3"><i class="fas fa-users"></i></div>
                    <div>
                        <h5 class="card-title mb-0">Total Student Participation</h5>
                        <h2 class="mb-0 fw-bold"><?= number_format($stats['total_participants']) ?></h2>
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
                    <h5 class="mb-0"><i class="fas fa-bullseye text-danger me-2"></i>Topic Focus Area</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="position: relative; height: 300px; width: 100%;"><canvas id="topicRadarChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h5 class="mb-0"><i class="fas fa-laptop-house text-warning me-2"></i>Delivery Mode</h5>
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
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush mt-3">
                        <?php 
                        $clubs = $stats['club_activity'];
                        arsort($clubs);
                        $top_clubs = array_slice($clubs, 0, 5, true);
                        if(empty($top_clubs)): ?>
                            <li class="list-group-item text-muted border-0">No club data available.</li>
                        <?php else: 
                            foreach($top_clubs as $club => $count): ?>
                            <li class="list-group-item border-0 d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($club) ?>
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
        <div class="col-md-12">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h5 class="mb-0"><i class="fas fa-university text-primary me-2"></i>University Level Department Comparison</h5>
                    <small class="text-muted">Comparing total events and student participation across departments</small>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 400px; width: 100%;"><canvas id="deptCompareChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#718096';
    const palette = ['#1A365D', '#2B6CB0', '#2F855A', '#C05621', '#805AD5', '#319795', '#D69E2E', '#E53E3E'];

    // 1. Timeline Chart
    const tlData = <?= json_encode($stats['timeline']) ?>;
    const ctxTL = document.getElementById('timelineChart').getContext('2d');
    
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

    // 2. Category Doughnut Chart
    const catData = <?= json_encode($stats['category_dist']) ?>;
    new Chart(document.getElementById('categoryChart'), {
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
            cutout: '75%',
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 12 } } } }
        }
    });

    // 3. Topic Bar Chart
    const topicData = <?= json_encode($stats['topic_dist']) ?>;
    new Chart(document.getElementById('topicRadarChart'), {
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
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { 
                x: { grid: { display: false } },
                y: { grid: { borderDash: [4, 4], color: '#E2E8F0' }, beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } 
            }
        }
    });

    // 4. Delivery Mode Doughnut
    const modeData = <?= json_encode($stats['mode_dist']) ?>;
    new Chart(document.getElementById('modeChart'), {
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
            cutout: '75%',
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 12 } } } }
        }
    });

    // 5. Department Comparison Bar Chart
    const deptData = <?= json_encode($stats['dept_compare']) ?>;
    const deptLabels = Object.keys(deptData);
    const deptEvents = deptLabels.map(dept => deptData[dept].events);
    const deptParticipants = deptLabels.map(dept => deptData[dept].participants);

    new Chart(document.getElementById('deptCompareChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [
                {
                    label: 'Total Events',
                    data: deptEvents,
                    backgroundColor: '#1A365D',
                    borderRadius: 4,
                    borderSkipped: false
                },
                {
                    label: 'Total Participants',
                    data: deptParticipants,
                    backgroundColor: '#2F855A',
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
});
</script>

</body>
</html>
