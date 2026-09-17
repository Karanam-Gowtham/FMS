<?php
/**
 * API: Upload NBA PDF
 * Handles AJAX PDF file uploads for NBA sections.
 */
require_once __DIR__ . '/../core/bootstrap.php';

header('Content-Type: application/json');

try {
    require_login();
    $auth = auth_context();
    $active_role = auth_active_role();

    $dept_id = (int)$active_role['dept_id'];
    if ($dept_id <= 0) {
        throw new Exception("You must have a department assigned to upload NBA data.");
    }

    if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("No file uploaded or an upload error occurred.");
    }

    $file = $_FILES['pdf_file'];
    
    // Strict PDF validation
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if ($mime !== 'application/pdf') {
        throw new Exception("Invalid file format. Only PDF files are allowed.");
    }

    // Determine upload directory
    $upload_dir = __DIR__ . '/../uploads/nba_pdfs/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generate a safe, unique filename
    $section = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['section'] ?? 'doc');
    $extension = 'pdf';
    $new_filename = sprintf('dept_%d_%s_%s.%s', $dept_id, $section, uniqid(), $extension);
    $destination = $upload_dir . $new_filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("Failed to save the uploaded file.");
    }

    // Return the relative path so the frontend can store it in JSON
    $relative_path = 'uploads/nba_pdfs/' . $new_filename;

    echo json_encode([
        'status' => 'success',
        'file_path' => $relative_path,
        'message' => 'File uploaded successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
