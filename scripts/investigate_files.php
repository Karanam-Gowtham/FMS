<?php
/**
 * Comprehensive View/Download diagnosis
 */
require_once __DIR__ . '/../core/bootstrap.php';

echo "=== View/Download Diagnosis ===\n\n";

// 1. Check which document_files actually have files on disk
echo "--- File existence summary ---\n";
$r = $conn->query("SELECT df.file_id, df.doc_id, df.file_path, d.title FROM document_files df JOIN documents d ON d.doc_id = df.doc_id ORDER BY df.doc_id");
$found = 0; $missing = 0;
while ($row = $r->fetch_assoc()) {
    $full = ROOT_PATH . '/' . $row['file_path'];
    if (file_exists($full)) {
        $found++;
        echo "  EXISTS: file_id={$row['file_id']} doc_id={$row['doc_id']} -> {$row['file_path']}\n";
    } else {
        $missing++;
    }
}
echo "  Total: $found exist, $missing missing\n\n";

// 2. Look at what the legacy tables actually store as file paths
echo "--- Legacy published_tab file paths ---\n";
$r = $conn->query("SELECT id, paper_title, paper_file FROM published_tab LIMIT 5");
while ($row = $r->fetch_assoc()) {
    echo "  id={$row['id']}: {$row['paper_file']}\n";
    if (!empty($row['paper_file'])) {
        // Check if the path exists relative to ROOT_PATH
        $abs = ROOT_PATH . '/' . $row['paper_file'];
        echo "    absolute: $abs [" . (file_exists($abs) ? 'EXISTS' : 'MISSING') . "]\n";
    }
}

echo "\n--- Legacy patents_table file paths ---\n";
$r = $conn->query("SELECT id, patent_title, patent_file FROM patents_table LIMIT 5");
while ($row = $r->fetch_assoc()) {
    echo "  id={$row['id']}: {$row['patent_file']}\n";
    if (!empty($row['patent_file'])) {
        $abs = ROOT_PATH . '/' . $row['patent_file'];
        echo "    absolute: $abs [" . (file_exists($abs) ? 'EXISTS' : 'MISSING') . "]\n";
    }
}

echo "\n--- Legacy fdps_tab file paths ---\n";
$r = $conn->query("SELECT id, title, certificate FROM fdps_tab LIMIT 5");
while ($row = $r->fetch_assoc()) {
    echo "  id={$row['id']}: {$row['certificate']}\n";
    if (!empty($row['certificate'])) {
        $abs = ROOT_PATH . '/' . $row['certificate'];
        echo "    absolute: $abs [" . (file_exists($abs) ? 'EXISTS' : 'MISSING') . "]\n";
    }
}

echo "\n--- Legacy dept_files file paths ---\n";
$r = $conn->query("SELECT id, file_name, file_path FROM dept_files LIMIT 5");
while ($row = $r->fetch_assoc()) {
    echo "  id={$row['id']}: {$row['file_path']}\n";
    if (!empty($row['file_path'])) {
        $abs = ROOT_PATH . '/' . $row['file_path'];
        echo "    absolute: $abs [" . (file_exists($abs) ? 'EXISTS' : 'MISSING') . "]\n";
    }
}

// 3. Test file_download function path resolution for an existing file
echo "\n--- file_download path resolution test ---\n";
$existing = $conn->query("SELECT df.file_id, df.file_path FROM document_files df LIMIT 1")->fetch_assoc();
if ($existing) {
    $full = ROOT_PATH . '/' . $existing['file_path'];
    echo "  file_id={$existing['file_id']}: ROOT_PATH/{$existing['file_path']}\n";
    echo "  Resolved to: $full\n";
    echo "  Exists: " . (file_exists($full) ? 'YES' : 'NO') . "\n";
}

echo "\nDone.\n";
