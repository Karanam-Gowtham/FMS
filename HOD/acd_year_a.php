<?php
require_once '../includes/session.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Dashboard</title>
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

        /* Main content */
        .main-content {
            padding: 2rem 1rem;
            margin-top: 80px;
            /* Space for fixed header */
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

        .card-content h3 {
            color: white;
            font-size: 1.25rem;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <?php include 'header_hod.php'; ?>
    <main class="main-content">
        <div class="container">
            <!-- Achievements Section -->
            <div class="header">
                <h1>Achievements</h1>
            </div>

            <div class="feedback-grid">
                <a href="fdps_down.php" class="feedback-card">
                    <div class="card-content">
                        <h3>View FDPS Attended Files</h3>
                    </div>
                </a>

                <a href="fdps_org_down.php" class="feedback-card">
                    <div class="card-content">
                        <h3>View FDPS Organized Files</h3>
                    </div>
                </a>

                <a href="published_down.php" class="feedback-card">
                    <div class="card-content">
                        <h3>View Papers Published Files</h3>
                    </div>
                </a>

                <a href="conference_down.php" class="feedback-card">
                    <div class="card-content">
                        <h3>View Conferences Published Files</h3>
                    </div>
                </a>

                <a href="patents_down.php" class="feedback-card">
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
                <a href="down_dept_files.php?event=admin" class="feedback-card">
                    <div class="card-content">
                        <h3>Admin Files</h3>
                    </div>
                </a>

                <a href="down_dept_files.php?event=faculty" class="feedback-card">
                    <div class="card-content">
                        <h3>Faculty Files</h3>
                    </div>
                </a>

                <a href="down_dept_files.php?event=student" class="feedback-card">
                    <div class="card-content">
                        <h3>Student Related Files</h3>
                    </div>
                </a>

                <a href="down_dept_files.php?event=exam" class="feedback-card">
                    <div class="card-content">
                        <h3>Exam Section Files</h3>
                    </div>
                </a>

                <a href="down_dept_files.php?event=calendar" class="feedback-card">
                    <div class="card-content">
                        <h3>Academic Calendar</h3>
                    </div>
                </a>

                <a href="hod_down_st_act_files.php" class="feedback-card">
                    <div class="card-content">
                        <h3>Student Activities Files</h3>
                    </div>
                </a>

                <a href="down_dept_files.php?event=Dept Meeting Minutes" class="feedback-card">
                    <div class="card-content">
                        <h3>Department Meeting Minutes</h3>
                    </div>
                </a>

                <a href="down_dept_files.php?event=AMC Meeting Minutes" class="feedback-card">
                    <div class="card-content">
                        <h3>AMC Meeting Minutes</h3>
                    </div>
                </a>

                <a href="down_dept_files.php?event=Board Of Studies" class="feedback-card">
                    <div class="card-content">
                        <h3>Board Of Studies (BOS)</h3>
                    </div>
                </a>
            </div>
        </div>
    </main>
</body>

</html>