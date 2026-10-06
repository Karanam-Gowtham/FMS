<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> — FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <style>
        .upload-container { max-width: 800px; margin: 2rem auto; padding: 2rem; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 0.3rem; color: #333; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 0.6rem; border: 1px solid #ccc; border-radius: 6px;
            font-size: 0.95rem; box-sizing: border-box;
        }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: #4a90d9; outline: none; box-shadow: 0 0 0 2px rgba(74,144,217,0.2);
        }
        .form-group .required::after { content: ' *'; color: #e74c3c; }
        .meta-section { background: #f8f9fa; padding: 1.2rem; border-radius: 8px; margin: 1.2rem 0; border: 1px solid #e9ecef; }
        .meta-section h3 { margin-top: 0; color: #495057; font-size: 1.1rem; }
        .file-section { background: #fff3cd; padding: 1.2rem; border-radius: 8px; margin: 1.2rem 0; border: 1px solid #ffc107; }
        .file-section h3 { margin-top: 0; color: #856404; font-size: 1.1rem; }
        .btn-upload {
            background: #28a745; color: white; border: none; padding: 0.8rem 2rem;
            border-radius: 6px; font-size: 1rem; cursor: pointer; font-weight: 600;
        }
        .btn-upload:hover { background: #218838; }
        .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1rem; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .type-selector { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.8rem; margin: 1rem 0; }
        .type-card {
            padding: 1rem; border: 2px solid #e9ecef; border-radius: 8px; text-decoration: none;
            color: #333; transition: border-color 0.2s, box-shadow 0.2s; display: block;
        }
        .type-card:hover { border-color: #4a90d9; box-shadow: 0 2px 8px rgba(74,144,217,0.15); }
        .type-card.active { border-color: #28a745; background: #f0fff4; }
        .type-card .type-label { font-weight: 600; font-size: 0.95rem; }
        .type-card .type-category { font-size: 0.8rem; color: #6c757d; text-transform: uppercase; }
        .breadcrumb-bar { background: #f8f9fa; padding: 0.8rem 2rem; border-bottom: 1px solid #e9ecef; font-size: 0.9rem; color: #6c757d; }
        .breadcrumb-bar a { color: #4a90d9; text-decoration: none; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="breadcrumb-bar">
    <a href="<?= htmlspecialchars(get_role_landing_url(auth_active_role())) ?>">Dashboard</a> &raquo;
    <?php if ($doc_type): ?>
        <?php if (!empty($preselected_subtype) && $preselected_subtype !== $doc_type['type_label']): ?>
            <?= htmlspecialchars($doc_type['type_label']) ?> &raquo;
            <span id="dynamic-breadcrumb"><?= htmlspecialchars(isset($_GET['activity']) ? $_GET['activity'] : (isset($_GET['exam']) ? $_GET['exam'] : $preselected_subtype)) ?></span>
        <?php else: ?>
            <span id="dynamic-breadcrumb"><?= htmlspecialchars(isset($_GET['activity']) ? $_GET['activity'] : (isset($_GET['exam']) ? $_GET['exam'] : $doc_type['type_label'])) ?></span>
        <?php endif; ?>
    <?php else: ?>
        Upload Document
    <?php endif; ?>
</div>

<div class="upload-container">
    <h1 id="page_main_heading"><?= $page_title ?></h1>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <?php if (!$doc_type): ?>
        <!-- Type selector -->
        <p>Select the type of document you want to upload:</p>

        <?php
        $categories = [];
        foreach ($types as $t) {
            $categories[$t['category']][] = $t;
        }
        ?>

        <?php foreach ($categories as $cat => $types): ?>
            <h3 style="text-transform: capitalize; margin-top: 1.5rem;"><?= htmlspecialchars($cat) ?></h3>
            <div class="type-selector">
                <?php foreach ($types as $t): ?>
                    <a href="?type=<?= urlencode($t['type_key']) ?>" class="type-card">
                        <div class="type-label"><?= htmlspecialchars($t['type_label']) ?></div>
                        <div class="type-category"><?= htmlspecialchars($t['category']) ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <!-- Upload form -->
        <form method="POST" enctype="multipart/form-data" id="uploadForm" action="<?= BASE_URL ?>/public/index.php?route=documents/upload">
            <?php if (function_exists('csrfField')) echo csrfField(); ?>
            <input type="hidden" name="type_key" value="<?= htmlspecialchars($type_key) ?>">


            <?php if (count($user_depts) > 1): ?>
                <div class="form-group">
                    <label class="required" for="dept_id">Department</label>
                    <select id="dept_id" name="dept_id" required>
                        <option value="">— Select Department —</option>
                        <?php foreach ($user_depts as $did => $dname): ?>
                            <option value="<?= $did ?>"><?= htmlspecialchars($dname) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <?php foreach ($user_depts as $did => $dname): ?>
                    <input type="hidden" name="dept_id" value="<?= $did ?>">
                <?php endforeach; ?>
            <?php endif; ?>
            <!-- Mentor Per No for Students -->
            <?php if (auth_active_role()['role_id'] == ROLE_STUDENT): ?>
                <div class="form-group">
                    <label class="required" for="mentor_per_no">Mentor's Per No.</label>
                    <input type="text" id="mentor_per_no" name="mentor_per_no" required placeholder="Enter the exact Per No of your mentor">
                    <small style="color: #6c757d;">Your upload will be sent to this mentor for verification.</small>
                </div>
            <?php endif; ?>

            <!-- Base meta fields -->
            <div class="meta-section" style="background: transparent; border: none; padding: 0; margin-bottom: 0; box-shadow: none;">
                
                <?php $has_custom_title = (strpos($type_key, 'student_') === 0 || in_array($type_key, ['exam_qual', 'journal', 'conference', 'patent'])); ?>
                <div class="form-group" <?= $has_custom_title ? 'style="display: none;"' : '' ?>>
                    <label class="<?= $has_custom_title ? '' : 'required' ?>" for="title">Title</label>
                    <input type="text" id="title" name="title" <?= $has_custom_title ? '' : 'required' ?>>
                </div>

                <div class="form-group">
                    <label for="year_id">Select Academic Year</label>
                    <select id="year_id" name="year_id">
                        <option value="">Select an academic year</option>
                        <?php foreach ($academic_years as $yr): ?>
                            <option value="<?= $yr['year_id'] ?>"<?= ($active_year && $yr['year_id'] == $active_year['year_id']) ? ' selected' : '' ?>>
                                <?= htmlspecialchars($yr['year_label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Type-specific meta fields -->
            <?php if (!empty($meta_fields)): ?>
            <div class="meta-section" style="background: transparent; border: none; padding: 0; margin-top: 0; box-shadow: none;">
                <?php foreach ($meta_fields as $field): ?>
                    <?php 
                    $isHiddenDeptCategory = ($field['type'] === 'dept_category' && !empty($preselected_subtype)); 
                    ?>
                    <div class="form-group" <?= $isHiddenDeptCategory ? 'style="display:none;"' : '' ?>>
                        <?php 
                        $dynamic_label = $field['label'];
                        if ($field['name'] === 'sub_file_type' && $preselected_subtype === 'Student Activities Files') {
                            $dynamic_label = 'Select an Activity';
                        }
                        ?>
                        <label class="<?= $field['required'] ? 'required' : '' ?>" for="meta_<?= $field['name'] ?>">
                            <?= htmlspecialchars($dynamic_label) ?>
                        </label>
                        <?php if ($field['type'] === 'textarea'): ?>
                            <textarea id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                      <?= $field['required'] ? 'required data-required="true"' : '' ?>></textarea>
                        <?php elseif ($field['type'] === 'date'): ?>
                            <input type="date" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                   <?= $field['required'] ? 'required data-required="true"' : '' ?>>
                        <?php elseif ($field['type'] === 'number'): ?>
                            <input type="number" step="any" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                   <?= $field['required'] ? 'required data-required="true"' : '' ?>>
                        <?php elseif ($field['type'] === 'url'): ?>
                            <input type="url" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                   <?= $field['required'] ? 'required data-required="true"' : '' ?> placeholder="https://">
                        <?php elseif ($field['type'] === 'select'): ?>
                            <select id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" <?= $field['required'] ? 'required data-required="true"' : '' ?>>
                                <?php if (!isset($field['default'])): ?>
                                    <option value="">— Select —</option>
                                <?php endif; ?>
                                <?php if (!empty($field['options'])): ?>
                                    <?php foreach ($field['options'] as $opt): ?>
                                        <?php 
                                        $selected = '';
                                        if ($field['name'] === 'activity_category' && isset($_GET['activity']) && $_GET['activity'] === $opt) {
                                            $selected = 'selected';
                                        } elseif (isset($field['default']) && $field['default'] === $opt) {
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="<?= htmlspecialchars($opt) ?>" <?= $selected ?>><?= htmlspecialchars($opt) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        <?php elseif ($field['type'] === 'radio'): ?>
                            <div class="radio-group" style="display: flex; gap: 15px; margin-top: 5px;">
                                <?php if (!empty($field['options'])): ?>
                                    <?php foreach ($field['options'] as $opt): ?>
                                        <label style="font-weight: normal; margin: 0; display: flex; align-items: center; gap: 5px;">
                                            <input type="radio" name="meta[<?= $field['name'] ?>]" value="<?= htmlspecialchars($opt) ?>" <?= $field['required'] ? 'required' : '' ?>>
                                            <?= htmlspecialchars($opt) ?>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($field['type'] === 'author_table'): ?>
                            <?php 
                                $isInventor = ($field['name'] === 'inventors' || $field['name'] === 'investors');
                                $personLabel = $isInventor ? 'Inventor' : 'Author';
                            ?>
                            <table class="author-table" style="width: 100%; margin-bottom: 1rem; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th style="border: 1px solid #ccc; padding: 5px;">Name of the <?= $personLabel ?></th>
                                        <th style="border: 1px solid #ccc; padding: 5px;">Affiliation</th>
                                        <th style="border: 1px solid #ccc; padding: 5px;">Position</th>
                                        <th style="border: 1px solid #ccc; padding: 5px; width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $hasOldAuthors = !empty($old_input['meta'][$field['name']]['name']) && is_array($old_input['meta'][$field['name']]['name']);
                                    $rowCount = $hasOldAuthors ? count($old_input['meta'][$field['name']]['name']) : 1;
                                    for ($i = 0; $i < $rowCount; $i++): 
                                        $oldName = $hasOldAuthors ? ($old_input['meta'][$field['name']]['name'][$i] ?? '') : '';
                                        $oldAffil = $hasOldAuthors ? ($old_input['meta'][$field['name']]['affiliation'][$i] ?? '') : '';
                                        $oldPos = $hasOldAuthors ? ($old_input['meta'][$field['name']]['position'][$i] ?? '') : '';
                                    ?>
                                    <tr>
                                        <td style="border: 1px solid #ccc; padding: 5px;"><input type="text" name="meta[<?= $field['name'] ?>][name][]" value="<?= htmlspecialchars($oldName) ?>" style="width:100%; border:none; padding:5px;"></td>
                                        <td style="border: 1px solid #ccc; padding: 5px;"><input type="text" name="meta[<?= $field['name'] ?>][affiliation][]" value="<?= htmlspecialchars($oldAffil) ?>" style="width:100%; border:none; padding:5px;"></td>
                                        <td style="border: 1px solid #ccc; padding: 5px;">
                                            <select name="meta[<?= $field['name'] ?>][position][]" style="width:100%; border:none; padding:5px;">
                                                <?php if ($isInventor): ?>
                                                    <option value="Main Inventor" <?= $oldPos === 'Main Inventor' ? 'selected' : '' ?>>Main Inventor</option>
                                                    <option value="Co-Inventor" <?= $oldPos === 'Co-Inventor' ? 'selected' : '' ?>>Co-Inventor</option>
                                                <?php else: ?>
                                                    <option value="First author" <?= $oldPos === 'First author' ? 'selected' : '' ?>>First author</option>
                                                    <option value="First author with equal contribution" <?= $oldPos === 'First author with equal contribution' ? 'selected' : '' ?>>First author with equal contribution</option>
                                                    <option value="Corresponding Author" <?= $oldPos === 'Corresponding Author' ? 'selected' : '' ?>>Corresponding Author</option>
                                                    <option value="Co-author" <?= $oldPos === 'Co-author' ? 'selected' : '' ?>>Co-author</option>
                                                <?php endif; ?>
                                            </select>
                                        </td>
                                        <td style="border: 1px solid #ccc; padding: 5px; text-align: center;">
                                            <button type="button" onclick="removeAuthorRow(this)" style="background: none; border: none; color: #ef4444; font-weight: bold; cursor: pointer; font-size: 20px; line-height: 1;" title="Remove Row">&minus;</button>
                                        </td>
                                    </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                            <button type="button" class="btn-sm" style="background:#e2e8f0; color:#334155; margin-bottom:1rem;" onclick="addAuthorRow(this, '<?= $field['name'] ?>')">+ Add <?= $personLabel ?></button>
                        <?php elseif ($field['type'] === 'dept_category'): ?>
                            <?php if (!empty($preselected_subtype)): ?>
                                <input type="hidden" name="meta[<?= $field['name'] ?>]" value="<?= htmlspecialchars($preselected_subtype) ?>">
                            <?php else: ?>
                                <div class="form-group">
                                    <label class="<?= $field['required'] ? 'required' : '' ?>" for="meta_<?= $field['name'] ?>">
                                        <?= htmlspecialchars($field['label']) ?>
                                    </label>
                                    <select id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" <?= $field['required'] ? 'required data-required="true"' : '' ?>>
                                        <option value="">— Select Category —</option>
                                        <?php
                                        $categories = ['Admin Files', 'Faculty Files', 'Student Related Files', 'Exam Section Files', 'Student Activities Files'];
                                        foreach ($categories as $cat) {
                                            $selected = (!empty($preselected_subtype) && $preselected_subtype === $cat) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($cat) . '" ' . $selected . '>' . htmlspecialchars($cat) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                        <?php elseif ($field['type'] === 'sub_file_type'): ?>
                            <select id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" <?= $field['required'] ? 'required data-required="true"' : '' ?> onchange="handleStudentActivityChange(this)">
                                <option value=""><?= htmlspecialchars($dynamic_label) ?></option>
                                <?php
                                $options = [];
                                if ($preselected_subtype === 'Admin Files') {
                                    $options = ['Course Structure', 'Result Analysis', 'Course Schedule', "Faculty's Feedback by Students", 'Feedback from Parents', 'Employer Feedback', 'Department Area Details', 'Departmental Laboratory Details', 'Major Equipment in the Laboratories', 'List of Experiments', 'Major Equipment Utilization Record', 'Equipment Maintenance Record', 'Courses Linked with Employability', 'Financial Statement/Budget Status', 'Departmental Library Details', 'Seminars/Workshops/Conferences Organized', 'Industrial Visits', 'Guest Lectures', 'List of Projects', 'Add-on Course/Training Conducted', 'Consultancy-New', 'External Sports and Projects', 'Transferrable and Life Skills Courses', 'Remedial Classes', 'Course End Feedback Form', 'Class Time Table', 'Faculty Time Table', 'Classroom Time Table', 'Lab Time Table', 'Student Progression to Higher Education', 'Feedback from Students/Alumni/Academic Peer', 'Course File-Index', 'Feedback on Curriculum from Students/Employer/Alumni', 'Workshops-Seminars on Research Methodology', 'Intellectual Property Rights (IPR)', 'Entrepreneurship-New', 'Professional Societies Chapters', 'Engineering Events Organized', 'Product Development Activities', 'Collaborative Activities', 'Functional MoUs with Ongoing Activities', 'Mini Project Work', 'Term Paper Work', 'Mentoring'];
                                } elseif ($preselected_subtype === 'Faculty Files') {
                                    $options = ['Faculty List', 'Faculty Profile', 'Academic Research', 'Books and Chapters Published', 'Faculty in Inter-Departmental/Institutional Activities', 'Faculty for Higher Studies', 'Faculty Attended Seminars/Internships', 'Faculty Self-Appraisal', 'Non-Teaching Staff Skill Upgradation', 'Observations on Student Feedback', 'Full-Time Teachers with PhD Guidance', 'Consultancy and Corporate Training', 'Financial Support to Faculty', 'Publication of Technical Magazines/Newsletters'];
                                } elseif ($preselected_subtype === 'Student Related Files') {
                                    $options = ['List of Forms', 'Student Addresses', 'Cumulative Monthly Attendance', 'Semester End Attendance', 'Condonation List', 'Detention List', 'Papers Published by Students', 'Students in Competitive Exams', 'Co-Curricular/Extra-Curricular Activities', 'Placement Record', 'Alumni Interaction', 'Field Projects/Internships', 'List of Seminars/Workshops Attended', 'Online Courses Completed', 'Coding/Hardware Competitions', 'Capacity Development Activities', 'Guidance for Competitive Exams', 'Career Counselling'];
                                } elseif ($preselected_subtype === 'Exam Section Files') {
                                    $options = ['Notice for Internal Lab Exams', 'Invigilation Schedule', 'Absentee Statement', 'Sessional Marks Record', 'Final Sessional Marks'];
                                } elseif ($preselected_subtype === 'Student Activities Files') {
                                    $options = ['Grad Talks', 'Expert Talks / Guest Lectures', 'Soft skills', 'Language and communication skills', 'Life skills', 'Professional Societies', 'Clubs', 'IIC'];
                                }

                                foreach ($options as $opt) {
                                    echo '<option value="' . htmlspecialchars($opt) . '">' . htmlspecialchars($opt) . '</option>';
                                }
                                ?>
                            </select>
                        <?php else: ?>
                            <?php
                            $default_val = '';
                            if ($field['name'] === 'activity' && isset($_GET['activity'])) {
                                $default_val = $_GET['activity'];
                            } elseif ($field['name'] === 'exam' && isset($_GET['exam'])) {
                                $default_val = $_GET['exam'];
                            }
                            ?>
                            <input type="text" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                   value="<?= htmlspecialchars($default_val) ?>"
                                   <?= $field['required'] ? 'required data-required="true"' : '' ?> <?= ($default_val !== '') ? 'readonly style="background-color: #e9ecef; cursor: not-allowed; color: #6c757d;"' : '' ?>>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- File upload slots -->
            <div class="file-section" style="background: transparent; border: none; padding: 0; margin-top: 0; box-shadow: none; <?= ($type_key === 'dept_file' && $preselected_subtype === 'Student Activities Files') ? 'display: none;' : '' ?>">
                <?php foreach ($file_slots as $slot): ?>
                    <div class="form-group">
                        <label class="<?= $slot['required'] ? 'required' : '' ?>" for="file_<?= $slot['name'] ?>">
                            <?= htmlspecialchars($slot['label']) ?>
                        </label>
                        <input type="file" id="file_<?= $slot['name'] ?>" name="<?= $slot['name'] ?>"
                               accept="<?= htmlspecialchars($slot['accept']) ?>"
                               <?= $slot['required'] ? 'required' : '' ?>>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn-upload" <?= ($type_key === 'dept_file' && $preselected_subtype === 'Student Activities Files') ? 'style="display: none;"' : '' ?>>Upload Document</button>
            <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload" style="margin-left: 1rem; color: #6c757d;">← Choose Different Type</a>
        </form>
    <?php endif; ?>
</div>

<script>
function addAuthorRow(btn, fieldName) {
    var table = btn.previousElementSibling;
    var tbody = table.getElementsByTagName('tbody')[0];
    var newRow = tbody.insertRow(tbody.rows.length);
    
    var cell1 = newRow.insertCell(0);
    var cell2 = newRow.insertCell(1);
    var cell3 = newRow.insertCell(2);
    var cell4 = newRow.insertCell(3);
    
    cell1.innerHTML = '<input type="text" name="meta[' + fieldName + '][name][]" style="width:100%; border:none; padding:5px;">';
    cell1.style.border = '1px solid #ccc';
    cell1.style.padding = '5px';
    
    cell2.innerHTML = '<input type="text" name="meta[' + fieldName + '][affiliation][]" style="width:100%; border:none; padding:5px;">';
    cell2.style.border = '1px solid #ccc';
    cell2.style.padding = '5px';
    
    var selectHtml = '';
    if (fieldName === 'inventors' || fieldName === 'investors') {
        selectHtml = `<select name="meta[` + fieldName + `][position][]" style="width:100%; border:none; padding:5px;">
                        <option value="Main Inventor">Main Inventor</option>
                        <option value="Co-Inventor">Co-Inventor</option>
                      </select>`;
    } else {
        selectHtml = `<select name="meta[` + fieldName + `][position][]" style="width:100%; border:none; padding:5px;">
                        <option value="First author">First author</option>
                        <option value="First author with equal contribution">First author with equal contribution</option>
                        <option value="Corresponding Author">Corresponding Author</option>
                        <option value="Co-author">Co-author</option>
                      </select>`;
    }
    cell3.innerHTML = selectHtml;
    cell3.style.border = '1px solid #ccc';
    cell3.style.padding = '5px';
    
    cell4.innerHTML = '<button type="button" onclick="removeAuthorRow(this)" style="background: none; border: none; color: #ef4444; font-weight: bold; cursor: pointer; font-size: 20px; line-height: 1;" title="Remove Row">&minus;</button>';
    cell4.style.border = '1px solid #ccc';
    cell4.style.padding = '5px';
    cell4.style.textAlign = 'center';
}

function removeAuthorRow(btn) {
    var row = btn.closest('tr');
    var tbody = row.closest('tbody');
    if (tbody.rows.length > 1) {
        row.remove();
    } else {
        var inputs = row.querySelectorAll('input');
        inputs.forEach(input => input.value = '');
        var select = row.querySelector('select');
        if (select) select.selectedIndex = 0;
    }
}

function handleStudentActivityChange(select) {
    var val = select.value;
    var type = '';
    var subtypeParam = '';
    
    if (!val) return;
    
    var intraCollegeCategories = ['Grad Talks', 'Expert Talks / Guest Lectures', 'Soft skills', 'Language and communication skills', 'Life skills', 'Professional Societies', 'Clubs', 'IIC'];
    if (intraCollegeCategories.includes(val)) {
        type = 'student_activity_file';
        subtypeParam = '&activity=' + encodeURIComponent(val);
    } else {
        // Fallbacks
        if (val === 'Journal Papers' || val === 'Papers Published by Students') {
            type = 'student_journal';
        } else if (val === 'Conference Papers') {
            type = 'student_conference';
        } else if (val === 'Professional Bodies') {
            type = 'student_body';
        } else if (val === 'GATE' || val === 'Students in Competitive Exams') {
            type = 'exam_qual';
            subtypeParam = '&exam=' + encodeURIComponent(val);
        } else {
            // DO NOT redirect for standard Department Admin / Faculty files
            return;
        }
    }
    
    if (type) {
        var url = '<?= BASE_URL ?>/public/index.php?route=documents/upload&type=' + type + subtypeParam;
        <?php if (!empty($_GET['sub_type'])): ?>
        url += '&sub_type=<?= urlencode($_GET['sub_type']) ?>';
        <?php endif; ?>
        window.location.href = url;
    }
}
</script>

<?php if ($type_key === 'student_activity_file'): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var activityCatEl = document.getElementById('meta_activity_category');
    var eventTypeEl = document.getElementById('meta_event_type');
    var topicDomainEl = document.getElementById('meta_topic_domain');
    var subCatEl = document.getElementById('meta_sub_category');
    var resourcePersonEl = document.getElementById('meta_resource_person');
    var locationEl = document.getElementById('meta_location');
    
    var targetAudienceEl = document.getElementById('meta_target_audience');
    var eventTitleEl = document.getElementById('meta_event_title');
    
    var eventTypeGrp = eventTypeEl.closest('.form-group');
    var topicDomainGrp = topicDomainEl ? topicDomainEl.closest('.form-group') : null;
    var subCatGrp = subCatEl.closest('.form-group');
    var resourcePersonGrp = resourcePersonEl.closest('.form-group');
    var locationGrp = locationEl ? locationEl.closest('.form-group') : null;
    var targetAudienceGrp = targetAudienceEl ? targetAudienceEl.closest('.form-group') : null;
    var eventTitleGrp = eventTitleEl ? eventTitleEl.closest('.form-group') : null;
    
    var circularGrp = document.getElementById('file_circular') ? document.getElementById('file_circular').closest('.form-group') : null;
    var scheduleGrp = document.getElementById('file_schedule') ? document.getElementById('file_schedule').closest('.form-group') : null;
    var attendanceGrp = document.getElementById('file_attendance') ? document.getElementById('file_attendance').closest('.form-group') : null;
    var certificateGrp = document.getElementById('file_certificate') ? document.getElementById('file_certificate').closest('.form-group') : null;
    var resourceProfileGrp = document.getElementById('file_resource_person_profile') ? document.getElementById('file_resource_person_profile').closest('.form-group') : null;

    // Helper to change input type (e.g. text to select)
    function changeInputType(element, type, options, placeholder) {
        var newEl;
        if (type === 'select') {
            newEl = document.createElement('select');
            newEl.innerHTML = '<option value="">' + placeholder + '</option>';
            options.forEach(function(opt) {
                var isSelected = (element.value === opt) ? 'selected' : '';
                newEl.innerHTML += '<option value="' + opt + '" ' + isSelected + '>' + opt + '</option>';
            });
        } else {
            newEl = document.createElement('input');
            newEl.type = 'text';
            newEl.placeholder = placeholder;
            newEl.value = element.value;
        }
        newEl.id = element.id;
        newEl.name = element.name;
        newEl.className = element.className;
        var isReq = element.hasAttribute('data-required') || element.hasAttribute('required');
        if (isReq) {
            newEl.setAttribute('data-required', 'true');
        }
        element.parentNode.replaceChild(newEl, element);
        return newEl;
    }

    var clubDatalist = document.createElement('datalist');
    clubDatalist.id = 'club_names_list';
    [
        'Coding Club', 'Google Developer Student Club (GDSC)', 'Robotics Club', 'STEM Club', 'Math Club', 'Project Club', 'Sector Club', 'HAM Club',
        'Film Club', 'Music Club', 'Dance Club', 'Photography Club', 'Literary Club', 'Hobby Club',
        'Women Empowerment Club', 'Civil Services Aspirants Club (CSAC)', 'Green Eco Club', 'UBA Club'
    ].forEach(function(c) {
        var opt = document.createElement('option');
        opt.value = c;
        clubDatalist.appendChild(opt);
    });
    document.body.appendChild(clubDatalist);

    function toTitleCase(str) {
        return str.replace(/\w\S*/g, function(txt){
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        }).replace(/\s+/g, ' ').trim();
    }

    function updateForm() {
        var activityCat = document.getElementById('meta_activity_category').value;
        
        // Re-fetch elements in case they were replaced
        eventTypeEl = document.getElementById('meta_event_type');
        topicDomainEl = document.getElementById('meta_topic_domain');
        subCatEl = document.getElementById('meta_sub_category');
        
        eventTypeGrp.style.display = 'none';
        if (topicDomainGrp) topicDomainGrp.style.display = 'none';
        subCatGrp.style.display = 'none';
        resourcePersonGrp.style.display = 'none';
        if(scheduleGrp) scheduleGrp.style.display = 'none';
        if(resourceProfileGrp) resourceProfileGrp.style.display = 'none';
        
        // Default visibility for static fields
        if(targetAudienceGrp) targetAudienceGrp.style.display = 'block';
        if(eventTitleGrp) {
            eventTitleGrp.style.display = 'block';
            eventTitleGrp.querySelector('label').innerText = 'Title of the Event';
        }

        // Set Labels based on Category
        eventTypeGrp.querySelector('label').innerText = 'Event Type';
        subCatGrp.querySelector('label').innerText = 'Sub Category';
        resourcePersonGrp.querySelector('label').innerText = 'Resource Person Details';

        var mainHeading = document.getElementById('page_main_heading');
        if (mainHeading) {
            mainHeading.innerText = 'Upload: ' + activityCat;
        }

        var dynamicBreadcrumb = document.getElementById('dynamic-breadcrumb');
        if (dynamicBreadcrumb) {
            dynamicBreadcrumb.innerText = activityCat;
        }

        // 1. Grad Talks & Expert Talks / Guest Lectures
        if (activityCat === 'Grad Talks' || activityCat === 'Expert Talks / Guest Lectures') {
            if (topicDomainGrp) {
                topicDomainGrp.style.display = 'block';
                topicDomainEl = changeInputType(topicDomainEl, 'select', ['Career Counseling', 'Awareness of Trends and Technologies', 'Domain Specific / Technical Skill', 'Other'], '— Select Topic —');
            }
            
            if(eventTitleGrp) eventTitleGrp.querySelector('label').innerText = 'Title of the Event';
            
            resourcePersonGrp.style.display = 'block';
            if(resourceProfileGrp) resourceProfileGrp.style.display = 'block';
        } 
        // 2. Soft skills, Language and communication skills
        else if (activityCat === 'Soft skills' || activityCat === 'Language and communication skills') {
            resourcePersonGrp.style.display = 'block';
            resourcePersonGrp.querySelector('label').innerText = 'Trainer / Agency Details';
            if(eventTitleGrp) eventTitleGrp.querySelector('label').innerText = 'Program Name';
            if(scheduleGrp) scheduleGrp.style.display = 'block';
        }
        // 3. Life skills
        else if (activityCat === 'Life skills') {
            subCatGrp.style.display = 'block';
            subCatGrp.querySelector('label').innerText = 'Specific Skill';
            subCatEl = changeInputType(subCatEl, 'select', ['Yoga', 'Physical fitness', 'Health and hygiene', 'Other'], '— Select Skill —');
            
            resourcePersonGrp.style.display = 'block';
            resourcePersonGrp.querySelector('label').innerText = 'Trainer / Agency Details';
            if(eventTitleGrp) eventTitleGrp.querySelector('label').innerText = 'Program Name';
            if(scheduleGrp) scheduleGrp.style.display = 'block';
        }
        // 4. Professional Societies
        else if (activityCat === 'Professional Societies') {
            subCatGrp.style.display = 'block';
            subCatGrp.querySelector('label').innerText = 'Society Name';
            subCatEl = changeInputType(subCatEl, 'select', ['ACM', 'CSI', 'IEEE', 'IEI', 'IETE', 'IICHE', 'ISTE', 'SAE'], '— Select Society —');

            eventTypeGrp.style.display = 'block';
            eventTypeEl = changeInputType(eventTypeEl, 'select', ['Expert Talks / Guest Lectures', 'Grad Talks', 'Workshop', 'Seminar', 'Conference', 'Competition', 'Industrial Visit', 'Training Program', 'Other'], '— Select Event Type —');
            
            if (topicDomainGrp) {
                // Only show Topic / Domain for specific event types
                var currentEventType = eventTypeEl.value;
                if (currentEventType === 'Industrial Visit' || currentEventType === 'Competition') {
                    topicDomainGrp.style.display = 'none';
                } else {
                    topicDomainGrp.style.display = 'block';
                    topicDomainEl = changeInputType(topicDomainEl, 'select', ['Career Counseling', 'Awareness of Trends and Technologies', 'Domain Specific / Technical Skill', 'Other'], '— Select Topic / Domain —');
                }
            }
            
            // Adjust Resource Person label based on Event Type
            if (resourcePersonGrp) {
                resourcePersonGrp.style.display = 'block';
                var currentEventType = eventTypeEl.value;
                if (currentEventType === 'Industrial Visit') {
                    resourcePersonGrp.querySelector('label').innerText = 'Industry Name & Contact Person';
                } else if (currentEventType === 'Competition') {
                    resourcePersonGrp.querySelector('label').innerText = 'Jury / Evaluator Details';
                } else {
                    resourcePersonGrp.querySelector('label').innerText = 'Resource Person Details';
                }
            }
            
            if(scheduleGrp) scheduleGrp.style.display = 'block';
        }
        // 5. Clubs
        else if (activityCat === 'Clubs') {
            subCatGrp.style.display = 'block';
            subCatGrp.querySelector('label').innerText = 'Club Name';
            subCatEl = changeInputType(subCatEl, 'text', [], 'Enter Club Name');
            subCatEl.setAttribute('list', 'club_names_list');
            
            // Format text on blur
            subCatEl.addEventListener('blur', function(e) {
                e.target.value = toTitleCase(e.target.value);
            });

            eventTypeGrp.style.display = 'block';
            eventTypeGrp.querySelector('label').innerText = 'Activity Type';
            eventTypeEl = changeInputType(eventTypeEl, 'select', ['Awareness Campaign / Drive', 'Competition / Contest', 'Exhibition / Showcase', 'Guest Lecture / Expert Talk', 'Hackathon / Ideathon', 'Workshop / Seminar', 'Other'], '— Select Activity Type —');
            
            if (topicDomainGrp) {
                topicDomainGrp.style.display = 'none'; // Never needed for clubs
            }
            
            // Adjust Resource Person label based on Activity Type
            if (resourcePersonGrp) {
                resourcePersonGrp.style.display = 'block';
                var currentEventType = eventTypeEl.value;
                if (currentEventType === 'Competition / Contest' || currentEventType === 'Hackathon / Ideathon') {
                    resourcePersonGrp.querySelector('label').innerText = 'Jury / Evaluator Details (If any)';
                } else if (currentEventType === 'Workshop / Seminar' || currentEventType === 'Guest Lecture / Expert Talk') {
                    resourcePersonGrp.querySelector('label').innerText = 'Resource Person Details';
                } else {
                    resourcePersonGrp.querySelector('label').innerText = 'Chief Guest / Special Invitee (If any)';
                }
            }
            
            // Show Schedule for Fests, Competitions, Workshops, Hackathons
            if(scheduleGrp) {
                var currentEventType = eventTypeEl.value;
                if (currentEventType === 'Competition / Contest' || currentEventType === 'Workshop / Seminar' || currentEventType === 'Hackathon / Ideathon') {
                    scheduleGrp.style.display = 'block';
                } else {
                    scheduleGrp.style.display = 'none';
                }
            }
        }
        // 6. IIC (default behavior from before)
        else if (activityCat === 'IIC') {
            eventTypeGrp.style.display = 'block';
            eventTypeEl = changeInputType(eventTypeEl, 'select', ['Workshop', 'Hackathon', 'Conference', 'Competition', 'Other'], '— Select Event Type —');
            
            subCatGrp.style.display = 'block';
            subCatGrp.querySelector('label').innerText = 'Organization / Details';
            subCatEl = changeInputType(subCatEl, 'text', [], 'Enter Details');
            
            resourcePersonGrp.style.display = 'block';
            if(scheduleGrp) scheduleGrp.style.display = 'block';
        }

        // Toggle required attributes for all dynamic fields
        var allDynamicInputs = document.querySelectorAll('#meta_event_type, #meta_topic_domain, #meta_sub_category, #meta_resource_person, #file_schedule, #file_resource_person_profile');
        allDynamicInputs.forEach(function(el) {
            var grp = el.closest('.form-group');
            if (grp && grp.style.display === 'none') {
                el.removeAttribute('required');
            } else if (grp && grp.style.display !== 'none' && el.hasAttribute('data-required')) {
                el.setAttribute('required', 'required');
            }
        });
    }

    function updateModeLabel() {
        var locationGrp = locationEl ? locationEl.closest('.form-group') : null;
        if (!locationGrp) return;
        var modeRadio = document.querySelector('input[name="meta[event_mode]"]:checked');
        var label = locationGrp.querySelector('label');
        if (modeRadio) {
            if (modeRadio.value === 'Online') {
                label.innerText = 'Platform Link';
                locationEl.placeholder = 'e.g., MS Teams link, Zoom link';
            } else if (modeRadio.value === 'Offline') {
                label.innerText = 'Location / Venue';
                locationEl.placeholder = 'e.g., Block 1 Seminar Hall';
            } else {
                label.innerText = 'Location / Platform Link';
                locationEl.placeholder = 'Enter Location and/or Link';
            }
        }
    }

    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'meta_activity_category') {
            updateForm();
        } else if (e.target && e.target.id === 'meta_event_type') {
            updateForm();
        } else if (e.target && e.target.name === 'meta[event_mode]') {
            updateModeLabel();
        }
    });

    updateForm();
    updateModeLabel();
});
</script>
<?php endif; ?>
<script>
// Repopulate standard fields on validation error
document.addEventListener('DOMContentLoaded', function() {
    var oldInput = <?= json_encode($old_input ?? []) ?>;
    if (!oldInput || Object.keys(oldInput).length === 0) return;

    // Trigger categories first so dynamic form structure builds
    if (oldInput.meta && oldInput.meta.activity_category) {
        var cat = document.getElementById('meta_activity_category');
        if (cat) { cat.value = oldInput.meta.activity_category; cat.dispatchEvent(new Event('change')); }
    }
    if (oldInput.meta && oldInput.meta.event_type) {
        var evt = document.getElementById('meta_event_type');
        if (evt) { evt.value = oldInput.meta.event_type; evt.dispatchEvent(new Event('change')); }
    }

    // Populate basic fields
    if (oldInput.title) { var t = document.getElementById('title'); if(t) t.value = oldInput.title; }
    if (oldInput.mentor_per_no) { var m = document.getElementById('mentor_per_no'); if(m) m.value = oldInput.mentor_per_no; }
    if (oldInput.dept_id) { var d = document.getElementById('dept_id'); if(d) d.value = oldInput.dept_id; }
    if (oldInput.ay_id) { var a = document.getElementById('ay_id'); if(a) a.value = oldInput.ay_id; }

    // Populate remaining meta fields
    if (oldInput.meta) {
        for (var key in oldInput.meta) {
            var val = oldInput.meta[key];
            if (typeof val === 'string' || typeof val === 'number') {
                var el = document.getElementById('meta_' + key);
                if (el) {
                    el.value = val;
                } else {
                    var radios = document.getElementsByName('meta[' + key + ']');
                    for (var i = 0; i < radios.length; i++) {
                        if (radios[i].type === 'radio' && radios[i].value === val) {
                            radios[i].checked = true;
                        }
                    }
                }
            }
        }
    }
    
    // One final update after all meta is populated to fix UI labels
    if (typeof updateModeLabel === 'function') updateModeLabel();
});
</script>
</body>
</html>
