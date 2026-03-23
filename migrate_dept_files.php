<?php
include_once "includes/connection.php";

$queries = [
    "ALTER TABLE dept_files ADD COLUMN IF NOT EXISTS semester INT NULL AFTER file_path",
    "ALTER TABLE dept_files ADD COLUMN IF NOT EXISTS study_year INT NULL AFTER semester",
    "ALTER TABLE dept_files ADD COLUMN IF NOT EXISTS meeting_no INT NULL AFTER study_year"
];

foreach ($queries as $q) {
    if ($conn->query($q)) {
        echo "Success: $q\n";
    } else {
        echo "Error: " . $conn->error . " for $q\n";
    }
}

