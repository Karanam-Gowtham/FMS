<?php
/**
 * NBA Criterion 1
 *
 * Outcome-Based Curriculum
 * URL: nba/criterion1.php?year={year}
 */
require_once __DIR__ . '/../core/bootstrap.php';

require_login();
$auth = auth_context();
$active_role = auth_active_role();

// Only specific roles should be able to edit this, but for now we'll allow access for testing.
// In a real scenario, this would be locked to NBA coordinator or HOD.

$year = isset($_GET['year']) ? trim($_GET['year']) : '2025-26';

// Load existing data if it exists
$dept_id = $active_role['dept_id'];

// Fetch departments for the selector
$dept_result = $conn->query("SELECT dept_id, dept_name FROM departments ORDER BY dept_name");
$departments = [];
while ($row = $dept_result->fetch_assoc()) {
    $departments[] = $row;
}
$submission = null;
$criteria_data = [];

if ($dept_id > 0) {
    // Check if submission exists
    $stmt = $conn->prepare("SELECT * FROM nba_submissions WHERE dept_id = ? AND academic_year = ?");
    $stmt->bind_param("is", $dept_id, $year);
    $stmt->execute();
    $sub_res = $stmt->get_result();
    if ($sub_res->num_rows > 0) {
        $submission = $sub_res->fetch_assoc();
        
        // Fetch Criterion 1 data
        $stmt2 = $conn->prepare("SELECT data_json FROM nba_criteria_data WHERE submission_id = ? AND criterion_number = 1");
        $stmt2->bind_param("i", $submission['submission_id']);
        $stmt2->execute();
        $data_res = $stmt2->get_result();
        if ($data_res->num_rows > 0) {
            $row = $data_res->fetch_assoc();
            $criteria_data = json_decode($row['data_json'], true) ?: [];
        }
        $stmt2->close();
    }
    $stmt->close();
}

