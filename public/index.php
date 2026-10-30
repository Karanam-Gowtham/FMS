<?php
/**
 * FMS Front Controller
 * 
 * All requests are routed through here.
 */

require_once __DIR__ . '/../core/bootstrap.php';

// Basic routing
$route = $_GET['route'] ?? 'dashboard';

if ($route === 'dashboard') {
    $controller = new \App\Controllers\DashboardController();
    $controller->index();
} elseif ($route === 'nba/dashboard') {
    $controller = new \App\Controllers\NBADashboardController();
    $controller->index();
} elseif ($route === 'nba/criterion') {
    $controller = new \App\Controllers\NBACriterionController();
    $controller->show();
} elseif ($route === 'naac/dashboard') {
    $controller = new \App\Controllers\NAACDashboardController();
    $controller->index();
} elseif ($route === 'naac/criterion') {
    $controller = new \App\Controllers\NAACCriterionController();
    $controller->show();
} elseif ($route === 'student_activities/dashboard' || $route === 'student_activity/dashboard' || $route === 'student_activities/list' || $route === 'student_activity/list') {
    $controller = new \App\Controllers\StudentActivityDashboardController();
    $controller->index();
} elseif ($route === 'api/nba/save' || $route === 'api/nba/save_criterion') {
    $controller = new \App\Controllers\NBAAPIController();
    $controller->save();
} elseif ($route === 'api/nba/upload_pdf') {
    $controller = new \App\Controllers\NBAAPIController();
    $controller->upload_pdf();
} elseif ($route === 'api/nba/generate_pdf') {
    $controller = new \App\Controllers\NBAAPIController();
    $controller->generate_pdf();
} elseif ($route === 'api/naac/save' || $route === 'api/naac/save_criterion') {
    $controller = new \App\Controllers\NAACAPIController();
    $controller->save();
} elseif ($route === 'api/naac/upload_pdf') {
    $controller = new \App\Controllers\NAACAPIController();
    $controller->upload_pdf();
} elseif ($route === 'api/naac/generate_pdf') {
    $controller = new \App\Controllers\NAACAPIController();
    $controller->generate_pdf();
} elseif ($route === 'api/documents/accreditation_preview') {
    $controller = new \App\Controllers\DocumentController();
    $controller->accreditationPreview();
} elseif ($route === 'documents/list' || $route === 'documents/department') {
    $controller = new \App\Controllers\DocumentController();
    $controller->index();
} elseif ($route === 'documents/my_uploads') {
    $controller = new \App\Controllers\DocumentController();
    $controller->myUploads();
} elseif ($route === 'documents/upload') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new \App\Controllers\DocumentActionController();
        $controller->processUpload();
    } else {
        $controller = new \App\Controllers\DocumentController();
        $controller->uploadForm();
    }
} elseif ($route === 'documents/view') {
    $controller = new \App\Controllers\DocumentController();
    $controller->view();
} elseif ($route === 'documents/approve') {
    $controller = new \App\Controllers\DocumentActionController();
    $controller->approve();
} elseif ($route === 'documents/download') {
    $controller = new \App\Controllers\DocumentActionController();
    $controller->download();
} elseif ($route === 'documents/bulk_action') {
    $controller = new \App\Controllers\DocumentActionController();
    $controller->bulkAction();
} elseif ($route === 'documents/edit') {
    $controller = new \App\Controllers\DocumentController();
    $controller->edit();
} elseif ($route === 'documents/process_update') {
    $controller = new \App\Controllers\DocumentActionController();
    $controller->processUpdate();
} elseif ($route === 'documents/delete' || $route === 'admin/documents/delete') {
    $controller = new \App\Controllers\DocumentActionController();
    $controller->delete();
} elseif ($route === 'profile/view') {
    $controller = new \App\Controllers\ProfileController();
    $controller->view();
} elseif ($route === 'profile/edit') {
    $controller = new \App\Controllers\ProfileController();
    $controller->edit();
} elseif ($route === 'academic_years/list') {
    $controller = new \App\Controllers\AcademicYearController();
    $controller->list();
} elseif ($route === 'admin/users' || $route === 'users/list') {
    $controller = new \App\Controllers\UserController();
    $controller->index();
} elseif ($route === 'admin/users/set_hod') {
    $controller = new \App\Controllers\UserController();
    $controller->setHod();
} elseif ($route === 'admin/users/toggle_coordinator') {
    $controller = new \App\Controllers\UserController();
    $controller->toggleCoordinator();
} elseif ($route === 'admin/users/toggle_status') {
    $controller = new \App\Controllers\UserController();
    $controller->toggleStatus();
} elseif ($route === 'admin/users/reset_password') {
    $controller = new \App\Controllers\UserController();
    $controller->resetPassword();
} elseif ($route === 'admin/users/delete') {
    $controller = new \App\Controllers\UserController();
    $controller->delete();
} elseif ($route === 'admin/users/create') {
    $controller = new \App\Controllers\UserController();
    $controller->create();
} elseif ($route === 'admin/audit_logs' || $route === 'audit_logs') {
    $controller = new \App\Controllers\AuditController();
    $controller->index();
} elseif ($route === 'public/department') {
    $controller = new \App\Controllers\PublicController();
    $controller->department();
} elseif ($route === 'public/download') {
    $controller = new \App\Controllers\PublicController();
    $controller->download();
} elseif ($route === 'auth/login') {
    $controller = new \App\Controllers\AuthController();
    $controller->login();
} elseif ($route === 'auth/register') {
    $controller = new \App\Controllers\AuthController();
    $controller->register();
} elseif ($route === 'auth/select_role') {
    $controller = new \App\Controllers\AuthController();
    $controller->selectRole();
} elseif ($route === 'auth/logout') {
    $controller = new \App\Controllers\AuthController();
    $controller->logout();
} elseif ($route === 'student/dashboard') {
    $controller = new \App\Controllers\StudentController();
    $controller->dashboard();
} elseif ($route === 'reports/download') {
    $controller = new \App\Controllers\ReportDownloadController();
    $controller->download();
} else {
    // 404 Route
    respond_json_error(404, 'API route not found: ' . htmlspecialchars($route, ENT_QUOTES, 'UTF-8'), 'ROUTE_NOT_FOUND');

    include __DIR__ . '/404.php';
    exit;
}
