<?php
$c = <<<'EOD'
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

include __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 text-white">Upload Document: <?php echo htmlspecialchars($doc_type['type_label']); ?></h4>
                <a href="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER'] ?? 'dashboard.php'); ?>" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2 fs-4"></i>
                    <div>
                        <strong>Success!</strong> Your document has been uploaded and submitted for review.
                        <br>
                        <a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" class="alert-link mt-2 d-inline-block">Upload Another</a>
                    </div>
                </div>
            <?php else: ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div class="card glass-card">
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <?php echo csrfToken(); ?>
                            
                            <h5 class="card-title text-primary mb-3">Core Information</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label class="form-label">Document Title <span class="text-danger">*</span></label>
                                    <input type="text" name="doc_title" class="form-control" required placeholder="Enter a descriptive title for this record" value="<?php echo htmlspecialchars($_POST['doc_title'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <select name="academic_year_id" class="form-select" required>
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
                                <h5 class="card-title text-primary mb-3 border-top pt-4">Specific Details</h5>
                                <div class="row g-3 mb-4">
                                    <?php foreach ($schema_fields as $f): 
                                        $isRequired = $f['required'] ? '<span class="text-danger">*</span>' : '';
                                        $reqAttr = $f['required'] ? 'required' : '';
                                        $val = htmlspecialchars($_POST[$f['name']] ?? '');
                                    ?>
                                        <div class="col-md-6">
                                            <label class="form-label"><?php echo htmlspecialchars($f['label']) . ' ' . $isRequired; ?></label>
                                            
                                            <?php if ($f['type'] === 'textarea'): ?>
                                                <textarea name="<?php echo $f['name']; ?>" class="form-control" rows="2" <?php echo $reqAttr; ?>><?php echo $val; ?></textarea>
                                            
                                            <?php elseif ($f['type'] === 'select' && isset($f['options'])): ?>
                                                <select name="<?php echo $f['name']; ?>" class="form-select" <?php echo $reqAttr; ?>>
                                                    <option value="">Select...</option>
                                                    <?php foreach ($f['options'] as $opt): ?>
                                                        <option value="<?php echo htmlspecialchars($opt); ?>" <?php echo ($val === $opt) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($opt); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                
                                            <?php else: ?>
                                                <input type="<?php echo htmlspecialchars($f['type']); ?>" name="<?php echo $f['name']; ?>" class="form-control" <?php echo $reqAttr; ?> value="<?php echo $val; ?>">
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <h5 class="card-title text-primary mb-3 border-top pt-4">File Attachments</h5>
                            <div class="row g-3 mb-4">
                                <?php foreach ($schema_files as $f): 
                                    $isRequired = $f['required'] ? '<span class="text-danger">*</span>' : '<span class="text-muted">(Optional)</span>';
                                    $reqAttr = $f['required'] ? 'required' : '';
                                ?>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo htmlspecialchars($f['label']) . ' ' . $isRequired; ?></label>
                                        <input type="file" name="<?php echo $f['name']; ?>" class="form-control form-control-sm" accept="<?php echo htmlspecialchars($f['accept'] ?? '*/*'); ?>" <?php echo $reqAttr; ?>>
                                        <?php if (!empty($f['accept'])): ?>
                                            <div class="form-text text-muted small">Accepted: <?php echo htmlspecialchars($f['accept']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="border-top pt-4 text-end">
                                <button type="reset" class="btn btn-outline-secondary me-2">Clear Form</button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-upload me-2"></i> Submit Document
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<style>
.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    border-radius: 12px;
}
.form-label {
    font-weight: 500;
    color: #2c3e50;
}
.card-title {
    font-weight: 600;
    color: #1a73e8 !important;
}
.form-control, .form-select {
    border-color: #dee2e6;
    padding: 0.6rem 0.75rem;
}
.form-control:focus, .form-select:focus {
    border-color: #1a73e8;
    box-shadow: 0 0 0 0.25rem rgba(26, 115, 232, 0.25);
}
</style>

<script>
// Simple client-side validation styling
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
EOD;

file_put_contents('e:\set\xampp\htdocs\mini\FMS\pages\upload.php', $c);
echo "upload.php rewritten.";
