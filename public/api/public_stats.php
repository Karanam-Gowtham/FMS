<?php
require_once __DIR__ . '/../../core/bootstrap.php';

header('Content-Type: application/json');

global $conn;

$response = [
    'status' => 'success',
    'data' => []
];

// Filters
$year_filter = isset($_GET['year']) && $_GET['year'] !== 'all' ? $_GET['year'] : null;
$dept_filter = isset($_GET['dept_id']) && $_GET['dept_id'] !== 'all' ? (int)$_GET['dept_id'] : null;
$cat_filter = isset($_GET['category']) && $_GET['category'] !== 'all' ? $_GET['category'] : null;
$focus_filter = isset($_GET['type']) && $_GET['type'] !== 'all' ? $_GET['type'] : null;

// Base JOINs for documents
$doc_joins = "
    JOIN users u ON d.uploaded_by = u.user_id 
    JOIN user_roles ur ON u.user_id = ur.user_id 
    JOIN document_types dt ON d.type_id = dt.type_id
";

// Base WHERE condition for accepted documents
$doc_where = "d.status = 'accepted'";

if ($year_filter) {
    $years = explode(',', $year_filter);
    $year_sql = implode(',', array_map(function($y) { return (int)substr(trim($y), 0, 4); }, $years));
    if (!empty($year_sql)) $doc_where .= " AND YEAR(d.created_at) IN ($year_sql)";
}

if ($dept_filter) {
    $depts = explode(',', $dept_filter);
    $dept_sql = implode(',', array_map('intval', $depts));
    if (!empty($dept_sql)) $doc_where .= " AND ur.dept_id IN ($dept_sql)";
}

// 1. Apply Focus Filter (Multi-Select)
if ($focus_filter) {
    $foci = explode(',', $focus_filter);
    $foci_sql = implode(',', array_map(function($f) { global $conn; return "'" . $conn->real_escape_string(trim($f)) . "'"; }, $foci));
    if (!empty($foci_sql)) {
        $doc_where .= " AND dt.type_code IN ($foci_sql)";
    }
}

// 2. Apply Category Filter (Card Click)
if ($cat_filter === 'patents') {
    $doc_where .= " AND dt.type_code = 'patent'";
} elseif ($cat_filter === 'pubs') {
    $doc_where .= " AND (dt.type_code IN ('journal', 'conference'))";
} elseif ($cat_filter === 'fdps') {
    $doc_where .= " AND (dt.type_code LIKE '%fdp%')";
}

// 1. Fetch Dynamic Hero Stats
$stats = [
    'total_docs' => 0,
    'pubs' => 0,
    'patents' => 0,
    'fdps' => 0,
    'researchers' => 0
];

// Total docs based on filter
$res = $conn->query("SELECT count(DISTINCT d.doc_id) as cnt FROM documents d $doc_joins WHERE $doc_where");
if($res) $stats['total_docs'] = $res->fetch_assoc()['cnt'];

// Pubs based on filter (if not exclusively filtering for something else)
if($cat_filter === 'all' || $cat_filter === 'pubs') {
    $res = $conn->query("SELECT count(DISTINCT d.doc_id) as cnt FROM documents d $doc_joins WHERE $doc_where AND (dt.type_code IN ('journal', 'conference'))");
    if($res) $stats['pubs'] = $res->fetch_assoc()['cnt'];
} else { $stats['pubs'] = ($cat_filter === 'pubs') ? $stats['total_docs'] : 0; }

// Patents
if($cat_filter === 'all' || $cat_filter === 'patents') {
    $res = $conn->query("SELECT count(DISTINCT d.doc_id) as cnt FROM documents d $doc_joins WHERE $doc_where AND dt.type_code = 'patent'");
    if($res) $stats['patents'] = $res->fetch_assoc()['cnt'];
} else { $stats['patents'] = ($cat_filter === 'patents') ? $stats['total_docs'] : 0; }

// FDPs
if($cat_filter === 'all' || $cat_filter === 'fdps') {
    $res = $conn->query("SELECT count(DISTINCT d.doc_id) as cnt FROM documents d $doc_joins WHERE $doc_where AND dt.type_code LIKE '%fdp%'");
    if($res) $stats['fdps'] = $res->fetch_assoc()['cnt'];
} else { $stats['fdps'] = ($cat_filter === 'fdps') ? $stats['total_docs'] : 0; }

// Active Researchers (Filtered by dept)
$r_where = "u.status='active' AND ur.role_id IN (SELECT role_id FROM roles WHERE role_name IN ('Faculty', 'HOD', 'R&D Dean'))";
if ($dept_filter) $r_where .= " AND ur.dept_id = $dept_filter";
$res = $conn->query("SELECT count(DISTINCT u.user_id) as cnt FROM users u JOIN user_roles ur ON u.user_id = ur.user_id WHERE $r_where");
if($res) $stats['researchers'] = $res->fetch_assoc()['cnt'];

$response['data']['stats'] = $stats;

// 2. Chart 1 Data: Timeline (Documents over time)
$timeline = [];
$res = $conn->query("
    SELECT DATE_FORMAT(d.created_at, '%Y-%m') as month, COUNT(DISTINCT d.doc_id) as cnt 
    FROM documents d 
    $doc_joins
    WHERE $doc_where 
    GROUP BY month 
    ORDER BY month ASC LIMIT 12
");
if($res) {
    while($row = $res->fetch_assoc()) { $timeline[$row['month']] = (int)$row['cnt']; }
}
$response['data']['timeline'] = $timeline;


// 3. Chart 2 Data: If department is selected, show category breakdown. If all departments, show department breakdown.
$distribution = [];
if ($dept_filter) {
    // Show Category breakdown for this department
    $res = $conn->query("
        SELECT dt.label as name, COUNT(DISTINCT d.doc_id) as cnt 
        FROM documents d 
        $doc_joins
        WHERE $doc_where 
        GROUP BY dt.type_id 
        ORDER BY cnt DESC LIMIT 10
    ");
} else {
    // Show Department breakdown
    $res = $conn->query("
        SELECT depts.dept_name as name, COUNT(DISTINCT d.doc_id) as cnt 
        FROM documents d 
        $doc_joins
        JOIN departments depts ON ur.dept_id = depts.dept_id
        WHERE $doc_where AND depts.is_academic = 1
        GROUP BY ur.dept_id 
        ORDER BY cnt DESC LIMIT 15
    ");
}
if($res) {
    while($row = $res->fetch_assoc()) { $distribution[$row['name']] = (int)$row['cnt']; }
}
$response['data']['distribution'] = $distribution;
$response['data']['distribution_label'] = $dept_filter ? "Documents by Category" : "Documents by Department";

echo json_encode($response);
exit;
