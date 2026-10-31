<?php
declare(strict_types=1);

namespace App\Controllers;

use mysqli;

/**
 * BaseController
 * 
 * Provides foundational controller functionality:
 * - Centralized database access ($this->conn)
 * - Standardized session & authentication context ($this->auth, $this->activeRole)
 * - Role-based authorization guards (requireLogin, requireAdmin, requireRole, hasRole)
 * - Flash messaging (flashSuccess, flashError, flashInfo, flashWarning, getFlashMessages)
 * - HTTP request & CSRF handling (isPost, validateCsrf, getParam, postParam, intParam, stringParam)
 * - Safe redirect & JSON response emitters
 * - View rendering helper with variable extraction
 */
abstract class BaseController {

    protected mysqli $conn;
    protected array $auth = [];
    protected ?array $activeRole = null;
    protected int $activeRoleId = 0;
    protected int $activeDeptId = 0;
    protected int $userId = 0;

    public function __construct() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        global $conn;
        $this->conn = $conn;

        $this->auth = auth_context();
        $this->activeRole = auth_active_role();
        $this->activeRoleId = $this->activeRole ? (int)($this->activeRole['role_id'] ?? 0) : 0;
        $this->activeDeptId = $this->activeRole ? (int)($this->activeRole['dept_id'] ?? 0) : 0;
        $this->userId = (int)($this->auth['user_id'] ?? 0);
    }

    /**
     * Enforces user authentication and refreshes state.
     */
    protected function requireLogin(): array {
        require_login();
        $this->auth = auth_context();
        $this->activeRole = auth_active_role();
        $this->activeRoleId = $this->activeRole ? (int)($this->activeRole['role_id'] ?? 0) : 0;
        $this->activeDeptId = $this->activeRole ? (int)($this->activeRole['dept_id'] ?? 0) : 0;
        $this->userId = (int)($this->auth['user_id'] ?? 0);
        return $this->auth;
    }

    /**
     * Enforces active role selection.
     */
    protected function requireActiveRole(): array {
        $this->requireLogin();
        if (!$this->activeRole) {
            $this->flashError("Please select an active role to continue.");
            $this->redirect(BASE_URL . "/public/index.php?route=auth/select_role");
        }
        return $this->activeRole;
    }

    /**
     * Enforces Administrator authorization.
     */
    protected function requireAdmin(): array {
        $this->requireActiveRole();
        if ($this->activeRoleId !== ROLE_ADMIN) {
            $this->flashError("Access denied. This section is restricted to Administrators.");
            $this->redirect(BASE_URL . "/public/index.php?route=dashboard");
        }
        return $this->auth;
    }

    /**
     * Enforces one of multiple permitted roles.
     * 
     * @param int|int[] $allowedRoles
     */
    protected function requireRole(int|array $allowedRoles): array {
        $this->requireActiveRole();
        $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
        if (!in_array($this->activeRoleId, $roles, true)) {
            $this->flashError("You do not have permission to access this module.");
            $this->redirect(BASE_URL . "/public/index.php?route=dashboard");
        }
        return $this->auth;
    }

    /**
     * Checks if active role matches one of the specified roles.
     * 
     * @param int|int[] $roles
     */
    protected function hasRole(int|array $roles): bool {
        $rList = is_array($roles) ? $roles : [$roles];
        return in_array($this->activeRoleId, $rList, true);
    }

    /**
     * Flash messaging helpers
     */
    protected function flashSuccess(string $message): void {
        $_SESSION['success_msg'] = $message;
    }

    protected function flashError(string $message): void {
        $_SESSION['error_msg'] = $message;
    }

    protected function flashInfo(string $message): void {
        $_SESSION['info_msg'] = $message;
    }

    protected function flashWarning(string $message): void {
        $_SESSION['warning_msg'] = $message;
    }

    /**
     * Retrieves and clears all session flash messages.
     */
    protected function getFlashMessages(): array {
        $msgs = [
            'success' => $_SESSION['success_msg'] ?? null,
            'error'   => $_SESSION['error_msg'] ?? null,
            'info'    => $_SESSION['info_msg'] ?? null,
            'warning' => $_SESSION['warning_msg'] ?? null,
        ];
        unset($_SESSION['success_msg'], $_SESSION['error_msg'], $_SESSION['info_msg'], $_SESSION['warning_msg']);
        return $msgs;
    }

    /**
     * HTTP & CSRF Helpers
     */
    protected function isPost(): bool {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function isGet(): bool {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET';
    }

    protected function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_GET['ajax']) && $_GET['ajax'] === '1');
    }

    protected function validateCsrf(?string $token = null): void {
        $submitted = $token ?? ($_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
        if (!verify_csrf($submitted)) {
            if ($this->isAjax() || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                $this->json(['success' => false, 'error' => 'Security token invalid or expired. Please refresh the page.'], 403);
            }
            $this->flashError('Security token invalid or expired. Please try again.');
            $referer = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/public/index.php?route=dashboard');
            $this->redirect($referer);
        }
    }

    /**
     * Request Parameter Extractors
     */
    protected function getParam(string $key, mixed $default = null): mixed {
        return $_GET[$key] ?? $default;
    }

    protected function postParam(string $key, mixed $default = null): mixed {
        return $_POST[$key] ?? $default;
    }

    protected function intParam(string $key, int $default = 0, string $source = 'request'): int {
        $val = match ($source) {
            'get'     => $_GET[$key] ?? null,
            'post'    => $_POST[$key] ?? null,
            'request' => $_REQUEST[$key] ?? null,
            default   => null
        };
        return ($val !== null && is_numeric($val)) ? (int)$val : $default;
    }

    protected function stringParam(string $key, string $default = '', string $source = 'request'): string {
        $val = match ($source) {
            'get'     => $_GET[$key] ?? null,
            'post'    => $_POST[$key] ?? null,
            'request' => $_REQUEST[$key] ?? null,
            default   => null
        };
        return ($val !== null && is_string($val)) ? trim($val) : $default;
    }

    /**
     * Response Helpers
     */
    protected function redirect(string $url): void {
        if (!headers_sent()) {
            header("Location: " . $url);
        } else {
            echo "<script>window.location.href=" . json_encode($url) . ";</script>";
        }
        exit;
    }

    protected function json(array $data, int $statusCode = 200): void {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        exit;
    }

    /**
     * View Renderer Helper
     * 
     * Extracts parameters and includes the view file relative to src/Views/
     * 
     * @param string $viewPath (e.g. 'admin/workflows' or 'dashboard')
     * @param array $data Variables to pass to the view
     */
    protected function renderView(string $viewPath, array $data = []): void {
        extract($data, EXTR_SKIP);
        $file = __DIR__ . '/../Views/' . ltrim($viewPath, '/') . '.php';
        if (file_exists($file)) {
            include $file;
        } else {
            include __DIR__ . '/../../public/404.php';
        }
        exit;
    }
}
