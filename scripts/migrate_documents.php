<?php
/**
 * FMS Phase 4C: Legacy Document Data Migration
 *
 * Migrates document data from legacy tables into the unified
 * documents + document_files + meta_* schema.
 *
 * - Maps legacy username → users.user_id
 * - Maps legacy branch/dept → dept.dept_id
 * - Maps legacy year → academic_years.year_id
 * - Maps legacy status → new status values
 * - Preserves existing file paths (files are NOT moved)
 *
 * Usage: php scripts/migrate_documents.php
 */

require_once __DIR__ . '/../includes/connection.php';
require_once __DIR__ . '/../core/constants.php';
require_once __DIR__ . '/../core/meta_registry.php';

echo "=== FMS Phase 4C: Legacy Document Data Migration ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// ============================================================
// HELPER FUNCTIONS
// ============================================================

/**
 * Resolve a legacy username to users.user_id.
 * Tries: exact match on email, then full_name, then partial match.
 */
function resolve_user_id(mysqli $conn, string $username): ?int
{
    if (empty(trim($username))) return null;

    // Try email match first
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row) return (int)$row['user_id'];

    // Try full_name match
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE full_name = ? LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row) return (int)$row['user_id'];

    // Try LIKE match on email prefix
    $like = $username . '%';
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email LIKE ? LIMIT 1");
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row) return (int)$row['user_id'];

    return null;
}

/**
 * Resolve a legacy branch/dept name to dept.dept_id.
 */
function resolve_dept_id(mysqli $conn, string $dept_name): ?int
{
    if (empty(trim($dept_name))) return null;

    $stmt = $conn->prepare("SELECT dept_id FROM dept WHERE dept_name = ? LIMIT 1");
    $stmt->bind_param('s', $dept_name);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row) return (int)$row['dept_id'];

    // Try case-insensitive
    $stmt = $conn->prepare("SELECT dept_id FROM dept WHERE LOWER(dept_name) = LOWER(?) LIMIT 1");
    $stmt->bind_param('s', $dept_name);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row) return (int)$row['dept_id'];

    return null;
}

/**
 * Resolve a legacy year string to academic_years.year_id.
 */
function resolve_year_id(mysqli $conn, string $year_label): ?int
{
    if (empty(trim($year_label))) return null;

    $stmt = $conn->prepare("SELECT year_id FROM academic_years WHERE year_label = ? LIMIT 1");
    $stmt->bind_param('s', $year_label);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ? (int)$row['year_id'] : null;
}

/**
 * Map legacy status to new status values.
 */
function map_status(string $legacy_status): string
{
    $legacy_status = trim($legacy_status);
    $map = [
        'Pending HOD'              => 'pending',
        'Pending Dept Coordinator'  => 'pending',
        'Pending Central Coordinator' => 'pending',
        'Accepted'                 => 'accepted',
        'Rejected'                 => 'rejected',
        'Rejected by HOD'          => 'rejected',
        'Rejected by Dept Coordinator' => 'rejected',
        ''                         => 'pending',
    ];
    return $map[$legacy_status] ?? 'pending';
}

/**
 * Get the first workflow step_id for 'department_standard'.
 */
function get_first_step_id(mysqli $conn, string $workflow_key): ?int
{
    $stmt = $conn->prepare(
        "SELECT ws.step_id FROM workflow_steps ws
         JOIN workflows w ON w.workflow_id = ws.workflow_id
         WHERE w.workflow_key = ? ORDER BY ws.step_order ASC LIMIT 1"
    );
    $stmt->bind_param('s', $workflow_key);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ? (int)$row['step_id'] : null;
}

/**
 * Insert a document and return doc_id.
 */
