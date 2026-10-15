<?php
// Dynamic Breadcrumb Generator
// Fits inline inside the global header navigation bar.

$current_script = basename($_SERVER['SCRIPT_NAME']);
$route = $_GET['route'] ?? '';

// Do not render breadcrumbs on home/login/registration screens
if ($current_script === 'reg.php' || strpos($current_script, 'login') !== false || ($current_script === 'index.php' && empty($route)) || in_array($route, ['auth/login', 'auth/register', 'auth/select_role'])) {
    return;
}

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$dir_name = basename(dirname($_SERVER['SCRIPT_NAME']));
$bc_base_url = (defined('BASE_URL') ? BASE_URL : '/mini/FMS');

$role = 'Dashboard';
$role_url = $bc_base_url . '/index.php';
$dept = '';

// Check modern unified auth first
if (function_exists('auth_is_logged_in') && auth_is_logged_in()) {
    $active_role = function_exists('auth_active_role') ? auth_active_role() : null;
    if ($active_role) {
        $role = ($active_role['role_name'] ?? 'User') . ' Dashboard';
        $role_url = function_exists('get_role_landing_url') ? get_role_landing_url($active_role) : ($bc_base_url . '/public/index.php?route=dashboard');
        $dept = $active_role['dept_name'] ?? '';
    }
}

// Fallback to legacy session detection if modern auth is not active
if ($role === 'Dashboard') {
    // Determine $dept from legacy sources
    if (!empty($_GET['dept'])) {
        $dept = $_GET['dept'];
    } elseif (!empty($_GET['event']) && in_array(strtoupper($_GET['event']), ['CSE', 'CSE-CS', 'CSE-AI&ML', 'CSE-AI&DS', 'IT', 'ECE', 'EEE', 'MECH', 'CIVIL', 'BSH', 'MATHEMATICS', 'PHYSICS', 'CHEMISTRY', 'NAAC', 'NBA', 'NCC', 'SPORTS', 'CLUBS'])) {
        $dept = $_GET['event'];
    } elseif (!empty($_SESSION['dept'])) {
        $dept = $_SESSION['dept'];
    } else {
        $user_session = $_SESSION['a_username'] ?? $_SESSION['h_username'] ?? $_SESSION['admin'] ?? $_SESSION['c_cord'] ?? $_SESSION['cri_username'] ?? $_SESSION['j_username'] ?? $_SESSION['username'] ?? null;
        if ($user_session && isset($conn)) {
            $stmt = $conn->prepare("SELECT dept FROM reg_tab WHERE userid = ?");
            if ($stmt) {
                $stmt->bind_param("s", $user_session);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    $dept = $row['dept'];
                }
                $stmt->close();
            }
        }
    }
    $dept = trim($dept);

    if (isset($_SESSION['a_username']) && !empty($_SESSION['a_username'])) {
        $role = 'Dept Coordinator Dashboard';
        $role_url = $bc_base_url . '/modules/dept_coordinator/dc_acd_year.php';
    } elseif (isset($_SESSION['h_username']) && !empty($_SESSION['h_username'])) {
        $role = 'HOD Dashboard';
        $role_url = $bc_base_url . '/HOD/hod_acd_year.php';
    } elseif (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
        $role = 'Admin Dashboard';
        $role_url = $bc_base_url . '/HOD/acd_year_aa.php';
    } elseif (isset($_SESSION['cri_username']) && !empty($_SESSION['cri_username'])) {
        $role = 'Criteria Dashboard';
        $role_url = $bc_base_url . '/modules/central/c_aqar_files.php?designation=criteria_coordinator' . (!empty($dept) ? '&event=' . urlencode($dept) : '');
    } elseif (isset($_SESSION['c_cord']) && !empty($_SESSION['c_cord'])) {
        $role = 'Central Dashboard';
        $role_url = $bc_base_url . '/modules/central/c_upload.php' . (!empty($dept) ? '?event=' . urlencode($dept) : '');
    } elseif (isset($_SESSION['j_username']) && !empty($_SESSION['j_username'])) {
        $role = 'Jr Assistant Dashboard';
        $role_url = $bc_base_url . '/modules/jr_assistant/jr_acd_year.php';
    } elseif (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        $role = 'Faculty Dashboard';
        $role_url = $bc_base_url . '/modules/faculty/acd_year.php';
    } else {
        if ($dir_name === 'faculty') {
            $role = 'Faculty Dashboard';
            $role_url = $bc_base_url . '/modules/faculty/acd_year.php';
        } elseif ($dir_name === 'dept_coordinator') {
            $role = 'Dept Coordinator Dashboard';
            $role_url = $bc_base_url . '/modules/dept_coordinator/dc_acd_year.php';
        } elseif ($dir_name === 'HOD') {
            $role = 'HOD Dashboard';
            $role_url = $bc_base_url . '/HOD/hod_acd_year.php'; 
        } elseif ($dir_name === 'admin') {
            $role = 'Admin Dashboard';
            $role_url = $bc_base_url . '/HOD/acd_year_aa.php';
        } elseif ($dir_name === 'central') {
            $role = 'Central Dashboard';
            $role_url = $bc_base_url . '/modules/central/cc_acd_year.php';
        } elseif ($dir_name === 'jr_assistant') {
            $role = 'Jr Assistant Dashboard';
            $role_url = $bc_base_url . '/modules/jr_assistant/jr_acd_year.php';
        } elseif ($dir_name === 'public') {
            $role = 'Public View';
            $role_url = $bc_base_url . '/index.php';
        } else {
            $role = ucfirst(str_replace('_', ' ', $dir_name));
        }
    }
}

