<?php
/**
 * FMS Application Bootstrap
 * 
 * Single entry point for all new pages. Include this once at the top of every page.
 * Provides: secure session,
 *           DB connection,
 *           config,
 *           auth context,
 *           CSRF.
 * 
 * Usage: require_once __DIR__ . '/../core/bootstrap.php';
 */

// Prevent double-inclusion
if (defined('FMS_BOOTSTRAPPED')) {
    return;
}
define('FMS_BOOTSTRAPPED', true);

// Register App namespace autoloader
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
        require_once $file;
    }
});

// Load configuration
require_once __DIR__ . '/../config.php';

// Secure session bootstrap
require_once __DIR__ . '/../includes/session.php';

// Database connection
require_once __DIR__ . '/../includes/connection.php';

// Core libraries
require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/meta_registry.php';
require_once __DIR__ . '/file_service.php';
require_once __DIR__ . '/workflow_engine.php';
require_once __DIR__ . '/document_service.php';

// CSRF protection
require_once __DIR__ . '/../includes/csrf.php';

// Error handling for production
if (!defined('FMS_DEBUG') || !FMS_DEBUG) {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');

    if (!defined('FMS_EXCEPTION_HANDLER_SET')) {
        define('FMS_EXCEPTION_HANDLER_SET', true);
        set_exception_handler(function (\Throwable $e) {
            error_log("Uncaught Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
            if (!headers_sent()) {
                http_response_code(500);
            }
            $is_api = (isset($_GET['route']) && str_starts_with($_GET['route'], 'api/'))
                   || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
                   || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

            if ($is_api) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'error' => 'An internal server error occurred. Please try again later.'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                exit(1);
            }

            $error_page = __DIR__ . '/../public/500.php';
            if (file_exists($error_page)) {
                include $error_page;
            } else {
                echo "An internal server error occurred. Please contact the administrator.";
            }
            exit(1);
        });
    }
}