$page_title = 'Criterion 1: Outcome-Based Curriculum';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> &mdash; FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <style>
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .breadcrumb { margin-bottom: 1rem; color: #6c757d; font-size: 0.9rem; }
        .breadcrumb a { color: #4a90d9; text-decoration: none; }
        .header-row { margin-bottom: 1.5rem; border-bottom: 2px solid #e9ecef; padding-bottom: 1rem; }
        .header-row h1 { margin: 0; color: #333; font-size: 1.8rem; }
        .header-row p { color: #6c757d; margin-top: 0.5rem; font-size: 0.95rem; }

        .section-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        
        .section-card h3 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 1.2rem;
            border-bottom: 1px solid #f1f3f5;
            padding-bottom: 0.8rem;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dynamic-list { margin-bottom: 1.5rem; }
        .dynamic-list-item { display: flex; gap: 10px; margin-bottom: 10px; }
        .dynamic-list-item input { flex: 1; padding: 0.5rem; border: 1px solid #ced4da; border-radius: 5px; }
        .btn-remove { background: #dc3545; color: white; border: none; padding: 0.5rem 0.8rem; border-radius: 5px; cursor: pointer; }
        .btn-add { background: #e9ecef; color: #495057; border: 1px solid #ced4da; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; font-size: 0.85rem; font-weight: 600; }
        .btn-add:hover { background: #dee2e6; }

        /* Matrix Table */
        .matrix-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .matrix-table th, .matrix-table td { border: 1px solid #dee2e6; padding: 0.5rem; text-align: center; }
        .matrix-table th { background: #f8f9fa; font-weight: 600; }
        .matrix-input { width: 50px; text-align: center; padding: 0.4rem; border: 1px solid #ced4da; border-radius: 4px; }
        
        /* CSV Dropzone */
        .csv-dropzone {
            border: 2px dashed #4a90d9;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 2.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .csv-dropzone:hover { background: #eaf3fb; border-color: #2b70b5; }
        .csv-dropzone h4 { margin: 0 0 10px 0; color: #333; }
        .csv-dropzone p { margin: 0; color: #6c757d; font-size: 0.9rem; }
        
        .file-upload-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 6px; border: 1px solid #e9ecef; }
        .file-upload-row strong { font-size: 0.95rem; color: #333; }
        .file-upload-row .desc { font-size: 0.8rem; color: #6c757d; display: block; margin-top: 4px; }

        .btn-save {
            background: #28a745;
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(40,167,69,0.2);
        }
        .btn-save:hover { background: #218838; }

        .toast {
            position: fixed; top: 20px; right: 20px; background: #28a745; color: white; padding: 1rem 1.5rem; border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: none; z-index: 1000;
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div id="toast" class="toast">Data saved successfully!</div>

<div class="container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a> &raquo; 
        <a href="dashboard.php?year=<?= urlencode($year) ?>">NBA Accreditation</a> &raquo; 
        Criterion 1
    </div>

    <div class="header-row">
        <h1>Criterion 1: Outcome-Based Curriculum</h1>
        <p>Academic Year: <strong><?= htmlspecialchars($year) ?></strong></p>
    </div>

    <form id="nbaForm">
        <!-- 1.1 Vision, Mission, PEOs -->
        <div class="section-card">
            <h3>1.1 Vision, Mission and Program Educational Objectives</h3>
            
            <h4>1.1.1 State the Vision and Mission of the Institute and the Department</h4>
            <p style="font-size: 0.85rem; color: #6c757d; margin-bottom: 15px;">(Vision statement typically indicates aspirations and Mission statement states the broad approach to achieve aspirations.)</p>
            
            <div style="margin-bottom: 20px; background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #e9ecef;">
                <label style="font-weight: 600; color: #333; margin-right: 10px;">Select Entity Level:</label>
                <select id="level-select" onchange="switchLevel()" style="padding: 6px 12px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.95rem;">
                    <option value="univ">University</option>
                    <option value="school">School of Computing</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="dept_<?= $d['dept_id'] ?>">Department: <?= htmlspecialchars($d['dept_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="vm-form-container">
                <div style="margin-bottom: 15px;">
                    <label id="vision-label" style="font-size: 0.85rem; font-weight: 600; color: #555;">University Vision</label>
                    <textarea id="vision-text" style="width: 100%; height: 80px; padding: 8px; border: 1px solid #ced4da; border-radius: 5px; margin-top: 5px;" placeholder="Enter Vision..." onchange="saveCurrentLevel()"></textarea>
                </div>

                <div id="mission-list-container" style="margin-bottom: 15px;">
                    <label id="mission-label" style="font-size: 0.85rem; font-weight: 600; color: #555;">University Mission Statements</label>
                    <p style="font-size: 0.8rem; color: #6c757d; margin-bottom: 10px;">Please enter the mission in points (M1, M2...).</p>
                    <div id="mission-list" class="dynamic-list">
                        <!-- Populated by JS -->
                    </div>
                    <button type="button" class="btn-add" onclick="addDynamicItem('mission-list', 'Mission')">+ Add Mission Point</button>
                </div>
            </div>
            
            <div id="peo-matrix-container" style="display: none;">
                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e9ecef;">

                <h4>1.1.2 State PEOs of the Program</h4>
                <p style="font-size: 0.85rem; color: #6c757d; margin-bottom: 15px;">(State the PEOs (3 to 5) of program seeking accreditation.)</p>
                <div id="peo-list" class="dynamic-list">
                    <!-- Populated by JS -->
                </div>
                <button type="button" class="btn-add" onclick="addDynamicItem('peo-list', 'PEO')">+ Add PEO</button>
                
                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e9ecef;">

                <h4>1.1.3 Process of Defining Vision, Mission and PEOs</h4>
                <div class="file-upload-row" style="margin-bottom: 15px;">
                    <div>
                        <span class="desc">Articulate the process involved in defining the Vision, Mission, and PEOs. Please upload PDF proof.</span>
                    </div>
                    <input type="file" name="file_1_1_3" accept=".pdf">
                </div>

                <h4>1.1.4 Dissemination of Vision, Mission and PEOs</h4>
                <div class="file-upload-row" style="margin-bottom: 15px;">
                    <div>
                        <span class="desc">Evidence of website, curricula, posters, and awareness processes. Please upload PDF proof.</span>
                    </div>
                    <input type="file" name="file_1_1_4" accept=".pdf">
                </div>

                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e9ecef;">

                <h4>1.1.5 Mapping of PEOs with Mission</h4>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <p style="font-size: 0.85rem; color: #6c757d; margin: 0;">Enter correlation levels: Low (1), Medium (2), High (3). If no correlation, use '-'.</p>
                    <button type="button" class="btn-primary" onclick="autoCalculateMatrix()" style="padding: 5px 12px; font-size: 0.85rem; background: #6f42c1; border: none; border-radius: 4px; color: white; cursor: pointer;">
                        ✨ Auto-Calculate Correlation
                    </button>
                </div>
                <div id="matrix-container" style="overflow-x: auto;">
                    <!-- Auto-generated Matrix Table -->
                </div>
            </div>
        </div>

        <!-- CSV Upload for COs -->
        <div class="section-card">
            <h3>1.4 & 1.5 Course Outcomes & Articulation Matrices</h3>
            <p style="font-size: 0.9rem; color: #555; margin-bottom: 1.5rem;">
                Criterion 1 requires CO mapping tables for <strong>16 Core Courses</strong> (2 per semester from semesters 1-8). 
                To save time, upload a CSV/Excel file containing the Course Codes, COs, and Mapping values. The system will automatically parse and ignore invalid/empty rows.
            </p>
            
            <div class="csv-dropzone" id="csvDropzone" onclick="document.getElementById('csvInput').click()">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                <h4>Click or Drag & Drop your CSV File Here</h4>
                <p>Supports .csv files containing CO mappings</p>
                <input type="file" id="csvInput" style="display: none;" accept=".csv" onchange="handleCSVSelect(this)">
            </div>
            <div id="csvFileName" style="margin-top: 10px; font-weight: 600; color: #28a745;"></div>
        </div>

        <div style="text-align: right; margin-bottom: 4rem;">
            <button type="button" class="btn-save" onclick="saveCriterionData()">Save Criterion 1</button>
        </div>
    </form>
</div>

<script>
    // —— Dynamic Level Logic ——
    let levelData = {};
    let currentLevel = 'univ';
    
    // Core matrix data (tied to the selected Department)
    let missions = []; // Active department's missions
    let peos = [];     // Active department's PEOs
    let matrixData = {}; 

    const initialData = <?= json_encode($criteria_data) ?>;

    function initData() {
        levelData = initialData.levelData || {};
        
        // Ensure defaults exist for all levels using arrays for missions
        if (!levelData['univ']) levelData['univ'] = { vision: '', missions: [{id: 'M1', text: ''}] };
        if (!levelData['school']) levelData['school'] = { vision: '', missions: [{id: 'M1', text: ''}] };
        
        // Initialize active department's data specifically for the matrix
        const defaultDept = 'dept_<?= $dept_id ?>';
        if (!levelData[defaultDept]) {
            levelData[defaultDept] = { vision: '', missions: [{id: 'M1', text: ''}] };
        }

        peos = initialData.peos || [{id: 'PEO1', text: ''}];
        matrixData = initialData.matrix || {};

        switchLevel(); // Load UI for current dropdown selection
        
        renderList('peo-list', peos, 'PEO', 'PEO');
        // Matrix is rendered inside switchLevel or renderList
    }

    function switchLevel() {
        const select = document.getElementById('level-select');
        currentLevel = select.value;
        const text = select.options[select.selectedIndex].text;
        
        document.getElementById('vision-label').innerText = text + ' Vision';
        document.getElementById('mission-label').innerText = text + ' Mission Statements';
        
        // Ensure data object exists for this level
        if (!levelData[currentLevel]) {
            levelData[currentLevel] = { vision: '', missions: [{id: 'M1', text: ''}] };
        }

        // Populate Vision
        document.getElementById('vision-text').value = levelData[currentLevel].vision || '';

        // Populate Missions
        missions = levelData[currentLevel].missions || [{id: 'M1', text: ''}];
        renderList('mission-list', missions, 'Mission', 'M');

        // Toggle PEO and Matrix container only for departments
        if (currentLevel.startsWith('dept_')) {
            document.getElementById('peo-matrix-container').style.display = 'block';
            renderMatrix();
        } else {
            document.getElementById('peo-matrix-container').style.display = 'none';
        }
    }

    function saveCurrentLevel() {
        if (!levelData[currentLevel]) {
            levelData[currentLevel] = { vision: '', missions: [] };
        }
        levelData[currentLevel].vision = document.getElementById('vision-text').value;
        // missions are bound to the object by reference in renderList, so they auto-update!
    }

    function renderList(containerId, dataArray, placeholderLabel, prefix) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';
        dataArray.forEach((item, index) => {
            item.id = `${prefix}${index + 1}`;
            const div = document.createElement('div');
            div.className = 'dynamic-list-item';
            div.innerHTML = `
                <div style="width: 60px; display: flex; align-items: center; font-weight: 600; color: #4a90d9;">${item.id}</div>
                <input type="text" placeholder="Enter ${placeholderLabel} details..." value="${item.text.replace(/"/g, '&quot;')}" onchange="updateText(this, '${prefix}', ${index})">
                <button type="button" class="btn-remove" onclick="removeItem('${prefix}', ${index})">&times;</button>
            `;
            container.appendChild(div);
        });
        if (prefix === 'M') renderMatrix();
    }

    function addDynamicItem(containerId, type) {
        if (type === 'Mission') {
            missions.push({ id: '', text: '' });
            renderList('mission-list', missions, 'Mission', 'M');
        } else {
            peos.push({ id: '', text: '' });
            renderList('peo-list', peos, 'PEO', 'PEO');
        }
    }

    function removeItem(prefix, index) {
        if (prefix === 'M') missions.splice(index, 1);
        else peos.splice(index, 1);
        
        if (prefix === 'M') renderList('mission-list', missions, 'Mission', 'M');
        else renderList('peo-list', peos, 'PEO', 'PEO');
    }

    function updateText(input, prefix, index) {
        if (prefix === 'M') missions[index].text = input.value;
        else peos[index].text = input.value;
    }

    function updateMatrixVal(input, rowId, colId) {
        matrixData[`${rowId}_${colId}`] = input.value;
    }

    function autoCalculateMatrix() {
        if (missions.length === 0 || peos.length === 0) return;
        
        // Basic NLP: Ignore common stop words
        const stopWords = new Set(["a","an","and","are","as","at","be","but","by","for","if","in","into","is","it","no","not","of","on","or","such","that","the","their","then","there","these","they","this","to","was","will","with","can","have","has","from","which"]);

        const getTokens = (str) => {
            if (!str) return [];
            const cleanStr = str.replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g," ").toLowerCase();
            const words = cleanStr.split(/\s+/);
            return words.filter(w => w.length > 3 && !stopWords.has(w));
        };

        peos.forEach(peo => {
            const peoTokens = getTokens(peo.text);
            
            missions.forEach(m => {
                const mTokens = getTokens(m.text);
                
                // Find keyword matches (allowing for partial matches like "develop" / "development")
                let matchCount = 0;
                peoTokens.forEach(pt => {
                    if (mTokens.some(mt => mt.includes(pt) || pt.includes(mt))) {
                        matchCount++;
                    }
                });

                // Heuristic mapping: 3 matches = High, 2 = Medium, 1 = Low
                let val = '-';
                if (matchCount >= 3) val = '3';
                else if (matchCount === 2) val = '2';
                else if (matchCount === 1) val = '1';

                matrixData[`${peo.id}_${m.id}`] = val;
            });
        });

        renderMatrix();
        
        // Show a brief success flash
        const btn = document.querySelector('button[onclick="autoCalculateMatrix()"]');
        const oldText = btn.innerHTML;
        btn.innerHTML = '✅ Calculated!';
        btn.style.background = '#28a745';
        setTimeout(() => {
            btn.innerHTML = oldText;
            btn.style.background = '#6f42c1';
        }, 2000);
    }

    function renderMatrix() {
        const container = document.getElementById('matrix-container');
        if (missions.length === 0 || peos.length === 0) {
            container.innerHTML = '<p style="color: #dc3545;">Please add at least one Mission and one PEO to generate the matrix.</p>';
            return;
        }

        let html = '<table class="matrix-table"><thead><tr><th></th>';
        
        // Header row (Missions)
        missions.forEach(m => {
            html += `<th>${m.id}</th>`;
        });
        html += '</tr></thead><tbody>';

        // Body rows (PEOs)
        peos.forEach(peo => {
            html += `<tr><th style="background:#f8f9fa;">${peo.id}</th>`;
            missions.forEach(m => {
                const key = `${peo.id}_${m.id}`;
                const val = matrixData[key] || '';
                html += `<td><input type="text" class="matrix-input" value="${val}" onchange="updateMatrixVal(this, '${peo.id}', '${m.id}')" maxlength="1"></td>`;
            });
            html += '</tr>';
        });

        html += '</tbody></table>';
        container.innerHTML = html;
    }

    // —— CSV UI Logic ——
    function handleCSVSelect(input) {
        if (input.files && input.files[0]) {
            document.getElementById('csvFileName').innerText = 'Selected File: ' + input.files[0].name;
        }
    }

    // —— Save Logic ——
    function saveCriterionData() {
        saveCurrentLevel(); // Ensure currently focused level is saved to object
        
        const payload = {
            levelData: levelData,
            peos: peos,
            matrix: matrixData
        };

        const formData = new FormData();
        formData.append('year', '<?= htmlspecialchars($year) ?>');
        formData.append('json_data', JSON.stringify(payload));

        // Append files if they exist
        const file113 = document.querySelector('input[name="file_1_1_3"]').files[0];
        const file114 = document.querySelector('input[name="file_1_1_4"]').files[0];
        const csvFile = document.getElementById('csvInput').files[0];

        if (file113) formData.append('file_1_1_3', file113);
        if (file114) formData.append('file_1_1_4', file114);
        if (csvFile) formData.append('csv_file', csvFile);

        // Send AJAX POST
        fetch('api_save_criterion1.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const toast = document.getElementById('toast');
                toast.style.display = 'block';
                setTimeout(() => toast.style.display = 'none', 3000);
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(err => {
            console.error(err);
            alert('A network error occurred while saving.');
        });
    }

    // Boot
    window.onload = initData;
</script>
</body>
</html>
