<?php
$qs = $_SERVER['QUERY_STRING'] ?? '';
header('Location: ../../pages/upload.php?type=dept_file&' . $qs);
exit;
