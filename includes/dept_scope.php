<?php
declare(strict_types=1);

/**
 * Maps file tables to the column that stores the faculty userid (for ownership / dept scoping).
 */
function fms_table_owner_column(string $table): ?string
{
    static $map = [
        'files' => 'UserName',
        'files5_1_1and2' => 'UserName',
        'files5_1_3' => 'username',
        'files5_1_4' => 'username',
        'files5_2_1' => 'username',
        'files5_2_2' => 'username',
        'fdps_tab' => 'username',
        'fdps_org_tab' => 'username',
        'conference_tab' => 'username',
        'published_tab' => 'username',
        'patents_table' => 'Username',
        's_journal_tab' => 'Username',
        's_conference_tab' => 'Username',
        's_events' => 'Username',
        's_bodies' => 'Username',
        'dept_files' => 'username',
        'files5_2_3' => 'username',
        'files5_3_1' => 'username',
        'files5_3_3' => 'username',
    ];
    return $map[$table] ?? null;
}

/**
 * SQL fragment: restrict rows to uploaders whose reg_tab.dept matches $dept (for HOD dashboards).
 */
function fms_hod_dept_exists_sql(mysqli $conn, string $table, string $dept): string
{
    if ($dept === '') {
        return ' AND 1=0';
    }
    $ownerCol = fms_table_owner_column($table);
    if ($ownerCol === null) {
        return ' AND 1=0';
    }
    $d = mysqli_real_escape_string($conn, $dept);
    $tb = '`' . str_replace('`', '``', $table) . '`';
    $oc = '`' . str_replace('`', '``', $ownerCol) . '`';
    return " AND EXISTS (SELECT 1 FROM reg_tab r_scope WHERE r_scope.userid = $tb.$oc AND r_scope.dept = '$d')";
}

/**
 * Restrict rows to the owning faculty userid (for "my uploads" style listings).
 */
function fms_faculty_owner_sql(mysqli $conn, string $table, string $userid): string
{
    $ownerCol = fms_table_owner_column($table);
    if ($ownerCol === null || $userid === '') {
        return ' AND 1=0';
    }
    $u = mysqli_real_escape_string($conn, $userid);
    $tb = '`' . str_replace('`', '``', $table) . '`';
    $oc = '`' . str_replace('`', '``', $ownerCol) . '`';
    return " AND $tb.$oc = '$u'";
}

/**
 * SQL fragment for admin/download.php style pages: admin & central coordinator = no extra filter;
 * faculty = owner column; HOD / dept coord / Jr = department via reg_tab.
 *
 * @param array{role: string, user_id: string, dept: string}|null $ctx
 */
function fms_download_scope_sql(mysqli $conn, string $table, ?array $ctx): string
{
    if (isset($_SESSION['admin'])) {
        return '';
    }
    if ($ctx === null) {
        return ' AND 1=0';
    }
    $role = $ctx['role'];
    if ($role === 'Central_Coordinator') {
        return '';
    }
    if ($role === 'Faculty') {
        return fms_faculty_owner_sql($conn, $table, $ctx['user_id']);
    }
    if ($role === 'HOD' || $role === 'Dept_Coordinator' || $role === 'Jr_Assistant') {
        $dept = $ctx['dept'] ?? '';
        if ($dept === '') {
            return ' AND 1=0';
        }
        return fms_hod_dept_exists_sql($conn, $table, $dept);
    }
    return ' AND 1=0';
}

/**
 * Whether the current user may delete/download a row in $table by primary key (mirrors listing rules).
 *
 * @param array{role: string, user_id: string, dept: string}|null $ctx
 */
function fms_download_row_allowed(mysqli $conn, string $table, int $id, ?array $ctx): bool
{
    if (isset($_SESSION['admin'])) {
        return true;
    }
    if ($ctx === null) {
        return false;
    }
    $role = $ctx['role'];
    if ($role === 'Central_Coordinator') {
        return true;
    }
    return fms_dashboard_row_in_scope($conn, $table, $id, $role, $ctx['user_id'], $ctx['dept'] ?? '');
}

/**
 * Resolves the faculty-upload table for criteria (same rules as admin download actions).
 */
