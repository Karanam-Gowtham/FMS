<?php
/**
 * FMS Configuration
 * Centralized path definitions for the entire application.
 */
define('ROOT_PATH', __DIR__);

if (!defined('DB_NAME')) {
    define('DB_NAME', getenv('DB_NAME') ?: 'gmritfms');
}
if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', getenv('DB_USER') ?: 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', getenv('DB_PASS') ?: '');
}

// Dynamic Base URL detection (works locally and on live servers like InfinityFree)
if (!defined('BASE_URL')) {
    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        ($_SERVER['SERVER_PORT'] ?? 80) == 443 ||
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
        (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
    );
    $protocol = $isHttps ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Calculate subDir cleanly
    $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
    $currentDir = str_replace('\\', '/', __DIR__);
    
    $subDir = '';
    if (!empty($docRoot) && strpos($currentDir, $docRoot) === 0) {
        $subDir = substr($currentDir, strlen($docRoot));
    }
    $subDir = '/' . ltrim(str_replace('\\', '/', $subDir), '/');
    if ($subDir === '/') {
        $subDir = '';
    }
    
    $baseUrl = $protocol . "://" . $host . $subDir;
    define('BASE_URL', rtrim($baseUrl, '/'));
}

define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('ASSETS_URL', BASE_URL . '/assets');
define('IMAGES_PATH', ASSETS_URL . '/img');
define('CSS_PATH', ASSETS_URL . '/css');
define('JS_PATH', ASSETS_URL . '/js');
define('CONNECTION_PATH', INCLUDES_PATH . '/connection.php');
define('PORTAL_PATH', BASE_URL . '/modules');
define('HEADER', INCLUDES_PATH . '/header.php');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('UPLOADS_URL', BASE_URL . '/uploads');
?>