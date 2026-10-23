<?php
/**
 * FMS Production Deployment & Pre-Flight Health Check
 *
 * Usage:
 *   php bin/deploy_check.php          (Human-readable CLI report)
 *   php bin/deploy_check.php --json   (JSON output for CI/CD pipelines)
 */

declare(strict_types=1);

$is_json = in_array('--json', $argv ?? [], true);
$root_dir = dirname(__DIR__);

$checks = [];
$status_overall = true;

function add_check(string $category, string $name, bool $passed, string $details = ''): void {
    global $checks, $status_overall;
    if (!$passed) {
        $status_overall = false;
    }
    $checks[] = [
        'category' => $category,
        'name' => $name,
        'passed' => $passed,
        'details' => $details
    ];
}

// 1. PHP Version
$php_version = PHP_VERSION;
$version_ok = version_compare($php_version, '8.1.0', '>=');
add_check('PHP Environment', 'PHP Version >= 8.1', $version_ok, "Installed: $php_version");

// 2. Extensions (Critical vs Recommended)
$required_extensions = [
    'mysqli'   => 'Database Connectivity',
    'fileinfo' => 'MIME Sniffing & File Validation',
    'mbstring' => 'Multi-byte Unicode Strings',
    'session'  => 'User Authentication Sessions',
    'json'     => 'API & Metadata Handling'
];

$recommended_extensions = [
    'zip'      => 'Bulk ZIP Archives',
    'gd'       => 'GIF / Advanced Image Processing',
    'curl'     => 'External Web Integrations'
];

foreach ($required_extensions as $ext => $purpose) {
    $loaded = extension_loaded($ext);
    add_check('Required Extensions', "Extension: $ext", $loaded, $purpose . ($loaded ? ' (Loaded)' : ' (MISSING - CRITICAL)'));
}

foreach ($recommended_extensions as $ext => $purpose) {
    $loaded = extension_loaded($ext);
    // Recommended extensions don't fail deployment, but emit status
    $checks[] = [
        'category' => 'Recommended Extensions',
        'name' => "Extension: $ext",
        'passed' => true,
        'details' => $purpose . ($loaded ? ' (Loaded)' : ' (Optional / Not enabled)')
    ];
}

// 3. Configuration & Environment
require_once $root_dir . '/config.php';
add_check('Configuration', 'Configuration Loaded', defined('ROOT_PATH'));
add_check('Configuration', 'Environment Set', defined('FMS_ENV'), "Active: " . (defined('FMS_ENV') ? FMS_ENV : 'None'));
add_check('Configuration', 'Debug Flag Set', defined('FMS_DEBUG'), "Debug: " . (FMS_DEBUG ? 'true' : 'false'));

// 4. Filesystem & Directory Permissions
$writable_dirs = [
    'uploads' => $root_dir . '/uploads',
    'tmp'     => $root_dir . '/tmp'
];

foreach ($writable_dirs as $label => $path) {
    if (!is_dir($path)) {
        @mkdir($path, 0755, true);
    }
    $is_writable = is_dir($path) && is_writable($path);
    add_check('Filesystem', "Directory Writable: $label", $is_writable, "Path: $path");
}

// 5. Database Connectivity & Tables
require_once $root_dir . '/includes/connection.php';
add_check('Database', 'MySQL Connection Active', isset($conn) && ($conn instanceof mysqli), "Host: " . DB_HOST . ", DB: " . DB_NAME);

if (isset($conn) && ($conn instanceof mysqli)) {
    $charset = $conn->character_set_name();
    $utf8_ok = stripos($charset, 'utf8') !== false;
    add_check('Database', 'UTF-8 Charset Configured', $utf8_ok, "Charset: $charset");

    $core_tables = [
        'users', 'roles', 'user_roles', 'user_profiles', 'departments',
        'academic_years', 'documents', 'document_files', 'document_actions',
        'document_types', 'workflows', 'workflow_steps', 'workflow_transitions'
    ];

    foreach ($core_tables as $tbl) {
        $q = $conn->query("SHOW TABLES LIKE '$tbl'");
        $table_exists = ($q && $q->num_rows > 0);
        add_check('Database Schema', "Table Exists: $tbl", $table_exists);
    }
}

// 6. Web Server Security
$htaccess_path = $root_dir . '/.htaccess';
$has_htaccess = file_exists($htaccess_path);
add_check('Web Server Security', 'Root .htaccess Configured', $has_htaccess);

if ($has_htaccess) {
    $htaccess_body = file_get_contents($htaccess_path);
    add_check('Web Server Security', 'Directory Listing Disabled (Options -Indexes)', strpos($htaccess_body, 'Options -Indexes') !== false);
    add_check('Web Server Security', 'Security Headers (X-Content-Type-Options)', strpos($htaccess_body, 'X-Content-Type-Options') !== false);
}

// Output rendering
if ($is_json) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status_overall ? 'READY_FOR_DEPLOYMENT' : 'NOT_READY',
        'timestamp' => date('c'),
        'checks' => $checks
    ], JSON_PRETTY_PRINT);
    exit($status_overall ? 0 : 1);
}

echo "================================================================================\n";
echo "           FILE MANAGEMENT SYSTEM (FMS) - PRE-FLIGHT DEPLOYMENT CHECK           \n";
echo "================================================================================\n\n";

$last_category = '';
foreach ($checks as $c) {
    if ($c['category'] !== $last_category) {
        echo "\n[" . strtoupper($c['category']) . "]\n";
        $last_category = $c['category'];
    }
    $badge = $c['passed'] ? '[PASS]' : '[FAIL]';
    echo sprintf("  %-8s %-45s %s\n", $badge, $c['name'], $c['details'] ? "({$c['details']})" : "");
}

echo "\n================================================================================\n";
if ($status_overall) {
    echo "STATUS: ALL PRE-FLIGHT CHECKS PASSED. SYSTEM IS PRODUCTION READY!\n";
    exit(0);
} else {
    echo "STATUS: PRE-FLIGHT CHECKS FAILED. RESOLVE ISSUES BEFORE OPENING TRAFFIC.\n";
    exit(1);
}
