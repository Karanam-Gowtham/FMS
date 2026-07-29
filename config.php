<?php
/**
 * FMS Configuration
 * Centralized path definitions for the entire application.
 */
define('ROOT_PATH', __DIR__);
define('BASE_URL', 'http://localhost/mini/FMS');
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