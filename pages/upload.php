<?php
/**
 * Unified EAV Upload Handler
 * Handles dynamic file uploads and metadata based on document_types.
 */
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/meta_registry.php';
require_once __DIR__ . '/../core/document_service.php';
require_login();

$type_key = $_GET['type'] ?? '';
if (empty($type_key)) {
    die("Error: No document type specified.");
}

$doc_type = doc_get_type_by_key($conn, $type_key);
if (!$doc_type) {
    die("Error: Document type '$type_key' is not registered or active.");
}

$schema_fields = meta_get_fields($type_key);
$schema_files = meta_get_file_slots($type_key);

// Fetch academic years for the dropdown
$years = [];
$res = $conn->query("SELECT year_id, year_label FROM academic_years ORDER BY year_label DESC");
while ($row = $res->fetch_assoc()) {
    $years[] = $row;
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();

    $academic_year_id = (int)($_POST['academic_year_id'] ?? 0);
    $auth = auth_context();
    $user_id = $auth['user_id'];
    $dept_id = auth_active_dept_id();
    
    // Core Document Title
    $doc_title = trim($_POST['doc_title'] ?? '');
    
    if (empty($doc_title)) {
        $error = "Document title is required.";
    } else {
        // Collect Meta Data
        $meta_data = [];
        foreach ($schema_fields as $field) {
            $key = $field['name'];
            $val = $_POST[$key] ?? '';
            // Handle arrays (e.g. from multiple selects if any)
            if (is_array($val)) {
                $val = json_encode($val);
            }
            $meta_data[$key] = trim((string)$val);
        }
        
        // Let doc_create handle the upload, EAV, and legacy sync
        $result = doc_create(
            $conn,
            $type_key,
            $user_id,
            $dept_id,
            $academic_year_id ?: null,
            $doc_title,
            $meta_data,
            $_FILES // Pass the entire $_FILES array, doc_create will pick what it needs based on meta_get_file_slots
        );
        
        if ($result['success']) {
            $success = true;
        } else {
            $error = $result['error'] ?? "An unknown error occurred during upload.";
        }
    }
}

// Set custom breadcrumb before including header
$custom_breadcrumb_title = 'Upload ' . $doc_type['type_label'];

