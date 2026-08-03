<?php
include("../../includes/connection.php");
if (session_status() === PHP_SESSION_NONE) { session_start(); }

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




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_SESSION['username'];

    // Fetch user's branch
    $branch_query = "SELECT dept FROM reg_tab WHERE userid = '$user'";
    $branch_result = $conn->query($branch_query);

    if ($branch_result && $branch_result->num_rows > 0) {
        $branch_row = $branch_result->fetch_assoc();
        $branch = $branch_row['dept'];
    } else {
        die("Branch not found for the user.");
    }

    // Form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $mode = mysqli_real_escape_string($conn, $_POST['mode']);
    $date_from = mysqli_real_escape_string($conn, $_POST['date_from']);
    $date_to = mysqli_real_escape_string($conn, $_POST['date_to']);
    $organised_by = mysqli_real_escape_string($conn, $_POST['organised_by']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $funded_by = mysqli_real_escape_string($conn, $_POST['funded_by']);
    $external_funder_name = isset($_POST['external_funder_name']) ? mysqli_real_escape_string($conn, $_POST['external_funder_name']) : '';
    $year = mysqli_real_escape_string($conn, $_POST['year']);

    // File uploads
    $uploads_dir = "uploads/certificates/";

    function uploadFile($file_key, $uploads_dir) {
        if (!empty($_FILES[$file_key]['name'])) {
            $file_name = basename($_FILES[$file_key]['name']);
            $target_file = $uploads_dir . $file_name;

            // Handle special characters
            $file_name = mysqli_real_escape_string($GLOBALS['conn'], $file_name);

            if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $target_file)) {
                return $target_file;
            } else {
                die("Error uploading file: " . $file_key);
            }
        }
        return null;
    }

    $brochure = uploadFile('Brochure', $uploads_dir);
    $fdp_schedule_invitation = uploadFile('fdp_s_i', $uploads_dir);
    $attendance_forms = uploadFile('attendence', $uploads_dir);
    $feedback_forms = uploadFile('feedback', $uploads_dir);
    $fdp_report = uploadFile('report', $uploads_dir);
    $photo1 = uploadFile('Photo1', $uploads_dir);
    $photo2 = uploadFile('Photo2', $uploads_dir);
    $photo3 = uploadFile('Photo3', $uploads_dir);

    // Submission time
    date_default_timezone_set('Asia/Kolkata');
    $submission_time = date('Y-m-d H:i:s');

    // SQL Insert Query
    $sql = "INSERT INTO fdps_org_tab (username, branch, title, mode, date_from, date_to, organised_by, location, funded_by, external_funder_name, brochure, fdp_schedule_invitation, attendance_forms, feedback_forms, fdp_report, photo1, photo2, photo3, submission_time,year)
            VALUES ('$user', '$dept', '$title', '$mode', '$date_from', '$date_to', '$organised_by', '$location', '$funded_by', '$external_funder_name', '$brochure', '$fdp_schedule_invitation', '$attendance_forms', '$feedback_forms', '$fdp_report', '$photo1', '$photo2', '$photo3', '$submission_time','$year')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Records uploaded successfully');</script>";
        echo "<script>window.location.href = 'acd_year.php?dept=" . urlencode($dept) . "';</script>";


    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details Form</title>
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
    <?php include "../../includes/header.php"; ?>

<div class="cont1">
<div class="container">
    <h1>FDPS Organised Form</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="mode">Mode:</label>
            <select name="mode" id="mode" required>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
            </select>
        </div>

        <div class="form-group">
            <label for="funded_by">Funded by:</label>
            <select name="funded_by" id="funded_by" required onchange="toggleExternalFunder()">
                <option value="" disabled selected>Select funding source</option>
                <option value="Institute">Institute</option>
                <option value="External">External</option>
            </select>
        </div>

        <div class="form-group" id="external-funder-div" style="display: none;">
            <label for="external_funder_name">Name of External Funder:</label>
            <input type="text" id="external_funder_name" name="external_funder_name">
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


        <div class="form-group">
            <label for="date-from">Date (From):</label>
            <input type="date" id="date-from" name="date_from" required>
        </div>

        <div class="form-group">
            <label for="date-to">Date (To):</label>
            <input type="date" id="date-to" name="date_to" required>
        </div>

        <div class="form-group">
            <label for="organised-by">Organized By:</label>
            <input type="text" id="organised-by" name="organised_by" required>
        </div>

        <div class="form-group">
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>
        </div>

        <!-- Certificate upload removed -->

        <div class="form-group">
            <label for="certificate">FDP Brochure:</label>
            <input type="file" id="Brochure" name="Brochure" required>
        </div>

        <div class="form-group">
            <label for="certificate">FDP Schedule and Invitation:</label>
            <input type="file" id="fdp_s_i" name="fdp_s_i" required>
        </div>

        <div class="form-group">
            <label for="certificate">Attendance Forms:</label>
            <input type="file" id="attendence" name="attendence" required>
        </div>

        <div class="form-group">
            <label for="certificate">Feedback Forms:</label>
            <input type="file" id="feedback" name="feedback" required>
        </div>

        <div class="form-group">
            <label for="certificate">FDP Report:</label>
            <input type="file" id="report" name="report" required>
        </div>

        <div class="form-group">
            <label for="certificate">Photo1:</label>
            <input type="file" id="Photo1" name="Photo1" required>
        </div>

        <div class="form-group">
            <label for="certificate">Photo2:</label>
            <input type="file" id="Photo2" name="Photo2" required>
        </div>

        <div class="form-group">
            <label for="certificate">Photo3:</label>
            <input type="file" id="Photo3" name="Photo3" required>
        </div>

        <button class="btn1" type="submit">Submit</button>
    </form>
</div>
</div>

<script>
    function toggleExternalFunder() {
        var fundedBy = document.getElementById("funded_by").value;
        var externalDiv = document.getElementById("external-funder-div");
        var externalInput = document.getElementById("external_funder_name");
        
        if (fundedBy === "External") {
            externalDiv.style.display = "block";
            externalInput.setAttribute("required", "required");
        } else {
            externalDiv.style.display = "none";
            externalInput.removeAttribute("required");
            externalInput.value = "";
        }
    }
</script>

</body>
</html>
