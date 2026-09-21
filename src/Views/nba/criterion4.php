<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> &mdash; FMS</title>
    <link href="<?= BASE_URL ?>/assets/css/layout.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/nba_module.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div id="toast" class="toast">Data saved successfully!</div>

<div class="container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a> &raquo; 
        <a href="dashboard.php?year=<?= urlencode($year) ?>">NBA Accreditation</a> &raquo; 
        Criterion 4
    </div>

    <div class="header-row">
        <h1>Criterion 4: Students' Performance</h1>
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

    <!-- Section 4.1 -->
    <div class="section-card">
        <h3>4.1 Enrolment Ratio</h3>
        <div class="section-desc">
            (Upload documentation regarding student enrolment data, intake, and admitted students for the stipulated period.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_4_1').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 4.1</h4>
            <p>Upload a single PDF document detailing Enrolment Ratio.</p>
            <div class="upload-overlay" id="overlay_4_1">Uploading...</div>
            <input type="file" id="pdfInput_4_1" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '4_1')">
        </div>
        <div class="file-preview" id="preview_4_1">
            <a href="#" id="link_4_1" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('4_1')">Remove</button>
        </div>
    </div>

    <!-- Section 4.2 -->
    <div class="section-card">
        <h3>4.2 Success Rate in the stipulated period of the program</h3>
        <div class="section-desc">
            (Upload documentation regarding graduation rates and success index of students over the required years.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_4_2').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 4.2</h4>
            <p>Upload a single PDF document detailing Success Rate.</p>
            <div class="upload-overlay" id="overlay_4_2">Uploading...</div>
            <input type="file" id="pdfInput_4_2" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '4_2')">
        </div>
        <div class="file-preview" id="preview_4_2">
            <a href="#" id="link_4_2" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('4_2')">Remove</button>
        </div>
    </div>

    <!-- Section 4.3 -->
    <div class="section-card">
        <h3>4.3 Academic Performance in Third Year</h3>
        <div class="section-desc">
            (Upload Academic Performance Index (API) data for the third year of study.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_4_3').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 4.3</h4>
            <p>Upload a single PDF document detailing Third Year Academic Performance.</p>
            <div class="upload-overlay" id="overlay_4_3">Uploading...</div>
            <input type="file" id="pdfInput_4_3" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '4_3')">
        </div>
        <div class="file-preview" id="preview_4_3">
            <a href="#" id="link_4_3" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('4_3')">Remove</button>
        </div>
    </div>

    <!-- Section 4.4 -->
    <div class="section-card">
        <h3>4.4 Academic Performance in Second Year</h3>
        <div class="section-desc">
            (Upload Academic Performance Index (API) data for the second year of study.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_4_4').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 4.4</h4>
            <p>Upload a single PDF document detailing Second Year Academic Performance.</p>
            <div class="upload-overlay" id="overlay_4_4">Uploading...</div>
            <input type="file" id="pdfInput_4_4" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '4_4')">
        </div>
        <div class="file-preview" id="preview_4_4">
            <a href="#" id="link_4_4" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('4_4')">Remove</button>
        </div>
    </div>

    <!-- Section 4.5 -->
    <div class="section-card">
        <h3>4.5 Placement, Higher Studies and Entrepreneurship</h3>
        <div class="section-desc">
            (Upload placement records, offer letters, higher education admission proofs, and details of students turned entrepreneurs.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_4_5').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 4.5</h4>
            <p>Upload a single PDF document detailing Placements, Higher Studies, and Entrepreneurship.</p>
            <div class="upload-overlay" id="overlay_4_5">Uploading...</div>
            <input type="file" id="pdfInput_4_5" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '4_5')">
        </div>
        <div class="file-preview" id="preview_4_5">
            <a href="#" id="link_4_5" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('4_5')">Remove</button>
        </div>
    </div>

    <!-- Section 4.6 -->
    <div class="section-card">
        <h3>4.6 Professional Activities</h3>
        <div class="section-desc">
            (Upload proofs of professional society memberships, student publications in conferences/journals, and organized events.)
        </div>
        
        <div class="pdf-dropzone" onclick="document.getElementById('pdfInput_4_6').click()">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h4>Click to Upload PDF for Section 4.6</h4>
            <p>Upload a single PDF document detailing Professional Activities.</p>
            <div class="upload-overlay" id="overlay_4_6">Uploading...</div>
            <input type="file" id="pdfInput_4_6" style="display: none;" accept="application/pdf" onchange="handlePDFUpload(this, '4_6')">
        </div>
        <div class="file-preview" id="preview_4_6">
            <a href="#" id="link_4_6" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                View Uploaded PDF Document
            </a>
            <button type="button" class="btn-remove-pdf" onclick="removePDF('4_6')">Remove</button>
        </div>
    </div>

    <div style="text-align: right; margin-bottom: 4rem;">
        <button type="button" class="btn-save" onclick="saveData()">Save Criterion 4</button>
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
            levelData[defaultDept] = { pdf_4_1: '', pdf_4_2: '', pdf_4_3: '', pdf_4_4: '', pdf_4_5: '', pdf_4_6: '' };
        }

        switchLevel();
    }

    function switchLevel() {
        const select = document.getElementById('level-select');
        currentLevel = select.value;
        
        if (!levelData[currentLevel]) {
            levelData[currentLevel] = { pdf_4_1: '', pdf_4_2: '', pdf_4_3: '', pdf_4_4: '', pdf_4_5: '', pdf_4_6: '' };
        }

        ['4_1', '4_2', '4_3', '4_4', '4_5', '4_6'].forEach(section => {
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
            const response = await fetch('<?= BASE_URL ?>/public/index.php?route=api/nba/upload_pdf&id=4', {
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

        fetch('<?= BASE_URL ?>/public/index.php?route=api/nba/save&id=4', {
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
