<?php
require 'e:/set/xampp/htdocs/mini/FMS/core/bootstrap.php';
global $conn;

$res = $conn->query("
    SELECT dm.meta_key, dm.meta_value, count(*) as cnt 
    FROM document_metadata dm 
    JOIN documents d ON dm.doc_id = d.doc_id 
    JOIN document_types dt ON d.type_id = dt.type_id 
    WHERE dt.type_code = 'student_activity_file' 
    GROUP BY dm.meta_key, dm.meta_value
");
while($row = $res->fetch_assoc()) {
    echo $row['meta_key'] . " => " . $row['meta_value'] . " (" . $row['cnt'] . ")\n";
}
