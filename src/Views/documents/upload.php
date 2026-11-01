<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?> â€” FMS</title>
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
    </style>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="upload-container">
    <h1 id="page_main_heading"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>

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

        <?php foreach ($categories as $cat => $cat_types): ?>
            <h3 style="text-transform: capitalize; margin-top: 1.5rem;"><?= htmlspecialchars($cat) ?></h3>
            <div class="type-selector">
                <?php foreach ($cat_types as $t): ?>
                    <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&amp;type=<?= urlencode($t['type_key']) ?>" class="type-card">
                        <div class="type-label"><?= htmlspecialchars($t['type_label']) ?></div>
                        <div class="type-category"><?= htmlspecialchars($t['category']) ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <!-- Upload form -->
        <?php
        $selected_activity_val = (isset($_GET['activity']) && is_string($_GET['activity'])) ? $_GET['activity'] : ($old_input['meta']['activity_category'] ?? '');
        $is_stepcone_selected = ($type_key === 'student_activity_file' && strcasecmp(trim((string)$selected_activity_val), 'stepcone') === 0);
        ?>
        <form method="POST" enctype="multipart/form-data" id="uploadForm" action="<?= BASE_URL ?>/public/index.php?route=documents/upload">
            <?php if (function_exists('csrfField')) echo csrfField(); ?>
            <input type="hidden" name="type_key" value="<?= htmlspecialchars($type_key) ?>">


            <?php if (count($user_depts) > 1): ?>
                <div class="form-group" <?= ($type_key === 'dept_file' && $preselected_subtype === 'Student Activities Files') ? 'style="display: none;"' : '' ?>>
                    <label class="required" for="dept_id">Department</label>
                    <select id="dept_id" name="dept_id" required>
                        <option value="">â€” Select Department â€”</option>
                        <?php foreach ($user_depts as $did => $dname): ?>
                            <option value="<?= $did ?>" <?= (isset($old_input['dept_id']) && $old_input['dept_id'] == $did) ? 'selected' : '' ?>><?= htmlspecialchars($dname) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <?php 
                $single_dept_id = !empty($user_depts) ? array_key_first($user_depts) : (auth_active_dept_id() ?: 1);
                ?>
                <input type="hidden" name="dept_id" value="<?= (int)$single_dept_id ?>">
            <?php endif; ?>
            <!-- Mentor Per No for Students -->
            <?php if ((auth_active_role()['role_id'] ?? null) == ROLE_STUDENT): ?>
                <div class="form-group">
                    <label class="required" for="mentor_per_no">Mentor's Per No.</label>
                    <input type="text" id="mentor_per_no" name="mentor_per_no" required placeholder="Enter the exact Per No of your mentor" value="<?= htmlspecialchars($old_input['mentor_per_no'] ?? '') ?>">
                    <small style="color: #6c757d;">Your upload will be sent to this mentor for verification.</small>
                </div>
            <?php endif; ?>

            <!-- Base meta fields -->
            <div class="meta-section" style="background: transparent; border: none; padding: 0; margin-bottom: 0; box-shadow: none; <?= ($type_key === 'dept_file' && $preselected_subtype === 'Student Activities Files') ? 'display: none;' : '' ?>">
                
                <?php $has_custom_title = (strpos($type_key, 'student_') === 0 || in_array($type_key, ['exam_qual', 'journal', 'conference', 'patent'], true)); ?>
                <div class="form-group" <?= $has_custom_title ? 'style="display: none;"' : '' ?>>
                    <label class="<?= $has_custom_title ? '' : 'required' ?>" for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($old_input['title'] ?? '') ?>" <?= $has_custom_title ? '' : 'required' ?>>
                </div>

                <div class="form-group">
                    <label for="year_id">Select Academic Year</label>
                    <select id="year_id" name="year_id">
                        <option value="">Select an academic year</option>
                        <?php foreach ($academic_years as $yr): ?>
                            <option value="<?= (int)$yr['year_id'] ?>"<?= ((int)($old_input['year_id'] ?? ($active_year['year_id'] ?? 0)) === (int)$yr['year_id']) ? ' selected' : '' ?>>
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
                    $is_meta_req = $field['required'] && (!$is_stepcone_selected || $field['name'] === 'activity_category');
                    $field_val = $old_input['meta'][$field['name']] ?? '';
                    ?>
                    <div class="form-group" <?= $isHiddenDeptCategory ? 'style="display:none;"' : '' ?>>
                        <?php 
                        $dynamic_label = $field['label'];
                        if ($field['name'] === 'sub_file_type' && $preselected_subtype === 'Student Activities Files') {
                            $dynamic_label = 'Select an Activity';
                        }
                        ?>
                        <label class="<?= $is_meta_req ? 'required' : '' ?>" for="meta_<?= $field['name'] ?>">
                            <?= htmlspecialchars($dynamic_label) ?>
                        </label>
                        <?php if ($field['type'] === 'textarea'): ?>
                            <textarea id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                      <?= $field['required'] ? (($is_meta_req ? 'required ' : '') . 'data-required="true"') : '' ?>><?= htmlspecialchars($field_val) ?></textarea>
                        <?php elseif ($field['type'] === 'date'): ?>
                            <input type="date" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                   value="<?= htmlspecialchars($field_val) ?>"
                                   <?= $field['required'] ? (($is_meta_req ? 'required ' : '') . 'data-required="true"') : '' ?>>
                        <?php elseif ($field['type'] === 'number'): ?>
                            <input type="number" step="any" min="0" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                   value="<?= htmlspecialchars($field_val) ?>"
                                   <?= $field['required'] ? (($is_meta_req ? 'required ' : '') . 'data-required="true"') : '' ?>>
                        <?php elseif ($field['type'] === 'url'): ?>
                            <input type="url" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                   value="<?= htmlspecialchars($field_val) ?>"
                                   <?= $field['required'] ? (($is_meta_req ? 'required ' : '') . 'data-required="true"') : '' ?> placeholder="https://">
                        <?php elseif ($field['type'] === 'select'): ?>
                            <select id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" <?= $field['required'] ? (($is_meta_req ? 'required ' : '') . 'data-required="true"') : '' ?>>
                                <?php if (!isset($field['default'])): ?>
                                    <option value="">â€” Select â€”</option>
                                <?php endif; ?>
                                <?php if (!empty($field['options'])): ?>
                                    <?php foreach ($field['options'] as $opt): ?>
                                        <?php 
                                        $selected = '';
                                        $curr_opt_val = ($field_val !== '') ? $field_val : ($field['name'] === 'activity_category' ? ((isset($_GET['activity']) && is_string($_GET['activity'])) ? $_GET['activity'] : '') : ($field['default'] ?? ''));
                                        if (strcasecmp(trim((string)$curr_opt_val), trim((string)$opt)) === 0) {
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
                                            <input type="radio" name="meta[<?= $field['name'] ?>]" value="<?= htmlspecialchars($opt) ?>" <?= ($field_val === $opt) ? 'checked' : '' ?> <?= $field['required'] ? (($is_meta_req ? 'required ' : '') . 'data-required="true"') : '' ?>>
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
                            <div style="overflow-x: auto; margin-bottom: 0.8rem;">
                                <table class="author-table" style="width: 100%; border-collapse: collapse; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
                                    <thead>
                                        <tr style="background: #f1f5f9; color: #334155; font-size: 0.85rem;">
                                            <th scope="col" style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Name of the <?= $personLabel ?></th>
                                            <th scope="col" style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Affiliation</th>
                                            <th scope="col" style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Position</th>
                                            <th scope="col" style="border: 1px solid #cbd5e1; padding: 8px 10px; width: 40px; text-align: center;"><span class="visually-hidden">Action</span></th>
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
                                            <td style="border: 1px solid #cbd5e1; padding: 0;">
                                                <label for="upload_author_name_<?= $i ?>" class="visually-hidden">Name of <?= htmlspecialchars($personLabel) ?> row <?= $i + 1 ?></label>
                                                <input type="text" id="upload_author_name_<?= $i ?>" name="meta[<?= $field['name'] ?>][name][]" value="<?= htmlspecialchars($oldName) ?>" placeholder="Full Name" aria-label="Name of <?= htmlspecialchars($personLabel) ?> row <?= $i + 1 ?>" style="width:100%; border:none; padding:7px 10px; font-size:0.9rem; outline:none; background:transparent;">
                                            </td>
                                            <td style="border: 1px solid #cbd5e1; padding: 0;">
                                                <label for="upload_author_affil_<?= $i ?>" class="visually-hidden">Affiliation row <?= $i + 1 ?></label>
                                                <input type="text" id="upload_author_affil_<?= $i ?>" name="meta[<?= $field['name'] ?>][affiliation][]" value="<?= htmlspecialchars($oldAffil) ?>" placeholder="e.g. GMRIT" aria-label="Affiliation row <?= $i + 1 ?>" style="width:100%; border:none; padding:7px 10px; font-size:0.9rem; outline:none; background:transparent;">
                                            </td>
                                            <td style="border: 1px solid #cbd5e1; padding: 0;">
                                                <label for="upload_author_pos_<?= $i ?>" class="visually-hidden">Position row <?= $i + 1 ?></label>
                                                <select id="upload_author_pos_<?= $i ?>" name="meta[<?= $field['name'] ?>][position][]" aria-label="Position row <?= $i + 1 ?>" style="width:100%; border:none; padding:7px 10px; font-size:0.9rem; outline:none; background:transparent; cursor:pointer;">
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
                                            <td style="border: 1px solid #cbd5e1; padding: 0; text-align: center;">
                                                <button type="button" onclick="removeAuthorRow(this)" style="background: none; border: none; color: #ef4444; font-weight: bold; cursor: pointer; font-size: 18px; line-height: 1; padding: 6px;" title="Remove Row">&minus;</button>
                                            </td>
                                        </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: #e2e8f0; color: #1e293b; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.2s; margin-bottom: 1.2rem;" onmouseover="this.style.background='#cbd5e1'" onmouseout="this.style.background='#e2e8f0'" onclick="addAuthorRow(this, '<?= $field['name'] ?>')"><i class="fas fa-plus"></i> Add <?= $personLabel ?></button>
                        <?php elseif ($field['type'] === 'dept_category'): ?>
                            <?php if (!empty($preselected_subtype)): ?>
                                <input type="hidden" name="meta[<?= $field['name'] ?>]" value="<?= htmlspecialchars($preselected_subtype) ?>">
                            <?php else: ?>
                                <select id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" <?= $field['required'] ? 'required data-required="true"' : '' ?>>
                                    <option value="">â€” Select Category â€”</option>
                                    <?php
                                    $categories = ['Admin Files', 'Faculty Files', 'Student Related Files', 'Exam Section Files', 'Student Activities Files'];
                                    foreach ($categories as $cat) {
                                        $selected = (!empty($preselected_subtype) && $preselected_subtype === $cat) ? 'selected' : (!empty($old_input['meta'][$field['name']]) && $old_input['meta'][$field['name']] === $cat ? 'selected' : '');
                                        echo '<option value="' . htmlspecialchars($cat) . '" ' . $selected . '>' . htmlspecialchars($cat) . '</option>';
                                    }
                                    ?>
                                </select>
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
                                    $options = ['Grad Talks', 'Expert Talks / Guest Lectures', 'Soft skills', 'Language and communication skills', 'Life skills', 'Professional Societies', 'Clubs', 'IIC', 'Stepcone'];
                                }

                                foreach ($options as $opt) {
                                    $is_sel = (!empty($old_input['meta'][$field['name']]) && $old_input['meta'][$field['name']] === $opt) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($opt) . '" ' . $is_sel . '>' . htmlspecialchars($opt) . '</option>';
                                }
                                ?>
                            </select>
                        <?php else: ?>
                            <?php
                            $default_val = $field_val !== '' ? $field_val : '';
                            $is_readonly = false;
                            if ($default_val === '') {
                                if ($field['name'] === 'activity' && isset($_GET['activity']) && is_string($_GET['activity'])) {
                                    $default_val = $_GET['activity'];
                                    $is_readonly = true;
                                } elseif ($field['name'] === 'exam' && isset($_GET['exam']) && is_string($_GET['exam'])) {
                                    $default_val = $_GET['exam'];
                                    $is_readonly = true;
                                }
                            }
                            ?>
                            <input type="text" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                   value="<?= htmlspecialchars($default_val) ?>"
                                   <?= $field['required'] ? (($is_meta_req ? 'required ' : '') . 'data-required="true"') : '' ?> <?= ($is_readonly) ? 'readonly style="background-color: #e9ecef; cursor: not-allowed; color: #6c757d;"' : '' ?>>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- File upload slots -->
            <div class="file-section" style="background: transparent; border: none; padding: 0; margin-top: 0; box-shadow: none; <?= ($type_key === 'dept_file' && $preselected_subtype === 'Student Activities Files') ? 'display: none;' : '' ?>">
                <?php foreach ($file_slots as $slot): ?>
                    <?php
                    $is_slot_req = $slot['required'] && !$is_stepcone_selected;
                    ?>
                    <div class="form-group">
                        <label class="<?= $is_slot_req ? 'required' : '' ?>" for="file_<?= $slot['name'] ?>">
                            <?= htmlspecialchars($slot['label']) ?>
                        </label>
                        <input type="file" id="file_<?= $slot['name'] ?>" name="<?= $slot['name'] ?>"
                               accept="<?= htmlspecialchars($slot['accept']) ?>"
                               <?= $slot['required'] ? (($is_slot_req ? 'required ' : '') . 'data-required="true"') : '' ?>>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn-upload" <?= ($type_key === 'dept_file' && $preselected_subtype === 'Student Activities Files') ? 'style="display: none;"' : '' ?>>Upload Document</button>
            <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload" style="margin-left: 1rem; color: #6c757d;">â† Choose Different Type</a>
        </form>
    <?php endif; ?>
