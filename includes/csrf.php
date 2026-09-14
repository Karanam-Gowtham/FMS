<?php
/**
 * CSRF Protection
 * Requires session to be started before use.
 * Currently used by: modules/common/contact.php, admin/jr_assist_lg.php
 */

function csrfToken(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(csrfToken()) . '">';
}

function csrfValidate(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    $submitted = $_POST['_csrf_token'] ?? '';
    $stored = $_SESSION['_csrf_token'] ?? '';

    if (empty($stored) || !hash_equals($stored, $submitted)) {
        http_response_code(403);
        die('CSRF validation failed. Please reload the form and try again.');
    }

    // Regenerate token after successful validation to prevent replay
    unset($_SESSION['_csrf_token']);
}
?>
