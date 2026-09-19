<?php
ob_start();
// Mock variables so it doesn't redirect
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/mini/FMS/pages/rnd/dashboard.php';
$_GET['year'] = '3';
$_GET['type'] = 'journal';
$_GET['tab'] = 'dept_rnd';

require 'core/bootstrap.php';
// Login as R&D Dean
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = 'rnd@gmrit.edu.in'");
$stmt->execute();
$uid = $stmt->get_result()->fetch_assoc()['user_id'];
$_SESSION['user_id'] = $uid;
$_SESSION['role_id'] = 8;
$_SESSION['dept_id'] = 18;
$_SESSION['logged_in'] = true;
$_SESSION['active_user_role_id'] = 48;

try {
    include 'pages/rnd/dashboard.php';
} catch (Throwable $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine();
}
$out = ob_get_clean();
file_put_contents('scratch_rnd_out.html', $out);
echo "Wrote " . strlen($out) . " bytes\n";
if (strpos($out, '<div class="tabs">') === false) {
    echo "TABS NOT FOUND IN HTML!\n";
} else {
    echo "Tabs found.\n";
}
