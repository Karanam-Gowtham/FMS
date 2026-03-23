<?php
include_once 'includes/connection.php';

echo "<h2>Cleaning up Duplicate Rejection History</h2>";

// finding duplicates
$sql = "
    SELECT file_id, table_name, rejection_reason, created_at, GROUP_CONCAT(id) as ids, COUNT(*) as count
    FROM rejection_history
    GROUP BY file_id, table_name, rejection_reason, created_at
    HAVING count > 1
";

$result = $conn->query($sql);
$deleted_total = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Found duplicate group: File ID " . $row['file_id'] . " (" . $row['count'] . " copies)<br>";
        
        $ids = explode(',', $row['ids']);
        // Keep the first one (or last, doesn't matter for duplicates)
        $keep_id = array_shift($ids);
        
        // Delete the rest
        if (!empty($ids)) {
            $ids_to_delete = implode(',', $ids);
            $delete_sql = "DELETE FROM rejection_history WHERE id IN ($ids_to_delete)";
            if ($conn->query($delete_sql)) {
                $deleted_count = count($ids);
                $deleted_total += $deleted_count;
                echo "Deleted $deleted_count duplicate rows.<br>";
            } else {
                echo "Error deleting: " . $conn->error . "<br>";
            }
        }
    }
} else {
    echo "No duplicates found.<br>";
}

echo "<h3>Total deleted: $deleted_total</h3>";
