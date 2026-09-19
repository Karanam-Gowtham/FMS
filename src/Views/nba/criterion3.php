<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> &mdash; FMS</title>
    <link href="<?= BASE_URL ?>/assets/css/layout.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/nba_module.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div id="toast" class="toast">Data saved successfully!</div>

<div class="container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a> &raquo; 
        <a href="dashboard.php?year=<?= urlencode($year) ?>">NBA Accreditation</a> &raquo; 
        Criterion 3
    </div>

    <div class="header-row">
        <h1>Criterion 3: Course Outcomes and Program Outcomes</h1>
        <p>Academic Year: <strong><?= htmlspecialchars($year) ?></strong></p>
    </div>

    <div style="margin-bottom: 2rem; background: #fff; padding: 1rem; border-radius: 8px; border: 1px solid #dee2e6; display: flex; align-items: center; gap: 15px;">
        <label style="font-weight: 600; color: #495057;">Select Department:</label>
        <select id="level-select" style="padding: 8px; border: 1px solid #ced4da; border-radius: 4px; min-width: 250px;" onchange="switchLevel()">
            <?php foreach ($departments as $d): ?>
                <option value="dept_<?= $d['dept_id'] ?>" <?= $d['dept_id'] == $dept_id ? 'selected' : '' ?>>
                    Department: <?= htmlspecialchars($d['dept_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Section 3.1 -->
    <div class="section-card">
        <h3>3.1 Evaluation of Continuous Assessment: Assignments, Unit Tests, Mid-Term, etc.</h3>
        <div class="section-desc">
            (Describe the process of evaluation followed during continuous assessment to maintain quality of assessment; constructive alignment of questions with COs and hence POs/ PSOs. Details to be kept in course files for evaluation.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_3_1').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.1</h4>
            <p>Upload a single PDF document detailing continuous assessment evaluation.</p>
            <div class="upload-overlay" id="overlay_3_1">Uploading...</div>
            <input type="file" id="pdfInput_3_1" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_1')">
        </div>
        <div class="file-preview" id="preview_3_1">
            <a href="#" id="link_3_1" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('3_1')">Remove</button>
        </div>
    </div>

    <!-- Section 3.2 -->
    <div class="section-card">
        <h3>3.2 Evaluation of the Semester End Exam (SEE) Question Paper</h3>
        <div class="section-desc">
            (Describe the process of setting of SEE papers & their evaluation to maintain quality of assessment, constructive alignment of questions with COs and POs/PSOs. Details to be kept in course files for evaluation.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_3_2').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.2</h4>
            <p>Upload a single PDF document detailing SEE question paper evaluation.</p>
            <div class="upload-overlay" id="overlay_3_2">Uploading...</div>
            <input type="file" id="pdfInput_3_2" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_2')">
        </div>
        <div class="file-preview" id="preview_3_2">
            <a href="#" id="link_3_2" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('3_2')">Remove</button>
        </div>
    </div>

    <!-- Section 3.3 -->
    <div class="section-card">
        <h3>3.3 Evaluation of Laboratory Work and Workshop (Continuous and SEE)</h3>
        <div class="section-desc">
            (Provide details of rubrics used to assess learnings in laboratories and workshops linking with COs and POs/PSOs targeted. Evidence of student assessments through rubrics to be kept in course files for evaluation.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_3_3').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.3</h4>
            <p>Upload a single PDF document detailing laboratory evaluation rubrics.</p>
            <div class="upload-overlay" id="overlay_3_3">Uploading...</div>
            <input type="file" id="pdfInput_3_3" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_3')">
        </div>
        <div class="file-preview" id="preview_3_3">
            <a href="#" id="link_3_3" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('3_3')">Remove</button>
        </div>
    </div>

    <!-- Section 3.4 -->
    <div class="section-card">
        <h3>3.4 Evaluation of Industrial Training/ Internship (Continuous and SEE)</h3>
        <div class="section-desc">
            (Provide details of rubrics used to assess learnings in internships/industrial trainings linking POs/PSOs targeted for attainment. Evidence of student assessments through rubrics to be kept in course files for evaluation.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_3_4').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.4</h4>
            <p>Upload a single PDF document detailing internship evaluation rubrics.</p>
            <div class="upload-overlay" id="overlay_3_4">Uploading...</div>
            <input type="file" id="pdfInput_3_4" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_4')">
        </div>
        <div class="file-preview" id="preview_3_4">
            <a href="#" id="link_3_4" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('3_4')">Remove</button>
        </div>
    </div>

    <!-- Section 3.5 -->
    <div class="section-card">
        <h3>3.5 Evaluation of Projects</h3>
        <div class="section-desc">
            (Provide details of rubrics used to assess learnings in projects linking POs/PSOs targeted for attainment. Evidence of student assessments through rubrics to be kept in course files for evaluation.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_3_5').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.5</h4>
            <p>Upload a single PDF document detailing project evaluation rubrics.</p>
            <div class="upload-overlay" id="overlay_3_5">Uploading...</div>
            <input type="file" id="pdfInput_3_5" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_5')">
        </div>
        <div class="file-preview" id="preview_3_5">
            <a href="#" id="link_3_5" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('3_5')">Remove</button>
        </div>
    </div>

    <!-- Section 3.6 -->
    <div class="section-card">
        <h3>3.6 Evidence of Addressing Sustainable Development Goals (SDG)</h3>
        <div class="section-desc">
            (Provide details of student work carried out to meet sustainable development goals such as research work, project work, student activities etc. Evidence in the form of a portfolio to be made available during the visit.)
        </div>
        
        <table class="matrix-table" style="width: 100%; font-size: 0.9rem;">
            <thead>
                <tr>
                    <th style="width: 20%;">Type of Activity</th>
                    <th style="width: 25%;">Title</th>
                    <th style="width: 25%;">Sustainable Goals Addressed</th>
                    <th style="width: 20%;">PDF (Proof)</th>
                    <th style="width: 10%;">Action</th>
                </tr>
            </thead>
            <tbody id="table-3-6-body">
                <!-- Dynamically rendered -->
            </tbody>
        </table>
        <button type="button" class="btn-add" style="margin-top: 15px;" onclick="addTableRow3_6()">+ Add Activity</button>
    </div>

    <!-- Section 3.7.1 -->
    <div class="section-card">
        <h3>3.7.1 Describe the Assessment Tools and Processes Used to Gather the Data for the Evaluation of Course Outcome</h3>
        <div class="section-desc">
            (Describe different assessment tools (semester end examinations, mid-semester tests, laboratory examinations, student portfolios etc.,) to measure the student learning and hence attainment of course outcomes.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_3_7_1').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.7.1</h4>
            <p>Upload a single PDF document detailing assessment tools and processes.</p>
            <div class="upload-overlay" id="overlay_3_7_1">Uploading...</div>
            <input type="file" id="pdfInput_3_7_1" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_7_1')">
        </div>
        <div class="file-preview" id="preview_3_7_1">
            <a href="#" id="link_3_7_1" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('3_7_1')">Remove</button>
        </div>
    </div>

    <!-- Section 3.7.2 -->
    <div class="section-card">
        <h3>3.7.2 Record the Attainment of Course Outcomes of all Courses with Respect to Set Attainment Levels</h3>
        <div class="section-desc">
            (Program shall set course outcome attainment levels for each course. Measuring CO attainment through Continuous Internal Examinations (CIE) and Semester End Examination (SEE) needs to be detailed. Target may be stated in terms of percentage of students getting more than class average marks or set by the program in each of the associated COs in the assessment instruments (midterm tests, assignments, mini projects, reports and presentations etc. as mapped with the COs.))
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_3_7_2').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.7.2</h4>
            <p>Upload a single PDF document detailing attainment levels.</p>
            <div class="upload-overlay" id="overlay_3_7_2">Uploading...</div>
            <input type="file" id="pdfInput_3_7_2" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_7_2')">
        </div>
        <div class="file-preview" id="preview_3_7_2">
            <a href="#" id="link_3_7_2" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('3_7_2')">Remove</button>
        </div>
    </div>

    <!-- Section 3.8 -->
    <div class="section-card">
        <h3>3.8 Attainment of Program Outcomes and Program Specific Outcomes (25)</h3>
        <div class="section-desc">
            (The attainment of POs and PSOs by direct assessment based on student performance and indirect assessment based on surveys are to be presented through program level Course-PO&PSO matrices as indicated.)<br><br>
            <strong>Table No.3.8.1: PO and PSO attainment value using direct assessment tools.</strong>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="matrix-table" style="min-width: 100%; font-size: 0.9rem;" id="table-3-8">
                <!-- Dynamically Rendered -->
            </table>
        </div>
        <div style="margin-top: 15px; display: flex; gap: 10px;">
            <button type="button" class="btn-add" onclick="addRow3_8()">+ Add Course Row</button>
            <button type="button" class="btn-add" onclick="addColumn3_8()">+ Add Column (PSO)</button>
            <button type="button" class="btn-add" style="background-color: #28a745;" onclick="document.getElementById('excel_3_8_1').click()">Upload Excel</button>
            <input type="file" id="excel_3_8_1" style="display: none;" accept=".xlsx, .xls, .csv" onchange="importExcel(this, 'table_3_8', 'course')">
        </div>
        
        <p style="font-size: 0.85rem; color: #666; margin-top: 15px;">
            <strong>Note:</strong><br>
            ❖ C101, C102 are indicative courses in the first year. Similarly, C409 is final year course. First numeric digit indicates year of study and remaining two digits indicate course nos. in the respective year of study.<br>
            ❖ Direct attainment of a PO/PSO is determined by taking average across all courses addressing that PO/PSO.
        </p>

        <hr style="margin: 2rem 0; border: 0; border-top: 1px solid #e9ecef;">
        
        <div class="section-desc">
            <strong>Table No.3.8.2: PO and PSO attainment value using indirect assessment tools.</strong>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="matrix-table" style="min-width: 100%; font-size: 0.9rem;" id="table-3-8-2">
                <!-- Dynamically Rendered -->
            </table>
        </div>
        <div style="margin-top: 15px; display: flex; gap: 10px;">
            <button type="button" class="btn-add" onclick="addRow3_8_2()">+ Add Survey Row</button>
            <button type="button" class="btn-add" onclick="addColumn3_8_2()">+ Add Column (PSO)</button>
            <button type="button" class="btn-add" style="background-color: #28a745;" onclick="document.getElementById('excel_3_8_2').click()">Upload Excel</button>
            <input type="file" id="excel_3_8_2" style="display: none;" accept=".xlsx, .xls, .csv" onchange="importExcel(this, 'table_3_8_2', 'survey')">
        </div>

        <p style="font-size: 0.85rem; color: #666; margin-top: 15px;">
            <strong>Note:</strong><br>
            ❖ Mention the type of survey conducted and the location of its source.<br>
            ❖ Indirect attainment level of a PO/PSO is determined based on the student exit surveys, employer surveys, etc.
        </p>

        <hr style="margin: 2rem 0; border: 0; border-top: 1px solid #e9ecef;">
        
        <div class="section-desc">
            <strong>Table No.3.8.3: Overall PO and PSO attainment value.</strong><br>
            Overall Attainment = 80% of Direct Attainment + 20% of Indirect Attainment.
        </div>
        
        <div style="overflow-x: auto;">
            <table class="matrix-table" style="min-width: 100%; font-size: 0.9rem;" id="table-3-8-3">
                <!-- Dynamically Rendered via JS -->
            </table>
        </div>
        <div style="margin-top: 15px; display: flex; gap: 10px;">
            <button type="button" class="btn-add" onclick="addRow3_8_3()">+ Add Assessment Row</button>
            <button type="button" class="btn-add" onclick="addColumn3_8_3()">+ Add Column (PSO)</button>
            <button type="button" class="btn-add" style="background-color: #28a745;" onclick="document.getElementById('excel_3_8_3').click()">Upload Excel</button>
            <input type="file" id="excel_3_8_3" style="display: none;" accept=".xlsx, .xls, .csv" onchange="importExcel(this, 'table_3_8_3', 'assessment')">
        </div>
    </div>

    <div style="text-align: right; margin-bottom: 4rem;">
        <button type="button" class="btn-save" onclick="saveData()">Save Criterion 3</button>
    </div>

</div>

<script>
    const year = <?= json_encode($year) ?>;
    const initialData = <?= json_encode($criteria_data) ?>;
    let levelData = {};
    let currentLevel = '';

    const sdgOptions = [
        "No Poverty", "Zero Hunger", "Good Health and Well-being", "Quality Education", "Gender Equality",
        "Clean Water and Sanitation", "Affordable and Clean Energy", "Decent Work and Economic Growth",
        "Industry, Innovation and Infrastructure", "Reduced Inequalities", "Sustainable Cities and Communities",
        "Responsible Consumption and Production", "Climate Action", "Life Below Water", "Life on Land",
        "Peace, Justice and Strong Institutions", "Partnerships for the Goals"
    ];

    function initData() {
        levelData = initialData.levelData || {};
        
        const defaultDept = 'dept_<?= $dept_id ?>';
        if (!levelData[defaultDept]) {
            levelData[defaultDept] = { pdf_3_1: '', pdf_3_2: '', pdf_3_3: '', pdf_3_4: '', pdf_3_5: '', table_3_6: [], pdf_3_7_1: '', pdf_3_7_2: '', table_3_8: { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] }, table_3_8_2: { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] }, table_3_8_3: { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] } };
        }

        switchLevel();
    }

    function switchLevel() {
        const select = document.getElementById('level-select');
        currentLevel = select.value;
        
        if (!levelData[currentLevel]) {
            levelData[currentLevel] = { pdf_3_1: '', pdf_3_2: '', pdf_3_3: '', pdf_3_4: '', pdf_3_5: '', table_3_6: [], pdf_3_7_1: '', pdf_3_7_2: '', table_3_8: { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] }, table_3_8_2: { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] }, table_3_8_3: { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] } };
        }
        if (!levelData[currentLevel].table_3_6) levelData[currentLevel].table_3_6 = [];
        if (!levelData[currentLevel].table_3_8) levelData[currentLevel].table_3_8 = { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] };
        if (!levelData[currentLevel].table_3_8_2) levelData[currentLevel].table_3_8_2 = { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] };
        if (!levelData[currentLevel].table_3_8_3) levelData[currentLevel].table_3_8_3 = { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] };

        ['3_1', '3_2', '3_3', '3_4', '3_5', '3_7_1', '3_7_2'].forEach(section => {
            updatePDFUI(section);
        });
        
        renderTable3_6();
        renderTable3_8();
        renderTable3_8_2();
        renderTable3_8_3();
    }

    // --- Table 3.8.1 Functions (Direct) ---
    function renderTable3_8() {
        const table = document.getElementById('table-3-8');
        const data = levelData[currentLevel].table_3_8;
        
        let html = '<thead><tr><th>Course</th>';
        data.columns.forEach((col, cIdx) => {
            html += `<th>
                        ${col}
                        ${cIdx >= 11 ? `<br><button class="btn-remove" style="padding: 2px 5px; font-size: 0.7rem;" onclick="removeColumn3_8(${cIdx})">✖</button>` : ''}
                     </th>`;
        });
        html += '<th>Action</th></tr></thead><tbody>';
        
        const sums = {};
        const counts = {};
        data.columns.forEach(col => { sums[col] = 0; counts[col] = 0; });

        data.rows.forEach((row, rIdx) => {
            html += `<tr>
                <td style="padding: 4px;">
                    <input type="text" value="${row.course || ''}" onchange="updateRow3_8(${rIdx}, 'course', this.value)" style="width: 100px; padding: 4px; box-sizing: border-box;" placeholder="e.g. C101">
                </td>`;
            
            data.columns.forEach(col => {
                let val = row.values ? (row.values[col] || '') : '';
                if (val !== '' && !isNaN(val)) {
                    sums[col] += parseFloat(val);
                    counts[col]++;
                }
                html += `
                    <td style="padding: 4px;">
                        <input type="number" step="0.01" value="${val}" onchange="updateCell3_8(${rIdx}, '${col}', this.value)" class="matrix-input">
                    </td>
                `;
            });

            html += `
                <td style="padding: 4px;">
                    <button type="button" class="btn-remove" onclick="removeRow3_8(${rIdx})">Delete</button>
                </td>
            </tr>`;
        });

        // Averages row
        html += `<tr><td style="font-weight: bold; background: #f8f9fa;">Direct Attainment</td>`;
        data.columns.forEach(col => {
            let avg = counts[col] > 0 ? (sums[col] / counts[col]).toFixed(2) : '-';
            html += `<td style="font-weight: bold; background: #f8f9fa;" id="direct_avg_${col}">${avg}</td>`;
        });
        html += `<td style="background: #f8f9fa;"></td></tr>`;

        html += '</tbody>';
        table.innerHTML = html;
        
        // Trigger 3.8.3 render as direct affects overall
        if (document.getElementById('table-3-8-3')) {
            renderTable3_8_3();
        }
    }

    function addRow3_8() {
        levelData[currentLevel].table_3_8.rows.push({ course: '', values: {} });
        renderTable3_8();
    }

    function removeRow3_8(rIdx) {
        if(confirm("Delete this row?")) {
            levelData[currentLevel].table_3_8.rows.splice(rIdx, 1);
            renderTable3_8();
            saveData(true);
        }
    }

    function addColumn3_8() {
        const colName = prompt("Enter new column name (e.g., PSO1, PSO2):");
        if(colName && colName.trim() !== '') {
            const cleanName = colName.trim();
            if (levelData[currentLevel].table_3_8.columns.includes(cleanName)) {
                alert("Column already exists in Direct table!");
                return;
            }
            levelData[currentLevel].table_3_8.columns.push(cleanName);
            // Auto add to indirect table as well
            if (!levelData[currentLevel].table_3_8_2.columns.includes(cleanName)) {
                levelData[currentLevel].table_3_8_2.columns.push(cleanName);
            }
            renderTable3_8();
            renderTable3_8_2();
            saveData(true);
        }
    }

    function removeColumn3_8(cIdx) {
        if(confirm("Delete this column from all tables?")) {
            const col = levelData[currentLevel].table_3_8.columns[cIdx];
            levelData[currentLevel].table_3_8.columns.splice(cIdx, 1);
            
            levelData[currentLevel].table_3_8.rows.forEach(row => {
                if (row.values && row.values[col] !== undefined) delete row.values[col];
            });
            
            // Remove from 3.8.2 as well
            const cIdx2 = levelData[currentLevel].table_3_8_2.columns.indexOf(col);
            if (cIdx2 !== -1) {
                levelData[currentLevel].table_3_8_2.columns.splice(cIdx2, 1);
                levelData[currentLevel].table_3_8_2.rows.forEach(row => {
                    if (row.values && row.values[col] !== undefined) delete row.values[col];
                });
            }
            
            renderTable3_8();
            renderTable3_8_2();
            saveData(true);
        }
    }

    function updateRow3_8(rIdx, field, value) {
        levelData[currentLevel].table_3_8.rows[rIdx][field] = value;
        renderTable3_8();
    }

    function updateCell3_8(rIdx, col, value) {
        if (!levelData[currentLevel].table_3_8.rows[rIdx].values) {
            levelData[currentLevel].table_3_8.rows[rIdx].values = {};
        }
        levelData[currentLevel].table_3_8.rows[rIdx].values[col] = value;
        renderTable3_8();
        saveData(true);
    }

    // --- Table 3.8.2 Functions ---
    function renderTable3_8_2() {
        const table = document.getElementById('table-3-8-2');
        const data = levelData[currentLevel].table_3_8_2;
        
        let html = '<thead><tr><th>Name of the Survey</th>';
        data.columns.forEach((col, cIdx) => {
            html += `<th>
                        ${col}
                        ${cIdx >= 11 ? `<br><button class="btn-remove" style="padding: 2px 5px; font-size: 0.7rem;" onclick="removeColumn3_8_2(${cIdx})">✖</button>` : ''}
                     </th>`;
        });
        html += '<th>Action</th></tr></thead><tbody>';
        
        const sums = {};
        const counts = {};
        data.columns.forEach(col => { sums[col] = 0; counts[col] = 0; });

        data.rows.forEach((row, rIdx) => {
            html += `<tr>
                <td style="padding: 4px;">
                    <input type="text" value="${row.survey || ''}" onchange="updateRow3_8_2(${rIdx}, 'survey', this.value)" style="width: 150px; padding: 4px; box-sizing: border-box;" placeholder="e.g. Survey 1">
                </td>`;
            
            data.columns.forEach(col => {
                let val = row.values ? (row.values[col] || '') : '';
                if (val !== '' && !isNaN(val)) {
                    sums[col] += parseFloat(val);
                    counts[col]++;
                }
                html += `
                    <td style="padding: 4px;">
                        <input type="number" step="0.01" value="${val}" onchange="updateCell3_8_2(${rIdx}, '${col}', this.value)" class="matrix-input">
                    </td>
                `;
            });

            html += `
                <td style="padding: 4px;">
                    <button type="button" class="btn-remove" onclick="removeRow3_8_2(${rIdx})">Delete</button>
                </td>
            </tr>`;
        });

        // Averages row
        html += `<tr><td style="font-weight: bold; background: #f8f9fa;">Indirect Attainment</td>`;
        data.columns.forEach(col => {
            let avg = counts[col] > 0 ? (sums[col] / counts[col]).toFixed(2) : '-';
            html += `<td style="font-weight: bold; background: #f8f9fa;" id="indirect_avg_${col}">${avg}</td>`;
        });
        html += `<td style="background: #f8f9fa;"></td></tr>`;

        html += '</tbody>';
        table.innerHTML = html;
        
        // Trigger 3.8.3 render as indirect affects overall
        if (document.getElementById('table-3-8-3')) {
            renderTable3_8_3();
        }
    }

    function addRow3_8_2() {
        levelData[currentLevel].table_3_8_2.rows.push({ survey: '', values: {} });
        renderTable3_8_2();
    }

    function removeRow3_8_2(rIdx) {
        if(confirm("Delete this survey row?")) {
            levelData[currentLevel].table_3_8_2.rows.splice(rIdx, 1);
            renderTable3_8_2();
            saveData(true);
        }
    }

    function addColumn3_8_2() {
        const colName = prompt("Enter new column name (e.g., PSO1, PSO2):");
        if(colName && colName.trim() !== '') {
            const cleanName = colName.trim();
            if (levelData[currentLevel].table_3_8_2.columns.includes(cleanName)) {
                alert("Column already exists in Indirect table!");
                return;
            }
            levelData[currentLevel].table_3_8_2.columns.push(cleanName);
            // Auto add to direct table as well
            if (!levelData[currentLevel].table_3_8.columns.includes(cleanName)) {
                levelData[currentLevel].table_3_8.columns.push(cleanName);
            }
            renderTable3_8();
            renderTable3_8_2();
            saveData(true);
        }
    }

    function removeColumn3_8_2(cIdx) {
        if(confirm("Delete this column from all tables?")) {
            const col = levelData[currentLevel].table_3_8_2.columns[cIdx];
            levelData[currentLevel].table_3_8_2.columns.splice(cIdx, 1);
            
            levelData[currentLevel].table_3_8_2.rows.forEach(row => {
                if (row.values && row.values[col] !== undefined) delete row.values[col];
            });
            
            // Remove from 3.8 as well
            const cIdx1 = levelData[currentLevel].table_3_8.columns.indexOf(col);
            if (cIdx1 !== -1) {
                levelData[currentLevel].table_3_8.columns.splice(cIdx1, 1);
                levelData[currentLevel].table_3_8.rows.forEach(row => {
                    if (row.values && row.values[col] !== undefined) delete row.values[col];
                });
            }
            
            renderTable3_8();
            renderTable3_8_2();
            saveData(true);
        }
    }

    function updateRow3_8_2(rIdx, field, value) {
        levelData[currentLevel].table_3_8_2.rows[rIdx][field] = value;
        renderTable3_8_2();
    }

    function updateCell3_8_2(rIdx, col, value) {
        if (!levelData[currentLevel].table_3_8_2.rows[rIdx].values) {
            levelData[currentLevel].table_3_8_2.rows[rIdx].values = {};
        }
        levelData[currentLevel].table_3_8_2.rows[rIdx].values[col] = value;
        renderTable3_8_2();
        saveData(true);
    }

    // --- Table 3.8.3 Functions (Overall) ---
    function renderTable3_8_3() {
        const table = document.getElementById('table-3-8-3');
        if (!table) return;
        
        const data = levelData[currentLevel].table_3_8_3;
        
        let html = '<thead><tr><th>Assessment</th>';
        data.columns.forEach((col, cIdx) => {
            html += `<th>
                        ${col}
                        ${cIdx >= 11 ? `<br><button class="btn-remove" style="padding: 2px 5px; font-size: 0.7rem;" onclick="removeColumn3_8_3(${cIdx})">✖</button>` : ''}
                     </th>`;
        });
        html += '<th>Action</th></tr></thead><tbody>';

        data.rows.forEach((row, rIdx) => {
            html += `<tr>
                <td style="padding: 4px;">
                    <input type="text" value="${row.assessment || ''}" onchange="updateRow3_8_3(${rIdx}, 'assessment', this.value)" style="width: 150px; padding: 4px; box-sizing: border-box;" placeholder="e.g. Direct Attainment">
                </td>`;
            
            data.columns.forEach(col => {
                let val = row.values ? (row.values[col] || '') : '';
                html += `
                    <td style="padding: 4px;">
                        <input type="number" step="0.01" value="${val}" onchange="updateCell3_8_3(${rIdx}, '${col}', this.value)" class="matrix-input">
                    </td>
                `;
            });

            html += `
                <td style="padding: 4px;">
                    <button type="button" class="btn-remove" onclick="removeRow3_8_3(${rIdx})">Delete</button>
                </td>
            </tr>`;
        });

        html += '</tbody>';
        table.innerHTML = html;
    }

    function addRow3_8_3() {
        levelData[currentLevel].table_3_8_3.rows.push({ assessment: '', values: {} });
        renderTable3_8_3();
    }

    function removeRow3_8_3(rIdx) {
        if(confirm("Delete this row?")) {
            levelData[currentLevel].table_3_8_3.rows.splice(rIdx, 1);
            renderTable3_8_3();
            saveData(true);
        }
    }

    function addColumn3_8_3() {
        const colName = prompt("Enter new column name (e.g., PSO1, PSO2):");
        if(colName && colName.trim() !== '') {
            const cleanName = colName.trim();
            if (levelData[currentLevel].table_3_8_3.columns.includes(cleanName)) {
                alert("Column already exists!");
                return;
            }
            levelData[currentLevel].table_3_8_3.columns.push(cleanName);
            renderTable3_8_3();
            saveData(true);
        }
    }

    function removeColumn3_8_3(cIdx) {
        if(confirm("Delete this column?")) {
            const col = levelData[currentLevel].table_3_8_3.columns[cIdx];
            levelData[currentLevel].table_3_8_3.columns.splice(cIdx, 1);
            
            levelData[currentLevel].table_3_8_3.rows.forEach(row => {
                if (row.values && row.values[col] !== undefined) delete row.values[col];
            });
            
            renderTable3_8_3();
            saveData(true);
        }
    }

    function updateRow3_8_3(rIdx, field, value) {
        levelData[currentLevel].table_3_8_3.rows[rIdx][field] = value;
        renderTable3_8_3();
    }

    function updateCell3_8_3(rIdx, col, value) {
        if (!levelData[currentLevel].table_3_8_3.rows[rIdx].values) {
            levelData[currentLevel].table_3_8_3.rows[rIdx].values = {};
        }
        levelData[currentLevel].table_3_8_3.rows[rIdx].values[col] = value;
        renderTable3_8_3();
        saveData(true);
    }

    function importExcel(input, tableKey, firstColumnKey) {
        const file = input.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {type: 'array'});
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                
                // Convert to 2D array
                const json = XLSX.utils.sheet_to_json(worksheet, {header: 1, defval: ''});
                
                if (json.length === 0) {
                    alert("Excel file is empty!");
                    return;
                }

                // The first row is the header, e.g., ["Course", "PO1", "PO2", ...]
                let headers = json[0].map(h => String(h).trim()).filter(h => h !== '');
                
                if (headers.length === 0) {
                    alert("No headers found in the Excel file.");
                    return;
                }

                // Remove the very first column name ("Course", "Name of the Survey", "Assessment")
                headers.shift();

                // Setup levelData table structure
                levelData[currentLevel][tableKey].columns = headers;
                levelData[currentLevel][tableKey].rows = [];
                
                // Loop through data rows (skip the first row)
                for (let i = 1; i < json.length; i++) {
                    let rowArray = json[i];
                    // Check if row is just empty strings
                    if (rowArray.every(val => val === '')) continue;
                    
                    let firstColValue = rowArray[0] ? String(rowArray[0]).trim() : '';
                    if (!firstColValue) continue;

                    let rowObj = { values: {} };
                    rowObj[firstColumnKey] = firstColValue;
                    
                    for (let j = 0; j < headers.length; j++) {
                        let val = rowArray[j + 1];
                        if (val !== undefined && val !== '') {
                            rowObj.values[headers[j]] = val;
                        }
                    }
                    
                    levelData[currentLevel][tableKey].rows.push(rowObj);
                }
                
                // Re-render based on tableKey
                if (tableKey === 'table_3_8') renderTable3_8();
                if (tableKey === 'table_3_8_2') renderTable3_8_2();
                if (tableKey === 'table_3_8_3') renderTable3_8_3();
                
                saveData(true);
                alert("Excel data successfully imported!");
            } catch (err) {
                console.error(err);
                alert("Error importing Excel file. Please check the file format.");
            }
        };
        reader.readAsArrayBuffer(file);
        
        // Reset input
        input.value = '';
    }

    function renderTable3_6() {
        const table = document.getElementById('table-3-8');
        const data = levelData[currentLevel].table_3_8;
        
        let html = '<thead><tr><th>Course</th>';
        data.columns.forEach((col, cIdx) => {
            html += `<th>
                        ${col}
                        ${cIdx >= 11 ? `<br><button class="btn-remove" style="padding: 2px 5px; font-size: 0.7rem;" onclick="removeColumn3_8(${cIdx})">✖</button>` : ''}
                     </th>`;
        });
        html += '<th>Action</th></tr></thead><tbody>';
        
        const sums = {};
        const counts = {};
        data.columns.forEach(col => { sums[col] = 0; counts[col] = 0; });

        data.rows.forEach((row, rIdx) => {
            html += `<tr>
                <td style="padding: 4px;">
                    <input type="text" value="${row.course || ''}" onchange="updateRow3_8(${rIdx}, 'course', this.value)" style="width: 100px; padding: 4px; box-sizing: border-box;" placeholder="e.g. C101">
                </td>`;
            
            data.columns.forEach(col => {
                let val = row.values ? (row.values[col] || '') : '';
                if (val !== '' && !isNaN(val)) {
                    sums[col] += parseFloat(val);
                    counts[col]++;
                }
                html += `
                    <td style="padding: 4px;">
                        <input type="number" step="0.01" value="${val}" onchange="updateCell3_8(${rIdx}, '${col}', this.value)" class="matrix-input">
                    </td>
                `;
            });

            html += `
                <td style="padding: 4px;">
                    <button type="button" class="btn-remove" onclick="removeRow3_8(${rIdx})">Delete</button>
                </td>
            </tr>`;
        });

        // Averages row
        html += `<tr><td style="font-weight: bold; background: #f8f9fa;">Direct Attainment</td>`;
        data.columns.forEach(col => {
            let avg = counts[col] > 0 ? (sums[col] / counts[col]).toFixed(2) : '-';
            html += `<td style="font-weight: bold; background: #f8f9fa;">${avg}</td>`;
        });
        html += `<td style="background: #f8f9fa;"></td></tr>`;

        html += '</tbody>';
        table.innerHTML = html;
    }

    function addRow3_8() {
        levelData[currentLevel].table_3_8.rows.push({ course: '', values: {} });
        renderTable3_8();
    }

    function removeRow3_8(rIdx) {
        if(confirm("Delete this row?")) {
            levelData[currentLevel].table_3_8.rows.splice(rIdx, 1);
            renderTable3_8();
            saveData(true);
        }
    }

    function addColumn3_8() {
        const colName = prompt("Enter new column name (e.g., PSO1, PSO2):");
        if(colName && colName.trim() !== '') {
            if (levelData[currentLevel].table_3_8.columns.includes(colName.trim())) {
                alert("Column already exists!");
                return;
            }
            levelData[currentLevel].table_3_8.columns.push(colName.trim());
            renderTable3_8();
            saveData(true);
        }
    }

    function removeColumn3_8(cIdx) {
        if(confirm("Delete this column?")) {
            const col = levelData[currentLevel].table_3_8.columns[cIdx];
            levelData[currentLevel].table_3_8.columns.splice(cIdx, 1);
            // Cleanup values
            levelData[currentLevel].table_3_8.rows.forEach(row => {
                if (row.values && row.values[col] !== undefined) {
                    delete row.values[col];
                }
            });
            renderTable3_8();
            saveData(true);
        }
    }

    function updateRow3_8(rIdx, field, value) {
        levelData[currentLevel].table_3_8.rows[rIdx][field] = value;
        renderTable3_8();
    }

    function updateCell3_8(rIdx, col, value) {
        if (!levelData[currentLevel].table_3_8.rows[rIdx].values) {
            levelData[currentLevel].table_3_8.rows[rIdx].values = {};
        }
        levelData[currentLevel].table_3_8.rows[rIdx].values[col] = value;
        renderTable3_8();
        saveData(true);
    }

    function renderTable3_6() {
        const tbody = document.getElementById('table-3-6-body');
        tbody.innerHTML = '';
        
        levelData[currentLevel].table_3_6.forEach((row, index) => {
            let sdgSelect = `<select onchange="updateRow3_6(${index}, 'sdg', this.value)" style="width: 100%; padding: 4px;">`;
            sdgSelect += `<option value="">Select SDG...</option>`;
            sdgOptions.forEach(opt => {
                sdgSelect += `<option value="${opt}" ${row.sdg === opt ? 'selected' : ''}>${opt}</option>`;
            });
            sdgSelect += `</select>`;

            let pdfHtml = '';
            if (row.pdf_path) {
                pdfHtml = `
                    <div style="font-size: 0.8rem; background: #e8f5e9; padding: 4px; border-radius: 4px; display: flex; align-items: center; justify-content: space-between; margin-top: 5px;">
                        <a href="<?= BASE_URL ?>/${row.pdf_path}" target="_blank" style="color: #28a745; text-decoration: none; overflow: hidden; text-overflow: ellipsis; max-width: 100px; white-space: nowrap;">View</a>
                        <button type="button" onclick="removeRowPDF3_6(${index})" style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 0;">✖</button>
                    </div>
                `;
            } else {
                pdfHtml = `
                    <div style="font-size: 0.8rem; color: #6c757d; margin-top: 5px;" id="row_uploading_${index}"></div>
                    <button type="button" class="btn-add" style="padding: 4px 8px; font-size: 0.8rem;" onclick="document.getElementById('row_pdf_${index}').click()">Upload PDF</button>
                    <input type="file" id="row_pdf_${index}" style="display: none;" accept="application/pdf" onchange="handleRowPDFUpload(this, ${index})">
                `;
            }

            tbody.innerHTML += `
                <tr>
                    <td style="padding: 4px;">
                        <select onchange="updateRow3_6(${index}, 'type', this.value)" style="width: 100%; padding: 4px;">
                            <option value="">Select...</option>
                            <option value="Research work" ${row.type === 'Research work' ? 'selected' : ''}>Research work</option>
                            <option value="Project work" ${row.type === 'Project work' ? 'selected' : ''}>Project work</option>
                            <option value="Student activities" ${row.type === 'Student activities' ? 'selected' : ''}>Student activities</option>
                        </select>
                    </td>
                    <td style="padding: 4px;">
                        <input type="text" value="${row.title || ''}" onchange="updateRow3_6(${index}, 'title', this.value)" style="width: 100%; padding: 4px; box-sizing: border-box;" placeholder="Enter title">
                    </td>
                    <td style="padding: 4px; text-align: left;">
                        ${sdgSelect}
                    </td>
                    <td style="padding: 4px;">
                        ${pdfHtml}
                    </td>
                    <td style="padding: 4px;">
                        <button type="button" class="btn-remove" onclick="removeRow3_6(${index})">Delete</button>
                    </td>
                </tr>
            `;
        });
    }

    function addTableRow3_6() {
        levelData[currentLevel].table_3_6.push({ type: '', title: '', sdg: '', pdf_path: '' });
        renderTable3_6();
    }

    function updateRow3_6(index, field, value) {
        levelData[currentLevel].table_3_6[index][field] = value;
    }

    function removeRow3_6(index) {
        if(confirm("Delete this row?")) {
            levelData[currentLevel].table_3_6.splice(index, 1);
            renderTable3_6();
            saveData(true);
        }
    }

    async function handleRowPDFUpload(input, index) {
        const file = input.files[0];
        if (!file) return;

        if (file.type !== 'application/pdf') {
            alert('Only PDF files are allowed.');
            input.value = '';
            return;
        }

        document.getElementById('row_uploading_' + index).innerText = 'Uploading...';

        const formData = new FormData();
        formData.append('pdf_file', file);
        formData.append('section', '3_6_row_' + index);

        try {
            const response = await fetch('<?= BASE_URL ?>/public/index.php?route=api/nba/upload_pdf&id=3', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                levelData[currentLevel].table_3_6[index].pdf_path = result.file_path;
                renderTable3_6();
                saveData(true);
            } else {
                alert('Upload failed: ' + result.message);
                document.getElementById('row_uploading_' + index).innerText = '';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred during upload.');
            document.getElementById('row_uploading_' + index).innerText = '';
        }
        input.value = '';
    }

    function removeRowPDF3_6(index) {
        if(confirm("Remove this document?")) {
            levelData[currentLevel].table_3_6[index].pdf_path = '';
            renderTable3_6();
            saveData(true);
        }
    }

    function updatePDFUI(section) {
        const path = levelData[currentLevel]['pdf_' + section];
        const preview = document.getElementById('preview_' + section);
        const link = document.getElementById('link_' + section);
        const dropzone = document.getElementById('pdfInput_' + section).parentElement;

        if (path) {
            link.href = '<?= BASE_URL ?>/' + path;
            preview.style.display = 'flex';
            dropzone.style.display = 'none';
        } else {
            preview.style.display = 'none';
            dropzone.style.display = 'block';
        }
    }

    async function handlePDFUpload(input, section) {
        const file = input.files[0];
        if (!file) return;

        if (file.type !== 'application/pdf') {
            alert('Only PDF files are allowed.');
            input.value = '';
            return;
        }

        const overlay = document.getElementById('overlay_' + section);
        overlay.style.display = 'flex';

        const formData = new FormData();
        formData.append('pdf_file', file);
        formData.append('section', section);

        try {
            const response = await fetch('<?= BASE_URL ?>/public/index.php?route=api/nba/upload_pdf&id=3', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                levelData[currentLevel]['pdf_' + section] = result.file_path;
                updatePDFUI(section);
                saveData(true); // Auto-save after successful upload
            } else {
                alert('Upload failed: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred during upload.');
        } finally {
            overlay.style.display = 'none';
            input.value = ''; // Reset input
        }
    }

    function removePDF(section) {
        if(confirm("Are you sure you want to remove this document?")) {
            levelData[currentLevel]['pdf_' + section] = '';
            updatePDFUI(section);
            saveData(true); // Auto-save after removal
        }
    }

    function saveData(silent = false) {
        const formData = new URLSearchParams();
        formData.append('year', year);
        
        const payload = { levelData: levelData };
        formData.append('json_data', JSON.stringify(payload));

        fetch('<?= BASE_URL ?>/public/index.php?route=api/nba/save&id=3', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (!silent) {
                    const toast = document.getElementById('toast');
                    toast.style.display = 'block';
                    setTimeout(() => toast.style.display = 'none', 3000);
                }
            } else {
                alert('Error saving data: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to communicate with server.');
        });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', initData);

</script>
</body>
</html>
