<?php
require_login();
$active_role = auth_active_role();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Document &mdash; <?= htmlspecialchars($document['type_label'] ?? 'Document') ?> &mdash; FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
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
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div class="upload-container">
    <div class="form-title">Edit Document: <?= htmlspecialchars($document['type_label'] ?? 'Document') ?></div>
    
    <?php if(!empty($success_msg)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if(!empty($error_msg)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/public/index.php?route=documents/process_update" method="POST" enctype="multipart/form-data">
        <?php if (function_exists('csrfField')) echo csrfField(); ?>
        <input type="hidden" name="doc_id" value="<?= (int)$document['doc_id'] ?>">

        <div class="meta-section">
            <div class="section-title">General Information</div>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="required" for="title">Document Title / Topic</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($document['title'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="required" for="academic_year_id">Academic Year</label>
                    <select id="academic_year_id" name="academic_year_id" required>
                        <?php foreach($academic_years as $ay): ?>
                            <option value="<?= (int)$ay['year_id'] ?>" <?= ((int)$ay['year_id'] === (int)($document['academic_year_id'] ?? 0)) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ay['year_label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="required" for="dept_id">Department</label>
                    <select id="dept_id" name="dept_id" required>
                        <?php foreach($user_depts as $id => $name): ?>
                            <option value="<?= (int)$id ?>" <?= ((int)$id === (int)($document['dept_id'] ?? 0)) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <?php if (!empty($meta_fields)): ?>
        <?php
        $is_stepcone_doc = ($type_key === 'student_activity_file' && strcasecmp(trim($meta_data['activity_category'] ?? ''), 'stepcone') === 0);
        ?>
        <div class="meta-section">
            <div class="section-title">Additional Details</div>
            <div class="form-grid">
                <?php foreach ($meta_fields as $field): ?>
                    <?php 
                    if ($field['type'] === 'json' || $field['type'] === 'authors') {
                        continue;
                    }
                    $val = $meta_data[$field['name']] ?? '';
                    if ($field['type'] !== 'author_table' && is_array($val)) {
                        continue;
                    }
                    $is_f_req = $field['required'] && (!$is_stepcone_doc || $field['name'] === 'activity_category');
                    $is_full_width = ($field['type'] === 'text' || $field['type'] === 'textarea' || $field['type'] === 'author_table' || $field['name'] === 'title');
                    ?>
                    <div class="form-group <?= $is_full_width ? 'full-width' : '' ?>">
                        <label class="<?= $is_f_req ? 'required' : '' ?>" for="meta_<?= $field['name'] ?>">
                            <?= htmlspecialchars($field['label']) ?>
                        </label>
                        <?php if ($field['type'] === 'textarea'): ?>
                            <textarea id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" <?= $is_f_req ? 'required' : '' ?> style="width: 100%; min-height: 80px;"><?= htmlspecialchars($val) ?></textarea>
                        <?php elseif ($field['type'] === 'date'): ?>
                            <input type="date" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" value="<?= htmlspecialchars($val) ?>" <?= $is_f_req ? 'required' : '' ?>>
                        <?php elseif ($field['type'] === 'number'): ?>
                            <input type="number" step="any" min="0" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" value="<?= htmlspecialchars($val) ?>" <?= $is_f_req ? 'required' : '' ?>>
                        <?php elseif ($field['type'] === 'url'): ?>
                            <input type="url" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" value="<?= htmlspecialchars($val) ?>" <?= $is_f_req ? 'required' : '' ?> placeholder="https://">
                        <?php elseif ($field['type'] === 'select' && !empty($field['options'])): ?>
                            <select id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" <?= $is_f_req ? 'required' : '' ?>>
                                <option value="">— Select —</option>
                                <?php foreach ($field['options'] as $opt): ?>
                                    <option value="<?= htmlspecialchars($opt) ?>" <?= ((string)$val === (string)$opt) ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ($field['type'] === 'radio'): ?>
                            <div class="radio-group" style="display: flex; gap: 15px; margin-top: 5px;">
                                <?php if (!empty($field['options'])): ?>
                                    <?php foreach ($field['options'] as $opt): ?>
                                        <label style="font-weight: normal; margin: 0; display: flex; align-items: center; gap: 5px;">
                                            <input type="radio" name="meta[<?= $field['name'] ?>]" value="<?= htmlspecialchars($opt) ?>" <?= ((string)$val === (string)$opt) ? 'checked' : '' ?> <?= $is_f_req ? 'required' : '' ?>>
                                            <?= htmlspecialchars($opt) ?>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($field['type'] === 'author_table'): ?>
                            <?php 
                                $isInventor = ($field['name'] === 'inventors' || $field['name'] === 'investors');
                                $personLabel = $isInventor ? 'Inventor' : 'Author';
                                $rawAuthors = $meta_data[$field['name']] ?? null;
                                if (is_string($rawAuthors)) {
                                    $rawAuthors = json_decode($rawAuthors, true) ?: [];
                                }
                                $parsedNames = [];
                                $parsedAffils = [];
                                $parsedPositions = [];
                                if (is_array($rawAuthors)) {
                                    if (isset($rawAuthors['name']) && is_array($rawAuthors['name'])) {
                                        $parsedNames = $rawAuthors['name'];
                                        $parsedAffils = $rawAuthors['affiliation'] ?? [];
                                        $parsedPositions = $rawAuthors['position'] ?? [];
                                    } else {
                                        foreach ($rawAuthors as $aItem) {
                                            if (is_array($aItem)) {
                                                $parsedNames[] = $aItem['name'] ?? '';
                                                $parsedAffils[] = $aItem['affiliation'] ?? '';
                                                $parsedPositions[] = $aItem['position'] ?? '';
                                            }
                                        }
                                    }
                                }
                                $authorCount = max(1, count($parsedNames));
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
                                        <?php for ($i = 0; $i < $authorCount; $i++): 
                                            $curName = $parsedNames[$i] ?? '';
                                            $curAffil = $parsedAffils[$i] ?? '';
                                            $curPos = $parsedPositions[$i] ?? '';
                                        ?>
                                        <tr>
                                            <td style="border: 1px solid #cbd5e1; padding: 0;">
                                                <label for="edit_author_name_<?= $i ?>" class="visually-hidden">Name of <?= htmlspecialchars($personLabel) ?> row <?= $i + 1 ?></label>
                                                <input type="text" id="edit_author_name_<?= $i ?>" name="meta[<?= $field['name'] ?>][name][]" value="<?= htmlspecialchars($curName) ?>" placeholder="Full Name" aria-label="Name of <?= htmlspecialchars($personLabel) ?> row <?= $i + 1 ?>" style="width:100%; border:none; padding:7px 10px; font-size:0.9rem; outline:none; background:transparent;">
                                            </td>
                                            <td style="border: 1px solid #cbd5e1; padding: 0;">
                                                <label for="edit_author_affil_<?= $i ?>" class="visually-hidden">Affiliation row <?= $i + 1 ?></label>
                                                <input type="text" id="edit_author_affil_<?= $i ?>" name="meta[<?= $field['name'] ?>][affiliation][]" value="<?= htmlspecialchars($curAffil) ?>" placeholder="e.g. GMRIT" aria-label="Affiliation row <?= $i + 1 ?>" style="width:100%; border:none; padding:7px 10px; font-size:0.9rem; outline:none; background:transparent;">
                                            </td>
                                            <td style="border: 1px solid #cbd5e1; padding: 0;">
                                                <label for="edit_author_pos_<?= $i ?>" class="visually-hidden">Position row <?= $i + 1 ?></label>
                                                <select id="edit_author_pos_<?= $i ?>" name="meta[<?= $field['name'] ?>][position][]" aria-label="Position row <?= $i + 1 ?>" style="width:100%; border:none; padding:7px 10px; font-size:0.9rem; outline:none; background:transparent; cursor:pointer;">
                                                    <?php if ($isInventor): ?>
                                                        <option value="Main Inventor" <?= $curPos === 'Main Inventor' ? 'selected' : '' ?>>Main Inventor</option>
                                                        <option value="Co-Inventor" <?= $curPos === 'Co-Inventor' ? 'selected' : '' ?>>Co-Inventor</option>
                                                    <?php else: ?>
                                                        <option value="First author" <?= $curPos === 'First author' ? 'selected' : '' ?>>First author</option>
                                                        <option value="First author with equal contribution" <?= $curPos === 'First author with equal contribution' ? 'selected' : '' ?>>First author with equal contribution</option>
                                                        <option value="Corresponding Author" <?= $curPos === 'Corresponding Author' ? 'selected' : '' ?>>Corresponding Author</option>
                                                        <option value="Co-author" <?= $curPos === 'Co-author' ? 'selected' : '' ?>>Co-author</option>
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
                        <?php else: ?>
                            <input type="text" id="meta_<?= $field['name'] ?>" name="meta[<?= $field['name'] ?>]" value="<?= htmlspecialchars($val) ?>" <?= $is_f_req ? 'required' : '' ?>>
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
        <a href="<?= BASE_URL ?>/public/index.php?route=documents/view&amp;id=<?= (int)$document['doc_id'] ?>" style="display:block; text-align:center; margin-top: 1rem; color: #6c757d;">Cancel</a>
    </form>
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
        selectHtml = '<select name="meta[' + fieldName + '][position][]" style="width:100%; border:none; padding:7px 10px; font-size:0.9rem; outline:none; background:transparent; cursor:pointer;">' +
                     '<option value="Main Inventor">Main Inventor</option>' +
                     '<option value="Co-Inventor">Co-Inventor</option>' +
                     '</select>';
    } else {
        selectHtml = '<select name="meta[' + fieldName + '][position][]" style="width:100%; border:none; padding:7px 10px; font-size:0.9rem; outline:none; background:transparent; cursor:pointer;">' +
                     '<option value="First author">First author</option>' +
                     '<option value="First author with equal contribution">First author with equal contribution</option>' +
                     '<option value="Corresponding Author">Corresponding Author</option>' +
                     '<option value="Co-author">Co-author</option>' +
                     '</select>';
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

document.addEventListener('DOMContentLoaded', function() {
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

    var editForm = document.querySelector('form[action*="documents/process_update"]');
    if (editForm) {
        var isSubmitting = false;
        editForm.addEventListener('submit', function(e) {
            var fileInputs = editForm.querySelectorAll('input[type="file"]');
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
            var btn = editForm.querySelector('button[type="submit"]');
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
