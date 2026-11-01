<?php
$criterion_num = 1;
$criterion_title = "Outcome-Based Curriculum";
$extra_head = '<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js" integrity="sha512-r22gChDnGvBylk90+2e/ycr3RVrDi8DIOkIGNhJlKfuyQM4tIRAI062MaV8sfjQKYVGjOBaZBOA87z+IhZE9DA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';
include __DIR__ . '/../partials/nba_header.php';
?>
    <form id="nbaForm">

            <div id="vm-form-container" style="display: flex; flex-direction: column; gap: 20px;">
                
                <!-- University Level -->
                <div style="border: 1px solid #e9ecef; padding: 15px; border-radius: 5px; background: #fafafa;">
                    <h5 style="margin-top: 0; color: #495057;">University</h5>
                    <div style="margin-bottom: 15px;">
                        <label for="univ-vision-text" style="font-size: 0.85rem; font-weight: 600; color: #555;">Vision</label>
                        <textarea aria-label="Enter University Vision" id="univ-vision-text" style="width: 100%; height: 60px; padding: 8px; border: 1px solid #ced4da; border-radius: 5px; margin-top: 5px;" placeholder="Enter University Vision..." onchange="saveCurrentLevel()"></textarea>
                    </div>
                    <div>
                        <span style="font-size: 0.85rem; font-weight: 600; color: #555; display: block;">Mission Statements</span>
                        <div id="univ-mission-list" class="dynamic-list" style="margin-top: 5px;"></div>
                        <button type="button" class="btn-add" onclick="addDynamicItem('univ-mission-list', 'UnivMission')">+ Add Univ Mission</button>
                    </div>
                </div>

                <!-- School Level -->
                <div style="border: 1px solid #e9ecef; padding: 15px; border-radius: 5px; background: #fafafa;">
                    <h5 id="school-vm-title" style="margin-top: 0; color: #495057;">School</h5>
                    <div style="margin-bottom: 15px;">
                        <label for="school-vision-text" style="font-size: 0.85rem; font-weight: 600; color: #555;">Vision</label>
                        <textarea aria-label="Enter School Vision" id="school-vision-text" style="width: 100%; height: 60px; padding: 8px; border: 1px solid #ced4da; border-radius: 5px; margin-top: 5px;" placeholder="Enter School Vision..." onchange="saveCurrentLevel()"></textarea>
                    </div>
                    <div>
                        <span style="font-size: 0.85rem; font-weight: 600; color: #555; display: block;">Mission Statements</span>
                        <div id="school-mission-list" class="dynamic-list" style="margin-top: 5px;"></div>
                        <button type="button" class="btn-add" onclick="addDynamicItem('school-mission-list', 'SchoolMission')">+ Add School Mission</button>
                    </div>
                </div>

                <!-- Department Level -->
                <div style="border: 1px solid #e9ecef; padding: 15px; border-radius: 5px; background: #fdfdfe;">
                    <h5 id="dept-vm-title" style="margin-top: 0; color: #007bff;">Department</h5>
                    <div style="margin-bottom: 15px;">
                        <label for="dept-vision-text" style="font-size: 0.85rem; font-weight: 600; color: #555;">Vision</label>
                        <textarea aria-label="Enter Department Vision" id="dept-vision-text" style="width: 100%; height: 60px; padding: 8px; border: 1px solid #ced4da; border-radius: 5px; margin-top: 5px;" placeholder="Enter Department Vision..." onchange="saveCurrentLevel()"></textarea>
                    </div>
                    <div>
                        <span style="font-size: 0.85rem; font-weight: 600; color: #555; display: block;">Mission Statements</span>
                        <div id="dept-mission-list" class="dynamic-list" style="margin-top: 5px;"></div>
                        <button type="button" class="btn-add" onclick="addDynamicItem('dept-mission-list', 'DeptMission')">+ Add Dept Mission</button>
                    </div>
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
                    <input aria-label="File 1 1 3" type="file" name="file_1_1_3" accept=".pdf">
                </div>

                <h4>1.1.4 Dissemination of Vision, Mission and PEOs</h4>
                <div class="file-upload-row" style="margin-bottom: 15px;">
                    <div>
                        <span class="desc">Evidence of website, curricula, posters, and awareness processes. Please upload PDF proof.</span>
                    </div>
                    <input aria-label="File 1 1 4" type="file" name="file_1_1_4" accept=".pdf">
                </div>

                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e9ecef;">

                <h4>1.1.5 Mapping of PEOs with Mission</h4>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <p style="font-size: 0.85rem; color: #6c757d; margin: 0;">Enter correlation levels: Low (1), Medium (2), High (3). If no correlation, use '-'.</p>
                    <button type="button" class="btn-primary" onclick="autoCalculateMatrix()" style="padding: 5px 12px; font-size: 0.85rem; background: #6f42c1; border: none; border-radius: 4px; color: white; cursor: pointer;">
                        âœ¨ Auto-Calculate Correlation
                    </button>
                </div>
                <div id="matrix-container" style="overflow-x: auto;">
                    <!-- Auto-generated Matrix Table -->
                </div>
            </div>
        </div>

        <!-- 1.2 Curriculum Structure -->
        <div class="section-card dept-specific-section" id="section-1-2" style="display: none;">
            <h3>1.2 Curriculum Structure and Features</h3>
            
            <h4>1.2.1 State the Process for Developing/Revising the Program Curriculum</h4>
            <p style="font-size: 0.85rem; color: #6c757d; margin-bottom: 15px;">
                Describe the process that periodically documents and demonstrates how the program curriculum has evolved, considering the Washington Accord Knowledge and Attitude Profile (WKs) and the Program Outcomes (POs) defined by the NBA, as listed in Annexure-II. Describe the process involving both internal and external stakeholders in framing the curriculum.
            </p>
            <div class="file-upload-row" style="margin-bottom: 25px;">
                <div>
                    <strong>Process Document</strong>
                    <span class="desc">Please upload the PDF proof of the curriculum development process.</span>
                </div>
                <input aria-label="File 1 2 1" type="file" name="file_1_2_1" accept=".pdf">
            </div>

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e9ecef;">

            <h4>1.2.2 Curriculum Structure</h4>
            <p style="font-size: 0.85rem; color: #6c757d; margin-bottom: 15px;">
                (Provide details of courses in terms of teaching and learning scheme and number of credits in the Program curriculum.)
            </p>
            
            <div style="overflow-x: auto; margin-bottom: 15px;">
                <table class="matrix-table" id="curriculum-table" style="min-width: 900px; font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th scope="col" rowspan="3" style="width: 100px;">Course Code</th>
                            <th scope="col" rowspan="3">Course Title</th>
                            <th scope="col" colspan="4" style="text-align: center; background: #f8f9fa;">Teaching & Learning Scheme</th>
                            <th scope="col" rowspan="3" style="width: 80px; text-align: center;">Total Hours / Sem</th>
                            <th scope="col" rowspan="3" style="width: 80px; text-align: center;">Total Credits<br>(Hours/30)</th>
                            <th scope="col" rowspan="3" style="width: 50px;"></th>
                        </tr>
                        <tr>
                            <th scope="col" colspan="2" style="text-align: center; background: #f8f9fa;">Classroom (CI)<br><span style="font-weight: normal; font-size: 0.75rem;">(hrs/sem)</span></th>
                            <th scope="col" style="text-align: center; background: #f8f9fa;">Lab (LI)<br><span style="font-weight: normal; font-size: 0.75rem;">(hrs/sem)</span></th>
                            <th scope="col" style="text-align: center; background: #f8f9fa;">TW & SL<br><span style="font-weight: normal; font-size: 0.75rem;">(hrs/sem)</span></th>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align: center; width: 60px; background: #f8f9fa;">L</th>
                            <th scope="col" style="text-align: center; width: 60px; background: #f8f9fa;">T</th>
                            <th scope="col" style="text-align: center; width: 60px; background: #f8f9fa;">P</th>
                            <th scope="col" style="text-align: center; width: 60px; background: #f8f9fa;">SL</th>
                        </tr>
                    </thead>
                    <tbody id="curriculum-body">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn-add" onclick="addCurriculumCourse()">+ Add Course</button>
            <p style="font-size: 0.75rem; color: #6c757d; margin-top: 15px; line-height: 1.4;">
                *This is as per the new National Credit Framework, which accounts for 30 hrs. of learning as equivalent to 1 credit. Those universities which are still following the LTP will transform them into no. of hours and fill in the above table.<br>
                Legend: <strong>CI:</strong> Classroom Instruction (L, T, Case method, etc.) <strong>LI:</strong> Laboratory Instruction (Experiments, practicals, field work) <strong>TW:</strong> Term work (assignments, seminars, micro projects) <strong>SL:</strong> Self Learning (MOOCs, spoken tutorials, online resources).
            </p>

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e9ecef;">

            <h4>1.2.3 Components of Curriculum (05)</h4>
            <p style="font-size: 0.85rem; color: #6c757d; margin-bottom: 15px;">
                (Provide details of Curriculum components for all relevant Years.)
            </p>
            
            <div style="overflow-x: auto; margin-bottom: 15px;">
                <table class="matrix-table" id="components-table" style="min-width: 800px; font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th scope="col" style="background: #f8f9fa;">Curriculum Component</th>
                            <th scope="col" style="background: #f8f9fa; width: 25%; text-align: center;">Curriculum Content<br><span style="font-weight: normal; font-size: 0.75rem;">(% of total credits)</span></th>
                            <th scope="col" style="background: #f8f9fa; width: 20%; text-align: center;">Total contact hours</th>
                            <th scope="col" style="background: #f8f9fa; width: 20%; text-align: center;">Total credits</th>
                            <th scope="col" style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="components-body">
                        <!-- Populated by JS -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="col" style="text-align: right; background: #f8f9fa;">Total number of Credits:</th>
                            <th style="text-align: center; background: #e9ecef; font-weight: bold;" id="comp-total-percent">0%</th>
                            <th style="text-align: center; background: #e9ecef; font-weight: bold;" id="comp-total-hours">0</th>
                            <th style="text-align: center; background: #e9ecef; font-weight: bold;" id="comp-total-credits">0</th>
                            <th scope="col" style="background: #e9ecef;"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <button type="button" class="btn-add" onclick="addCurriculumComponent()">+ Add Component</button>
            <p style="font-size: 0.8rem; color: #6c757d; margin-top: 10px;">
                <em>Hint: Type in the Total Credits, and the Percentage will automatically calculate based on the overall sum!</em>
            </p>

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e9ecef;">

            <h4>1.2.4 Strategies for Education Reforms</h4>
            <p style="font-size: 0.85rem; color: #6c757d; margin-bottom: 15px;">
                (A brief explanation of the plans to implement and map activities in curriculum design with multidisciplinary and interdisciplinary programs, the establishment of an academic bank of credits system, APAAR etc.)
            </p>
            <div class="file-upload-row">
                <div>
                    <strong>Education Reforms Document</strong>
                    <span class="desc">Please upload the PDF proof detailing the education reform strategies.</span>
                </div>
                <input aria-label="File 1 2 4" type="file" name="file_1_2_4" accept=".pdf">
            </div>
        </div>

        <!-- 1.3 PO, PSO and Mapping -->
        <div class="section-card dept-specific-section" id="section-1-3" style="display: none;">
            <h3>1.3 PO, PSO and their Mapping with Courses</h3>
            
            <h4>1.3.1 POs and PSOs</h4>
            <p style="font-size: 0.85rem; color: #6c757d; margin-bottom: 15px;">
                (Program Specific Outcomes (PSOs) are defined by the program, with up to 3 PSOs specified. The 12 standard Program Outcomes (POs) are defined by the NBA in Annexure II.)
            </p>
            
            <div style="margin-bottom: 20px;">
                <span style="font-size: 0.85rem; font-weight: 600; color: #555; display: block;">List of POs</span>
                <div id="po-list" class="dynamic-list">
                    <!-- Populated by JS -->
                </div>
                <button type="button" class="btn-add" onclick="addDynamicItem('po-list', 'PO')">+ Add PO</button>
            </div>

            <div style="margin-bottom: 15px;">
                <span style="font-size: 0.85rem; font-weight: 600; color: #555; display: block;">List of PSOs (Max 3)</span>
                <div id="pso-list" class="dynamic-list">
                    <!-- Populated by JS -->
                </div>
                <button type="button" class="btn-add" onclick="addDynamicItem('pso-list', 'PSO')">+ Add PSO</button>
            </div>
            
            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e9ecef;">
            
            <h4>1.3.2 Mapping between the Courses and POs/PSOs</h4>
            <p style="font-size: 0.85rem; color: #6c757d; margin-bottom: 15px;">
                (Mention the courses relevant to the POs/PSOs.) Table No.1.3.1: Connection of courses with POs/PSOs.
            </p>
            <div style="overflow-x: auto; margin-bottom: 15px;">
                <table class="matrix-table" id="po-pso-course-mapping-table" style="min-width: 600px; font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th scope="col" style="background: #f8f9fa; width: 150px;">PO / PSO Number</th>
                            <th scope="col" style="background: #f8f9fa;">List of Courses (e.g., C101, C102)</th>
                        </tr>
                    </thead>
                    <tbody id="po-pso-course-body">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- CSV Upload for COs -->
        <div id="section-1-4" class="section-card" style="display: none;">
            <h3>1.4 Course Outcomes and Course Articulation Matrix</h3>
            
            <h4>1.4.1 Course Outcome (Semester Wise)</h4>
            <p style="font-size: 0.9rem; color: #555; margin-bottom: 1.5rem;">
                (Provide Course outcomes (COs) for two core courses per semester from 1-8 semesters as a sample. The maximum number of outcomes for a course is expected to be around 6. COs should reflect on the measurable outcomes towards attaining POs and PSOs).<br><br>
                <em>Note: You can manually fill the 16 tables below, or upload a CSV/Excel file to auto-populate them.</em>
            </p>
            
            <div class="csv-dropzone" id="csvDropzone" role="button" tabindex="0" onclick="document.getElementById('csvInput').click()" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                <h4>Click or Drag & Drop your Excel/CSV File Here</h4>
                <p>Supports .xlsx, .xls, .csv files containing CO mappings</p>
                <input aria-label="CsvInput" type="file" id="csvInput" style="display: none;" accept=".xlsx, .xls, .csv" onchange="handleCSVSelect(this)">
            </div>
            <div id="csvFileName" style="margin-top: 10px; font-weight: 600; color: #28a745; margin-bottom: 20px;"></div>
            
            <div id="co-tables-container">
                <!-- 16 Tables Populated by JS -->
            </div>
            
            <hr style="margin: 40px 0; border: 0; border-top: 1px solid #e9ecef;">
            
            <h4>1.4.2 Course Articulation Matrix</h4>
            <p style="font-size: 0.9rem; color: #555; margin-bottom: 1.5rem;">
                (Provide course articulation matrices for two core courses per semester from 1-8 semesters which have been provided in the section 1.4.1. Select courses to demonstrate the mapping/correlation with POs and PSOs.)
            </p>
            
            <div id="co-matrix-tables-container">
                <!-- 16 Matrices Populated by JS -->
            </div>
            
            <hr style="margin: 40px 0; border: 0; border-top: 1px solid #e9ecef;">
            
            <h4>1.5 Program Articulation Matrix</h4>
            <p style="font-size: 0.9rem; color: #555; margin-bottom: 15px;">
                Table No.1.5.1: Program articulation matrix
            </p>
            
            <div id="program-articulation-container" style="overflow-x: auto; margin-bottom: 30px;">
                <!-- Populated by JS -->
            </div>
        </div>

        <?php 
        $crit_id = 1;
        $module = 'nba';
        $save_onclick = 'saveCriterionData()';
        include __DIR__ . '/../partials/accreditation_action_bar.php'; 
        ?>
    </form>
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
<script src="<?= BASE_URL ?>/assets/js/nba_criterion1.js"></script>
</body>
</html>

