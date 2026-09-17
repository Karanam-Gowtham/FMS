<?php
/**
 * NBA Criterion 2
 *
 * Teaching-Learning Processes
 * URL: nba/criterion2.php?year={year}
 */
require_once __DIR__ . '/../core/bootstrap.php';

require_login();
$auth = auth_context();
$active_role = auth_active_role();

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
    $stmt = $conn->prepare("SELECT * FROM nba_submissions WHERE dept_id = ? AND academic_year = ?");
    $stmt->bind_param("is", $dept_id, $year);
    $stmt->execute();
    $sub_res = $stmt->get_result();
    if ($sub_res->num_rows > 0) {
        $submission = $sub_res->fetch_assoc();
        
        // Fetch Criterion 2 data
        $stmt2 = $conn->prepare("SELECT data_json FROM nba_criteria_data WHERE submission_id = ? AND criterion_number = 2");
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

$page_title = 'Criterion 2: Teaching-Learning Processes';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> &mdash; FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/nba_module.css">
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div id="toast" class="toast">Data saved successfully!</div>

<div class="container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a> &raquo; 
        <a href="dashboard.php?year=<?= urlencode($year) ?>">NBA Accreditation</a> &raquo; 
        Criterion 2
    </div>

    <div class="header-row">
        <h1>Criterion 2: Teaching-Learning Processes</h1>
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

    <!-- Section 2.1 -->
    <div class="section-card">
        <h3>2.1 Describe Processes Followed to Ensure Quality of Teaching & Learning</h3>
        <div class="section-desc">
            (Processes may include adherence to academic calendar and instruction methods using pedagogical initiatives such as real-world examples, collaborative learning, quality of laboratory experience with regard to conducting experiments, recording observations, analysis of data etc. encouraging fast learners, assisting slow learners etc. The implementation details and impact analysis need to be documented.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_2_1').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 2.1</h4>
            <p>Upload a single PDF document detailing the teaching & learning processes.</p>
            <div class="upload-overlay" id="overlay_2_1">Uploading...</div>
            <input type="file" id="pdfInput_2_1" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '2_1')">
        </div>
        <div class="file-preview" id="preview_2_1">
            <a href="#" id="link_2_1" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('2_1')">Remove</button>
        </div>
    </div>

    <!-- Section 2.2 -->
    <div class="section-card">
        <h3>2.2 Quality of Student Capstone Project</h3>
        <div class="section-desc">
            (Quality of the capstone/major project is measured in terms of consideration to factors including, but not limited to, environment, sustainability, safety, ethics, cost, type (application, product, research, review etc.) and standards. Processes related to project identification, allotment, continuous monitoring, evaluation including demonstration of working prototypes and enhancing the relevance of projects. Mention implementation details including details of POs and PSOs addressed through the projects with justification.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_2_2').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 2.2</h4>
            <p>Upload a single PDF document detailing capstone projects.</p>
            <div class="upload-overlay" id="overlay_2_2">Uploading...</div>
            <input type="file" id="pdfInput_2_2" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '2_2')">
        </div>
        <div class="file-preview" id="preview_2_2">
            <a href="#" id="link_2_2" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('2_2')">Remove</button>
        </div>
    </div>

    <!-- Section 2.3 -->
    <div class="section-card">
        <h3>2.3 Internship / Industrial Training</h3>
        <div class="section-desc">
            (Describe process, duration, POs/PSOs addressed.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_2_3').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 2.3</h4>
            <p>Upload a single PDF document detailing internship processes.</p>
            <div class="upload-overlay" id="overlay_2_3">Uploading...</div>
            <input type="file" id="pdfInput_2_3" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '2_3')">
        </div>
        <div class="file-preview" id="preview_2_3">
            <a href="#" id="link_2_3" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('2_3')">Remove</button>
        </div>
    </div>

    <!-- Section 2.4 -->
    <div class="section-card">
        <h3>2.4 Seminar and Mini/Micro Projects</h3>
        <div class="section-desc">
            (Describe process, POs/PSOs addressed.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_2_4').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 2.4</h4>
            <p>Upload a single PDF document detailing seminar and project processes.</p>
            <div class="upload-overlay" id="overlay_2_4">Uploading...</div>
            <input type="file" id="pdfInput_2_4" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '2_4')">
        </div>
        <div class="file-preview" id="preview_2_4">
            <a href="#" id="link_2_4" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('2_4')">Remove</button>
        </div>
    </div>

    <!-- Section 2.5 -->
    <div class="section-card">
        <h3>2.5 Case Studies and Real-Life Examples</h3>
        <div class="section-desc">
            (Type and complexity, POs/PSOs addressed.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_2_5').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 2.5</h4>
            <p>Upload a single PDF document detailing case studies.</p>
            <div class="upload-overlay" id="overlay_2_5">Uploading...</div>
            <input type="file" id="pdfInput_2_5" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '2_5')">
        </div>
        <div class="file-preview" id="preview_2_5">
            <a href="#" id="link_2_5" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('2_5')">Remove</button>
        </div>
    </div>

    <!-- Section 2.6 -->
    <div class="section-card">
        <h3>2.6 SWAYAM/NPTEL/MOOC/Self Learning</h3>
        <div class="section-desc">
            (Number of students registered, certification and POs/PSOs addressed.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_2_6').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 2.6</h4>
            <p>Upload a single PDF document detailing MOOC/Self Learning.</p>
            <div class="upload-overlay" id="overlay_2_6">Uploading...</div>
            <input type="file" id="pdfInput_2_6" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '2_6')">
        </div>
        <div class="file-preview" id="preview_2_6">
            <a href="#" id="link_2_6" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('2_6')">Remove</button>
        </div>
    </div>

    <!-- Section 2.7 -->
    <div class="section-card">
        <h3>2.7 Solving Complex Engineering Problems Incorporating Sustainability Goals</h3>
        <div class="section-desc">
            (Provide details of core courses (Project based learning, problem-based learning), mini projects, integrated design projects, capstone projects, hackathon or any other activity-based learning towards solving complex engineering problems targeting relevant SDGs.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_2_7').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 2.7</h4>
            <p>Upload a single PDF document detailing complex engineering problem solving.</p>
            <div class="upload-overlay" id="overlay_2_7">Uploading...</div>
            <input type="file" id="pdfInput_2_7" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '2_7')">
        </div>
        <div class="file-preview" id="preview_2_7">
            <a href="#" id="link_2_7" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('2_7')">Remove</button>
        </div>
    </div>

    <!-- Section 2.8 -->
    <div class="section-card">
        <h3>2.8 Steps Taken for Enhancing Industry Institute Partnerships</h3>
        <div class="section-desc">
            (Provide details of partial delivery of courses, industry supported labs, industry offered short-term programs/training etc.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_2_8').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 2.8</h4>
            <p>Upload a single PDF document detailing industry partnerships.</p>
            <div class="upload-overlay" id="overlay_2_8">Uploading...</div>
            <input type="file" id="pdfInput_2_8" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '2_8')">
        </div>
        <div class="file-preview" id="preview_2_8">
            <a href="#" id="link_2_8" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('2_8')">Remove</button>
        </div>
    </div>

    <div style="text-align: right; margin-bottom: 4rem;">
        <button type="button" class="btn-save" onclick="saveData()">Save Criterion 2</button>
    </div>

</div>

<script>
    const year = <?= json_encode($year) ?>;
    const initialData = <?= json_encode($criteria_data) ?>;
    let levelData = {};
    let currentLevel = '';

    function initData() {
        levelData = initialData.levelData || {};
        
        const defaultDept = 'dept_<?= $dept_id ?>';
        if (!levelData[defaultDept]) {
            levelData[defaultDept] = { pdf_2_1: '', pdf_2_2: '', pdf_2_3: '', pdf_2_4: '', pdf_2_5: '', pdf_2_6: '', pdf_2_7: '', pdf_2_8: '' };
        }

        switchLevel();
    }

    function switchLevel() {
        const select = document.getElementById('level-select');
        currentLevel = select.value;
        
        if (!levelData[currentLevel]) {
            levelData[currentLevel] = { pdf_2_1: '', pdf_2_2: '', pdf_2_3: '', pdf_2_4: '', pdf_2_5: '', pdf_2_6: '', pdf_2_7: '', pdf_2_8: '' };
        }

        ['2_1', '2_2', '2_3', '2_4', '2_5', '2_6', '2_7', '2_8'].forEach(section => {
            updatePDFUI(section);
        });
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
            const response = await fetch('api_upload_pdf.php', {
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

        fetch('api_save_criterion2.php', {
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
