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
            $_SESSION['error_msg'] = "You must select an active role to access the NAAC dashboard.";
            header("Location: " . BASE_URL . "/public/index.php?route=auth/select_role");
            exit();
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

        $success_msg = $_SESSION['success_msg'] ?? ($_GET['msg'] ?? '');
        $error_msg   = $_SESSION['error_msg']   ?? ($_GET['err'] ?? '');
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        include __DIR__ . '/../Views/naac/dashboard.php';
    }
}
