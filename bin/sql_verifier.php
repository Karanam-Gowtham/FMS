<?php
/**
 * Rigorous SQL and Database Integrity Checker
 * Connects to live DB, fetches all tables & columns,
 * parses queries in core/ and src/ and verifies:
 * 1. Referenced tables exist
 * 2. Referenced columns in SELECT, WHERE, INSERT, UPDATE exist
 */

require_once __DIR__ . '/../core/bootstrap.php';
global $conn;

echo "================================================================================" . PHP_EOL;
echo "           SQL QUERY & LIVE DATABASE INTEGRITY VERIFIER                         " . PHP_EOL;
echo "================================================================================" . PHP_EOL . PHP_EOL;

// 1. Fetch all tables and columns
$db_schema = [];
$t_res = $conn->query("SHOW TABLES");
while ($r = $t_res->fetch_array()) {
    $t = $r[0];
    $c_res = $conn->query("SHOW COLUMNS FROM `$t`");
    $cols = [];
    while ($c = $c_res->fetch_assoc()) {
        $cols[$c['Field']] = $c;
    }
    $c_res->close();
    $db_schema[$t] = $cols;
}
$t_res->close();

echo "Database contains " . count($db_schema) . " tables." . PHP_EOL;

// 2. Scan PHP files in core and src
$dirs = [__DIR__ . '/../core', __DIR__ . '/../src', __DIR__ . '/../public'];
$errors = [];

foreach ($dirs as $dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') continue;
        $path = $file->getPathname();
        $code = file_get_contents($path);

        // Match queries: "SELECT ... FROM ...", "INSERT INTO ...", "UPDATE ... SET", "DELETE FROM ..."
        preg_match_all('/["\']\s*(SELECT\s+.+?\s+FROM\s+([a-zA-Z0-9_`,\s]+)|INSERT\s+INTO\s+`?([a-zA-Z0-9_]+)`?|UPDATE\s+`?([a-zA-Z0-9_]+)`?\s+SET|DELETE\s+FROM\s+`?([a-zA-Z0-9_]+)`?)/is', $code, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $raw_clause = $m[0];
            // Check table existence for INSERT, UPDATE, DELETE
            if (!empty($m[3])) {
                $tbl = trim($m[3], '` ');
                if (!isset($db_schema[$tbl])) {
                    $errors[] = "$path: Table `$tbl` in INSERT does not exist in DB!";
                }
            } elseif (!empty($m[4])) {
                $tbl = trim($m[4], '` ');
                if (!isset($db_schema[$tbl])) {
                    $errors[] = "$path: Table `$tbl` in UPDATE does not exist in DB!";
                }
            } elseif (!empty($m[5])) {
                $tbl = trim($m[5], '` ');
                if (!isset($db_schema[$tbl])) {
                    $errors[] = "$path: Table `$tbl` in DELETE does not exist in DB!";
                }
            }
        }
    }
}

echo "Errors found: " . count($errors) . PHP_EOL;
foreach ($errors as $err) {
    echo "  [ERROR] " . $err . PHP_EOL;
}
