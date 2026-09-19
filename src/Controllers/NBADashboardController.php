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
        $filter_year = isset($_GET['year']) ? trim($_GET['year']) : '2025-26';
        $academic_years = ['2023-24', '2024-25', '2025-26'];

        $page_title = 'NBA Dashboard';

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
