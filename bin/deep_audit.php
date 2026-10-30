<?php
/**
 * Comprehensive FMS Production Readiness & Deep Integrity Audit
 */

require_once __DIR__ . '/../core/bootstrap.php';
global $conn;

echo "================================================================================" . PHP_EOL;
echo "           FMS PRODUCTION READINESS & DEEP INTEGRITY AUDIT                      " . PHP_EOL;
echo "================================================================================" . PHP_EOL . PHP_EOL;

$findings = [];
$score = 0;

function add_finding($category, $severity, $message, $file = '', $line = '') {
    global $findings;
    $findings[] = [
        'category' => $category,
        'severity' => $severity, // CRITICAL, HIGH, MEDIUM, LOW
        'message' => $message,
        'file' => $file,
        'line' => $line
    ];
}

// -----------------------------------------------------------------------------
// [CHECK 1] ROLES & CONSTANTS INTEGRITY
// -----------------------------------------------------------------------------
echo "[1] Checking Role Constants vs Live `roles` Table..." . PHP_EOL;
$r_res = $conn->query("SELECT role_id, role_name FROM roles");
$db_roles = [];
while ($row = $r_res->fetch_assoc()) {
    $db_roles[(int)$row['role_id']] = $row['role_name'];
}
$r_res->close();

$code_roles = [
    ROLE_ADMIN => 'ROLE_ADMIN',
    ROLE_IQAC => 'ROLE_IQAC',
    ROLE_HOD => 'ROLE_HOD',
    ROLE_FACULTY => 'ROLE_FACULTY',
    ROLE_DEPT_COORDINATOR => 'ROLE_DEPT_COORDINATOR',
    ROLE_CENTRAL_COORDINATOR => 'ROLE_CENTRAL_COORDINATOR',
    ROLE_JUNIOR_ASSISTANT => 'ROLE_JUNIOR_ASSISTANT',
    ROLE_RND_DEAN => 'ROLE_RND_DEAN',
    ROLE_STUDENT => 'ROLE_STUDENT'
];

foreach ($code_roles as $id => $const_name) {
    if (!isset($db_roles[$id])) {
        add_finding('Database / Roles', 'CRITICAL', "Constant $const_name (ID $id) does not exist in `roles` table!");
    }
}
echo "    Verified " . count($code_roles) . " role constants against " . count($db_roles) . " database roles." . PHP_EOL;

// -----------------------------------------------------------------------------
// [CHECK 2] DOCUMENT TYPES VS META REGISTRY
// -----------------------------------------------------------------------------
echo "[2] Checking `document_types` Table vs `meta_registry.php`..." . PHP_EOL;
$col_query = $conn->query("SHOW COLUMNS FROM document_types");
$dt_cols = [];
while ($c = $col_query->fetch_assoc()) {
    $dt_cols[] = $c['Field'];
}
echo "    Live columns in `document_types`: " . implode(', ', $dt_cols) . PHP_EOL;

$type_col = in_array('type_code', $dt_cols) ? 'type_code' : 'type_key';
$label_col = in_array('label', $dt_cols) ? 'label' : 'type_label';

$dt_res = $conn->query("SELECT type_id, `$type_col` AS type_code, `$label_col` AS type_label FROM document_types");
$db_doc_types = [];
while ($row = $dt_res->fetch_assoc()) {
    $db_doc_types[$row['type_code']] = $row;
}
$dt_res->close();

$type_keys = [
    'journal', 'conference', 'patent', 'fdp', 'fdp_attended', 'fdp_organised',
    'conf_organised', 'criteria_file', 'dept_file', 'central_file',
    'scholarship', 'placement', 'higher_ed', 'exam_qual', 'award',
    'student_event', 'student_body', 'student_journal', 'student_conference',
    'stu_act', 'student_activity_file'
];

foreach ($type_keys as $type_code) {
    if (!isset($db_doc_types[$type_code])) {
        add_finding('Database / Document Types', 'HIGH', "Type '$type_code' registered in meta_registry.php does not exist in `document_types` table!");
    }
    // Check table existence
    $table = meta_get_table($type_code);
    if (!$table) {
        add_finding('Meta Registry', 'CRITICAL', "Type '$type_code' returned null table!");
        continue;
    }
    $t_check = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($table) . "'");
    if ($t_check->num_rows === 0) {
        add_finding('Database / Schema', 'CRITICAL', "Meta registry type '$type_code' references table `$table` which DOES NOT EXIST!");
    } else {
        // Check columns
        $c_res = $conn->query("SHOW COLUMNS FROM `$table`");
        $cols = [];
        while ($c = $c_res->fetch_assoc()) {
            $cols[$c['Field']] = true;
        }
        $c_res->close();

        // Check doc_id foreign key column in meta table
        if (!isset($cols['doc_id'])) {
            add_finding('Database / Schema', 'CRITICAL', "Meta table `$table` is missing required primary link column `doc_id`!");
        }

        $fields = meta_get_fields($type_code);
        foreach ($fields as $field_def) {
            $field_name = $field_def['name'];
            if (!isset($cols[$field_name])) {
                add_finding('Database / Schema', 'HIGH', "Meta table `$table` is missing field `$field_name` declared in meta_registry.php!");
            }
        }
    }
    $t_check->close();
}
echo "    Verified " . count($type_keys) . " metadata types and schemas." . PHP_EOL;

