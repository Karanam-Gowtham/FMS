<?php
if (!isset($criteria_data)) {
    $criteria_data = [];
}
?>
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
        .section-card { margin-bottom: 2rem; background: #fff; padding: 1.5rem; border-radius: 8px; border: 1px solid #dee2e6; }
        .calc-box { width:100%; text-align:center; border:1px solid #ced4da; padding:5px; box-sizing:border-box; }
        .readonly-box { background:transparent; border:none; font-weight:bold; }
        .btn-add { padding: 6px 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; }
        .btn-remove { padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div id="toast" class="toast">Data saved successfully!</div>

<div class="container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/public/index.php?route=dashboard">Dashboard</a> &raquo; 
        <a href="<?= BASE_URL ?>/public/index.php?route=nba/dashboard&year=<?= urlencode($year) ?>">NBA Accreditation</a> &raquo; 
        Criterion 5 (August 2024 Revised)
    </div>

    <div class="header-row">
        <h1>Criterion 5: Faculty Information (100)</h1>
        <p>Academic Year: <strong><?= htmlspecialchars($year) ?></strong></p>
    </div>

    <div style="margin-bottom: 2rem; background: #fff; padding: 1rem; border-radius: 8px; border: 1px solid #dee2e6; display: flex; align-items: center; gap: 15px;">
        <label style="font-weight: 600; color: #495057;">Select Department:</label>
        <select id="level-select" style="padding: 8px; border: 1px solid #ced4da; border-radius: 4px; min-width: 250px;" onchange="changeDepartment()">
            <?php foreach ($departments as $d): ?>
                <option value="<?= $d['dept_id'] ?>" <?= $d['dept_id'] == $dept_id ? 'selected' : '' ?>>
                    Department: <?= htmlspecialchars($d['dept_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <form id="nbaForm">
        <!-- Table 5A -->
        <div class="section-card">
            <h3>Table No. 5A: Faculty details</h3>
            <div class="section-desc">
                <p style="color: #0d6efd; font-style: italic;">Note: This data is automatically fetched directly from the database based on the faculty profiles associated with this department.</p>
            </div>
            <?php
            $faculty_data = [];
            if (isset($dept_id) && $dept_id > 0) {
                global $conn;
                $sql = "
                    SELECT u.full_name, up.* 
                    FROM users u
                    JOIN user_roles ur ON u.user_id = ur.user_id
                    LEFT JOIN user_profiles up ON u.user_id = up.user_id
                    WHERE ur.dept_id = ?
                    GROUP BY u.user_id ORDER BY u.full_name ASC
                ";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $dept_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    while ($row = $res->fetch_assoc()) { $faculty_data[] = $row; }
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
                            <tr><td colspan="16" style="text-align: center; padding: 20px;">No faculty data found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($faculty_data as $index => $fac): ?>
                                <tr>
                                    <td style="text-align: center;"><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($fac['full_name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['pan_no'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['apaar_id'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($fac['highest_degree'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['university'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['specialization'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['doj_institution'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['experience_years'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['designation_joining'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['designation_present'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['date_designated_prof'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($fac['association_nature'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['contract_type'] ?: '-') ?></td>
                                    <td style="text-align: center;"><?= !empty($fac['is_currently_associated']) ? 'Y' : 'N' ?></td>
                                    <td><?= htmlspecialchars($fac['date_of_leaving'] ?: '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- 5.1 Student-Faculty Ratio -->
        <div class="section-card">
            <h3>5.1 Student-Faculty Ratio (SFR) (30)</h3>
            <div style="overflow-x: auto; margin-top: 15px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="text-align: left;">Item</th>
                            <th>CAY</th>
                            <th>CAYm1</th>
                            <th>CAYm2</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>DS = Total no. of students in all UG and PG programs in the Department</td>
                            <td><input type="number" id="t51_DS_cay" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t51_DS_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t51_DS_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>AS = Total no. of students of all UG and PG programs in allied departments</td>
                            <td><input type="number" id="t51_AS_cay" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t51_AS_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t51_AS_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr style="background:#f1f3f5; font-weight:bold;">
                            <td>S = Total no. of students (DS + AS)</td>
                            <td><input type="text" id="t51_S_cay" class="calc-box readonly-box" readonly></td>
                            <td><input type="text" id="t51_S_caym1" class="calc-box readonly-box" readonly></td>
                            <td><input type="text" id="t51_S_caym2" class="calc-box readonly-box" readonly></td>
                        </tr>
                        <tr>
                            <td>DF = Total no. of faculty members in the Department</td>
                            <td><input type="number" id="t51_DF_cay" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t51_DF_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t51_DF_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>AF = Total no. of faculty members in the allied Departments</td>
                            <td><input type="number" id="t51_AF_cay" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t51_AF_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t51_AF_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr style="background:#f1f3f5; font-weight:bold;">
                            <td>F = Total no. of faculty members (DF + AF)</td>
                            <td><input type="text" id="t51_F_cay" class="calc-box readonly-box" readonly></td>
                            <td><input type="text" id="t51_F_caym1" class="calc-box readonly-box" readonly></td>
                            <td><input type="text" id="t51_F_caym2" class="calc-box readonly-box" readonly></td>
                        </tr>
                        <tr>
                            <td>FF = Faculty members in F who have a 100% teaching load in first-year courses</td>
                            <td><input type="number" id="t51_FF_cay" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t51_FF_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t51_FF_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td>Student Faculty Ratio (SFR) = S / (F - FF)</td>
                            <td><input type="text" id="t51_SFR_cay" class="calc-box readonly-box" readonly></td>
                            <td><input type="text" id="t51_SFR_caym1" class="calc-box readonly-box" readonly></td>
                            <td><input type="text" id="t51_SFR_caym2" class="calc-box readonly-box" readonly></td>
                        </tr>
                        <tr style="background:#e9ecef;">
                            <td colspan="3" style="text-align:right;"><strong>Average SFR for three years:</strong></td>
                            <td><input type="text" id="t51_SFR_avg" class="calc-box readonly-box" readonly style="font-size:1.1rem; color:#d9534f;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5.2 Faculty Qualification -->
        <div class="section-card">
            <h3>5.2 Faculty Qualification (25)</h3>
            <p><strong>FQI = 2.5 x [(10X + 4Y) / RF]</strong> where X = PhD, Y = M.Tech/Masters.</p>
            <div style="overflow-x: auto; margin-top: 15px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th>Year</th>
                            <th>X (Ph.D)</th>
                            <th>Y (Masters)</th>
                            <th>RF (Required Faculty = S/20)</th>
                            <th>FQI = 2.5 x [(10X + 4Y)/RF]</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach(['cay','caym1','caym2'] as $yr): ?>
                            <tr>
                                <td style="text-align:center; font-weight:bold;"><?= strtoupper($yr) ?></td>
                                <td><input type="number" id="t52_X_<?= $yr ?>" class="calc-box" oninput="saveCurrentLevel()"></td>
                                <td><input type="number" id="t52_Y_<?= $yr ?>" class="calc-box" oninput="saveCurrentLevel()"></td>
                                <td><input type="number" id="t52_RF_<?= $yr ?>" class="calc-box readonly-box" readonly></td>
                                <td><input type="text" id="t52_FQI_<?= $yr ?>" class="calc-box readonly-box" readonly></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background:#e9ecef;">
                            <td colspan="4" style="text-align:right;"><strong>Average FQI:</strong></td>
                            <td><input type="text" id="t52_FQI_avg" class="calc-box readonly-box" readonly style="font-size:1.1rem; color:#d9534f;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5.3 Faculty Cadre Proportion -->
        <div class="section-card">
            <h3>5.3 Faculty Cadre Proportion (25)</h3>
            <div style="overflow-x: auto; margin-top: 15px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th rowspan="2">Year</th>
                            <th colspan="2">Professors</th>
                            <th colspan="2">Associate Professors</th>
                            <th colspan="2">Assistant Professors</th>
                        </tr>
                        <tr>
                            <th>Required (RF1)</th>
                            <th>Available (AF1)</th>
                            <th>Required (RF2)</th>
                            <th>Available (AF2)</th>
                            <th>Required (RF3)</th>
                            <th>Available (AF3)</th>
                        </tr>
                    </thead>
                    <tbody id="cadre-body">
                        <?php $years = ['CAY', 'CAYm1', 'CAYm2']; ?>
                        <?php foreach($years as $yr): ?>
                            <tr>
                                <td style="text-align:center; font-weight:bold;"><?= $yr ?></td>
                                <td><input type="number" id="t53_prof_req_<?= $yr ?>" class="calc-box readonly-box" readonly></td>
                                <td><input type="number" id="t53_prof_avl_<?= $yr ?>" class="calc-box" oninput="saveCurrentLevel()"></td>
                                <td><input type="number" id="t53_asso_req_<?= $yr ?>" class="calc-box readonly-box" readonly></td>
                                <td><input type="number" id="t53_asso_avl_<?= $yr ?>" class="calc-box" oninput="saveCurrentLevel()"></td>
                                <td><input type="number" id="t53_asst_req_<?= $yr ?>" class="calc-box readonly-box" readonly></td>
                                <td><input type="number" id="t53_asst_avl_<?= $yr ?>" class="calc-box" oninput="saveCurrentLevel()"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5.4 Visiting/Adjunct Faculty -->
        <div class="section-card">
            <h3>5.4 Visiting/Adjunct Faculty/Professor of Practice (10)</h3>
            <button type="button" class="btn-add" onclick="addVisitingFaculty()">+ Add Visiting Faculty Row</button>
            <div style="overflow-x: auto; margin-top: 15px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th>Year</th>
                            <th>Name of the Person</th>
                            <th>Designation & Organization</th>
                            <th>Name of the Course</th>
                            <th>No. of hours handled</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="visiting-body">
                        <!-- Populated by JS -->
                    </tbody>
                    <tfoot>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td colspan="4" style="text-align:right;">Total CAYm1 Hours:</td>
                            <td id="t54_tot_caym1" style="text-align:center;">0</td><td></td>
                        </tr>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td colspan="4" style="text-align:right;">Total CAYm2 Hours:</td>
                            <td id="t54_tot_caym2" style="text-align:center;">0</td><td></td>
                        </tr>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td colspan="4" style="text-align:right;">Total CAYm3 Hours:</td>
                            <td id="t54_tot_caym3" style="text-align:center;">0</td><td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- 5.5 Faculty Retention -->
        <div class="section-card">
            <h3>5.5 Faculty Retention (10)</h3>
            <div style="overflow-x: auto; margin-top: 15px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="text-align: left;">Item</th>
                            <th>CAYm1</th>
                            <th>CAYm2</th>
                            <th>CAYm3</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>RF = No. of required faculty to adhere to 20:1 ratio</td>
                            <td><input type="number" id="t55_RF_caym1" class="calc-box readonly-box" readonly></td>
                            <td><input type="number" id="t55_RF_caym2" class="calc-box readonly-box" readonly></td>
                            <td><input type="number" id="t55_RF_caym3" class="calc-box" oninput="saveCurrentLevel()" placeholder="(Enter manually)"></td>
                        </tr>
                        <tr>
                            <td>AF = The no. of available faculty members in Dept & allied</td>
                            <td><input type="number" id="t55_AF_caym1" class="calc-box readonly-box" readonly></td>
                            <td><input type="number" id="t55_AF_caym2" class="calc-box readonly-box" readonly></td>
                            <td><input type="number" id="t55_AF_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>A = Faculty with < 1 year experience</td>
                            <td><input type="number" id="t55_A_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t55_A_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t55_A_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>B = Faculty with > 1 year and < 2 years experience</td>
                            <td><input type="number" id="t55_B_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t55_B_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t55_B_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>C = Faculty with > 2 years and < 3 years experience</td>
                            <td><input type="number" id="t55_C_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t55_C_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t55_C_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>D = Faculty with > 3 years and < 4 years experience</td>
                            <td><input type="number" id="t55_D_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t55_D_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t55_D_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>E = Faculty with > 4 years experience</td>
                            <td><input type="number" id="t55_E_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t55_E_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t55_E_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr style="background:#f1f3f5; font-weight:bold;">
                            <td>FR = (((A*0)+(B*1)+(C*2)+(D*3)+(E*4))/RF) * 2.50</td>
                            <td><input type="text" id="t55_FR_caym1" class="calc-box readonly-box" readonly></td>
                            <td><input type="text" id="t55_FR_caym2" class="calc-box readonly-box" readonly></td>
                            <td><input type="text" id="t55_FR_caym3" class="calc-box readonly-box" readonly></td>
                        </tr>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td colspan="3" style="text-align:right;">Average FR (Limited to 10):</td>
                            <td><input type="text" id="t55_FR_avg" class="calc-box readonly-box" readonly style="color:#d9534f; font-size:1.1rem;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 15px; margin-bottom: 4rem;">
            <button type="button" class="btn-save" onclick="saveCriterionData()" style="padding: 10px 20px; font-weight: bold; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Criterion 5</button>
        </div>
    </form>
</div>

<script>
    let levelData = {};
    let currentLevel = 'dept_<?= $dept_id ?>';
    
    const rawData = <?= json_encode($criteria_data ?? []) ?>;
    let initialData = {};
    if (!Array.isArray(rawData)) { initialData = rawData; }

    function changeDepartment() {
        const select = document.getElementById('level-select');
        window.location.href = '?route=nba/criterion&id=5&year=<?= urlencode($year) ?>&dept_id=' + select.value;
    }

    const simpleInputs = [
        't51_DS_cay', 't51_AS_cay', 't51_DF_cay', 't51_AF_cay', 't51_FF_cay',
        't51_DS_caym1', 't51_AS_caym1', 't51_DF_caym1', 't51_AF_caym1', 't51_FF_caym1',
        't51_DS_caym2', 't51_AS_caym2', 't51_DF_caym2', 't51_AF_caym2', 't51_FF_caym2',
        
        't52_X_cay', 't52_Y_cay', 't52_X_caym1', 't52_Y_caym1', 't52_X_caym2', 't52_Y_caym2',
        
        't53_prof_avl_CAY', 't53_asso_avl_CAY', 't53_asst_avl_CAY',
        't53_prof_avl_CAYm1', 't53_asso_avl_CAYm1', 't53_asst_avl_CAYm1',
        't53_prof_avl_CAYm2', 't53_asso_avl_CAYm2', 't53_asst_avl_CAYm2',
        
        't55_RF_caym3', 't55_AF_caym3',
        't55_A_caym1', 't55_B_caym1', 't55_C_caym1', 't55_D_caym1', 't55_E_caym1',
        't55_A_caym2', 't55_B_caym2', 't55_C_caym2', 't55_D_caym2', 't55_E_caym2',
        't55_A_caym3', 't55_B_caym3', 't55_C_caym3', 't55_D_caym3', 't55_E_caym3'
    ];

    let visitingFacultyList = [];

    function initData() {
        levelData = initialData.levelData || {};
        if (!levelData[currentLevel]) levelData[currentLevel] = {};

        const data = levelData[currentLevel];
        simpleInputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = data[id] || '';
        });

        visitingFacultyList = data.visitingFaculty || [];
        renderVisitingFaculty();

        calculateAll();
    }

    function saveCurrentLevel() {
        if (!levelData[currentLevel]) levelData[currentLevel] = {};
        
        simpleInputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) levelData[currentLevel][id] = el.value;
        });

        levelData[currentLevel].visitingFaculty = visitingFacultyList;

        calculateAll();
    }

    function addVisitingFaculty() {
        visitingFacultyList.push({ year: 'CAYm1', name: '', org: '', course: '', hours: 0 });
        renderVisitingFaculty();
        saveCurrentLevel();
    }

    function removeVisitingFaculty(idx) {
        visitingFacultyList.splice(idx, 1);
        renderVisitingFaculty();
        saveCurrentLevel();
    }

    function updateVisiting(idx, field, value) {
        visitingFacultyList[idx][field] = value;
        saveCurrentLevel();
    }

    function renderVisitingFaculty() {
        const tbody = document.getElementById('visiting-body');
        tbody.innerHTML = '';
        let tot1 = 0, tot2 = 0, tot3 = 0;
        
        visitingFacultyList.forEach((vf, idx) => {
            if (vf.year === 'CAYm1') tot1 += parseFloat(vf.hours)||0;
            if (vf.year === 'CAYm2') tot2 += parseFloat(vf.hours)||0;
            if (vf.year === 'CAYm3') tot3 += parseFloat(vf.hours)||0;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <select onchange="updateVisiting(${idx}, 'year', this.value); renderVisitingFaculty();">
                        <option value="CAYm1" ${vf.year==='CAYm1'?'selected':''}>CAYm1</option>
                        <option value="CAYm2" ${vf.year==='CAYm2'?'selected':''}>CAYm2</option>
                        <option value="CAYm3" ${vf.year==='CAYm3'?'selected':''}>CAYm3</option>
                    </select>
                </td>
                <td><input type="text" style="width:100%" value="${vf.name}" onchange="updateVisiting(${idx}, 'name', this.value)"></td>
                <td><input type="text" style="width:100%" value="${vf.org}" onchange="updateVisiting(${idx}, 'org', this.value)"></td>
                <td><input type="text" style="width:100%" value="${vf.course}" onchange="updateVisiting(${idx}, 'course', this.value)"></td>
                <td><input type="number" style="width:100%" value="${vf.hours}" onchange="updateVisiting(${idx}, 'hours', this.value); renderVisitingFaculty();"></td>
                <td style="text-align:center;"><button type="button" class="btn-remove" onclick="removeVisitingFaculty(${idx})">&times;</button></td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('t54_tot_caym1').innerText = tot1;
        document.getElementById('t54_tot_caym2').innerText = tot2;
        document.getElementById('t54_tot_caym3').innerText = tot3;
    }

    function calculateAll() {
        let rfMap = {};
        let afMap = {};

        // 5.1
        let totalSFR = 0, validYears = 0;
        ['cay', 'caym1', 'caym2'].forEach(yr => {
            const ds = parseFloat(document.getElementById('t51_DS_' + yr).value) || 0;
            const as = parseFloat(document.getElementById('t51_AS_' + yr).value) || 0;
            const df = parseFloat(document.getElementById('t51_DF_' + yr).value) || 0;
            const af = parseFloat(document.getElementById('t51_AF_' + yr).value) || 0;
            const ff = parseFloat(document.getElementById('t51_FF_' + yr).value) || 0;
            
            const S = ds + as;
            const F = df + af;
            
            document.getElementById('t51_S_' + yr).value = S;
            document.getElementById('t51_F_' + yr).value = F;

            let rf = S / 20;
            rfMap[yr] = rf;
            afMap[yr] = F;

            const sfrEl = document.getElementById('t51_SFR_' + yr);
            if ((F - ff) > 0) {
                let sfr = S / (F - ff);
                sfrEl.value = sfr.toFixed(2);
                totalSFR += sfr;
                validYears++;
            } else {
                sfrEl.value = '0.00';
            }
        });
        document.getElementById('t51_SFR_avg').value = validYears > 0 ? (totalSFR / validYears).toFixed(2) : '0.00';

        // 5.2
        let totalFQI = 0, validFQI = 0;
        ['cay', 'caym1', 'caym2'].forEach(yr => {
            const x = parseFloat(document.getElementById('t52_X_' + yr).value) || 0;
            const y = parseFloat(document.getElementById('t52_Y_' + yr).value) || 0;
            const rf = rfMap[yr] || 0;
            
            document.getElementById('t52_RF_' + yr).value = rf.toFixed(2);
            
            const fqiEl = document.getElementById('t52_FQI_' + yr);
            if (rf > 0) {
                let fqi = 2.5 * ((10 * x + 4 * y) / rf);
                fqiEl.value = fqi.toFixed(2);
                totalFQI += fqi;
                validFQI++;
            } else {
                fqiEl.value = '0.00';
            }
        });
        document.getElementById('t52_FQI_avg').value = validFQI > 0 ? (totalFQI / validFQI).toFixed(2) : '0.00';

        // 5.3
        ['CAY', 'CAYm1', 'CAYm2'].forEach(yr => {
            const lowerYr = yr.toLowerCase();
            const rf = rfMap[lowerYr] || 0;
            
            document.getElementById('t53_prof_req_' + yr).value = (rf / 9).toFixed(2);
            document.getElementById('t53_asso_req_' + yr).value = ((rf * 2) / 9).toFixed(2);
            document.getElementById('t53_asst_req_' + yr).value = ((rf * 6) / 9).toFixed(2);
        });

        // 5.5
        document.getElementById('t55_RF_caym1').value = (rfMap['caym1'] || 0).toFixed(2);
        document.getElementById('t55_RF_caym2').value = (rfMap['caym2'] || 0).toFixed(2);
        
        document.getElementById('t55_AF_caym1').value = (afMap['caym1'] || 0);
        document.getElementById('t55_AF_caym2').value = (afMap['caym2'] || 0);

        let totalFR = 0, validFR = 0;
        ['caym1', 'caym2', 'caym3'].forEach(yr => {
            let rf = parseFloat(document.getElementById('t55_RF_' + yr).value) || 0;
            
            const a = parseFloat(document.getElementById('t55_A_' + yr).value) || 0;
            const b = parseFloat(document.getElementById('t55_B_' + yr).value) || 0;
            const c = parseFloat(document.getElementById('t55_C_' + yr).value) || 0;
            const d = parseFloat(document.getElementById('t55_D_' + yr).value) || 0;
            const e = parseFloat(document.getElementById('t55_E_' + yr).value) || 0;
            
            const frEl = document.getElementById('t55_FR_' + yr);
            if (rf > 0) {
                let fr = (((a*0) + (b*1) + (c*2) + (d*3) + (e*4)) / rf) * 2.50;
                if (fr > 10) fr = 10;
                frEl.value = fr.toFixed(2);
                totalFR += fr;
                validFR++;
            } else {
                frEl.value = '0.00';
            }
        });
        
        const avgFR = validFR > 0 ? (totalFR / validFR) : 0;
        document.getElementById('t55_FR_avg').value = (avgFR > 10 ? 10 : avgFR).toFixed(2);
    }

    function saveCriterionData() {
        saveCurrentLevel();

        const payload = { levelData: levelData };
        const formData = new FormData(document.getElementById('nbaForm'));
        formData.append('json_data', JSON.stringify(payload));
        formData.append('year', '<?= htmlspecialchars($year) ?>');

        const btn = document.querySelector('.btn-save');
        const oldText = btn.innerText;
        btn.innerText = 'Saving...';
        btn.disabled = true;

        fetch('<?= BASE_URL ?>/public/index.php?route=api/nba/save_criterion&id=5', {
            method: 'POST', body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.innerText = oldText; btn.disabled = false;
            if (data.success) {
                const toast = document.getElementById('toast');
                toast.style.display = 'block';
                setTimeout(() => toast.style.display = 'none', 3000);
            } else { alert('Error: ' + (data.error || 'Unknown error')); }
        })
        .catch(err => {
            console.error(err);
            btn.innerText = oldText; btn.disabled = false;
            alert('A network error occurred.');
        });
    }

    window.onload = function() { initData(); };
</script>
</body>
</html>
