<?php
$conn = mysqli_connect('localhost', 'root', '', 'project-fms');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() . "\n");
}

function addColumnIfNotExists($conn, $table, $column, $definition) {
    $res = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    if (mysqli_num_rows($res) == 0) {
        $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
        if (mysqli_query($conn, $sql)) {
            echo "Added `$column` to `$table` successfully.\n";
        } else {
            echo "Error adding `$column` to `$table`: " . mysqli_error($conn) . "\n";
        }
    } else {
        echo "Column `$column` already exists in `$table`.\n";
    }
}

// 1. Ensure conf_org_tab exists
$res = mysqli_query($conn, "SHOW TABLES LIKE 'conf_org_tab'");
if (mysqli_num_rows($res) == 0) {
    $create_sql = "CREATE TABLE IF NOT EXISTS `conf_org_tab` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(255) DEFAULT NULL,
        `branch` varchar(255) DEFAULT NULL,
        `title` varchar(255) DEFAULT NULL,
        `mode` varchar(50) DEFAULT NULL,
        `date_from` date DEFAULT NULL,
        `date_to` date DEFAULT NULL,
        `organised_by` varchar(255) DEFAULT NULL,
        `location` varchar(255) DEFAULT NULL,
        `brochure` varchar(255) DEFAULT NULL,
        `fdp_schedule_invitation` varchar(255) DEFAULT NULL,
        `attendance_forms` varchar(255) DEFAULT NULL,
        `feedback_forms` varchar(255) DEFAULT NULL,
        `fdp_report` varchar(255) DEFAULT NULL,
        `photo1` varchar(255) DEFAULT NULL,
        `photo2` varchar(255) DEFAULT NULL,
        `photo3` varchar(255) DEFAULT NULL,
        `submission_time` datetime DEFAULT NULL,
        `year` varchar(255) DEFAULT NULL,
        `status` varchar(50) DEFAULT 'Pending HOD',
        `rejection_reason` text DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    if (mysqli_query($conn, $create_sql)) {
        echo "Created table `conf_org_tab` successfully.\n";
    } else {
        echo "Error creating table `conf_org_tab`: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "Table `conf_org_tab` already exists.\n";
}

// 2. Fix fdps_tab columns
addColumnIfNotExists($conn, 'fdps_tab', 'mode', "VARCHAR(50) DEFAULT NULL");
addColumnIfNotExists($conn, 'fdps_tab', 'brochure', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'fdps_tab', 'fdp_schedule', "VARCHAR(255) DEFAULT NULL");

// 3. Fix fdps_org_tab columns
addColumnIfNotExists($conn, 'fdps_org_tab', 'mode', "VARCHAR(50) DEFAULT NULL");
addColumnIfNotExists($conn, 'fdps_org_tab', 'funded_by', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'fdps_org_tab', 'external_funder_name', "VARCHAR(255) DEFAULT NULL");

// 4. Fix patents_table columns
addColumnIfNotExists($conn, 'patents_table', 'type', "VARCHAR(100) DEFAULT NULL");

// 5. Fix published_tab columns
addColumnIfNotExists($conn, 'published_tab', 'authors', "TEXT DEFAULT NULL");
addColumnIfNotExists($conn, 'published_tab', 'issn_no', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'published_tab', 'volume_no', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'published_tab', 'issue_no', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'published_tab', 'page_no', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'published_tab', 'doi', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'published_tab', 'jcr_quartile', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'published_tab', 'scopus_quartile', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'published_tab', 'publication_link', "VARCHAR(255) DEFAULT NULL");

// 6. Fix conference_tab columns
addColumnIfNotExists($conn, 'conference_tab', 'authors', "TEXT DEFAULT NULL");
addColumnIfNotExists($conn, 'conference_tab', 'conference_name', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'conference_tab', 'published_paper_name', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'conference_tab', 'volume_no', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'conference_tab', 'issue_no', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'conference_tab', 'page_no', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'conference_tab', 'indexing', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'conference_tab', 'publication_link', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'conference_tab', 'issn_no', "VARCHAR(255) DEFAULT NULL");
addColumnIfNotExists($conn, 'conference_tab', 'doi', "VARCHAR(255) DEFAULT NULL");

mysqli_close($conn);
echo "Database column synchronization completed.\n";
