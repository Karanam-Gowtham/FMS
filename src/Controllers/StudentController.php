<?php
namespace App\Controllers;

class StudentController {
    public function dashboard() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_role(ROLE_STUDENT);

        $auth = $_SESSION[SESSION_AUTH_KEY];
        $user_id = (int)$auth['user_id'];
        
        global $conn;
        $stmt = $conn->prepare("
            SELECT d.doc_id, d.status, d.created_at, dt.label as activity_category, d.title as event_title
            FROM documents d
            JOIN document_types dt ON dt.type_id = d.type_id
            WHERE d.uploaded_by = ?
            ORDER BY d.created_at DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $submissions = [];
        $stats = ['pending' => 0, 'accepted' => 0, 'rejected' => 0];
        
        $chart_categories = [];
        $chart_timeline = [];

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
            
            // For Timeline Chart (Group by Month-Year)
            $date = date('Y-m', strtotime($row['created_at']));
            if (!isset($chart_timeline[$date])) {
                $chart_timeline[$date] = 0;
            }
            $chart_timeline[$date]++;
        }
        $stmt->close();
        
        // Sort timeline chronologically
        ksort($chart_timeline);

        // Check if there are academic years (for the form)
        $ay_res = $conn->query("SELECT year_id, year_label FROM academic_years ORDER BY year_label DESC");
        $academic_years = [];
        while ($ay_row = $ay_res->fetch_assoc()) {
            $academic_years[] = [
                'ay_id' => $ay_row['year_id'],
                'ay_name' => $ay_row['year_label']
            ];
        }

        include __DIR__ . '/../Views/student/dashboard.php';
    }
}
