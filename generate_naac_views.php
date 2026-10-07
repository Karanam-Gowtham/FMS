<?php
$viewsDir = __DIR__ . '/src/Views/naac';
if (!is_dir($viewsDir)) {
    mkdir($viewsDir, 0755, true);
}

for ($i = 1; $i <= 7; $i++) {
    $content = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(\$page_title) ?> &mdash; FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/nba_module.css">
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div id="toast" class="toast">Data saved successfully!</div>

<div class="container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/public/index.php?route=dashboard">Dashboard</a> &raquo; 
        <a href="<?= BASE_URL ?>/public/index.php?route=naac/dashboard&year=<?= urlencode(\$year) ?>">NAAC Accreditation</a> &raquo; 
        Criterion $i
    </div>

    <div class="header-row">
        <h1><?= htmlspecialchars(\$page_title) ?></h1>
        <p>Academic Year: <strong><?= htmlspecialchars(\$year) ?></strong></p>
    </div>

    <form id="naacForm">
        <div class="section-card">
            <h3>Overview</h3>
            <p>This is a placeholder for NAAC Criterion $i. You can design your forms here.</p>
            <div style="margin-bottom: 15px;">
                <label style="font-size: 0.85rem; font-weight: 600; color: #555;">Observations / Data</label>
                <textarea id="data-text" style="width: 100%; height: 100px; padding: 8px; border: 1px solid #ced4da; border-radius: 5px; margin-top: 5px;" placeholder="Enter details..."></textarea>
            </div>
            
            <div class="file-upload-row">
                <div>
                    <strong>Proof Document</strong>
                    <span class="desc">Please upload the PDF proof.</span>
                </div>
                <input type="file" id="proof_file" accept=".pdf">
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 15px; margin-bottom: 4rem;">
            <button type="button" class="btn-save" onclick="saveCriterionData()">Save Criterion $i</button>
        </div>
    </form>
</div>

<script>
    const initialData = <?= json_encode(\$criteria_data) ?>;
    
    document.addEventListener("DOMContentLoaded", () => {
        if (initialData.observations) {
            document.getElementById('data-text').value = initialData.observations;
        }
    });

    function saveCriterionData() {
        const payload = {
            observations: document.getElementById('data-text').value
        };

        const formData = new FormData();
        formData.append('year', '<?= htmlspecialchars(\$year) ?>');
        formData.append('criterion_number', '$i');
        formData.append('json_data', JSON.stringify(payload));
        
        const fileInput = document.getElementById('proof_file');
        if (fileInput.files.length > 0) {
            const fileFormData = new FormData();
            fileFormData.append('pdf_file', fileInput.files[0]);
            fileFormData.append('section', 'criterion{$i}_proof');
            
            fetch('<?= BASE_URL ?>/public/index.php?route=api/naac/upload_pdf', {
                method: 'POST',
                body: fileFormData
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    payload.proof_file = data.file_path;
                    formData.set('json_data', JSON.stringify(payload));
                    submitData(formData);
                } else {
                    alert('File upload failed: ' + data.message);
                }
            });
        } else {
            submitData(formData);
        }
    }
    
    function submitData(formData) {
        fetch('<?= BASE_URL ?>/public/index.php?route=api/naac/save&id=$i', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const toast = document.getElementById('toast');
                toast.style.display = 'block';
                setTimeout(() => { toast.style.display = 'none'; }, 3000);
            } else {
                alert('Error: ' + data.error);
            }
        });
    }
</script>
</body>
</html>
HTML;
    
    file_put_contents($viewsDir . "/criterion{$i}.php", $content);
}

echo "Views generated successfully.\n";
