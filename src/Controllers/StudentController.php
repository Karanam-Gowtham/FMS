<?php
declare(strict_types=1);

namespace App\Controllers;

use mysqli_result;

/**
 * StudentController
 * 
 * Manages the self-service student achievement dashboard, upload metrics, and progress tracking.
 */
class StudentController extends BaseController {

    public function dashboard(): void {
        $this->requireRole(ROLE_STUDENT);

        $auth = $this->auth;
        $user_id = $this->userId;
        $roll_number = !empty($auth['email']) ? strtoupper(explode('@', $auth['email'])[0]) : 'Student';

        $event_date_coalesce = doc_meta_event_date_coalesce_sql('d');
        $meta_joins = doc_meta_joins_sql('d');

        $stmt = $this->conn->prepare("
            SELECT d.doc_id, d.uploaded_by, d.dept_id, d.status, d.created_at,
                   COALESCE(msaf.activity_category, dt.label) as activity_category,
                   COALESCE(msaf.event_title, d.title) as event_title,
                   {$event_date_coalesce} as event_date
            FROM documents d
            JOIN document_types dt ON dt.type_id = d.type_id
            {$meta_joins}
            WHERE d.uploaded_by = ?
            ORDER BY d.created_at DESC
        ");

        $submissions = [];
        $stats = ['pending' => 0, 'accepted' => 0, 'rejected' => 0, 'draft' => 0];
        $chart_categories = [];
        $chart_timeline = [];

        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $res = $stmt->get_result();

            while ($row = $res->fetch_assoc()) {
                $submissions[] = $row;
                if (isset($stats[$row['status']])) {
                    $stats[$row['status']]++;
                }

                $cat = $row['activity_category'] ?: 'Uncategorized';
                $chart_categories[$cat] = ($chart_categories[$cat] ?? 0) + 1;

                $event_dt = (!empty($row['event_date']) && $row['event_date'] !== '0000-00-00') ? $row['event_date'] : $row['created_at'];
                $ts = strtotime($event_dt);
                if ($ts === false) {
                    $ts = !empty($row['created_at']) ? (strtotime($row['created_at']) ?: time()) : time();
                }
                $date = date('Y-m', $ts);
                $chart_timeline[$date] = ($chart_timeline[$date] ?? 0) + 1;
            }
            $stmt->close();
        }

        ksort($chart_timeline);

        // Fetch academic years for student forms
        $ay_res = $this->conn->query("SELECT year_id, year_label FROM academic_years ORDER BY year_label DESC");
        $academic_years = [];
        if ($ay_res instanceof mysqli_result) {
            while ($ay_row = $ay_res->fetch_assoc()) {
                $academic_years[] = [
                    'ay_id' => (int)$ay_row['year_id'],
                    'ay_name' => $ay_row['year_label']
                ];
            }
            $ay_res->close();
        }

        $flash = $this->getFlashMessages();
        $success_msg = $flash['success'] ?? ($this->stringParam('msg', '', 'get'));
        $error_msg   = $flash['error']   ?? ($this->stringParam('err', '', 'get'));

        $this->renderView('student/dashboard', get_defined_vars());
    }
}
