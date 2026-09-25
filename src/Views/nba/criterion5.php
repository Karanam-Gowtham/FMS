<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Criterion 5' ?> &mdash; FMS</title>
    <link href="<?= BASE_URL ?>/assets/css/layout.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/nba_module.css" rel="stylesheet">
    <style>
        .nba-table th, .nba-table td { padding: 8px; text-align: left; }
        .nba-table thead { font-weight: bold; }
        .nba-table th { text-align: center; vertical-align: middle; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/public/index.php?route=dashboard">Dashboard</a> &raquo; 
        <a href="<?= BASE_URL ?>/public/index.php?route=nba/dashboard&year=<?= urlencode($year) ?>">NBA Accreditation</a> &raquo; 
        Criterion 5
    </div>

    <div class="header-row">
        <h1>Criterion 5: Faculty Information (100)</h1>
        <p>Academic Year: <strong><?= htmlspecialchars($year) ?></strong></p>
    </div>

    <div style="margin-bottom: 2rem; background: #fff; padding: 1rem; border-radius: 8px; border: 1px solid #dee2e6; display: flex; align-items: center; gap: 15px;">
        <label style="font-weight: 600; color: #495057;">Select Department:</label>
        <select id="level-select" style="padding: 8px; border: 1px solid #ced4da; border-radius: 4px; min-width: 250px;" onchange="window.location.href='?route=nba/criterion&id=5&year=<?= urlencode($year) ?>&dept_id=' + this.value">
            <?php foreach ($departments as $d): ?>
                <option value="<?= $d['dept_id'] ?>" <?= $d['dept_id'] == $dept_id ? 'selected' : '' ?>>
                    Department: <?= htmlspecialchars($d['dept_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="section-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <p style="font-weight: bold; margin: 0; text-align: left; flex-grow: 1;">Table No. 5A: Faculty details</p>
        </div>

        <div class="section-desc">
            <p style="color: #0d6efd; font-style: italic;">Note: This data is automatically fetched directly from the database based on the faculty profiles associated with this department. Faculty can update their details from their Edit Profile page.</p>
        </div>

        <?php
        // Fetch faculty data from the database directly for the current department
        $faculty_data = [];
        if (isset($dept_id) && $dept_id > 0) {
            $sql = "
                SELECT 
                    u.full_name, 
                    up.* 
                FROM users u
                JOIN user_roles ur ON u.user_id = ur.user_id
                LEFT JOIN user_profiles up ON u.user_id = up.user_id
                WHERE ur.dept_id = ?
                GROUP BY u.user_id
                ORDER BY u.full_name ASC
            ";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $dept_id);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc()) {
                    $faculty_data[] = $row;
                }
                $stmt->close();
            }
        }
        ?>

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <table class="nba-table" style="width: 100%; border-collapse: collapse; font-size: 0.85rem;" border="1">
                <thead style="background: #fff3cd;">
                    <tr>
                        <th style="min-width: 50px;">S.N.</th>
                        <th style="min-width: 150px;">Name of the Faculty</th>
                        <th style="min-width: 100px;">PAN No.</th>
                        <th style="min-width: 120px;">APAAR faculty ID* (if any)</th>
                        <th style="min-width: 100px;">Highest degree</th>
                        <th style="min-width: 120px;">University</th>
                        <th style="min-width: 150px;">Area of Specialization</th>
                        <th style="min-width: 100px;">Date of Joining in this Institution</th>
                        <th style="min-width: 120px;">Date of Joining in the Department (in case of transfer)</th>
                        <th style="min-width: 100px;">Experience in years in current institute</th>
                        <th style="min-width: 150px;">Designation at Time Joining in this Institution</th>
                        <th style="min-width: 150px;">Present Designation</th>
                        <th style="min-width: 120px;">The date on which Designated as Professor/ Associate Professor if any</th>
                        <th style="min-width: 120px;">Nature of Association (Regular/ Contract/ Ad hoc)</th>
                        <th style="min-width: 120px;">If contractual mention Full time or (Part time or hourly based)</th>
                        <th style="min-width: 80px;">Currently Associated (Y/N)</th>
                        <th style="min-width: 100px;">Date of Leaving if any</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($faculty_data)): ?>
                        <tr>
                            <td colspan="17" style="text-align: center; padding: 20px;">No faculty data found for this department in the database. Please ensure faculty members have updated their profiles.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($faculty_data as $index => $fac): ?>
                            <tr>
                                <td style="text-align: center;"><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($fac['full_name']) ?></td>
                                <td><?= htmlspecialchars($fac['pan_no']) ?></td>
                                <td><?= htmlspecialchars($fac['apaar_id'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($fac['highest_degree']) ?></td>
                                <td><?= htmlspecialchars($fac['university']) ?></td>
                                <td><?= htmlspecialchars($fac['specialization']) ?></td>
                                <td><?= htmlspecialchars($fac['doj_institution']) ?></td>
                                <td><?= htmlspecialchars($fac['doj_department'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($fac['experience_years']) ?></td>
                                <td><?= htmlspecialchars($fac['designation_joining']) ?></td>
                                <td><?= htmlspecialchars($fac['designation_present']) ?></td>
                                <td><?= htmlspecialchars($fac['date_designated_prof'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($fac['association_nature']) ?></td>
                                <td><?= htmlspecialchars($fac['contract_type'] ?: '-') ?></td>
                                <td style="text-align: center;"><?= $fac['is_currently_associated'] ? 'Y' : 'N' ?></td>
                                <td><?= htmlspecialchars($fac['date_of_leaving'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
