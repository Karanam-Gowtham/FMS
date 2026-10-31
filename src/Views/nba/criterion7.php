<?php
$criterion_num = 7;
$criterion_title = "Facilities and Technical Support (100)";
include __DIR__ . '/../partials/nba_header.php';
?>
    <form id="nbaForm">

        <!-- 7.1 Laboratories & Manpower -->
        <div class="section-card">
            <h3>7.1 Adequate and Well-Equipped Laboratories, and Technical Manpower (40)</h3>
            <button type="button" class="btn-add" onclick="addRow('laboratories')">+ Add Laboratory / Manpower</button>
            <div style="overflow-x: auto;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th scope="col" rowspan="2">S.N.</th>
                            <th scope="col" rowspan="2">Name of the Laboratory</th>
                            <th scope="col" rowspan="2">No. of students per setup (Batch Size)</th>
                            <th scope="col" rowspan="2">Name of the major equipment</th>
                            <th scope="col" rowspan="2">Weekly utilization status</th>
                            <th scope="col" colspan="3">Technical Manpower support</th>
                            <th scope="col" rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th scope="col">Name of the technical staff</th>
                            <th scope="col">Designation</th>
                            <th scope="col">Qualification</th>
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
                            <th scope="col">S.N.</th>
                            <th scope="col">Name of the Facility</th>
                            <th scope="col">Details</th>
                            <th scope="col">Purpose for creating facility</th>
                            <th scope="col">Utilization</th>
                            <th scope="col">Relevance to POs/PSOs</th>
                            <th scope="col">Action</th>
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
            <textarea aria-label="Describe Maintenance Procedures, Ambiance, Lighting, Seating, Circulation Space, Etc" name="text_7_3" id="text_7_3" style="width:100%; height:100px; padding:10px; border:1px solid #ced4da;" placeholder="Describe maintenance procedures, ambiance, lighting, seating, circulation space, etc..." onchange="saveCurrentLevel()"></textarea>
            <div class="file-upload-row" style="margin-top: 15px;">
                <div><strong>Maintenance Protocols / Documentation PDF</strong></div>
                <input aria-label="File 7 3" type="file" name="file_7_3" accept=".pdf">
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
                            <th scope="col">S.N.</th>
                            <th scope="col">Name of the Laboratory</th>
                            <th scope="col">Safety measures deployed</th>
                            <th scope="col">Action</th>
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
                            <th scope="col">S.N.</th>
                            <th scope="col">Name of the Laboratory / Centre of Excellence</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-project_labs"></tbody>
                </table>
            </div>
        </div>

        <?php 
        $crit_id = 7;
        $module = 'nba';
        $save_onclick = 'saveCriterionData()';
        include __DIR__ . '/../partials/accreditation_action_bar.php'; 
        ?>

    </form>
</div>

<script>
    let levelData = {};
    let currentLevel = 'dept_<?= $dept_id ?>';
    
    const rawData = <?= json_encode($criteria_data ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
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
                html += `<td><input aria-label="Text Input" type="text" style="width:100%" value="${row[k]}" oninput="updateRow('${listName}', ${idx}, '${k}', this.value)"></td>`;
            });
            html += `<td style="text-align:center;"><button type="button" class="btn-remove" onclick="removeRow('${listName}', ${idx})">&times;</button></td>`;
            tr.innerHTML = html;
            tbody.appendChild(tr);
        });
    }

    async function saveCriterionData(silent = false) {
        saveCurrentLevel();
        return await saveAccreditationCriterion(7, { levelData: levelData }, silent);
    }


    window.onload = function() { initData(); };
</script>
</body>
</html>

