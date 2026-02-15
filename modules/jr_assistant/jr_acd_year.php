<?php
include "../../includes/connection.php";
session_start();

if (!isset($_SESSION['j_username'])) {
    die("You need to log in as Jr Assistant to view this page.");
}

$username = $_SESSION['j_username'];
$dept = $_GET['dept'] ?? '';

if (empty($dept)) {
    echo "Department not set.";
}

// Define specific styles for this dashboard to be included in the header
$extra_head = '
<style>
/* Reset and base styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    background-color: rgb(249, 250, 251);
    color: rgb(55, 65, 81);
    line-height: 1.5;
}

/* Navigation / Breadcrumbs */
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

/* Feedback Grid */
.feedback-grid1 {
    margin-top: 0px;
    margin-bottom: 50px;
    margin:0px;
    display: grid;
    grid-template-columns: 1fr;
    gap: 2.5rem;
}

@media (min-width: 768px) {
    .feedback-grid1 {
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

.card-content h3 {
    color: white;
    font-size: 1.25rem;
    font-weight: 600;
}
</style>';

include '../../includes/header.php';
?>

<nav class="navbar">
    <div class="nav-container">
        <div class="nav-items">
            <a href="../../index.php" class="home-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </a>
            <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
            <span>&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a"> jr_assistant </a></span>
        </div>
    </div>
</nav>

<main class="main-content">
    <div class="container12">
        <div class="header">
            <h1>Meeting Minutes</h1>
        </div>
        <div class="feedback-grid1">
            <a href="../dept_coordinator/dept_meeting_minutes.php?dept=<?php echo urlencode($dept); ?>" class="feedback-card">
                <div class="card-content">
                    <h3>Dept Meeting Minutes</h3>
                </div>
            </a>

            <a href="../dept_coordinator/amc_meeting_minutes.php?dept=<?php echo urlencode($dept); ?>" class="feedback-card">
                <div class="card-content">
                    <h3>AMC Meeting Minutes</h3>
                </div>
            </a>

            <a href="../dept_coordinator/bos_meeting_minutes.php?dept=<?php echo urlencode($dept); ?>" class="feedback-card">
                <div class="card-content">
                    <h3>Board Of Studies</h3>
                </div>
            </a>
        </div>
    </div>
</main>

<script src="script.js"></script>
</body>
</html>
