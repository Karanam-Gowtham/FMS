<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> &mdash; FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/nba_module.css">
    <style>
        .metric-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #f8fafc;
        }
        .metric-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
        }
        .metric-desc {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 15px;
        }
        .form-row {
            margin-bottom: 15px;
        }
        .form-row label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: #334155;
        }
        textarea.form-control, input.form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 0.95rem;
        }
        textarea.form-control {
            height: 120px;
        }
        .mt-3 {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div id="toast" class="toast">Data saved successfully!</div>

<div class="container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/public/index.php?route=dashboard">Dashboard</a> &raquo; 
        <a href="<?= BASE_URL ?>/public/index.php?route=naac/dashboard&year=<?= urlencode($year) ?>">NAAC Accreditation</a> &raquo; 
        Criterion <?= $crit_id ?>
    </div>

    <div class="header-row">
        <h1><?= htmlspecialchars($page_title) ?></h1>
        <p>Academic Year: <strong><?= htmlspecialchars($year) ?></strong></p>
    </div>

    <form id="naacForm">
        <?php foreach ($form_schema['sections'] ?? [] as $section): ?>
            <div class="section-card">
                <h3><?= htmlspecialchars($section['title']) ?></h3>
                
                <?php foreach ($section['metrics'] ?? [] as $metric): ?>
                    <div class="metric-card">
                        <div class="metric-title"><?= htmlspecialchars($metric['title']) ?></div>
                        <?php if (!empty($metric['description'])): ?>
                            <div class="metric-desc"><?= htmlspecialchars($metric['description']) ?></div>
                        <?php endif; ?>
                        
                        <?php foreach ($metric['fields'] ?? [] as $field): ?>
                            <?php $fname = $field['name']; ?>
                            <?php if ($field['type'] === 'textarea'): ?>
                                <div class="form-row">
                                    <?php if (!empty($field['label'])): ?><label><?= htmlspecialchars($field['label']) ?></label><?php endif; ?>
                                    <textarea id="<?= htmlspecialchars($fname) ?>" class="form-control" placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>"></textarea>
                                </div>
                            <?php elseif ($field['type'] === 'number' || $field['type'] === 'url' || $field['type'] === 'text'): ?>
                                <div class="form-row">
                                    <label><?= htmlspecialchars($field['label']) ?></label>
                                    <input type="<?= htmlspecialchars($field['type']) ?>" id="<?= htmlspecialchars($fname) ?>" class="form-control" placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>">
                                </div>
                            <?php elseif ($field['type'] === 'checkbox_group'): ?>
                                <div class="form-row">
                                    <label><?= htmlspecialchars($field['label']) ?></label>
                                    <div style="display: flex; gap: 20px; margin-top: 10px;">
                                        <?php foreach ($field['options'] ?? [] as $opt): ?>
                                            <label style="font-weight: normal;">
                                                <input type="checkbox" id="<?= htmlspecialchars($opt['name']) ?>"> <?= htmlspecialchars($opt['label']) ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php elseif ($field['type'] === 'file'): ?>
                                <div class="file-upload-row mt-3">
                                    <div>
                                        <strong><?= htmlspecialchars($field['label']) ?></strong>
                                        <?php if (!empty($field['description'])): ?>
                                            <span class="desc"><?= htmlspecialchars($field['description']) ?></span>
                                        <?php endif; ?>
                                        <div id="<?= htmlspecialchars($fname) ?>_status" style="font-size:0.8rem; color:#28a745; display:none; margin-top:5px;">File uploaded: <a href="#" target="_blank" id="<?= htmlspecialchars($fname) ?>_link">View</a></div>
                                    </div>
                                    <input type="file" id="<?= htmlspecialchars($fname) ?>" accept="<?= htmlspecialchars($field['accept'] ?? '.pdf') ?>">
                                </div>
                            <?php elseif ($field['type'] === 'table'): ?>
                                <div class="form-row mt-3" style="overflow-x: auto;">
                                    <label><?= htmlspecialchars($field['label']) ?></label>
                                    <table class="data-template-table" id="<?= htmlspecialchars($fname) ?>_table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                                        <thead>
                                            <tr>
                                                <?php foreach ($field['columns'] as $col): ?>
                                                    <th style="border: 1px solid #cbd5e1; padding: 8px; background: #e2e8f0; font-size: 0.85rem; text-align: left;"><?= htmlspecialchars($col['label']) ?></th>
                                                <?php endforeach; ?>
                                                <th style="border: 1px solid #cbd5e1; padding: 8px; background: #e2e8f0; font-size: 0.85rem;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Rows will be injected here by JS -->
                                        </tbody>
                                    </table>
                                    <button type="button" onclick="addTableRow('<?= htmlspecialchars($fname) ?>')" style="margin-top: 10px; padding: 6px 12px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">+ Add Row</button>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 15px; margin-bottom: 4rem;">
            <button type="button" class="btn-save" onclick="saveCriterionData()">Save Criterion <?= $crit_id ?></button>
        </div>
    </form>
</div>

<script>
    const initialData = <?= json_encode($criteria_data) ?>;
    const formSchema = <?= json_encode($form_schema) ?>;
    
    // Build an index of table schemas for easy access
    const tableSchemas = {};
    formSchema.sections.forEach(sec => {
        sec.metrics.forEach(met => {
            met.fields.forEach(f => {
                if (f.type === 'table') {
                    tableSchemas[f.name] = f;
                }
            });
        });
    });

    function escapeHtml(unsafe) {
        if (unsafe == null) return '';
        return unsafe.toString()
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    function addTableRow(tableName, rowData = null) {
        const schema = tableSchemas[tableName];
        if (!schema) return;
        
        const tbody = document.querySelector(`#${tableName}_table tbody`);
        const tr = document.createElement('tr');
        
        let html = '';
        schema.columns.forEach(col => {
            const rawVal = (rowData && rowData[col.name] !== undefined) ? rowData[col.name] : '';
            const val = escapeHtml(rawVal);
            html += `<td style="border: 1px solid #cbd5e1; padding: 4px;">`;
            if (col.type === 'textarea') {
                html += `<textarea name="${col.name}" style="width:100%; border:1px solid #cbd5e1; padding:4px;">${val}</textarea>`;
            } else {
                html += `<input type="${col.type}" name="${col.name}" value="${val}" style="width:100%; border:1px solid #cbd5e1; padding:4px;">`;
            }
            html += `</td>`;
        });
        html += `<td style="border: 1px solid #cbd5e1; padding: 4px; text-align:center;">
                    <button type="button" onclick="this.closest('tr').remove()" style="background:#ef4444; color:white; border:none; border-radius:4px; padding:4px 8px; cursor:pointer;">X</button>
                 </td>`;
        tr.innerHTML = html;
        tbody.appendChild(tr);
    }

    document.addEventListener("DOMContentLoaded", () => {
        // Hydrate form
        formSchema.sections.forEach(section => {
            section.metrics.forEach(metric => {
                metric.fields.forEach(field => {
                    if (field.type === 'textarea' || field.type === 'number' || field.type === 'url' || field.type === 'text') {
                        if (initialData[field.name]) {
                            document.getElementById(field.name).value = initialData[field.name];
                        }
                    } else if (field.type === 'checkbox_group') {
                        field.options.forEach(opt => {
                            if (initialData[opt.name]) {
                                document.getElementById(opt.name).checked = true;
                            }
                        });
                    } else if (field.type === 'file') {
                        if (initialData[field.name + '_path']) {
                            const statusDiv = document.getElementById(field.name + '_status');
                            const link = document.getElementById(field.name + '_link');
                            statusDiv.style.display = 'block';
                            link.href = '<?= BASE_URL ?>/' + initialData[field.name + '_path'];
                        }
                    } else if (field.type === 'table') {
                        // Hydrate Table Rows
                        if (initialData[field.name] && Array.isArray(initialData[field.name])) {
                            initialData[field.name].forEach(row => {
                                addTableRow(field.name, row);
                            });
                        } else {
                            // Add one empty row by default
                            addTableRow(field.name);
                        }
                    }
                });
            });
        });
    });

    async function saveCriterionData() {
        const btn = document.querySelector('.btn-save');
        btn.innerText = 'Saving...';
        btn.disabled = true;

        const payload = {};
        
        // Collect data
        formSchema.sections.forEach(section => {
            section.metrics.forEach(metric => {
                metric.fields.forEach(field => {
                    if (field.type === 'textarea' || field.type === 'number' || field.type === 'url' || field.type === 'text') {
                        payload[field.name] = document.getElementById(field.name).value;
                    } else if (field.type === 'checkbox_group') {
                        field.options.forEach(opt => {
                            payload[opt.name] = document.getElementById(opt.name).checked;
                        });
                    } else if (field.type === 'table') {
                        const tbody = document.querySelector(`#${field.name}_table tbody`);
                        const rows = [];
                        tbody.querySelectorAll('tr').forEach(tr => {
                            const rowData = {};
                            let hasData = false;
                            field.columns.forEach(col => {
                                const input = tr.querySelector(`[name="${col.name}"]`);
                                if (input) {
                                    rowData[col.name] = input.value;
                                    if (input.value.trim() !== '') hasData = true;
                                }
                            });
                            // Only save row if at least one field has data
                            if (hasData) rows.push(rowData);
                        });
                        payload[field.name] = rows;
                    }
                });
            });
        });

        // Collect files to upload
        let fileFields = [];
        formSchema.sections.forEach(section => {
            section.metrics.forEach(metric => {
                metric.fields.forEach(field => {
                    if (field.type === 'file') {
                        fileFields.push(field.name);
                        // Preserve existing paths
                        if (initialData[field.name + '_path']) {
                            payload[field.name + '_path'] = initialData[field.name + '_path'];
                        }
                    }
                });
            });
        });

        for (const id of fileFields) {
            const fileInput = document.getElementById(id);
            if (fileInput && fileInput.files.length > 0) {
                const fileFormData = new FormData();
                fileFormData.append('pdf_file', fileInput.files[0]);
                fileFormData.append('section', id);
                
                try {
                    const res = await fetch('<?= BASE_URL ?>/public/index.php?route=api/naac/upload_pdf', {
                        method: 'POST',
                        body: fileFormData
                    });
                    const data = await res.json();
                    if (data.success || data.status === 'success') {
                        payload[id + '_path'] = data.file_path;
                    } else {
                        alert('File upload failed for ' + id + ': ' + data.message);
                    }
                } catch (e) {
                    console.error('Upload error', e);
                }
            }
        }

        // Save JSON data
        const formData = new FormData();
        formData.append('dept_id', '<?= $dept_id ?>'); formData.append('year', '<?= htmlspecialchars($year) ?>');
        formData.append('criterion_number', '<?= $crit_id ?>');
        formData.append('json_data', JSON.stringify(payload));
        
        fetch('<?= BASE_URL ?>/public/index.php?route=api/naac/save&id=<?= $crit_id ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success || data.status === 'success') {
                const toast = document.getElementById('toast');
                toast.style.display = 'block';
                setTimeout(() => { toast.style.display = 'none'; }, 3000);
            } else {
                alert('Error: ' + data.error);
            }
        })
        .finally(() => {
            btn.innerText = 'Save Criterion <?= $crit_id ?>';
            btn.disabled = false;
        });
    }
</script>
</body>
</html>

