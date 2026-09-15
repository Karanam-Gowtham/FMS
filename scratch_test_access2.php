<?php
require 'includes/connection.php';
require 'includes/dept_scope.php';

$rawPath = 'uploads/certificates/Mars Rover _Problem Statement_AndinoHack.pdf';
$rel = fms_normalize_uploads_relative_path($rawPath);
echo "Normalized relative path: $rel\n";

$variants = [$rel];
if (strpos($rel, 'uploads/') === 0) {
    $tail = substr($rel, strlen('uploads/'));
    $variants[] = $tail;
    $variants[] = '../uploads/' . $tail;
    $variants[] = '../../uploads/' . $tail;
} else {
    $variants[] = 'uploads/' . $rel;
    $variants[] = '../uploads/' . $rel;
    $variants[] = '../../uploads/' . $rel;
}
$variants = array_values(array_unique($variants));
print_r($variants);

// Test query directly
$sql = "SELECT id FROM fdps_tab WHERE certificate IN (?, ?, ?, ?) LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", ...$variants);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
print_r($row);

if ($row) {
    $id = $row['id'];
    echo "Found ID: $id\n";
    $in_scope = fms_dashboard_row_in_scope($conn, 'fdps_tab', $id, 'Dept_Coordinator', 'dc_cse', 'CSE');
    echo "In scope? " . ($in_scope ? 'YES' : 'NO') . "\n";
} else {
    echo "Row not found in fdps_tab.\n";
}
