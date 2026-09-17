<?php
/**
 * NBA Dashboard
 *
 * Dedicated dashboard for NBA criteria management.
 * 
 * URL: nba/dashboard.php
 */
require_once __DIR__ . '/../core/bootstrap.php';

// —— Authentication & Authorization ——
require_login();
$auth = auth_context();
$active_role = auth_active_role();

// —— Hardcoded Filters for now ——
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> &mdash; FMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <style>
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .breadcrumb { margin-bottom: 1rem; color: #6c757d; font-size: 0.9rem; }
        .breadcrumb a { color: #4a90d9; text-decoration: none; }
        .header-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e9ecef; padding-bottom: 1rem; }
        .header-row h1 { margin: 0; color: #333; font-size: 1.8rem; }

        /* Filters */
        .filters { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem; align-items: flex-end; background: #f8f9fa; padding: 1.2rem; border-radius: 8px; border: 1px solid #e9ecef; }
        .filters .form-group { flex: 1; min-width: 200px; max-width: 300px; }
        .filters label { display: block; font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .filters select { width: 100%; padding: 0.6rem; border: 1px solid #ced4da; border-radius: 6px; font-size: 0.95rem; background: #fff; }
        .filters select:focus { outline: none; border-color: #4a90d9; box-shadow: 0 0 0 3px rgba(74, 144, 217, 0.2); }

        /* Criteria Grid */
        .criteria-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.2rem; }
        .criteria-card { 
            background: white; 
            padding: 1.5rem; 
            border: 1px solid #e9ecef; 
            border-radius: 10px; 
            text-decoration: none; 
            color: #333;
            transition: all 0.2s ease-in-out;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            position: relative;
            overflow: hidden;
        }
        
        .criteria-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #4a90d9;
            opacity: 0.8;
        }

        .criteria-card:hover { 
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.08); 
            border-color: #4a90d9;
        }
        
        .criteria-number { 
            font-size: 0.8rem; 
            font-weight: 700; 
            color: #4a90d9; 
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        
        .criteria-title { 
            font-size: 1.1rem; 
            font-weight: 600; 
            color: #2c3e50; 
            line-height: 1.4;
            flex-grow: 1;
        }
        
        .criteria-footer {
            margin-top: 1.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f1f3f5;
            padding-top: 1rem;
        }
        
        .status-badge {
            background: #e9ecef;
            color: #495057;
            padding: 0.25rem 0.6rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .btn-view { 
            color: #4a90d9; 
            font-size: 0.85rem; 
            font-weight: 600; 
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .criteria-card:hover .btn-view { color: #2c3e50; }

        @media (max-width: 768px) {
            .criteria-grid { grid-template-columns: 1fr; }
            .filters .form-group { max-width: 100%; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a> &raquo; NBA Accreditation
    </div>

    <!-- Header -->
    <div class="header-row">
        <h1>NBA Accreditation Dashboard</h1>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="filters">
        <div class="form-group">
            <label for="year">Academic Year</label>
            <select name="year" id="year" onchange="this.form.submit()">
                <?php foreach ($academic_years as $y): ?>
                    <option value="<?= htmlspecialchars($y) ?>" <?= $filter_year === $y ? 'selected' : '' ?>>
                        <?= htmlspecialchars($y) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Future Proofing: Could add Program/Department filters here later -->
    </form>

    <!-- Criteria Grid -->
    <div class="criteria-grid">
        <?php foreach ($nba_criteria as $num => $title): ?>
            <a href="<?= $num === 1 ? BASE_URL . '/nba/criterion1.php?year=' . urlencode($filter_year) : ($num === 2 ? BASE_URL . '/nba/criterion2.php?year=' . urlencode($filter_year) : ($num === 3 ? BASE_URL . '/nba/criterion3.php?year=' . urlencode($filter_year) : '#')) ?>" class="criteria-card">
                <div class="criteria-number">Criterion <?= $num ?></div>
                <div class="criteria-title"><?= htmlspecialchars($title) ?></div>
                
                <div class="criteria-footer">
                    <span class="status-badge">Pending Configuration</span>
                    <span class="btn-view">Manage &rarr;</span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>
