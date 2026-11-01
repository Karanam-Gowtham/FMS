<?php
/**
 * FMS Database Migration Runner CLI
 * 
 * Usage:
 *   php bin/migrate.php          (Applies pending migrations)
 *   php bin/migrate.php --status (Displays migration status)
 *   php bin/migrate.php --run    (Explicitly runs pending migrations)
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "This tool must be run from the command line.\n";
    exit(1);
}

require_once __DIR__ . '/../core/bootstrap.php';
global $conn;

if (!$conn || $conn->connect_error) {
    fwrite(STDERR, "[ERROR] Database connection failed: " . ($conn ? $conn->connect_error : 'Connection null') . "\n");
    exit(1);
}

// 1. Ensure schema_migrations tracking table exists
$tableSql = "
    CREATE TABLE IF NOT EXISTS schema_migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL UNIQUE,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";
if (!$conn->query($tableSql)) {
    fwrite(STDERR, "[ERROR] Failed to ensure schema_migrations table: " . $conn->error . "\n");
    exit(1);
}

// 2. Discover migration files
$migrationsDir = __DIR__ . '/../database/migrations';
if (!is_dir($migrationsDir)) {
    fwrite(STDERR, "[ERROR] Migrations directory not found: {$migrationsDir}\n");
    exit(1);
}

$files = scandir($migrationsDir);
$migrationFiles = [];
foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
        $migrationFiles[] = $file;
    }
}
sort($migrationFiles, SORT_NATURAL);

// 3. Query already applied migrations
$appliedMigrations = [];
$res = $conn->query("SELECT migration, applied_at FROM schema_migrations ORDER BY id ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $appliedMigrations[$row['migration']] = $row['applied_at'];
    }
}

// 4. Handle CLI arguments
$args = array_slice($argv, 1);
$action = 'run';
if (in_array('--status', $args, true)) {
    $action = 'status';
} elseif (in_array('--baseline', $args, true)) {
    $action = 'baseline';
}

echo "\n=== FMS Database Migration Engine ===\n\n";

if ($action === 'baseline') {
    $count = 0;
    foreach ($migrationFiles as $file) {
        if (!isset($appliedMigrations[$file])) {
            $stmt = $conn->prepare("INSERT INTO schema_migrations (migration) VALUES (?)");
            if ($stmt) {
                $stmt->bind_param("s", $file);
                $stmt->execute();
                $stmt->close();
                $count++;
            }
        }
    }
    echo "[OK] Baselined {$count} migration(s) as already applied.\n\n";
    exit(0);
}

if ($action === 'status') {
    echo sprintf("%-45s | %-12s | %s\n", "Migration File", "Status", "Applied At");
    echo str_repeat("-", 80) . "\n";
    foreach ($migrationFiles as $file) {
        $isApplied = isset($appliedMigrations[$file]);
        $status = $isApplied ? "APPLIED" : "PENDING";
        $appliedAt = $isApplied ? $appliedMigrations[$file] : "-";
        echo sprintf("%-45s | %-12s | %s\n", $file, $status, $appliedAt);
    }
    echo "\n";
    exit(0);
}

// Action: Run pending migrations
$pending = [];
foreach ($migrationFiles as $file) {
    if (!isset($appliedMigrations[$file])) {
        $pending[] = $file;
    }
}

if (empty($pending)) {
    echo "[OK] All " . count($migrationFiles) . " migrations are already up to date.\n\n";
    exit(0);
}

echo "Found " . count($pending) . " pending migration(s) to apply:\n\n";

foreach ($pending as $file) {
    $filePath = $migrationsDir . '/' . $file;
    echo "  -> Applying: {$file} ... ";
    
    $sqlContent = file_get_contents($filePath);
    if ($sqlContent === false) {
        fwrite(STDERR, "[FAILED] Could not read file.\n");
        exit(1);
    }
    
    // Execute multi-query script
    if ($conn->multi_query($sqlContent)) {
        do {
            // Drain all result sets
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    }
    
    if ($conn->error) {
        fwrite(STDERR, "\n[ERROR] Migration failed in {$file}: " . $conn->error . "\n");
        exit(1);
    }
    
    // Record migration
    $stmt = $conn->prepare("INSERT INTO schema_migrations (migration) VALUES (?)");
    if ($stmt) {
        $stmt->bind_param("s", $file);
        $stmt->execute();
        $stmt->close();
    }
    
    echo "[DONE]\n";
}

echo "\n[SUCCESS] Successfully applied " . count($pending) . " migration(s).\n\n";
exit(0);
