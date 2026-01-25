<?php
include("connection.php");
session_start();
if (!isset($_SESSION['username'])) {
    die("You need to log in to view your uploads.");
}

$username = $_SESSION['username'];
if (isset($_GET['event'])) {
    $event = $_GET['event'];
} else {
    $event = ''; // Default value if no event is provided
}
if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}

$pages = [
    "J_Papers" => "s_journal.php",
    "C_Papers" => "s_conference.php",
    "Projects" => "s_act_files.php",
    "Internships" => "s_act_files.php",
    "SIH" => "s_act_files.php",
    "GATE" => "s_act_files.php",
    "Hackathons" => "s_act_files.php",
    "Professional_Bodies" => "s_body_files.php"
];

if (isset($_POST['activity']) && isset($pages[$_POST['activity']])) {
    header("Location: " . $pages[$_POST['activity']] . "?activity=" . $_POST['activity'] . "&dept=" . urlencode($dept) . "&event=" . urlencode($event));

    exit(); // Stop execution to prevent errors
}

include 'header.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Events</title>
    <style>
       body {
            background: linear-gradient(135deg, #0a192f 0%, #172a45 100%);
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #fff;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        /* Navbar */
        .nav-container {
            margin-top: 80px;
            margin-left: -800px;
            background-color: white;
            width: 100%;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            top: 0;
        }

        .nav-items {
            margin-left: -800px;
            display: flex;
            align-items: center;
            height: 4rem;
            font-size: larger;
        }

        #sp{
            color:blue;
        }
        .sid {
            color: rgb(48, 30, 138);
            font-weight: 500;
        }

        .main-a {
            color: rgb(138, 30, 113);
            font-weight: 500;
            text-decoration: none;
        }

        .main-a:hover {
            color: rgb(182, 64, 211);
        }

        .home-icon {
            color: rgb(30, 58, 138);
            transition: color 0.2s;
            text-decoration: none;
        }

        .home-icon:hover {
            color: rgb(29, 78, 216);
        }

        /* Main Content */
        .container {
            margin-top: 25vh;
            text-align: center;
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.2);
            min-height: 190px;
        }

        .cont1 {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        /* Heading */
        h1 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 2.5em;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Select and Button Styling */
        select {
            width: 200px;
            margin-bottom: 20px;
            padding: 10px;
            font-size: 1em;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        button {
            left:300;
            background: linear-gradient(to right, rgb(139, 10, 130), rgb(229, 129, 225));
            color: white;
            font-weight: bold;
            padding: 10px 15px;
            font-size: 1em;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            transform: translateY(-3px);
        }

        button:active {
            transform: translateY(0);
        }

        /* Upload Button */
        .button1 {
            position: fixed;
            top: 200px;
            right: 300px;
            background: linear-gradient(to right, rgb(2, 26, 48), rgb(129, 187, 229));
            padding: 10px 15px;
            border-radius: 5px;
            color: white;
            text-align: center;
            width: 120px;
            text-decoration: none;
            font-size: 1em;
        }

        .button1:hover {
            background: linear-gradient(to right, rgb(2, 40, 70), rgb(100, 160, 200));
        }

    </style>
</head>
<body>
<nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span id="sp">&nbsp; >> &nbsp;  </span><span class="sid"><a href="admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span id="sp">&nbsp; >> &nbsp;  </span><span class="sid"><a href="acd_year.php?dept=<?php echo"$dept" ?>" class="home-icon"> Faculty </a></span>
                <span id="sp">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a"> <?php echo"$event" ?>_Files </a></span>
                <span id="sp">&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>
<div class="cont1">
    <a href="s_down_files1.php?dept=<?php echo   $dept ?>" class="button1">Uploads</a>
    <div class="container">
        <h1>Student Activities</h1>
        <form id="activityForm" action="" method="POST">
            <select name="activity" id="activity" onchange="showProfessionalBodies()">
                <option value="" disabled selected>Select an Activity</option>
                <option value="J_Papers">Journal Papers</option>
                <option value="C_Papers">Conference Papers</option>
                <option value="Projects">Projects</option>
                <option value="Internships">Internships</option>
                <option value="SIH">SIH</option>
                <option value="GATE">GATE</option>
                <option value="Hackathons">Hackathons</option>
                <option value="Professional_Bodies">Professional Bodies</option>
            </select><br>

            <!-- Second Dropdown for Professional Bodies (Initially Hidden) -->
            <div id="professionalBodiesDiv" style="display: none;">
                <select name="professional_body" id="professional_body">
                    <option value="" selected disabled>Select ProfessionalBody</option>
                    <option value="s_body_files.php?activity=ISTE">ISTE</option>
                    <option value="s_body_files.php?activity=CSI">CSI</option>
                    <option value="s_body_files.php?activity=ACM">ACM</option>
                    <option value="s_body_files.php?activity=ACMW">ACMW</option>
                    <option value="s_body_files.php?activity=Coding_Club">Coding Club</option>
                    <option value="s_body_files.php?activity=IEEE">IEEE</option>
                    <option value="s_body_files.php?activity=IEEE-WIE">IEEE-WIE</option>
                </select><br>
            </div>

            <button type="submit">Submit</button>
        </form>
    </div>
</div>
<script>
    function showProfessionalBodies() {
        var activity = document.getElementById("activity").value;
        var professionalBodiesDiv = document.getElementById("professionalBodiesDiv");

        if (activity === "Professional_Bodies") {
            professionalBodiesDiv.style.display = "block";
        } else {
            professionalBodiesDiv.style.display = "none";
        }
    }

    function validateForm(event) {
        var activity = document.getElementById("activity").value;

        if (activity === "Professional_Bodies") {
            var professionalBody = document.getElementById("professional_body").value;
            if (!professionalBody) {
                alert("Please select a Professional Body.");
                event.preventDefault();
                return false;
            }
            // Redirect to selected professional body
            window.location.href = professionalBody + "&dept=" + encodeURIComponent("<?php echo $dept; ?>")+ "&event=" + encodeURIComponent("<?php echo $event; ?>");
            event.preventDefault(); // Prevent default form submission
            return false;
        }
    }

    document.getElementById("activityForm").addEventListener("submit", validateForm);
</script>


</body>
</html>
