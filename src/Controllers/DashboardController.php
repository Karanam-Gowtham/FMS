<?php
namespace App\Controllers;

class DashboardController {
    public function index() {
        require_once __DIR__ . '/../../core/bootstrap.php';

        require_login();

        $auth = auth_context();
        $active_role = auth_active_role();
        $user_roles = $auth['roles'] ?? [];



        // If the active role is Student, redirect to student dashboard.
        if ($active_role && (int)$active_role['role_id'] === ROLE_STUDENT) {
            header("Location: " . BASE_URL . "/public/index.php?route=student/dashboard");
            exit;
        }

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

        // Fetch recent uploads for the user
        $recent_uploads = [];
        if ($active_role) {
            global $conn;
            $stmt = $conn->prepare(
                "SELECT d.doc_id, d.type_id, d.title, d.status, d.current_step, d.created_at, dt.label as type_label, dep.dept_name, ws.step_label
                 FROM documents d
                 JOIN document_types dt ON dt.type_id = d.type_id
                 JOIN departments dep ON dep.dept_id = d.dept_id
                 LEFT JOIN workflow_steps ws ON ws.step_id = d.current_step
                 WHERE d.uploaded_by = ? 
                 ORDER BY d.created_at DESC 
                 LIMIT 10"
            );
            $user_id = $auth['user_id'];
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $res = $stmt->get_result();
            
            while ($row = $res->fetch_assoc()) {
                if ($row['status'] !== 'pending' || !$row['current_step']) {
                    $row['step_label'] = '—';
                }
                $recent_uploads[] = $row;
            }
            $stmt->close();
        }

        // Fetch pending approvals for reviewer roles (including Faculty acting as Mentors)
        $pending_approvals = [];
        if ($active_role && in_array((int)$active_role['role_id'], [ROLE_FACULTY, ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR])) {
            require_once __DIR__ . '/../../core/document_service.php';
            $pending_res = doc_list_pending_for_user($conn, $auth, 10, 0);
            $pending_approvals = $pending_res['rows'];
        }

        // Render the view
        include __DIR__ . '/../Views/dashboard.php';
    }
}
