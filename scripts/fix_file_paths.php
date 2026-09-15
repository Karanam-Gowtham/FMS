<?php
/**
 * Fix migrated document_files paths to point to actual files on disk.
 * Maps legacy file paths to their real locations.
 */
require_once __DIR__ . '/../core/bootstrap.php';

echo "=== Fix Migrated File Paths ===\n\n";

// Backup first
$backup = __DIR__ . '/../database/backup_pre_file_fix_' . date('Ymd_His') . '.sql';
$fp = fopen($backup, 'w');
fwrite($fp, "-- document_files backup before path fix\n\n");
$r = $conn->query("SELECT * FROM document_files");
while ($row = $r->fetch_assoc()) {
    $vals = [];
    foreach ($row as $v) $vals[] = ($v === null) ? "NULL" : "'" . $conn->real_escape_string($v) . "'";
    fwrite($fp, "REPLACE INTO document_files VALUES (" . implode(', ', $vals) . ");\n");
}
fclose($fp);
echo "Backup: $backup\n\n";

// Build an index of all actual files on disk under uploads/
$upload_root = ROOT_PATH . '/uploads';
$disk_files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($upload_root));
foreach ($iterator as $file) {
    if ($file->isFile()) {
        $rel = str_replace('\\', '/', str_replace(ROOT_PATH . '/', '', $file->getPathname()));
        $basename = basename($file->getPathname());
        $disk_files[$basename][] = $rel;
    }
}
echo "Found " . count($disk_files) . " unique filenames on disk\n\n";

// Now check each document_files record
$r = $conn->query("SELECT df.file_id, df.doc_id, df.file_label, df.original_name, df.stored_name, df.file_path, d.title, dt.type_key
    FROM document_files df
    JOIN documents d ON d.doc_id = df.doc_id
    JOIN document_types dt ON dt.type_id = d.doc_type_id
    ORDER BY df.doc_id");

$fixed = 0;
$still_missing = 0;

while ($row = $r->fetch_assoc()) {
    $full = ROOT_PATH . '/' . $row['file_path'];
    if (file_exists($full)) {
        // Already correct
        continue;
    }

    // Try to find the file by its stored_name (basename)
    $basename = $row['stored_name'];
    if (isset($disk_files[$basename])) {
        $new_path = $disk_files[$basename][0];
        $stmt = $conn->prepare("UPDATE document_files SET file_path = ? WHERE file_id = ?");
        $stmt->bind_param('si', $new_path, $row['file_id']);
        $stmt->execute();
        $stmt->close();
        echo "  FIXED file_id={$row['file_id']}: {$row['file_path']} -> $new_path\n";
        $fixed++;
        continue;
    }

    // Try matching by original_name
    $orig = $row['original_name'];
    if (isset($disk_files[$orig])) {
        $new_path = $disk_files[$orig][0];
        $stmt = $conn->prepare("UPDATE document_files SET file_path = ?, stored_name = ? WHERE file_id = ?");
        $new_stored = basename($new_path);
        $stmt->bind_param('ssi', $new_path, $new_stored, $row['file_id']);
        $stmt->execute();
        $stmt->close();
        echo "  FIXED file_id={$row['file_id']}: {$row['file_path']} -> $new_path (matched by original_name)\n";
        $fixed++;
        continue;
    }

    // Try partial match — look for files in the expected legacy subdirectory
    $type_key = $row['type_key'];
    $legacy_dirs = [
        'journal' => 'published',
        'conference' => 'conference',
        'patent' => 'patents',
        'fdp_attended' => 'fdps',
        'fdp_organised' => 'fdps_org',
        'conf_organised' => 'fdps_org',
        'dept_file' => '',
    ];
    
    $still_missing++;
    echo "  MISSING file_id={$row['file_id']} doc_id={$row['doc_id']} ({$row['type_key']}): {$row['file_path']} — no match found\n";
}

echo "\n--- Summary ---\n";
echo "Fixed: $fixed\n";
echo "Still missing: $still_missing\n";

// Final verification
echo "\n--- Final file status ---\n";
$r = $conn->query("SELECT df.file_id, df.doc_id, df.file_path FROM document_files df ORDER BY df.doc_id");
$exists = 0; $miss = 0;
while ($row = $r->fetch_assoc()) {
    $full = ROOT_PATH . '/' . $row['file_path'];
    if (file_exists($full)) $exists++;
    else $miss++;
}
echo "Files found on disk: $exists\n";
echo "Files still missing: $miss\n";

echo "\nDone.\n";
