<?php
declare(strict_types=1);

namespace App\Controllers;

use mysqli_result;

/**
 * DashboardController
 * 
 * Manages role-tailored dashboards for Faculty, Department Heads,
 * Coordinators, Deans, and Institutional Administrators.
 */
class DashboardController extends BaseController {

    public function index(): void {
        $auth = $this->requireLogin();
        $active_role = $this->activeRole;
        $active_role_id = $this->activeRoleId;
        $user_id = $this->userId;
        $user_roles = $this->auth['roles'] ?? [];

        // If the active role is Student, redirect to student dashboard.
        if ($active_role && $active_role_id === ROLE_STUDENT) {
            $this->redirect(BASE_URL . "/public/index.php?route=student/dashboard");
        }

        // Fetch profile photo
        $profile_photo = null;
        $photo_stmt = $this->conn->prepare("SELECT profile_photo FROM user_profiles WHERE user_id = ?");
        if ($photo_stmt) {
            $photo_stmt->bind_param("i", $user_id);
            $photo_stmt->execute();
            $photo_res = $photo_stmt->get_result();
            if ($photo_row = $photo_res->fetch_assoc()) {
                $profile_photo = $photo_row['profile_photo'];
            }
            $photo_stmt->close();
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

        $role_icon = $active_role ? ($role_icons[$active_role_id] ?? '👤') : '👤';
        $role_landing_url = $active_role ? get_role_landing_url($active_role) : null;

        // Fetch recent uploads for the user
        $recent_uploads = [];
        $chart_categories = [];
        $chart_timeline = [];
        $stats = ['pending' => 0, 'accepted' => 0, 'rejected' => 0, 'draft' => 0];

        $event_date_coalesce = doc_meta_event_date_coalesce_sql('d');
        $meta_joins = doc_meta_joins_sql('d');

        if ($active_role) {
            $stmt = $this->conn->prepare("
                SELECT d.doc_id, d.type_id, d.uploaded_by, d.dept_id, d.mentor_id, d.title, d.status, d.current_step, d.created_at,
                       dt.label as type_label, dep.dept_name, ws.step_label,
                       {$event_date_coalesce} as event_date
                FROM documents d
                JOIN document_types dt ON dt.type_id = d.type_id
                JOIN departments dep ON dep.dept_id = d.dept_id
                LEFT JOIN workflow_steps ws ON ws.step_id = d.current_step
                {$meta_joins}
                WHERE d.uploaded_by = ? 
                ORDER BY d.created_at DESC 
                LIMIT 10
            ");
            if ($stmt) {
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

            // Fetch chart data for Faculty/User Dashboard
            $chart_stmt = $this->conn->prepare("
                SELECT dt.label as type_label, d.status, d.created_at,
                       {$event_date_coalesce} as event_date
                FROM documents d
                JOIN document_types dt ON dt.type_id = d.type_id
                {$meta_joins}
                WHERE d.uploaded_by = ?
            ");
            if ($chart_stmt) {
                $chart_stmt->bind_param('i', $user_id);
                $chart_stmt->execute();
                $agg = $this->aggregateDocumentMetrics($chart_stmt->get_result());
                $stats = $agg['stats'];
                $chart_categories = $agg['categories'];
                $chart_timeline = $agg['timeline'];
                $chart_stmt->close();
            }
        }

        // Fetch pending approvals for reviewer roles (including Faculty acting as Mentors)
        $pending_approvals = [];
        $dept_stats = ['pending' => 0, 'accepted' => 0, 'rejected' => 0];
        $dept_categories = [];
        $dept_timeline = [];

        if ($active_role && $active_role_id !== ROLE_ADMIN && in_array($active_role_id, [ROLE_FACULTY, ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_RND_DEAN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true)) {
            $pending_res = doc_list_pending_for_user($this->conn, $auth, 10, 0);
            $pending_approvals = $pending_res['rows'];
        }

        // Reviewer Analytics (Department / College level)
        if (in_array($active_role_id, [ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true)) {
            $is_college_wide = in_array($active_role_id, [ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true);
            $dept_query = "
                SELECT dt.label as type_label, d.status, d.created_at,
                       {$event_date_coalesce} as event_date
                FROM documents d
                JOIN document_types dt ON dt.type_id = d.type_id
                {$meta_joins}
            ";

            if (!$is_college_wide) {
                $dept_query .= " WHERE d.dept_id = ?";
                $d_stmt = $this->conn->prepare($dept_query);
                if ($d_stmt) {
                    $dept_id_param = (int)($active_role['dept_id'] ?? 0);
                    $d_stmt->bind_param('i', $dept_id_param);
                }
            } else {
                $d_stmt = $this->conn->prepare($dept_query);
            }

            if ($d_stmt) {
                $d_stmt->execute();
                $dept_agg = $this->aggregateDocumentMetrics($d_stmt->get_result());
                $dept_stats = $dept_agg['stats'];
                $dept_categories = $dept_agg['categories'];
                $dept_timeline = $dept_agg['timeline'];
                $d_stmt->close();
            }
        }

        $flash = $this->getFlashMessages();
        $success_msg = $flash['success'] ?? ($this->stringParam('msg', '', 'get'));
        $error_msg   = $flash['error']   ?? ($this->stringParam('err', '', 'get'));

        // Render the view with clean extracted variables
        $this->renderView('dashboard', get_defined_vars());
    }

    /**
     * Shared aggregator for document statistics, categories, and timeline series.
     */
    private function aggregateDocumentMetrics(mysqli_result $result): array {
        $stats = ['pending' => 0, 'accepted' => 0, 'rejected' => 0, 'draft' => 0];
        $categories = [];
        $timeline = [];

        while ($c_row = $result->fetch_assoc()) {
            if (isset($stats[$c_row['status']])) {
                $stats[$c_row['status']]++;
            }

            $cat = !empty($c_row['type_label']) ? $c_row['type_label'] : 'Other';
            $categories[$cat] = ($categories[$cat] ?? 0) + 1;

            $event_dt = (!empty($c_row['event_date']) && $c_row['event_date'] !== '0000-00-00') ? $c_row['event_date'] : ($c_row['created_at'] ?? '');
            $ts = !empty($event_dt) ? strtotime($event_dt) : false;
            if ($ts === false) {
                $ts = !empty($c_row['created_at']) ? (strtotime($c_row['created_at']) ?: time()) : time();
            }
            $date = date('Y-m', $ts);
            $timeline[$date] = ($timeline[$date] ?? 0) + 1;
        }

        ksort($timeline);

        return [
            'stats'      => $stats,
            'categories' => $categories,
            'timeline'   => $timeline
        ];
    }
}