$breadcrumbs = [];
// Home link
$home_icon_svg = '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="vertical-align: middle;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 3l9 7.5M5 10v11h14V10M9 21v-6h6v6"/></svg>';
$breadcrumbs[] = [
    'label' => $home_icon_svg, 
    'url' => $bc_base_url . '/index.php',
    'title' => 'Home'
];

$role_label = $role;
if ($dept !== '') {
    $role_label .= ' (' . htmlspecialchars($dept) . ')';
    if ($role_url !== '#') {
        $role_url .= (strpos($role_url, '?') !== false ? '&' : '?') . "dept=" . urlencode($dept);
    }
}

$breadcrumbs[] = ['label' => $role_label, 'url' => $role_url];

// File Map
$file_map = [
    // Faculty Module
    'acd_year.php' => 'Academic Year Selection',
    'conf_org.php' => 'Conferences Organized',
    'conference.php' => 'Conferences Attended',
    'criteria.php' => 'Criteria Uploads',
    'dashboard_profiles.php' => 'Faculty Profiles',
    'edit_profile.php' => 'Edit Profile',
    'fdps.php' => 'FDPs Attended',
    'fdps_org.php' => 'FDPs Organized',
    'my_uploads_new.php' => 'My Uploads',
    'patents.php' => 'Patents Upload',
    'published.php' => 'Published Papers',
    's_act_files.php' => 'Student Activity Files',
    's_body_files.php' => 'Student Body Files',
    's_conference.php' => 'Student Conferences',
    's_down_files.php' => 'Student Downloads',
    's_down_files1.php' => 'Student Activity Files',
    's_journal.php' => 'Student Journals',
    's_p_bodies.php' => 'Student Professional Bodies',
    's_papers.php' => 'Student Papers',
    'student_act.php' => 'Student Activities',
    'up5.1.1&2.php' => 'Upload Criteria 5.1.1 & 5.1.2',
    'up5.1.3.php' => 'Upload Criteria 5.1.3',
    'up5.1.4.php' => 'Upload Criteria 5.1.4',
    'up5.2.1.php' => 'Placement Details Form (5.2.1)',
    'up5.2.2.php' => 'Upload Criteria 5.2.2',
    'up5.2.3.php' => 'Upload Criteria 5.2.3',
    'up5.3.1.php' => 'Upload Criteria 5.3.1',
    'up5.3.3.php' => 'Upload Criteria 5.3.3',
    'upload.php' => 'Upload Criteria File',

    // Dept Coordinator Module
    'dc_acd_year.php' => 'Academic Year Selection',
    'dept_files.php' => 'Department Files',
    'dept_down_files.php' => 'Department Downloads',
    'dc_down_dept_files.php' => 'Department Files Download',
    'dc_down_fdps_files.php' => 'FDP Files Download',
    'dc_down_st_act_files.php' => 'Student Activity Files Download',
    'dc_down_st_act_files_hod.php' => 'Student Activity Files (HOD View)',
    'dc_down_up_files.php' => 'Uploaded Files Download',
    'dc_up_files.php' => 'Department File Upload',
    'department.php' => 'Department Overview',
    'dept_meeting_minutes.php' => 'Department Meeting Minutes',
    'bos_meeting_minutes.php' => 'BOS Meeting Minutes',
    'amc_meeting_minutes.php' => 'AMC Meeting Minutes',

    // Central Module
    'cc_acd_year.php' => 'Central Academic Year Selection',
    'cc_down_dc_files.php' => 'Central Coordinator Files',
    'c_aqar_files.php' => 'AQAR Files',
    'c_down_files.php' => 'Central Downloads',
    'c_down_files_by_cord.php' => 'Coordinator Files',
    'c_upload.php' => 'Central Upload',
    'central_events.php' => 'Central Events',

    // HOD Module
    'hod_acd_year.php' => 'HOD Dashboard',
    'central_dashboard.php' => 'HOD Dashboard',
    'acd_year_aa.php' => 'AQAR Academic Year',
    'admin_criteria.php' => 'Criteria Management',
    'add_cri_form.php' => 'Add Criteria',
    'edit_acd_year.php' => 'Manage Academic Years',
    'files_view_fac.php' => 'Faculty Uploaded Files',
    'hod_down_dept_files.php' => 'Department Files',
    'hod_down_fdps_files.php' => 'FDP Files',
    'hod_down_st_act_files.php' => 'Student Activity Files',
    'hod_fac_download.php' => 'Faculty Downloads',
    'hod_faculty_files.php' => 'Faculty Files List',
    'hod_manage_meeting_minutes.php' => 'Manage Meeting Minutes',

    // Common & Public
    'dashboard.php' => 'Main Dashboard',
    'pdf_merger.php' => 'PDF Merger Tool',
    'view_file.php' => 'View Document',
    'view_file1.php' => 'View Document',
    'download_papers1.php' => 'Download Papers',
    'admins.php' => 'Admin Panel',
    'dept.php' => 'Department Details',
];

