<?php
include("../../includes/connection.php");
session_start();

if (!isset($_SESSION['username'])) {
    die("You need to log in to view this page.");
}
$event = isset($_POST['event']) ? $_POST['event'] : (isset($_GET['event']) ? $_GET['event'] : '');
$designation = isset($_POST['desg']) ? $_POST['desg'] : (isset($_GET['desg']) ? $_GET['desg'] : '');
$academic_year = $_POST['academic_year'] ?? '';
$criteria = $_POST['criteria'] ?? '';
$criteria_no = $_POST['criteria_no'] ?? '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    date_default_timezone_set('Asia/Kolkata');

    $username = $_SESSION['username'];
    $faculty_name = $_POST['faculty_name'];
    $year = $_POST['year'];
    $academic_year = $_POST['academic_year'];
    $activity_exam = $_POST['activity_exam'];
    $filename = $_POST['file_name'];
    $students_exam = $_POST['students_exam'];
    $career_details = $_POST['career_details'];
    $students_career = $_POST['students_career'];
    $students_placed = $_POST['students_placed'];
    $currentDateTime = date('Y-m-d H:i:s');

    $targetDir = "../../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (!empty($_FILES['files']['name'][0])) {
        foreach ($_FILES['files']['name'] as $key => $file_name) {
            $fileTmpPath = $_FILES['files']['tmp_name'][$key];
            $filepath = $targetDir . basename($file_name);

            if (move_uploaded_file($fileTmpPath, $filepath)) {
                $sql = "INSERT INTO files5_1_4 (username, faculty_name, academic_year, year, activity_exam, students_exam, career_details, students_career, students_placed, uploaded_at, file_name, file_path) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssssss", 
                                  $username, $faculty_name, $academic_year, $year, 
                                  $activity_exam, $students_exam, $career_details, 
                                  $students_career, $students_placed, $currentDateTime, 
                                  $filename, $filepath);

                if (!$stmt->execute()) {
                    echo "Error: " . $stmt->error;
                }
            } else {
                echo "<p class='error-message'>Error uploading file: $file_name</p>";
            }
        }
        echo "<script>alert('File(s) uploaded successfully.'); window.location.href='criteria.php?year=' + encodeURIComponent('$academic_year') + '&criteria=' + encodeURIComponent('$criteria') + '&designation=' + encodeURIComponent('$designation') + '&event=' + encodeURIComponent('$event');</script>";
    } else {
        echo "<p class='error-message'>No files selected for upload.</p>";
    }
}
?>
<?php include '../../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guidance & Career Counselling Form</title>
    <link rel="stylesheet" href="../../assets/css/upload_aaa.css">
    <style>
                                        /* Navigation */
    .navbar { 
        font-size: larger;
        margin-bottom: -50px;
    }

    .nav-container {
        background-color: white;
        width:100vw;
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
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../central/c_login_n.php?event=<?php echo urlencode($event); ?>" class="home-icon">Central (<?php echo htmlspecialchars($event); ?>)</a></span>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../central/c_aqar_files.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon"><?php echo htmlspecialchars($designation); ?></a></span>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="criteria.php?year=<?php echo urlencode($academic_year); ?>&criteria=<?php echo urlencode($criteria); ?>&designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon">Criteria <?php echo htmlspecialchars($criteria); ?></a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a">Upload Files  </a></span>
                
            </div>
        </div>
    </nav>
    <div class="upload-container">
        <h1>Guidance & Career Counselling Form</h1>
        <form action="" method="POST" enctype="multipart/form-data" >

            <input type="hidden" name="academic_year" value="<?php echo $academic_year; ?>">
            <input type="hidden" name="criteria" value="<?php echo $criteria; ?>">
            <input type="hidden" name="criteria_no" value="<?php echo $criteria_no; ?>">
            <input type="hidden" name="event" value="<?php echo htmlspecialchars($event); ?>">
            <input type="hidden" name="desg" value="<?php echo htmlspecialchars($designation); ?>">


            <label for="faculty_name">Faculty Name:</label>
            <input type="text" id="faculty_name" name="faculty_name" required>

            <label for="academic_year">Academic Year:</label>
            <input type="text" id="academic_year" name="year" required>

            <label for="activity_exam">Name of the Activity (Competitive Exams):</label>
            <input type="text" id="activity_exam" name="activity_exam" required>

            <label for="students_exam">Number of Students Attended (Competitive Exams):</label>
            <input type="number" id="students_exam" name="students_exam" required>

            <label for="career_details">Details of Career Counselling:</label>
            <textarea id="career_details" name="career_details" rows="3" required></textarea>

            <label for="students_career">Number of Students Attended (Career Counselling):</label>
            <input type="number" id="students_career" name="students_career" required>

            <label for="students_placed">Number of Students Placed Through Campus Placement:</label>
            <input type="number" id="students_placed" name="students_placed" required>

            <label for="file_name">File Name:</label>
            <input type="text" id="file_name" name="file_name" required>

            <label for="files">Choose Files:</label>
            <input type="file" id="files" name="files[]" multiple required>

            <button type="submit" name="upload">Upload</button>
        </form>
    </div>

</body>
</html>
