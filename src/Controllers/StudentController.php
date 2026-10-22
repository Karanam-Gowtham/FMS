<?php
namespace App\Controllers;

class StudentController {
    public function dashboard() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_role(ROLE_STUDENT);

        $auth = auth_context();
        $user_id = (int)($auth['user_id'] ?? 0);
        $roll_number = !empty($auth['email']) ? strtoupper(explode('@', $auth['email'])[0]) : 'Student';
        
        global $conn;
        $stmt = $conn->prepare("
            SELECT d.doc_id, d.status, d.created_at,
                   COALESCE(msaf.activity_category, dmsa.activity_category, dt.label) as activity_category,
                   COALESCE(msaf.event_title, d.title) as event_title,
                   COALESCE(
                       msaf.date_from,
                       mse.from_date,
                       msb.from_date,
                       msc.from_date,
                       msj.date_of_submission,
                       d.created_at
                   ) as event_date
            FROM documents d
            JOIN document_types dt ON dt.type_id = d.type_id
            LEFT JOIN meta_student_activity_file msaf ON msaf.doc_id = d.doc_id
            LEFT JOIN document_meta_student_activity dmsa ON dmsa.doc_id = d.doc_id
            LEFT JOIN meta_student_event mse ON mse.doc_id = d.doc_id
            LEFT JOIN meta_student_body msb ON msb.doc_id = d.doc_id
            LEFT JOIN meta_student_conference msc ON msc.doc_id = d.doc_id
            LEFT JOIN meta_student_journal msj ON msj.doc_id = d.doc_id
            WHERE d.uploaded_by = ?
            ORDER BY d.created_at DESC
        ");
        $submissions = [];
        $stats = ['pending' => 0, 'accepted' => 0, 'rejected' => 0];
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
                
                // For Categories Chart
                $cat = $row['activity_category'] ?: 'Uncategorized';
                if (!isset($chart_categories[$cat])) {
                    $chart_categories[$cat] = 0;
                }
                $chart_categories[$cat]++;
                
                // For Timeline Chart (Group by Month-Year based on event start date)
                $event_dt = (!empty($row['event_date']) && $row['event_date'] !== '0000-00-00') ? $row['event_date'] : $row['created_at'];
                $ts = strtotime($event_dt);
                if ($ts === false) {
                    $ts = !empty($row['created_at']) ? (strtotime($row['created_at']) ?: time()) : time();
                }
                $date = date('Y-m', $ts);
                if (!isset($chart_timeline[$date])) {
                    $chart_timeline[$date] = 0;
                }
                $chart_timeline[$date]++;
            }
            $stmt->close();
        }
        
        // Sort timeline chronologically
        ksort($chart_timeline);

        // Check if there are academic years (for the form)
        $ay_res = $conn->query("SELECT year_id, year_label FROM academic_years ORDER BY year_label DESC");
        $academic_years = [];
        if ($ay_res) {
            while ($ay_row = $ay_res->fetch_assoc()) {
                $academic_years[] = [
                    'ay_id' => $ay_row['year_id'],
                    'ay_name' => $ay_row['year_label']
                ];
            }
        }

        $success_msg = $_SESSION['success_msg'] ?? ($_GET['msg'] ?? '');
        $error_msg   = $_SESSION['error_msg']   ?? ($_GET['err'] ?? '');
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        include __DIR__ . '/../Views/student/dashboard.php';
    }
}
