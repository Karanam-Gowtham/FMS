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
</style>

<div class="student-dashboard-wrapper container-fluid">


<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="h3 mb-0 text-gray-800">My Activities</h1>
        <p class="text-muted">Upload and track your Inter-College activity proofs.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadActivityModal">
            <i class="fas fa-plus-circle me-1"></i> Upload Activity Proof
        </button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-warning border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-1">Pending Approval</h6>
                <h3 class="mb-0 font-weight-bold text-gray-800"><?= $stats['pending'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-success border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-1">Accepted Activities</h6>
                <h3 class="mb-0 font-weight-bold text-gray-800"><?= $stats['accepted'] ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-danger border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-1">Rejected (Needs Edit)</h6>
                <h3 class="mb-0 font-weight-bold text-gray-800"><?= $stats['rejected'] ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <h6 class="m-0 font-weight-bold text-primary">My Submissions</h6>
    </div>
    <div class="card-body">
        <?php if (empty($submissions)): ?>
            <p class="text-muted">You have not uploaded any activities yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
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
                                <td>#<?= $sub['doc_id'] ?></td>
                                <td><strong><?= htmlspecialchars($sub['activity_category']) ?></strong></td>
                                <td>
                                    <?php
                                        $eventName = is_array($sub['event_details']) && isset($sub['event_details']['event_name']) ? $sub['event_details']['event_name'] : 'N/A';
                                        $level = is_array($sub['event_details']) && isset($sub['event_details']['level']) ? $sub['event_details']['level'] : '';
                                        echo htmlspecialchars($eventName);
                                        if ($level) echo " <small class='text-muted'>($level)</small>";
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
