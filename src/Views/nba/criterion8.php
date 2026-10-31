<?php
$criterion_num = 8;
$criterion_title = "Continuous Improvement (80)";
include __DIR__ . '/../partials/nba_header.php';
?>
    <form id="nbaForm">

        <!-- 8.1 Actions Taken Based on the Results of Evaluation -->
        <h2 style="border-bottom: 2px solid #0d6efd; padding-bottom: 5px; color: #0d6efd;">8.1 Actions Taken Based on the Results of Evaluation of the COs, POs, and PSOs (40)</h2>
        
        <div class="section-card">
            <h3>8.1.1 Actions Taken Based on the Results of Evaluation of the COs Attainment (20)</h3>
            <p>Identify the areas of weaknesses in the program based on the analysis of evaluation of COs attainment levels. Measures identified and implemented to improve COs attainment levels for the assessment year (CAYm1) including curriculum intervention, pedagogical initiatives, support system improvements, etc.</p>
            <textarea aria-label="Describe Actions Taken For COs Attainment" name="text_8_1_1" id="text_8_1_1" style="width:100%; height:100px; padding:10px; border:1px solid #ced4da;" placeholder="Describe actions taken for COs attainment..." onchange="saveCurrentLevel()"></textarea>
            <div class="file-upload-row" style="margin-top: 15px;">
                <div><strong>COs Attainment Actions Documentation PDF</strong></div>
                <input aria-label="File 8 1 1" type="file" name="file_8_1_1" accept=".pdf">
            </div>
        </div>

        <div class="section-card">
            <h3>8.1.2 Actions Taken Based on the Results of Evaluation of the POs/PSOs Attainment (20)</h3>
            <p>Identify the areas of weaknesses in the program based on the analysis of evaluation of POs/PSOs attainment levels. Measures identified and implemented during two years to improve POs attainment levels including curriculum intervention, pedagogical initiatives, support system improvements, etc.</p>
            <textarea aria-label="Describe Actions Taken For POs/PSOs Attainment" name="text_8_1_2" id="text_8_1_2" style="width:100%; height:100px; padding:10px; border:1px solid #ced4da;" placeholder="Describe actions taken for POs/PSOs attainment..." onchange="saveCurrentLevel()"></textarea>
            <div class="file-upload-row" style="margin-top: 15px;">
                <div><strong>POs/PSOs Attainment Actions Documentation PDF</strong></div>
                <input aria-label="File 8 1 2" type="file" name="file_8_1_2" accept=".pdf">
            </div>
        </div>

        <!-- 8.2 Academic Audit -->
        <h2 style="border-bottom: 2px solid #0d6efd; padding-bottom: 5px; color: #0d6efd; margin-top: 3rem;">8.2 Academic Audit (15)</h2>
        <div class="section-card">
            <h3>8.2 Academic Audit and Actions Taken thereof during the Period of Assessment (15)</h3>
            <p>Academic audit system/process and its implementation in relation to continuous improvement.</p>
            <textarea aria-label="Describe The Academic Audit Process And Its Findings" name="text_8_2" id="text_8_2" style="width:100%; height:100px; padding:10px; border:1px solid #ced4da;" placeholder="Describe the academic audit process and its findings..." onchange="saveCurrentLevel()"></textarea>
            <div class="file-upload-row" style="margin-top: 15px;">
                <div><strong>Academic Audit Reports PDF</strong></div>
                <input aria-label="File 8 2" type="file" name="file_8_2" accept=".pdf">
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
                            <th scope="col" style="text-align: left;">Item</th>
                            <th scope="col">CAYm1</th>
                            <th scope="col">CAYm2</th>
                            <th scope="col">CAYm3</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>No. of faculty members with Ph.D. degree</td>
                            <td><input aria-label="T83 Phd Caym1" type="number" min="0" id="t83_phd_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T83 Phd Caym2" type="number" min="0" id="t83_phd_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T83 Phd Caym3" type="number" min="0" id="t83_phd_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>No. of publications in peer reviewed journals</td>
                            <td><input aria-label="T83 Jour Caym1" type="number" min="0" id="t83_jour_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T83 Jour Caym2" type="number" min="0" id="t83_jour_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T83 Jour Caym3" type="number" min="0" id="t83_jour_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>No. of publications in conferences</td>
                            <td><input aria-label="T83 Conf Caym1" type="number" min="0" id="t83_conf_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T83 Conf Caym2" type="number" min="0" id="t83_conf_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T83 Conf Caym3" type="number" min="0" id="t83_conf_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
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
                            <th scope="col" style="text-align: left;">Item</th>
                            <th scope="col">CAYm1</th>
                            <th scope="col">CAYm2</th>
                            <th scope="col">CAYm3</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Academic Performance Index (API) of the First-Year Students in the Program (Refer to section 4.3)</td>
                            <td><input aria-label="T84 Api1 Caym1" type="text" id="t84_api1_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T84 Api1 Caym2" type="text" id="t84_api1_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T84 Api1 Caym3" type="text" id="t84_api1_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>Academic Performance Index of the Second-Year Students in the Program (Refer to section 4.4)</td>
                            <td><input aria-label="T84 Api2 Caym1" type="text" id="t84_api2_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T84 Api2 Caym2" type="text" id="t84_api2_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T84 Api2 Caym3" type="text" id="t84_api2_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>Academic Performance Index of the Third Year Students in the Program (Refer to section 4.5)</td>
                            <td><input aria-label="T84 Api3 Caym1" type="text" id="t84_api3_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T84 Api3 Caym2" type="text" id="t84_api3_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T84 Api3 Caym3" type="text" id="t84_api3_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php 
        $crit_id = 8;
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
        'text_8_1_1', 'text_8_1_2', 'text_8_2',
        't83_phd_caym1', 't83_phd_caym2', 't83_phd_caym3',
        't83_jour_caym1', 't83_jour_caym2', 't83_jour_caym3',
        't83_conf_caym1', 't83_conf_caym2', 't83_conf_caym3',
        't84_api1_caym1', 't84_api1_caym2', 't84_api1_caym3',
        't84_api2_caym1', 't84_api2_caym2', 't84_api2_caym3',
        't84_api3_caym1', 't84_api3_caym2', 't84_api3_caym3'
    ];

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

    async function saveCriterionData(silent = false) {
        saveCurrentLevel();
        return await saveAccreditationCriterion(8, { levelData: levelData }, silent);
    }


    window.onload = function() { initData(); };
</script>
</body>
</html>

