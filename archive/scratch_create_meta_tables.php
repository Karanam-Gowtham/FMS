<?php
require_once __DIR__ . '/core/bootstrap.php';
require_once __DIR__ . '/core/meta_registry.php';

$types = [
    'journal', 'conference', 'patent', 'fdp_attended', 'fdp_organised',
    'conf_organised', 'criteria_file', 'dept_file', 'central_file',
    'scholarship', 'placement', 'higher_ed', 'exam_qual', 'award',
    'student_event', 'student_body', 'student_journal', 'student_conference'
];

foreach ($types as $type_key) {
    $table_name = meta_get_table($type_key);
    $fields = meta_get_fields($type_key);
    
    $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
        `meta_id` int(11) NOT NULL AUTO_INCREMENT,
        `doc_id` int(11) NOT NULL,
    ";
    
    foreach ($fields as $f) {
        $name = $f['name'];
        if ($f['type'] === 'number') {
            $sql .= "`$name` varchar(255) DEFAULT NULL,\n"; // Use varchar to be safe since numbers might be strings
        } else if ($f['type'] === 'textarea') {
            $sql .= "`$name` text DEFAULT NULL,\n";
        } else if ($f['type'] === 'date') {
            $sql .= "`$name` date DEFAULT NULL,\n";
        } else {
            $sql .= "`$name` varchar(255) DEFAULT NULL,\n";
        }
    }
    
    $sql .= "
        PRIMARY KEY (`meta_id`),
        KEY `idx_doc_id` (`doc_id`),
        CONSTRAINT `fk_{$table_name}_doc` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    if ($conn->query($sql) === TRUE) {
        echo "Created table $table_name\n";
    } else {
        echo "Error creating $table_name: " . $conn->error . "\n";
    }
}
