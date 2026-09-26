<?php
namespace App\Controllers;

class StudentController {
    public function dashboard() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_role(ROLE_STUDENT);

        $auth = $_SESSION[SESSION_AUTH_KEY];
        $user_id = (int)$auth['user_id'];
        
        global $conn;

        // Fetch student's submissions
        $stmt = $conn->prepare("
            SELECT d.doc_id, d.status, d.created_at, m.activity_category, m.event_details
            FROM documents d
            JOIN document_meta_student_activity m ON m.doc_id = d.doc_id
            WHERE d.uploaded_by = ?
            ORDER BY d.created_at DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $submissions = [];
        $stats = ['pending' => 0, 'accepted' => 0, 'rejected' => 0];

        while ($row = $res->fetch_assoc()) {
            if ($row['event_details']) {
                $row['event_details'] = json_decode($row['event_details'], true);
            } else {
                $row['event_details'] = [];
            }
            $submissions[] = $row;
            if (isset($stats[$row['status']])) {
                $stats[$row['status']]++;
            }
        }
        $stmt->close();

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
