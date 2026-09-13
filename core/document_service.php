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
        "SELECT type_id, type_key, type_label, category, workflow_key, meta_table, form_template, is_active
         FROM document_types WHERE type_key = ? AND is_active = 1"
    );
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
        "SELECT type_id, type_key, type_label, category, workflow_key, meta_table, form_template, is_active
         FROM document_types WHERE type_id = ?"
    );
    $stmt->bind_param('i', $type_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/**
 * Lists all active document types, optionally filtered by category.
 *
 * @param mysqli      $conn
 * @param string|null $category  Optional filter (e.g. 'research', 'student', 'criteria')
 * @return array
 */
function doc_get_types(mysqli $conn, ?string $category = null): array
{
    if ($category !== null) {
        $stmt = $conn->prepare(
            "SELECT type_id, type_key, type_label, category, workflow_key
             FROM document_types WHERE is_active = 1 AND category = ?
             ORDER BY type_label"
        );
        $stmt->bind_param('s', $category);
    } else {
        $stmt = $conn->prepare(
            "SELECT type_id, type_key, type_label, category, workflow_key
             FROM document_types WHERE is_active = 1
             ORDER BY category, type_label"
        );
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $types = [];
    while ($row = $result->fetch_assoc()) {
        $types[] = $row;
    }
    $stmt->close();
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
    array  $files
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
            "INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, 'pending', NULL, NOW(), NOW())"
        );
        $type_id = (int)$doc_type['type_id'];
        $stmt->bind_param('iiiis', $type_id, $uploaded_by, $dept_id, $year_id, $title);
        $stmt->execute();
        $doc_id = $stmt->insert_id;
        $stmt->close();

        if (!$doc_id) {
            throw new RuntimeException('Failed to insert document record.');
        }

        // 4. Insert meta data
        $meta_result = doc_insert_meta($conn, $meta_table, $doc_id, $type_key, $meta_data);
        if (!$meta_result['success']) {
            throw new RuntimeException($meta_result['error']);
        }

        // 5. Store files
        $file_slots = meta_get_file_slots($type_key);
        foreach ($file_slots as $slot) {
            $slot_name = $slot['name'];

            // Check if file was uploaded for this slot
            if (!isset($files[$slot_name]) || $files[$slot_name]['error'] === UPLOAD_ERR_NO_FILE) {
                if ($slot['required']) {
                    throw new RuntimeException('Required file "' . $slot['label'] . '" was not uploaded.');
                }
                continue;
            }

            $store_result = file_store($files[$slot_name], $type_key, $slot_name);
            if (!$store_result['success']) {
                throw new RuntimeException('File upload failed for "' . $slot['label'] . '": ' . $store_result['error']);
            }

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

        $conn->commit();

        return ['success' => true, 'doc_id' => $doc_id, 'error' => null];

    } catch (Exception $e) {
        $conn->rollback();
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

    foreach ($field_defs as $field) {
        $name = $field['name'];
        if (array_key_exists($name, $meta_data)) {
            $columns[] = "`$name`";
            $placeholders[] = '?';

            $val = $meta_data[$name];
            if ($val === '' || $val === null) {
                $types_str .= 's';
                $values[] = null;
            } elseif ($field['type'] === 'number') {
                $types_str .= 's'; // store as string, DB will cast
                $values[] = $val;
            } else {
                $types_str .= 's';
                $values[] = $val;
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
        "SELECT d.doc_id, d.doc_type_id, d.uploaded_by, d.dept_id, d.year_id,
                d.title, d.status, d.current_step, d.rejection_reason,
                d.created_at, d.updated_at,
                dt.type_key, dt.type_label, dt.category, dt.workflow_key,
                u.full_name AS uploader_name,
                dep.dept_name,
                ay.year_label
         FROM documents d
         JOIN document_types dt ON dt.type_id = d.doc_type_id
         JOIN users u ON u.user_id = d.uploaded_by
         JOIN dept dep ON dep.dept_id = d.dept_id
         LEFT JOIN academic_years ay ON ay.year_id = d.year_id
         WHERE d.doc_id = ?"
    );
    $stmt->bind_param('i', $doc_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
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
    $stmt->bind_param('i', $doc_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
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

    if (!empty($filters['dept_id'])) {
        $where_clauses[] = 'd.dept_id = ?';
        $params[] = (int)$filters['dept_id'];
        $types .= 'i';
    }

    if (!empty($filters['doc_type_id'])) {
        $where_clauses[] = 'd.doc_type_id = ?';
        $params[] = (int)$filters['doc_type_id'];
        $types .= 'i';
    }

    if (!empty($filters['type_key'])) {
        $where_clauses[] = 'dt.type_key = ?';
        $params[] = $filters['type_key'];
        $types .= 's';
    }

    if (!empty($filters['category'])) {
        $where_clauses[] = 'dt.category = ?';
        $params[] = $filters['category'];
        $types .= 's';
    }

    if (!empty($filters['status'])) {
        $where_clauses[] = 'd.status = ?';
        $params[] = $filters['status'];
        $types .= 's';
    }

    if (!empty($filters['year_id'])) {
        $where_clauses[] = 'd.year_id = ?';
        $params[] = (int)$filters['year_id'];
        $types .= 'i';
    }

    if (!empty($filters['search'])) {
        $where_clauses[] = 'd.title LIKE ?';
        $params[] = '%' . $filters['search'] . '%';
        $types .= 's';
    }

    $where_sql = $where_clauses ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

    // Whitelist order column to prevent injection
    $allowed_orders = [
        'd.created_at DESC', 'd.created_at ASC',
        'd.updated_at DESC', 'd.updated_at ASC',
        'd.title ASC', 'd.title DESC',
        'dt.type_label ASC', 'dt.type_label DESC',
    ];
    if (!in_array($order, $allowed_orders, true)) {
        $order = 'd.created_at DESC';
    }

    // Count total
    $count_sql = "SELECT COUNT(*) as total FROM documents d
                  JOIN document_types dt ON dt.type_id = d.doc_type_id
                  $where_sql";
    $count_stmt = $conn->prepare($count_sql);
    if ($types && $params) {
        $count_stmt->bind_param($types, ...$params);
    }
    $count_stmt->execute();
    $total = (int)$count_stmt->get_result()->fetch_assoc()['total'];
    $count_stmt->close();

    // Fetch rows
    $sql = "SELECT d.doc_id, d.doc_type_id, d.uploaded_by, d.dept_id, d.year_id,
                   d.title, d.status, d.current_step, d.rejection_reason,
                   d.created_at, d.updated_at,
                   dt.type_key, dt.type_label, dt.category,
                   u.full_name AS uploader_name,
                   dep.dept_name,
                   ay.year_label
            FROM documents d
            JOIN document_types dt ON dt.type_id = d.doc_type_id
            JOIN users u ON u.user_id = d.uploaded_by
            JOIN dept dep ON dep.dept_id = d.dept_id
            LEFT JOIN academic_years ay ON ay.year_id = d.year_id
            $where_sql
            ORDER BY $order
            LIMIT ? OFFSET ?";

    $params[] = $limit;
    $types .= 'i';
    $params[] = $offset;
    $types .= 'i';

    $stmt = $conn->prepare($sql);
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

    foreach ($auth['roles'] as $role) {
        $role_id = (int)$role['role_id'];
        $dept_id = (int)$role['dept_id'];

        // Match steps where this role is responsible
        // For department scope: also match dept
        // For global scope: any dept
        $role_conditions[] = "(ws.responsible_role_id = ? AND (ws.scope = 'global' OR d.dept_id = ?))";
        $params[] = $role_id;
        $types .= 'i';
        $params[] = $dept_id;
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
    if ($params) {
        $count_stmt->bind_param($types, ...$params);
    }
    $count_stmt->execute();
    $total = (int)$count_stmt->get_result()->fetch_assoc()['total'];
    $count_stmt->close();

    $sql = "SELECT d.doc_id, d.doc_type_id, d.uploaded_by, d.dept_id, d.year_id,
                   d.title, d.status, d.current_step,
                   d.created_at, d.updated_at,
                   dt.type_key, dt.type_label, dt.category,
                   u.full_name AS uploader_name,
                   dep.dept_name,
                   ay.year_label,
                   ws.step_label
            FROM documents d
            JOIN document_types dt ON dt.type_id = d.doc_type_id
            JOIN users u ON u.user_id = d.uploaded_by
            JOIN dept dep ON dep.dept_id = d.dept_id
            LEFT JOIN academic_years ay ON ay.year_id = d.year_id
            JOIN workflow_steps ws ON ws.step_id = d.current_step
            WHERE d.status = 'pending' AND $role_sql
            ORDER BY d.created_at ASC
            LIMIT ? OFFSET ?";

    $params[] = $limit;
    $types .= 'i';
    $params[] = $offset;
    $types .= 'i';

    $stmt = $conn->prepare($sql);
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
    $stmt = $conn->prepare("SELECT year_id, year_label, is_active FROM academic_years ORDER BY year_label DESC");
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
    $stmt = $conn->prepare("SELECT year_id, year_label FROM academic_years WHERE is_active = 1 LIMIT 1");
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
    $stmt = $conn->prepare("SELECT dept_id, dept_name FROM dept ORDER BY dept_name");
    $stmt->execute();
    $result = $stmt->get_result();
    $depts = [];
    while ($row = $result->fetch_assoc()) {
        $depts[] = $row;
    }
    $stmt->close();
    return $depts;
}
