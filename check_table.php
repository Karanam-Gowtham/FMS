<?php
include_once 'includes/connection.php';

$result = $conn->query("DESCRIBE files");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Table 'files' does not exist or error: " . $conn->error;
}
