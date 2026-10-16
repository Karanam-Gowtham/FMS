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

// Fallback for public or unauthenticated browsing
if ($role === 'Dashboard') {
    if (!empty($_GET['dept'])) {
        $dept = trim($_GET['dept']);
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

// Campus-wide institutional pages link directly from Home without injecting a personal role/dept
$institutional_routes = [
    'student_activities/dashboard',
    'nba/dashboard',
    'naac/dashboard',
    'public/department',
];

if (!in_array($route, $institutional_routes)) {
    $breadcrumbs[] = ['label' => $role_label, 'url' => $role_url];
}



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
