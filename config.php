<?php

/**
 * FMS Configuration
 * Centralized path definitions for the entire application.
 */
/* =========================================================
   ROOT PATH
   ========================================================= */

define('ROOT_PATH', __DIR__);

/* =========================================================
   ENVIRONMENT & .ENV CONFIGURATION
   ========================================================= */

if (file_exists(ROOT_PATH . '/.env') && is_readable(ROOT_PATH . '/.env')) {
    $env_lines = file(ROOT_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($env_lines !== false) {
        foreach ($env_lines as $env_line) {
            $trimmed = trim($env_line);
            if ($trimmed === '' || $trimmed[0] === '#') {
                continue;
            }
            if (strpos($trimmed, '=') !== false) {
                list($env_key, $env_val) = explode('=', $trimmed, 2);
                $env_key = trim($env_key);
                $env_val = trim($env_val, " \t\n\r\0\x0B\"'");
                if (!array_key_exists($env_key, $_SERVER) && !array_key_exists($env_key, $_ENV)) {
                    putenv("{$env_key}={$env_val}");
                    $_ENV[$env_key] = $env_val;
                    $_SERVER[$env_key] = $env_val;
                }
            }
        }
    }
}

if (!defined('FMS_ENV')) {
    define('FMS_ENV', getenv('APP_ENV') ?: (getenv('FMS_ENV') ?: 'production'));
}

if (!defined('FMS_DEBUG')) {
    $env_debug = getenv('APP_DEBUG') ?: getenv('FMS_DEBUG');
    if ($env_debug !== false && $env_debug !== null && $env_debug !== '') {
        define('FMS_DEBUG', filter_var($env_debug, FILTER_VALIDATE_BOOLEAN));
    } else {
        define('FMS_DEBUG', FMS_ENV === 'development');
    }
}

if (!defined('SESSION_TIMEOUT')) {
    define('SESSION_TIMEOUT', (int)(getenv('SESSION_TIMEOUT') ?: 7200));
}

if (!defined('SESSION_LIFETIME')) {
    define('SESSION_LIFETIME', (int)(getenv('SESSION_LIFETIME') ?: 0));
}

/* =========================================================
   DATABASE CONFIGURATION
   ========================================================= */

if (!defined('DB_NAME')) {
   define('DB_NAME', getenv('DB_NAME') ?: 'gmrdufms');
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
if (!defined('DB_PORT')) {
   define('DB_PORT', (int)(getenv('DB_PORT') ?: 3306));
}

/* =========================================================
   BASE URL
   automatically find your FMS project’s URL
   ========================================================= */

if (!defined('BASE_URL')) {
   if (getenv('BASE_URL')) {
       define('BASE_URL', rtrim(getenv('BASE_URL'), '/'));
   } else {
       $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
       $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

       $root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
       $path = str_replace('\\', '/', __DIR__);

       if (!empty($root) && stripos($path, $root) === 0) {
           $projectPath = substr($path, strlen($root));
       } else {
           if (preg_match('#/htdocs(/.*)$#i', $path, $matches)) {
               $projectPath = $matches[1];
           } else {
               $projectPath = '/mini/FMS';
           }
       }
       $projectPath = '/' . ltrim($projectPath, '/');

       define('BASE_URL', rtrim($protocol . '://' . $host . $projectPath, '/'));
   }
}
/* =========================================================
   INCLUDES
   ========================================================= */

define('INCLUDES_PATH', ROOT_PATH . '/includes');
/* =========================================================
   ASSETS
   ========================================================= */

define('ASSETS_URL', BASE_URL . '/assets');
define('IMAGES_PATH', ASSETS_URL . '/img');
define('CSS_PATH', ASSETS_URL . '/css');
define('JS_PATH', ASSETS_URL . '/js');

/* =========================================================
   APPLICATION FILES
   ========================================================= */

define('CONNECTION_PATH', INCLUDES_PATH . '/connection.php');
define('HEADER', INCLUDES_PATH . '/header.php');

/* =========================================================
   MODULES
   ========================================================= */
define('PORTAL_PATH', BASE_URL . '/modules');
/* =========================================================
   UPLOADS
   ========================================================= */

define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('UPLOADS_URL', BASE_URL . '/uploads');

/* =========================================================
   API / AJAX REQUEST HELPER
   ========================================================= */

if (!function_exists('is_api_request')) {
    function is_api_request(): bool {
        return (isset($_GET['route']) && str_starts_with($_GET['route'], 'api/'))
            || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
            || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }
}

if (!function_exists('respond_json_error')) {
    function respond_json_error(int $statusCode, string $message, string $code = 'ERROR'): void {
        if (!function_exists('is_api_request') || !is_api_request()) {
            return;
        }
        while (ob_get_level()) {
            ob_end_clean();
        }
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'status'  => 'error',
            'error'   => $message,
            'message' => $message,
            'code'    => $code
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        exit();
    }
}
