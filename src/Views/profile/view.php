<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile &mdash; FMS</title>
    <link href="<?= BASE_URL ?>/assets/css/layout.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/nba_module.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-container { max-width: 960px; margin: 0 auto; }
        .profile-card { background: #fff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 2rem; }
        .profile-header { background: linear-gradient(135deg, #003366 0%, #1e3a8a 100%); color: #fff; padding: 2rem; display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; }
        .profile-avatar { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.2); background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #64748b; }
        .profile-header-info h2 { margin: 0 0 0.3rem; font-size: 1.6rem; font-weight: 700; color: #fff; }
        .profile-header-info p { margin: 0; opacity: 0.9; font-size: 0.95rem; }
        .profile-body { padding: 2rem; }
        .profile-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem; }
        .info-group { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1rem; }
        .info-label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 700; margin-bottom: 0.25rem; }
        .info-value { font-size: 0.95rem; color: #1e293b; font-weight: 600; }
        .badge-status { display: inline-block; padding: 0.25rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; }
        .badge-active { background: #dcfce7; color: #15803d; }
        .badge-inactive { background: #fee2e2; color: #b91c1c; }
        .action-bar { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem; border-top: 1px solid #e2e8f0; padding-top: 1.5rem; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="container profile-container">

    <div class="profile-card">
        <div class="profile-header">
            <?php if (!empty($profile['profile_photo'])): ?>
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($profile['profile_photo']) ?>" alt="Avatar" class="profile-avatar">
            <?php else: ?>
                <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <?php endif; ?>
            
            <div class="profile-header-info">
                <h2><?= htmlspecialchars($profile['name'] ?? 'User') ?></h2>
                <p><i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($profile['email'] ?? '') ?></p>
                <?php if (!empty($profile['designation_present'])): ?>
                    <p style="margin-top: 4px;"><i class="fas fa-briefcase me-1"></i> <?= htmlspecialchars($profile['designation_present']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-body">
            <h3 style="margin-top: 0; margin-bottom: 1rem; font-size: 1.15rem; color: #003366; font-weight: 700;">
                <i class="fas fa-id-card me-1"></i> Identification & Academic Qualifications
            </h3>
            <div class="profile-grid">
                <div class="info-group">
                    <div class="info-label">Personnel No (Per No)</div>
                    <div class="info-value"><?= htmlspecialchars($profile['per_no'] ?? '—') ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">PAN Number</div>
                    <div class="info-value"><?= htmlspecialchars($profile['pan_no'] ?? '—') ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">APAAR ID</div>
                    <div class="info-value"><?= htmlspecialchars($profile['apaar_id'] ?? '—') ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Highest Degree</div>
                    <div class="info-value"><?= htmlspecialchars($profile['highest_degree'] ?? '—') ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">University / Institute</div>
                    <div class="info-value"><?= htmlspecialchars($profile['university'] ?? '—') ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Area of Specialization</div>
                    <div class="info-value"><?= htmlspecialchars($profile['specialization'] ?? '—') ?></div>
                </div>
            </div>

            <h3 style="margin-top: 1.5rem; margin-bottom: 1rem; font-size: 1.15rem; color: #003366; font-weight: 700;">
                <i class="fas fa-university me-1"></i> Institutional & Employment Details
            </h3>
            <div class="profile-grid">
                <div class="info-group">
                    <div class="info-label">Designation at Joining</div>
                    <div class="info-value"><?= htmlspecialchars($profile['designation_joining'] ?? '—') ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Present Designation</div>
                    <div class="info-value"><?= htmlspecialchars($profile['designation_present'] ?? '—') ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Date of Joining Institution</div>
                    <div class="info-value"><?= !empty($profile['doj_institution']) ? date('d-m-Y', strtotime($profile['doj_institution'])) : '—' ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Date of Joining Department</div>
                    <div class="info-value"><?= !empty($profile['doj_department']) ? date('d-m-Y', strtotime($profile['doj_department'])) : '—' ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Total Experience (Years)</div>
                    <div class="info-value"><?= htmlspecialchars($profile['experience_years'] ?? '0') ?> years</div>
                </div>
                <div class="info-group">
                    <div class="info-label">Nature of Association</div>
                    <div class="info-value"><?= htmlspecialchars($profile['association_nature'] ?? 'Regular') ?> <?= !empty($profile['contract_type']) ? '(' . htmlspecialchars($profile['contract_type']) . ')' : '' ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Current Association Status</div>
                    <div class="info-value">
                        <?php if (!isset($profile['is_currently_associated']) || $profile['is_currently_associated'] == 1): ?>
                            <span class="badge-status badge-active"><i class="fas fa-check-circle me-1"></i> Currently Associated</span>
                        <?php else: ?>
                            <span class="badge-status badge-inactive"><i class="fas fa-times-circle me-1"></i> Relieved on <?= htmlspecialchars($profile['date_of_leaving'] ?? '') ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="action-bar">
                <a href="<?= BASE_URL ?>/public/index.php?route=profile/edit" class="btn btn-primary" style="background: #003366; color: #fff; padding: 0.6rem 1.4rem; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
