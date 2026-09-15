<?php
require 'includes/connection.php';
$count_sql = "SELECT COUNT(*) as total FROM documents d
              JOIN document_types dt ON dt.type_id = d.type_id";
$count_stmt = $conn->prepare($count_sql);
if (!$count_stmt) {
    echo "Error: " . $conn->error . "\n";
} else {
    echo "Count works\n";
}
