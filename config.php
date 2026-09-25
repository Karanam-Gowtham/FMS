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
   define('DB_PORT', getenv('DB_PORT') ?: 3306);
}

/* =========================================================
   BASE URL
   automatically find your FMS project’s URL
   ========================================================= */

if (!defined('BASE_URL')) {
   $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
   $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
   /*
    * Find the folder in which the FMS project is located.
    */
   $documentRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
   $currentDirectory = str_replace('\\', '/', __DIR__);
   $subDirectory = '';
   if (!empty($documentRoot) && strpos($currentDirectory, $documentRoot) === 0) {
      $subDirectory = substr($currentDirectory, strlen($documentRoot));
   }
   $subDirectory = '/' . ltrim($subDirectory, '/');
   if ($subDirectory === '/') {
      $subDirectory = '';
   }
   define('BASE_URL', rtrim($protocol . '://' . $host . $subDirectory, '/'));
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

?>