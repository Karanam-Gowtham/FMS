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
    $submitted = $_POST['_csrf_token'] ?? $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $stored = $_SESSION['_csrf_token'] ?? '';

    if (empty($stored) || !hash_equals($stored, $submitted)) {
        http_response_code(403);
        $error_message = 'CSRF validation failed. Security token expired or invalid. Please reload the page and try again.';
        $page_403 = __DIR__ . '/../public/403.php';
        if (file_exists($page_403)) {
            include $page_403;
        } else {
            echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8');
        }
        exit();
    }
}
?>