function fms_upload_table_for_criteria(string $criteria, string $subCriteria): ?string
{
    require_once __DIR__ . '/constants.php';
    if (
        $criteria === '1' || $criteria === '2' || $criteria === '3' || $criteria === '4' || $criteria === '7' ||
        ($criteria === '5' && in_array($subCriteria, [CRIT_5_1_5, CRIT_5_3_2, CRIT_5_4_1, CRIT_5_4_2], true)) ||
        ($criteria === '6')
    ) {
        return TABLE_FILES;
    }
    if ($criteria === '5' && in_array($subCriteria, [CRIT_5_1_1, CRIT_5_1_2], true)) {
        return 'files5_1_1and2';
    }
    if ($criteria === '5' && $subCriteria === CRIT_5_1_3) {
        return 'files5_1_3';
    }
    if ($criteria === '5' && $subCriteria === CRIT_5_1_4) {
        return 'files5_1_4';
    }
    if ($criteria === '5' && $subCriteria === CRIT_5_2_1) {
        return 'files5_2_1';
    }
    if ($criteria === '5' && $subCriteria === CRIT_5_2_2) {
        return 'files5_2_2';
    }
    if ($criteria === '5' && $subCriteria === CRIT_5_2_3) {
        return 'files5_2_3';
    }
    if ($criteria === '5' && $subCriteria === CRIT_5_3_1) {
        return 'files5_3_1';
    }
    if ($criteria === '5' && $subCriteria === CRIT_5_3_3) {
        return 'files5_3_3';
    }
    return null;
}

/**
 * Normalize to a uploads/... relative path for DB lookup.
 */
function fms_normalize_uploads_relative_path(string $raw): string
{
    $raw = str_replace('\\', '/', trim($raw));
    if (preg_match('#(uploads/.+)#i', $raw, $m)) {
        return $m[1];
    }
    return ltrim($raw, '/');
}

/**
 * Returns true if $rawPath matches a row the user may access (faculty owner, or HOD/Jr/Dept via dept scope).
 */
function fms_verify_file_path_access(mysqli $conn, string $rawPath, string $role, string $user_id, string $dept): bool
{
    $rel = fms_normalize_uploads_relative_path($rawPath);
    $variants = [$rel];
    if (strpos($rel, 'uploads/') === 0) {
        $tail = substr($rel, strlen('uploads/'));
        $variants[] = $tail;
        $variants[] = '../uploads/' . $tail;
        $variants[] = '../../uploads/' . $tail;
    } else {
        $variants[] = 'uploads/' . $rel;
        $variants[] = '../uploads/' . $rel;
        $variants[] = '../../uploads/' . $rel;
    }
    $variants = array_values(array_unique($variants));

    $tables = [
        'files', 'files5_1_1and2', 'files5_1_3', 'files5_1_4', 'files5_2_1', 'files5_2_2', 'files5_2_3',
        'files5_3_1', 'files5_3_3', 'fdps_tab', 'fdps_org_tab', 'conference_tab', 'published_tab', 'patents_table',
        's_journal_tab', 's_conference_tab', 's_events', 's_bodies', 'dept_files',
    ];

    foreach ($tables as $table) {
        if (fms_table_owner_column($table) === null) {
            continue;
        }
        $pk = fms_table_pk_column($table);
        $placeholders = implode(',', array_fill(0, count($variants), '?'));
        $sql = "SELECT `$pk` FROM `$table` WHERE file_path IN ($placeholders) LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            continue;
        }
        $types = str_repeat('s', count($variants));
        $stmt->bind_param($types, ...$variants);
        if (!$stmt->execute()) {
            $stmt->close();
            continue;
        }
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        if (!$row) {
            continue;
        }
        $id = (int) ($row[$pk] ?? $row['ID'] ?? $row['id'] ?? 0);
        if ($id < 1) {
            continue;
        }
        return fms_dashboard_row_in_scope($conn, $table, $id, $role, $user_id, $dept);
    }
    return false;
}

/**
 * Maps current session to role context for access checks (excludes $_SESSION['admin'] — handle that separately).
 *
 * @return array{role: string, user_id: string, dept: string}|null
 */
