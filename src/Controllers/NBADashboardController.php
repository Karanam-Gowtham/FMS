<?php
namespace App\Controllers;

require_once __DIR__ . '/AbstractAccreditationDashboardController.php';

class NBADashboardController extends AbstractAccreditationDashboardController {
    protected string $moduleType = 'nba';
    protected string $pageTitle = 'NBA Dashboard';
    protected string $viewPath = 'nba/dashboard';

    protected array $criteria = [
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
}
