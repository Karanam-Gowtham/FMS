<?php
require_once __DIR__ . '/../../../includes/header.php';
?>
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
<div class="container-fluid mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-users-cog text-primary"></i> Student Activities Dashboard</h2>
            <p class="text-muted mb-0">Analytics and engagement tracking for all student-driven initiatives and events.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body bg-light rounded">
            <form method="GET" action="<?= BASE_URL ?>/public/index.php" class="row g-3 align-items-end">
                <input type="hidden" name="route" value="student_activities/dashboard">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Academic Year</label>
                    <select name="year_id" class="form-control">
                        <option value="all">All Years</option>
                        <?php foreach($years as $yr): ?>
                            <option value="<?= $yr['year_id'] ?>" <?= ($year_filter == $yr['year_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($yr['year_label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Department</label>
                    <select name="dept_id" class="form-control">
                        <option value="all">All Departments</option>
                        <?php foreach($depts as $d): ?>
                            <option value="<?= $d['dept_id'] ?>" <?= ($dept_filter == $d['dept_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['dept_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Apply</button>
                </div>
                <div class="col-md-2">
                    <a href="<?= BASE_URL ?>/public/index.php?route=student_activities/dashboard" class="btn btn-outline-secondary w-100">Reset</a>
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
                    <h5 class="mb-0"><i class="fas fa-chart-line text-primary"></i> Participation Engagement Timeline</h5>
                    <small class="text-muted">Total students participating across months</small>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 320px; width: 100%;"><canvas id="timelineChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h5 class="mb-0"><i class="fas fa-chart-pie text-info"></i> Event Categories</h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 320px; width: 100%;"><canvas id="categoryChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h5 class="mb-0"><i class="fas fa-bullseye text-danger"></i> Topic Focus Area</h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;"><canvas id="topicRadarChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h5 class="mb-0"><i class="fas fa-laptop-house text-warning"></i> Delivery Mode</h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;"><canvas id="modeChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h5 class="mb-0"><i class="fas fa-crown text-secondary"></i> Most Active Clubs</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush mt-3">
                        <?php 
                        $clubs = $stats['club_activity'];
                        arsort($clubs);
                        $top_clubs = array_slice($clubs, 0, 5, true);
                        if(empty($top_clubs)): ?>
                            <li class="list-group-item text-muted">No club data available.</li>
                        <?php else: 
                            foreach($top_clubs as $club => $count): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($club) ?>
                                <span class="badge bg-primary rounded-pill"><?= $count ?> Events</span>
                            </li>
                        <?php endforeach; endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    <!-- Charts Row 3 -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h5 class="mb-0"><i class="fas fa-university text-primary"></i> University Level Department Comparison</h5>
                    <small class="text-muted">Comparing total events and student participation across departments</small>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 350px; width: 100%;"><canvas id="deptCompareChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Shared Colors
    const palette = ['#0d6efd', '#198754', '#dc3545', '#ffc107', '#0dcaf0', '#6f42c1', '#fd7e14', '#20c997'];

    // 1. Timeline Chart (Area)
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
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.2)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                borderWidth: 2
            }]
        },
        options: { 
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { 
                x: { offset: true },
                y: { beginAtZero: true, ticks: { precision: 0 } } 
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
                borderWidth: 2, borderColor: '#fff'
            }]
        },
        options: { 
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } } }
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
                backgroundColor: 'rgba(220, 53, 69, 0.7)',
                borderColor: '#dc3545',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } }
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
                backgroundColor: ['#ffc107', '#0dcaf0', '#6f42c1', '#198754'],
                borderWidth: 2, borderColor: '#fff'
            }]
        },
        options: { 
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }
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
                    backgroundColor: '#0d6efd',
                    borderRadius: 4
                },
                {
                    label: 'Total Participants',
                    data: deptParticipants,
                    backgroundColor: '#20c997',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
});
</script>

</body>
</html>