function insert_document(mysqli $conn, int $type_id, int $user_id, int $dept_id, ?int $year_id, string $title, string $status, ?int $step_id, ?string $rejection_reason, string $created_at): int
{
    $stmt = $conn->prepare(
        "INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, rejection_reason, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('iiiissisis', $type_id, $user_id, $dept_id, $year_id, $title, $status, $step_id, $rejection_reason, $created_at, $created_at);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    return $id;
}

/**
 * Insert a file record for a document.
 */
function insert_file(mysqli $conn, int $doc_id, string $label, string $file_path): void
{
    if (empty(trim($file_path))) return;
    $original = basename($file_path);
    $stmt = $conn->prepare(
        "INSERT INTO document_files (doc_id, file_label, original_name, stored_name, file_path, mime_type, file_size)
         VALUES (?, ?, ?, ?, ?, 'application/octet-stream', 0)"
    );
    $stmt->bind_param('issss', $doc_id, $label, $original, $original, $file_path);
    $stmt->execute();
    $stmt->close();
}

// ============================================================
// Get type IDs and first steps
// ============================================================
$type_ids = [];
$res = $conn->query("SELECT type_id, type_key FROM document_types");
while ($r = $res->fetch_assoc()) {
    $type_ids[$r['type_key']] = (int)$r['type_id'];
}

$first_step_dept = get_first_step_id($conn, 'department_standard');
$first_step_central = get_first_step_id($conn, 'central');

echo "First step (department_standard): $first_step_dept\n";
echo "First step (central): $first_step_central\n\n";

$total_migrated = 0;
$errors = [];

// ============================================================
// MIGRATE: published_tab → journal
// ============================================================
echo "--- published_tab → journal ---\n";
$rows = $conn->query("SELECT * FROM published_tab");
$count = 0;
while ($r = $rows->fetch_assoc()) {
    $user_id = resolve_user_id($conn, $r['username']);
    $dept_id = resolve_dept_id($conn, $r['branch']);
    $year_id = resolve_year_id($conn, $r['year'] ?? '');

    if (!$user_id) { $errors[] = "published_tab id={$r['id']}: cannot resolve user '{$r['username']}'"; continue; }
    if (!$dept_id) { $errors[] = "published_tab id={$r['id']}: cannot resolve dept '{$r['branch']}'"; continue; }

    $status = map_status($r['status'] ?? '');
    $step_id = ($status === 'pending') ? $first_step_dept : (($status === 'accepted') ? null : $first_step_dept);

    $doc_id = insert_document($conn, $type_ids['journal'], $user_id, $dept_id, $year_id,
        $r['paper_title'] ?: 'Journal Paper', $status, $step_id, $r['rejection_reason'] ?? null, $r['submission_time'] ?? date('Y-m-d H:i:s'));

    // Insert meta
    $conn->query("INSERT INTO meta_journal (doc_id, paper_title, journal_name, authors, issn_no, volume_no, issue_no, page_no, doi, jcr_quartile, scopus_quartile, publication_link, indexing, date_of_publication, impact_factor, quality_factor, payment)
        VALUES ($doc_id, " . q($conn, $r['paper_title']) . ", " . q($conn, $r['journal_name']) . ", " . q($conn, $r['authors']) . ", " . q($conn, $r['issn_no']) . ", " . q($conn, $r['volume_no']) . ", " . q($conn, $r['issue_no']) . ", " . q($conn, $r['page_no']) . ", " . q($conn, $r['doi']) . ", " . q($conn, $r['jcr_quartile']) . ", " . q($conn, $r['scopus_quartile']) . ", " . q($conn, $r['publication_link']) . ", " . q($conn, $r['indexing']) . ", " . qd($conn, $r['date_of_submission'] ?? null) . ", " . qn($r['impact_factor'] ?? null) . ", " . qn($r['quality_factor'] ?? null) . ", " . q($conn, $r['payment']) . ")");

    if (!empty($r['paper_file'])) insert_file($conn, $doc_id, 'paper_file', $r['paper_file']);
    $count++;
}
echo "  Migrated: $count rows\n";
$total_migrated += $count;

// ============================================================
// MIGRATE: conference_tab → conference
// ============================================================
echo "--- conference_tab → conference ---\n";
$rows = $conn->query("SELECT * FROM conference_tab");
$count = 0;
while ($r = $rows->fetch_assoc()) {
    $user_id = resolve_user_id($conn, $r['username']);
    $dept_id = resolve_dept_id($conn, $r['branch']);
    $year_id = resolve_year_id($conn, $r['year'] ?? '');
    if (!$user_id) { $errors[] = "conference_tab id={$r['id']}: cannot resolve user"; continue; }
    if (!$dept_id) { $errors[] = "conference_tab id={$r['id']}: cannot resolve dept"; continue; }

    $status = map_status($r['status'] ?? '');
    $step_id = ($status === 'pending') ? $first_step_dept : (($status === 'accepted') ? null : $first_step_dept);

    $doc_id = insert_document($conn, $type_ids['conference'], $user_id, $dept_id, $year_id,
        $r['paper_title'] ?: 'Conference Paper', $status, $step_id, $r['rejection_reason'] ?? null, $r['submission_time'] ?? date('Y-m-d H:i:s'));

    $conn->query("INSERT INTO meta_conference (doc_id, paper_title, conference_name, authors, paper_type, volume_no, issue_no, page_no, indexing, publication_link, issn_no, doi, from_date, to_date, organised_by, location)
        VALUES ($doc_id, " . q($conn, $r['paper_title']) . ", " . q($conn, $r['conference_name'] ?? $r['published_paper_name'] ?? '') . ", " . q($conn, $r['authors'] ?? '') . ", " . q($conn, $r['paper_type']) . ", " . q($conn, $r['volume_no']) . ", " . q($conn, $r['issue_no']) . ", " . q($conn, $r['page_no']) . ", " . q($conn, $r['indexing']) . ", " . q($conn, $r['publication_link']) . ", " . q($conn, $r['issn_no']) . ", " . q($conn, $r['doi']) . ", " . qd($conn, $r['from_date']) . ", " . qd($conn, $r['to_date']) . ", " . q($conn, $r['organised_by']) . ", " . q($conn, $r['location']) . ")");

    if (!empty($r['certificate_path'])) insert_file($conn, $doc_id, 'certificate', $r['certificate_path']);
    if (!empty($r['paper_file_path'])) insert_file($conn, $doc_id, 'paper_file', $r['paper_file_path']);
    $count++;
}
echo "  Migrated: $count rows\n";
$total_migrated += $count;

// ============================================================
// MIGRATE: patents_table → patent
// ============================================================
echo "--- patents_table → patent ---\n";
$rows = $conn->query("SELECT * FROM patents_table");
$count = 0;
while ($r = $rows->fetch_assoc()) {
    $user_id = resolve_user_id($conn, $r['Username']);
    $dept_id = resolve_dept_id($conn, $r['branch']);
    $year_id = resolve_year_id($conn, $r['year'] ?? '');
    if (!$user_id) { $errors[] = "patents_table id={$r['id']}: cannot resolve user"; continue; }
    if (!$dept_id) { $errors[] = "patents_table id={$r['id']}: cannot resolve dept"; continue; }

    $status = map_status($r['status'] ?? '');
    $step_id = ($status === 'pending') ? $first_step_dept : (($status === 'accepted') ? null : $first_step_dept);

    $doc_id = insert_document($conn, $type_ids['patent'], $user_id, $dept_id, $year_id,
        $r['patent_title'] ?: 'Patent', $status, $step_id, $r['rejection_reason'] ?? null, $r['submission_time'] ?? date('Y-m-d H:i:s'));

    $conn->query("INSERT INTO meta_patent (doc_id, patent_title, patent_no, patent_type, date_of_issue, inventors)
        VALUES ($doc_id, " . q($conn, $r['patent_title']) . ", " . q($conn, $r['patent_no']) . ", " . q($conn, $r['type']) . ", " . qd($conn, $r['date_of_issue']) . ", " . q($conn, $r['investors']) . ")");

    if (!empty($r['patent_file'])) insert_file($conn, $doc_id, 'patent_file', $r['patent_file']);
    $count++;
}
echo "  Migrated: $count rows\n";
$total_migrated += $count;

// ============================================================
// MIGRATE: fdps_tab → fdp_attended
// ============================================================
echo "--- fdps_tab → fdp_attended ---\n";
$rows = $conn->query("SELECT * FROM fdps_tab");
$count = 0;
while ($r = $rows->fetch_assoc()) {
    $user_id = resolve_user_id($conn, $r['username']);
    $dept_id = resolve_dept_id($conn, $r['branch']);
    $year_id = resolve_year_id($conn, $r['year'] ?? '');
    if (!$user_id) { $errors[] = "fdps_tab id={$r['id']}: cannot resolve user"; continue; }
    if (!$dept_id) { $errors[] = "fdps_tab id={$r['id']}: cannot resolve dept"; continue; }

    $status = map_status($r['status'] ?? '');
    $step_id = ($status === 'pending') ? $first_step_dept : (($status === 'accepted') ? null : $first_step_dept);

    $doc_id = insert_document($conn, $type_ids['fdp_attended'], $user_id, $dept_id, $year_id,
        $r['title'] ?: 'FDP Attended', $status, $step_id, $r['rejection_reason'] ?? null, $r['submission_time'] ?? date('Y-m-d H:i:s'));

    $conn->query("INSERT INTO meta_fdp_attended (doc_id, mode, date_from, date_to, organised_by, location)
        VALUES ($doc_id, " . q($conn, $r['mode']) . ", " . qd($conn, $r['date_from']) . ", " . qd($conn, $r['date_to']) . ", " . q($conn, $r['organised_by']) . ", " . q($conn, $r['location']) . ")");

    if (!empty($r['certificate'])) insert_file($conn, $doc_id, 'certificate', $r['certificate']);
    if (!empty($r['brochure'])) insert_file($conn, $doc_id, 'brochure', $r['brochure']);
    $count++;
}
echo "  Migrated: $count rows\n";
$total_migrated += $count;

// ============================================================
// MIGRATE: fdps_org_tab → fdp_organised
// ============================================================
echo "--- fdps_org_tab → fdp_organised ---\n";
$rows = $conn->query("SELECT * FROM fdps_org_tab");
$count = 0;
while ($r = $rows->fetch_assoc()) {
    $user_id = resolve_user_id($conn, $r['username']);
    $dept_id = resolve_dept_id($conn, $r['branch']);
    $year_id = resolve_year_id($conn, $r['year'] ?? '');
    if (!$user_id) { $errors[] = "fdps_org_tab id={$r['id']}: cannot resolve user"; continue; }
    if (!$dept_id) { $errors[] = "fdps_org_tab id={$r['id']}: cannot resolve dept"; continue; }

    $status = map_status($r['status'] ?? '');
    $step_id = ($status === 'pending') ? $first_step_dept : (($status === 'accepted') ? null : $first_step_dept);

    $doc_id = insert_document($conn, $type_ids['fdp_organised'], $user_id, $dept_id, $year_id,
        $r['title'] ?: 'FDP Organised', $status, $step_id, $r['rejection_reason'] ?? null, $r['submission_time'] ?? date('Y-m-d H:i:s'));

    $conn->query("INSERT INTO meta_fdp_organised (doc_id, date_from, date_to, organised_by, location)
        VALUES ($doc_id, " . qd($conn, $r['date_from']) . ", " . qd($conn, $r['date_to']) . ", " . q($conn, $r['organised_by']) . ", " . q($conn, $r['location']) . ")");

    $file_fields = ['certificate','brochure','fdp_schedule_invitation','attendance_forms','feedback_forms','fdp_report','photo1','photo2','photo3'];
    foreach ($file_fields as $ff) {
        if (!empty($r[$ff])) insert_file($conn, $doc_id, $ff, $r[$ff]);
    }
    if (!empty($r['merged_file'])) insert_file($conn, $doc_id, 'merged_file', $r['merged_file']);
    $count++;
}
echo "  Migrated: $count rows\n";
$total_migrated += $count;

// ============================================================
// MIGRATE: conf_org_tab → conf_organised
// ============================================================
echo "--- conf_org_tab → conf_organised ---\n";
$rows = $conn->query("SELECT * FROM conf_org_tab");
$count = 0;
while ($r = $rows->fetch_assoc()) {
    $user_id = resolve_user_id($conn, $r['username']);
    $dept_id = resolve_dept_id($conn, $r['branch']);
    $year_id = resolve_year_id($conn, $r['year'] ?? '');
    if (!$user_id) { $errors[] = "conf_org_tab id={$r['id']}: cannot resolve user"; continue; }
    if (!$dept_id) { $errors[] = "conf_org_tab id={$r['id']}: cannot resolve dept"; continue; }

    $status = map_status($r['status'] ?? '');
    $step_id = ($status === 'pending') ? $first_step_dept : (($status === 'accepted') ? null : $first_step_dept);

    $doc_id = insert_document($conn, $type_ids['conf_organised'], $user_id, $dept_id, $year_id,
        $r['title'] ?: 'Conference Organised', $status, $step_id, $r['rejection_reason'] ?? null, $r['submission_time'] ?? date('Y-m-d H:i:s'));

    $conn->query("INSERT INTO meta_conf_organised (doc_id, mode, date_from, date_to, organised_by, location)
        VALUES ($doc_id, " . q($conn, $r['mode']) . ", " . qd($conn, $r['date_from']) . ", " . qd($conn, $r['date_to']) . ", " . q($conn, $r['organised_by']) . ", " . q($conn, $r['location']) . ")");

    $file_fields = ['brochure','fdp_schedule_invitation','attendance_forms','feedback_forms','fdp_report','photo1','photo2','photo3'];
    foreach ($file_fields as $ff) {
        if (!empty($r[$ff])) insert_file($conn, $doc_id, $ff, $r[$ff]);
    }
    $count++;
}
echo "  Migrated: $count rows\n";
$total_migrated += $count;

// ============================================================
// MIGRATE: dept_files → dept_file
// ============================================================
echo "--- dept_files → dept_file ---\n";
$rows = $conn->query("SELECT * FROM dept_files");
$count = 0;
while ($r = $rows->fetch_assoc()) {
    $user_id = resolve_user_id($conn, $r['username']);
    $dept_id = resolve_dept_id($conn, $r['dept']);
    $year_id = resolve_year_id($conn, $r['academic_year'] ?? '');
    if (!$user_id) { $errors[] = "dept_files id={$r['id']}: cannot resolve user '{$r['username']}'"; continue; }
    if (!$dept_id) { $errors[] = "dept_files id={$r['id']}: cannot resolve dept '{$r['dept']}'"; continue; }

    $status = map_status($r['status'] ?? '');
    $step_id = ($status === 'pending') ? $first_step_dept : (($status === 'accepted') ? null : $first_step_dept);

    $doc_id = insert_document($conn, $type_ids['dept_file'], $user_id, $dept_id, $year_id,
        $r['file_name'] ?: 'Department File', $status, $step_id, $r['rejection_reason'] ?? null, $r['uploaded_at'] ?? date('Y-m-d H:i:s'));

    $conn->query("INSERT INTO meta_dept_file (doc_id, file_type, sub_file_type, semester, review_period, study_year, meeting_no)
        VALUES ($doc_id, " . q($conn, $r['file_type']) . ", " . q($conn, $r['sub_file_type']) . ", " . qn($r['semester'] ?? null) . ", " . q($conn, $r['review_period']) . ", " . q($conn, $r['study_year']) . ", " . q($conn, $r['meeting_no']) . ")");

    if (!empty($r['file_path'])) insert_file($conn, $doc_id, 'document_file', $r['file_path']);
    $count++;
}
echo "  Migrated: $count rows\n";
$total_migrated += $count;

// ============================================================
// MIGRATE: s_journal_tab → student_journal
// ============================================================
echo "--- s_journal_tab → student_journal ---\n";
$rows = $conn->query("SELECT * FROM s_journal_tab");
$count = 0;
while ($r = $rows->fetch_assoc()) {
    $user_id = resolve_user_id($conn, $r['Username']);
    $dept_id = resolve_dept_id($conn, $r['branch']);
    $year_id = resolve_year_id($conn, $r['acd_year'] ?? '');
    if (!$user_id) { $errors[] = "s_journal_tab id={$r['id']}: cannot resolve user"; continue; }
    if (!$dept_id) { $errors[] = "s_journal_tab id={$r['id']}: cannot resolve dept"; continue; }

    $status = map_status($r['status'] ?? '');
    $step_id = ($status === 'pending') ? $first_step_dept : (($status === 'accepted') ? null : $first_step_dept);

    $doc_id = insert_document($conn, $type_ids['student_journal'], $user_id, $dept_id, $year_id,
        $r['paper_title'] ?: 'Student Journal', $status, $step_id, $r['rejection_reason'] ?? null, $r['submission_time'] ?? date('Y-m-d H:i:s'));

    $conn->query("INSERT INTO meta_student_journal (doc_id, paper_title, journal_name, indexing, date_of_submission, impact_factor, quality_factor, payment)
        VALUES ($doc_id, " . q($conn, $r['paper_title']) . ", " . q($conn, $r['journal_name']) . ", " . q($conn, $r['indexing']) . ", " . qd($conn, $r['date_of_submission']) . ", " . qn($r['impact_factor'] ?? null) . ", " . qn($r['quality_factor'] ?? null) . ", " . q($conn, $r['payment']) . ")");

    if (!empty($r['paper_file'])) insert_file($conn, $doc_id, 'paper_file', $r['paper_file']);
    $count++;
}
echo "  Migrated: $count rows\n";
$total_migrated += $count;

// ============================================================
// SUMMARY
// ============================================================
echo "\n=== MIGRATION SUMMARY ===\n";
echo "Total documents migrated: $total_migrated\n";

if (!empty($errors)) {
    echo "\nERRORS (" . count($errors) . "):\n";
    foreach ($errors as $e) {
        echo "  - $e\n";
    }
}

// Verification
echo "\nVerification:\n";
$doc_count = (int)$conn->query("SELECT COUNT(*) as c FROM documents")->fetch_assoc()['c'];
$file_count = (int)$conn->query("SELECT COUNT(*) as c FROM document_files")->fetch_assoc()['c'];
echo "  documents: $doc_count rows\n";
echo "  document_files: $file_count rows\n";

// Per-type breakdown
$breakdown = $conn->query("SELECT dt.type_key, COUNT(*) as cnt FROM documents d JOIN document_types dt ON dt.type_id = d.doc_type_id GROUP BY dt.type_key ORDER BY dt.type_key");
echo "\n  Per-type breakdown:\n";
while ($r = $breakdown->fetch_assoc()) {
    echo "    {$r['type_key']}: {$r['cnt']}\n";
}

// Status breakdown
$status_q = $conn->query("SELECT status, COUNT(*) as cnt FROM documents GROUP BY status");
echo "\n  Status breakdown:\n";
while ($r = $status_q->fetch_assoc()) {
    echo "    {$r['status']}: {$r['cnt']}\n";
}

echo "\nPhase 4C migration " . (empty($errors) ? "COMPLETE" : "COMPLETE WITH ERRORS") . ".\n";

// ============================================================
// SQL QUOTING HELPERS
// ============================================================

function q(mysqli $conn, ?string $val): string
{
    if ($val === null || $val === '') return "NULL";
    return "'" . $conn->real_escape_string($val) . "'";
}

function qd(mysqli $conn, ?string $val): string
{
    if ($val === null || $val === '' || $val === '0000-00-00') return "NULL";
    return "'" . $conn->real_escape_string($val) . "'";
}

function qn(?string $val): string
{
    if ($val === null || $val === '') return "NULL";
    return is_numeric($val) ? $val : "NULL";
}
