<?php
include 'includes/connection.php';

// Check if study_year column exists
$check_year = $conn->query("SHOW COLUMNS FROM dept_files LIKE 'study_year'");
if ($check_year->num_rows == 0) {
    // Add study_year column
    if ($conn->query("ALTER TABLE dept_files ADD COLUMN study_year VARCHAR(20) AFTER academic_year")) {
        echo "Column 'study_year' added successfully.\n";
    } else {
        echo "Error adding 'study_year': " . $conn->error . "\n";
    }
} else {
    echo "Column 'study_year' already exists.\n";
}

// Check if semester column exists
$check_sem = $conn->query("SHOW COLUMNS FROM dept_files LIKE 'semester'");
if ($check_sem->num_rows == 0) {
    // Add semester column
    if ($conn->query("ALTER TABLE dept_files ADD COLUMN semester VARCHAR(20) AFTER study_year")) {
        echo "Column 'semester' added successfully.\n";
    } else {
        echo "Error adding 'semester': " . $conn->error . "\n";
    }
} else {
    echo "Column 'semester' already exists.\n";
}
?>
