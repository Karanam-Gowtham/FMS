<?php
$conn = mysqli_connect('localhost', 'root', '', 'project-fms');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() . "\n");
}

// 1. Get all tables and their column lists
$tables = [];
$res = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_array($res)) {
    $tableName = $row[0];
    $colRes = mysqli_query($conn, "SHOW COLUMNS FROM `$tableName`");
    $cols = [];
    while ($colRow = mysqli_fetch_assoc($colRes)) {
        $cols[] = strtolower($colRow['Field']);
    }
    $tables[$tableName] = $cols;
}

echo "Found " . count($tables) . " tables in database 'project-fms'.\n\n";

// 2. Scan all PHP files for INSERT INTO queries
$dir = __DIR__;
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$issues = [];

foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    if (pathinfo($file->getFilename(), PATHINFO_EXTENSION) !== 'php') continue;

    $content = file_get_contents($file->getPathname());
    
    // Match INSERT INTO table_name (col1, col2, ...)
    if (preg_match_all('/INSERT\s+INTO\s+([`\w]+)\s*\(([^)]+)\)/i', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $tableName = strtolower(trim($match[1], '`'));
            $columnsStr = $match[2];
            
            // Clean columns list
            $columns = array_map(function($c) {
                return strtolower(trim($c, " `\t\n\r\0\x0B"));
            }, explode(',', $columnsStr));

            if (!isset($tables[$tableName])) {
                $issues[] = [
                    'file' => $file->getPathname(),
                    'table' => $tableName,
                    'error' => "Table '$tableName' does not exist in database!"
                ];
                continue;
            }

            $dbCols = $tables[$tableName];
            foreach ($columns as $col) {
                // Ignore subqueries or functions if any slipped in
                if (empty($col) || strpos($col, ' ') !== false) continue;

                if (!in_array($col, $dbCols)) {
                    $issues[] = [
                        'file' => str_replace(__DIR__ . '\\', '', $file->getPathname()),
                        'table' => $tableName,
                        'column' => $col,
                        'error' => "Column '$col' is missing in table '$tableName'!"
                    ];
                }
            }
        }
    }
}

if (empty($issues)) {
    echo "SUCCESS: All column references in INSERT statements match existing database columns!\n";
} else {
    echo "ATTENTION: Found " . count($issues) . " column/table mismatches:\n";
    foreach ($issues as $issue) {
        if (isset($issue['column'])) {
            echo " - File: " . $issue['file'] . " | Table: " . $issue['table'] . " | Missing Column: " . $issue['column'] . "\n";
        } else {
            echo " - File: " . $issue['file'] . " | Error: " . $issue['error'] . "\n";
        }
    }
}

mysqli_close($conn);
