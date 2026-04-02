<?php
require_once '../includes/session.php';
require_once '../includes/connection.php';
if (!isset($_SESSION['h_username']) && !isset($_SESSION['admin'])) {
    die("You need to log in to view this page.");
}
$dept = isset($_GET['dept']) ? $_GET['dept'] : (isset($_SESSION['dept']) ? $_SESSION['dept'] : '');

// Archive data for Achievement Trends Chart
$years = [2020, 2021, 2022, 2023, 2024];
$chartData = [
    'fdps_attended' => [],
    'fdps_organized' => [],
    'papers' => [],
    'conferences' => [],
    'patents' => []
];
$tables = [
    'fdps_attended' => 'fdps_tab',
    'fdps_organized' => 'fdps_org_tab',
    'papers' => 'published_tab',
    'conferences' => 'conference_tab',
    'patents' => 'patents_table'
];
foreach ($years as $y) {
    foreach ($tables as $key => $tbl) {
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM $tbl WHERE branch = ? AND (year LIKE ? OR year = ?) AND status = 'Accepted'");
        $pattern = $y . "-%";
        $stmt->bind_param("sss", $dept, $pattern, $y);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $chartData[$key][] = (int)$row['cnt'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: rgb(249, 250, 251);
            color: rgb(55, 65, 81);
            line-height: 1.5;
        }

        /* Navigation */
        .navbar {
            background-color: white;
            font-size: larger;
        }

        .nav-container {
            margin-top: 100px;
            margin-left: 100px;
            max-width: 80rem;
            padding: 0 1rem;
        }

        .nav-items {
            display: flex;
            align-items: center;
            height: 4rem;
        }

        .sid {
            color: rgb(48, 30, 138);
            font-weight: 500;
        }

        .main-a {
            color: rgb(138, 30, 113);
            font-weight: 500;
        }

        .main-a:hover {
            color: rgb(182, 64, 211);
        }

        .home-icon {
            color: rgb(30, 58, 138);
            transition: color 0.2s;
        }

        .home-icon:hover {
            color: rgb(29, 78, 216);
        }

        /* Main content */
        .main-content {
            padding: 2rem 1rem;
        }

        .container {
            max-width: 80rem;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .header {
            margin-bottom: 1.5rem;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: bold;
            color: rgb(17, 24, 39);
        }

        /* Feedback Grid */
        .feedback-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.5rem;
            margin-bottom: 50px;
        }

        @media (min-width: 768px) {
            .feedback-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .feedback-card {
            text-decoration: none;
            display: block;
            transition: transform 0.2s;
            background: #fff;
            border: none;
            padding: 0;
            cursor: pointer;
            width: 100%;
            text-align: left;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .feedback-card:hover {
            transform: scale(1.05);
        }

        .card-content {
            background: linear-gradient(to right, rgb(30, 64, 175), rgb(37, 99, 235));
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        #sp {
            color: blue;
        }

        .card-content h3 {
            color: white;
            font-size: 1.25rem;
            font-weight: 600;
        }

        /* Achievement Trends Chart Styles */
        .analytics-container {
            background-color: #2c3e50;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            color: white;
        }
        .analytics-header {
            text-align: center;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #ecf0f1;
        }
        .chart-wrapper {
            position: relative;
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
<?php include_once 'header_hod.php'; ?>

    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="../admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span id="sp">&nbsp; >> &nbsp;</span><span class="main"> <a href="#" class="main-a"> HOD </a></span>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <!-- Achievements Section -->
            <div class="header">
                <h1>Achievements</h1>
            </div>

            <!-- Annual Achievement Trends Chart -->
            <div class="analytics-container">
                <div class="analytics-header">Annual Achievement Trends (<?php echo htmlspecialchars($dept); ?>)</div>
                <div class="chart-wrapper">
                    <canvas id="achievementTrendsChart"></canvas>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('achievementTrendsChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode($years); ?>,
                            datasets: [
                                {
                                    label: 'FDPS Attended',
                                    data: <?php echo json_encode($chartData['fdps_attended']); ?>,
                                    backgroundColor: '#3498db'
                                },
                                {
                                    label: 'FDPS Organized',
                                    data: <?php echo json_encode($chartData['fdps_organized']); ?>,
                                    backgroundColor: '#e74c3c'
                                },
                                {
                                    label: 'Papers Published',
                                    data: <?php echo json_encode($chartData['papers']); ?>,
                                    backgroundColor: '#2ecc71'
                                },
                                {
                                    label: 'Conferences Published',
                                    data: <?php echo json_encode($chartData['conferences']); ?>,
                                    backgroundColor: '#f39c12'
                                },
                                {
                                    label: 'Patents',
                                    data: <?php echo json_encode($chartData['patents']); ?>,
                                    backgroundColor: '#9b59b6'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#bdc3c7',
                                        padding: 20,
                                        font: { size: 12 }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0,0,0,0.8)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff'
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#bdc3c7' }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(255,255,255,0.1)' },
                                    ticks: { color: '#bdc3c7' },
                                    title: {
                                        display: true,
                                        text: 'Number of Achievements',
                                        color: '#bdc3c7'
                                    }
                                }
                            }
                        }
                    });
                });
            </script>

            <div class="feedback-grid">
                <a href="hod_down_fdps_files.php?action_name=fdps" class="feedback-card">
                    <div class="card-content">
                        <h3>View FDPS Attended Files</h3>
                    </div>
                </a>

                <a href="hod_down_fdps_files.php?action_name=fdps_org" class="feedback-card">
                    <div class="card-content">
                        <h3>View FDPS Organized Files</h3>
                    </div>
                </a>

                <a href="hod_down_fdps_files.php?action_name=published" class="feedback-card">
                    <div class="card-content">
                        <h3>View Papers Published Files</h3>
                    </div>
                </a>

                <a href="hod_down_fdps_files.php?action_name=conference" class="feedback-card">
                    <div class="card-content">
                        <h3>View Conferences Published Files</h3>
                    </div>
                </a>

                <a href="hod_down_fdps_files.php?action_name=patents" class="feedback-card">
                    <div class="card-content">
                        <h3>View Patents Files</h3>
                    </div>
                </a>
            </div>

            <!-- Department Files Section -->
            <div class="header">
                <h1>Department Files</h1>
            </div>

            <div class="feedback-grid">
                <a href="hod_down_dept_files.php?event=admin" class="feedback-card">
                    <div class="card-content">
                        <h3>Admin Files</h3>
                    </div>
                </a>

                <a href="hod_down_dept_files.php?event=faculty" class="feedback-card">
                    <div class="card-content">
                        <h3>Faculty Files</h3>
                    </div>
                </a>

                <a href="hod_down_dept_files.php?event=student" class="feedback-card">
                     <div class="card-content">
                        <h3>Student Related Files</h3>
                    </div>
                </a>

                <a href="hod_down_dept_files.php?event=exam" class="feedback-card">
                     <div class="card-content">
                        <h3>Exam Section Files</h3>
                    </div>
                </a>

                <a href="hod_down_dept_files.php?event=calendar" class="feedback-card">
                     <div class="card-content">
                        <h3>Academic Calendar</h3>
                    </div>
                </a>

                <a href="hod_down_st_act_files.php" class="feedback-card">
                     <div class="card-content">
                        <h3>Student Activity Files</h3>
                    </div>
                </a>
            </div>

            <!-- Meeting Minutes Section -->
            <div class="header">
                <h1>Meeting Minutes (Jr Assistant Uploads)</h1>
            </div>

            <div class="feedback-grid">
                <a href="hod_down_dept_files.php?event=Dept Meeting Minutes" class="feedback-card">
                    <div class="card-content">
                        <h3>Department Meeting Minutes</h3>
                    </div>
                </a>

                <a href="hod_down_dept_files.php?event=AMC Meeting Minutes" class="feedback-card">
                    <div class="card-content">
                        <h3>AMC Meeting Minutes</h3>
                    </div>
                </a>

                <a href="hod_down_dept_files.php?event=Board Of Studies" class="feedback-card">
                    <div class="card-content">
                        <h3>Board Of Studies</h3>
                    </div>
                </a>
            </div>
        </div>
    </main>
</body>
</html>