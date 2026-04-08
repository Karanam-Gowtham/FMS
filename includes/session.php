<?php
// Centralized session bootstrap with secure cookie flags
include_once __DIR__ . '/constants.php';
if (session_status() === PHP_SESSION_NONE) {
    // Detect if the current connection is secure (HTTPS)
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    // Security best practices for sessions
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => (bool)$isSecure, // SECURE_ONLY: Allow HTTP for localhost development, enforce Secure on HTTPS connections
        'httponly' => true,        // Prevent JavaScript access to session cookie
        'samesite' => 'Lax',       // Cross-site cookie restriction
    ]);
    
    session_start();
}

