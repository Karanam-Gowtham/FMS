<?php
namespace App\Controllers;

require_once __DIR__ . '/AbstractAccreditationDashboardController.php';

class NAACDashboardController extends AbstractAccreditationDashboardController {
    protected string $moduleType = 'naac';
    protected string $pageTitle = 'NAAC Dashboard';
    protected string $viewPath = 'naac/dashboard';

    protected array $criteria = [
        1 => 'Curricular Aspects',
        2 => 'Teaching-Learning and Evaluation',
        3 => 'Research, Innovations and Extension',
        4 => 'Infrastructure and Learning Resources',
        5 => 'Student Support and Progression',
        6 => 'Governance, Leadership and Management',
        7 => 'Institutional Values and Best Practices'
    ];
}
