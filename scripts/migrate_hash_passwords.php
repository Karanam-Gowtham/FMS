<?php
/**
 * FMS Phase 2: Password Hash Migration Script
 * 
 * This script hashes all plaintext passwords in the `users` table
 * using PHP's password_hash() with PASSWORD_DEFAULT (bcrypt).
 * 
 * PREREQUISITES:
 *   - Full database backup taken
 *   - Run from command line: php scripts/migrate_hash_passwords.php
 *   - Do NOT run from a web browser
 * 
 * SAFETY:
 *   - Skips passwords already hashed (starting with $2y$)
 *   - Reports each user processed
 *   - Dry-run mode by default (set $dryRun = false to execute)
 *   - Counts successes and failures
 * 
 * ROLLBACK:
 *   - Restore from database/backup_pre_phase2_20260913.sql
 */

// Prevent web execution
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

// Database connection
require_once __DIR__ . '/../includes/connection.php';

// === CONFIGURATION ===
$dryRun = false;  // Set to false to actually update passwords
// =====================

echo "=== FMS Password Hash Migration ===\n";
echo "Mode: " . ($dryRun ? "DRY RUN (no changes)" : "LIVE (will update passwords)") . "\n\n";

// Fetch all users
$result = $conn->query("SELECT user_id, full_name, email, password FROM users ORDER BY user_id");

if (!$result) {
    die("Query failed: " . $conn->error . "\n");
}

$total = 0;
$skipped = 0;
$hashed = 0;
$failed = 0;

while ($row = $result->fetch_assoc()) {
    $total++;
    $userId = (int)$row['user_id'];
    $email = $row['email'];
    $currentPwd = $row['password'];
    
    // Check if already hashed (bcrypt starts with $2y$)
    if (strpos($currentPwd, '$2y$') === 0) {
        echo "  SKIP user_id={$userId} ({$email}) — already hashed\n";
        $skipped++;
        continue;
    }
    
    // Hash the plaintext password
    $hashedPwd = password_hash($currentPwd, PASSWORD_DEFAULT);
    
    if ($hashedPwd === false) {
        echo "  FAIL user_id={$userId} ({$email}) — password_hash() returned false\n";
        $failed++;
        continue;
    }
    
    if ($dryRun) {
        echo "  WOULD HASH user_id={$userId} ({$email}): '{$currentPwd}' → bcrypt\n";
        $hashed++;
    } else {
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $hashedPwd, $userId);
        
        if ($stmt->execute() && $stmt->affected_rows === 1) {
            echo "  HASHED user_id={$userId} ({$email})\n";
            $hashed++;
        } else {
            echo "  FAIL user_id={$userId} ({$email}) — UPDATE failed\n";
            $failed++;
        }
        $stmt->close();
    }
}

echo "\n=== Summary ===\n";
echo "Total users:    {$total}\n";
echo "Already hashed: {$skipped}\n";
echo "Hashed:         {$hashed}\n";
echo "Failed:         {$failed}\n";

if ($dryRun) {
    echo "\nThis was a DRY RUN. No passwords were changed.\n";
    echo "To execute, edit this script and set \$dryRun = false;\n";
}

// Verification (only in live mode)
if (!$dryRun && $failed === 0) {
    $check = $conn->query("SELECT COUNT(*) AS cnt FROM users WHERE password NOT LIKE '\$2y\$%'");
    $row = $check->fetch_assoc();
    if ((int)$row['cnt'] === 0) {
        echo "\nVERIFICATION PASSED: All passwords are now hashed.\n";
    } else {
        echo "\nWARNING: {$row['cnt']} passwords still appear unhashed!\n";
    }
}

$conn->close();
echo "\nDone.\n";
