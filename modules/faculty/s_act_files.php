<?php
$qs = $_SERVER['QUERY_STRING'] ?? '';
header('Location: ../../pages/upload.php?type=student_event&' . $qs);
exit;
