<?php
require_once 'config.php';
$result = $conn->query("SHOW TABLES LIKE 'meta_%'");
while ($row = $result->fetch_array()) {
    echo $row[0] . "\n";
    $cols = $conn->query("SHOW COLUMNS FROM " . $row[0]);
    while ($col = $cols->fetch_assoc()) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
}
$result = $conn->query("SELECT * FROM document_types WHERE type_key = 'student_activity_file'");
if ($row = $result->fetch_assoc()) {
    echo "Found student_activity_file in document_types!\n";
    print_r($row);
} else {
    echo "student_activity_file NOT found in document_types!\n";
}
