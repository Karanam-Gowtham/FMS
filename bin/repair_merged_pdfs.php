<?php
/**
 * CLI Tool: Re-merge and Heal Multi-File Documents
 *
 * Scans the database for multi-file documents (e.g. Student Activity Files, Events),
 * re-compiles them fresh from their raw source uploads using PdfNormalizer,
 * and eliminates any stale placeholder slips.
 *
 * Usage:
 *   php bin/repair_merged_pdfs.php
 */

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/pdf_merger_service.php';
require_once __DIR__ . '/../core/pdf_normalizer.php';

global $conn;

echo "=======================================================\n";
echo "   FMS Document Healing & PDF Re-Merge Service        \n";
echo "=======================================================\n\n";

if (!$conn || $conn->connect_error) {
    echo "ERROR: Database connection failed.\n";
    exit(1);
}

// 1. Fetch all documents that have records in document_files
$sql = "SELECT DISTINCT d.doc_id, d.title, d.doc_type_id, dt.type_key, dt.type_label, d.file_path as current_file_path
        FROM documents d
        LEFT JOIN document_types dt ON d.doc_type_id = dt.type_id
        ORDER BY d.doc_id ASC";

$result = $conn->query($sql);
if (!$result) {
    echo "ERROR querying documents: " . $conn->error . "\n";
    exit(1);
}

$base_dir = realpath(__DIR__ . '/..');
$total_scanned = 0;
$total_healed = 0;
$total_skipped = 0;

while ($doc = $result->fetch_assoc()) {
    $total_scanned++;
    $doc_id = (int)$doc['doc_id'];
    $type_key = $doc['type_key'] ?: 'document';

    // Query genuine child files (exclude any existing merged_pdf master)
    $stmt = $conn->prepare("SELECT file_id, file_path, file_label FROM document_files WHERE doc_id = ? AND file_label != 'merged_pdf' ORDER BY file_id ASC");
    if (!$stmt) {
        continue;
    }
    $stmt->bind_param('i', $doc_id);
    $stmt->execute();
    $fres = $stmt->get_result();
    
    $child_files = [];
    $supported_exts = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];

    while ($row = $fres->fetch_assoc()) {
        $p = file_exists($row['file_path']) ? $row['file_path'] : ($base_dir . '/' . ltrim($row['file_path'], '/\\'));
        $real_p = realpath($p);
        if ($real_p && file_exists($real_p)) {
            $ext = strtolower(pathinfo($real_p, PATHINFO_EXTENSION));
            if (in_array($ext, $supported_exts, true)) {
                $child_files[] = [
                    'path' => $real_p,
                    'title' => ucfirst(str_replace('_', ' ', $row['file_label']))
                ];
            }
        }
    }
    $stmt->close();

    if (count($child_files) > 1) {
        $safe_type_key = preg_replace('/[^a-z0-9_]/', '', strtolower((string)$type_key));
        $merged_dir = $base_dir . '/uploads/' . $safe_type_key . '/';
        if (!is_dir($merged_dir)) {
            @mkdir($merged_dir, 0755, true);
        }

        $merged_filename = $safe_type_key . '_merged_' . uniqid() . '.pdf';
        $merged_path = $merged_dir . $merged_filename;
        $rel_merged_path = 'uploads/' . $safe_type_key . '/' . $merged_filename;

        try {
            if (merge_pdfs_with_headings($child_files, $merged_path)) {
                // Delete previous merged_pdf record and physical file
                $del_stmt = $conn->prepare("SELECT file_path FROM document_files WHERE doc_id = ? AND file_label = 'merged_pdf'");
                if ($del_stmt) {
                    $del_stmt->bind_param('i', $doc_id);
                    $del_stmt->execute();
                    $dres = $del_stmt->get_result();
                    while ($drow = $dres->fetch_assoc()) {
                        $old_file = $base_dir . '/' . ltrim($drow['file_path'], '/\\');
                        if (file_exists($old_file)) {
                            @unlink($old_file);
                        }
                    }
                    $del_stmt->close();
                }

                $del_rec = $conn->prepare("DELETE FROM document_files WHERE doc_id = ? AND file_label = 'merged_pdf'");
                if ($del_rec) {
                    $del_rec->bind_param('i', $doc_id);
                    $del_rec->execute();
                    $del_rec->close();
                }

                // Update master file_path in documents
                $upd_stmt = $conn->prepare("UPDATE documents SET file_path = ? WHERE doc_id = ?");
                if ($upd_stmt) {
                    $upd_stmt->bind_param('si', $rel_merged_path, $doc_id);
                    $upd_stmt->execute();
                    $upd_stmt->close();
                }

                // Insert updated document_files record
                $fsize = file_exists($merged_path) ? filesize($merged_path) : 0;
                $ins_stmt = $conn->prepare("INSERT INTO document_files (doc_id, original_name, stored_name, file_path, mime_type, file_size, file_label, uploaded_at) VALUES (?, 'Merged_Document.pdf', ?, ?, 'application/pdf', ?, 'merged_pdf', NOW())");
                if ($ins_stmt) {
                    $ins_stmt->bind_param('issis', $doc_id, $merged_filename, $rel_merged_path, $fsize);
                    $ins_stmt->execute();
                    $ins_stmt->close();
                }

                echo "[HEALED] Doc #{$doc_id} ('" . ($doc['title'] ?: $doc['type_label']) . "'): Successfully re-merged " . count($child_files) . " source files into {$rel_merged_path}\n";
                $total_healed++;
            }
        } catch (\Throwable $e) {
            echo "[ERROR] Doc #{$doc_id}: Merge failed - " . $e->getMessage() . "\n";
        }
    } else {
        $total_skipped++;
    }
}

echo "\n-------------------------------------------------------\n";
echo "Healing Complete!\n";
echo "Total Documents Scanned : {$total_scanned}\n";
echo "Multi-File Docs Healed  : {$total_healed}\n";
echo "Single-File Docs Skipped: {$total_skipped}\n";
echo "=======================================================\n";
