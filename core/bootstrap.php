<?php
/**
 * FMS Application Bootstrap
 * 
 * Single entry point for all new pages. Include this once at the top of every page.
 * Provides: secure session, DB connection, config, auth context, CSRF.
 * 
 * Usage: require_once __DIR__ . '/../core/bootstrap.php';
 */

// Prevent double-inclusion
if (defined('FMS_BOOTSTRAPPED')) {
    return;
}
define('FMS_BOOTSTRAPPED', true);

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
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}
