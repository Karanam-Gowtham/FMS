<?php
require 'includes/connection.php';
$tables = ['files', 'files5_1_1and2', 'files5_1_3', 'files5_1_4', 'files5_2_1', 'files5_2_2', 'files5_2_3', 'files5_3_1', 'files5_3_3', 'fdps_tab', 'fdps_org_tab', 'conference_tab', 'conf_org_tab', 'published_tab', 'patents_table', 's_journal_tab', 's_conference_tab', 's_events', 's_bodies', 'dept_files'];
foreach ($tables as $t) {
    $res = $conn->query("SHOW COLUMNS FROM `$t`");
    if (!$res) continue;
    $cols = [];
    while($row = $res->fetch_assoc()) {
        $f = strtolower($row['Field']);
        if(strpos($f, 'path') !== false || strpos($f, 'file') !== false || strpos($f, 'cert') !== false || strpos($f, 'brochure') !== false || strpos($f, 'doc') !== false) {
            $cols[] = $row['Field'];
        }
    }
    echo "$t: " . implode(', ', $cols) . "\n";
}
?>
