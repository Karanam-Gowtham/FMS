<?php
require 'includes/connection.php';

$queries = [
    // Document_Categories
    "CREATE TABLE IF NOT EXISTS `Document_Categories` (
        `category_id` int(11) NOT NULL AUTO_INCREMENT,
        `category_name` varchar(100) NOT NULL,
        PRIMARY KEY (`category_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    // Document_Types
    "CREATE TABLE IF NOT EXISTS `Document_Types` (
        `type_id` int(11) NOT NULL AUTO_INCREMENT,
        `type_name` varchar(100) NOT NULL,
        `category_id` int(11) DEFAULT NULL,
        PRIMARY KEY (`type_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    // Academic_Years
    "CREATE TABLE IF NOT EXISTS `Academic_Years` (
        `academic_year_id` int(11) NOT NULL AUTO_INCREMENT,
        `year_name` varchar(20) NOT NULL,
        `is_active` tinyint(1) DEFAULT 0,
        PRIMARY KEY (`academic_year_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    // Approval_Flow
    "CREATE TABLE IF NOT EXISTS `Approval_Flow` (
        `flow_id` int(11) NOT NULL AUTO_INCREMENT,
        `type_id` int(11) NOT NULL,
        `sequence_no` int(11) NOT NULL,
        `current_role_id` int(11) NOT NULL,
        PRIMARY KEY (`flow_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

foreach ($queries as $q) {
    if ($conn->query($q)) {
        echo "Successfully executed query.\n";
    } else {
        echo "Error executing query: " . $conn->error . "\n";
    }
}

// Insert dummy data if empty so JOINs don't fail immediately
$conn->query("INSERT IGNORE INTO Academic_Years (academic_year_id, year_name, is_active) VALUES (1, '2023-2024', 1)");
$conn->query("INSERT IGNORE INTO Document_Categories (category_id, category_name) VALUES (1, 'General')");
$conn->query("INSERT IGNORE INTO Document_Types (type_id, type_name, category_id) VALUES (1, 'Misc', 1)");

$conn->close();
echo "Done fixing missing RBAC tables.\n";
?>
