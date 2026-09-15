<?php
declare(strict_types=1);

/**
 * FMS Legacy Sync Engine
 * 
 * Synchronizes unified documents with legacy tables for backward compatibility.
 */

require_once __DIR__ . '/constants.php';

/**
 * Maps and inserts a newly created document into the corresponding legacy table.
 */
function legacy_sync_insert_document(mysqli $conn, int $doc_id, string $type_key, int $user_id, int $dept_id, int $year_id, string $title, array $meta_data): bool
{
    // 1. Fetch user information
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $username = $user['full_name'] ?? 'Unknown';

    // 2. Fetch department information
    $stmt = $conn->prepare("SELECT dept_name FROM departments WHERE dept_id = ?");
    $stmt->bind_param('i', $dept_id);
    $stmt->execute();
    $dept = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $branch = $dept['dept_name'] ?? 'Unknown';

    // 3. Fetch year string
    $year_str = '2024-25'; // Fallback
    if ($year_id) {
        $stmt = $conn->prepare("SELECT year_label FROM academic_years WHERE year_id = ?");
        $stmt->bind_param('i', $year_id);
        $stmt->execute();
        $yr = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($yr) $year_str = $yr['year_label'];
    }

    // Current time
    $submission_time = date('Y-m-d H:i:s');
    $status = 'Pending HOD';

    // We need to fetch the uploaded file path from document_files
    $stmt = $conn->prepare("SELECT file_path FROM document_files WHERE doc_id = ? LIMIT 1");
    $stmt->bind_param('i', $doc_id);
    $stmt->execute();
    $fres = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $file_path = $fres['file_path'] ?? '';

    // Route by type_key
    if ($type_key === 'fdp' || $type_key === 'fdp_attended') {
        $mode = $meta_data['mode'] ?? '';
        $date_from = $meta_data['date_from'] ?? date('Y-m-d');
        $date_to = $meta_data['date_to'] ?? date('Y-m-d');
        $organised_by = $meta_data['organised_by'] ?? '';
        $location = $meta_data['location'] ?? '';
        
        $sql = "INSERT INTO fdps_tab (username, branch, title, mode, date_from, date_to, organised_by, location, certificate, submission_time, year, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssssssss', $username, $branch, $title, $mode, $date_from, $date_to, $organised_by, $location, $file_path, $submission_time, $year_str, $status);
        $stmt->execute();
        $stmt->close();
    }
    elseif ($type_key === 'patent') {
        $date_of_issue = $meta_data['date_of_issue'] ?? date('Y-m-d');
        $patent_type = $meta_data['patent_type'] ?? '';
        
        $sql = "INSERT INTO patents_table (Username, branch, patent_title, type, date_of_issue, patent_file, submission_time, year, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssssss', $username, $branch, $title, $patent_type, $date_of_issue, $file_path, $submission_time, $year_str, $status);
        $stmt->execute();
        $stmt->close();
    }
    
    return true;
}
