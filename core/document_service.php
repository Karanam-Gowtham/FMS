<?php
declare(strict_types=1);

/**
 * FMS Document Service
 *
 * CRUD operations for the `documents` table and associated meta_* extension tables.
 * Uses meta_registry.php for safe table resolution and workflow_engine.php for
 * workflow initialization.
 */

require_once __DIR__ . '/meta_registry.php';
require_once __DIR__ . '/file_service.php';

require_once __DIR__ . '/workflow_engine.php';

// ============================================================
// DOCUMENT TYPE LOOKUP
// ============================================================

/**
 * Loads a document type by its type_key.
 *
 * @param mysqli $conn
 * @param string $type_key
 * @return array|null
 */
function doc_get_type_by_key(mysqli $conn, string $type_key): ?array
{
    $stmt = $conn->prepare(
        "SELECT type_id, type_code AS type_key, label AS type_label, workflow_id
         FROM document_types WHERE type_code = ?"
    );
    if (!$stmt) {
        error_log("Failed to prepare statement in doc_get_type_by_key: " . $conn->error);
        return null;
    }
    $stmt->bind_param('s', $type_key);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/**
 * Loads a document type by its type_id.
 *
 * @param mysqli $conn
 * @param int    $type_id
 * @return array|null
 */
function doc_get_type_by_id(mysqli $conn, int $type_id): ?array
{
    $stmt = $conn->prepare(
        "SELECT type_id, type_code AS type_key, label AS type_label, workflow_id
         FROM document_types WHERE type_id = ?"
    );
    if (!$stmt) {
        error_log("Failed to prepare statement in doc_get_type_by_id: " . $conn->error);
        return null;
    }
    $stmt->bind_param('i', $type_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/**
 * Central mapping of type_code to category.
 */
function doc_get_category_map(): array
{
    return [
        'journal' => 'research',
        'conference' => 'research',
        'patent' => 'research',
        'fdp_attended' => 'research',
        'fdp_organised' => 'research',
        'conf_organised' => 'research',
        'criteria_file' => 'criteria',
        'scholarship' => 'criteria',
        'dept_file' => 'department',
        'central_file' => 'central',
        'placement' => 'student',
        'higher_ed' => 'student',
        'exam_qual' => 'student',
        'award' => 'student',
        'student_event' => 'student',
        'student_body' => 'student',
        'student_journal' => 'student',
        'student_conference' => 'student',
        'student_activity_file' => 'student'
    ];
}

/**
 * Lists all active document types, optionally filtered by category.
 *
 * @param mysqli $conn
 * @return array
 */
function doc_get_types(mysqli $conn): array
{
    $types = [];
    $stmt = $conn->prepare(
        "SELECT type_id, type_code, label AS type_label, workflow_id
         FROM document_types
         ORDER BY label"
    );
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        
        $category_map = doc_get_category_map();

        while ($row = $result->fetch_assoc()) {
            $type_code = $row['type_code'];
            // Inject the missing properties that the codebase expects
            $row['type_key'] = $type_code;
            $row['category'] = $category_map[$type_code] ?? 'other';
            $row['is_active'] = 1; // Assuming all returned are active
            
            $types[] = $row;
        }
        $stmt->close();
    }
    return $types;
}

// ============================================================
// DOCUMENT CRUD
// ============================================================

/**
 * Creates a new document with its meta data and files.
 *
 * This is the main entry point for document creation. It:
 * 1. Validates the type_key
 * 2. Inserts into `documents`
 * 3. Inserts into the appropriate meta_* table
 * 4. Stores uploaded files in `document_files`
 * 5. Initializes the workflow (sets status and current_step)
 *
 * @param mysqli $conn
 * @param string $type_key       Document type key (e.g. 'journal', 'patent')
 * @param int    $uploaded_by    User ID of the uploader
 * @param int    $dept_id        Department ID
 * @param int|null $year_id      Academic year ID (nullable)
 * @param string $title          Document title
 * @param array  $meta_data      Associative array of meta field values
 * @param array  $files          $_FILES array (keyed by file slot name)
 * @param int|null $mentor_id    Mentor User ID (nullable)
 * @return array ['success' => bool, 'doc_id' => int|null, 'error' => string|null]
 */
function doc_create(
    mysqli $conn,
    string $type_key,
    int    $uploaded_by,
    int    $dept_id,
    ?int   $year_id,
    string $title,
    array  $meta_data,
    array  $files,
    ?int   $mentor_id = null
): array {
    // 1. Validate type_key
    $doc_type = doc_get_type_by_key($conn, $type_key);
    if (!$doc_type) {
        return ['success' => false, 'doc_id' => null, 'error' => 'Invalid document type.'];
    }

    // 2. Resolve meta table from whitelist
    $meta_table = meta_get_table($type_key);
    if ($meta_table === null) {
        return ['success' => false, 'doc_id' => null, 'error' => 'Document type not registered in meta registry.'];
    }

    $conn->begin_transaction();

    try {
        // 3. Insert into documents
        $stmt = $conn->prepare(
            "INSERT INTO documents (type_id, uploaded_by, dept_id, academic_year_id, mentor_id, title, file_path, status, current_step, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, '', 'pending', NULL, NOW(), NOW())"
        );
        if (!$stmt) {
            throw new RuntimeException('Failed to prepare document insert statement: ' . $conn->error);
        }
        $type_id = (int)$doc_type['type_id'];
        $stmt->bind_param('iiiiis', $type_id, $uploaded_by, $dept_id, $year_id, $mentor_id, $title);
        $stmt->execute();
        $doc_id = $stmt->insert_id;
        $error = $stmt->error;
        $stmt->close();

        if (!$doc_id) {
            throw new RuntimeException('Failed to insert document record: ' . ($error ?: 'Unknown error'));
        }

        // 4. Insert meta data
        $meta_result = doc_insert_meta($conn, $meta_table, $doc_id, $type_key, $meta_data);
        if (!$meta_result['success']) {
            throw new RuntimeException($meta_result['error']);
        }

        // 5. Store files
        $file_slots = meta_get_file_slots($type_key);
        $stored_paths = []; // Track physical paths to cleanup on rollback
        $is_stepcone = ($type_key === 'student_activity_file' && isset($meta_data['activity_category']) && is_string($meta_data['activity_category']) && strcasecmp(trim($meta_data['activity_category']), 'stepcone') === 0);

        foreach ($file_slots as $slot) {
            $slot_name = $slot['name'];

            // Check if file was uploaded for this slot
            if (!isset($files[$slot_name]) || $files[$slot_name]['error'] === UPLOAD_ERR_NO_FILE) {
                if ($slot['required'] && !$is_stepcone) {
                    throw new RuntimeException('Required file "' . $slot['label'] . '" was not uploaded.');
                }
                continue;
            }

            $store_result = file_store($files[$slot_name], $type_key, $slot_name);
            if (!$store_result['success']) {
                throw new RuntimeException('File upload failed for "' . $slot['label'] . '": ' . $store_result['error']);
            }
            
            $stored_paths[] = __DIR__ . '/../' . $store_result['data']['file_path'];

            $file_record_id = file_save_record($conn, $doc_id, $store_result['data'], $slot_name);
            if (!$file_record_id) {
                throw new RuntimeException('Failed to save file record for "' . $slot['label'] . '".');
            }
        }

        // 6. Initialize workflow
        $wf_result = wf_initialize_document($conn, $doc_id, $type_id, $uploaded_by);
        if (!$wf_result['success']) {
            throw new RuntimeException('Workflow initialization failed: ' . $wf_result['error']);
        }

        // 7. Auto-Merge Multiple PDFs
        $pdfs_to_merge = [];
        $file_slots_by_name = array_column($file_slots, null, 'name');
        
        // Refetch files from db to get correct paths
        $stmt_f = $conn->prepare("SELECT file_path, file_label FROM document_files WHERE doc_id = ?");
        if ($stmt_f) {
            $stmt_f->bind_param('i', $doc_id);
            $stmt_f->execute();
            $f_res = $stmt_f->get_result();
            while ($f_row = $f_res->fetch_assoc()) {
                if (strtolower(pathinfo($f_row['file_path'], PATHINFO_EXTENSION)) === 'pdf') {
                    $slot_def = $file_slots_by_name[$f_row['file_label']] ?? null;
                    $title = $slot_def ? $slot_def['label'] : ucfirst(str_replace('_', ' ', $f_row['file_label']));
                    $pdfs_to_merge[] = [
                        'path' => __DIR__ . '/../' . $f_row['file_path'],
                        'title' => $title
                    ];
                }
            }
            $stmt_f->close();
        }

        if (count($pdfs_to_merge) > 1) {
            require_once __DIR__ . '/pdf_merger_service.php';
            $safe_type_key = preg_replace('/[^a-z0-9_]/', '', strtolower((string)$type_key));
            $merged_filename = $safe_type_key . '_merged_' . uniqid() . '.pdf';
            $merged_dir = __DIR__ . '/../uploads/' . $safe_type_key . '/';
            if (!is_dir($merged_dir)) { @mkdir($merged_dir, 0755, true); }
            $merged_path = $merged_dir . $merged_filename;
            
            try {
                if (merge_pdfs_with_headings($pdfs_to_merge, $merged_path)) {
                    $rel_merged_path = 'uploads/' . $safe_type_key . '/' . $merged_filename;
                    
                    // Update the master file_path in documents table
                    $upd_stmt = $conn->prepare("UPDATE documents SET file_path = ? WHERE doc_id = ?");
                    if ($upd_stmt) {
                        $upd_stmt->bind_param('si', $rel_merged_path, $doc_id);
                        $upd_stmt->execute();
                        $upd_stmt->close();
                    }
                    
                    // Store as a document_files record
                    $real_merged_file = realpath($merged_path);
                    $fsize = ($real_merged_file && is_file($real_merged_file)) ? @filesize($real_merged_file) : 0;
                    $merged_data = [
                        'original_name' => 'Merged_Document.pdf',
                        'stored_name' => $merged_filename,
                        'file_path' => $rel_merged_path,
                        'mime_type' => 'application/pdf',
                        'file_size' => ($fsize !== false && $fsize >= 0) ? $fsize : 0
                    ];
                    file_save_record($conn, $doc_id, $merged_data, 'merged_pdf');
                    $stored_paths[] = $merged_path;
                }
            } catch (\Throwable $e) {
                // Ignore merge errors and keep original files
                error_log("PDF Merge Failed: " . $e->getMessage());
            }
        } else {
            // Populate master file_path with the primary uploaded file so documents.file_path is never left blank
            $stmt_single = $conn->prepare("SELECT file_path FROM document_files WHERE doc_id = ? ORDER BY file_id ASC LIMIT 1");
            if ($stmt_single) {
                $stmt_single->bind_param('i', $doc_id);
                $stmt_single->execute();
                $s_res = $stmt_single->get_result();
                if ($s_row = $s_res->fetch_assoc()) {
                    $single_file_path = $s_row['file_path'];
                    $upd_stmt = $conn->prepare("UPDATE documents SET file_path = ? WHERE doc_id = ?");
                    if ($upd_stmt) {
                        $upd_stmt->bind_param('si', $single_file_path, $doc_id);
                        $upd_stmt->execute();
                        $upd_stmt->close();
                    }
                }
                $stmt_single->close();
            }
        }

        // 7. Sync to legacy tables for backward compatibility
        // legacy_sync_insert_document($conn, $doc_id, $type_key, $uploaded_by, $dept_id, $academic_year_id ?? 0, $title, $meta_data, $files);

        $conn->commit();

        return ['success' => true, 'doc_id' => $doc_id, 'error' => null];

    } catch (\Throwable $e) {
        $conn->rollback();
        
        // Cleanup orphaned files created during the failed transaction
        if (isset($stored_paths) && is_array($stored_paths)) {
            foreach ($stored_paths as $path) {
                if (file_exists($path) && is_file($path)) {
                    @unlink($path);
                }
            }
        }
        
        return ['success' => false, 'doc_id' => null, 'error' => $e->getMessage()];
    }
}

/**
 * Inserts meta data into the appropriate meta_* table.
 *
 * @param mysqli $conn
 * @param string $meta_table  The validated meta table name (from whitelist)
 * @param int    $doc_id
 * @param string $type_key
 * @param array  $meta_data   Associative array of field => value
 * @return array ['success' => bool, 'error' => string|null]
 */
function doc_insert_meta(mysqli $conn, string $meta_table, int $doc_id, string $type_key, array $meta_data): array
{
    // Get field definitions for this type
    $field_defs = meta_get_fields($type_key);
    if (empty($field_defs)) {
        // No meta fields defined — just insert doc_id
        $stmt = $conn->prepare("INSERT INTO `$meta_table` (doc_id) VALUES (?)");
        if (!$stmt) {
            return ['success' => false, 'error' => 'Failed to prepare meta record insert: ' . $conn->error];
        }
        $stmt->bind_param('i', $doc_id);
        $result = $stmt->execute();
        $stmt->close();
        return ['success' => $result, 'error' => $result ? null : 'Failed to insert meta record.'];
    }

    // Build column list and values from known fields only
    $columns = ['doc_id'];
    $placeholders = ['?'];
    $types_str = 'i';
    $values = [$doc_id];

    // Ensure event_title is never empty/null for student_activity_file (DB column is VARCHAR NOT NULL)
    if ($type_key === 'student_activity_file' && empty($meta_data['event_title'])) {
        $meta_data['event_title'] = (!empty($meta_data['activity_category']) && is_string($meta_data['activity_category'])) ? $meta_data['activity_category'] . ' Event' : 'Event';
    }

    // Ensure target_audience (Event Scope) defaults safely for student_activity_file
    if ($type_key === 'student_activity_file') {
        $act_cat = (isset($meta_data['activity_category']) && is_string($meta_data['activity_category'])) ? $meta_data['activity_category'] : '';
        if (empty($meta_data['target_audience'])) {
            if ($act_cat === 'Clubs' || $act_cat === 'Stepcone') {
                $meta_data['target_audience'] = 'Central / University Wide';
            } else {
                $meta_data['target_audience'] = 'Department Wise';
            }
        } elseif ($act_cat === 'Clubs') {
            // Clubs are inherently college/university-wide
            if (isset($meta_data['target_audience']) && $meta_data['target_audience'] === 'Department Wise') {
                $meta_data['target_audience'] = 'Central / University Wide';
            }
        }
    }

    foreach ($field_defs as $field) {
        $name = $field['name'];
        if (array_key_exists($name, $meta_data)) {
            $columns[] = "`$name`";
            $placeholders[] = '?';

            $val = $meta_data[$name];
            if ($val === '' || $val === null) {
                if ($type_key === 'student_activity_file' && $name === 'event_title') {
                    $types_str .= 's';
                    $values[] = (!empty($meta_data['activity_category']) && is_string($meta_data['activity_category'])) ? $meta_data['activity_category'] . ' Event' : 'Event';
                } else {
                    $types_str .= 's';
                    $values[] = null;
                }
            } elseif ($field['type'] === 'number') {
                $types_str .= 's'; // store as string, DB will cast
                $values[] = $val;
            } elseif (isset($field['type']) && ($field['type'] === 'json' || $field['type'] === 'author_table')) {
                $types_str .= 's';
                // Encode if it's an array or object, otherwise leave as is
                $values[] = (is_array($val) || is_object($val)) ? json_encode($val) : $val;
            } else {
                $types_str .= 's';
                $values[] = (is_array($val) || is_object($val)) ? json_encode($val) : $val;
            }
        }
    }

    $sql = "INSERT INTO `$meta_table` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return ['success' => false, 'error' => 'Failed to prepare meta insert: ' . $conn->error];
    }

    $stmt->bind_param($types_str, ...$values);
    $result = $stmt->execute();
    $error = $result ? null : 'Meta insert failed: ' . $stmt->error;
    $stmt->close();

    return ['success' => $result, 'error' => $error];
}

// ============================================================
// DOCUMENT RETRIEVAL
// ============================================================

/**
 * Loads a single document with its type info.
 *
 * @param mysqli $conn
 * @param int    $doc_id
 * @return array|null Document record with type details
 */
function doc_get(mysqli $conn, int $doc_id): ?array
{
    $stmt = $conn->prepare(
        "SELECT d.doc_id, d.type_id, d.uploaded_by, d.dept_id, d.academic_year_id, d.mentor_id, d.file_path,
                d.title, d.status, d.current_step, d.rejection_reason,
                d.created_at, d.updated_at,
                dt.type_code as type_key, dt.label as type_label, dt.workflow_id as workflow_key,
                u.full_name AS uploader_name,
                dep.dept_name,
                ay.year_label,
                (CASE 
                    WHEN dt.type_code = 'student_activity_file' THEN (SELECT date_from FROM meta_student_activity_file WHERE doc_id = d.doc_id LIMIT 1)
                    WHEN dt.type_code = 'conference' THEN (SELECT from_date FROM meta_conference WHERE doc_id = d.doc_id LIMIT 1)
                    WHEN dt.type_code = 'journal' THEN (SELECT date_of_publication FROM meta_journal WHERE doc_id = d.doc_id LIMIT 1)
                    WHEN dt.type_code = 'patent' THEN (SELECT date_of_issue FROM meta_patent WHERE doc_id = d.doc_id LIMIT 1)
                    WHEN dt.type_code IN ('fdp', 'fdp_attended') THEN (SELECT date_from FROM meta_fdp_attended WHERE doc_id = d.doc_id LIMIT 1)
                    WHEN dt.type_code = 'fdp_organised' THEN (SELECT date_from FROM meta_fdp_organised WHERE doc_id = d.doc_id LIMIT 1)
                    WHEN dt.type_code = 'conf_organised' THEN (SELECT date_from FROM meta_conf_organised WHERE doc_id = d.doc_id LIMIT 1)
                    WHEN dt.type_code = 'student_event' THEN (SELECT from_date FROM meta_student_event WHERE doc_id = d.doc_id LIMIT 1)
                    WHEN dt.type_code = 'student_body' THEN (SELECT from_date FROM meta_student_body WHERE doc_id = d.doc_id LIMIT 1)
                    WHEN dt.type_code = 'student_conference' THEN (SELECT from_date FROM meta_student_conference WHERE doc_id = d.doc_id LIMIT 1)
                    WHEN dt.type_code = 'student_journal' THEN (SELECT date_of_submission FROM meta_student_journal WHERE doc_id = d.doc_id LIMIT 1)
                    ELSE NULL
                END) AS event_date
         FROM documents d
         JOIN document_types dt ON dt.type_id = d.type_id
         JOIN users u ON u.user_id = d.uploaded_by
         JOIN departments dep ON dep.dept_id = d.dept_id
         LEFT JOIN academic_years ay ON ay.year_id = d.academic_year_id
         WHERE d.doc_id = ?"
    );
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('i', $doc_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row) {
        $category_map = doc_get_category_map();
        $row['category'] = $category_map[$row['type_key']] ?? 'other';
        return $row;
    }
    return null;
}

/**
 * Loads a single document with its type info (alias for doc_get).
 *
 * @param mysqli $conn
 * @param int    $doc_id
 * @return array|null
 */
function doc_get_by_id(mysqli $conn, int $doc_id): ?array
{
    return doc_get($conn, $doc_id);
}

/**
 * Loads meta data for a document.
 *
 * @param mysqli $conn
 * @param int    $doc_id
 * @param string $type_key
 * @return array|null Meta record, or null if not found
 */
function doc_get_meta(mysqli $conn, int $doc_id, string $type_key): ?array
{
    $meta_table = meta_get_table($type_key);
    if ($meta_table === null) {
        return null;
    }

    $stmt = $conn->prepare("SELECT * FROM `$meta_table` WHERE doc_id = ?");
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('i', $doc_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/**
 * Helper to sanitize array values and build SQL IN clause
 *
 * @param string $column Column expression (e.g. 'd.dept_id')
 * @param array $raw_values Raw array of inputs
 * @param string $type 'int' or 'string'
 * @param array &$where_clauses Target where array
 * @param array &$params Target params array
 * @param string &$types Target types string
 * @return bool True if an IN clause was appended, false otherwise
 */
function _doc_service_append_in_clause(string $column, array $raw_values, string $type, array &$where_clauses, array &$params, string &$types): bool
{
    $clean = [];
    if ($type === 'int') {
        foreach ($raw_values as $val) {
            if (is_numeric($val) && (int)$val > 0) {
                $clean[] = (int)$val;
            }
        }
        $char = 'i';
    } else {
        foreach ($raw_values as $val) {
            if (is_string($val) && trim($val) !== '') {
                $clean[] = trim($val);
            }
        }
        $char = 's';
    }

    if (!empty($clean)) {
        $placeholders = str_repeat('?,', count($clean) - 1) . '?';
        $where_clauses[] = "$column IN ($placeholders)";
        foreach ($clean as $item) {
            $params[] = $item;
            $types .= $char;
        }
        return true;
    }
    return false;
}

/**
 * Lists documents with filtering and pagination.
 *
 * @param mysqli $conn
 * @param array  $filters Associative array of optional filters:
 *   - 'uploaded_by'  => int     (filter by uploader)
 *   - 'dept_id'      => int     (filter by department)
 *   - 'doc_type_id'  => int     (filter by type)
 *   - 'type_key'     => string  (filter by type key)
 *   - 'category'     => string  (filter by category)
 *   - 'status'       => string  (filter by status)
 *   - 'year_id'      => int     (filter by academic year)
 *   - 'search'       => string  (search title)
 * @param int    $limit   Max results (default 50)
 * @param int    $offset  Offset for pagination (default 0)
 * @param string $order   ORDER BY clause (default 'd.created_at DESC')
 * @return array ['rows' => [...], 'total' => int]
 */
function doc_list(mysqli $conn, array $filters = [], int $limit = 50, int $offset = 0, string $order = 'd.created_at DESC'): array
{
    $where_clauses = [];
    $params = [];
    $types = '';

    if (!empty($filters['uploaded_by'])) {
        $where_clauses[] = 'd.uploaded_by = ?';
        $params[] = (int)$filters['uploaded_by'];
        $types .= 'i';
    }

    if (!empty($filters['dept_ids']) && is_array($filters['dept_ids'])) {
        _doc_service_append_in_clause('d.dept_id', $filters['dept_ids'], 'int', $where_clauses, $params, $types);
    } elseif (!empty($filters['dept_id'])) {
        $where_clauses[] = 'd.dept_id = ?';
        $params[] = (int)$filters['dept_id'];
        $types .= 'i';
    }

    $type_id_filter = !empty($filters['type_id']) ? (int)$filters['type_id'] : (!empty($filters['doc_type_id']) ? (int)$filters['doc_type_id'] : 0);
    if ($type_id_filter > 0) {
        $where_clauses[] = 'd.type_id = ?';
        $params[] = $type_id_filter;
        $types .= 'i';
    }

    if (!empty($filters['type_key']) && is_string($filters['type_key'])) {
        $where_clauses[] = 'dt.type_code = ?';
        $params[] = trim($filters['type_key']);
        $types .= 's';
    }
    
    // Support filtering by an array of type keys (instead of category)
    if (!empty($filters['type_keys']) && is_array($filters['type_keys'])) {
        _doc_service_append_in_clause('dt.type_code', $filters['type_keys'], 'string', $where_clauses, $params, $types);
    }

    if (!empty($filters['statuses']) && is_array($filters['statuses'])) {
        _doc_service_append_in_clause('d.status', $filters['statuses'], 'string', $where_clauses, $params, $types);
    } elseif (!empty($filters['status']) && is_string($filters['status'])) {
        $where_clauses[] = 'd.status = ?';
        $params[] = trim($filters['status']);
        $types .= 's';
    }

    if (!empty($filters['year_ids']) && is_array($filters['year_ids'])) {
        _doc_service_append_in_clause('d.academic_year_id', $filters['year_ids'], 'int', $where_clauses, $params, $types);
    } elseif (!empty($filters['year_id'])) {
        $where_clauses[] = 'd.academic_year_id = ?';
        $params[] = (int)$filters['year_id'];
        $types .= 'i';
    }

    if (!empty($filters['search']) && is_string($filters['search'])) {
        $where_clauses[] = 'd.title LIKE ?';
        $params[] = '%' . trim($filters['search']) . '%';
        $types .= 's';
    }

    $join_sql = '';
    $meta_select = '';
    if (!empty($filters['sub_types']) && is_array($filters['sub_types'])) {
        $clean_subtypes = [];
        foreach ($filters['sub_types'] as $st) {
            if (is_string($st) && trim($st) !== '') {
                $clean_subtypes[] = trim($st);
            }
        }
        if (!empty($clean_subtypes)) {
            $subtype_conditions = [];
            $has_dept_file_subtypes = false;
            $dept_file_subtypes = [];

            foreach ($clean_subtypes as $st) {
                if ($st === 'Student Activities Files') {
                    $subtype_conditions[] = "dt.type_code = 'student_activity_file'";
                } else {
                    $has_dept_file_subtypes = true;
                    $dept_file_subtypes[] = $st;
                    if ($st === 'Student Related Files') {
                        $subtype_conditions[] = "dt.type_code IN ('student_journal', 'student_conference', 'student_body', 'exam_qual')";
                    }
                }
            }

            $or_clauses = [];
            if (!empty($subtype_conditions)) {
                $or_clauses[] = implode(' OR ', $subtype_conditions);
            }
            if ($has_dept_file_subtypes) {
                if (strpos($join_sql, 'meta_dept_file mdf') === false) {
                    $join_sql .= ' LEFT JOIN meta_dept_file mdf ON mdf.doc_id = d.doc_id ';
                }
                $placeholders = str_repeat('?,', count($dept_file_subtypes) - 1) . '?';
                $or_clauses[] = "(dt.type_code = 'dept_file' AND mdf.file_type IN ($placeholders))";
                foreach ($dept_file_subtypes as $st) {
                    $params[] = $st;
                    $types .= 's';
                }
            }
            
            if (!empty($or_clauses)) {
                $where_clauses[] = '(' . implode(' OR ', $or_clauses) . ')';
            }
        }
    } elseif (!empty($filters['sub_type']) && is_string($filters['sub_type'])) {
        $st = trim($filters['sub_type']);
        if ($st === 'Student Activities Files') {
            $where_clauses[] = "dt.type_code = 'student_activity_file'";
        } else {
            if (strpos($join_sql, 'meta_dept_file mdf') === false) {
                $join_sql .= ' LEFT JOIN meta_dept_file mdf ON mdf.doc_id = d.doc_id ';
            }
            if ($st === 'Student Related Files') {
                $where_clauses[] = "(dt.type_code IN ('student_journal', 'student_conference', 'student_body', 'exam_qual') OR (dt.type_code = 'dept_file' AND mdf.file_type = ?))";
            } else {
                $where_clauses[] = "(dt.type_code = 'dept_file' AND mdf.file_type = ?)";
            }
            $params[] = $st;
            $types .= 's';
        }
    }

    if (!empty($filters['sub_file_types']) && is_array($filters['sub_file_types'])) {
        $clean_subfiletypes = [];
        foreach ($filters['sub_file_types'] as $st) {
            if (is_string($st) && trim($st) !== '') {
                $clean_subfiletypes[] = trim($st);
            }
        }
        if (!empty($clean_subfiletypes)) {
            $or_clauses = [];
            $mdf_types = [];
            $saf_types = [];
            
            $type_code_mappings = [
                'Papers Published by Students' => 'student_journal',
                'Co-Curricular/Extra-Curricular Activities' => 'student_conference',
                'Professional Societies' => 'student_body',
                'Students in Competitive Exams' => 'exam_qual',
            ];
            
            $saf_known_types = ['Grad Talks', 'Expert Talks / Guest Lectures', 'Soft skills', 'Language and communication skills', 'Life skills', 'Professional Societies', 'Clubs', 'IIC', 'Stepcone'];
            
            foreach ($clean_subfiletypes as $st) {
                if (isset($type_code_mappings[$st])) {
                    $or_clauses[] = "dt.type_code = '" . $type_code_mappings[$st] . "'";
                }
                
                if (in_array($st, $saf_known_types, true)) {
                    $saf_types[] = $st;
                }
                
                if (!isset($type_code_mappings[$st]) && !in_array($st, $saf_known_types, true)) {
                    $mdf_types[] = $st;
                }
            }

            if (!empty($mdf_types)) {
                if (strpos($join_sql, 'meta_dept_file mdf') === false) {
                    $join_sql .= ' LEFT JOIN meta_dept_file mdf ON mdf.doc_id = d.doc_id ';
                }
                $placeholders = str_repeat('?,', count($mdf_types) - 1) . '?';
                $or_clauses[] = "(dt.type_code = 'dept_file' AND mdf.sub_file_type IN ($placeholders))";
                foreach ($mdf_types as $st) {
                    $params[] = $st;
                    $types .= 's';
                }
            }
            
            if (!empty($saf_types)) {
                if (strpos($join_sql, 'meta_student_activity_file msaf') === false) {
                    $join_sql .= ' LEFT JOIN meta_student_activity_file msaf ON msaf.doc_id = d.doc_id ';
                }
                $placeholders = str_repeat('?,', count($saf_types) - 1) . '?';
                $or_clauses[] = "(dt.type_code = 'student_activity_file' AND msaf.activity_category IN ($placeholders))";
                foreach ($saf_types as $st) {
                    $params[] = $st;
                    $types .= 's';
                }
            }

            if (!empty($or_clauses)) {
                $where_clauses[] = '(' . implode(' OR ', $or_clauses) . ')';
            }
        }
    }

    if (!empty($filters['global_event_mode']) && is_array($filters['global_event_mode'])) {
        if (_doc_service_append_in_clause('msaf.event_mode', $filters['global_event_mode'], 'string', $where_clauses, $params, $types) && strpos($join_sql, 'meta_student_activity_file msaf') === false) {
            $join_sql .= ' LEFT JOIN meta_student_activity_file msaf ON msaf.doc_id = d.doc_id ';
        }
    }

    if (!empty($filters['global_topic_domain']) && is_array($filters['global_topic_domain'])) {
        if (_doc_service_append_in_clause('msaf.topic_domain', $filters['global_topic_domain'], 'string', $where_clauses, $params, $types) && strpos($join_sql, 'meta_student_activity_file msaf') === false) {
            $join_sql .= ' LEFT JOIN meta_student_activity_file msaf ON msaf.doc_id = d.doc_id ';
        }
    }

    if (!empty($filters['global_event_type']) && is_array($filters['global_event_type'])) {
        $clean_etypes = [];
        foreach ($filters['global_event_type'] as $et) {
            if (is_string($et) && trim($et) !== '') {
                $clean_etypes[] = trim($et);
            }
        }
        if (!empty($clean_etypes)) {
            if (strpos($join_sql, 'meta_student_activity_file msaf') === false) {
                $join_sql .= ' LEFT JOIN meta_student_activity_file msaf ON msaf.doc_id = d.doc_id ';
            }
            if (strpos($join_sql, 'meta_dept_file mdf') === false) {
                $join_sql .= ' LEFT JOIN meta_dept_file mdf ON mdf.doc_id = d.doc_id ';
            }
            $placeholders = str_repeat('?,', count($clean_etypes) - 1) . '?';
            $where_clauses[] = "(msaf.event_type IN ($placeholders) OR msaf.activity_category IN ($placeholders) OR mdf.sub_file_type IN ($placeholders))";
            for ($i = 0; $i < 3; $i++) {
                foreach ($clean_etypes as $et) {
                    $params[] = $et;
                    $types .= 's';
                }
            }
        }
    }

    if (!empty($filters['type_key'])) {
        $meta_table = meta_get_table($filters['type_key']);
        $meta_fields = meta_get_fields($filters['type_key']);
        if ($meta_table && !empty($meta_fields)) {
            // Check if already joined (e.g. mdf)
            if (strpos($join_sql, $meta_table) === false) {
                 $join_sql .= " LEFT JOIN `$meta_table` mt ON mt.doc_id = d.doc_id ";
            } else {
                 if ($meta_table === 'meta_dept_file') {
                     $meta_table_alias = 'mdf';
                 } elseif ($meta_table === 'meta_student_activity_file') {
                     $meta_table_alias = 'msaf';
                 } else {
                     $meta_table_alias = 'mt';
                 }
            }
            $alias = isset($meta_table_alias) ? $meta_table_alias : 'mt';
            foreach ($meta_fields as $mf) {
                $meta_select .= ", $alias.`{$mf['name']}` as `meta_{$mf['name']}` ";
            }
        }
    }

    $where_sql = $where_clauses ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

    // Whitelist order column to prevent injection
    $allowed_orders = [
        'd.created_at DESC', 'd.created_at ASC',
        'event_date DESC', 'event_date ASC',
        'd.updated_at DESC', 'd.updated_at ASC',
        'd.title ASC', 'd.title DESC',
        'dt.label ASC', 'dt.label DESC',
    ];
    if (!in_array($order, $allowed_orders, true)) {
        $order = 'd.created_at DESC';
    }

    // Count total
    $total = 0;
    $count_sql = "SELECT COUNT(*) as total FROM documents d
                  JOIN document_types dt ON dt.type_id = d.type_id
                  $join_sql
                  $where_sql";
    $count_stmt = $conn->prepare($count_sql);
    if ($count_stmt) {
        if ($types && $params) {
            $count_stmt->bind_param($types, ...$params);
        }
        $count_stmt->execute();
        $c_res = $count_stmt->get_result();
        $total = (int)($c_res && ($crow = $c_res->fetch_assoc()) ? ($crow['total'] ?? 0) : 0);
        $count_stmt->close();
    }

    // Fetch rows
    $sql = "SELECT d.doc_id, d.type_id, d.uploaded_by, d.dept_id, d.academic_year_id, d.mentor_id, d.file_path,
                   d.title, d.status, d.current_step, d.rejection_reason,
                   d.created_at, d.updated_at,
                   dt.type_code as type_key, dt.label as type_label, dt.workflow_id as workflow_key,
                   u.full_name AS uploader_name,
                   dep.dept_name,
                   ay.year_label,
                   (CASE 
                       WHEN dt.type_code = 'student_activity_file' THEN (SELECT date_from FROM meta_student_activity_file WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'conference' THEN (SELECT from_date FROM meta_conference WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'journal' THEN (SELECT date_of_publication FROM meta_journal WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'patent' THEN (SELECT date_of_issue FROM meta_patent WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code IN ('fdp', 'fdp_attended') THEN (SELECT date_from FROM meta_fdp_attended WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'fdp_organised' THEN (SELECT date_from FROM meta_fdp_organised WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'conf_organised' THEN (SELECT date_from FROM meta_conf_organised WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'student_event' THEN (SELECT from_date FROM meta_student_event WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'student_body' THEN (SELECT from_date FROM meta_student_body WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'student_conference' THEN (SELECT from_date FROM meta_student_conference WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'student_journal' THEN (SELECT date_of_submission FROM meta_student_journal WHERE doc_id = d.doc_id LIMIT 1)
                       ELSE NULL
                   END) AS event_date
                   $meta_select
            FROM documents d
            JOIN document_types dt ON dt.type_id = d.type_id
            JOIN users u ON u.user_id = d.uploaded_by
            JOIN departments dep ON dep.dept_id = d.dept_id
            LEFT JOIN academic_years ay ON ay.year_id = d.academic_year_id
            $join_sql
            $where_sql
            ORDER BY $order
            LIMIT ? OFFSET ?";

    $params[] = $limit;
    $types .= 'i';
    $params[] = $offset;
    $types .= 'i';

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("doc_list prepare failed: " . $conn->error . " | SQL: " . $sql);
        return ['rows' => [], 'total' => 0];
    }
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    return ['rows' => $rows, 'total' => $total];
}

/**
 * Lists documents that are pending the given user's approval.
 *
 * Uses the workflow engine to determine which documents the user can act on,
 * based on their roles and department assignments.
 *
 * @param mysqli $conn
 * @param array  $auth  The $_SESSION['_fms_auth'] array
 * @param int    $limit
 * @param int    $offset
 * @return array ['rows' => [...], 'total' => int]
 */
function doc_list_pending_for_user(mysqli $conn, array $auth, int $limit = 50, int $offset = 0): array
{
    // Build a list of (role_id, dept_id) pairs the user holds
    $role_conditions = [];
    $params = [];
    $types = '';

    $user_id = (int)($auth['user_id'] ?? 0);
    $roles = isset($auth['roles']) && is_array($auth['roles']) ? $auth['roles'] : [];
    foreach ($roles as $role) {
        $role_id = is_array($role) ? (int)($role['role_id'] ?? 0) : (int)$role;
        $dept_id = is_array($role) ? (int)($role['dept_id'] ?? 0) : (int)($auth['dept_id'] ?? 0);

        // Match steps where this role is responsible
        // For department scope: also match dept
        // For global scope: any dept
        // And if mentor_id is set and this is the ROLE_FACULTY step, only that mentor sees it.
        $role_conditions[] = "(
            ws.responsible_role_id = ? 
            AND (ws.scope = 'global' OR d.dept_id = ?)
            AND (d.mentor_id IS NULL OR ws.responsible_role_id != " . ROLE_FACULTY . " OR d.mentor_id = ?)
        )";
        $params[] = $role_id;
        $types .= 'i';
        $params[] = $dept_id;
        $types .= 'i';
        $params[] = $user_id;
        $types .= 'i';
    }

    if (empty($role_conditions)) {
        return ['rows' => [], 'total' => 0];
    }

    $role_sql = '(' . implode(' OR ', $role_conditions) . ')';

    $count_sql = "SELECT COUNT(*) as total
                  FROM documents d
                  JOIN workflow_steps ws ON ws.step_id = d.current_step
                  WHERE d.status = 'pending' AND $role_sql";

    $count_stmt = $conn->prepare($count_sql);
    if (!$count_stmt) {
        error_log("doc_list_pending_for_user count prepare failed: " . $conn->error);
        return ['rows' => [], 'total' => 0];
    }
    if ($params) {
        $count_stmt->bind_param($types, ...$params);
    }
    $count_stmt->execute();
    $total = (int)$count_stmt->get_result()->fetch_assoc()['total'];
    $count_stmt->close();

    $sql = "SELECT d.doc_id, d.type_id, d.uploaded_by, d.dept_id, d.academic_year_id, d.mentor_id,
                   d.title, d.status, d.current_step,
                   d.created_at, d.updated_at,
                   dt.type_code as type_key, dt.label as type_label, dt.workflow_id as workflow_key,
                   u.full_name AS uploader_name,
                   dep.dept_name,
                   ay.year_label,
                   ws.step_label,
                   (CASE 
                       WHEN dt.type_code = 'student_activity_file' THEN (SELECT date_from FROM meta_student_activity_file WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'conference' THEN (SELECT from_date FROM meta_conference WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'journal' THEN (SELECT date_of_publication FROM meta_journal WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'patent' THEN (SELECT date_of_issue FROM meta_patent WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'fdp_attended' THEN (SELECT date_from FROM meta_fdp_attended WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'fdp_organised' THEN (SELECT date_from FROM meta_fdp_organised WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'conf_organised' THEN (SELECT date_from FROM meta_conf_organised WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'student_event' THEN (SELECT from_date FROM meta_student_event WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'student_body' THEN (SELECT from_date FROM meta_student_body WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'student_conference' THEN (SELECT from_date FROM meta_student_conference WHERE doc_id = d.doc_id LIMIT 1)
                       WHEN dt.type_code = 'student_journal' THEN (SELECT date_of_submission FROM meta_student_journal WHERE doc_id = d.doc_id LIMIT 1)
                       ELSE NULL
                   END) AS event_date
            FROM documents d
            JOIN document_types dt ON dt.type_id = d.type_id
            JOIN users u ON u.user_id = d.uploaded_by
            JOIN departments dep ON dep.dept_id = d.dept_id
            LEFT JOIN academic_years ay ON ay.year_id = d.academic_year_id
            JOIN workflow_steps ws ON ws.step_id = d.current_step
            WHERE d.status = 'pending' AND $role_sql
            ORDER BY d.created_at ASC
            LIMIT ? OFFSET ?";

    $params[] = $limit;
    $types .= 'i';
    $params[] = $offset;
    $types .= 'i';

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("doc_list_pending_for_user query prepare failed: " . $conn->error);
        return ['rows' => [], 'total' => 0];
    }
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();

    return ['rows' => $rows, 'total' => $total];
}

// ============================================================
// ACADEMIC YEARS
// ============================================================

/**
 * Lists all academic years.
 *
 * @param mysqli $conn
 * @return array
 */
function doc_get_academic_years(mysqli $conn): array
{
    $stmt = $conn->prepare("SELECT year_id, year_label FROM academic_years ORDER BY year_label DESC");
    if (!$stmt) {
        error_log("Failed to prepare statement in doc_get_academic_years: " . $conn->error);
        return [];
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $years = [];
    while ($row = $result->fetch_assoc()) {
        $years[] = $row;
    }
    $stmt->close();
    return $years;
}

/**
 * Gets the currently active academic year.
 *
 * @param mysqli $conn
 * @return array|null
 */
function doc_get_active_year(mysqli $conn): ?array
{
    $stmt = $conn->prepare("SELECT year_id, year_label FROM academic_years ORDER BY year_label DESC LIMIT 1");
    if (!$stmt) {
        error_log("Failed to prepare statement in doc_get_active_year: " . $conn->error);
        return null;
    }
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

// ============================================================
// DEPARTMENTS
// ============================================================

/**
 * Lists all departments.
 *
 * @param mysqli $conn
 * @return array
 */
function doc_get_departments(mysqli $conn): array
{
    $stmt = $conn->prepare("SELECT dept_id, dept_name FROM departments ORDER BY dept_name");
    if (!$stmt) {
        error_log("Failed to prepare statement in doc_get_departments: " . $conn->error);
        return [];
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $depts = [];
    while ($row = $result->fetch_assoc()) {
        $depts[] = $row;
    }
    $stmt->close();
    return $depts;
}

// ============================================================
// DOCUMENT PERMISSIONS & HISTORY
// ============================================================

/**
 * Checks if a user has permission to view a document.
 */
function doc_can_view(mysqli $conn, array $auth, array $doc): bool
{
    // Uploader can always view
    $uploader_id = $doc['uploaded_by'] ?? ($doc['created_by'] ?? null);
    if ($uploader_id !== null && (int)$uploader_id === (int)($auth['user_id'] ?? 0)) {
        return true;
    }
    
    // Check role-based access
    $roles = $auth['roles'] ?? [];
    foreach ($roles as $role) {
        $role_id = is_array($role) ? (int)($role['role_id'] ?? 0) : (int)$role;
        $dept_id = is_array($role) ? (int)($role['dept_id'] ?? 0) : (int)($auth['dept_id'] ?? 0);
        
        if (in_array($role_id, [ROLE_ADMIN, ROLE_RND_DEAN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true)) {
            return true;
        }
        
        // CRITICAL FIX: Students and Faculty can NEVER view department-wide files (unless they uploaded them, caught above)
        if ($role_id === ROLE_STUDENT || $role_id === ROLE_FACULTY) {
            continue;
        }

        if (isset($doc['dept_id']) && $dept_id === (int)$doc['dept_id']) {
            return true;
        }
    }
    
    // If they have pending actions on it, they can view it
    if (doc_can_approve($conn, $auth, $doc)) {
        return true;
    }
    
    // Check if they previously took action on this document
    $d_id = (int)($doc['doc_id'] ?? 0);
    $u_id = (int)($auth['user_id'] ?? 0);
    if ($d_id > 0 && $u_id > 0) {
        $stmt = $conn->prepare("SELECT 1 FROM document_actions WHERE doc_id = ? AND acted_by = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $d_id, $u_id);
            $stmt->execute();
            $acted = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($acted) {
                return true;
            }
        }
    }
    
    // Check if they are the assigned mentor (if applicable)
    if (!empty($doc['mentor_id']) && (int)$doc['mentor_id'] === (int)($auth['user_id'] ?? 0)) {
        return true;
    }
    
    return false;
}

/**
 * Checks if a user can approve a document (i.e. it is pending and their role matches the current step).
 */
function doc_can_approve(mysqli $conn, array $auth, array $doc): bool
{
    if ($doc['status'] !== 'pending' || empty($doc['current_step'])) {
        return false;
    }
    
    $step = wf_get_step($conn, (int)$doc['current_step']);
    if (!$step) {
        return false;
    }
    
    if (!empty($doc['mentor_id']) && (int)$step['responsible_role_id'] === ROLE_FACULTY) {
        if ((int)$auth['user_id'] !== (int)$doc['mentor_id']) {
            return false;
        }
    }
    
    return wf_can_user_act($step, $auth, $doc);
}

/**
 * Retrieves the action history for a document.
 */
function doc_get_history(mysqli $conn, int $doc_id): array
{
    if ($doc_id <= 0) {
        return [];
    }
    $stmt = $conn->prepare(
        "SELECT a.*, u.full_name as actor_name, s.step_label 
         FROM document_actions a
         JOIN users u ON a.acted_by = u.user_id
         LEFT JOIN workflow_steps s ON a.step_id = s.step_id
         WHERE a.doc_id = ? 
         ORDER BY a.acted_at ASC"
    );
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param('i', $doc_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    $stmt->close();
    return $history;
}

/**
 * Checks if a user is authorized to edit a document.
 * Must be the uploader, cannot be accepted, and if pending, must not have any partial approvals.
 */
function doc_can_edit(mysqli $conn, array $auth, array $doc): bool
{
    $uploader = (int)($doc['uploaded_by'] ?? ($doc['created_by'] ?? 0));
    $user_id  = (int)($auth['user_id'] ?? 0);
    if ($uploader !== $user_id || $user_id <= 0) {
        return false;
    }

    $status = strtolower($doc['status'] ?? '');
    if ($status === 'accepted') {
        return false;
    }

    if ($status === 'draft' || $status === 'rejected') {
        return true;
    }

    if ($status === 'pending') {
        $d_id = (int)($doc['doc_id'] ?? 0);
        $history = doc_get_history($conn, $d_id);
        foreach ($history as $h) {
            $act = strtolower($h['action'] ?? '');
            if ($act === 'approve' || $act === 'approved') {
                return false;
            }
        }
        return true;
    }

    return false;
}
