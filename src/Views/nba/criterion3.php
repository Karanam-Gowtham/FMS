<?php
$criterion_num = 3;
$criterion_title = "Course Outcomes and Program Outcomes";
$extra_head = '<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js" integrity="sha512-r22gChDnGvBylk90+2e/ycr3RVrDi8DIOkIGNhJlKfuyQM4tIRAI062MaV8sfjQKYVGjOBaZBOA87z+IhZE9DA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';
include __DIR__ . '/../partials/nba_header.php';
?>

    <!-- Section 3.1 -->
    <div class="section-card">
        <h3>3.1 Evaluation of Continuous Assessment: Assignments, Unit Tests, Mid-Term, etc.</h3>
        <div class="section-desc">
            (Describe the process of evaluation followed during continuous assessment to maintain quality of assessment; constructive alignment of questions with COs and hence POs/ PSOs. Details to be kept in course files for evaluation.)
        </div>
        
        <div class="pdf-dropzone" role="button" tabindex="0" onclick="document.getElementById('pdfInput_3_1').click()" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.1</h4>
            <p>Upload a single PDF document detailing continuous assessment evaluation.</p>
            <div class="upload-overlay" id="overlay_3_1">Uploading...</div>
            <input aria-label="PdfInput 3 1" type="file" id="pdfInput_3_1" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_1')">
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
        
        <div class="pdf-dropzone" role="button" tabindex="0" onclick="document.getElementById('pdfInput_3_2').click()" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.2</h4>
            <p>Upload a single PDF document detailing SEE question paper evaluation.</p>
            <div class="upload-overlay" id="overlay_3_2">Uploading...</div>
            <input aria-label="PdfInput 3 2" type="file" id="pdfInput_3_2" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_2')">
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
        
        <div class="pdf-dropzone" role="button" tabindex="0" onclick="document.getElementById('pdfInput_3_3').click()" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.3</h4>
            <p>Upload a single PDF document detailing laboratory evaluation rubrics.</p>
            <div class="upload-overlay" id="overlay_3_3">Uploading...</div>
            <input aria-label="PdfInput 3 3" type="file" id="pdfInput_3_3" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_3')">
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
        
        <div class="pdf-dropzone" role="button" tabindex="0" onclick="document.getElementById('pdfInput_3_4').click()" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.4</h4>
            <p>Upload a single PDF document detailing internship evaluation rubrics.</p>
            <div class="upload-overlay" id="overlay_3_4">Uploading...</div>
            <input aria-label="PdfInput 3 4" type="file" id="pdfInput_3_4" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_4')">
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
        
        <div class="pdf-dropzone" role="button" tabindex="0" onclick="document.getElementById('pdfInput_3_5').click()" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.5</h4>
            <p>Upload a single PDF document detailing project evaluation rubrics.</p>
            <div class="upload-overlay" id="overlay_3_5">Uploading...</div>
            <input aria-label="PdfInput 3 5" type="file" id="pdfInput_3_5" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_5')">
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
                    <th scope="col" style="width: 20%;">Type of Activity</th>
                    <th scope="col" style="width: 25%;">Title</th>
                    <th scope="col" style="width: 25%;">Sustainable Goals Addressed</th>
                    <th scope="col" style="width: 20%;">PDF (Proof)</th>
                    <th scope="col" style="width: 10%;">Action</th>
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
        
        <div class="pdf-dropzone" role="button" tabindex="0" onclick="document.getElementById('pdfInput_3_7_1').click()" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.7.1</h4>
            <p>Upload a single PDF document detailing assessment tools and processes.</p>
            <div class="upload-overlay" id="overlay_3_7_1">Uploading...</div>
            <input aria-label="PdfInput 3 7 1" type="file" id="pdfInput_3_7_1" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_7_1')">
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
        
        <div class="pdf-dropzone" role="button" tabindex="0" onclick="document.getElementById('pdfInput_3_7_2').click()" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 3.7.2</h4>
            <p>Upload a single PDF document detailing attainment levels.</p>
            <div class="upload-overlay" id="overlay_3_7_2">Uploading...</div>
            <input aria-label="PdfInput 3 7 2" type="file" id="pdfInput_3_7_2" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '3_7_2')">
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
                <caption class="visually-hidden">Direct Assessment Matrix</caption>
                <thead><tr><th scope="col">Course / Outcome</th></tr></thead>
                <tbody><!-- Dynamically Rendered --></tbody>
            </table>
        </div>
        <div style="margin-top: 15px; display: flex; gap: 10px;">
            <button type="button" class="btn-add" onclick="addRow3_8()">+ Add Course Row</button>
            <button type="button" class="btn-add" onclick="addColumn3_8()">+ Add Column (PSO)</button>
            <button type="button" class="btn-add" style="background-color: #28a745;" onclick="document.getElementById('excel_3_8_1').click()">Upload Excel</button>
            <input aria-label="Excel 3 8 1" type="file" id="excel_3_8_1" style="display: none;" accept=".xlsx, .xls, .csv" onchange="importExcel(this, 'table_3_8', 'course')">
        </div>
        
        <p style="font-size: 0.85rem; color: #666; margin-top: 15px;">
            <strong>Note:</strong><br>
            &bull; C101, C102 are indicative courses in the first year. Similarly, C409 is final year course. First numeric digit indicates year of study and remaining two digits indicate course nos. in the respective year of study.<br>
            &bull; Direct attainment of a PO/PSO is determined by taking average across all courses addressing that PO/PSO.
        </p>

        <hr style="margin: 2rem 0; border: 0; border-top: 1px solid #e9ecef;">
        
        <div class="section-desc">
            <strong>Table No.3.8.2: PO and PSO attainment value using indirect assessment tools.</strong>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="matrix-table" style="min-width: 100%; font-size: 0.9rem;" id="table-3-8-2">
                <caption class="visually-hidden">Indirect Assessment Matrix</caption>
                <thead><tr><th scope="col">Survey / Outcome</th></tr></thead>
                <tbody><!-- Dynamically Rendered --></tbody>
            </table>
        </div>
        <div style="margin-top: 15px; display: flex; gap: 10px;">
            <button type="button" class="btn-add" onclick="addRow3_8_2()">+ Add Survey Row</button>
            <button type="button" class="btn-add" onclick="addColumn3_8_2()">+ Add Column (PSO)</button>
            <button type="button" class="btn-add" style="background-color: #28a745;" onclick="document.getElementById('excel_3_8_2').click()">Upload Excel</button>
            <input aria-label="Excel 3 8 2" type="file" id="excel_3_8_2" style="display: none;" accept=".xlsx, .xls, .csv" onchange="importExcel(this, 'table_3_8_2', 'survey')">
        </div>

        <p style="font-size: 0.85rem; color: #666; margin-top: 15px;">
            <strong>Note:</strong><br>
            &bull; Mention the type of survey conducted and the location of its source.<br>
            &bull; Indirect attainment level of a PO/PSO is determined based on the student exit surveys, employer surveys, etc.
        </p>

        <hr style="margin: 2rem 0; border: 0; border-top: 1px solid #e9ecef;">
        
        <div class="section-desc">
            <strong>Table No.3.8.3: Overall PO and PSO attainment value.</strong><br>
            Overall Attainment = 80% of Direct Attainment + 20% of Indirect Attainment.
        </div>
        
        <div style="overflow-x: auto;">
            <table class="matrix-table" style="min-width: 100%; font-size: 0.9rem;" id="table-3-8-3">
                <caption class="visually-hidden">Overall PO and PSO Attainment Matrix</caption>
                <thead><tr><th scope="col">Assessment / Outcome</th></tr></thead>
                <tbody><!-- Dynamically Rendered via JS --></tbody>
            </table>
        </div>
        <div style="margin-top: 15px; display: flex; gap: 10px;">
            <button type="button" class="btn-add" onclick="addRow3_8_3()">+ Add Assessment Row</button>
            <button type="button" class="btn-add" onclick="addColumn3_8_3()">+ Add Column (PSO)</button>
            <button type="button" class="btn-add" style="background-color: #28a745;" onclick="document.getElementById('excel_3_8_3').click()">Upload Excel</button>
            <input aria-label="Excel 3 8 3" type="file" id="excel_3_8_3" style="display: none;" accept=".xlsx, .xls, .csv" onchange="importExcel(this, 'table_3_8_3', 'assessment')">
        </div>
    </div>

    <?php 
    $crit_id = 3;
    $module = 'nba';
    $save_onclick = 'saveData()';
    include __DIR__ . '/../partials/accreditation_action_bar.php'; 
    ?>


</div>

<script>
window.NBA_CONFIG = {
    deptId: <?= json_encode((string)$dept_id) ?>,
    year: <?= json_encode((string)$year) ?>,
    csrfToken: <?= json_encode(csrfToken()) ?>,
    baseUrl: <?= json_encode(BASE_URL) ?>,
    initialData: <?= json_encode($criteria_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
};
</script>
<script src="<?= BASE_URL ?>/assets/js/nba_criterion3.js"></script>
</body>
</html>

