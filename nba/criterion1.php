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

// Fetch departments for the selector (Exclude central/admin wings)
$excluded_depts = "'Antiragging', 'Clubs', 'Exam_Section', 'IIC', 'IQAC', 'NAAC', 'NBA', 'NCC', 'NSS', 'PASH', 'PE', 'PG', 'R&D', 'SAC', 'Sports', 'Women_Empowerment'";
$dept_result = $conn->query("SELECT dept_id, dept_name FROM departments WHERE dept_name NOT IN ($excluded_depts) ORDER BY dept_name");
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
                    <?php foreach ($departments as $d): ?>
                        <option value="dept_<?= $d['dept_id'] ?>">Department: <?= htmlspecialchars($d['dept_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="vm-form-container" style="display: flex; flex-direction: column; gap: 20px;">
                
                <!-- University Level -->
                <div style="border: 1px solid #e9ecef; padding: 15px; border-radius: 5px; background: #fafafa;">
                    <h5 style="margin-top: 0; color: #495057;">University</h5>
                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 0.85rem; font-weight: 600; color: #555;">Vision</label>
                        <textarea id="univ-vision-text" style="width: 100%; height: 60px; padding: 8px; border: 1px solid #ced4da; border-radius: 5px; margin-top: 5px;" placeholder="Enter University Vision..." onchange="saveCurrentLevel()"></textarea>
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: #555;">Mission Statements</label>
                        <div id="univ-mission-list" class="dynamic-list" style="margin-top: 5px;"></div>
                        <button type="button" class="btn-add" onclick="addDynamicItem('univ-mission-list', 'UnivMission')">+ Add Univ Mission</button>
                    </div>
                </div>

                <!-- School Level -->
                <div style="border: 1px solid #e9ecef; padding: 15px; border-radius: 5px; background: #fafafa;">
                    <h5 id="school-vm-title" style="margin-top: 0; color: #495057;">School</h5>
                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 0.85rem; font-weight: 600; color: #555;">Vision</label>
                        <textarea id="school-vision-text" style="width: 100%; height: 60px; padding: 8px; border: 1px solid #ced4da; border-radius: 5px; margin-top: 5px;" placeholder="Enter School Vision..." onchange="saveCurrentLevel()"></textarea>
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: #555;">Mission Statements</label>
                        <div id="school-mission-list" class="dynamic-list" style="margin-top: 5px;"></div>
                        <button type="button" class="btn-add" onclick="addDynamicItem('school-mission-list', 'SchoolMission')">+ Add School Mission</button>
                    </div>
                </div>

                <!-- Department Level -->
                <div style="border: 1px solid #e9ecef; padding: 15px; border-radius: 5px; background: #fdfdfe;">
                    <h5 id="dept-vm-title" style="margin-top: 0; color: #007bff;">Department</h5>
                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 0.85rem; font-weight: 600; color: #555;">Vision</label>
                        <textarea id="dept-vision-text" style="width: 100%; height: 60px; padding: 8px; border: 1px solid #ced4da; border-radius: 5px; margin-top: 5px;" placeholder="Enter Department Vision..." onchange="saveCurrentLevel()"></textarea>
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 600; color: #555;">Mission Statements</label>
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
                <input type="file" name="file_1_2_1" accept=".pdf">
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
                            <th rowspan="3" style="width: 100px;">Course Code</th>
                            <th rowspan="3">Course Title</th>
                            <th colspan="4" style="text-align: center; background: #f8f9fa;">Teaching & Learning Scheme</th>
                            <th rowspan="3" style="width: 80px; text-align: center;">Total Hours / Sem</th>
                            <th rowspan="3" style="width: 80px; text-align: center;">Total Credits<br>(Hours/30)</th>
                            <th rowspan="3" style="width: 50px;"></th>
                        </tr>
                        <tr>
                            <th colspan="2" style="text-align: center; background: #f8f9fa;">Classroom (CI)<br><span style="font-weight: normal; font-size: 0.75rem;">(hrs/sem)</span></th>
                            <th style="text-align: center; background: #f8f9fa;">Lab (LI)<br><span style="font-weight: normal; font-size: 0.75rem;">(hrs/sem)</span></th>
                            <th style="text-align: center; background: #f8f9fa;">TW & SL<br><span style="font-weight: normal; font-size: 0.75rem;">(hrs/sem)</span></th>
                        </tr>
                        <tr>
                            <th style="text-align: center; width: 60px; background: #f8f9fa;">L</th>
                            <th style="text-align: center; width: 60px; background: #f8f9fa;">T</th>
                            <th style="text-align: center; width: 60px; background: #f8f9fa;">P</th>
                            <th style="text-align: center; width: 60px; background: #f8f9fa;">SL</th>
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
                            <th style="background: #f8f9fa;">Curriculum Component</th>
                            <th style="background: #f8f9fa; width: 25%; text-align: center;">Curriculum Content<br><span style="font-weight: normal; font-size: 0.75rem;">(% of total credits)</span></th>
                            <th style="background: #f8f9fa; width: 20%; text-align: center;">Total contact hours</th>
                            <th style="background: #f8f9fa; width: 20%; text-align: center;">Total credits</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="components-body">
                        <!-- Populated by JS -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="text-align: right; background: #f8f9fa;">Total number of Credits:</th>
                            <th style="text-align: center; background: #e9ecef; font-weight: bold;" id="comp-total-percent">0%</th>
                            <th style="text-align: center; background: #e9ecef; font-weight: bold;" id="comp-total-hours">0</th>
                            <th style="text-align: center; background: #e9ecef; font-weight: bold;" id="comp-total-credits">0</th>
                            <th style="background: #e9ecef;"></th>
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
                <input type="file" name="file_1_2_4" accept=".pdf">
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
                <label style="font-size: 0.85rem; font-weight: 600; color: #555;">List of POs</label>
                <div id="po-list" class="dynamic-list">
                    <!-- Populated by JS -->
                </div>
                <button type="button" class="btn-add" onclick="addDynamicItem('po-list', 'PO')">+ Add PO</button>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-size: 0.85rem; font-weight: 600; color: #555;">List of PSOs (Max 3)</label>
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
                            <th style="background: #f8f9fa; width: 150px;">PO / PSO Number</th>
                            <th style="background: #f8f9fa;">List of Courses (e.g., C101, C102)</th>
                        </tr>
                    </thead>
                    <tbody id="po-pso-course-body">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
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
    let currentLevel = 'dept_<?= $dept_id ?>';
    
    // Core matrix data (tied to the selected Department)
    let missions = []; // Active department's missions (Dept level)
    let univMissions = [];
    let schoolMissions = [];
    let peos = [];     // Active department's PEOs
    let pos = [];      // Active department's POs
    let psos = [];     // Active department's PSOs
    let matrixData = {}; 
    let poPsoCourseMapping = {}; // Format: { 'PO1': 'C101, C102', 'PSO1': 'C203' }

    let curriculumCourses = []; // [{code: '', title: '', l: 0, t: 0, p: 0, sl: 0}]
    let curriculumComponents = []; // [{name: '', percent: '', hours: '', credits: ''}]
    const defaultComponents = [
        "Basic Sciences", "Basic Engineering", "Humanities and Social Sciences",
        "Program Core", "Program Electives", "Open Electives", "Project(s)", "Internships/Seminars"
    ];

    const schoolMapping = {
        'AIDS': 'School of Advanced Computing',
        'AIML': 'School of Advanced Computing',
        'CSE': 'School of Advanced Computing',
        'CSE-CS': 'School of Advanced Computing',
        'IT': 'School of Advanced Computing',
        'CIVIL': 'School of Applied Engineering',
        'ECE': 'School of Applied Engineering',
        'EEE': 'School of Applied Engineering',
        'MECH': 'School of Applied Engineering',
        'BSH': 'School of Physical Sciences',
        'Chemistry': 'School of Physical Sciences',
        'MatheMatics': 'School of Physical Sciences',
        'Physics': 'School of Physical Sciences'
    };

    function getSchoolKey(deptName) {
        const schoolName = schoolMapping[deptName] || 'School';
        return 'school_' + schoolName.replace(/[^a-zA-Z0-9]/g, '_').toLowerCase();
    }

    const initialData = <?= json_encode($criteria_data) ?>;

    function initData() {
        levelData = initialData.levelData || {};
        
        // Ensure defaults exist for Univ
        if (!levelData['univ']) levelData['univ'] = { vision: '', missions: [{id: 'M1', text: ''}] };

        // Initialize active department's data specifically for the matrix
        const defaultDept = 'dept_<?= $dept_id ?>';
        if (!levelData[defaultDept]) {
            levelData[defaultDept] = { vision: '', missions: [{id: 'M1', text: ''}], curriculum: [], components: [], pos: [{id: 'PO1', text: ''}], psos: [{id: 'PSO1', text: ''}], poPsoCourseMapping: {} };
        }

        peos = initialData.peos || [{id: 'PEO1', text: ''}];
        pos = initialData.pos || [{id: 'PO1', text: ''}];
        psos = initialData.psos || [{id: 'PSO1', text: ''}];
        matrixData = initialData.matrix || {};

        switchLevel(); // Load UI for current dropdown selection
        
        renderList('peo-list', peos, 'PEO', 'PEO', 'PEO');
        // Matrix is rendered inside switchLevel or renderList
    }

    // —— Curriculum Table Logic ——
    function renderCurriculumTable() {
        const tbody = document.getElementById('curriculum-body');
        tbody.innerHTML = '';
        
        curriculumCourses.forEach((c, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" style="width:100%; border:1px solid #ccc; padding:4px;" value="${c.code}" onchange="updateCurriculum(${index}, 'code', this.value)"></td>
                <td><input type="text" style="width:100%; border:1px solid #ccc; padding:4px;" value="${c.title}" onchange="updateCurriculum(${index}, 'title', this.value)"></td>
                <td><input type="number" style="width:100%; border:1px solid #ccc; padding:4px; text-align:center;" value="${c.l || ''}" onchange="updateCurriculum(${index}, 'l', this.value)"></td>
                <td><input type="number" style="width:100%; border:1px solid #ccc; padding:4px; text-align:center;" value="${c.t || ''}" onchange="updateCurriculum(${index}, 't', this.value)"></td>
                <td><input type="number" style="width:100%; border:1px solid #ccc; padding:4px; text-align:center;" value="${c.p || ''}" onchange="updateCurriculum(${index}, 'p', this.value)"></td>
                <td><input type="number" style="width:100%; border:1px solid #ccc; padding:4px; text-align:center;" value="${c.sl || ''}" onchange="updateCurriculum(${index}, 'sl', this.value)"></td>
                <td style="text-align:center; font-weight:bold; background:#f8f9fa;">${c.totalHours || 0}</td>
                <td style="text-align:center; font-weight:bold; background:#f8f9fa;">${c.credits || 0}</td>
                <td style="text-align:center;"><button type="button" class="btn-remove" onclick="removeCurriculumCourse(${index})" style="padding:2px 6px;">&times;</button></td>
            `;
            tbody.appendChild(tr);
        });
    }

    function addCurriculumCourse() {
        curriculumCourses.push({ code: '', title: '', l: '', t: '', p: '', sl: '', totalHours: 0, credits: 0 });
        renderCurriculumTable();
    }

    function removeCurriculumCourse(index) {
        curriculumCourses.splice(index, 1);
        renderCurriculumTable();
    }

    function updateCurriculum(index, field, value) {
        curriculumCourses[index][field] = value;
        
        // Auto-calculate if L, T, P, SL changed
        if (['l', 't', 'p', 'sl'].includes(field)) {
            const l = parseInt(curriculumCourses[index].l) || 0;
            const t = parseInt(curriculumCourses[index].t) || 0;
            const p = parseInt(curriculumCourses[index].p) || 0;
            const sl = parseInt(curriculumCourses[index].sl) || 0;
            
            const totalHours = l + t + p + sl;
            curriculumCourses[index].totalHours = totalHours;
            // 30 hours = 1 credit. We can use a decimal or integer
            curriculumCourses[index].credits = (totalHours / 30).toFixed(1);
        }
        
        renderCurriculumTable();
    }

    // —— Curriculum Components Logic ——
    function initDefaultComponents() {
        if (curriculumComponents.length === 0) {
            defaultComponents.forEach(name => {
                curriculumComponents.push({ name: name, percent: '', hours: '', credits: '' });
            });
        }
    }

    function renderCurriculumComponents() {
        const tbody = document.getElementById('components-body');
        tbody.innerHTML = '';
        
        let totalHours = 0;
        let totalCredits = 0;

        // First pass: sum totals for percentage calculation
        curriculumComponents.forEach(c => {
            totalHours += parseFloat(c.hours) || 0;
            totalCredits += parseFloat(c.credits) || 0;
        });

        curriculumComponents.forEach((c, index) => {
            // Auto calculate percentage if total credits exist
            let pct = '';
            if (totalCredits > 0) {
                const creds = parseFloat(c.credits) || 0;
                pct = ((creds / totalCredits) * 100).toFixed(1) + '%';
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" style="width:100%; border:1px solid #ccc; padding:4px;" value="${c.name}" onchange="updateComponent(${index}, 'name', this.value)"></td>
                <td style="text-align:center; background:#f8f9fa;">${pct}</td>
                <td><input type="number" style="width:100%; border:1px solid #ccc; padding:4px; text-align:center;" value="${c.hours || ''}" onchange="updateComponent(${index}, 'hours', this.value)"></td>
                <td><input type="number" step="0.1" style="width:100%; border:1px solid #ccc; padding:4px; text-align:center;" value="${c.credits || ''}" onchange="updateComponent(${index}, 'credits', this.value)"></td>
                <td style="text-align:center;"><button type="button" class="btn-remove" onclick="removeComponent(${index})" style="padding:2px 6px;">&times;</button></td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('comp-total-hours').innerText = totalHours;
        document.getElementById('comp-total-credits').innerText = totalCredits;
        document.getElementById('comp-total-percent').innerText = totalCredits > 0 ? '100%' : '0%';
    }

    function addCurriculumComponent() {
        curriculumComponents.push({ name: '', percent: '', hours: '', credits: '' });
        renderCurriculumComponents();
    }

    function removeComponent(index) {
        curriculumComponents.splice(index, 1);
        renderCurriculumComponents();
    }

    function updateComponent(index, field, value) {
        curriculumComponents[index][field] = value;
        renderCurriculumComponents();
    }

    function renderPoPsoCourseMapping() {
        const tbody = document.getElementById('po-pso-course-body');
        if (!tbody) return;
        tbody.innerHTML = '';
        
        // Render 12 standard POs
        for (let i = 1; i <= 12; i++) {
            const id = 'PO' + i;
            const val = poPsoCourseMapping[id] || '';
            tbody.innerHTML += `
                <tr>
                    <th style="background: #f8f9fa;">${id}</th>
                    <td><input type="text" style="width: 100%; border: 1px solid #ccc; padding: 4px;" value="${val}" onchange="updatePoPsoCourseMapping('${id}', this.value)" placeholder="e.g. C101, C102..."></td>
                </tr>
            `;
        }
        
        // Render dynamic PSOs
        psos.forEach(pso => {
            if (!pso.text && pso.id !== 'PSO1') return; // skip completely empty ones except maybe PSO1
            const id = pso.id;
            const val = poPsoCourseMapping[id] || '';
            tbody.innerHTML += `
                <tr>
                    <th style="background: #e9ecef;">${id}</th>
                    <td><input type="text" style="width: 100%; border: 1px solid #ccc; padding: 4px;" value="${val}" onchange="updatePoPsoCourseMapping('${id}', this.value)" placeholder="e.g. C101, C102..."></td>
                </tr>
            `;
        });
    }

    function updatePoPsoCourseMapping(id, value) {
        poPsoCourseMapping[id] = value;
    }

    function switchLevel() {
        const select = document.getElementById('level-select');
        currentLevel = select.value;
        const text = select.options[select.selectedIndex].text;
        const deptName = text.replace('Department: ', '').trim();
        
        document.getElementById('dept-vm-title').innerText = text;
        
        // Ensure data object exists for this dept level
        if (!levelData[currentLevel]) {
            levelData[currentLevel] = { vision: '', missions: [{id: 'M1', text: ''}], curriculum: [], components: [], pos: [{id: 'PO1', text: ''}], psos: [{id: 'PSO1', text: ''}], poPsoCourseMapping: {} };
        }

        // Populate Univ Level
        document.getElementById('univ-vision-text').value = levelData['univ'].vision || '';
        univMissions = levelData['univ'].missions || [{id: 'M1', text: ''}];
        renderList('univ-mission-list', univMissions, 'Univ Mission', 'M', 'UnivMission');

        // Populate School Level
        const schoolName = schoolMapping[deptName] || 'School';
        const schoolKey = getSchoolKey(deptName);
        document.getElementById('school-vm-title').innerText = schoolName;
        
        if (!levelData[schoolKey]) {
            levelData[schoolKey] = { vision: '', missions: [{id: 'M1', text: ''}] };
        }
        
        document.getElementById('school-vision-text').value = levelData[schoolKey].vision || '';
        schoolMissions = levelData[schoolKey].missions || [{id: 'M1', text: ''}];
        renderList('school-mission-list', schoolMissions, 'School Mission', 'M', 'SchoolMission');

        // Populate Dept Level
        document.getElementById('dept-vision-text').value = levelData[currentLevel].vision || '';
        missions = levelData[currentLevel].missions || [{id: 'M1', text: ''}];
        renderList('dept-mission-list', missions, 'Dept Mission', 'M', 'DeptMission');

        // Toggle PEO, Matrix, and Curriculum containers only for departments
        if (currentLevel.startsWith('dept_')) {
            document.getElementById('peo-matrix-container').style.display = 'block';
            document.getElementById('section-1-2').style.display = 'block';
            document.getElementById('section-1-3').style.display = 'block';
            
            // Populate Curriculum Courses
            curriculumCourses = levelData[currentLevel].curriculum || [];
            renderCurriculumTable();
            
            // Populate Curriculum Components
            curriculumComponents = levelData[currentLevel].components || [];
            if (curriculumComponents.length === 0) initDefaultComponents();
            renderCurriculumComponents();

            // Populate POs
            pos = levelData[currentLevel].pos || [{id: 'PO1', text: ''}];
            renderList('po-list', pos, 'PO', 'PO', 'PO');
            
            // Populate PSOs
            psos = levelData[currentLevel].psos || [{id: 'PSO1', text: ''}];
            renderList('pso-list', psos, 'PSO', 'PSO');
            
            // Populate PO/PSO Mapping
            poPsoCourseMapping = levelData[currentLevel].poPsoCourseMapping || {};
            renderPoPsoCourseMapping();
            
            renderMatrix();
        } else {
            document.getElementById('peo-matrix-container').style.display = 'none';
            document.getElementById('section-1-2').style.display = 'none';
            document.getElementById('section-1-3').style.display = 'none';
        }
    }

    function saveCurrentLevel() {
        if (!levelData[currentLevel]) {
            levelData[currentLevel] = { vision: '', missions: [], curriculum: [], components: [], pos: [], psos: [], poPsoCourseMapping: {} };
        }
        
        const select = document.getElementById('level-select');
        const text = select.options[select.selectedIndex].text;
        const deptName = text.replace('Department: ', '').trim();
        const schoolKey = getSchoolKey(deptName);
        
        levelData['univ'].vision = document.getElementById('univ-vision-text').value;
        if (!levelData[schoolKey]) {
            levelData[schoolKey] = { vision: '', missions: [] };
        }
        levelData[schoolKey].vision = document.getElementById('school-vision-text').value;
        levelData[currentLevel].vision = document.getElementById('dept-vision-text').value;
        
        if (currentLevel.startsWith('dept_')) {
            levelData[currentLevel].curriculum = curriculumCourses;
            levelData[currentLevel].components = curriculumComponents;
            levelData[currentLevel].pos = pos;
            levelData[currentLevel].psos = psos;
            levelData[currentLevel].poPsoCourseMapping = poPsoCourseMapping;
        }
    }

    function renderList(containerId, dataArray, placeholderLabel, prefix, type) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';
        dataArray.forEach((item, index) => {
            item.id = `${prefix}${index + 1}`;
            const div = document.createElement('div');
            div.className = 'dynamic-list-item';
            div.innerHTML = `
                <div style="width: 60px; display: flex; align-items: center; font-weight: 600; color: #4a90d9;">${item.id}</div>
                <input type="text" placeholder="Enter ${placeholderLabel} details..." value="${item.text.replace(/"/g, '&quot;')}" onchange="updateText(this, '${type}', ${index})">
                <button type="button" class="btn-remove" onclick="removeItem('${type}', ${index})">&times;</button>
            `;
            container.appendChild(div);
        });
        if (type === 'DeptMission') renderMatrix();
        if (type === 'PSO') renderPoPsoCourseMapping();
    }

    function addDynamicItem(containerId, type) {
        if (type === 'UnivMission') {
            univMissions.push({ id: '', text: '' });
            renderList('univ-mission-list', univMissions, 'Univ Mission', 'M', 'UnivMission');
        } else if (type === 'SchoolMission') {
            schoolMissions.push({ id: '', text: '' });
            renderList('school-mission-list', schoolMissions, 'School Mission', 'M', 'SchoolMission');
        } else if (type === 'DeptMission') {
            missions.push({ id: '', text: '' });
            renderList('dept-mission-list', missions, 'Dept Mission', 'M', 'DeptMission');
        } else if (type === 'PEO') {
            peos.push({ id: '', text: '' });
            renderList('peo-list', peos, 'PEO', 'PEO', 'PEO');
        } else if (type === 'PO') {
            if (pos.length >= 12) {
                alert("The NBA specifies exactly 12 standard Program Outcomes (POs).");
                return;
            }
            pos.push({ id: '', text: '' });
            renderList('po-list', pos, 'PO', 'PO', 'PO');
        } else if (type === 'PSO') {
            if (psos.length >= 3) {
                alert("You can only specify up to 3 PSOs.");
                return;
            }
            psos.push({ id: '', text: '' });
            renderList('pso-list', psos, 'PSO', 'PSO', 'PSO');
        }
    }

    function removeItem(type, index) {
        if (type === 'UnivMission') {
            univMissions.splice(index, 1);
            renderList('univ-mission-list', univMissions, 'Univ Mission', 'M', 'UnivMission');
        } else if (type === 'SchoolMission') {
            schoolMissions.splice(index, 1);
            renderList('school-mission-list', schoolMissions, 'School Mission', 'M', 'SchoolMission');
        } else if (type === 'DeptMission') {
            missions.splice(index, 1);
            renderList('dept-mission-list', missions, 'Dept Mission', 'M', 'DeptMission');
        } else if (type === 'PEO') {
            peos.splice(index, 1);
            renderList('peo-list', peos, 'PEO', 'PEO', 'PEO');
        } else if (type === 'PO') {
            pos.splice(index, 1);
            renderList('po-list', pos, 'PO', 'PO', 'PO');
        } else if (type === 'PSO') {
            psos.splice(index, 1);
            renderList('pso-list', psos, 'PSO', 'PSO', 'PSO');
        }
    }

    function updateText(input, type, index) {
        if (type === 'UnivMission') univMissions[index].text = input.value;
        else if (type === 'SchoolMission') schoolMissions[index].text = input.value;
        else if (type === 'DeptMission') missions[index].text = input.value;
        else if (type === 'PEO') peos[index].text = input.value;
        else if (type === 'PO') pos[index].text = input.value;
        else if (type === 'PSO') psos[index].text = input.value;
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
