<?php
    include "../../includes/connection.php";


    
session_start();

if (!isset($_SESSION['a_username'])) {
    die("You need to log in to view your uploads.");
}

$username = $_SESSION['a_username'];


if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}

if (isset($_GET['desg'])) {
    $desg = $_GET['desg']; // Get the 'dept' value from the URL
} else {
    $desg = " ";
}

include '../../includes/header.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GMRIT Feedback Center</title>
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
    margin-left:100px;
    max-width: 80rem;
    padding: 0 1rem;
}

.nav-items {
    display: flex;
    align-items: center;
    height: 4rem;
}

.sid{
    color: rgb(48, 30, 138);
    font-weight: 500;
}

.main-a {
    color: rgb(138, 30, 113);
    font-weight: 500;
}
.main-a:hover{
    color:rgb(182, 64, 211);
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
    margin: 0px auto 100px auto;
}
.container12 {
    max-width: 80rem;
    margin:0px 100px;
}

.header {
    margin-bottom: 1.5rem;
}

.header h1 {
    font-size: 1.5rem;
    font-weight: bold;
    color: rgb(17, 24, 39);
}

.header h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: rgb(234, 179, 8);
    margin-top: 0.5rem;
}

.description {
    margin-bottom: 2rem;
}

.description p {
    margin-bottom: 1rem;
    color: rgb(75, 85, 99);
}

.up_ach {
    display: flex;
    justify-content: space-between;
    align-items: center; /* or baseline if you prefer that alignment */
}

/* Feedback Grid */
.feedback-grid {
    margin-top: 0px;
    margin-bottom: 50px;
    margin:0px 100px;
    display: grid;
    grid-template-columns: 1fr;
    gap: 2.5rem;
    margin-bottom: 50px;
}
.feedback-grid1 {
    margin-top: 0px;
    margin-bottom: 50px;
    margin:0px;
    display: grid;
    grid-template-columns: 1fr;
    gap: 2.5rem;
    margin-bottom: 50px;
}

@media (min-width: 768px) {
    .feedback-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .feedback-grid1 {
        grid-template-columns: repeat(2, 1fr);
    }
}

.feedback-card {
    text-decoration: none;
    display: block;
    transition: transform 0.2s;
    background: #fff; /* optional background */
    border: none;
    padding: 0;
    
    cursor: pointer;
    width: 100%; /* optional: makes it behave like block element */
    text-align: left; /* if you want content aligned like an anchor */
}


.feedback-card:hover {
    transform: scale(1.05);
}

.card-content {
    background: linear-gradient(to right, rgb(30, 64, 175), rgb(37, 99, 235));
    padding: 1.5rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.card-content_1 {
    background: linear-gradient(to right, rgb(37, 175, 30), rgb(9, 80, 2));
    padding: 1.5rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.card-content_1 h3 {
    color: white;
    font-size: 1.25rem;
    font-weight: 600;
}
.icon {
    color: white;
}

.card-content h3 {
    color: white;
    font-size: 1.25rem;
    font-weight: 600;
}
.my-achievements{
    font-size: larger;
    margin-left: -17px;
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
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span>&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a"> dept_coordinator </a></span>
                <span>&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container12">
        <div class="header">
                <h1>Achievements</h1>
                <div class="up_ach">
                    <a href="dc_down_up_files.php?event=Achievements&dept=<?php echo"$dept" ?>" class="btn my-achievements">My Achievements</a>
                    <a href="dc_up_files.php?event=Achievements&dept=<?php echo"$dept" ?>" class="feedback-card_1">
                        <div class="card-content_1">
                            <h3>Upload Achievement Files </h3>
                        </div>
                    </a>
                </div>
            </div>
            </div>

            
    <div class="feedback-grid">
            <form method="POST" action="dc_down_fdps_files.php?&dept=<?php echo"$dept" ?>">
                    <input type="hidden" name="action_name" value="fdps">
                    <button type="submit" class="feedback-card">
                        <div class="card-content">
                            <h3>View FDPS Attended Files</h3>
                        </div>
                    </button>
                </form>

                <form method="POST" action="dc_down_fdps_files.php?&dept=<?php echo"$dept" ?>">
                    <input type="hidden" name="action_name" value="fdps_org">
                    <button type="submit" class="feedback-card">
                        <div class="card-content">
                            <h3>View FDPS Organized Files</h3>
                        </div>
                    </button>
                </form>

                <form method="POST" action="dc_down_fdps_files.php?&dept=<?php echo"$dept" ?>">
                    <input type="hidden" name="action_name" value="published">
                    <button type="submit" class="feedback-card">
                        <div class="card-content">
                            <h3>View Papers Published Files</h3>
                        </div>
                    </button>
                </form>

                <form method="POST" action="dc_down_fdps_files.php?&dept=<?php echo"$dept" ?>">
                    <input type="hidden" name="action_name" value="conference">
                    <button type="submit" class="feedback-card">
                        <div class="card-content">
                            <h3>View Conferences Published Files</h3>
                        </div>
                    </button>
                </form>

                <form method="POST" action="dc_down_fdps_files.php?&dept=<?php echo"$dept" ?>">
                    <input type="hidden" name="action_name" value="patents">
                    <button type="submit" class="feedback-card">
                        <div class="card-content">
                            <h3>View Patents Files</h3>
                        </div>
                    </button>
                </form>
            </div>

        <div class="container">
            <div class="header">
                <h1>Department Files</h1>
                <div class="up_ach">
                    <a href="dc_down_up_files.php?event=dept&dept=<?php echo"$dept" ?>" class="btn my-achievements">My Dept Files</a>
                    <a href="dc_up_files.php?event=dept&dept=<?php echo"$dept" ?>" class="feedback-card1">
                        <div class="card-content_1">
                            <h3>Upload Department Files </h3>
                        </div>
                    </a>
                </div>
            </div>

            <div class="feedback-grid1">

                    <form method="POST" action="dc_down_dept_files.php?&dept=<?php echo"$dept" ?>">
                        <input type="hidden" name="file_type1" value="admin">
                        <button type="submit" class="feedback-card">
                            <div class="card-content">
                                <h3>Admin Files</h3>
                            </div>
                        </button>
                    </form>

                    <form method="POST" action="dc_down_dept_files.php?&dept=<?php echo"$dept" ?>">
                        <input type="hidden" name="file_type1" value="faculty">
                        <button type="submit" class="feedback-card">
                            <div class="card-content">
                                <h3>Faculty Files</h3>
                            </div>
                        </button>
                    </form>

                    <form method="POST" action="dc_down_dept_files.php?&dept=<?php echo"$dept" ?>">
                        <input type="hidden" name="file_type1" value="student">
                        <button type="submit" class="feedback-card">
                            <div class="card-content">
                                <h3>Student Related Files</h3>
                            </div>
                        </button>
                    </form>

                    <form method="POST" action="dc_down_dept_files.php?&dept=<?php echo"$dept" ?>">
                        <input type="hidden" name="file_type1" value="exam_section">
                        <button type="submit" class="feedback-card">
                            <div class="card-content">
                                <h3>Exam Section Files</h3>
                            </div>
                        </button>
                    </form>

                    <form method="POST" action="dc_down_st_act_files.php?&dept=<?php echo"$dept" ?>">
                        <input type="hidden" name="file_type1" value="Student Activities Files">
                        <button type="submit" class="feedback-card">
                            <div class="card-content">
                                <h3>Student Activities Files</h3>
                            </div>
                        </button>
                    </form>

                </div>

        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>
