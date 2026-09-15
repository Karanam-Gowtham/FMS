<?php
include("includes/connection.php");
$stmt = $conn->prepare("SELECT dept_id FROM departments WHERE dept_name = ?");
if (!$stmt) {
    echo "Prepare failed: " . $conn->error . "\n";
} else {
    echo "Prepare successful.\n";
}
