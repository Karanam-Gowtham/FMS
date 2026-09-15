<?php
require 'includes/connection.php';
require 'includes/dept_scope.php';

$res = fms_verify_file_path_access($conn, 'uploads/certificates/cse - fdps 1 org.pdf', 'Department Coordinator', 'dc_cse', 'CSE');
echo $res ? 'YES' : 'NO';
