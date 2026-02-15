<?php
include '../includes/connection.php';

echo "<h2>Debug Dept Files</h2>";

// Check columns
$result = $conn->query("SHOW COLUMNS FROM dept_files");
echo "<h3>Columns:</h3><ul>";
while($row = $result->fetch_assoc()){
    echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
}
echo "</ul>";

// Check data
$result = $conn->query("SELECT * FROM dept_files LIMIT 5");
echo "<h3>First 5 Rows:</h3>";
if ($result->num_rows > 0) {
    echo "<table border='1'><tr>";
    $fields = $result->fetch_fields();
    foreach ($fields as $field) {
        echo "<th>{$field->name}</th>";
    }
    echo "</tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $val) {
            echo "<td>" . htmlspecialchars((string)$val) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No rows found in dept_files.";
}

// Check session to see who is logged in
session_start();
echo "<h3>Session:</h3>";
print_r($_SESSION);
?>