</div>

<script>
function addAuthorRow(btn, fieldName) {
    var table = (btn.previousElementSibling && btn.previousElementSibling.querySelector('table.author-table'))
        || (btn.parentElement ? btn.parentElement.querySelector('table.author-table') : null)
        || btn.previousElementSibling;
    if (!table) return;
    var tbody = table.getElementsByTagName('tbody')[0] || table;
    var newRow = tbody.insertRow(tbody.rows.length);
    
    var cell1 = newRow.insertCell(0);
    var cell2 = newRow.insertCell(1);
    var cell3 = newRow.insertCell(2);
    var cell4 = newRow.insertCell(3);
    
    cell1.innerHTML = '<input type="text" name="meta[' + fieldName + '][name][]" placeholder="Full Name" style="width:100%; border:none; padding:7px 10px; font-size:0.9rem; outline:none; background:transparent;">';
    cell1.style.border = '1px solid #cbd5e1';
    cell1.style.padding = '0';
    
    cell2.innerHTML = '<input type="text" name="meta[' + fieldName + '][affiliation][]" placeholder="e.g. GMRIT" style="width:100%; border:none; padding:7px 10px; font-size:0.9rem; outline:none; background:transparent;">';
    cell2.style.border = '1px solid #cbd5e1';
    cell2.style.padding = '0';
    
    var selectHtml = '';
    if (fieldName === 'inventors' || fieldName === 'investors') {
        selectHtml = `<select name="meta[` + fieldName + `][position][]" style="width:100%; border:none; padding:7px 10px; font-size:0.9rem; outline:none; background:transparent; cursor:pointer;">
                        <option value="Main Inventor">Main Inventor</option>
                        <option value="Co-Inventor">Co-Inventor</option>
                      </select>`;
    } else {
        selectHtml = `<select name="meta[` + fieldName + `][position][]" style="width:100%; border:none; padding:7px 10px; font-size:0.9rem; outline:none; background:transparent; cursor:pointer;">
                        <option value="First author">First author</option>
                        <option value="First author with equal contribution">First author with equal contribution</option>
                        <option value="Corresponding Author">Corresponding Author</option>
                        <option value="Co-author">Co-author</option>
                      </select>`;
    }
    cell3.innerHTML = selectHtml;
    cell3.style.border = '1px solid #cbd5e1';
    cell3.style.padding = '0';
    
    cell4.innerHTML = '<button type="button" onclick="removeAuthorRow(this)" style="background: none; border: none; color: #ef4444; font-weight: bold; cursor: pointer; font-size: 18px; line-height: 1; padding: 6px;" title="Remove Row">&minus;</button>';
    cell4.style.border = '1px solid #cbd5e1';
    cell4.style.padding = '0';
    cell4.style.textAlign = 'center';
}

