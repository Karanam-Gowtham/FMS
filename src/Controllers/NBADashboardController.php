<?php
namespace App\Controllers;

class NBADashboardController {
    public function index() {
        require_once __DIR__ . '/../../core/bootstrap.php';

        // Authentication & Authorization
        require_login();
        $auth = auth_context();
        $active_role = auth_active_role();

        if (!$active_role) {
            die("You must have an active role to access the NBA dashboard.");
        }

        // Filters
        global $conn;
        $academic_years = [];
        $ay_res = $conn->query("SELECT year_label FROM academic_years ORDER BY year_label DESC");
        if ($ay_res) {
            while ($ay_row = $ay_res->fetch_assoc()) {
                $academic_years[] = $ay_row['year_label'];
            }
        }
        if (empty($academic_years)) {
            $academic_years = ['2023-24', '2024-25', '2025-26'];
        }
        $filter_year = isset($_GET['year']) ? trim($_GET['year']) : ($academic_years[0] ?? '2025-26');

        $page_title = 'NBA Dashboard';

        // Fetch pending approvals for reviewer roles
        $pending_approvals = [];
        if (in_array((int)$active_role['role_id'], [ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR])) {
            require_once __DIR__ . '/../../core/document_service.php';
            global $conn;
            $pending_res = doc_list_pending_for_user($conn, $auth, 10, 0);
            $pending_approvals = $pending_res['rows'];
        }

        // Static criteria list based on NBA structure
        $nba_criteria = [
            1 => 'Outcome-Based Curriculum',
            2 => 'Outcome-Based Teaching Learning',
            3 => 'Outcome-Based Assessment',
            4 => 'Students’ Performance',
            5 => 'Faculty Information',
            6 => 'Faculty Contributions',
            7 => 'Facilities and Technical Support',
            8 => 'Continuous Improvement',
            9 => 'Student Support and Governance'
        ];

        include __DIR__ . '/../Views/nba/dashboard.php';
    }
}
