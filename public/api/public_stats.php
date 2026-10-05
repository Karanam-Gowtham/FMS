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
$dept_filter = isset($_GET['dept_id']) && $_GET['dept_id'] !== 'all' ? $_GET['dept_id'] : null;
$cat_filter = isset($_GET['category']) && $_GET['category'] !== 'all' ? $_GET['category'] : null;
$focus_filter = isset($_GET['type']) && $_GET['type'] !== 'all' ? $_GET['type'] : null;

// Base JOINs for documents
$doc_joins = "
    JOIN document_types dt ON d.type_id = dt.type_id
    LEFT JOIN academic_years ay ON d.academic_year_id = ay.year_id
";

// Base WHERE condition for accepted documents
$whitelist = "'journal', 'conference', 'patent', 'fdp_attended', 'fdp_organised', 'conf_organised', 'scholarship', 'placement', 'higher_ed', 'award', 'student_event', 'student_body', 'student_journal', 'student_conference'";
$doc_where = "d.status = 'accepted' AND dt.type_code IN ($whitelist)";

if ($year_filter) {
    $years = explode(',', $year_filter);
    $year_sql = implode(',', array_map(function($y) { return (int)substr(trim($y), 0, 4); }, $years));
    if (!empty($year_sql)) $doc_where .= " AND CAST(SUBSTRING(ay.year_label, 1, 4) AS UNSIGNED) IN ($year_sql)";
}

if ($dept_filter) {
    $depts = explode(',', $dept_filter);
    $dept_sql = implode(',', array_map('intval', $depts));
    if (!empty($dept_sql)) $doc_where .= " AND d.dept_id IN ($dept_sql)";
}

// 1. Apply Focus Filter (Multi-Select)
if ($focus_filter) {
    $foci = explode(',', $focus_filter);
    $foci_sql = implode(',', array_map(function($f) { global $conn; return "'" . $conn->real_escape_string(trim($f)) . "'"; }, $foci));
    if (!empty($foci_sql)) {
        $doc_where .= " AND dt.type_code IN ($foci_sql)";
    }
}

// 1. Fetch Dynamic Hero Stats (calculated BEFORE applying category filter)
$stats = [
    'total_docs' => 0,
    'pubs' => 0,
    'patents' => 0,
    'fdps' => 0,
    'researchers' => 0
];

// Total docs based on sidebar filters
$res = $conn->query("SELECT count(DISTINCT d.doc_id) as cnt FROM documents d $doc_joins WHERE $doc_where");
if($res) $stats['total_docs'] = $res->fetch_assoc()['cnt'];

// Pubs
$res = $conn->query("SELECT count(DISTINCT d.doc_id) as cnt FROM documents d $doc_joins WHERE $doc_where AND (dt.type_code IN ('journal', 'conference'))");
if($res) $stats['pubs'] = $res->fetch_assoc()['cnt'];

// Patents
$res = $conn->query("SELECT count(DISTINCT d.doc_id) as cnt FROM documents d $doc_joins WHERE $doc_where AND dt.type_code = 'patent'");
if($res) $stats['patents'] = $res->fetch_assoc()['cnt'];

// FDPs
$res = $conn->query("SELECT count(DISTINCT d.doc_id) as cnt FROM documents d $doc_joins WHERE $doc_where AND dt.type_code LIKE '%fdp%'");
if($res) $stats['fdps'] = $res->fetch_assoc()['cnt'];

// Active Researchers (Filtered by dept)
$r_where = "u.status='active' AND ur.role_id IN (SELECT role_id FROM roles WHERE role_name IN ('Faculty', 'HOD', 'R&D Dean'))";
if ($dept_filter && !empty($dept_sql)) {
    $r_where .= " AND ur.dept_id IN ($dept_sql)";
}
$res = $conn->query("SELECT count(DISTINCT u.user_id) as cnt FROM users u JOIN user_roles ur ON u.user_id = ur.user_id WHERE $r_where");
if($res) $stats['researchers'] = $res->fetch_assoc()['cnt'];

$response['data']['stats'] = $stats;

// 2. Apply Category Filter (Card Click) - This only affects the charts below
if ($cat_filter === 'patents') {
    $doc_where .= " AND dt.type_code = 'patent'";
} elseif ($cat_filter === 'pubs') {
    $doc_where .= " AND (dt.type_code IN ('journal', 'conference'))";
} elseif ($cat_filter === 'fdps') {
    $doc_where .= " AND (dt.type_code LIKE '%fdp%')";
}


// 2. Chart 1 Data: Timeline (Academic Trajectory)
$timeline = [];
$res = $conn->query("
    SELECT ay.year_label as yr, COUNT(DISTINCT d.doc_id) as cnt 
    FROM documents d 
    $doc_joins
    WHERE $doc_where AND ay.year_label IS NOT NULL
    GROUP BY yr 
    ORDER BY yr DESC LIMIT 10
");
if($res) {
    while($row = $res->fetch_assoc()) { $timeline[$row['yr']] = (int)$row['cnt']; }
}
// Reverse sort by key (year label) to flow chronologically on the chart
ksort($timeline);
$response['data']['timeline'] = $timeline;

// 3. Category Distribution (For Doughnut & Radar)
$category_dist = [];
$res = $conn->query("
    SELECT dt.label as name, COUNT(DISTINCT d.doc_id) as cnt 
    FROM documents d 
    $doc_joins
    WHERE $doc_where 
    GROUP BY dt.type_id 
    ORDER BY cnt DESC LIMIT 10
");
if($res) {
    while($row = $res->fetch_assoc()) { $category_dist[$row['name']] = (int)$row['cnt']; }
}
$response['data']['category_distribution'] = $category_dist;

// 4. Department Distribution (For Polar Area)
$dept_dist = [];
$res = $conn->query("
    SELECT depts.dept_name as name, COUNT(DISTINCT d.doc_id) as cnt 
    FROM documents d 
    $doc_joins
    JOIN departments depts ON d.dept_id = depts.dept_id
    WHERE $doc_where AND depts.is_academic = 1
    GROUP BY d.dept_id 
    ORDER BY cnt DESC LIMIT 15
");
if($res) {
    while($row = $res->fetch_assoc()) { $dept_dist[$row['name']] = (int)$row['cnt']; }
}
$response['data']['dept_distribution'] = $dept_dist;

echo json_encode($response);
exit;
