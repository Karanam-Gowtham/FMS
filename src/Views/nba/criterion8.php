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
    <title><?= $page_title ?? 'Criterion 8' ?> &mdash; FMS</title>
    <link href="<?= BASE_URL ?>/assets/css/layout.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/nba_module.css" rel="stylesheet">
    <style>
        .nba-table th, .nba-table td { padding: 8px; text-align: left; }
        .nba-table thead { font-weight: bold; background: #fff3cd; }
        .nba-table th { text-align: center; vertical-align: middle; }
        .section-card { margin-bottom: 2rem; background: #fff; padding: 1.5rem; border-radius: 8px; border: 1px solid #dee2e6; }
        .calc-box { width:100%; text-align:center; border:1px solid #ced4da; padding:5px; box-sizing:border-box; }
        .readonly-box { background:transparent; border:none; font-weight:bold; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div id="toast" class="toast">Data saved successfully!</div>

<div class="container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/public/index.php?route=dashboard">Dashboard</a> &raquo; 
        <a href="<?= BASE_URL ?>/public/index.php?route=nba/dashboard&year=<?= urlencode($year) ?>">NBA Accreditation</a> &raquo; 
        Criterion 8
    </div>

    <div class="header-row">
        <h1>Criterion 8: Continuous Improvement (80)</h1>
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

        <!-- 8.1 Actions Taken Based on the Results of Evaluation -->
        <h2 style="border-bottom: 2px solid #0d6efd; padding-bottom: 5px; color: #0d6efd;">8.1 Actions Taken Based on the Results of Evaluation of the COs, POs, and PSOs (40)</h2>
        
        <div class="section-card">
            <h3>8.1.1 Actions Taken Based on the Results of Evaluation of the COs Attainment (20)</h3>
            <p>Identify the areas of weaknesses in the program based on the analysis of evaluation of COs attainment levels. Measures identified and implemented to improve COs attainment levels for the assessment year (CAYm1) including curriculum intervention, pedagogical initiatives, support system improvements, etc.</p>
            <textarea name="text_8_1_1" id="text_8_1_1" style="width:100%; height:100px; padding:10px; border:1px solid #ced4da;" placeholder="Describe actions taken for COs attainment..." onchange="saveCurrentLevel()"></textarea>
            <div class="file-upload-row" style="margin-top: 15px;">
                <div><strong>COs Attainment Actions Documentation PDF</strong></div>
                <input type="file" name="file_8_1_1" accept=".pdf">
            </div>
        </div>

        <div class="section-card">
            <h3>8.1.2 Actions Taken Based on the Results of Evaluation of the POs/PSOs Attainment (20)</h3>
            <p>Identify the areas of weaknesses in the program based on the analysis of evaluation of POs/PSOs attainment levels. Measures identified and implemented during two years to improve POs attainment levels including curriculum intervention, pedagogical initiatives, support system improvements, etc.</p>
            <textarea name="text_8_1_2" id="text_8_1_2" style="width:100%; height:100px; padding:10px; border:1px solid #ced4da;" placeholder="Describe actions taken for POs/PSOs attainment..." onchange="saveCurrentLevel()"></textarea>
            <div class="file-upload-row" style="margin-top: 15px;">
                <div><strong>POs/PSOs Attainment Actions Documentation PDF</strong></div>
                <input type="file" name="file_8_1_2" accept=".pdf">
            </div>
        </div>

        <!-- 8.2 Academic Audit -->
        <h2 style="border-bottom: 2px solid #0d6efd; padding-bottom: 5px; color: #0d6efd; margin-top: 3rem;">8.2 Academic Audit (15)</h2>
        <div class="section-card">
            <h3>8.2 Academic Audit and Actions Taken thereof during the Period of Assessment (15)</h3>
            <p>Academic audit system/process and its implementation in relation to continuous improvement.</p>
            <textarea name="text_8_2" id="text_8_2" style="width:100%; height:100px; padding:10px; border:1px solid #ced4da;" placeholder="Describe the academic audit process and its findings..." onchange="saveCurrentLevel()"></textarea>
            <div class="file-upload-row" style="margin-top: 15px;">
                <div><strong>Academic Audit Reports PDF</strong></div>
                <input type="file" name="file_8_2" accept=".pdf">
            </div>
        </div>

        <!-- 8.3 Improvement in Faculty Qualification -->
        <h2 style="border-bottom: 2px solid #0d6efd; padding-bottom: 5px; color: #0d6efd; margin-top: 3rem;">8.3 & 8.4 Improvements</h2>
        <div class="section-card">
            <h3>8.3 Improvement in Faculty Qualification/Contribution (15)</h3>
            <p>Assessment is based on improvement in qualification and publications with respect to the Department.</p>
            <div style="overflow-x: auto; margin-top: 15px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Item</th>
                            <th>CAYm1</th>
                            <th>CAYm2</th>
                            <th>CAYm3</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>No. of faculty members with Ph.D. degree</td>
                            <td><input type="number" id="t83_phd_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t83_phd_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t83_phd_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>No. of publications in peer reviewed journals</td>
                            <td><input type="number" id="t83_jour_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t83_jour_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t83_jour_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>No. of publications in conferences</td>
                            <td><input type="number" id="t83_conf_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t83_conf_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="number" id="t83_conf_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 8.4 Improvement in Academic Performance -->
        <div class="section-card">
            <h3>8.4 Improvement in Academic Performance (10)</h3>
            <p>Provide details of improvement in academic performance of 1st year, 2nd year, 3rd year students during the assessment period.</p>
            <div style="overflow-x: auto; margin-top: 15px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Item</th>
                            <th>CAYm1</th>
                            <th>CAYm2</th>
                            <th>CAYm3</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Academic Performance Index (API) of the First-Year Students in the Program (Refer to section 4.3)</td>
                            <td><input type="text" id="t84_api1_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="text" id="t84_api1_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="text" id="t84_api1_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>Academic Performance Index of the Second-Year Students in the Program (Refer to section 4.4)</td>
                            <td><input type="text" id="t84_api2_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="text" id="t84_api2_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="text" id="t84_api2_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>Academic Performance Index of the Third Year Students in the Program (Refer to section 4.5)</td>
                            <td><input type="text" id="t84_api3_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="text" id="t84_api3_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input type="text" id="t84_api3_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 15px; margin-bottom: 4rem;">
            <button type="button" class="btn-save" onclick="saveCriterionData()" style="padding: 10px 20px; font-weight: bold; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Criterion 8</button>
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
        'text_8_1_1', 'text_8_1_2', 'text_8_2',
        't83_phd_caym1', 't83_phd_caym2', 't83_phd_caym3',
        't83_jour_caym1', 't83_jour_caym2', 't83_jour_caym3',
        't83_conf_caym1', 't83_conf_caym2', 't83_conf_caym3',
        't84_api1_caym1', 't84_api1_caym2', 't84_api1_caym3',
        't84_api2_caym1', 't84_api2_caym2', 't84_api2_caym3',
        't84_api3_caym1', 't84_api3_caym2', 't84_api3_caym3'
    ];

    function changeDepartment() {
        const select = document.getElementById('level-select');
        window.location.href = '?route=nba/criterion&id=8&year=<?= urlencode($year) ?>&dept_id=' + select.value;
    }

    function initData() {
        levelData = initialData.levelData || {};
        if (!levelData[currentLevel]) levelData[currentLevel] = {};

        const data = levelData[currentLevel];
        simpleInputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = data[id] || '';
        });
    }

    function saveCurrentLevel() {
        if (!levelData[currentLevel]) levelData[currentLevel] = {};
        
        simpleInputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) levelData[currentLevel][id] = el.value;
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

        fetch('<?= BASE_URL ?>/public/index.php?route=api/nba/save_criterion&id=8', {
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

