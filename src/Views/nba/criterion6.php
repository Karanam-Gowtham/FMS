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
    <title><?= $page_title ?? 'Criterion 6' ?> &mdash; FMS</title>
    <link href="<?= BASE_URL ?>/assets/css/layout.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/nba_module.css" rel="stylesheet">
    <style>
        .nba-table th, .nba-table td { padding: 8px; text-align: left; }
        .nba-table thead { font-weight: bold; background: #fff3cd; }
        .nba-table th { text-align: center; vertical-align: middle; }
        .section-card { margin-bottom: 2rem; background: #fff; padding: 1.5rem; border-radius: 8px; border: 1px solid #dee2e6; }
        .calc-box { width:100%; text-align:center; border:1px solid #ced4da; padding:5px; box-sizing:border-box; }
        .readonly-box { background:transparent; border:none; font-weight:bold; }
        .btn-add { padding: 6px 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; margin-bottom: 10px; }
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
        Criterion 6
    </div>

    <div class="header-row">
        <h1>Criterion 6: Faculty Contribution (120)</h1>
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

        <!-- 6.1 Professional Development Activities -->
        <h2 style="border-bottom: 2px solid #0d6efd; padding-bottom: 5px; color: #0d6efd;">6.1 Professional Development Activities (60)</h2>

        <!-- 6.1.1 Memberships -->
        <div class="section-card">
            <h3>6.1.1 Memberships in Profession Societies at National/International Levels (05)</h3>
            <button type="button" class="btn-add" onclick="addRow('memberships')">+ Add Faculty Membership</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Name of the Faculty</th>
                            <th>Name of the Professional Society/Body</th>
                            <th>Grade/Level/Position</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-memberships"></tbody>
                </table>
            </div>
        </div>

        <!-- 6.1.2.1 Resource Persons -->
        <div class="section-card">
            <h3>6.1.2.1 Faculty as Resource Persons in STTPs/FDPs (05)</h3>
            <button type="button" class="btn-add" onclick="addRow('resource_persons')">+ Add Resource Person Record</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Name of Faculty</th>
                            <th>Name of the STTP/FDP</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Organized by</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-resource_persons"></tbody>
                </table>
            </div>
        </div>

        <!-- 6.1.2.2 STTP Participation -->
        <div class="section-card">
            <h3>6.1.2.2 Faculty Members' Participation in STTPs/FDPs (05)</h3>
            <p>Participation in 2 to 5 days = 3 Points. >5 days = 5 points. (Max 5 per faculty).</p>
            <div style="display:flex; gap:15px; margin-bottom:15px;">
                <label><strong>RF for CAYm1:</strong> <input type="number" id="t6122_rf_caym1" class="calc-box" style="width:80px;" oninput="calc6122()"></label>
                <label><strong>RF for CAYm2:</strong> <input type="number" id="t6122_rf_caym2" class="calc-box" style="width:80px;" oninput="calc6122()"></label>
                <label><strong>RF for CAYm3:</strong> <input type="number" id="t6122_rf_caym3" class="calc-box" style="width:80px;" oninput="calc6122()"></label>
            </div>
            <button type="button" class="btn-add" onclick="addRow('participation')">+ Add Faculty Participation Points</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th rowspan="2">Name of the Faculty</th>
                            <th colspan="3">Max. 5 per Faculty</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>CAYm1</th>
                            <th>CAYm2</th>
                            <th>CAYm3</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-participation"></tbody>
                    <tfoot>
                        <tr style="background:#f1f3f5; font-weight:bold;">
                            <td style="text-align:right;">Sum</td>
                            <td><input type="text" id="t6122_sum_caym1" class="calc-box readonly-box" readonly></td>
                            <td><input type="text" id="t6122_sum_caym2" class="calc-box readonly-box" readonly></td>
                            <td><input type="text" id="t6122_sum_caym3" class="calc-box readonly-box" readonly></td>
                            <td></td>
                        </tr>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td style="text-align:right;">Assessment Points (AP) = Sum / (0.5 * RF) <br><small>(Limited to 5)</small></td>
                            <td><input type="text" id="t6122_ap_caym1" class="calc-box readonly-box" readonly style="color:#d9534f;"></td>
                            <td><input type="text" id="t6122_ap_caym2" class="calc-box readonly-box" readonly style="color:#d9534f;"></td>
                            <td><input type="text" id="t6122_ap_caym3" class="calc-box readonly-box" readonly style="color:#d9534f;"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- 6.1.3 & 6.1.4 MOOCs -->
        <div class="section-card">
            <h3>6.1.3 & 6.1.4 Development and Certification of MOOCs (15)</h3>
            <button type="button" class="btn-add" onclick="addRow('mooc_dev')">+ Add MOOC Developed (6.1.3)</button>
            <div style="overflow-x: auto; margin-bottom: 20px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr><th colspan="4">6.1.3 Courses Developed (Swayam/e-PG, etc.)</th></tr>
                        <tr><th>S.N.</th><th>Name of Faculty</th><th>Course Developed (Platform)</th><th>Action</th></tr>
                    </thead>
                    <tbody id="tbody-mooc_dev"></tbody>
                </table>
            </div>

            <button type="button" class="btn-add" onclick="addRow('mooc_cert')">+ Add MOOC Certification (6.1.4)</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr><th colspan="6">6.1.4 Courses Certified</th></tr>
                        <tr><th>S.N.</th><th>Name of Faculty</th><th>Course Passed</th><th>Course Offered By</th><th>Grade</th><th>Action</th></tr>
                    </thead>
                    <tbody id="tbody-mooc_cert"></tbody>
                </table>
            </div>
        </div>

        <!-- 6.1.5 FDP Organized -->
        <div class="section-card">
            <h3>6.1.5 FDP/STTP Organized by the Department (10)</h3>
            <button type="button" class="btn-add" onclick="addRow('fdp_org')">+ Add Organized FDP/STTP</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Name of Program</th>
                            <th>Date</th>
                            <th>Duration (Days)</th>
                            <th>Speaker & Org</th>
                            <th>No. Attended</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-fdp_org"></tbody>
                </table>
            </div>
        </div>

        <!-- 6.1.6 Innovative Projects -->
        <div class="section-card">
            <h3>6.1.6 Faculty Support in Student Innovative Projects (10)</h3>
            <button type="button" class="btn-add" onclick="addRow('innovative')">+ Add Innovative Project Support</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Faculty Name</th>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Place</th>
                            <th>Website Link</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-innovative"></tbody>
                </table>
            </div>
        </div>

        <!-- 6.1.7 Industry Collab -->
        <div class="section-card">
            <h3>6.1.7 Faculty Internship/Training/Collaboration with Industry (10)</h3>
            <button type="button" class="btn-add" onclick="addRow('industry')">+ Add Industry Collab</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Faculty Name</th>
                            <th>Internship/Training Name</th>
                            <th>Company & Place</th>
                            <th>Duration</th>
                            <th>Outcomes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-industry"></tbody>
                </table>
            </div>
        </div>

        <!-- 6.2 Research & Development -->
        <h2 style="border-bottom: 2px solid #0d6efd; padding-bottom: 5px; color: #0d6efd; margin-top:3rem;">6.2 Research and Development Activities (60)</h2>

        <!-- 6.2.1 & 6.2.2 Publications & PhDs -->
        <div class="section-card">
            <h3>6.2.1 Academic Research (10) & 6.2.2 Ph.D. Student Details (05)</h3>
            <div style="overflow-x: auto; margin-bottom:20px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr><th style="text-align:left;">Table 6.2.1.1 Publication Details</th><th>CAYm1</th><th>CAYm2</th><th>CAYm3</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Peer reviewed journal papers</td>
                            <td><input type="number" id="t621_j_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t621_j_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t621_j_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>Peer reviewed conference papers</td>
                            <td><input type="number" id="t621_c_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t621_c_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t621_c_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>Books/book chapters</td>
                            <td><input type="number" id="t621_b_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t621_b_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t621_b_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr><th style="text-align:left;">Table 6.2.2.1 Ph.D. Details</th><th>CAYm1</th><th>CAYm2</th><th>CAYm3</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Students enrolled for Ph.D</td>
                            <td><input type="number" id="t622_e_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t622_e_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t622_e_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>Students graduated for Ph.D</td>
                            <td><input type="number" id="t622_g_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t622_g_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t622_g_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 6.2.3 Development Activities -->
        <div class="section-card">
            <h3>6.2.3 Development Activities (10)</h3>
            <p>Provide details of patents granted/published, working models, and prototypes developed by faculty.</p>
            <textarea name="text_6_2_3" id="text_6_2_3" style="width:100%; height:80px; padding:10px; border:1px solid #ced4da;" placeholder="Describe patents and prototypes..." onchange="saveCurrentLevel()"></textarea>
            <div class="file-upload-row" style="margin-top: 10px;">
                <div><strong>Proof/Documentation PDF</strong></div>
                <input type="file" name="file_6_2_3" accept=".pdf">
            </div>
        </div>

        <!-- 6.2.4 & 6.2.5 Sponsored Research & Consultancy -->
        <div class="section-card">
            <h3>6.2.4 Sponsored Research (15) & 6.2.5 Consultancy (15)</h3>
            
            <button type="button" class="btn-add" onclick="addRow('sponsored')">+ Add Sponsored Research (6.2.4)</button>
            <div style="overflow-x: auto; margin-bottom:20px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr><th>S.N.</th><th>Year</th><th>PI Name</th><th>Co-PI</th><th>Dept</th><th>Title</th><th>Agency</th><th>Duration</th><th>Amount (Lacs)</th><th>Action</th></tr>
                    </thead>
                    <tbody id="tbody-sponsored"></tbody>
                    <tfoot>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td colspan="8" style="text-align:right;">Total Amount Received for Past 3 Years (Lacs):</td>
                            <td id="t624_tot_lacs" style="text-align:center;">0</td><td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <button type="button" class="btn-add" onclick="addRow('consultancy')">+ Add Consultancy Project (6.2.5)</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr><th>S.N.</th><th>Year</th><th>PI Name</th><th>Co-PI</th><th>Dept</th><th>Title</th><th>Agency</th><th>Duration</th><th>Amount (Lacs)</th><th>Action</th></tr>
                    </thead>
                    <tbody id="tbody-consultancy"></tbody>
                    <tfoot>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td colspan="8" style="text-align:right;">Total Amount Received for Past 3 Years (Lacs):</td>
                            <td id="t625_tot_lacs" style="text-align:center;">0</td><td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- 6.2.6 Seed Money -->
        <div class="section-card">
            <h3>6.2.6 Institution Seed Money / Internal Research Grant (05)</h3>
            <button type="button" class="btn-add" onclick="addRow('seed_money')">+ Add Seed Money Grant</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Faculty Name</th>
                            <th>Project/Support Title</th>
                            <th>Duration</th>
                            <th>Amount Recv (Lacs)</th>
                            <th>Amount Utilized (Lacs)</th>
                            <th>Outcomes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-seed_money"></tbody>
                    <tfoot>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td colspan="5" style="text-align:right;">Total Received (Lacs):</td>
                            <td id="t626_tot_recv" style="text-align:center;">0</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 15px; margin-bottom: 4rem;">
            <button type="button" class="btn-save" onclick="saveCriterionData()" style="padding: 10px 20px; font-weight: bold; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Criterion 6</button>
        </div>
    </form>
</div>

<script>
    let levelData = {};
    let currentLevel = 'dept_<?= $dept_id ?>';
    
    const rawData = <?= json_encode($criteria_data ?? []) ?>;
    let initialData = {};
    if (!Array.isArray(rawData)) { initialData = rawData; }

    const simpleInputs = [
        't6122_rf_caym1', 't6122_rf_caym2', 't6122_rf_caym3',
        't621_j_caym1', 't621_j_caym2', 't621_j_caym3',
        't621_c_caym1', 't621_c_caym2', 't621_c_caym3',
        't621_b_caym1', 't621_b_caym2', 't621_b_caym3',
        't622_e_caym1', 't622_e_caym2', 't622_e_caym3',
        't622_g_caym1', 't622_g_caym2', 't622_g_caym3',
        'text_6_2_3'
    ];

    const lists = {
        'memberships': { keys: ['fac', 'soc', 'grade'] },
        'resource_persons': { keys: ['year', 'fac', 'sttp', 'date', 'loc', 'org'] },
        'participation': { keys: ['fac', 'c1', 'c2', 'c3'] },
        'mooc_dev': { keys: ['fac', 'course'] },
        'mooc_cert': { keys: ['fac', 'course', 'offered', 'grade'] },
        'fdp_org': { keys: ['year', 'prog', 'date', 'dur', 'speaker', 'att'] },
        'innovative': { keys: ['year', 'fac', 'event', 'date', 'place', 'link'] },
        'industry': { keys: ['fac', 'train', 'comp', 'dur', 'out'] },
        'sponsored': { keys: ['year', 'pi', 'copi', 'dept', 'title', 'agency', 'dur', 'amt'] },
        'consultancy': { keys: ['year', 'pi', 'copi', 'dept', 'title', 'agency', 'dur', 'amt'] },
        'seed_money': { keys: ['year', 'fac', 'title', 'dur', 'recv', 'util', 'out'] }
    };

    function changeDepartment() {
        const select = document.getElementById('level-select');
        window.location.href = '?route=nba/criterion&id=6&year=<?= urlencode($year) ?>&dept_id=' + select.value;
    }

    function initData() {
        levelData = initialData.levelData || {};
        if (!levelData[currentLevel]) levelData[currentLevel] = {};

        const data = levelData[currentLevel];
        simpleInputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = data[id] || '';
        });

        // Initialize lists
        Object.keys(lists).forEach(listName => {
            if (!data[listName]) data[listName] = [];
            renderTable(listName);
        });

        calc6122();
        calcSums();
    }

    function saveCurrentLevel() {
        if (!levelData[currentLevel]) levelData[currentLevel] = {};
        
        simpleInputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) levelData[currentLevel][id] = el.value;
        });

        calc6122();
    }

    function addRow(listName) {
        let obj = {};
        lists[listName].keys.forEach(k => {
            if (k === 'year') obj[k] = 'CAYm1';
            else if (k === 'c1' || k === 'c2' || k === 'c3' || k === 'amt' || k === 'recv' || k === 'util') obj[k] = 0;
            else obj[k] = '';
        });
        levelData[currentLevel][listName].push(obj);
        renderTable(listName);
        calcSums();
    }

    function removeRow(listName, idx) {
        levelData[currentLevel][listName].splice(idx, 1);
        renderTable(listName);
        calcSums();
    }

    function updateRow(listName, idx, key, val) {
        levelData[currentLevel][listName][idx][key] = val;
        if (listName === 'participation') calc6122();
        if (['sponsored','consultancy','seed_money'].includes(listName)) calcSums();
    }

    function yrSel(val) {
        return `<select onchange="return this.value"><option value="CAYm1" ${val==='CAYm1'?'selected':''}>CAYm1</option><option value="CAYm2" ${val==='CAYm2'?'selected':''}>CAYm2</option><option value="CAYm3" ${val==='CAYm3'?'selected':''}>CAYm3</option></select>`;
    }

    function renderTable(listName) {
        const tbody = document.getElementById('tbody-' + listName);
        if (!tbody) return;
        tbody.innerHTML = '';
        
        const rows = levelData[currentLevel][listName];
        rows.forEach((row, idx) => {
            const tr = document.createElement('tr');
            let html = `<td>${idx + 1}</td>`;
            lists[listName].keys.forEach(k => {
                if (k === 'year') {
                    html += `<td><select onchange="updateRow('${listName}', ${idx}, '${k}', this.value); renderTable('${listName}'); calcSums();">
                        <option value="CAYm1" ${row[k]==='CAYm1'?'selected':''}>CAYm1</option>
                        <option value="CAYm2" ${row[k]==='CAYm2'?'selected':''}>CAYm2</option>
                        <option value="CAYm3" ${row[k]==='CAYm3'?'selected':''}>CAYm3</option>
                    </select></td>`;
                } else if (['c1','c2','c3','amt','recv','util'].includes(k)) {
                    html += `<td><input type="number" style="width:100%" value="${row[k]}" oninput="updateRow('${listName}', ${idx}, '${k}', this.value)"></td>`;
                } else {
                    html += `<td><input type="text" style="width:100%" value="${row[k]}" oninput="updateRow('${listName}', ${idx}, '${k}', this.value)"></td>`;
                }
            });
            html += `<td style="text-align:center;"><button type="button" class="btn-remove" onclick="removeRow('${listName}', ${idx})">&times;</button></td>`;
            tr.innerHTML = html;
            tbody.appendChild(tr);
        });
    }

    function calc6122() {
        if (!levelData[currentLevel]) return;
        const rows = levelData[currentLevel]['participation'] || [];
        let s1=0, s2=0, s3=0;
        rows.forEach(r => {
            s1 += parseFloat(r.c1)||0;
            s2 += parseFloat(r.c2)||0;
            s3 += parseFloat(r.c3)||0;
        });

        document.getElementById('t6122_sum_caym1').value = s1;
        document.getElementById('t6122_sum_caym2').value = s2;
        document.getElementById('t6122_sum_caym3').value = s3;

        const rf1 = parseFloat(document.getElementById('t6122_rf_caym1').value)||0;
        const rf2 = parseFloat(document.getElementById('t6122_rf_caym2').value)||0;
        const rf3 = parseFloat(document.getElementById('t6122_rf_caym3').value)||0;

        const el1 = document.getElementById('t6122_ap_caym1');
        const el2 = document.getElementById('t6122_ap_caym2');
        const el3 = document.getElementById('t6122_ap_caym3');

        el1.value = rf1 > 0 ? Math.min(5, s1/(0.5*rf1)).toFixed(2) : '0.00';
        el2.value = rf2 > 0 ? Math.min(5, s2/(0.5*rf2)).toFixed(2) : '0.00';
        el3.value = rf3 > 0 ? Math.min(5, s3/(0.5*rf3)).toFixed(2) : '0.00';
    }

    function calcSums() {
        if (!levelData[currentLevel]) return;
        
        let s_spons = 0;
        (levelData[currentLevel]['sponsored']||[]).forEach(r => { s_spons += parseFloat(r.amt)||0; });
        const eS = document.getElementById('t624_tot_lacs');
        if(eS) eS.innerText = s_spons.toFixed(2);

        let s_cons = 0;
        (levelData[currentLevel]['consultancy']||[]).forEach(r => { s_cons += parseFloat(r.amt)||0; });
        const eC = document.getElementById('t625_tot_lacs');
        if(eC) eC.innerText = s_cons.toFixed(2);

        let s_seed = 0;
        (levelData[currentLevel]['seed_money']||[]).forEach(r => { s_seed += parseFloat(r.recv)||0; });
        const eSd = document.getElementById('t626_tot_recv');
        if(eSd) eSd.innerText = s_seed.toFixed(2);
    }

    function saveCriterionData() {
        saveCurrentLevel();

        const payload = { levelData: levelData };
        const formData = new FormData(document.getElementById('nbaForm'));
        formData.append('json_data', JSON.stringify(payload));
        formData.append('dept_id', '<?= $dept_id ?>'); formData.append('year', '<?= htmlspecialchars($year) ?>');

        const btn = document.querySelector('.btn-save');
        const oldText = btn.innerText;
        btn.innerText = 'Saving...';
        btn.disabled = true;

        fetch('<?= BASE_URL ?>/public/index.php?route=api/nba/save_criterion&id=6', {
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