function fms_session_role_context(mysqli $conn): ?array
{
    if (isset($_SESSION['username'])) {
        return ['role' => 'Faculty', 'user_id' => (string) $_SESSION['username'], 'dept' => ''];
    }
    if (isset($_SESSION['a_username'])) {
        $uid = (string) $_SESSION['a_username'];
        $dept = '';
        $stmt = $conn->prepare('SELECT department FROM reg_dept_cord WHERE userid = ?');
        $stmt->bind_param('s', $uid);
        $stmt->execute();
        if ($r = $stmt->get_result()->fetch_assoc()) {
            $dept = (string) $r['department'];
        }
        $stmt->close();
        return ['role' => 'Dept_Coordinator', 'user_id' => $uid, 'dept' => $dept];
    }
    if (isset($_SESSION['j_username'])) {
        $uid = (string) $_SESSION['j_username'];
        $dept = '';
        $stmt = $conn->prepare('SELECT department FROM reg_jr_assistant WHERE userid = ?');
        $stmt->bind_param('s', $uid);
        $stmt->execute();
        if ($r = $stmt->get_result()->fetch_assoc()) {
            $dept = (string) $r['department'];
        }
        $stmt->close();
        return ['role' => 'Jr_Assistant', 'user_id' => $uid, 'dept' => $dept];
    }
    if (isset($_SESSION['h_username'])) {
        if ($_SESSION['h_username'] === 'central') {
            return ['role' => 'Central_Coordinator', 'user_id' => 'central', 'dept' => ''];
        }
        return [
            'role' => 'HOD',
            'user_id' => (string) $_SESSION['h_username'],
            'dept' => (string) ($_SESSION['dept'] ?? ''),
        ];
    }
    if (isset($_SESSION['c_cord']) || isset($_SESSION['c_username']) || isset($_SESSION['cri_username'])) {
        return ['role' => 'Central_Coordinator', 'user_id' => 'central', 'dept' => ''];
    }
    return null;
}

/**
 * Returns true if $fileId in $table is allowed for approve/reject/re-upload under $role.
 */
function fms_table_pk_column(string $table): string
{
    if ($table === 's_events' || $table === 's_bodies') {
        return 'ID';
    }
    return 'id';
}

function fms_dashboard_row_in_scope(
    mysqli $conn,
    string $table,
    int $fileId,
    string $role,
    string $user_id,
    string $dept
): bool {
    $ownerCol = fms_table_owner_column($table);
    if ($ownerCol === null) {
        return false;
    }
    $pk = fms_table_pk_column($table);

    if ($role === 'Faculty') {
        $stmt = $conn->prepare("SELECT `$pk` FROM `$table` WHERE `$pk` = ? AND `$ownerCol` = ? LIMIT 1");
        $stmt->bind_param('is', $fileId, $user_id);
        $stmt->execute();
        $ok = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $ok;
    }

    if ($table === 'dept_files') {
        if ($role === 'HOD' && $dept !== '') {
            $stmt = $conn->prepare('SELECT id FROM dept_files WHERE id = ? AND dept = ? LIMIT 1');
            $stmt->bind_param('is', $fileId, $dept);
            $stmt->execute();
            $ok = $stmt->get_result()->num_rows > 0;
            $stmt->close();
            return $ok;
        }
        if ($role === 'Dept_Coordinator' || $role === 'Jr_Assistant') {
            $stmt = $conn->prepare('SELECT id FROM dept_files WHERE id = ? AND username = ? LIMIT 1');
            $stmt->bind_param('is', $fileId, $user_id);
            $stmt->execute();
            $ok = $stmt->get_result()->num_rows > 0;
            $stmt->close();
            return $ok;
        }
        return false;
    }

    if ($role === 'HOD' || $role === 'Dept_Coordinator' || $role === 'Jr_Assistant') {
        if ($dept === '') {
            return false;
        }
        $stmt = $conn->prepare(
            "SELECT t.`$pk` FROM `$table` t WHERE t.`$pk` = ? AND EXISTS (SELECT 1 FROM reg_tab r_scope WHERE r_scope.userid = t.`$ownerCol` AND r_scope.dept = ?) LIMIT 1"
        );
        $stmt->bind_param('is', $fileId, $dept);
        $stmt->execute();
        $ok = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $ok;
    }

    if ($role === 'Central_Coordinator') {
        return true;
    }

    return false;
}
