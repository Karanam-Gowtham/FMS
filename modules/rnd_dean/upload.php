<?php
include_once '../../config.php';
include_once CONNECTION_PATH;
include_once INCLUDES_PATH . '/helpers.php';
requireLogin();

// Verify role
$role_id = $_SESSION['role_id'] ?? 0;
if (!$role_id && isset($_SESSION['roles'])) {
    foreach ($_SESSION['roles'] as $r) {
        if ($r['role_id'] == ROLE_RND_DEAN) { $role_id = ROLE_RND_DEAN; break; }
    }
}
if ($role_id != ROLE_RND_DEAN) {
    die("Access denied. R&D Dean role required.");
}

$years = getAcademicYears($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_central'])) {
    $category = $_POST['category'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $academic_year = $_POST['academic_year'];
    
    $file_path = '';
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload = handleFileUpload($_FILES['file'], 'central_rnd');
        if ($upload) {
            $file_path = $upload['file_path'];
        }
    }

    if ($file_path) {
        $stmt = $conn->prepare("INSERT INTO rnd_central_documents (uploader_id, category, title, description, file_path, academic_year) VALUES (?, ?, ?, ?, ?, ?)");
        $user_id = $_SESSION['user_id'];
        $stmt->bind_param("isssss", $user_id, $category, $title, $description, $file_path, $academic_year);
        if ($stmt->execute()) {
            $success = "Central R&D Document uploaded successfully!";
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = "File upload failed or missing file.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Central Document</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 10px; border: none; }
    </style>
</head>
<body>
<?php include_once HEADER; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Upload Central R&D Document</h2>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <div class="card p-4 shadow-sm">
        <form method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Category *</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select Category</option>
                        <option value="R&D Policies">R&D Policies</option>
                        <option value="Research Projects">Research Projects</option>
                        <option value="Research Grants">Research Grants</option>
                        <option value="Research Centres/Labs">Research Centres/Labs</option>
                        <option value="Collaborations/MoUs">Collaborations/MoUs</option>
                        <option value="Events">Events</option>
                        <option value="Ethics">Ethics</option>
                        <option value="Patents/IPR">Patents/IPR</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Academic Year *</label>
                    <select name="academic_year" class="form-select" required>
                        <?php foreach($years as $y): ?>
                            <option value="<?php echo htmlspecialchars($y['year_name']); ?>"><?php echo htmlspecialchars($y['year_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Title *</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">File Upload *</label>
                <input type="file" name="file" class="form-control" required>
            </div>

            <button type="submit" name="upload_central" class="btn btn-primary w-100">Upload Document</button>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
