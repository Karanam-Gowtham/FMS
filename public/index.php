<?php
/**
 * FMS Front Controller
 * 
 * All requests are routed through here.
 */

// Simple autoloader for now
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

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
} elseif ($route === 'api/nba/save') {
    $controller = new \App\Controllers\NBAAPIController();
    $controller->save();
} elseif ($route === 'api/nba/upload_pdf') {
    $controller = new \App\Controllers\NBAAPIController();
    $controller->upload_pdf();
} elseif ($route === 'api/nba/generate_pdf') {
    $controller = new \App\Controllers\NBAAPIController();
    $controller->generate_pdf();
} elseif ($route === 'documents/list') {
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
} elseif ($route === 'profile/view') {
    $controller = new \App\Controllers\ProfileController();
    $controller->view();
} elseif ($route === 'profile/edit') {
    $controller = new \App\Controllers\ProfileController();
    $controller->edit();
} elseif ($route === 'academic_years/list') {
    $controller = new \App\Controllers\AcademicYearController();
    $controller->list();
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
} else {
    // 404 Route
    http_response_code(404);
    echo "404 Not Found in MVC Router. Route: " . htmlspecialchars($route);
}
