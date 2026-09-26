<?php
namespace App\Controllers;

class DashboardController {
    public function index() {
        require_once __DIR__ . '/../../core/bootstrap.php';

        require_login();

        $auth = auth_context();
        $active_role = auth_active_role();
        $user_roles = $auth['roles'] ?? [];

        // If the active role is NBA/NAAC, they should use the NBA module dashboard.
        if ($active_role && (in_array((int)$active_role['role_id'], [ROLE_CENTRAL_COORDINATOR, ROLE_IQAC]))) {
            header("Location: " . BASE_URL . "/public/index.php?route=nba/dashboard");
            exit;
        }

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
                "SELECT d.doc_id, d.type_id, d.title, d.status, d.current_step, d.created_at, dt.label as type_label, dep.dept_name
                 FROM documents d
                 JOIN document_types dt ON dt.type_id = d.type_id
                 JOIN departments dep ON dep.dept_id = d.dept_id
                 WHERE d.uploaded_by = ? 
                 ORDER BY d.created_at DESC 
                 LIMIT 10"
            );
            $user_id = $auth['user_id'];
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $res = $stmt->get_result();
            
            // Prepare a statement for workflow step lookups
            $st_stmt = $conn->prepare("SELECT label FROM workflow_steps ws JOIN document_types dt ON ws.workflow_id = dt.workflow_id WHERE dt.type_id = ? AND ws.step_order = ?");
            
            while ($row = $res->fetch_assoc()) {
                $row['step_label'] = '—';
                if ($row['status'] === 'pending') {
                    $st_stmt->bind_param('ii', $row['type_id'], $row['current_step']);
                    $st_stmt->execute();
                    $st_res = $st_stmt->get_result();
                    if ($st_row = $st_res->fetch_assoc()) {
                        $row['step_label'] = $st_row['label'];
                    }
                }
                $recent_uploads[] = $row;
            }
            $st_stmt->close();
            $stmt->close();
        }

        // Fetch pending approvals for reviewer roles
        $pending_approvals = [];
        if ($active_role && in_array((int)$active_role['role_id'], [ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR])) {
            require_once __DIR__ . '/../../core/document_service.php';
            $pending_res = doc_list_pending_for_user($conn, $auth, 10, 0);
            $pending_approvals = $pending_res['rows'];
        }

        // Render the view
        include __DIR__ . '/../Views/dashboard.php';
    }
}
