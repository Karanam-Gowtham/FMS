<?php
namespace App\Controllers;

class NAACDashboardController {
    public function index() {
        require_once __DIR__ . '/../../core/bootstrap.php';

        // Authentication & Authorization
        require_login();
        $auth = auth_context();
        $active_role = auth_active_role();

        if (!$active_role) {
            die("You must have an active role to access the NAAC dashboard.");
        }

        // Filters
        $filter_year = isset($_GET['year']) ? trim($_GET['year']) : '2025-26';
        $academic_years = ['2023-24', '2024-25', '2025-26'];

        $page_title = 'NAAC Dashboard';

        // Fetch pending approvals for reviewer roles
        $pending_approvals = [];
        if (in_array((int)$active_role['role_id'], [ROLE_HOD, ROLE_DEPT_COORDINATOR, ROLE_RND_DEAN, ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR])) {
            require_once __DIR__ . '/../../core/document_service.php';
            global $conn;
            $pending_res = doc_list_pending_for_user($conn, $auth, 10, 0);
            $pending_approvals = $pending_res['rows'];
        }

        // Static criteria list based on NAAC structure
        $naac_criteria = [
            1 => 'Curricular Aspects',
            2 => 'Teaching-Learning and Evaluation',
            3 => 'Research, Innovations and Extension',
            4 => 'Infrastructure and Learning Resources',
            5 => 'Student Support and Progression',
            6 => 'Governance, Leadership and Management',
            7 => 'Institutional Values and Best Practices'
        ];

        include __DIR__ . '/../Views/naac/dashboard.php';
    }
}
