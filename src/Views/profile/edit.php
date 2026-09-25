<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile &mdash; FMS</title>
    <link href="<?= BASE_URL ?>/assets/css/layout.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/nba_module.css" rel="stylesheet">
    <style>
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: #495057; font-size: 0.9rem; }
        .form-group input, .form-group select { width: 100%; padding: 0.6rem; border: 1px solid #ced4da; border-radius: 4px; font-size: 0.9rem; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #4a90d9; box-shadow: 0 0 0 2px rgba(74, 144, 217, 0.2); }
        .success-msg { background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #c3e6cb; }
        .error-msg { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/public/index.php?route=dashboard">Dashboard</a> &raquo; 
        Edit Profile
    </div>

    <div class="header-row">
        <h1>👤 Edit Profile</h1>
        <p>Update your personal and academic information.</p>
    </div>

    <div class="section-card" style="max-width: 900px; margin: 0 auto;">
        <?php if (!empty($success)): ?>
            <div class="success-msg"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="form-grid">
            <?= csrfField() ?>
            
            <div class="form-group full-width">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($profile['name'] ?? '') ?>" required placeholder="e.g. Dr. John Doe">
            </div>

            <div class="form-group">
                <label for="pan_no">PAN No. *</label>
                <input type="text" id="pan_no" name="pan_no" value="<?= htmlspecialchars($profile['pan_no'] ?? '') ?>" required 
                       pattern="[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}" 
                       title="Format: 5 letters, 4 numbers, 1 letter (e.g., ABCDE1234F)" 
                       placeholder="e.g. ABCDE1234F" style="text-transform: uppercase;">
            </div>

            <div class="form-group">
                <label for="apaar_id">APAAR faculty ID (if any)</label>
                <input type="text" id="apaar_id" name="apaar_id" value="<?= htmlspecialchars($profile['apaar_id'] ?? '') ?>" 
                       pattern="[0-9]{12}" title="12-digit APAAR ID" placeholder="e.g. 123456789012">
            </div>

            <div class="form-group">
                <label for="highest_degree">Highest Degree *</label>
                <input type="text" id="highest_degree" name="highest_degree" value="<?= htmlspecialchars($profile['highest_degree'] ?? '') ?>" required placeholder="e.g. Ph.D. in Computer Science">
            </div>

            <div class="form-group">
                <label for="university">University *</label>
                <input type="text" id="university" name="university" value="<?= htmlspecialchars($profile['university'] ?? '') ?>" required placeholder="e.g. Stanford University">
            </div>

            <div class="form-group full-width">
                <label for="specialization">Area of Specialization *</label>
                <input type="text" id="specialization" name="specialization" value="<?= htmlspecialchars($profile['specialization'] ?? '') ?>" required placeholder="e.g. Artificial Intelligence, Machine Learning">
            </div>

            <div class="form-group">
                <label for="doj_institution">Date of Joining (Institution) *</label>
                <input type="date" id="doj_institution" name="doj_institution" value="<?= htmlspecialchars($profile['doj_institution'] ?? '') ?>" required max="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label for="doj_department">Date of Joining (Department)</label>
                <input type="date" id="doj_department" name="doj_department" value="<?= htmlspecialchars($profile['doj_department'] ?? '') ?>" title="In case of transfer from one Department to another" max="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label for="experience_years">Experience in current institute (Years) *</label>
                <input type="number" step="0.1" min="0" id="experience_years" name="experience_years" value="<?= htmlspecialchars($profile['experience_years'] ?? '') ?>" required placeholder="e.g. 5.5">
            </div>

            <div class="form-group">
                <label for="designation_joining">Designation at Time of Joining *</label>
                <input type="text" id="designation_joining" name="designation_joining" value="<?= htmlspecialchars($profile['designation_joining'] ?? '') ?>" required placeholder="e.g. Assistant Professor">
            </div>

            <div class="form-group">
                <label for="designation_present">Present Designation *</label>
                <input type="text" id="designation_present" name="designation_present" value="<?= htmlspecialchars($profile['designation_present'] ?? '') ?>" required placeholder="e.g. Associate Professor">
            </div>

            <div class="form-group">
                <label for="date_designated_prof">Date Designated as Prof/Assoc. Prof (if any)</label>
                <input type="date" id="date_designated_prof" name="date_designated_prof" value="<?= htmlspecialchars($profile['date_designated_prof'] ?? '') ?>" max="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label for="association_nature">Nature of Association *</label>
                <select id="association_nature" name="association_nature" required onchange="toggleContractFields()">
                    <option value="">&mdash; Select &mdash;</option>
                    <option value="Regular" <?= ($profile['association_nature'] ?? '') === 'Regular' ? 'selected' : '' ?>>Regular</option>
                    <option value="Contract" <?= ($profile['association_nature'] ?? '') === 'Contract' ? 'selected' : '' ?>>Contract</option>
                    <option value="Ad hoc" <?= ($profile['association_nature'] ?? '') === 'Ad hoc' ? 'selected' : '' ?>>Ad hoc</option>
                </select>
            </div>

            <div class="form-group" id="contract_type_grp" style="display: none;">
                <label for="contract_type">If Contractual (Full/Part time/Hourly)</label>
                <select id="contract_type" name="contract_type">
                    <option value="">&mdash; Select &mdash;</option>
                    <option value="Full time" <?= ($profile['contract_type'] ?? '') === 'Full time' ? 'selected' : '' ?>>Full time</option>
                    <option value="Part time" <?= ($profile['contract_type'] ?? '') === 'Part time' ? 'selected' : '' ?>>Part time</option>
                    <option value="Hourly based" <?= ($profile['contract_type'] ?? '') === 'Hourly based' ? 'selected' : '' ?>>Hourly based</option>
                </select>
            </div>

            <div class="form-group">
                <label for="is_currently_associated">Currently Associated? *</label>
                <select id="is_currently_associated" name="is_currently_associated" required onchange="toggleLeavingDate()">
                    <option value="1" <?= (!isset($profile['is_currently_associated']) || $profile['is_currently_associated']) ? 'selected' : '' ?>>Yes</option>
                    <option value="0" <?= (isset($profile['is_currently_associated']) && !$profile['is_currently_associated']) ? 'selected' : '' ?>>No</option>
                </select>
            </div>

            <div class="form-group" id="date_leaving_grp" style="display: none;">
                <label for="date_of_leaving">Date of Leaving (If not associated) *</label>
                <input type="date" id="date_of_leaving" name="date_of_leaving" value="<?= htmlspecialchars($profile['date_of_leaving'] ?? '') ?>">
            </div>

            <div class="form-group full-width" style="margin-top: 1rem;">
                <button type="submit" class="btn-save">Update Profile</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleContractFields() {
        const nature = document.getElementById('association_nature').value;
        const contractGrp = document.getElementById('contract_type_grp');
        if (nature === 'Contract') {
            contractGrp.style.display = 'block';
            document.getElementById('contract_type').setAttribute('required', 'required');
        } else {
            contractGrp.style.display = 'none';
            document.getElementById('contract_type').removeAttribute('required');
            document.getElementById('contract_type').value = '';
        }
    }

    function toggleLeavingDate() {
        const isAssoc = document.getElementById('is_currently_associated').value;
        const leavingGrp = document.getElementById('date_leaving_grp');
        if (isAssoc === '0') {
            leavingGrp.style.display = 'block';
            document.getElementById('date_of_leaving').setAttribute('required', 'required');
        } else {
            leavingGrp.style.display = 'none';
            document.getElementById('date_of_leaving').removeAttribute('required');
            document.getElementById('date_of_leaving').value = '';
        }
    }

    // Auto-calculate experience from Date of Joining
    document.getElementById('doj_institution').addEventListener('change', function() {
        if (this.value) {
            const doj = new Date(this.value);
            const today = new Date();
            const diffTime = Math.abs(today - doj);
            const diffYears = diffTime / (1000 * 60 * 60 * 24 * 365.25);
            document.getElementById('experience_years').value = diffYears.toFixed(1);
        }
    });

    // Initialize on load to restore state
    window.onload = function() {
        toggleContractFields();
        toggleLeavingDate();
    };
</script>
</body>
</html>
