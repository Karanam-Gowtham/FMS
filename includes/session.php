<?php
// Centralized session bootstrap with secure cookie flags
if (session_status() === PHP_SESSION_NONE) {
    // Detect if the current connection is secure (HTTPS)
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    // Security best practices for sessions (configure only before headers are dispatched)
    if (!headers_sent()) {
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_secure', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');

        $lifetime = defined('SESSION_LIFETIME') ? (int)SESSION_LIFETIME : 0;
        session_set_cookie_params($lifetime, '/', '', true, true);
    }

    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
}