if (isset($custom_breadcrumb_title)) {
    $page_name = $custom_breadcrumb_title;
} elseif (!empty($route)) {
    $route_map = [
        'dashboard'                     => 'Main Dashboard',
        'documents/list'                => 'Documents',
        'documents/upload'              => 'Upload Document',
        'documents/view'                => 'View Document',
        'documents/edit'                => 'Edit Document',
        'documents/my_uploads'          => 'My Uploads',
        'student/dashboard'             => 'Student Dashboard',
        'student_activities/dashboard'  => 'Student Activities Dashboard',
        'nba/dashboard'                 => 'NBA Dashboard',
        'naac/dashboard'                => 'NAAC Dashboard',
        'public/department'             => 'Department Overview',
        'profile/edit'                  => 'Edit Profile',
        'academic_years/list'           => 'Academic Years',
    ];

    if ($route === 'documents/upload' && !empty($_GET['activity'])) {
        $page_name = 'Upload: ' . htmlspecialchars($_GET['activity']);
    } elseif ($route === 'documents/list' && isset($_GET['context']) && $_GET['context'] === 'dept_file') {
        $page_name = 'Department Files';
    } elseif ($route === 'public/department' && !empty($_GET['dept'])) {
        $page_name = htmlspecialchars($_GET['dept']) . ' Department';
    } elseif (isset($route_map[$route])) {
        $page_name = $route_map[$route];
    } else {
        $page_name = ucwords(str_replace(['/', '_'], ' ', $route));
    }
} elseif (isset($file_map[$current_script])) {
    $page_name = $file_map[$current_script];
} else {
    $page_name = ucwords(str_replace(['_', '.php'], [' ', ''], $current_script));
}

// Only append page_name if current script/route is not the role dashboard itself
$role_path = parse_url($role_url, PHP_URL_PATH);
$role_query = parse_url($role_url, PHP_URL_QUERY);
$is_dashboard_page = ($current_script === basename($role_path) && (empty($route) || (isset($role_query) && strpos($role_query, 'route=' . $route) !== false)));

if (!$is_dashboard_page) {
    $breadcrumbs[] = ['label' => $page_name, 'url' => '#'];
}
?>

<style>
.custom-breadcrumb {
    display: inline-flex;
    align-items: center;
    font-size: 13.5px;
    font-weight: 500;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    color: #e2e8f0;
    margin-left: 12px;
    flex-wrap: nowrap;
    white-space: nowrap;
}
.custom-breadcrumb a {
    color: #94a3b8;
    text-decoration: none;
    transition: color 0.2s ease;
    display: inline-flex;
    align-items: center;
}
.custom-breadcrumb a:hover {
    color: #38bdf8;
}
.custom-breadcrumb .separator {
    color: #64748b;
    margin: 0 6px;
    display: inline-flex;
    align-items: center;
}
.custom-breadcrumb .current-page {
    color: #38bdf8;
    font-weight: 600;
}
@media (max-width: 900px) {
    .custom-breadcrumb {
        font-size: 12px;
    }
    .custom-breadcrumb .hide-mobile {
        display: none;
    }
}
</style>

<div class="custom-breadcrumb">
    <?php
    $count = count($breadcrumbs);
    foreach ($breadcrumbs as $index => $crumb) {
        if ($index == $count - 1) {
            echo '<span class="current-page">' . $crumb['label'] . '</span>';
        } else {
            echo '<a href="' . $crumb['url'] . '"' . (isset($crumb['title']) ? ' title="' . $crumb['title'] . '"' : '') . '>' . $crumb['label'] . '</a>';
            echo '<span class="separator"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>';
        }
    }
    ?>
</div>
