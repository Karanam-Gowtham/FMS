<?php
// Requires: $auth, $submissions, $stats, $academic_years
$page_title = "Student Dashboard";
require __DIR__ . '/../../../includes/header.php';
?>

<!-- Load Bootstrap and FontAwesome for Student Dashboard -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Scope some Bootstrap resets so they don't break the global header */
    .main-header-navbar { box-sizing: border-box; }
    .student-dashboard-wrapper { padding: 30px; font-family: 'Segoe UI', sans-serif; }
    /* Hide double arrows caused by Bootstrap CSS conflicting with our custom header icons */
    .dropdown-toggle::after { display: none !important; }
</style>

<div class="student-dashboard-wrapper container-fluid">


    <!-- 1. HERO PROFILE SECTION -->
    <div style="background: linear-gradient(135deg, #0b1c3b 0%, #1e3a8a 100%); color: white; border-radius: 12px; padding: 2.5rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); position: relative; overflow: hidden;">
        <!-- Decorative background elements -->
        <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -80px; right: 10%; width: 300px; height: 300px; background: rgba(255,255,255,0.03); border-radius: 50%;"></div>
        
        <div style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: center; position: relative; z-index: 2;">
            <!-- Avatar -->
            <div style="width: 130px; height: 130px; border-radius: 50%; background: white; padding: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($auth['name'] ?? 'Student') ?>&background=random&size=120" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" alt="Profile Picture">
            </div>
            
            <!-- Info -->
            <div style="flex-grow: 1;">
                <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0 0 0.5rem 0; letter-spacing: -0.5px;"><?= htmlspecialchars($auth['name'] ?? 'Student Name') ?></h1>
                <h3 style="font-size: 1.1rem; font-weight: 400; margin: 0 0 0.2rem 0; color: #93c5fd;"><i class="fas fa-user-graduate me-2"></i>Student</h3>
                <p style="font-size: 1rem; margin: 0 0 1.2rem 0; color: #cbd5e1;"><i class="fas fa-id-card me-2"></i><?= htmlspecialchars($auth['username'] ?? 'Roll Number') ?></p>
                
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#uploadActivityModal" style="background: rgba(255,255,255,0.15); color: white; padding: 0.5rem 1.2rem; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 0.9rem; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                        <i class="fas fa-plus-circle me-1"></i> Submit New Activity
                    </button>
                </div>
            </div>
            
            <!-- Hero Stats -->
            <div style="display: flex; gap: 1.5rem; background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 12px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.05);">
                <div style="text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: 800; color: #60a5fa; line-height: 1;"><?= array_sum($stats) ?></div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 0.5rem;">Total Submissions</div>
                </div>
                <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
                <div style="text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: 800; color: #34d399; line-height: 1;"><?= $stats['accepted'] ?? 0 ?></div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 0.5rem;">Approved</div>
                </div>
                <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
                <div style="text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: 800; color: #fbbf24; line-height: 1;"><?= $stats['pending'] ?? 0 ?></div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-top: 0.5rem;">In Review</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. MAIN DASHBOARD CHARTS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
        
        <!-- Chart 1: Categories -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0;">
            <div style="background: #f8fafc; padding: 1.2rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
                <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700; display: flex; align-items: center;"><i class="fas fa-chart-pie me-2" style="color: #3b82f6;"></i> Activities by Category</h3>
            </div>
            <div style="padding: 1.5rem; height: 350px; display: flex; justify-content: center; align-items: center;">
                <?php if (empty($chart_categories)): ?>
                    <div style="text-align: center; color: #94a2b8;">
                        <i class="fas fa-folder-open mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                        <p>No data available to visualize.</p>
                    </div>
                <?php else: ?>
                    <canvas id="studentCategoryChart"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chart 2: Timeline -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0;">
            <div style="background: #f8fafc; padding: 1.2rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
                <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700; display: flex; align-items: center;"><i class="fas fa-chart-bar me-2" style="color: #3b82f6;"></i> Submission Timeline</h3>
            </div>
            <div style="padding: 1.5rem; height: 350px;">
                <?php if (empty($chart_timeline)): ?>
                    <div style="text-align: center; color: #94a2b8; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <i class="fas fa-chart-line mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                        <p>No data available to visualize.</p>
                    </div>
                <?php else: ?>
                    <canvas id="studentTimelineChart"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Chart.js Setup -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const colors = ['#1e40af', '#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#64748b'];

            // Category Chart
            const catData = <?= json_encode($chart_categories ?? []) ?>;
            if (Object.keys(catData).length > 0) {
                const ctxCat = document.getElementById('studentCategoryChart').getContext('2d');
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
                                labels: { font: { family: "'Segoe UI', sans-serif", size: 13 }, padding: 20, usePointStyle: true, pointStyle: 'circle' } 
                            },
                            tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8 }
                        },
                        cutout: '65%'
                    }
                });
            }

            // Timeline Chart
            const timeData = <?= json_encode($chart_timeline ?? []) ?>;
            if (Object.keys(timeData).length > 0) {
                const ctxTime = document.getElementById('studentTimelineChart').getContext('2d');
                let gradient = ctxTime.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, '#3b82f6');
                gradient.addColorStop(1, '#1e3a8a');

                new Chart(ctxTime, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(timeData).map(d => {
                            const [y, m] = d.split('-');
                            return new Date(y, m-1).toLocaleString('default', { month: 'short', year: 'numeric' });
                        }),
                        datasets: [{
                            label: 'Uploads',
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
                        plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8 } },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { drawBorder: false } },
                            x: { grid: { display: false, drawBorder: false } }
                        }
                    }
                });
            }
        });
    </script>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <h6 class="m-0 font-weight-bold text-primary">My Submissions</h6>
    </div>
    <div class="card-body">
        <?php if (empty($submissions)): ?>
            <p class="text-muted">You have not uploaded any activities yet.</p>
        <?php else: ?>
            <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=documents/bulk_action" id="bulkActionFormStudent">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                <div class="mb-3 d-flex gap-2 align-items-center">
                    <span class="fw-bold text-muted small">Bulk Actions:</span>
                    <button type="submit" name="action_type" value="zip" class="btn btn-sm btn-secondary">Download as ZIP</button>
                    <button type="submit" name="action_type" value="merge_pdf" class="btn btn-sm btn-primary">Merge to Single PDF</button>
                </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAllStudentDocs"></th>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Event Details</th>
                            <th>Date Submitted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $sub): 
                            $badge_class = 'bg-secondary';
                            if ($sub['status'] === 'pending') $badge_class = 'bg-warning text-dark';
                            if ($sub['status'] === 'accepted') $badge_class = 'bg-success';
                            if ($sub['status'] === 'rejected') $badge_class = 'bg-danger';
                        ?>
                            <tr>
                                <td class="text-center"><input type="checkbox" name="doc_ids[]" value="<?= $sub['doc_id'] ?>" class="student-doc-checkbox"></td>
                                <td>#<?= $sub['doc_id'] ?></td>
                                <td><strong><?= htmlspecialchars($sub['activity_category']) ?></strong></td>
                                <td>
                                    <?php
                                        echo htmlspecialchars($sub['event_title'] ?? 'N/A');
                                    ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($sub['created_at'])) ?></td>
                                <td><span class="badge <?= $badge_class ?> text-uppercase"><?= $sub['status'] ?></span></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&id=<?= $sub['doc_id'] ?>" class="btn btn-sm btn-outline-secondary">View</a>
                                    <?php if ($sub['status'] === 'rejected'): ?>
                                        <a href="<?= BASE_URL ?>/public/index.php?route=documents/edit&id=<?= $sub['doc_id'] ?>" class="btn btn-sm btn-outline-danger" title="Edit & Resubmit"><i class="fas fa-edit"></i> Edit</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            </form>

            <script>
                document.getElementById('selectAllStudentDocs').addEventListener('change', function() {
                    let checkboxes = document.querySelectorAll('.student-doc-checkbox');
                    for (let cb of checkboxes) {
                        cb.checked = this.checked;
                    }
                });
            </script>
        <?php endif; ?>
    </div>
