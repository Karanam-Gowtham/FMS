<?php
session_start();
include("../../includes/connection.php");
include("../../includes/header.php");

if (!isset($_SESSION['username'])) {
    die("You need to log in to view this page.");
}

$username = $_SESSION['username'];
$dept = isset($_GET['dept']) ? $_GET['dept'] : '';

// Fetch branch/dept if not set
if (!$dept) {
    $stmt = $conn->prepare("SELECT dept FROM reg_tab WHERE userid = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $dept = $row['dept'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $organised_by = $_POST['organised_by'];
    $location = $_POST['location'];
    $year = $_POST['year'];
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];
    
    // Upload Dir
    $target_dir = "../../uploads/fdps_org/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    // Helper to upload file
    function uploadFile($fileInputName, $target_dir) {
        if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == 0) {
            $fileName = time() . '_' . $fileInputName . '_' . basename($_FILES[$fileInputName]["name"]);
            $targetFile = $target_dir . $fileName;
            if (move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $targetFile)) {
                return $targetFile;
            }
        }
        return "";
    }
    
    $certificate = uploadFile('certificate', $target_dir);
    $brochure = uploadFile('brochure', $target_dir);
    $schedule = uploadFile('schedule', $target_dir);
    $attendance = uploadFile('attendance', $target_dir);
    $feedback = uploadFile('feedback', $target_dir);
    $report = uploadFile('report', $target_dir);
    $photo1 = uploadFile('photo1', $target_dir);
    $photo2 = uploadFile('photo2', $target_dir);
    $photo3 = uploadFile('photo3', $target_dir);
    
    $status = 'Pending Dept Coordinator';
    
    $sql = "INSERT INTO fdps_org_tab (username, branch, title, date_from, date_to, organised_by, location, year, certificate, brochure, fdp_schedule_invitation, attendance_forms, feedback_forms, fdp_report, photo1, photo2, photo3, submission_time, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssssssss", $username, $dept, $title, $date_from, $date_to, $organised_by, $location, $year, $certificate, $brochure, $schedule, $attendance, $feedback, $report, $photo1, $photo2, $photo3, $status);
    
    if ($stmt->execute()) {
        echo "<script>alert('FDP Organized Record added successfully!'); window.location.href='acd_year.php?dept=" . urlencode($dept) . "';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload FDP Organized</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #0a192f 0%, #172a45 100%);
            color: white;
            min-height: 100vh;
            margin: 0;
            padding-bottom: 50px;
        }
        .navbar { background-color: white; font-size: larger; }
        .nav-container { margin-top: 100px; margin-left:100px; max-width: 80rem; padding: 0 1rem; }
        .nav-items { display: flex; align-items: center; height: 4rem; }
        .sid { color: rgb(48, 30, 138); font-weight: 500; }
        .main-a { color: rgb(138, 30, 113); font-weight: 500; }
        #sp { color: blue; }
        .container11 {
            margin: 50px auto;
            background: rgba(16, 15, 15, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.2);
            max-width: 800px;
            width: 90%;
        }
        h2 { text-align: center; margin-bottom: 20px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="date"], select, input[type="file"] {
            width: 100%; padding: 10px; margin-bottom: 15px;
            border-radius: 5px; border: none;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        .full-width { grid-column: span 2; }
        button {
            width: 100%; padding: 10px; background: #ff6347; color: white;
            border: none; border-radius: 5px; font-size: 1rem; cursor: pointer;
            margin-top: 20px;
        }
        button:hover { background: #e55337; }
        option { background-color: #333; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <div class="nav-items">
            <a href="../../index.php" class="home-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </a>
            <span id="sp">&nbsp; >> &nbsp;</span><span class="sid"><a href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
            <span id="sp">&nbsp; >> &nbsp;</span><span class="sid"><a href="acd_year.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Faculty</a></span>
            <span id="sp">&nbsp; >> &nbsp;</span><span class="main"><a href="#" class="main-a">FDPs Organized</a></span>
        </div>
    </div>
</nav>

<div class="container11">
    <h2>FDPs Organized</h2>
    <form method="POST" enctype="multipart/form-data" class="form-grid">
        <div class="full-width">
            <label>Title of FDP:</label>
            <input type="text" name="title" required>
        </div>
        
        <div>
            <label>Organised By:</label>
            <input type="text" name="organised_by" required>
        </div>
        
        <div>
            <label>Location:</label>
            <input type="text" name="location" required>
        </div>
        
        <div class="full-width">
            <label>Academic Year:</label>
            <select name="year" required>
                <option value="">Select Year</option>
                <?php
                $y_sql = "SELECT year FROM academic_year ORDER BY year DESC";
                $y_res = $conn->query($y_sql);
                if ($y_res) {
                    while($y = $y_res->fetch_assoc()) {
                        echo "<option value='".$y['year']."'>".$y['year']."</option>";
                    }
                }
                ?>
            </select>
        </div>
        
        <div>
            <label>Date From:</label>
            <input type="date" name="date_from" required>
        </div>
        
        <div>
            <label>Date To:</label>
            <input type="date" name="date_to" required>
        </div>
        
        <div><label>Certificate:</label><input type="file" name="certificate"></div>
        <div><label>Brochure:</label><input type="file" name="brochure"></div>
        <div><label>Schedule/Invitation:</label><input type="file" name="schedule"></div>
        <div><label>Attendance Forms:</label><input type="file" name="attendance"></div>
        <div><label>Feedback Forms:</label><input type="file" name="feedback"></div>
        <div><label>Report:</label><input type="file" name="report"></div>
        <div><label>Photo 1:</label><input type="file" name="photo1"></div>
        <div><label>Photo 2:</label><input type="file" name="photo2"></div>
        <div><label>Photo 3:</label><input type="file" name="photo3"></div>
        
        <div class="full-width">
            <button type="submit">Submit</button>
        </div>
    </form>
</div>

</body>
</html>
<?php $conn->close(); ?>
