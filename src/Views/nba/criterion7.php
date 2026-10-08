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
    <title><?= $page_title ?? 'Criterion 7' ?> &mdash; FMS</title>
    <link href="<?= BASE_URL ?>/assets/css/layout.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/nba_module.css" rel="stylesheet">
    <style>
        .nba-table th, .nba-table td { padding: 8px; text-align: left; }
        .nba-table thead { font-weight: bold; background: #fff3cd; }
        .nba-table th { text-align: center; vertical-align: middle; }
        .section-card { margin-bottom: 2rem; background: #fff; padding: 1.5rem; border-radius: 8px; border: 1px solid #dee2e6; }
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
        Criterion 7
    </div>

    <div class="header-row">
        <h1>Criterion 7: Facilities and Technical Support (100)</h1>
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

        <!-- 7.1 Laboratories & Manpower -->
        <div class="section-card">
            <h3>7.1 Adequate and Well-Equipped Laboratories, and Technical Manpower (40)</h3>
            <button type="button" class="btn-add" onclick="addRow('laboratories')">+ Add Laboratory / Manpower</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th rowspan="2">S.N.</th>
                            <th rowspan="2">Name of the Laboratory</th>
                            <th rowspan="2">No. of students per setup (Batch Size)</th>
                            <th rowspan="2">Name of the major equipment</th>
                            <th rowspan="2">Weekly utilization status</th>
                            <th colspan="3">Technical Manpower support</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>Name of the technical staff</th>
                            <th>Designation</th>
                            <th>Qualification</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-laboratories"></tbody>
                </table>
            </div>
        </div>

        <!-- 7.2 Additional Facilities -->
        <div class="section-card">
            <h3>7.2 Additional Facilities Created for Improving Quality of Learning in Labs (20)</h3>
            <button type="button" class="btn-add" onclick="addRow('additional_facilities')">+ Add Additional Facility</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Name of the Facility</th>
                            <th>Details</th>
                            <th>Purpose for creating facility</th>
                            <th>Utilization</th>
                            <th>Relevance to POs/PSOs</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-additional_facilities"></tbody>
                </table>
            </div>
        </div>

        <!-- 7.3 Maintenance & Ambiance -->
        <div class="section-card">
            <h3>7.3 Maintenance of Laboratories and Overall Ambiance (10)</h3>
            <p>Provide details of overall laboratories maintenance and overall ambiance in the Department.</p>
            <textarea name="text_7_3" id="text_7_3" style="width:100%; height:100px; padding:10px; border:1px solid #ced4da;" placeholder="Describe maintenance procedures, ambiance, lighting, seating, circulation space, etc..." onchange="saveCurrentLevel()"></textarea>
            <div class="file-upload-row" style="margin-top: 15px;">
                <div><strong>Maintenance Protocols / Documentation PDF</strong></div>
                <input type="file" name="file_7_3" accept=".pdf">
            </div>
        </div>

        <!-- 7.4 Safety Measures -->
        <div class="section-card">
            <h3>7.4 Safety Measures in Laboratories (10)</h3>
            <button type="button" class="btn-add" onclick="addRow('safety_measures')">+ Add Safety Measure</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Name of the Laboratory</th>
                            <th>Safety measures deployed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-safety_measures"></tbody>
                </table>
            </div>
        </div>

        <!-- 7.5 Project Labs / CoE -->
        <div class="section-card">
            <h3>7.5 Project Laboratory / Research Laboratory / Centre of Excellence (20)</h3>
            <p>Provide details of laboratories for supporting projects, research, Centre of Excellence, innovation, and startups.</p>
            <button type="button" class="btn-add" onclick="addRow('project_labs')">+ Add Project/Research Lab</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Name of the Laboratory / Centre of Excellence</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-project_labs"></tbody>
                </table>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 15px; margin-bottom: 4rem;">
            <button type="button" class="btn-save" onclick="saveCriterionData()" style="padding: 10px 20px; font-weight: bold; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Criterion 7</button>
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
        'text_7_3'
    ];

    const lists = {
        'laboratories': { keys: ['lab_name', 'batch_size', 'major_eq', 'weekly_util', 'staff_name', 'designation', 'qualification'] },
        'additional_facilities': { keys: ['facility', 'details', 'purpose', 'utilization', 'relevance'] },
        'safety_measures': { keys: ['lab_name', 'measures'] },
        'project_labs': { keys: ['lab_name'] }
    };

    function changeDepartment() {
        const select = document.getElementById('level-select');
        window.location.href = '?route=nba/criterion&id=7&year=<?= urlencode($year) ?>&dept_id=' + select.value;
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
    }

    function saveCurrentLevel() {
        if (!levelData[currentLevel]) levelData[currentLevel] = {};
        
        simpleInputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) levelData[currentLevel][id] = el.value;
        });
    }

    function addRow(listName) {
        let obj = {};
        lists[listName].keys.forEach(k => {
            obj[k] = '';
        });
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
            let html = `<td style="text-align:center; font-weight:bold;">${idx + 1}</td>`;
            lists[listName].keys.forEach(k => {
                html += `<td><input type="text" style="width:100%" value="${row[k]}" oninput="updateRow('${listName}', ${idx}, '${k}', this.value)"></td>`;
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

        fetch('<?= BASE_URL ?>/public/index.php?route=api/nba/save_criterion&id=7', {
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

