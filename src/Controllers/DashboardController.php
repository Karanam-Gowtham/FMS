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
        $user_id = (int)$auth['user_id'];
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

        $role_icon = $active_role ? ($role_icons[(int)($active_role['role_id'] ?? 0)] ?? '👤') : '👤';
        $role_landing_url = $active_role ? get_role_landing_url($active_role) : null;

        // Fetch recent uploads for the user
        $recent_uploads = [];
        if ($active_role) {
            global $conn;
            $stmt = $conn->prepare(
                "SELECT d.doc_id, d.type_id, d.uploaded_by, d.dept_id, d.mentor_id, d.title, d.status, d.current_step, d.created_at, dt.label as type_label, dep.dept_name, ws.step_label,
                        COALESCE(
                            msaf.date_from,
                            mc.from_date,
                            mj.date_of_publication,
                            mp.date_of_issue,
                            mfa.date_from,
                            mfo.date_from,
                            mco.date_from,
                            mse.from_date,
                            msb.from_date,
                            msc.from_date,
                            msj.date_of_submission,
                            d.created_at
                        ) as event_date
                 FROM documents d
                 JOIN document_types dt ON dt.type_id = d.type_id
                 JOIN departments dep ON dep.dept_id = d.dept_id
                 LEFT JOIN workflow_steps ws ON ws.step_id = d.current_step
                 LEFT JOIN meta_student_activity_file msaf ON msaf.doc_id = d.doc_id
                 LEFT JOIN meta_conference mc ON mc.doc_id = d.doc_id
                 LEFT JOIN meta_journal mj ON mj.doc_id = d.doc_id
                 LEFT JOIN meta_patent mp ON mp.doc_id = d.doc_id
                 LEFT JOIN meta_fdp_attended mfa ON mfa.doc_id = d.doc_id
                 LEFT JOIN meta_fdp_organised mfo ON mfo.doc_id = d.doc_id
                 LEFT JOIN meta_conf_organised mco ON mco.doc_id = d.doc_id
                 LEFT JOIN meta_student_event mse ON mse.doc_id = d.doc_id
                 LEFT JOIN meta_student_body msb ON msb.doc_id = d.doc_id
                 LEFT JOIN meta_student_conference msc ON msc.doc_id = d.doc_id
                 LEFT JOIN meta_student_journal msj ON msj.doc_id = d.doc_id
                 WHERE d.uploaded_by = ? 
                 ORDER BY d.created_at DESC 
                 LIMIT 10"
            );
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
            $chart_categories = [];
            $chart_timeline = [];
            $stats = ['pending' => 0, 'accepted' => 0, 'rejected' => 0, 'draft' => 0];

            $chart_stmt = $conn->prepare("
                SELECT dt.label as type_label, d.status, d.created_at,
                       COALESCE(
                           msaf.date_from,
                           mc.from_date,
                           mj.date_of_publication,
                           mp.date_of_issue,
                           mfa.date_from,
                           mfo.date_from,
                           mco.date_from,
                           mse.from_date,
                           msb.from_date,
                           msc.from_date,
                           msj.date_of_submission,
                           d.created_at
                       ) as event_date
                FROM documents d
                JOIN document_types dt ON dt.type_id = d.type_id
                LEFT JOIN meta_student_activity_file msaf ON msaf.doc_id = d.doc_id
                LEFT JOIN meta_conference mc ON mc.doc_id = d.doc_id
                LEFT JOIN meta_journal mj ON mj.doc_id = d.doc_id
                LEFT JOIN meta_patent mp ON mp.doc_id = d.doc_id
                LEFT JOIN meta_fdp_attended mfa ON mfa.doc_id = d.doc_id
                LEFT JOIN meta_fdp_organised mfo ON mfo.doc_id = d.doc_id
                LEFT JOIN meta_conf_organised mco ON mco.doc_id = d.doc_id
                LEFT JOIN meta_student_event mse ON mse.doc_id = d.doc_id
                LEFT JOIN meta_student_body msb ON msb.doc_id = d.doc_id
                LEFT JOIN meta_student_conference msc ON msc.doc_id = d.doc_id
                LEFT JOIN meta_student_journal msj ON msj.doc_id = d.doc_id
                WHERE d.uploaded_by = ?
            ");
            if ($chart_stmt) {
                $chart_stmt->bind_param('i', $user_id);
                $chart_stmt->execute();
                $chart_res = $chart_stmt->get_result();
                
                while ($c_row = $chart_res->fetch_assoc()) {
                    if (isset($stats[$c_row['status']])) {
                        $stats[$c_row['status']]++;
                    }
                    
                    $cat = !empty($c_row['type_label']) ? $c_row['type_label'] : 'Other';
                    if (!isset($chart_categories[$cat])) {
                        $chart_categories[$cat] = 0;
                    }
                    $chart_categories[$cat]++;
                    
                    $event_dt = (!empty($c_row['event_date']) && $c_row['event_date'] !== '0000-00-00') ? $c_row['event_date'] : ($c_row['created_at'] ?? '');
                    $ts = !empty($event_dt) ? strtotime($event_dt) : false;
                    if ($ts === false) {
                        $ts = !empty($c_row['created_at']) ? (strtotime($c_row['created_at']) ?: time()) : time();
                    }
                    $date = date('Y-m', $ts);
                    if (!isset($chart_timeline[$date])) {
                        $chart_timeline[$date] = 0;
                    }
                    $chart_timeline[$date]++;
                }
                $chart_stmt->close();
                ksort($chart_timeline);
            }
        }

        // Fetch pending approvals for reviewer roles (including Faculty acting as Mentors)
        $pending_approvals = [];
        $dept_stats = ['pending' => 0, 'accepted' => 0, 'rejected' => 0];
        $dept_categories = [];
        $dept_timeline = [];

        if ($active_role && in_array((int)$active_role['role_id'], [ROLE_FACULTY, ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true)) {
            require_once __DIR__ . '/../../core/document_service.php';
            $pending_res = doc_list_pending_for_user($conn, $auth, 10, 0);
            $pending_approvals = $pending_res['rows'];

            // Reviewer Analytics (Department / College level)
            if (in_array((int)$active_role['role_id'], [ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true)) {
                $is_college_wide = in_array((int)$active_role['role_id'], [ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true);
                $dept_query = "
                    SELECT dt.label as type_label, d.status, d.created_at,
                           COALESCE(
                               msaf.date_from,
                               mc.from_date,
                               mj.date_of_publication,
                               mp.date_of_issue,
                               mfa.date_from,
                               mfo.date_from,
                               mco.date_from,
                               mse.from_date,
                               msb.from_date,
                               msc.from_date,
                               msj.date_of_submission,
                               d.created_at
                           ) as event_date
                    FROM documents d
                    JOIN document_types dt ON dt.type_id = d.type_id
                    LEFT JOIN meta_student_activity_file msaf ON msaf.doc_id = d.doc_id
                    LEFT JOIN meta_conference mc ON mc.doc_id = d.doc_id
                    LEFT JOIN meta_journal mj ON mj.doc_id = d.doc_id
                    LEFT JOIN meta_patent mp ON mp.doc_id = d.doc_id
                    LEFT JOIN meta_fdp_attended mfa ON mfa.doc_id = d.doc_id
                    LEFT JOIN meta_fdp_organised mfo ON mfo.doc_id = d.doc_id
                    LEFT JOIN meta_conf_organised mco ON mco.doc_id = d.doc_id
                    LEFT JOIN meta_student_event mse ON mse.doc_id = d.doc_id
                    LEFT JOIN meta_student_body msb ON msb.doc_id = d.doc_id
                    LEFT JOIN meta_student_conference msc ON msc.doc_id = d.doc_id
                    LEFT JOIN meta_student_journal msj ON msj.doc_id = d.doc_id
                ";
                
                $d_stmt = null;
                if (!$is_college_wide) {
                    $dept_query .= " WHERE d.dept_id = ?";
                    $d_stmt = $conn->prepare($dept_query);
                    if ($d_stmt) {
                        $dept_id_param = (int)($active_role['dept_id'] ?? 0);
                        $d_stmt->bind_param('i', $dept_id_param);
                    }
                } else {
                    $d_stmt = $conn->prepare($dept_query);
                }
                
                if ($d_stmt) {
                    $d_stmt->execute();
                    $d_res = $d_stmt->get_result();
                    while ($c_row = $d_res->fetch_assoc()) {
                        if (isset($dept_stats[$c_row['status']])) {
                            $dept_stats[$c_row['status']]++;
                        }
                        
                        $cat = !empty($c_row['type_label']) ? $c_row['type_label'] : 'Other';
                        if (!isset($dept_categories[$cat])) {
                            $dept_categories[$cat] = 0;
                        }
                        $dept_categories[$cat]++;
                        
                        $event_dt = (!empty($c_row['event_date']) && $c_row['event_date'] !== '0000-00-00') ? $c_row['event_date'] : ($c_row['created_at'] ?? '');
                        $ts = !empty($event_dt) ? strtotime($event_dt) : false;
                        if ($ts === false) {
                            $ts = !empty($c_row['created_at']) ? (strtotime($c_row['created_at']) ?: time()) : time();
                        }
                        $date = date('Y-m', $ts);
                        if (!isset($dept_timeline[$date])) {
                            $dept_timeline[$date] = 0;
                        }
                        $dept_timeline[$date]++;
                    }
                    $d_stmt->close();
                    ksort($dept_timeline);
                }
            }
        }

        $raw_success = $_SESSION['success_msg'] ?? ($_GET['msg'] ?? '');
        $raw_error   = $_SESSION['error_msg']   ?? ($_GET['err'] ?? '');
        $success_msg = is_string($raw_success) ? $raw_success : '';
        $error_msg   = is_string($raw_error)   ? $raw_error   : '';
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        // Render the view
        include __DIR__ . '/../Views/dashboard.php';
    }
}
