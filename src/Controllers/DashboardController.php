<?php
namespace App\Controllers;

class DashboardController {
    public function index() {
        require_once __DIR__ . '/../../core/bootstrap.php';

        require_login();

        $auth = auth_context();
        $active_role = auth_active_role();
        $user_roles = $auth['roles'] ?? [];

        // Fetch profile photo
        global $conn;
        $profile_photo = null;
        $user_id = $auth['user_id'];
        $photo_stmt = $conn->prepare("SELECT profile_photo FROM user_profiles WHERE user_id = ?");
        if ($photo_stmt) {
            $photo_stmt->bind_param("i", $user_id);
            $photo_stmt->execute();
            $photo_res = $photo_stmt->get_result();
            if ($photo_row = $photo_res->fetch_assoc()) {
                $profile_photo = $photo_row['profile_photo'];
            }
            $photo_stmt->close();
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

            // Fetch chart data for Faculty/User Dashboard
            $chart_categories = [];
            $chart_timeline = [];
            $stats = ['pending' => 0, 'accepted' => 0, 'rejected' => 0];

            $chart_stmt = $conn->prepare("
                SELECT dt.label as type_label, d.status, d.created_at
                FROM documents d
                JOIN document_types dt ON dt.type_id = d.type_id
                WHERE d.uploaded_by = ?
            ");
            $chart_stmt->bind_param('i', $user_id);
            $chart_stmt->execute();
            $chart_res = $chart_stmt->get_result();
            
            while ($c_row = $chart_res->fetch_assoc()) {
                if (isset($stats[$c_row['status']])) {
                    $stats[$c_row['status']]++;
                }
                
                $cat = $c_row['type_label'];
                if (!isset($chart_categories[$cat])) {
                    $chart_categories[$cat] = 0;
                }
                $chart_categories[$cat]++;
                
                $date = date('Y-m', strtotime($c_row['created_at']));
                if (!isset($chart_timeline[$date])) {
                    $chart_timeline[$date] = 0;
                }
                $chart_timeline[$date]++;
            }
            $chart_stmt->close();
            ksort($chart_timeline);
        }

        // Fetch pending approvals for reviewer roles (including Faculty acting as Mentors)
        $pending_approvals = [];
        $dept_stats = ['pending' => 0, 'accepted' => 0, 'rejected' => 0];
        $dept_categories = [];
        $dept_timeline = [];

        if ($active_role && in_array((int)$active_role['role_id'], [ROLE_FACULTY, ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR])) {
            require_once __DIR__ . '/../../core/document_service.php';
            $pending_res = doc_list_pending_for_user($conn, $auth, 10, 0);
            $pending_approvals = $pending_res['rows'];

            // Reviewer Analytics (Department / College level)
            if (in_array((int)$active_role['role_id'], [ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC])) {
                $is_college_wide = in_array((int)$active_role['role_id'], [ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC]);
                $dept_query = "
                    SELECT dt.label as type_label, d.status, d.created_at
                    FROM documents d
                    JOIN document_types dt ON dt.type_id = d.type_id
                ";
                
                if (!$is_college_wide) {
                    $dept_query .= " WHERE d.dept_id = ?";
                    $d_stmt = $conn->prepare($dept_query);
                    $d_stmt->bind_param('i', $active_role['dept_id']);
                } else {
                    $d_stmt = $conn->prepare($dept_query);
                }
                
                $d_stmt->execute();
                $d_res = $d_stmt->get_result();
                while ($c_row = $d_res->fetch_assoc()) {
                    if (isset($dept_stats[$c_row['status']])) {
                        $dept_stats[$c_row['status']]++;
                    }
                    
                    $cat = $c_row['type_label'];
                    if (!isset($dept_categories[$cat])) {
                        $dept_categories[$cat] = 0;
                    }
                    $dept_categories[$cat]++;
                    
                    $date = date('Y-m', strtotime($c_row['created_at']));
                    if (!isset($dept_timeline[$date])) {
                        $dept_timeline[$date] = 0;
                    }
                    $dept_timeline[$date]++;
                }
                $d_stmt->close();
                ksort($dept_timeline);
            }
        }

        // Render the view
        include __DIR__ . '/../Views/dashboard.php';
    }
}
