<?php
include 'connection.php'; // Include database connection
session_start();
if (!isset($_SESSION['username'])) {
    die("You need to log in to view your uploads.");
}

$username = $_SESSION['username'];
if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}

// Set timezone to Indian Standard Time
date_default_timezone_set('Asia/Kolkata');

if (isset($_GET['activity'])) {
    $activity = urldecode($_GET['activity']); // Decode the activity
} else {
    $activity = 'Unknown'; // Fallback if no activity is provided
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $year =  $_POST['year'];
    $event_name = $_POST['event_name'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $organised_by = $_POST['organised_by'];
    $location = $_POST['location'];
    $participation_status = $_POST['participation_status'];
    $uploaded_by = $_POST['uploaded_by'];
    $Branch = $_POST['Branch'];

    // Handle certificate upload
    $certificate_path = '';
    if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] == 0) {
        $certificate_path = 'uploads/' . basename($_FILES['certificate']['name']);
        move_uploaded_file($_FILES['certificate']['tmp_name'], $certificate_path);
    }
    date_default_timezone_set('Asia/Kolkata');
    $submission_time = date('d-m-Y H:i:s');
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO s_events (Username,acd_year,branch,activity, event_name, from_date, to_date, organised_by, location, participation_status, certificate_path, uploaded_by, submission_time) 
                            VALUES (?, ?, ?,  ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
    $stmt->bind_param("sssssssssssss",$username, $year, $Branch, $activity, $event_name, $from_date, $to_date, $organised_by, $location, $participation_status, $certificate_path, $uploaded_by, $submission_time);

    if ($stmt->execute()) {
        echo "<script>alert('Event submitted successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Submission</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #0a192f 0%, #172a45 100%);
            background-size: cover;
            background-position: center;
            justify-content: center;
            height: 100%;
            margin: 0;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 158%;
            background: rgba(0, 0, 0, 0.5); /* Adjust overlay opacity */
            z-index: -1;
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
<?php 
    include "header.php";
?>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="acd_year.php?dept=<?php echo "$dept" ?>" class="home-icon"> Faculty </a></span>
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="student_act.php?event=student_act&dept=<?php echo $dept ?>" class="home-icon"> student_activities </a></span>
                <span>&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a"> <?php echo"$activity" ?> </a></span>
                <span>&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>
<div class="cont1">

<div class="container">
    <h1>Enter <?php echo"$activity" ?> Details</h1>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="event_name">Name of the Event:</label>
            <input type="text" id="event_name" name="event_name" required>
        </div>
        <div class="form-group">
                        <label for="academic-year">Select Academic Year:</label>
                        <select name="year" id="academic-year" required>
                            <option value="" disabled selected>Select an academic year</option>
                            <?php
                            include("connection.php"); // Must be before this code

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
            <div class="form-group">
            <label for="participation_status">Branch:</label>
            <select id="participation_status" name="Branch" required>
                <option value="" selected disabled>Choose one</option>
                <option value="CSE">CSE</option>
                <option value="AIML">AIML</option>
                <option value="AIDS">AIDS</option>
                <option value="IT">IT</option>
                <option value="ECE">ECE</option>
                <option value="EEE">EEE</option>
                <option value="MECH">MECH</option>
                <option value="CIVIL">CIVIL</option>
                <option value="BSH">BSH</option>
            </select>
        </div>
        <div class="form-group">
            <label for="from_date">From Date:</label>
            <input type="date" id="from_date" name="from_date" required>
        </div>
        <div class="form-group">
            <label for="to_date">To Date:</label>
            <input type="date" id="to_date" name="to_date" required>
        </div>
        <div class="form-group">
            <label for="organised_by">Organised By:</label>
            <input type="text" id="organised_by" name="organised_by" required>
        </div>
        <div class="form-group">
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>
        </div>
        <div class="form-group">
            <label for="participation_status">Participation Status:</label>
            <select id="participation_status" name="participation_status" required>
                <option value="Participated">Participated</option>
                <option value="1st">1st</option>
                <option value="2nd">2nd</option>
                <option value="3rd">3rd</option>
            </select>
        </div>
        <div class="form-group">
            <label for="certificate">Report:</label>
            <input type="file" id="certificate" name="certificate">
        </div>
        <div class="form-group">
            <label for="uploaded_by">Uploaded By:</label>
            <input type="text" id="uploaded_by" name="uploaded_by" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn1">Submit</button>
        </div>
    </form>
</div>
</div>
</body>
</html>

