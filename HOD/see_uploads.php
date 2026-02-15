<?php
require_once '../includes/session.php';
if (!isset($_SESSION['h_username']) && !isset($_SESSION['admin'])) {
    die("You need to log in to view this page.");
}
$dept = isset($_GET['dept']) ? $_GET['dept'] : (isset($_SESSION['dept']) ? $_SESSION['dept'] : '');
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
    </style>
</head>
<body>
<?php include 'header_hod.php'; ?>

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
                <a href="hod_manage_meeting_minutes.php?event=Dept Meeting Minutes" class="feedback-card">
                    <div class="card-content">
                        <h3>Department Meeting Minutes</h3>
                    </div>
                </a>

                <a href="hod_manage_meeting_minutes.php?event=AMC Meeting Minutes" class="feedback-card">
                    <div class="card-content">
                        <h3>AMC Meeting Minutes</h3>
                    </div>
                </a>

                <a href="hod_manage_meeting_minutes.php?event=Board Of Studies" class="feedback-card">
                    <div class="card-content">
                        <h3>Board Of Studies</h3>
                    </div>
                </a>
            </div>
        </div>
    </main>
</body>
</html>
