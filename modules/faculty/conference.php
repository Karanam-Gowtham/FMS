<?php

include '../../includes/connection.php';
session_start();
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_SESSION['username'])) {
    die("You need to log in to view your uploads.");
}

$username = $_SESSION['username'];
if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}

if (isset($_GET['type'])) {
    $type = $_GET['type']; // Get the 'dept' value from the URL
} else {
    echo "desg not set.";
}

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user = $_SESSION['username'];

    $branch_query = "SELECT dept FROM reg_tab WHERE userid = '$user'";
        $branch_result = $conn->query($branch_query);

        if ($branch_result && $branch_result->num_rows > 0) {
            $branch_row = $branch_result->fetch_assoc();
            $branch = $branch_row['dept'];
        } else {
            die("Branch not found for the user.");
        }

    $paper_title = $_POST['paper_title'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $organised_by = $_POST['organised_by'];
    $location = $_POST['location'];
    $paper_type = $_POST['paper_type'];
    $year = $_POST['year'];

    // Handle file uploads
    $certificate_path = '';
    if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] == 0) {
        $certificate_path = '../../uploads/' . basename($_FILES['certificate']['name']);
        move_uploaded_file($_FILES['certificate']['tmp_name'], $certificate_path);
    }

    $paper_file_path = '';
    if (isset($_FILES['paper_file']) && $_FILES['paper_file']['error'] == 0) {
        $paper_file_path = '../../uploads/' . basename($_FILES['paper_file']['name']);
        move_uploaded_file($_FILES['paper_file']['tmp_name'], $paper_file_path);
    }

    // Insert data into the database
    date_default_timezone_set('Asia/Kolkata');

    $submission_time = date('Y-m-d H:i:s');

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO conference_tab (username, branch, paper_title, from_date, to_date, organised_by, location, certificate_path, paper_type, paper_file_path, submission_time,year) VALUES (?, ?, ?, ?,?,?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", $user,$dept, $paper_title, $from_date, $to_date, $organised_by, $location, $certificate_path, $paper_type, $paper_file_path, $submission_time,$year);

    if ($stmt->execute()) {
    echo "<script>alert('Submission successful!');</script>";

} else {
    echo "<script>showPopup('Error: " . addslashes($stmt->error) . "');</script>";
}


    $stmt->close();
}

$conn->close();
?>

<?php 
    include '../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Paper Submission</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0a192f 0%, #172a45 100%);
            background-size: cover;
            background-position: center;
            justify-content: center;
            height: 100%;
            margin: 0;
        }

        .container {
            margin-top: 30px;
            margin-bottom: 50px;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.2);
            width: 600px;
            max-width: 100%;
            color: white;
        }

        .cont1{
            display: flex;
            justify-content: center;
            align-items: center;
        }
          /* Navigation */
    .navbar { 
        font-size: larger;
    }

    .nav-container {
        background-color: white;
        width:150vw;
        margin-top: 80px;
        padding: 0 1rem;
    }

    .nav-items {
        margin-left: 70px;
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

        h1 {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #84fab0;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 16px;
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 0.2px solid rgb(165, 225, 239);
            background-color: #1c1c1c;
            color: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #84fab0;
        }

        .btn1 {
            padding: 15px;
            font-size: 18px;
            background-color: #84fab0;
            color: black;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn1:hover {
            background-color: #4ca1af;
        }

        .btn1:active {
            transform: scale(0.98);
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 12px 20px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #c0392b;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                width: 90%;
            }
            h1 {
                font-size: 2rem;
            }
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
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="acd_year.php?dept=<?php echo"$dept" ?>" class="home-icon"> Faculty </a></span>
                <span>&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a"> conference_papers </a></span>
                <span>&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>
<div class="cont1">
    <div class="container">
        <h1>Conference Papers Form</h1>

        <form id="conference-form" method="POST" enctype="multipart/form-data" action="">


            <!-- Paper Title -->
            <div class="form-group">
                <label for="paper-title">Paper Title:</label>
                <input type="text" id="paper-title" name="paper_title" required>
            </div>

            <div class="form-group">
                <label for="academic-year">Select Academic Year:</label>
                <select name="year" id="academic-year" required>
                    <option value="" disabled selected>Select an academic year</option>
                    <?php
                    include("../../includes/connection.php"); // Must be before this code

                    $query = "SELECT year FROM academic_year ORDER BY year DESC";
                    $result = mysqli_query($conn, $query);

                    if (!$result) {
                        die("Query Failed: " . mysqli_error($conn)); // Debug error
                    }

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $year = htmlspecialchars($row['year']);
                            echo "<option value=\"$year\">$year</option>";
                        }
                    } else {
                        echo '<option value="" disabled>No years found</option>';
                    }
                    ?>
                </select>
            </div>

            <!-- From and To Dates -->
            <div class="form-group">
                <label for="from-date">From Date:</label>
                <input type="date" id="from-date" name="from_date" required>
            </div>

            <div class="form-group">
                <label for="to-date">To Date:</label>
                <input type="date" id="to-date" name="to_date" required>
            </div>

            <!-- Organised By and Location -->
            <div class="form-group">
                <label for="organised-by">Organized By:</label>
                <input type="text" id="organised-by" name="organised_by" required>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>
            </div>

            <!-- Certificate -->
            <div class="form-group">
                <label for="certificate">Certificate:</label>
                <input type="file" id="certificate" name="certificate" required>
            </div>

            <!-- Paper Type -->
            <div class="form-group">
                <label for="paper-type">Paper Type:</label>
                <select id="paper-type" name="paper_type" required>
                    <option value="participated">Participated</option>
                    <option value="paper_publication">Paper Publication</option>
                    <option value="poster_presentation">Poster Presentation</option>
                </select>
            </div>

            <!-- Paper File (conditional visibility) -->
            <div id="paper-upload-div" class="form-group">
                <label for="paper-file">Paper :</label>
                <input type="file" id="paper-file" name="paper_file">
            </div>

            <button class="btn1" type="submit">Submit</button>

        </form>
    </div></div>

    <script src="../../assets/../../assets/css/conf.js"></script>
</body>
</html>
