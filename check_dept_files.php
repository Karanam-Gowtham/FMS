<?php
include 'includes/connection.php';

$result = $conn->query("DESCRIBE dept_files");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Table 'dept_files' does not exist or error: " . $conn->error;
}
?>