// Include FMS header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Document: <?= htmlspecialchars($doc_type['type_label']) ?> — FMS</title>
    <!-- Use Google Fonts for premium typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --danger: #ef4444;
            --success: #10b981;
            --success-bg: #d1fae5;
            --success-text: #065f46;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .upload-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .upload-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .upload-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .btn-back {
            padding: 8px 16px;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .btn-back:hover {
            background: #f1f5f9;
            transform: translateY(-1px);
        }

        .glass-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 32px;
            position: relative;
            overflow: hidden;
        }
        
        /* Subtle top gradient bar for premium feel */
        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--primary);
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border-color);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-bottom: 32px;
        }

        @media (min-width: 640px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .full-width {
                grid-column: 1 / -1;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-main);
        }

        .text-danger { color: var(--danger); }
        .text-muted { color: var(--text-muted); font-size: 0.75rem; font-weight: normal; }

        .form-control {
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
            color: var(--text-main);
            background-color: #fff;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02) inset;
            width: 100%;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .file-input-wrapper {
            position: relative;
        }
        
        .file-input-wrapper input[type="file"] {
            padding: 8px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
        }
        
        .file-input-wrapper input[type="file"]:hover {
            border-color: var(--primary);
            background: #f1f5f9;
        }

        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background-color: var(--success-bg);
            color: var(--success-text);
            border: 1px solid #a7f3d0;
        }
        
        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert a {
            color: currentColor;
            font-weight: 600;
            text-decoration: underline;
        }

        .form-actions {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-submit {
            padding: 10px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(79, 70, 229, 0.3);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-reset {
            padding: 10px 20px;
            background: white;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-reset:hover {
            background: #f1f5f9;
            color: var(--text-main);
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="upload-container">
    <div class="upload-header">
        <h2>Upload Document: <?php echo htmlspecialchars($doc_type['type_label']); ?></h2>
        <a href="<?php echo htmlspecialchars($role_url ?? 'dashboard.php'); ?>" class="btn-back">
            &larr; Back
        </a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <div>
                <strong>Success!</strong> Your document has been uploaded and submitted for review.
                <br>
                <a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" style="display:inline-block; margin-top:8px;">Upload Another</a>
            </div>
        </div>
    <?php else: ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <div><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></div>
            </div>
        <?php endif; ?>

        <div class="glass-card">
            <form method="POST" enctype="multipart/form-data">
                <?php echo csrfField(); // FIXED: using csrfField() instead of csrfToken() ?>
                
                <h3 class="section-title">Core Information</h3>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">Document Title <span class="text-danger">*</span></label>
                        <input type="text" name="doc_title" class="form-control" required placeholder="Enter a descriptive title for this record" value="<?php echo htmlspecialchars($_POST['doc_title'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <select name="academic_year_id" class="form-control" required>
                            <option value="">Select Year...</option>
                            <?php foreach ($years as $y): ?>
                                <option value="<?php echo $y['year_id']; ?>" <?php echo (($_POST['academic_year_id'] ?? '') == $y['year_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($y['year_label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <?php if (!empty($schema_fields)): ?>
                    <h3 class="section-title">Specific Details</h3>
                    <div class="form-grid">
                        <?php foreach ($schema_fields as $f): 
                            $isRequired = $f['required'] ? '<span class="text-danger">*</span>' : '';
                            $reqAttr = $f['required'] ? 'required' : '';
                            $val = htmlspecialchars($_POST[$f['name']] ?? '');
                            $isFullWidth = ($f['type'] === 'textarea' || $f['type'] === 'author_table') ? 'full-width' : '';
                        ?>
                            <div class="form-group <?php echo $isFullWidth; ?>">
                                <label class="form-label"><?php echo htmlspecialchars($f['label']) . ' ' . $isRequired; ?></label>
                                
                                <?php if ($f['type'] === 'textarea'): ?>
                                    <textarea name="<?php echo $f['name']; ?>" class="form-control" <?php echo $reqAttr; ?>><?php echo $val; ?></textarea>
                                
                                <?php elseif ($f['type'] === 'select' && isset($f['options'])): ?>
                                    <select name="<?php echo $f['name']; ?>" class="form-control" <?php echo $reqAttr; ?>>
                                        <option value="">Select...</option>
                                        <?php foreach ($f['options'] as $opt): ?>
                                            <option value="<?php echo htmlspecialchars($opt); ?>" <?php echo ($val === $opt) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($opt); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                <?php elseif ($f['type'] === 'dept_category'): 
                                    $evt = $_GET['event'] ?? '';
                                    $options = [];
                                    switch ($evt) {
                                        case 'admin':
                                            $options = ['Course Structure', 'Result Analysis', 'Course Schedule', "Faculty's Feedback by Students", 'Feedback from Parents', 'Employer Feedback', 'Department Area Details', 'Departmental Laboratory Details', 'Major Equipment in the Laboratories', 'List of Experiments', 'Major Equipment Utilization Record', 'Equipment Maintenance Record', 'Courses Linked with Employability', 'Financial Statement/Budget Status', 'Departmental Library Details', 'Seminars/Workshops/Conferences Organized', 'Industrial Visits', 'Guest Lectures', 'List of Projects', 'Add-on Course/Training Conducted', 'Consultancy-New', 'External Sports and Projects', 'Transferrable and Life Skills Courses', 'Remedial Classes', 'Course End Feedback Form', 'Class Time Table', 'Faculty Time Table', 'Classroom Time Table', 'Lab Time Table', 'Student Progression to Higher Education', 'Feedback from Students/Alumni/Academic Peer', 'Course File-Index', 'Feedback on Curriculum from Students/Employer/Alumni', 'Workshops-Seminars on Research Methodology', 'Intellectual Property Rights (IPR)', 'Entrepreneurship-New', 'Professional Societies Chapters', 'Engineering Events Organized', 'Product Development Activities', 'Collaborative Activities', 'Functional MoUs with Ongoing Activities', 'Mini Project Work', 'Term Paper Work', 'Mentoring'];
                                            break;
                                        case 'faculty':
                                            $options = ['Faculty List', 'Faculty Profile', 'Academic Research', 'Books and Chapters Published', 'Faculty in Inter-Departmental/Institutional Activities', 'Faculty for Higher Studies', 'Faculty Attended Seminars/Internships', 'Faculty Self-Appraisal', 'Non-Teaching Staff Skill Upgradation', 'Observations on Student Feedback', 'Full-Time Teachers with PhD Guidance', 'Consultancy and Corporate Training', 'Financial Support to Faculty', 'Publication of Technical Magazines/Newsletters'];
                                            break;
                                        case 'student':
                                            $options = ['List of Forms', 'Student Addresses', 'Cumulative Monthly Attendance', 'Semester End Attendance', 'Condonation List', 'Detention List', 'Papers Published by Students', 'Students in Competitive Exams', 'Co-Curricular/Extra-Curricular Activities', 'Placement Record', 'Alumni Interaction', 'Field Projects/Internships', 'List of Seminars/Workshops Attended', 'Online Courses Completed', 'Coding/Hardware Competitions', 'Capacity Development Activities', 'Guidance for Competitive Exams', 'Career Counselling'];
                                            break;
                                        case 'exam':
                                            $options = ['Notice for Internal Lab Exams', 'Invigilation Schedule', 'Absentee Statement', 'Sessional Marks Record', 'Final Sessional Marks'];
                                            break;
                                    }
                                ?>
                                    <select name="<?php echo $f['name']; ?>" class="form-control" <?php echo $reqAttr; ?>>
                                        <option value="">Select File Category...</option>
                                        <?php foreach ($options as $opt): ?>
                                            <option value="<?php echo htmlspecialchars($opt); ?>" <?php echo ($val === $opt) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($opt); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                <?php elseif ($f['type'] === 'author_table'): ?>
                                    <div class="table-responsive" style="overflow-x: auto; border: 1px solid var(--border-color); border-radius: 8px;">
                                        <table class="author-table" style="width: 100%; min-width: 600px; border-collapse: collapse; margin-bottom: 0;">
                                            <thead style="background: #f8fafc; border-bottom: 1px solid var(--border-color);">
                                                <tr>
                                                    <th style="padding: 10px; text-align: left; font-size: 0.85rem; color: var(--text-muted);">Name of Author</th>
                                                    <th style="padding: 10px; text-align: left; font-size: 0.85rem; color: var(--text-muted);">Affiliation</th>
                                                    <th style="padding: 10px; text-align: left; font-size: 0.85rem; color: var(--text-muted);">Position</th>
                                                    <th style="padding: 10px; text-align: center; width: 50px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="authorTableBody-<?php echo $f['name']; ?>">
                                                <tr>
                                                    <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><input type="text" name="<?php echo $f['name']; ?>[name][]" class="form-control" style="padding:6px; font-size:0.85rem;" required></td>
                                                    <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><input type="text" name="<?php echo $f['name']; ?>[affiliation][]" class="form-control" style="padding:6px; font-size:0.85rem;" required></td>
                                                    <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                                                        <select name="<?php echo $f['name']; ?>[position][]" class="form-control" style="padding:6px; font-size:0.85rem;" required>
                                                            <option value="First author">First author</option>
                                                            <option value="First author with equal contribution">First author with equal contribution</option>
                                                            <option value="Corresponding Author">Corresponding Author</option>
                                                            <option value="Co-author">Co-author</option>
                                                        </select>
                                                    </td>
                                                    <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center;">
                                                        <button type="button" onclick="this.closest('tr').remove()" style="padding: 4px 8px; background: #fee2e2; color: #ef4444; border: 1px solid #fca5a5; border-radius: 4px; cursor: pointer; font-weight: bold;">&times;</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <button type="button" onclick="addAuthorRow('<?php echo $f['name']; ?>')" style="margin: 10px; padding: 6px 12px; background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">+ Add Author</button>
                                    </div>
                                    
                                <?php else: ?>
                                    <input type="<?php echo htmlspecialchars($f['type']); ?>" name="<?php echo $f['name']; ?>" class="form-control" <?php echo $reqAttr; ?> value="<?php echo $val; ?>">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h3 class="section-title">File Attachments</h3>
                <div class="form-grid">
                    <?php foreach ($schema_files as $f): 
                        $isRequired = $f['required'] ? '<span class="text-danger">*</span>' : '<span class="text-muted">(Optional)</span>';
                        $reqAttr = $f['required'] ? 'required' : '';
                    ?>
                        <div class="form-group file-input-wrapper">
                            <label class="form-label"><?php echo htmlspecialchars($f['label']) . ' ' . $isRequired; ?></label>
                            <input type="file" name="<?php echo $f['name']; ?>" class="form-control" accept="<?php echo htmlspecialchars($f['accept'] ?? '*/*'); ?>" <?php echo $reqAttr; ?>>
                            <?php if (!empty($f['accept'])): ?>
                                <span class="text-muted">Accepted formats: <?php echo htmlspecialchars($f['accept']); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn-reset">Clear Form</button>
                    <button type="submit" class="btn-submit">Submit Document</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
    
</div>

<script>
function addAuthorRow(fieldName) {
    const tbody = document.getElementById('authorTableBody-' + fieldName);
    if (!tbody) return;
    
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><input type="text" name="${fieldName}[name][]" class="form-control" style="padding:6px; font-size:0.85rem;" required></td>
        <td style="padding: 10px; border-bottom: 1px solid var(--border-color);"><input type="text" name="${fieldName}[affiliation][]" class="form-control" style="padding:6px; font-size:0.85rem;" required></td>
        <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
            <select name="${fieldName}[position][]" class="form-control" style="padding:6px; font-size:0.85rem;" required>
                <option value="First author">First author</option>
                <option value="First author with equal contribution">First author with equal contribution</option>
                <option value="Corresponding Author">Corresponding Author</option>
                <option value="Co-author">Co-author</option>
            </select>
        </td>
        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center;">
            <button type="button" onclick="this.closest('tr').remove()" style="padding: 4px 8px; background: #fee2e2; color: #ef4444; border: 1px solid #fca5a5; border-radius: 4px; cursor: pointer; font-weight: bold;">&times;</button>
        </td>
    `;
    tbody.appendChild(tr);
}
</script>

</body>
</html>