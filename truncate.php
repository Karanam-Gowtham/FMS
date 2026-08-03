<?php
include 'e:/set/xampp/htdocs/mini/FMS/includes/connection.php';

$tables_to_clear = [
    'conference_tab', 'conf_org_tab', 'fdps_tab', 'fdps_org_tab', 
    'published_tab', 'patents_table', 'dept_files', 's_bodies', 
    's_events', 's_journal_tab', 's_conference_tab', 'files', 
    'a_files', 'a_c_files', 'a_cri_files', 'central_files', 
    'dc_up_files', 'files5_1_1and2', 'files5_1_3', 'files5_1_4', 
    'files5_2_1', 'files5_2_2', 'files5_2_3', 'files5_3_1', 'files5_3_3'
];

foreach ($tables_to_clear as $table) {
    try {
        $conn->query("TRUNCATE TABLE `$table`");
    } catch (Exception $e) {
        // Ignore missing tables
    }
}
echo "Live database tables truncated successfully.\n";
?>
