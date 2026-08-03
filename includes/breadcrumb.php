<?php
// Dynamic Breadcrumb Generator
// This assumes $dept is set in the parent file, or we can fetch it from session.
$current_script = basename($_SERVER['SCRIPT_NAME']);
if ($current_script === 'index.php' || strpos($current_script, 'login') !== false || $current_script === 'dashboard.php') {
    return; // Do not render breadcrumbs on home/login screens
}

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($dept)) {
    if (isset($_GET['dept'])) {
        $dept = $_GET['dept'];
    } elseif (isset($_SESSION['username'])) {
        // Fetch from db if conn is available
        if (isset($conn)) {
            $stmt = $conn->prepare("SELECT dept FROM reg_tab WHERE userid = ?");
            if ($stmt) {
                $stmt->bind_param("s", $_SESSION['username']);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    $dept = $row['dept'];
                }
            }
        }
    }
}
$dept = isset($dept) ? $dept : '';

$current_script = basename($_SERVER['SCRIPT_NAME']);
$dir_name = basename(dirname($_SERVER['SCRIPT_NAME']));

$breadcrumbs = [];
$breadcrumbs[] = ['label' => '<svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="vertical-align: middle;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>', 'url' => (defined('BASE_URL') ? BASE_URL : '/mini/FMS') . '/index.php'];

// Map directories to roles
if ($dir_name === 'faculty') {
    $role = 'Faculty';
    $role_url = 'acd_year.php';
} elseif ($dir_name === 'dept_coordinator') {
    $role = 'Dept Coordinator';
    $role_url = 'dc_acd_year.php';
} elseif ($dir_name === 'HOD') {
    $role = 'HOD';
    $role_url = 'HOD_lg.php'; // or whatever dashboard
} elseif ($dir_name === 'admin') {
    $role = 'Admin';
    $role_url = 'admins.php';
} elseif ($dir_name === 'central') {
    $role = 'Central';
    $role_url = 'c_login_n.php';
} else {
    $role = ucfirst($dir_name);
    $role_url = '#';
}

if ($dept) {
    // We don't always know exactly where the dept dashboard is, but typically it's the module root or public/dept.php
    $breadcrumbs[] = ['label' => 'Department(' . htmlspecialchars($dept) . ')', 'url' => (defined('BASE_URL') ? BASE_URL : '/mini/FMS') . '/public/dept.php?dept=' . urlencode($dept)];
}

$breadcrumbs[] = ['label' => $role, 'url' => $role_url . ($dept ? "?dept=" . urlencode($dept) : "")];

// Map specific files to names
$file_map = [
    'fdps.php' => 'FDPS Attended',
    'fdps_org.php' => 'FDPS Organised',
    'conference.php' => 'Conferences Attended',
    'conf_org.php' => 'Conferences Organised',
    'published.php' => 'Published Papers',
    'patents.php' => 'Patents',
    'student_act.php' => 'Student Activities',
    's_conference.php' => 'Student Conferences',
    's_journal.php' => 'Student Journals',
    'dept_files.php' => 'Department Files',
    'upload.php' => 'Upload Files',
    'view_file.php' => 'View File',
    'dashboard_profiles.php' => 'Dashboard Profiles',
    'edit_profile.php' => 'Edit Profile',
    'acd_year.php' => 'Academic Year Selection',
];

$page_name = isset($file_map[$current_script]) ? $file_map[$current_script] : ucfirst(str_replace('.php', '', str_replace('_', ' ', $current_script)));

if ($current_script !== $role_url) {
    $breadcrumbs[] = ['label' => $page_name, 'url' => '#'];
}
?>

<style>
.custom-breadcrumb {
    padding: 15px 5%;
    display: flex;
    align-items: center;
    background: transparent;
    color: rgb(48, 30, 138);
    font-size: 16px;
    font-weight: 500;
}
.custom-breadcrumb a {
    color: rgb(30, 58, 138);
    text-decoration: none;
    transition: color 0.2s;
    display: flex;
    align-items: center;
}
.custom-breadcrumb a:hover {
    color: rgb(182, 64, 211);
}
.custom-breadcrumb .separator {
    color: blue;
    margin: 0 10px;
}
.custom-breadcrumb .current-page {
    color: rgb(138, 30, 113);
}
.custom-breadcrumb .current-page:hover {
    color: rgb(182, 64, 211);
}
</style>

<div class="custom-breadcrumb">
    <?php
    $count = count($breadcrumbs);
    foreach ($breadcrumbs as $index => $crumb) {
        if ($index == $count - 1) {
            echo '<span class="current-page">' . $crumb['label'] . '</span>';
        } else {
            echo '<a href="' . $crumb['url'] . '">' . $crumb['label'] . '</a>';
            echo '<span class="separator">&nbsp;>>&nbsp;</span>';
        }
    }
    ?>
</div>
