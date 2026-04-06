<?php
require_once '../includes/session.php';
include_once "../includes/connection.php";

if (!isset($_SESSION['admin'])) {
    die("Unauthorized access. Please log in as Admin.");
}

$dept = '';
if (isset($_GET['dept'])) {
    $dept = $_GET['dept'];
} elseif (isset($_SESSION['dept'])) {
    $dept = $_SESSION['dept'];
}
$designation = "admin";
$event = "aa"; // Default event for admin

include_once 'header_hod.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FMS</title>
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
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 100px;
            border-bottom: 1px solid #eee;

            background-color: white;
            font-size: larger;
        }

        .nav-container {
             /* margin-top moved to .navbar */
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

        .sp {
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
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: rgb(17, 24, 39);
            letter-spacing: -0.025em;
        }

        .header p {
            color: rgb(107, 114, 128);
            margin-top: 0.5rem;
            font-size: 1.1rem;
        }

        /* Grid Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        @media (min-width: 768px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .card {
            text-decoration: none;
            display: block;
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgb(229, 231, 235);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: rgb(59, 130, 246);
        }

        .card-content {
            padding: 2rem;
            text-align: center;
        }

        .card-icon {
            width: 4rem;
            height: 4rem;
            background: rgb(239, 246, 255);
            color: rgb(37, 99, 235);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .card h3 {
            color: rgb(31, 41, 55);
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .card p {
            color: rgb(107, 114, 128);
            font-size: 0.95rem;
        }

        /* Specific card colors */
        .card-year .card-icon { background: rgb(236, 253, 245); color: rgb(5, 150, 105); }
        .card-criteria .card-icon { background: rgb(255, 251, 235); color: rgb(217, 119, 6); }
        .card-files .card-icon { background: rgb(245, 243, 255); color: rgb(124, 58, 237); }

    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sp">&nbsp; >> &nbsp;</span><span class="main"><span class="main-a">Admin Dashboard</span></span>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="header">
                <h1>Admin Command Center</h1>
                <p>Manage system-wide academic years, criteria, and document structures.</p>
            </div>

            <div class="dashboard-grid">
                <!-- Academic Year Card -->
                <a href="Add_academic_year.php?designation=admin&event=aa" class="card card-year">
                    <div class="card-content">
                        <div class="card-icon">
                            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3>Academic Years</h3>
                        <p>Add, edit, or remove academic years from the system.</p>
                    </div>
                </a>

                <!-- Criteria Management Card -->
                <a href="acd_year_criteria_select.php" class="card card-criteria">
                    <div class="card-content">
                        <div class="card-icon">
                            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3>Criteria Management</h3>
                        <p>Define and structure AQAR criteria and sub-criteria.</p>
                    </div>
                </a>

                <!-- Supporting Documents Card -->
                <a href="acd_year_a.php?designation=admin&event=aa" class="card card-files">
                    <div class="card-content">
                        <div class="card-icon">
                            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3>Supporting Docs</h3>
                        <p>Review and manage uploaded supporting documents for AQAR.</p>
                    </div>
                </a>
            </div>
        </div>
    </main>
</body>
</html>

