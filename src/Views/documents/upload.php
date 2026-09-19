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
        .breadcrumb { margin-bottom: 1rem; color: #6c757d; }
        .breadcrumb a { color: #4a90d9; text-decoration: none; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="upload-container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/public/index.php">Dashboard</a> &raquo;
        <?php if ($doc_type): ?>
            <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload">Upload Document</a> &raquo;
            <?= htmlspecialchars($doc_type['type_label']) ?>
        <?php else: ?>
            Upload Document
        <?php endif; ?>
    </div>

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
        foreach ($all_types as $t) {
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

            <!-- Common fields -->
            <div class="form-group">
                <label class="required" for="title">Document Title</label>
                <input type="text" id="title" name="title" required
                       placeholder="Enter a descriptive title for this document">
            </div>

            <div class="form-group">
                <label class="required" for="dept_id">Department</label>
                <select id="dept_id" name="dept_id" required>
                    <option value="">— Select Department —</option>
                    <?php foreach ($user_depts as $did => $dname): ?>
                        <option value="<?= $did ?>"<?= count($user_depts) === 1 ? ' selected' : '' ?>>
                            <?= htmlspecialchars($dname) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="year_id">Academic Year</label>
                <select id="year_id" name="year_id">
                    <option value="">— Select Academic Year —</option>
                    <?php foreach ($academic_years as $yr): ?>
                        <option value="<?= $yr['year_id'] ?>"<?= ($active_year && $yr['year_id'] == $active_year['year_id']) ? ' selected' : '' ?>>
                            <?= htmlspecialchars($yr['year_label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Type-specific meta fields -->
            <?php if (!empty($meta_fields)): ?>
            <div class="meta-section">
                <h3><?= htmlspecialchars($doc_type['type_label']) ?> Details</h3>
                <?php foreach ($meta_fields as $field): ?>
                    <div class="form-group">
                        <label class="<?= $field['required'] ? 'required' : '' ?>" for="meta_<?= $field['name'] ?>">
                            <?= htmlspecialchars($field['label']) ?>
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
                        <?php else: ?>
                            <input type="text" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]"
                                   <?= $field['required'] ? 'required' : '' ?>>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- File upload slots -->
            <div class="file-section">
                <h3>File Attachments</h3>
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

            <button type="submit" class="btn-upload">Upload Document</button>
            <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload" style="margin-left: 1rem; color: #6c757d;">← Choose Different Type</a>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