</div>

<!-- Upload Activity Modal (Seamless AJAX Form) -->
<div class="modal fade" id="uploadActivityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalTitle">Upload Inter-College Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body" style="padding-bottom: 0;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Activity Category *</label>
                        <select class="form-select" id="route_activity_category">
                            <option value="">- Select -</option>
                            <option value="Co-Curricular">Co-Curricular Activities</option>
                            <option value="Sports & Games">Sports & Games</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3 d-none" id="route_event_type_container">
                        <label class="form-label fw-bold">Event Type *</label>
                        <select class="form-select" id="route_event_type">
                            <!-- Populated dynamically -->
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-body bg-light border-top d-none" id="modalFormBody">
                <!-- AJAX loaded form will be injected here -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary d-none" id="btnSubmitAjaxForm">Submit for Approval</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const catSelect = document.getElementById('route_activity_category');
    const typeContainer = document.getElementById('route_event_type_container');
    const typeSelect = document.getElementById('route_event_type');
    
    const formBody = document.getElementById('modalFormBody');
    const btnSubmit = document.getElementById('btnSubmitAjaxForm');

    const baseUrl = '<?= BASE_URL ?>/public/index.php?route=documents/upload';

    const coCurricularOptions = [
        { value: '', label: '- Select -' },
        { value: 'student_conference', label: 'Paper Presentation' },
        { value: 'student_conference', label: 'Poster Presentation' },
        { value: 'student_event', label: 'Hackathon', activity: 'Hackathon' },
        { value: 'student_event', label: 'Coding Contest', activity: 'Coding Contest' },
        { value: 'student_event', label: 'Project Expo', activity: 'Project Expo' },
        { value: 'student_event', label: 'Workshop/Seminar', activity: 'Workshop/Seminar' },
        { value: 'student_journal', label: 'Journal' },
        { value: 'exam_qual', label: 'GATE Exam', exam: 'GATE' },
        { value: 'student_body', label: 'Professional Body / Club' },
        { value: 'student_event', label: 'Other Events' }
    ];

    function fetchAndInjectForm(targetUrl) {
        formBody.classList.remove('d-none');
        btnSubmit.classList.add('d-none');
        formBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Loading specific fields...</p></div>';

        fetch(targetUrl)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const form = doc.getElementById('uploadForm');
                
                if (form) {
                    // Hide native submit and back links
                    const oldSubmit = form.querySelector('button[type="submit"]');
                    if (oldSubmit) oldSubmit.style.display = 'none';
                    const backLink = form.querySelector('a[href*="documents/upload"]');
                    if (backLink) backLink.style.display = 'none';
                    
                    // Inject hidden field so backend redirects back here
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'custom_handler';
                    hiddenInput.value = 'student_activity';
                    form.appendChild(hiddenInput);

                    // Inject Bootstrap classes
                    const inputs = form.querySelectorAll('input:not([type="hidden"]):not([type="radio"]):not([type="checkbox"]), select, textarea');
                    inputs.forEach(input => {
                        if (input.tagName === 'SELECT') {
                            input.classList.add('form-select');
                        } else {
                            input.classList.add('form-control');
                        }
                    });
                    
                    const labels = form.querySelectorAll('label');
                    labels.forEach(label => {
                        label.classList.add('form-label');
                        label.classList.add('fw-bold');
                        label.classList.add('small');
                    });
                    
                    const formGroups = form.querySelectorAll('.form-group');
                    formGroups.forEach(group => {
                        group.classList.add('mb-3');
                    });

                    // Add a nice header for the dynamic section
                    formBody.innerHTML = '<h6 class="text-primary mb-3"><i class="fas fa-list me-1"></i> Form Details</h6>';
                    formBody.appendChild(form);
                    
                    btnSubmit.classList.remove('d-none');
                    btnSubmit.disabled = false;
                    
                    // Wire up custom submit button
                    btnSubmit.onclick = function() {
                        if (form.checkValidity()) {
                            btnSubmit.disabled = true;
                            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
                            form.submit();
                        } else {
                            form.reportValidity();
                        }
                    };
                    
                    // Re-run dynamic scripts like authors
                    const scripts = doc.querySelectorAll('script');
                    scripts.forEach(script => {
                        if (script.textContent.includes('addAuthorRow') || script.textContent.includes('handleStudentActivityChange')) {
                            const newScript = document.createElement('script');
                            newScript.textContent = script.textContent;
                            document.body.appendChild(newScript);
                        }
                    });
                } else {
                    formBody.innerHTML = '<div class="alert alert-danger">Failed to load the correct fields. Please try again or use the main upload page.</div>';
                }
            })
            .catch(err => {
                formBody.innerHTML = '<div class="alert alert-danger">Network error. Please try again later.</div>';
            });
    }

    function handleChange() {
        let targetUrl = baseUrl;
        let valid = false;
        
        if (catSelect.value === 'Sports & Games') {
            targetUrl += '&type=student_event&activity=Sports';
            valid = true;
        } else if (catSelect.value === 'Co-Curricular') {
            const selectedOpt = typeSelect.options[typeSelect.selectedIndex];
            if (selectedOpt && selectedOpt.value !== '') {
                targetUrl += '&type=' + selectedOpt.value;
                if (selectedOpt.dataset.activity) {
                    targetUrl += '&activity=' + encodeURIComponent(selectedOpt.dataset.activity);
                }
                if (selectedOpt.dataset.exam) {
                    targetUrl += '&exam=' + encodeURIComponent(selectedOpt.dataset.exam);
                }
                valid = true;
            }
        }
        
        if (valid) {
            fetchAndInjectForm(targetUrl);
        } else {
            formBody.classList.add('d-none');
            btnSubmit.classList.add('d-none');
        }
    }

    catSelect.addEventListener('change', function() {
        typeSelect.innerHTML = '';
        formBody.classList.add('d-none');
        btnSubmit.classList.add('d-none');
        
        if (this.value === 'Co-Curricular') {
            coCurricularOptions.forEach(opt => {
                const el = document.createElement('option');
                el.value = opt.value;
                el.textContent = opt.label;
                if (opt.activity) el.dataset.activity = opt.activity;
                if (opt.exam) el.dataset.exam = opt.exam;
                typeSelect.appendChild(el);
            });
            typeContainer.classList.remove('d-none');
        } else if (this.value === 'Sports & Games') {
            typeContainer.classList.add('d-none');
            handleChange();
        } else {
            typeContainer.classList.add('d-none');
        }
    });

    typeSelect.addEventListener('change', handleChange);
});
</script>

</div> <!-- End student-dashboard-wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php require __DIR__ . '/../../../includes/footer.php'; ?>