// -----------------------------------------------------------------------------
// [CHECK 3] WORKFLOW ENGINE INTEGRITY
// -----------------------------------------------------------------------------
echo "[3] Checking Workflow Engine State Machine Integrity..." . PHP_EOL;
$wf_res = $conn->query("SELECT workflow_id, workflow_key, label FROM workflows");
$workflows = [];
while ($w = $wf_res->fetch_assoc()) {
    $workflows[$w['workflow_id']] = $w;
}
$wf_res->close();

if (empty($workflows)) {
    add_finding('Workflow', 'CRITICAL', "No active workflows found in `workflows` table!");
}

$steps_res = $conn->query("SELECT step_id, workflow_id, step_order, step_label, responsible_role_id FROM workflow_steps");
$steps = [];
while ($s = $steps_res->fetch_assoc()) {
    $steps[$s['step_id']] = $s;
    if (!isset($workflows[$s['workflow_id']])) {
        add_finding('Workflow', 'HIGH', "workflow_step {$s['step_id']} references non-existent workflow {$s['workflow_id']}!");
    }
    if (!isset($db_roles[(int)$s['responsible_role_id']])) {
        add_finding('Workflow', 'HIGH', "workflow_step {$s['step_id']} references non-existent role {$s['responsible_role_id']}!");
    }
}
$steps_res->close();
echo "    Verified " . count($workflows) . " workflows and " . count($steps) . " workflow steps." . PHP_EOL;

// -----------------------------------------------------------------------------
// [CHECK 4] ROUTING & CONTROLLER DISPATCH AUDIT
// -----------------------------------------------------------------------------
echo "[4] Checking Routes in public/index.php vs Controller Methods..." . PHP_EOL;
$index_file = realpath(__DIR__ . '/../public/index.php');
$index_code = file_get_contents($index_file);

// Match route conditionals and controller dispatches
preg_match_all('/if\s*\(\$route\s*===\s*[\'"]([^\'"]+)[\'"].*?\)\s*\{([^}]+)\}/s', $index_code, $route_blocks, PREG_SET_ORDER);
preg_match_all('/elseif\s*\(\$route\s*===\s*[\'"]([^\'"]+)[\'"].*?\)\s*\{([^}]+)\}/s', $index_code, $elseif_blocks, PREG_SET_ORDER);
$all_blocks = array_merge($route_blocks, $elseif_blocks);

$checked_routes = 0;
foreach ($all_blocks as $blk) {
    $r_name = $blk[1];
    $code_body = $blk[2];
    
    // Look for new \App\Controllers\XController() and method call ->method()
    if (preg_match('/new\s+\\\\?App\\\\Controllers\\\\([a-zA-Z0-9_]+)\(\)/', $code_body, $c_match)) {
        $c_class = 'App\\Controllers\\' . $c_match[1];
        if (!class_exists($c_class)) {
            add_finding('Routing', 'CRITICAL', "Route '$r_name' dispatches to non-existent class $c_class!");
        } else {
            if (preg_match('/\$controller->([a-zA-Z0-9_]+)\s*\(/', $code_body, $m_match)) {
                $m_name = $m_match[1];
                if (!method_exists($c_class, $m_name)) {
                    add_finding('Routing', 'CRITICAL', "Route '$r_name' dispatches to non-existent method $c_class::$m_name()!");
                }
            }
        }
    }
    $checked_routes++;
}
echo "    Verified $checked_routes route dispatches in public/index.php." . PHP_EOL;

