<?php
declare(strict_types=1);

/**
 * FMS File Service
 *
 * Handles secure file upload, download, and validation.
 * All file operations go through this service.
 */

/**
 * Allowed MIME types for uploaded files.
 */
function file_allowed_mimes(): array
{
    return [
        'application/pdf',
    ];
}

/**
 * Allowed file extensions (lowercase).
 */
function file_allowed_extensions(): array
{
    return ['pdf'];
}

/**
 * Maximum file size in bytes (2 MB).
 */
function file_max_size(): int
{
    return 2 * 1024 * 1024;
}

/**
 * Validates an uploaded file.
 *
 * @param array $file The $_FILES entry for a single file
 * @return array ['valid' => bool, 'error' => string|null]
 */
function file_validate(array $file): array
{
    // Check upload error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds maximum upload size.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form maximum size.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'File upload stopped by extension.',
        ];
        return ['valid' => false, 'error' => $messages[$file['error']] ?? 'Unknown upload error.'];
    }

    // Check file size
    if ($file['size'] > file_max_size()) {
        return ['valid' => false, 'error' => 'File size exceeds ' . (file_max_size() / 1024 / 1024) . ' MB limit.'];
    }

    // Check extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, file_allowed_extensions(), true)) {
        return ['valid' => false, 'error' => 'File type .' . $ext . ' is not allowed.'];
    }

    // Check MIME type (using finfo for reliability)
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, file_allowed_mimes(), true)) {
            return ['valid' => false, 'error' => 'File MIME type ' . $mime . ' is not allowed.'];
        }
    }

    return ['valid' => true, 'error' => null];
}

/**
 * Stores an uploaded file securely.
 *
 * @param array  $file      The $_FILES entry for a single file
 * @param string $type_key  The document type key (used for directory organization)
 * @param string $file_label The logical label for this file slot (e.g. 'paper_file', 'certificate')
 * @return array ['success' => bool, 'data' => [...], 'error' => string|null]
 *   On success, data contains: original_name, stored_name, file_path, mime_type, file_size
 */
function file_store(array $file, string $type_key, string $file_label): array
{
    // Validate first
    $validation = file_validate($file);
    if (!$validation['valid']) {
        return ['success' => false, 'data' => null, 'error' => $validation['error']];
    }

    // Build target directory: uploads/{type_key}/
    $base_dir = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__);
    $upload_dir = $base_dir . '/uploads/' . preg_replace('/[^a-z0-9_]/', '', $type_key) . '/';

    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            return ['success' => false, 'data' => null, 'error' => 'Failed to create upload directory.'];
        }
    }

    // Generate unique filename: {type_key}_{label}_{uniqid}.{ext}
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $stored_name = $type_key . '_' . preg_replace('/[^a-z0-9_]/', '', $file_label) . '_' . uniqid() . '.' . $ext;
    $full_path = $upload_dir . $stored_name;

    // Move the uploaded file
    if (!move_uploaded_file($file['tmp_name'], $full_path)) {
        return ['success' => false, 'data' => null, 'error' => 'Failed to move uploaded file.'];
    }

    // Compute relative path from project root
    $relative_path = 'uploads/' . preg_replace('/[^a-z0-9_]/', '', $type_key) . '/' . $stored_name;

    // Detect MIME type
    $mime = 'application/octet-stream';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $full_path);
        finfo_close($finfo);
    }

    return [
        'success' => true,
        'error'   => null,
        'data'    => [
            'original_name' => basename($file['name']),
            'stored_name'   => $stored_name,
            'file_path'     => $relative_path,
            'mime_type'     => $mime,
            'file_size'     => (int)$file['size'],
        ],
    ];
}

/**
 * Saves file metadata to the document_files table.
 *
 * @param mysqli $conn
 * @param int    $doc_id
 * @param array  $file_data From file_store()['data']
 * @param string $file_label
 * @return int|false The inserted file_id, or false on failure
 */
function file_save_record(mysqli $conn, int $doc_id, array $file_data, string $file_label)
{
    $stmt = $conn->prepare(
        "INSERT INTO document_files (doc_id, file_label, original_name, stored_name, file_path, mime_type, file_size)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param(
        'isssssi',
        $doc_id,
        $file_label,
        $file_data['original_name'],
        $file_data['stored_name'],
        $file_data['file_path'],
        $file_data['mime_type'],
        $file_data['file_size']
    );
    $result = $stmt->execute();
    $id = $result ? $stmt->insert_id : false;
    $stmt->close();
    return $id;
}

/**
 * Streams a file for download.
 *
 * @param mysqli $conn
 * @param int    $file_id
 * @param bool   $inline If true, display inline instead of forcing download
 * @return void Exits after streaming, or returns on error
 */
function file_download(mysqli $conn, int $file_id, bool $inline = false): void
{
    $stmt = $conn->prepare("SELECT original_name, file_path, mime_type FROM document_files WHERE file_id = ?");
    $stmt->bind_param('i', $file_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $file = $result->fetch_assoc();
    $stmt->close();

    if (!$file) {
        http_response_code(404);
        echo "File not found.";
        return;
    }

    $base_dir = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__);
    $full_path = $base_dir . '/' . $file['file_path'];

    if (!file_exists($full_path)) {
        http_response_code(404);
        echo "File not found on disk.";
        return;
    }

    // Clean output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    $disposition = $inline ? 'inline' : 'attachment';
    header('Content-Type: ' . ($file['mime_type'] ?: 'application/octet-stream'));
    header('Content-Disposition: ' . $disposition . '; filename="' . basename($file['original_name']) . '"');
    header('Content-Length: ' . filesize($full_path));
    header('Cache-Control: no-cache, must-revalidate');

    readfile($full_path);
    exit;
}

/**
 * Retrieves all files associated with a document.
 *
 * @param mysqli $conn
 * @param int    $doc_id
 * @return array List of file records
 */
function file_get_by_document(mysqli $conn, int $doc_id): array
{
    $stmt = $conn->prepare(
        "SELECT file_id, doc_id, file_label, original_name, stored_name, file_path, mime_type, file_size, uploaded_at
         FROM document_files WHERE doc_id = ? ORDER BY file_id"
    );
    $stmt->bind_param('i', $doc_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $files = [];
    while ($row = $result->fetch_assoc()) {
        $files[] = $row;
    }
    $stmt->close();
    return $files;
}
