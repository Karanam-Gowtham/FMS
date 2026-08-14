<?php
    include "../../includes/connection.php";


    
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['username'])) {
    die("You need to log in to view your uploads.");
}

$username = $_SESSION['username'];
if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    // Fallback: get dept from reg_tab
    $dept_query = "SELECT dept FROM reg_tab WHERE userid = '$username'";
    $dept_result = mysqli_query($conn, $dept_query);
    if ($dept_result && mysqli_num_rows($dept_result) > 0) {
        $dept_row = mysqli_fetch_assoc($dept_result);
        $dept = $dept_row['dept'];
    } else {
        echo "Department not set.";
        $dept = "";
    }
}
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

/* Feedback Grid */
.feedback-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2.5rem;
    margin-top: 2rem;
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

.icon {
    color: white;
}

.card-content h3 {
    color: white;
    font-size: 1.25rem;
    font-weight: 600;
}
.my-achievements {
    display: inline-block;
    padding: 10px 20px;
    background-color: #2563eb;
    color: white !important;
    text-decoration: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 600;
    transition: background-color 0.3s ease, transform 0.2s ease;
    margin-top: 10px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.my-achievements:hover {
    background-color: #1d4ed8;
    transform: translateY(-2px);
}
        </style>

</head>
<body>
    <?php include "../../includes/header.php"; ?>
    

    <main class="main-content">
        <div class="container">
            <div class="header">
                <h1>Achievements</h1>
                <a href="../common/download_papers1.php?dept=<?php echo"$dept" ?>" class="btn my-achievements">My Achievements</a>
            </div>

            <div class="feedback-grid">
                

            <a href="fdps.php?dept=<?php echo"$dept" ?>&type=faculty" class="feedback-card">
                    <div class="card-content">
                        <h3>FDPS Attended</h3>
                    </div>
                </a>

                <a href="fdps_org.php?dept=<?php echo"$dept" ?>&type=faculty" class="feedback-card">
                    <div class="card-content">
                        <h3>FDPS Organized</h3>
                    </div>
                </a>

                <a href="conf_org.php?dept=<?php echo"$dept" ?>&type=faculty" class="feedback-card">
                    <div class="card-content">
                        <h3>Conference Organised</h3>
                    </div>
                </a>

                <a href="published.php?dept=<?php echo"$dept" ?>&type=faculty" class="feedback-card">
                    <div class="card-content">
                        <h3>Research Papers Published</h3>
                    </div>
                </a>

                <a href="conference.php?dept=<?php echo"$dept" ?>&type=faculty" class="feedback-card">
                    <div class="card-content">
                        <h3>Conference Proceedings Published</h3>
                    </div>
                </a>
                <a href="patents.php?dept=<?php echo"$dept" ?>&type=faculty" class="feedback-card">
                    <div class="card-content">
                        <h3>Patents</h3>
                    </div>
                </a>
            </div>
        </div>
        <div class="container">
            <div class="header">
                <h1>Department Files</h1>
                <a href="../dept_coordinator/dept_down_files.php?dept=<?php echo"$dept" ?>" class="btn my-achievements">My Dept Files</a>
            </div>

            <div class="feedback-grid">
                

                <a href="../dept_coordinator/dept_files.php?event=admin&dept=<?php echo"$dept" ?>" class="feedback-card">
                    <div class="card-content">
                        <h3>Admin Files</h3>
                    </div>
                </a>

                <a href="../dept_coordinator/dept_files.php?event=faculty&dept=<?php echo"$dept" ?>" class="feedback-card">
                    <div class="card-content">
                        <h3>Faculty Files</h3>
                    </div>
                </a>

                <a href="../dept_coordinator/dept_files.php?event=student&dept=<?php echo"$dept" ?>" class="feedback-card">
                    <div class="card-content">
                        <h3>Student Related Files</h3>
                    </div>
                </a>

                <a href="../dept_coordinator/dept_files.php?event=exam&dept=<?php echo"$dept" ?>" class="feedback-card">
                    <div class="card-content">
                        <h3>Exam Section Files</h3>
                    </div>
                </a>

                <a href="student_act.php?event=student_act&dept=<?php echo"$dept" ?>" class="feedback-card">
                    <div class="card-content">
                        <h3>student Activities Files</h3>
                    </div>
                </a>
            </div>
        </div>

        <!-- WORKFLOW DASHBOARD -->
        <div class="container" style="margin-top: 60px;">
            <?php 
                $user_id = $_SESSION['user_id'] ?? 0;
                $dept_id = 0; // Not strictly needed for faculty docs
                include "../../includes/dashboard_table.php"; 
            ?>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>