// -----------------------------------------------------------------------------
// [CHECK 5] VIEW TEMPLATES EXISTENCE & COMPILATION CHECK
// -----------------------------------------------------------------------------
echo "[5] Checking View Files & Template Integrity..." . PHP_EOL;
$view_dir = realpath(__DIR__ . '/../src/Views');
$view_files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($view_dir));
$view_count = 0;
foreach ($view_files as $vf) {
    if (!$vf->isFile() || $vf->getExtension() !== 'php') continue;
    $vpath = $vf->getPathname();
    $vrel = str_replace(realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR, '', $vpath);
    $view_count++;

    // Syntax check each view using current PHP runtime
    $php_bin = defined('PHP_BINARY') && file_exists(PHP_BINARY) ? PHP_BINARY : 'php';
    $cmd = escapeshellarg($php_bin) . ' -l ' . escapeshellarg($vpath) . ' 2>&1';
    exec($cmd, $out, $ret);
    if ($ret !== 0) {
        add_finding('Views', 'CRITICAL', "Syntax error in view $vrel: " . implode(' ', $out), $vrel);
    }

    // Check for broken includes inside views
    $vcontent = file_get_contents($vpath);
    preg_match_all('/(include|require|include_once|require_once)\s+[\'"]([^\'"]+\.php)[\'"]/', $vcontent, $inc_matches);
    foreach ($inc_matches[2] as $inc_path) {
        if (!str_starts_with($inc_path, '/') && !str_starts_with($inc_path, 'http')) {
            $resolved = dirname($vpath) . '/' . $inc_path;
            if (!file_exists($resolved)) {
                add_finding('Views', 'HIGH', "View $vrel includes non-existent file '$inc_path'!", $vrel);
            }
        }
    }
}
echo "    Verified $view_count view templates." . PHP_EOL;

// -----------------------------------------------------------------------------
// [CHECK 6] CONTROLLER INCLUDES & VIEW PATH RESOLUTION
// -----------------------------------------------------------------------------
echo "[6] Checking Controller View Inclusions..." . PHP_EOL;
$ctrl_dir = realpath(__DIR__ . '/../src/Controllers');
$ctrl_files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($ctrl_dir));
foreach ($ctrl_files as $cf) {
    if (!$cf->isFile() || $cf->getExtension() !== 'php') continue;
    $cpath = $cf->getPathname();
    $crel = str_replace(realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR, '', $cpath);
    $ccontent = file_get_contents($cpath);

    preg_match_all('/include\s+__DIR__\s*\.\s*[\'"]([^\'"]+\.php)[\'"]/', $ccontent, $view_matches);
    foreach ($view_matches[1] as $vm) {
        $full_view_path = realpath(dirname($cpath) . '/' . $vm);
        if (!$full_view_path || !file_exists($full_view_path)) {
            add_finding('Controllers', 'CRITICAL', "Controller $crel includes non-existent view: '$vm'", $crel);
        }
    }
}
echo "    Verified all controller view paths." . PHP_EOL;

// -----------------------------------------------------------------------------
// [CHECK 7] ACCREDITATION (NBA & NAAC) DATABASE TABLES & COLUMNS
// -----------------------------------------------------------------------------
echo "[7] Checking Accreditation Tables (NBA & NAAC)..." . PHP_EOL;
$accreditation_tables = [
    'nba_submissions' => ['submission_id', 'dept_id', 'academic_year', 'status'],
    'nba_criteria_data' => ['id', 'submission_id', 'criterion_number', 'data_json'],
    'naac_submissions' => ['submission_id', 'dept_id', 'academic_year', 'status'],
    'naac_criteria_data' => ['id', 'submission_id', 'criterion_number', 'data_json'],
    'naac_form_schemas' => ['schema_id', 'criterion_number', 'schema_json', 'is_active']
];

foreach ($accreditation_tables as $atbl => $acols) {
    $tchk = $conn->query("SHOW TABLES LIKE '$atbl'");
    if ($tchk->num_rows === 0) {
        add_finding('Accreditation', 'CRITICAL', "Accreditation table `$atbl` DOES NOT EXIST in database!");
    } else {
        $c_res = $conn->query("SHOW COLUMNS FROM `$atbl`");
        $cols = [];
        while ($c = $c_res->fetch_assoc()) {
            $cols[$c['Field']] = true;
        }
        $c_res->close();
        foreach ($acols as $c) {
            if (!isset($cols[$c])) {
                add_finding('Accreditation', 'CRITICAL', "Accreditation table `$atbl` is missing expected column `$c`!");
            }
        }
    }
    $tchk->close();
}

// -----------------------------------------------------------------------------
// [CHECK 8] ACADEMIC YEARS TABLE INTEGRITY
// -----------------------------------------------------------------------------
echo "[8] Checking Academic Years Integrity..." . PHP_EOL;
require_once __DIR__ . '/../core/document_service.php';
$ay_rows = doc_get_academic_years($conn);
if (empty($ay_rows)) {
    add_finding('Database / Academic Years', 'CRITICAL', "No academic years configured in `academic_years` table!");
} else {
    $active_ay = doc_get_active_year($conn);
    if (!$active_ay) {
        add_finding('Database / Academic Years', 'HIGH', "No active academic year could be resolved via doc_get_active_year()!");
    } else {
        echo "    Active academic year: " . $active_ay['year_label'] . " (Total: " . count($ay_rows) . " years configured)" . PHP_EOL;
    }
}

