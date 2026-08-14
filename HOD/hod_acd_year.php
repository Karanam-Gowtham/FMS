<?php
include "header_hod.php";
include "../includes/connection.php";

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['h_username']) && !isset($_SESSION['user_id'])) {
    die("You need to log in to view your dashboard.");
}

$dept = $_GET['dept'] ?? $_SESSION['dept'] ?? 'CSE';
$desg = $_GET['designation'] ?? 'HOD';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Dashboard - FMS</title>
    <style>
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

    .main-content {
        padding: 2rem 1rem;
    }

    .container {
        max-width: 80rem;
        margin: 0px auto 50px auto;
    }

    .container12 {
        max-width: 80rem;
        margin: 0px auto;
    }

    .header {
        margin-bottom: 1.5rem;
    }

    .header h1 {
        font-size: 1.5rem;
        font-weight: bold;
        color: rgb(17, 24, 39);
    }

    .feedback-grid, .feedback-grid1 {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 40px;
    }

    @media (min-width: 768px) {
        .feedback-grid, .feedback-grid1 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .feedback-card {
        text-decoration: none;
        display: block;
        transition: transform 0.2s;
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        width: 100%;
        text-align: left;
    }

    .feedback-card:hover {
        transform: scale(1.02);
    }

    .card-content {
        background: linear-gradient(to right, rgb(30, 64, 175), rgb(37, 99, 235));
        padding: 1.5rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .card-content h3 {
        color: white;
        font-size: 1.25rem;
        font-weight: 600;
    }
    </style>
</head>
<body>
    <main class="main-content">
        <div class="container12">
            <div class="header">
                <h1>Achievements</h1>
            </div>

            <div class="feedback-grid">
                <a href="hod_down_fdps_files.php?dept=<?php echo urlencode($dept); ?>&action_name=fdps" class="feedback-card">
                    <div class="card-content">
                        <h3>View FDPS Attended Files</h3>
                    </div>
                </a>

                <a href="hod_down_fdps_files.php?dept=<?php echo urlencode($dept); ?>&action_name=fdps_org" class="feedback-card">
                    <div class="card-content">
                        <h3>View FDPS Organized Files</h3>
                    </div>
                </a>

                <a href="hod_down_fdps_files.php?dept=<?php echo urlencode($dept); ?>&action_name=published" class="feedback-card">
                    <div class="card-content">
                        <h3>View Papers Published Files</h3>
                    </div>
                </a>

                <a href="hod_down_fdps_files.php?dept=<?php echo urlencode($dept); ?>&action_name=conference" class="feedback-card">
                    <div class="card-content">
                        <h3>View Conferences Published Files</h3>
                    </div>
                </a>

                <a href="hod_down_fdps_files.php?dept=<?php echo urlencode($dept); ?>&action_name=patents" class="feedback-card">
                    <div class="card-content">
                        <h3>View Patents Files</h3>
                    </div>
                </a>
            </div>
        </div>

        <div class="container">
            <div class="header">
                <h1>Department Files</h1>
            </div>

            <div class="feedback-grid1">
                <a href="hod_down_dept_files.php?event=admin&dept=<?php echo urlencode($dept); ?>" class="feedback-card">
                    <div class="card-content">
                        <h3>Admin Files</h3>
                    </div>
                </a>

                <a href="hod_down_dept_files.php?event=faculty&dept=<?php echo urlencode($dept); ?>" class="feedback-card">
                    <div class="card-content">
                        <h3>Faculty Files</h3>
                    </div>
                </a>

                <a href="hod_down_dept_files.php?event=student&dept=<?php echo urlencode($dept); ?>" class="feedback-card">
                    <div class="card-content">
                        <h3>Student Related Files</h3>
                    </div>
                </a>

                <a href="hod_down_dept_files.php?event=exam&dept=<?php echo urlencode($dept); ?>" class="feedback-card">
                    <div class="card-content">
                        <h3>Exam Section Files</h3>
                    </div>
                </a>

                <a href="hod_down_st_act_files.php?dept=<?php echo urlencode($dept); ?>" class="feedback-card">
                    <div class="card-content">
                        <h3>Student Activities Files</h3>
                    </div>
                </a>
            </div>
        </div>

        <!-- WORKFLOW DASHBOARD -->
        <div class="container111" style="max-width: 80rem; margin: 40px auto 100px auto; background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <?php 
                $user_id = $_SESSION['user_id'] ?? 0;
                $dept_id = 0;
                if (isset($_SESSION['dept'])) {
                    $stmt = $conn->prepare("SELECT dept_id FROM Dept WHERE dept_name = ?");
                    $stmt->bind_param("s", $_SESSION['dept']);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    if ($row = $res->fetch_assoc()) $dept_id = $row['dept_id'];
                    $stmt->close();
                }
                $base_url = '../';
                include "../includes/dashboard_table.php"; 
            ?>
        </div>
    </main>
</body>
</html>
