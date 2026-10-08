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
    <title><?= $page_title ?? 'Criterion 9' ?> &mdash; FMS</title>
    <link href="<?= BASE_URL ?>/assets/css/layout.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/nba_module.css" rel="stylesheet">
    <style>
        .nba-table th, .nba-table td { padding: 8px; text-align: left; }
        .nba-table thead { font-weight: bold; background: #fff3cd; }
        .nba-table th { text-align: center; vertical-align: middle; }
        .section-card { margin-bottom: 2rem; background: #fff; padding: 1.5rem; border-radius: 8px; border: 1px solid #dee2e6; }
        .btn-add { padding: 6px 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; margin-bottom: 10px; }
        .btn-remove { padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; }
        .calc-box { width:100%; text-align:center; border:1px solid #ced4da; padding:5px; box-sizing:border-box; }
        .textarea-box { width:100%; height:100px; padding:10px; border:1px solid #ced4da; margin-top:10px; border-radius:4px; resize:vertical; font-family:inherit; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div id="toast" class="toast">Data saved successfully!</div>

<div class="container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/public/index.php?route=dashboard">Dashboard</a> &raquo; 
        <a href="<?= BASE_URL ?>/public/index.php?route=nba/dashboard&year=<?= urlencode($year) ?>">NBA Accreditation</a> &raquo; 
        Criterion 9
    </div>

    <div class="header-row">
        <h1>Criterion 9: Student Support System and Governance (120)</h1>
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

        <!-- 9.1 FYSFR -->
        <div class="section-card">
            <h3>9.1. First Year Student-Faculty Ratio (FYSFR) (05)</h3>
            <p>Data for first year courses to calculate the FYSFR. Note: Percentage = ((NS1*0.8) + (NS2*0.2)) / RF4.</p>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Sanctioned intake (S4)</th>
                            <th>Required faculty (RF4 = S4/20)</th>
                            <th>Basic Sci/Hum faculty (NS1)</th>
                            <th>Engg Sci faculty (NS2)</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CAY</td>
                            <td><input type="number" id="t91_s4_cay" class="calc-box" oninput="calc91()"></td>
                            <td><input type="text" id="t91_rf4_cay" class="calc-box" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" id="t91_ns1_cay" class="calc-box" oninput="calc91()"></td>
                            <td><input type="number" id="t91_ns2_cay" class="calc-box" oninput="calc91()"></td>
                            <td><input type="text" id="t91_perc_cay" class="calc-box" readonly style="background:#f8f9fa;"></td>
                        </tr>
                        <tr>
                            <td>CAYm1</td>
                            <td><input type="number" id="t91_s4_caym1" class="calc-box" oninput="calc91()"></td>
                            <td><input type="text" id="t91_rf4_caym1" class="calc-box" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" id="t91_ns1_caym1" class="calc-box" oninput="calc91()"></td>
                            <td><input type="number" id="t91_ns2_caym1" class="calc-box" oninput="calc91()"></td>
                            <td><input type="text" id="t91_perc_caym1" class="calc-box" readonly style="background:#f8f9fa;"></td>
                        </tr>
                        <tr>
                            <td>CAYm2</td>
                            <td><input type="number" id="t91_s4_caym2" class="calc-box" oninput="calc91()"></td>
                            <td><input type="text" id="t91_rf4_caym2" class="calc-box" readonly style="background:#f8f9fa;"></td>
                            <td><input type="number" id="t91_ns1_caym2" class="calc-box" oninput="calc91()"></td>
                            <td><input type="number" id="t91_ns2_caym2" class="calc-box" oninput="calc91()"></td>
                            <td><input type="text" id="t91_perc_caym2" class="calc-box" readonly style="background:#f8f9fa;"></td>
                        </tr>
                        <tr style="font-weight:bold; background:#e9ecef;">
                            <td colspan="5" style="text-align:right;">Average Percentage:</td>
                            <td><input type="text" id="t91_avg_perc" class="calc-box" readonly style="background:transparent; border:none;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 9.2 Mentoring -->
        <div class="section-card">
            <h3>9.2. Mentoring System (05)</h3>
            <textarea id="text_9_2" class="textarea-box" placeholder="Describe mentoring system..." onchange="saveCurrentLevel()"></textarea>
        </div>

        <!-- 9.3 Feedback -->
        <div class="section-card">
            <h3>9.3. Feedback Analysis (10)</h3>
            <h4>9.3.1. Feedback on Teaching and Learning Process and Corrective Measures (05)</h4>
            <textarea id="text_9_3_1" class="textarea-box" placeholder="Describe feedback collection on TLP..." onchange="saveCurrentLevel()"></textarea>
            <h4>9.3.2. Feedback on Academic Facilities (05)</h4>
            <textarea id="text_9_3_2" class="textarea-box" placeholder="Describe feedback collection on facilities..." onchange="saveCurrentLevel()"></textarea>
        </div>

        <!-- 9.4 Placement -->
        <div class="section-card">
            <h3>9.4. Training and Placement Support (10)</h3>
            <textarea id="text_9_4" class="textarea-box" placeholder="Describe training and placement support..." onchange="saveCurrentLevel()"></textarea>
        </div>

        <!-- 9.5 Start-up -->
        <div class="section-card">
            <h3>9.5. Start-up and Entrepreneurship Activities (05)</h3>
            <textarea id="text_9_5" class="textarea-box" placeholder="Describe start-up initiatives..." onchange="saveCurrentLevel()"></textarea>
        </div>

        <!-- 9.6 Governance -->
        <div class="section-card">
            <h3>9.6. Governance and Transparency (25)</h3>
            <h4>9.6.1. Availability of Institutional Strategic Plan (10)</h4>
            <textarea id="text_9_6_1" class="textarea-box" placeholder="Describe strategic plan..." onchange="saveCurrentLevel()"></textarea>
            <h4>9.6.2. Governing Body, Administrative Setup, Service Rules (10)</h4>
            <textarea id="text_9_6_2" class="textarea-box" placeholder="Describe governing body..." onchange="saveCurrentLevel()"></textarea>
            <h4>9.6.3. Transparency (05)</h4>
            <textarea id="text_9_6_3" class="textarea-box" placeholder="Describe transparency policies..." onchange="saveCurrentLevel()"></textarea>
        </div>

        <!-- 9.7 Budget Institute -->
        <div class="section-card">
            <h3>9.7. Budget Allocation, Utilization, and Public Accounting at Institute Level (12)</h3>
            
            <h4 style="margin-top:10px;">Table No. 9.7.1: Summary of budget and actual expenditure incurred at Institute level</h4>
            <button type="button" class="btn-add" onclick="addRow('inst_budget')">+ Add Institute Budget Record (per year)</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Year</th>
                            <th>Total Income (Fee/Gov/Grant/Other)</th>
                            <th>Actual Expenditure</th>
                            <th>Total Students</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-inst_budget"></tbody>
                </table>
            </div>
            
            <h4 style="margin-top:20px;">Table No. 9.7.2: Budget and actual expenditure incurred at Institute level (Detailed Items)</h4>
            <button type="button" class="btn-add" onclick="addRow('inst_expenditure')">+ Add Detailed Expenditure Row</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Item Name (e.g. Library, Lab, Salaries)</th>
                            <th>Budgeted CAY</th>
                            <th>Actual CAY</th>
                            <th>Budgeted CAYm1</th>
                            <th>Actual CAYm1</th>
                            <th>Budgeted CAYm2</th>
                            <th>Actual CAYm2</th>
                            <th>Budgeted CAYm3</th>
                            <th>Actual CAYm3</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-inst_expenditure"></tbody>
                </table>
            </div>
        </div>

        <!-- 9.8 Budget Program -->
        <div class="section-card">
            <h3>9.8. Program Specific Budget Allocation, Utilization (08)</h3>

            <h4 style="margin-top:10px;">Table No. 9.8.1: Summary of budget and actual expenditure incurred at program level</h4>
            <button type="button" class="btn-add" onclick="addRow('prog_budget')">+ Add Program Budget Record (per year)</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Year</th>
                            <th>Total Budget (Demanded | Allocated)</th>
                            <th>Actual Expenditure</th>
                            <th>% Spent</th>
                            <th>Total Students</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-prog_budget"></tbody>
                </table>
            </div>
            
            <h4 style="margin-top:20px;">Table No. 9.8.2: Budget and actual expenditure incurred at program level (Detailed Items)</h4>
            <button type="button" class="btn-add" onclick="addRow('prog_expenditure')">+ Add Detailed Program Expenditure Row</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Item Name</th>
                            <th>Budget CAY</th>
                            <th>Actual CAY</th>
                            <th>Budget CAYm1</th>
                            <th>Actual CAYm1</th>
                            <th>Budget CAYm2</th>
                            <th>Actual CAYm2</th>
                            <th>Budget CAYm3</th>
                            <th>Actual CAYm3</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-prog_expenditure"></tbody>
                </table>
            </div>
        </div>

        <!-- 9.9 to 9.14 Descriptive Sections -->
        <div class="section-card">
            <h3>9.9. Quality of Learning Resources (Hard/Soft) (05)</h3>
            <textarea id="text_9_9" class="textarea-box" placeholder="Describe library/e-resources..." onchange="saveCurrentLevel()"></textarea>
            
            <h3>9.10. E-Governance (05)</h3>
            <textarea id="text_9_10" class="textarea-box" placeholder="Describe e-governance..." onchange="saveCurrentLevel()"></textarea>

            <h3>9.11. Initiatives and Implementation of SDGs (10)</h3>
            <textarea id="text_9_11" class="textarea-box" placeholder="Describe SDG initiatives..." onchange="saveCurrentLevel()"></textarea>

            <h3>9.12. Innovative Educational Initiatives (05)</h3>
            <textarea id="text_9_12" class="textarea-box" placeholder="Describe ABC, IKS, holistic education..." onchange="saveCurrentLevel()"></textarea>

            <h3>9.13. Faculty Performance Appraisal (FPADS) (10)</h3>
            <textarea id="text_9_13" class="textarea-box" placeholder="Describe FPADS..." onchange="saveCurrentLevel()"></textarea>

            <h3>9.14. Outreach Activities (05)</h3>
            <textarea id="text_9_14" class="textarea-box" placeholder="Describe Unnat Bharat Abhiyan, community service..." onchange="saveCurrentLevel()"></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 15px; margin-bottom: 4rem;">
            <button type="button" class="btn-save" onclick="saveCriterionData()" style="padding: 10px 20px; font-weight: bold; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Criterion 9</button>
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
        't91_s4_cay', 't91_ns1_cay', 't91_ns2_cay',
        't91_s4_caym1', 't91_ns1_caym1', 't91_ns2_caym1',
        't91_s4_caym2', 't91_ns1_caym2', 't91_ns2_caym2',
        'text_9_2', 'text_9_3_1', 'text_9_3_2', 'text_9_4', 'text_9_5',
        'text_9_6_1', 'text_9_6_2', 'text_9_6_3', 'text_9_9', 'text_9_10',
        'text_9_11', 'text_9_12', 'text_9_13', 'text_9_14'
    ];

    const lists = {
        'inst_budget': { keys: ['year', 'income', 'exp', 'students'] },
        'inst_expenditure': { keys: ['item', 'b_cay', 'a_cay', 'b_m1', 'a_m1', 'b_m2', 'a_m2', 'b_m3', 'a_m3'] },
        'prog_budget': { keys: ['year', 'budget', 'exp', 'spent', 'students'] },
        'prog_expenditure': { keys: ['item', 'b_cay', 'a_cay', 'b_m1', 'a_m1', 'b_m2', 'a_m2', 'b_m3', 'a_m3'] }
    };

    function changeDepartment() {
        const select = document.getElementById('level-select');
        window.location.href = '?route=nba/criterion&id=9&year=<?= urlencode($year) ?>&dept_id=' + select.value;
    }

    function initData() {
        levelData = initialData.levelData || {};
        if (!levelData[currentLevel]) levelData[currentLevel] = {};

        const data = levelData[currentLevel];
        simpleInputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = data[id] || '';
        });

        Object.keys(lists).forEach(listName => {
            if (!data[listName]) data[listName] = [];
            renderTable(listName);
        });

        calc91();
    }

    function saveCurrentLevel() {
        if (!levelData[currentLevel]) levelData[currentLevel] = {};
        
        simpleInputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) levelData[currentLevel][id] = el.value;
        });
    }

    function calcRow91(yr) {
        const s4 = parseFloat(document.getElementById(`t91_s4_${yr}`).value) || 0;
        const ns1 = parseFloat(document.getElementById(`t91_ns1_${yr}`).value) || 0;
        const ns2 = parseFloat(document.getElementById(`t91_ns2_${yr}`).value) || 0;
        
        const rf4 = s4 / 20;
        document.getElementById(`t91_rf4_${yr}`).value = rf4.toFixed(2);
        
        if (rf4 > 0) {
            const perc = ((ns1 * 0.8) + (ns2 * 0.2)) / rf4 * 100;
            document.getElementById(`t91_perc_${yr}`).value = perc.toFixed(2) + '%';
            return perc;
        }
        document.getElementById(`t91_perc_${yr}`).value = '0%';
        return 0;
    }

    function calc91() {
        saveCurrentLevel();
        const p1 = calcRow91('cay');
        const p2 = calcRow91('caym1');
        const p3 = calcRow91('caym2');
        document.getElementById('t91_avg_perc').value = ((p1+p2+p3)/3).toFixed(2) + '%';
    }

    function addRow(listName) {
        let obj = {};
        lists[listName].keys.forEach(k => { obj[k] = ''; });
        levelData[currentLevel][listName].push(obj);
        renderTable(listName);
    }

    function removeRow(listName, idx) {
        levelData[currentLevel][listName].splice(idx, 1);
        renderTable(listName);
    }

    function updateRow(listName, idx, key, val) {
        levelData[currentLevel][listName][idx][key] = val;
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
                    html += `<td><select style="width:100%" onchange="updateRow('${listName}', ${idx}, '${k}', this.value)">
                        <option value="CAY" ${row[k]==='CAY'?'selected':''}>CAY</option>
                        <option value="CAYm1" ${row[k]==='CAYm1'?'selected':''}>CAYm1</option>
                        <option value="CAYm2" ${row[k]==='CAYm2'?'selected':''}>CAYm2</option>
                        <option value="CAYm3" ${row[k]==='CAYm3'?'selected':''}>CAYm3</option>
                    </select></td>`;
                } else {
                    html += `<td><input type="text" style="width:100%" value="${row[k]}" oninput="updateRow('${listName}', ${idx}, '${k}', this.value)"></td>`;
                }
            });
            html += `<td style="text-align:center;"><button type="button" class="btn-remove" onclick="removeRow('${listName}', ${idx})">&times;</button></td>`;
            tr.innerHTML = html;
            tbody.appendChild(tr);
        });
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

        fetch('<?= BASE_URL ?>/public/index.php?route=api/nba/save_criterion&id=9', {
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

