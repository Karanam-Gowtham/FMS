<?php
// Database configuration
include_once '../../includes/connection.php';


    
session_start();

if (!isset($_SESSION['c_cord'])) {
    die("You need to log in .");
}

$username = $_SESSION['c_cord'];
include_once '../../includes/header.php';
$event = htmlspecialchars($_GET['event'] ?? 'Unknown');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $acd_year = $_POST['year'];
    $fileName = $_POST['event_details'];
    $uploadedBy = $_POST['uploaded_by'];
    $fileTmpPath = $_FILES['file_upload']['tmp_name'];
    $fileNameStored = basename($_FILES['file_upload']['name']);
    $uploadDir = '../../uploads/';
    $filePath = $uploadDir . $fileNameStored;

    // Handle photos
    $photoPaths = [];
    for ($i = 1; $i <= 4; $i++) {
        $photoKey = "photo$i";
        if (isset($_FILES[$photoKey]) && $_FILES[$photoKey]['error'] == 0) {
            $photoTmpPath = $_FILES[$photoKey]['tmp_name'];
            $photoName = basename($_FILES[$photoKey]['name']);
            $photoPath = $uploadDir . $photoName;
            if (move_uploaded_file($photoTmpPath, $photoPath)) {
                $photoPaths[] = $photoPath;
            }
        }
    }

    // Create uploads directory if it doesn't exist
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
        die("Failed to create upload directory.");
    }

    // Move the main file to the uploads directory
    if (file_exists($fileTmpPath) && move_uploaded_file($fileTmpPath, $filePath)) {
        // Prepare the SQL statement for storing file details including photos
        // Prepare the SQL statement for storing file details including photos
            $photo1 = $photoPaths[0] ?? null;
            $photo2 = $photoPaths[1] ?? null;
            $photo3 = $photoPaths[2] ?? null;
            $photo4 = $photoPaths[3] ?? null;

            // Inside the if ($_SERVER['REQUEST_METHOD'] == 'POST') block

            // Set the timezone to IST
            date_default_timezone_set('Asia/Kolkata');
            $submissionTime = date('d-m-Y H:i:s'); // Current IST time

            if (strtolower($event) === 'clubs') {
                $club_name = $_POST['club_name'] ?? null;

                // Insert including club_name
                $stmt = $conn->prepare("INSERT INTO central_files (event, acd_year, club_name, event_name, file_name, file_path, uploaded_by, photo1, photo2, photo3, photo4) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssssss", $event, $acd_year, $club_name, $event_name, $fileName, $filePath, $uploadedBy, $photo1, $photo2, $photo3, $photo4);
            } else {
                // Insert without club_name
                $stmt = $conn->prepare("INSERT INTO central_files (event, acd_year, event_name, file_name, file_path, uploaded_by, photo1, photo2, photo3, photo4) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssss", $event, $acd_year, $event_name, $fileName, $filePath, $uploadedBy, $photo1, $photo2, $photo3, $photo4);
            }
        
        if ($stmt->execute()) {
            echo "<script>alert('Details uploaded successfully!');</script>";
        } else {
            echo "<script>alert('Database error: Could not save the file details.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error uploading the file. Temporary file not found or move failed.');</script>";
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Global Styles */
        body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background: linear-gradient(135deg, #0a192f 0%, #172a45 100%);
        
        color: #fff;
    }
        /* Container */
        .container111 {
            
            background:rgba(0, 0, 0, 0.6);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 500px;
            margin-bottom: 50px;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.2);
        }
        

        .container11{
            display: flex;
        justify-content: center;
        align-items: center;
            margin-top: 80px;
            align-items: center;
            margin-bottom:100px;
            margin-right: 200px;
        }
        /* Form */
        .upload-form h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #007bff;
        }
        .upload-form h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color:rgb(133, 38, 162);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: white;
        }

        input[type="text"],
        select,
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        input[type="text"]:focus,
        textarea,select,
        input[type="file"]:focus {
            outline: none;
            border-color: #007bff;
        }
        .file{
            color: white;
        }/* Navigation */
        .navbar {
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 80px;
            border-bottom: 1px solid #eee;
 
            font-size: larger;
        }

        .sp{
            color:blue;
        }
        
        .nav-container {
            background-color: white;
            width:150vw;
             /* margin-top moved to .navbar */
            padding: 0 1rem;
        }

        .nav-items {
            margin-left: 30px;
            display: flex;
            align-items: center;
            justify-content:flex-start;
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
        .btn1{
            width: 80px;
            font-weight: bold;
            border-radius: 10px;
            border-style: none;
            height:30px;
            background-color: #007BFF;
        }
        .btn1:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
            
        }
        .btn_12 {
            position: relative;
            bottom:500px;
            left: 800px;
            padding: 15px 30px;
            font-size: 18px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease, transform 0.3s ease;
            }

            .btn12:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            }

            .btn12:active {
            transform: translateY(0);
            }
        
            </style>
            
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-items">
            <a href="../../index.php" class="home-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </a>
            <span class="sp">&nbsp; >> &nbsp;</span><span class="sid"><a href="c_login.php?event=<?php echo urlencode($event); ?>" class="home-icon">Central</a></span>
            <span class="sp">&nbsp; >> &nbsp;</span><span class="main"><span class="main-a"><?php echo htmlspecialchars($event); ?> Files</span></span>
            <span class="sp">&nbsp; >> &nbsp;</span>
        </div>
    </div>