// -----------------------------------------------------------------------------
// [CHECK 9] SCANNING FOR SQL INJECTION / UNESCAPED DYNAMIC QUERIES
// -----------------------------------------------------------------------------
echo "[9] Scanning for Potential SQL Vulnerabilities or Syntax Bugs..." . PHP_EOL;
$scan_dirs = [__DIR__ . '/../src/Controllers', __DIR__ . '/../core'];
foreach ($scan_dirs as $sdir) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sdir));
    foreach ($it as $f) {
        if (!$f->isFile() || $f->getExtension() !== 'php') continue;
        $fpath = $f->getPathname();
        $frel = str_replace(realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR, '', $fpath);
        $code = file_get_contents($fpath);

        // Check for direct unescaped POST or GET variables inside $conn->query()
        if (preg_match('/\$conn->query\([^)]*\$_(POST|GET|REQUEST)\[/i', $code, $vuln)) {
            add_finding('Security / SQL', 'CRITICAL', "Direct query interpolation of request variable: " . substr($vuln[0], 0, 50), $frel);
        }
    }
}

// -----------------------------------------------------------------------------
// [CHECK 10] SCANNING FOR HARDCODED REDIRECTS OR 404 URLS IN VIEWS & SCRIPTS
// -----------------------------------------------------------------------------
echo "[10] Scanning for Redirection, Broken Routes & Mismatches..." . PHP_EOL;
$all_files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(realpath(__DIR__ . '/..')));
foreach ($all_files as $f) {
    if (!$f->isFile() || !in_array($f->getExtension(), ['php', 'js'])) continue;
    $fpath = $f->getPathname();
    $frel = str_replace(realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR, '', $fpath);
    if (str_starts_with($frel, 'vendor') || str_starts_with($frel, 'libs') || str_starts_with($frel, 'tmp') || str_starts_with($frel, 'uploads') || str_starts_with($frel, '.git')) {
        continue;
    }
    $code = file_get_contents($fpath);

    // Check for route= that is not in index.php
    preg_match_all('/route=([a-zA-Z0-9_\/]+)/', $code, $rmatches);
    foreach ($rmatches[1] as $r) {
        // Exclude dummy templates or tests
        $known_routes = [
            'dashboard', 'nba/dashboard', 'nba/criterion', 'naac/dashboard', 'naac/criterion',
            'student_activities/dashboard', 'api/nba/save', 'api/nba/save_criterion', 'api/nba/upload_pdf',
            'api/nba/generate_pdf', 'api/naac/save', 'api/naac/upload_pdf', 'api/naac/generate_pdf',
            'api/documents/accreditation_preview', 'documents/list', 'documents/my_uploads', 'documents/upload',
            'documents/view', 'documents/approve', 'documents/download', 'documents/bulk_action',
            'documents/edit', 'documents/process_update', 'profile/view', 'profile/edit',
            'academic_years/list', 'public/department', 'public/download', 'auth/login',
            'auth/register', 'auth/select_role', 'auth/logout', 'student/dashboard', 'reports/download'
        ];
        if (!in_array($r, $known_routes)) {
            add_finding('Routing / Dead Link', 'HIGH', "Dead route detected: 'route=$r'", $frel);
        }
    }
}

// -----------------------------------------------------------------------------
// SUMMARY
// -----------------------------------------------------------------------------
echo PHP_EOL . "================================================================================" . PHP_EOL;
echo "AUDIT SUMMARY & SCORED FINDINGS:" . PHP_EOL;
echo "================================================================================" . PHP_EOL;

if (empty($findings)) {
    echo "SUCCESS: 0 BUGS, 0 MISMATCHES, 0 ROUTING ISSUES FOUND!" . PHP_EOL;
    echo "Every database table, column, metadata type, route, view, and workflow is in sync!" . PHP_EOL;
} else {
    echo "Found " . count($findings) . " issues:" . PHP_EOL . PHP_EOL;
    foreach ($findings as $idx => $f) {
        $num = $idx + 1;
        echo "[$num] [{$f['severity']}] [{$f['category']}] {$f['message']}" . PHP_EOL;
        if (!empty($f['file'])) {
            echo "    Location: {$f['file']}" . (!empty($f['line']) ? ":{$f['line']}" : "") . PHP_EOL;
        }
    }
}
