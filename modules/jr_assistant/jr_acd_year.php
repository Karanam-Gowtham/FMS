<?php
include "../../includes/connection.php";
session_start();

if (!isset($_SESSION['j_username'])) {
    die("You need to log in as Jr Assistant to view this page.");
}

$username = $_SESSION['j_username'];
$dept = $_GET['dept'] ?? (isset($_SESSION['dept']) ? $_SESSION['dept'] : '');

if (empty($dept)) {
    die("Department not set.");
}

include '../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jr Assistant Dashboard</title>
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

        #sp {
            color: blue;
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
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.875rem;
            font-weight: bold;
            color: rgb(17, 24, 39);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background-color: rgb(37, 99, 235);
            color: white;
        }

        .btn-primary:hover {
            background-color: rgb(29, 78, 216);
        }

        /* Feedback Grid */
        .feedback-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        @media (min-width: 768px) {
            .feedback-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .feedback-card {
            text-decoration: none;
            display: block;
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgb(229, 231, 235);
        }

        .feedback-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: rgb(59, 130, 246);
        }

        .card-content {
            padding: 0.75rem 1.25rem;
            text-align: center;
            background: linear-gradient(to right, rgb(30, 64, 175), rgb(37, 99, 235));
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 60px;
        }

        .card-content h3 {
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
        }

    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span>&nbsp; >> &nbsp;</span><span class="sid"><a href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span id="sp">&nbsp; >> &nbsp;</span><span class="main"> <a href="#" class="main-a"> Jr Assistant </a></span>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="header">
                <h1>Meeting Minutes Management</h1>
                <a href="../dept_coordinator/dept_down_files.php?dept=<?php echo"$dept" ?>" class="btn btn-primary">My Uploads</a>
            </div>

            <div class="feedback-grid">
                <a href="../dept_coordinator/dept_meeting_minutes.php?dept=<?php echo urlencode($dept); ?>" class="feedback-card">
                    <div class="card-content">
                        <h3>Department Meeting Minutes</h3>
                    </div>
                </a>
            </div>

            <!-- New Department Files Section -->
            <div class="header" style="margin-top: 3rem;">
                <h1>Department Files</h1>
            </div>

            <div class="feedback-grid">
                <a href="../dept_coordinator/dept_files.php?event=student&dept=<?php echo urlencode($dept); ?>" class="feedback-card">
                    <div class="card-content">
                        <h3>Student Related Files</h3>
                    </div>
                </a>
            </div>

            <!-- New Academic Calendar Section -->
            <div class="header" style="margin-top: 3rem;">
                <h1>Academic Calendar</h1>
            </div>

            <div class="feedback-grid">
                <a href="../dept_coordinator/dept_files.php?event=calendar&dept=<?php echo urlencode($dept); ?>" class="feedback-card">
                    <div class="card-content">
                        <h3>Academic Calendar</h3>
                    </div>
                </a>
            </div>
        </div>
    </main>
</body>
</html>
