<?php
include("connection.php");
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

if (isset($_GET['type'])) {
    $type = $_GET['type']; // Get the 'dept' value from the URL
} else {
    echo "desg not set.";
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get session username
    $user = $_SESSION['username'];

    $branch_query = "SELECT dept FROM reg_tab WHERE userid = '$user'";
        $branch_result = $conn->query($branch_query);

        if ($branch_result && $branch_result->num_rows > 0) {
            $branch_row = $branch_result->fetch_assoc();
            $branch = $branch_row['dept'];
        } else {
            die("Branch not found for the user.");
        }

    // Get form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $date_from = mysqli_real_escape_string($conn, $_POST['date_from']);
    $date_to = mysqli_real_escape_string($conn, $_POST['date_to']);
    $organised_by = mysqli_real_escape_string($conn, $_POST['organised_by']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);

    // Handle file upload
    $certificate = $_FILES['certificate']['name'];
    $target_dir = "uploads/certificates/";
    $target_file = $target_dir . basename($certificate);

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['certificate']['tmp_name'], $target_file)) {
        // Prepare the SQL query to insert data into the database
        date_default_timezone_set('Asia/Kolkata');

        $submission_time = date('Y-m-d H:i:s');

        $sql = "INSERT INTO fdps_tab (username, branch, title, date_from, date_to, organised_by, location, certificate, submission_time,year)
                VALUES ('$user','$dept', '$title', '$date_from', '$date_to', '$organised_by', '$location', '$target_file' , '$submission_time','$year')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Records uploaded successfully');</script>";

        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
include 'header.php';
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
                <span>&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a"> FPDs </a></span>
                <span>&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>
<div class="cont1">
<div class="container">
    <h1>FDPS Attended Form</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
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

        <div class="form-group">
            <label for="certificate">Upload Certificate:</label>
            <input type="file" id="certificate" name="certificate" required>
        </div>

        <button class="btn1" type="submit">Submit</button>
    </form>
</div>
</div>
</body>
</html>