</nav>

    <div class="container11">
        
    <a href="c_down_files.php?event=<?php echo urlencode($event); ?>"><button class="btn_12">view <?php echo htmlspecialchars($event); ?> details</button></a>
    <div class="container111">
        <form action="" method="POST" enctype="multipart/form-data" class="upload-form">
            <h1><?php echo htmlspecialchars($event); ?> Events</h1>

            <h2>Upload details</h2>
            <?php if (strtolower($event) === 'clubs'): ?>
                <div class="form-group">
                    <label for="club_name">Choose Club:</label>
                    <select id="club_name" name="club_name" required>
                        <option value="" selected disabled>-- Select a Club --</option>
                        <option value="Amateur Radio Club">Amateur Radio Club</option>
                        <option value="Protect and Innovation Club">Protect and Innovation Club</option>
                        <option value="Green Eco Club">Green Eco Club</option>
                        <option value="Robotics Club">Robotics Club</option>
                        <option value="Women Empowerment Cell">Women Empowerment Cell</option>
                        <option value="Coding club (CodeChef Chapter)">Coding club (CodeChef Chapter)</option>
                        <option value="Civil Services Aspirants Club">Civil Services Aspirants Club</option>
                        <option value="Music Club">Music Club</option>
                        <option value="Dance Club">Dance Club</option>
                        <option value="Sector Club (Indian Green Building Council)">Sector Club (Indian Green Building Council)</option>
                        <option value="Swami Vivekananda Centre for Human Excellence (Meditation)">Swami Vivekananda Centre for Human Excellence (Meditation)</option>
                        <option value="Students' Counselling">Students' Counselling</option>
                        <option value="Maths Club">Maths Club</option>
                        <option value="Quiz Club">Quiz Club</option>
                        <option value="Literary Club">Literary Club</option>
                        <option value="FM Radio">FM Radio</option>
                        <option value="Unnat Bharat Abhiyan">Unnat Bharat Abhiyan</option>
                    </select>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="event_name">Event Name:</label>
                <input type="text" id="event_name" name="event_name" placeholder="Enter event name" required>
            </div>
            <div class="form-group">
                    <label for="academic-year">Select Academic Year:</label>
                    <select name="year" id="academic-year" required>
                        <option value="" disabled selected>Select an academic year</option>
                        <?php
                        include_once "../../includes/connection.php"; // Must be before this code

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
                <label for="file_name">Event Description:</label>
                <textarea id="event_details" name="event_details" rows="4" cols="50" placeholder="Enter details about the event..." required></textarea>
            </div>
            <div class="form-group">
                <label for="file_upload">Choose File:</label>
                <input type="file" id="file_upload" class="file" name="file_upload" required>
            </div>
            <div class="form-group">
                <label for="uploaded_by">Uploaded By:</label>
                <input type="text" id="uploaded_by" name="uploaded_by" placeholder="Your name" required>
            </div>
            <div class="form-group">
                    <label for="photo1">Photo 1:</label>
                    <input type="file" id="photo1" class="file" name="photo1" required>
                </div>
                <div class="form-group">
                    <label for="photo2">Photo 2:</label>
                    <input type="file" id="photo2" class="file" name="photo2" required>
                </div>
                <div class="form-group">
                    <label for="photo3">Photo 3:</label>
                    <input type="file" id="photo3" class="file" name="photo3" required>
                </div>
                <div class="form-group">
                    <label for="photo4">Photo 4:</label>
                    <input type="file" id="photo4" class="file" name="photo4" required>
                </div>

            <div style="text-align: center;"><button type="submit" class="btn1">Upload</button></div>
        </form>
    </div>
    

    </div>
    
</body>
</html>

