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

/**
 * SMTP Configuration
 * Used by includes/send_email.php for PHPMailer.
 * Move these to environment variables in production.
 */
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_AUTH', true);
define('SMTP_USERNAME', 'gowtham.lite@gmail.com');
define('SMTP_PASSWORD', 'uqyk efpk muqq usfa');
define('SMTP_FROM_EMAIL', 'gowtham.lite@gmail.com');
define('SMTP_FROM_NAME', 'FMS Notification System');
?>