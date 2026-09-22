<?php
require_login();
$active_role = $_SESSION['active_role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Document - <?= htmlspecialchars($document['type_label']) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
    <style>
        .upload-container { max-width: 800px; margin: 2rem auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-title { color: #1e293b; margin-bottom: 1.5rem; text-align: center; font-size: 1.5rem; font-weight: 600; }
        .meta-section, .file-section { background: #f8fafc; padding: 1.5rem; border-radius: 6px; margin-bottom: 1.5rem; border: 1px solid #e2e8f0; }
        .section-title { font-size: 1.1rem; color: #475569; margin-bottom: 1rem; border-bottom: 2px solid #cbd5e1; padding-bottom: 0.5rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-weight: 500; color: #334155; margin-bottom: 0.5rem; }
        .form-group label.required::after { content: " *"; color: #ef4444; }
        .form-group input, .form-group select, .form-group textarea { padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.95rem; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.2); }
        .full-width { grid-column: 1 / -1; }
        .btn-update { background: #eab308; color: white; border: none; padding: 0.75rem 1.5rem; font-size: 1rem; font-weight: 600; border-radius: 4px; cursor: pointer; width: 100%; transition: background 0.2s; }
        .btn-update:hover { background: #ca8a04; }
        .alert { padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .existing-file { font-size: 0.85rem; color: #15803d; margin-top: 0.25rem; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../components/navbar.php'; ?>

<div class="upload-container">
    <div class="form-title">Edit Document: <?= htmlspecialchars($document['type_label']) ?></div>
    
    <?php if(!empty($success_msg)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if(!empty($error_msg)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/public/index.php?route=documents/process_update" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="doc_id" value="<?= (int)$document['doc_id'] ?>">

        <div class="meta-section">
            <div class="section-title">General Information</div>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="required" for="title">Document Title / Topic</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($document['title']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="required" for="academic_year_id">Academic Year</label>
                    <select id="academic_year_id" name="academic_year_id" required>
                        <?php foreach($academic_years as $ay): ?>
                            <option value="<?= $ay['year_id'] ?>" <?= ($ay['year_id'] == $document['academic_year_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ay['year_label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="required" for="dept_id">Department</label>
                    <select id="dept_id" name="dept_id" required>
                        <?php foreach($user_depts as $id => $name): ?>
                            <option value="<?= $id ?>" <?= ($id == $document['dept_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <?php if (!empty($meta_fields)): ?>
        <div class="meta-section">
            <div class="section-title">Additional Details</div>
            <div class="form-grid">
                <?php foreach ($meta_fields as $field): ?>
                    <?php 
                    $val = $meta_data[$field['name']] ?? '';
                    // For array fields (like authors), we need special handling, but for now just encode to json if it's an array
                    if (is_array($val)) {
                        // Not handling complex authors list perfectly here in edit, just a basic fallback
                        $val = "Multiple Authors (Edit disabled in basic mode)";
                    }
                    ?>
                    <div class="form-group <?= ($field['type'] === 'text' || $field['name'] === 'title' || $field['type'] === 'authors') ? 'full-width' : '' ?>">
                        <label class="<?= $field['required'] ? 'required' : '' ?>" for="meta_<?= $field['name'] ?>">
                            <?= htmlspecialchars($field['label']) ?>
                        </label>
                        <?php if ($field['type'] === 'date'): ?>
                            <input type="date" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" value="<?= htmlspecialchars($val) ?>" <?= $field['required'] ? 'required' : '' ?>>
                        <?php elseif ($field['type'] === 'number'): ?>
                            <input type="number" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" value="<?= htmlspecialchars($val) ?>" <?= $field['required'] ? 'required' : '' ?>>
                        <?php else: ?>
                            <input type="text" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" value="<?= htmlspecialchars($val) ?>" <?= $field['required'] ? 'required' : '' ?> <?= is_array($meta_data[$field['name']] ?? null) ? 'readonly' : '' ?>>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="file-section">
            <div class="section-title">Files</div>
            <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 1rem;">Upload a new file ONLY if you want to replace the existing one.</p>
            <?php foreach ($file_slots as $slot): ?>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="file_<?= $slot['name'] ?>">
                        <?= htmlspecialchars($slot['label']) ?>
                    </label>
                    <input type="file" id="file_<?= $slot['name'] ?>" name="<?= $slot['name'] ?>" accept="<?= htmlspecialchars($slot['accept']) ?>">
                    
                    <?php if (isset($existing_files_map[$slot['name']])): ?>
                        <div class="existing-file">Current File: <strong><?= htmlspecialchars($existing_files_map[$slot['name']]['original_name']) ?></strong></div>
                    <?php else: ?>
                        <div class="existing-file" style="color: #ef4444;">No file uploaded previously.</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn-update">Update Document</button>
        <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&id=<?= $document['doc_id'] ?>" style="display:block; text-align:center; margin-top: 1rem; color: #6c757d;">Cancel</a>
    </form>
</div>
</body>
</html>