function removeAuthorRow(btn) {
    var row = btn.closest('tr');
    if (!row) return;
    var tbody = row.closest('tbody');
    if (tbody && tbody.rows.length > 1) {
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
    
    var intraCollegeCategories = ['Grad Talks', 'Expert Talks / Guest Lectures', 'Soft skills', 'Language and communication skills', 'Life skills', 'Professional Societies', 'Clubs', 'IIC', 'Stepcone'];
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
        <?php if (!empty($_GET['sub_type']) && is_string($_GET['sub_type'])): ?>
        url += '&sub_type=' + encodeURIComponent(<?= json_encode($_GET['sub_type'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>);
        <?php endif; ?>
        window.location.href = url;
    }
}
</script>

<?php if ($type_key === 'student_activity_file'): ?>
<script src="<?= BASE_URL ?>/assets/js/student_activity_upload.js"></script>
<?php endif; ?>
<script>
// Repopulate standard fields on validation error
document.addEventListener('DOMContentLoaded', function() {
    var oldInput = <?= json_encode($old_input ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
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
    if (oldInput.year_id) { var y = document.getElementById('year_id'); if(y) y.value = oldInput.year_id; }
    else if (oldInput.ay_id) { var a = document.getElementById('year_id') || document.getElementById('ay_id'); if(a) a.value = oldInput.ay_id; }

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

    // Instant client-side file size guard (15 MB)
    var MAX_FILE_BYTES = 15 * 1024 * 1024;
    document.querySelectorAll('input[type="file"]').forEach(function(fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                if (this.files[0].size > MAX_FILE_BYTES) {
                    alert('File size exceeds the 15 MB limit (' + (this.files[0].size / (1024 * 1024)).toFixed(2) + ' MB). Please choose a smaller file.');
                    this.value = '';
                }
            }
        });
    });

    // Double submit and file size check on submission
    var uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        var isSubmitting = false;
        uploadForm.addEventListener('submit', function(e) {
            var fileInputs = uploadForm.querySelectorAll('input[type="file"]');
            for (var i = 0; i < fileInputs.length; i++) {
                if (fileInputs[i].files && fileInputs[i].files[0] && fileInputs[i].files[0].size > MAX_FILE_BYTES) {
                    alert('File "' + fileInputs[i].files[0].name + '" exceeds the 15 MB limit. Please select a smaller file.');
                    e.preventDefault();
                    return false;
                }
            }
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            var btn = uploadForm.querySelector('button[type="submit"]');
            if (btn) {
                btn.style.opacity = '0.7';
                btn.style.pointerEvents = 'none';
            }
            isSubmitting = true;
        });
    }
});
</script>
</body>
</html>
