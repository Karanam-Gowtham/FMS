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
                                        $eventName = $sub['event_details']['event_name'] ?? 'N/A';
                                        $level = $sub['event_details']['level'] ?? '';
                                        echo htmlspecialchars($eventName);
                                        if ($level) echo " <small class='text-muted'>($level)</small>";
                                    ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($sub['created_at'])) ?></td>
                                <td><span class="badge <?= $badge_class ?> text-uppercase"><?= $sub['status'] ?></span></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&id=<?= $sub['doc_id'] ?>" class="btn btn-sm btn-outline-secondary">View</a>
                                    <?php if ($sub['status'] === 'rejected'): ?>
                                        <button class="btn btn-sm btn-outline-danger" title="Edit & Resubmit"><i class="fas fa-edit"></i> Edit</button>
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

<!-- Upload Activity Modal -->
<div class="modal fade" id="uploadActivityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Inter-College Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_URL ?>/public/index.php?route=documents/upload" method="POST" enctype="multipart/form-data" id="studentActivityForm">
                <?= csrfField() ?>
                <!-- We simulate document type selection for the backend processor -->
                <input type="hidden" name="doc_type_id" value="4"> <!-- Student Activity -->
                <input type="hidden" name="custom_handler" value="student_activity">
                
                <div class="modal-body">
                    <!-- Standard Fields -->
                    <h6 class="border-bottom pb-2 mb-3 text-primary">1. Basic Details</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Activity Category *</label>
                            <select class="form-select" name="activity_category" id="activity_category" required>
                                <option value="">- Select -</option>
                                <option value="Co-Curricular">Co-Curricular Activities</option>
                                <option value="Sports & Games">Sports & Games</option>
                                <option value="NSS & NCC">NSS & NCC</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Event Name *</label>
                            <input type="text" class="form-control" name="event_name" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Date of Event *</label>
                            <input type="date" class="form-control" name="event_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Academic Year *</label>
                            <select class="form-select" name="ay_id" required>
                                <?php foreach($academic_years as $ay): ?>
                                    <option value="<?= $ay['ay_id'] ?>"><?= htmlspecialchars($ay['ay_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Upload Proof (PDF Only, Max 2MB) *</label>
                        <input type="file" class="form-control" name="proof_file" accept=".pdf" required>
                    </div>

                    <!-- Dynamic Fields Section -->
                    <h6 class="border-bottom pb-2 mb-3 text-primary d-none" id="dynamicHeading">2. Specific Details</h6>
                    <div id="dynamicFieldsContainer"></div>
                    
                    <!-- Team vs Individual Logic -->
                    <h6 class="border-bottom pb-2 mb-3 mt-4 text-primary">3. Participants</h6>
                    <div class="mb-3">
                        <label class="form-label d-block">Participation Type *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="participation_type" id="partInd" value="Individual" checked>
                            <label class="form-check-label" for="partInd">Individual</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="participation_type" id="partTeam" value="Team">
                            <label class="form-check-label" for="partTeam">Team</label>
                        </div>
                    </div>
                    
                    <div id="participantsContainer">
                        <div class="row mb-2 participant-row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="participant_names[]" placeholder="Student Name" required>
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="participant_jntu[]" placeholder="JNTU No. (e.g., 21341A0501)" required>
                            </div>
                            <div class="col-md-1">
                                <!-- First row can't be deleted easily, leave empty -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="addMemberWrapper" style="display: none;">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="addMemberBtn">
                            <i class="fas fa-plus"></i> Add Team Member
                        </button>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit for Approval</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('activity_category');
    const dynamicContainer = document.getElementById('dynamicFieldsContainer');
    const dynamicHeading = document.getElementById('dynamicHeading');
    
    // Team Logic
    const partInd = document.getElementById('partInd');
    const partTeam = document.getElementById('partTeam');
    const participantsContainer = document.getElementById('participantsContainer');
    const addMemberWrapper = document.getElementById('addMemberWrapper');
    const addMemberBtn = document.getElementById('addMemberBtn');
    
    // Dynamic Fields HTML Templates
    const tplCoCurricular = `
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Event Type *</label>
                <select class="form-select" name="event_type" required>
                    <option value="">- Select -</option>
                    <option value="Paper Presentation">Paper Presentation</option>
                    <option value="Poster Presentation">Poster Presentation</option>
                    <option value="Hackathon">Hackathon</option>
                    <option value="Coding Contest">Coding Contest</option>
                    <option value="Project Expo">Project Expo</option>
                    <option value="Workshop/Seminar">Workshop/Seminar</option>
                    <option value="Journal">Journal</option>
                    <option value="GATE Exam">GATE Exam</option>
                    <option value="Other">Other Events</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Title / Details</label>
                <input type="text" class="form-control" name="event_title" placeholder="Paper title, Journal name, GATE rank...">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Host Institution / College *</label>
                <input type="text" class="form-control" name="host_institution" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Level *</label>
                <select class="form-select" name="level" required>
                    <option value="">- Select -</option>
                    <option value="State">State</option>
                    <option value="National">National</option>
                    <option value="International">International</option>
                    <option value="Inter-University">Inter-University</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Achievement / Result *</label>
            <select class="form-select" name="achievement" required>
                <option value="Participated">Participated</option>
                <option value="1st Prize">1st Prize</option>
                <option value="2nd Prize">2nd Prize</option>
                <option value="3rd Prize">3rd Prize</option>
                <option value="Qualified">Qualified (GATE/Journal)</option>
            </select>
        </div>
    `;
    
    const tplSports = `
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Name of Sport/Game *</label>
                <select class="form-select" name="sport_name" id="sportSelect" required onchange="if(this.value==='Other'){document.getElementById('sportOther').classList.remove('d-none');}else{document.getElementById('sportOther').classList.add('d-none');}">
                    <option value="">- Select -</option>
                    <option value="Cricket">Cricket</option>
                    <option value="Football">Football</option>
                    <option value="Basketball">Basketball</option>
                    <option value="Athletics">Athletics</option>
                    <option value="Volleyball">Volleyball</option>
                    <option value="Badminton">Badminton</option>
                    <option value="Chess">Chess</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" class="form-control mt-2 d-none" id="sportOther" name="sport_name_other" placeholder="Please specify">
            </div>
            <div class="col-md-6">
                <label class="form-label">Host Institution / Venue *</label>
                <input type="text" class="form-control" name="host_institution" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Level *</label>
                <select class="form-select" name="level" required>
                    <option value="">- Select -</option>
                    <option value="Zonal">Zonal</option>
                    <option value="Inter-University">Inter-University</option>
                    <option value="State">State</option>
                    <option value="National">National</option>
                    <option value="International">International</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Achievement / Medal *</label>
                <select class="form-select" name="achievement" required>
                    <option value="Participated">Participated</option>
                    <option value="Gold / 1st">Gold / 1st</option>
                    <option value="Silver / 2nd">Silver / 2nd</option>
                    <option value="Bronze / 3rd">Bronze / 3rd</option>
                </select>
            </div>
        </div>
    `;
    
    const tplNSS = `
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Type *</label>
                <select class="form-select" name="nss_type" required>
                    <option value="NSS">NSS</option>
                    <option value="NCC">NCC</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Location / Venue *</label>
                <input type="text" class="form-control" name="location" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Duration (Days) *</label>
                <input type="number" class="form-control" name="duration" min="1" required>
            </div>
        </div>
    `;

    categorySelect.addEventListener('change', function() {
        let val = this.value;
        if(val) {
            dynamicHeading.classList.remove('d-none');
            if(val === 'Co-Curricular') dynamicContainer.innerHTML = tplCoCurricular;
            else if(val === 'Sports & Games') dynamicContainer.innerHTML = tplSports;
            else if(val === 'NSS & NCC') dynamicContainer.innerHTML = tplNSS;
        } else {
            dynamicHeading.classList.add('d-none');
            dynamicContainer.innerHTML = '';
        }
    });

    // Toggle Team UI
    function toggleTeamUI() {
        if (partTeam.checked) {
            addMemberWrapper.style.display = 'block';
        } else {
            addMemberWrapper.style.display = 'none';
            // Remove all extra rows
            const rows = participantsContainer.querySelectorAll('.participant-row');
            for(let i=1; i<rows.length; i++) {
                rows[i].remove();
            }
        }
    }
    
    partInd.addEventListener('change', toggleTeamUI);
    partTeam.addEventListener('change', toggleTeamUI);
    
    // Add Member Button
    addMemberBtn.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'row mb-2 participant-row';
        row.innerHTML = `
            <div class="col-md-6">
                <input type="text" class="form-control" name="participant_names[]" placeholder="Student Name" required>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control" name="participant_jntu[]" placeholder="JNTU No." required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-member"><i class="fas fa-times"></i></button>
            </div>
        `;
        participantsContainer.appendChild(row);
        
        row.querySelector('.remove-member').addEventListener('click', function() {
            row.remove();
        });
    });
});
</script>

</div> <!-- End student-dashboard-wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php require __DIR__ . '/../../../includes/footer.php'; ?>
