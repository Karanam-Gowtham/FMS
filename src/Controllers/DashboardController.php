<?php
namespace App\Controllers;

class DashboardController {
    public function index() {
        require_once __DIR__ . '/../../core/bootstrap.php';

        require_login();

        $auth = auth_context();
        $active_role = auth_active_role();
        $user_roles = $auth['roles'] ?? [];

        // Role display name mapping
        $role_icons = [
            ROLE_ADMIN               => '🛡️',
            ROLE_IQAC                => '📋',
            ROLE_HOD                 => '👔',
            ROLE_FACULTY             => '📚',
            ROLE_DEPT_COORDINATOR    => '📂',
            ROLE_CENTRAL_COORDINATOR => '🏛️',
            ROLE_JUNIOR_ASSISTANT    => '📝',
            ROLE_RND_DEAN            => '🎓',
        ];

        $role_icon = $active_role ? ($role_icons[$active_role['role_id']] ?? '👤') : '👤';
        
        $role_landing_url = $active_role ? get_role_landing_url($active_role) : null;

        // Render the view
        include __DIR__ . '/../Views/dashboard.php';
    }
}
