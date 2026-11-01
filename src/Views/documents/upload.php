<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?> - FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        .upload-wrapper {
            max-width: 980px;
            margin: 2rem auto 4rem auto;
            padding: 0 1.25rem;
        }
        .page-nav-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .breadcrumb-trail {
            font-size: 0.875rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .breadcrumb-trail a {
            color: #0284c7;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .breadcrumb-trail a:hover {
            color: #0369a1;
            text-decoration: underline;
        }
        .breadcrumb-trail .sep {
            color: #94a3b8;
            font-size: 0.75rem;
        }
        .breadcrumb-trail .current {
            color: #1e293b;
            font-weight: 600;
        }
        .btn-nav-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 0.4rem 0.85rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .btn-nav-back:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .upload-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 2px 6px -1px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }
        .upload-card-header {
            background: linear-gradient(135deg, #003366 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .header-title-box h1 {
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0 0 0.25rem 0;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .header-title-box p {
            font-size: 0.875rem;
            color: #bfdbfe;
            margin: 0;
        }
        .doc-badge {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .upload-card-body {
            padding: 2rem 2.25rem;
        }

        /* Alerts */
        .alert {
            padding: 0.9rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.925rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* 2-Column Responsive Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.25rem 1.5rem;
        }
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 1.1rem;
            }
            .upload-card-body {
                padding: 1.5rem;
            }
            .upload-card-header {
                padding: 1.25rem 1.5rem;
            }
        }
        .col-span-full {
            grid-column: 1 / -1;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.4rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .form-group label.required::after,
        .form-group .required::after {
            content: ' *';
            color: #ef4444;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="url"],
        .form-group input[type="date"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 0.925rem;
            color: #1e293b;
            background-color: #ffffff;
            box-sizing: border-box;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            font-family: inherit;
        }
        .form-group select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.2em 1.2em;
            padding-right: 2.25rem;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
        }
        .form-group textarea {
            min-height: 90px;
            resize: vertical;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.18);
        }

        /* File Upload Box */
        .file-upload-card {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 1.25rem;
            transition: all 0.2s ease;
            margin-top: 0.25rem;
        }
        .file-upload-card:hover {
            border-color: #3b82f6;
            background: #f0f7ff;
        }
        .file-upload-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        .file-upload-header i {
            color: #0284c7;
            font-size: 1.1rem;
        }
        .file-upload-card input[type="file"] {
            display: block;
            width: 100%;
            font-size: 0.875rem;
            color: #475569;
            cursor: pointer;
        }
        .file-hint {
            font-size: 0.775rem;
            color: #64748b;
            margin-top: 0.35rem;
        }

        /* Author Table */
        .author-table-container {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
            background: #ffffff;
            margin-bottom: 0.75rem;
        }
        .author-table {
            width: 100%;
            border-collapse: collapse;
        }
        .author-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.825rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 0.65rem 0.85rem;
            border-bottom: 1px solid #cbd5e1;
            text-align: left;
        }
        .author-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 0;
        }
        .author-table tr:last-child td {
            border-bottom: none;
        }
        .btn-add-author {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.45rem 1rem;
            background: #f1f5f9;
            color: #1e293b;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-add-author:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* Action Buttons */
        .form-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1.75rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }
        .btn-upload {
            background: #16a34a;
            color: #ffffff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.15s ease;
            box-shadow: 0 2px 4px rgba(22, 163, 74, 0.2);
        }
        .btn-upload:hover {
            background: #15803d;
            box-shadow: 0 4px 8px rgba(22, 163, 74, 0.3);
            transform: translateY(-1px);
        }
        .btn-choose-diff {
            color: #475569;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.75rem 1.25rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            transition: all 0.15s ease;
        }
        .btn-choose-diff:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #94a3b8;
        }

        /* Type Selector Grid */
        .type-selector {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1rem;
            margin: 1.25rem 0 2rem 0;
        }
        .type-card {
            background: #ffffff;
            padding: 1.25rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            text-decoration: none;
            color: #1e293b;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 90px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .type-card:hover {
            border-color: #0284c7;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -2px rgba(2, 132, 199, 0.12);
        }
        .type-card .type-label {
            font-weight: 600;
            font-size: 1rem;
            color: #0f172a;
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .type-card .type-category {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }
        .category-heading {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            text-transform: capitalize;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 0.4rem;
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="upload-wrapper">
    <div class="page-nav-bar">
        <div class="breadcrumb-trail">
            <a href="<?= BASE_URL ?>/public/index.php?route=dashboard"><i class="fas fa-home"></i> Home</a>
            <span class="sep">&rsaquo;</span>
            <a href="<?= BASE_URL ?>/public/index.php?route=documents/my_uploads">My Uploads</a>
            <span class="sep">&rsaquo;</span>
            <span class="current"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <a href="<?= BASE_URL ?>/public/index.php?route=documents/my_uploads" class="btn-nav-back">
            <i class="fas fa-arrow-left"></i> Back to My Uploads
        </a>
    </div>

    <div class="upload-card">
        <div class="upload-card-header">
            <div class="header-title-box">
                <h1 id="page_main_heading">
                    <i class="fas fa-cloud-arrow-up"></i>
                    <?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?>
                </h1>
                <p>Provide verified metadata details and upload supporting document attachments.</p>
            </div>
            <?php if ($doc_type): ?>
                <span class="doc-badge"><?= htmlspecialchars($doc_type['category'] ?? 'Document') ?></span>
            <?php endif; ?>
        </div>

        <div class="upload-card-body">
            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars($success_msg) ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?= htmlspecialchars($error_msg) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!$doc_type): ?>
                <!-- Type selector -->
                <p style="color: #475569; font-size: 0.95rem; margin-top: 0;">Select the category and specific type of document you want to upload:</p>

                <?php
                $categories = [];
                foreach ($types as $t) {
                    $categories[$t['category']][] = $t;
                }
                ?>

                <?php foreach ($categories as $cat => $cat_types): ?>
                    <div class="category-heading">
                        <i class="fas fa-folder-open" style="color: #0284c7;"></i>
                        <?= htmlspecialchars($cat) ?>
                    </div>
                    <div class="type-selector">
                        <?php foreach ($cat_types as $t): ?>
                            <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&amp;type=<?= urlencode($t['type_key']) ?>" class="type-card">
                                <div class="type-label">
                                    <i class="far fa-file-alt" style="color: #64748b;"></i>
                                    <?= htmlspecialchars($t['type_label']) ?>
                                </div>
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

                    <div class="form-grid">
                        <?php if (count($user_depts) > 1): ?>
                            <div class="form-group" <?= ($type_key === 'dept_file' && $preselected_subtype === 'Student Activities Files') ? 'style="display: none;"' : '' ?>>
                                <label class="required" for="dept_id">Department</label>
                                <select id="dept_id" name="dept_id" required>
                                    <option value="">-- Select Department --</option>
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
                            <div class="form-group col-span-full">
                                <label class="required" for="mentor_per_no">Mentor's Per No.</label>
                                <input type="text" id="mentor_per_no" name="mentor_per_no" required placeholder="Enter the exact Per No of your mentor" value="<?= htmlspecialchars($old_input['mentor_per_no'] ?? '') ?>">
                                <small style="color: #64748b; margin-top: 4px;">Your upload will be routed to this mentor for verification and approval.</small>
                            </div>
                        <?php endif; ?>

                        <!-- Base meta fields -->
                        <?php $has_custom_title = (strpos($type_key, 'student_') === 0 || in_array($type_key, ['exam_qual', 'journal', 'conference', 'patent'], true)); ?>
                        <div class="form-group col-span-full" <?= $has_custom_title ? 'style="display: none;"' : '' ?>>
                            <label class="<?= $has_custom_title ? '' : 'required' ?>" for="title">Document Title</label>
                            <input type="text" id="title" name="title" placeholder="Enter title" value="<?= htmlspecialchars($old_input['title'] ?? '') ?>" <?= $has_custom_title ? '' : 'required' ?>>
                        </div>

                        <div class="form-group" <?= ($type_key === 'dept_file' && $preselected_subtype === 'Student Activities Files') ? 'style="display: none;"' : '' ?>>
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

                        <!-- Type-specific meta fields -->
                        <?php if (!empty($meta_fields)): ?>
                            <?php foreach ($meta_fields as $field): ?>
                                <?php 
                                $isHiddenDeptCategory = ($field['type'] === 'dept_category' && !empty($preselected_subtype)); 
                                $is_meta_req = $field['required'] && (!$is_stepcone_selected || $field['name'] === 'activity_category');
                                $field_val = $old_input['meta'][$field['name']] ?? '';
                                $is_full_width = in_array($field['type'], ['textarea', 'author_table'], true) 
                                    || in_array($field['name'], ['paper_title', 'conference_name', 'patent_title', 'description', 'event_name', 'scheme_name'], true);
                                ?>
                                <div class="form-group <?= $is_full_width ? 'col-span-full' : '' ?>" <?= $isHiddenDeptCategory ? 'style="display:none;"' : '' ?>>
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
                                                  <?= $field['required'] ? (($is_meta_req ? 'required ' : '') . 'data-required="true"') : '' ?> placeholder="Enter <?= htmlspecialchars(strtolower($dynamic_label)) ?>"><?= htmlspecialchars($field_val) ?></textarea>
                                    <?php elseif ($field['type'] === 'date'): ?>
                                        <input type="date" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                               value="<?= htmlspecialchars($field_val) ?>"
                                               <?= $field['required'] ? (($is_meta_req ? 'required ' : '') . 'data-required="true"') : '' ?>>
                                    <?php elseif ($field['type'] === 'number'): ?>
                                        <input type="number" step="any" min="0" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                               value="<?= htmlspecialchars($field_val) ?>"
                                               <?= $field['required'] ? (($is_meta_req ? 'required ' : '') . 'data-required="true"') : '' ?> placeholder="0">
                                    <?php elseif ($field['type'] === 'url'): ?>
                                        <input type="url" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                               value="<?= htmlspecialchars($field_val) ?>"
                                               <?= $field['required'] ? (($is_meta_req ? 'required ' : '') . 'data-required="true"') : '' ?> placeholder="https://">
                                    <?php elseif ($field['type'] === 'select'): ?>
                                        <select id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" <?= $field['required'] ? (($is_meta_req ? 'required ' : '') . 'data-required="true"') : '' ?>>
                                            <?php if (!isset($field['default'])): ?>
                                                <option value="">-- Select --</option>
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
                                        <div class="radio-group" style="display: flex; gap: 18px; margin-top: 6px;">
                                            <?php if (!empty($field['options'])): ?>
                                                <?php foreach ($field['options'] as $opt): ?>
                                                    <label style="font-weight: 500; margin: 0; display: flex; align-items: center; gap: 6px; cursor: pointer;">
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
                                        <div class="author-table-container">
                                            <div style="overflow-x: auto;">
                                                <table class="author-table">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col" style="padding: 9px 12px;">Name of the <?= $personLabel ?></th>
                                                            <th scope="col" style="padding: 9px 12px;">Affiliation</th>
                                                            <th scope="col" style="padding: 9px 12px;">Position</th>
                                                            <th scope="col" style="padding: 9px 12px; width: 44px; text-align: center;"><span class="visually-hidden">Action</span></th>
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
                                                            <td style="border: 1px solid #e2e8f0; padding: 0;">
                                                                <label for="upload_author_name_<?= $i ?>" class="visually-hidden">Name of <?= htmlspecialchars($personLabel) ?> row <?= $i + 1 ?></label>
                                                                <input type="text" id="upload_author_name_<?= $i ?>" name="meta[<?= $field['name'] ?>][name][]" value="<?= htmlspecialchars($oldName) ?>" placeholder="Full Name" aria-label="Name of <?= htmlspecialchars($personLabel) ?> row <?= $i + 1 ?>" style="width:100%; border:none; padding:8px 12px; font-size:0.9rem; outline:none; background:transparent;">
                                                            </td>
                                                            <td style="border: 1px solid #e2e8f0; padding: 0;">
                                                                <label for="upload_author_affil_<?= $i ?>" class="visually-hidden">Affiliation row <?= $i + 1 ?></label>
                                                                <input type="text" id="upload_author_affil_<?= $i ?>" name="meta[<?= $field['name'] ?>][affiliation][]" value="<?= htmlspecialchars($oldAffil) ?>" placeholder="e.g. GMRIT" aria-label="Affiliation row <?= $i + 1 ?>" style="width:100%; border:none; padding:8px 12px; font-size:0.9rem; outline:none; background:transparent;">
                                                            </td>
                                                            <td style="border: 1px solid #e2e8f0; padding: 0;">
                                                                <label for="upload_author_pos_<?= $i ?>" class="visually-hidden">Position row <?= $i + 1 ?></label>
                                                                <select id="upload_author_pos_<?= $i ?>" name="meta[<?= $field['name'] ?>][position][]" aria-label="Position row <?= $i + 1 ?>" style="width:100%; border:none; padding:8px 12px; font-size:0.9rem; outline:none; background:transparent; cursor:pointer;">
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
                                                            <td style="border: 1px solid #e2e8f0; padding: 0; text-align: center;">
                                                                <button type="button" onclick="removeAuthorRow(this)" style="background: none; border: none; color: #ef4444; font-weight: bold; cursor: pointer; font-size: 20px; line-height: 1; padding: 6px;" title="Remove Row">&minus;</button>
                                                            </td>
                                                        </tr>
                                                        <?php endfor; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn-add-author" onclick="addAuthorRow(this, '<?= $field['name'] ?>')">
                                                <i class="fas fa-plus"></i> Add <?= $personLabel ?>
                                            </button>
                                        </div>
                                    <?php elseif ($field['type'] === 'dept_category'): ?>
                                        <?php if (!empty($preselected_subtype)): ?>
                                            <input type="hidden" name="meta[<?= $field['name'] ?>]" value="<?= htmlspecialchars($preselected_subtype) ?>">
                                        <?php else: ?>
                                            <select id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" <?= $field['required'] ? 'required data-required="true"' : '' ?>>
                                                <option value="">-- Select Category --</option>
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
                                               <?= $field['required'] ? (($is_meta_req ? 'required ' : '') . 'data-required="true"') : '' ?> <?= ($is_readonly) ? 'readonly style="background-color: #f1f5f9; cursor: not-allowed; color: #64748b;"' : '' ?> placeholder="Enter <?= htmlspecialchars(strtolower($dynamic_label)) ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- File upload slots -->
                        <div class="col-span-full" style="<?= ($type_key === 'dept_file' && $preselected_subtype === 'Student Activities Files') ? 'display: none;' : '' ?>">
                            <?php foreach ($file_slots as $slot): ?>
                                <?php
                                $is_slot_req = $slot['required'] && !$is_stepcone_selected;
                                ?>
                                <div class="form-group" style="margin-bottom: 1rem;">
                                    <label class="<?= $is_slot_req ? 'required' : '' ?>" for="file_<?= $slot['name'] ?>">
                                        <?= htmlspecialchars($slot['label']) ?>
                                    </label>
                                    <div class="file-upload-card">
                                        <div class="file-upload-header">
                                            <i class="fas fa-file-pdf"></i>
                                            <span>Select file to attach</span>
                                        </div>
                                        <input type="file" id="file_<?= $slot['name'] ?>" name="<?= $slot['name'] ?>"
                                               accept="<?= htmlspecialchars($slot['accept']) ?>"
                                               <?= $slot['required'] ? (($is_slot_req ? 'required ' : '') . 'data-required="true"') : '' ?>>
                                        <div class="file-hint">Accepted formats: <?= htmlspecialchars($slot['accept']) ?> (Maximum file size: 15 MB)</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Action buttons -->
                        <div class="form-actions col-span-full">
                            <button type="submit" class="btn-upload" <?= ($type_key === 'dept_file' && $preselected_subtype === 'Student Activities Files') ? 'style="display: none;"' : '' ?>>
                                <i class="fas fa-cloud-arrow-up"></i> Upload Document
                            </button>
                            <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload" class="btn-choose-diff">
                                <i class="fas fa-arrow-left"></i> Choose Different Type
                            </a>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
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
    
    cell1.innerHTML = '<input type="text" name="meta[' + fieldName + '][name][]" placeholder="Full Name" style="width:100%; border:none; padding:8px 12px; font-size:0.9rem; outline:none; background:transparent;">';
    cell1.style.border = '1px solid #e2e8f0';
    cell1.style.padding = '0';
    
    cell2.innerHTML = '<input type="text" name="meta[' + fieldName + '][affiliation][]" placeholder="e.g. GMRIT" style="width:100%; border:none; padding:8px 12px; font-size:0.9rem; outline:none; background:transparent;">';
    cell2.style.border = '1px solid #e2e8f0';
    cell2.style.padding = '0';
    
    var selectHtml = '';
    if (fieldName === 'inventors' || fieldName === 'investors') {
        selectHtml = `<select name="meta[` + fieldName + `][position][]" style="width:100%; border:none; padding:8px 12px; font-size:0.9rem; outline:none; background:transparent; cursor:pointer;">
                        <option value="Main Inventor">Main Inventor</option>
                        <option value="Co-Inventor">Co-Inventor</option>
                      </select>`;
    } else {
        selectHtml = `<select name="meta[` + fieldName + `][position][]" style="width:100%; border:none; padding:8px 12px; font-size:0.9rem; outline:none; background:transparent; cursor:pointer;">
                        <option value="First author">First author</option>
                        <option value="First author with equal contribution">First author with equal contribution</option>
                        <option value="Corresponding Author">Corresponding Author</option>
                        <option value="Co-author">Co-author</option>
                      </select>`;
    }
    cell3.innerHTML = selectHtml;
    cell3.style.border = '1px solid #e2e8f0';
    cell3.style.padding = '0';
    
    cell4.innerHTML = '<button type="button" onclick="removeAuthorRow(this)" style="background: none; border: none; color: #ef4444; font-weight: bold; cursor: pointer; font-size: 20px; line-height: 1; padding: 6px;" title="Remove Row">&minus;</button>';
    cell4.style.border = '1px solid #e2e8f0';
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
