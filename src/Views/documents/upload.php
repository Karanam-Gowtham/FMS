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
            <?= htmlspecialchars(isset($_GET['activity']) ? $_GET['activity'] : (isset($_GET['exam']) ? $_GET['exam'] : $preselected_subtype)) ?>
        <?php else: ?>
            <?= htmlspecialchars(isset($_GET['activity']) ? $_GET['activity'] : (isset($_GET['exam']) ? $_GET['exam'] : $doc_type['type_label'])) ?>
        <?php endif; ?>
    <?php else: ?>
        Upload Document
    <?php endif; ?>
</div>

<div class="upload-container">
    <h1><?= $page_title ?></h1>

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
            <!-- Base meta fields -->
            <div class="meta-section" style="background: transparent; border: none; padding: 0; margin-bottom: 0; box-shadow: none; <?= ($preselected_subtype === 'Student Activities Files') ? 'display: none;' : '' ?>">
                
                <?php $is_student_activity = (strpos($type_key, 'student_') === 0 || $type_key === 'exam_qual'); ?>
                <div class="form-group" <?= $is_student_activity ? 'style="display: none;"' : '' ?>>
                    <label class="<?= $is_student_activity ? '' : 'required' ?>" for="title">Title</label>
                    <input type="text" id="title" name="title" <?= $is_student_activity ? '' : 'required' ?>>
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
                                      <?= $field['required'] ? 'required' : '' ?>></textarea>
                        <?php elseif ($field['type'] === 'date'): ?>
                            <input type="date" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                   <?= $field['required'] ? 'required' : '' ?>>
                        <?php elseif ($field['type'] === 'number'): ?>
                            <input type="number" step="any" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                   <?= $field['required'] ? 'required' : '' ?>>
                        <?php elseif ($field['type'] === 'url'): ?>
                            <input type="url" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                   <?= $field['required'] ? 'required' : '' ?> placeholder="https://">
                        <?php elseif ($field['type'] === 'select'): ?>
                            <select id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" <?= $field['required'] ? 'required' : '' ?>>
                                <option value="">— Select —</option>
                                <?php if (!empty($field['options'])): ?>
                                    <?php foreach ($field['options'] as $opt): ?>
                                        <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
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
                                    <tr>
                                        <td style="border: 1px solid #ccc; padding: 5px;"><input type="text" name="meta[<?= $field['name'] ?>][name][]" style="width:100%; border:none; padding:5px;"></td>
                                        <td style="border: 1px solid #ccc; padding: 5px;"><input type="text" name="meta[<?= $field['name'] ?>][affiliation][]" style="width:100%; border:none; padding:5px;"></td>
                                        <td style="border: 1px solid #ccc; padding: 5px;">
                                            <select name="meta[<?= $field['name'] ?>][position][]" style="width:100%; border:none; padding:5px;">
                                                <?php if ($isInventor): ?>
                                                    <option value="Main Inventor">Main Inventor</option>
                                                    <option value="Co-Inventor">Co-Inventor</option>
                                                <?php else: ?>
                                                    <option value="First author">First author</option>
                                                    <option value="First author with equal contribution">First author with equal contribution</option>
                                                    <option value="Corresponding Author">Corresponding Author</option>
                                                    <option value="Co-author">Co-author</option>
                                                <?php endif; ?>
                                            </select>
                                        </td>
                                        <td style="border: 1px solid #ccc; padding: 5px; text-align: center;">
                                            <button type="button" onclick="removeAuthorRow(this)" style="background: none; border: none; color: #ef4444; font-weight: bold; cursor: pointer; font-size: 20px; line-height: 1;" title="Remove Row">&minus;</button>
                                        </td>
                                    </tr>
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
                                    <select id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" <?= $field['required'] ? 'required' : '' ?>>
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
                            <select id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" <?= $field['required'] ? 'required' : '' ?> onchange="handleStudentActivityChange(this)">
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
                                    $options = ['Journal Papers', 'Conference Papers', 'Projects', 'Internships', 'SIH', 'GATE', 'Hackathons', 'Professional Bodies'];
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
                                   <?= $field['required'] ? 'required' : '' ?> <?= ($default_val !== '') ? 'readonly style="background-color: #e9ecef; cursor: not-allowed; color: #6c757d;"' : '' ?>>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- File upload slots -->
            <div class="file-section" style="background: transparent; border: none; padding: 0; margin-top: 0; box-shadow: none; <?= ($preselected_subtype === 'Student Activities Files') ? 'display: none;' : '' ?>">
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

            <button type="submit" class="btn-upload" <?= ($preselected_subtype === 'Student Activities Files') ? 'style="display: none;"' : '' ?>>Upload Document</button>
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
    
    if (val === 'Journal Papers') {
        type = 'student_journal';
    } else if (val === 'Conference Papers') {
        type = 'student_conference';
    } else if (val === 'Professional Bodies') {
        type = 'student_body';
    } else if (val === 'GATE') {
        type = 'exam_qual';
        subtypeParam = '&exam=' + encodeURIComponent(val);
    } else {
        type = 'student_event';
        subtypeParam = '&activity=' + encodeURIComponent(val);
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
</body>
</html>
