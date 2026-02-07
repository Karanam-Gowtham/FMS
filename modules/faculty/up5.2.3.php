<?php
include("../../includes/connection.php");
session_start();

if (!isset($_SESSION['username'])) {
    die("<script>alert('You need to log in to view this page.'); window.location.href='login.php';</script>");
}

$event = isset($_POST['event']) ? $_POST['event'] : (isset($_GET['event']) ? $_GET['event'] : '');
$designation = isset($_POST['desg']) ? $_POST['desg'] : (isset($_GET['desg']) ? $_GET['desg'] : '');
if(isset($_POST['academic_year'])){
    $academic_year = $_POST['academic_year'];
    $criteria = $_POST['criteria'] ?? '';
    $criteria_no = $_POST['criteria_no'] ?? '';
}else{
    echo"please select the academic_year";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    date_default_timezone_set('Asia/Kolkata');

    $username = $_SESSION['username'];
    $faculty_name = htmlspecialchars($_POST['faculty_name']);
    $academic_year = htmlspecialchars($_POST['academic_year']);
    $criteria = htmlspecialchars($_POST['criteria']);
    $criteria_no = htmlspecialchars($_POST['criteria_no']);
    $reg_no = htmlspecialchars($_POST['reg_no']);
    $exam = htmlspecialchars($_POST['exam']);
    $exam_status = htmlspecialchars($_POST['status']);
    $filename = $_POST['file_name'];
    $currentDateTime = date('Y-m-d H:i:s');

    $targetDir = "../../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    foreach ($_FILES['files']['name'] as $key => $file_name) {
        $fileTmpPath = $_FILES['files']['tmp_name'][$key];
        $safeFileName = basename($file_name);
        $filepath = $targetDir . $safeFileName;

        if (move_uploaded_file($fileTmpPath, $filepath)) {
            $sql = "INSERT INTO files5_2_3 (username, faculty_name, academic_year, reg_no, exam, exam_status, uploaded_at, file_name, file_path) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssss", $username, $faculty_name, $academic_year, $reg_no, $exam, $exam_status, $currentDateTime, $filename, $filepath);

            if (!$stmt->execute()) {
                echo "<script>alert('Database Error: " . $stmt->error . "');</script>";
            }
        } else {
            echo "<script>alert('Error uploading file: $safeFileName');</script>";
        }
    }

    $stmt->close();
    $conn->close();
    echo "<script>alert('File(s) uploaded successfully.'); window.location.href='criteria.php?year=' + encodeURIComponent('$academic_year') + '&criteria=' + encodeURIComponent('$criteria') + '&designation=' + encodeURIComponent('$designation') + '&event=' + encodeURIComponent('$event');</script>";
}
?>

<?php include '../../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details of students who qualified in state/ national/ international examinations during the year</title>
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
        <h1>Details of students who qualified in state/ national/ international examinations during the year    </h1>
        <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">

            <label for="faculty_name">Faculty Name:</label>
            <input type="text" id="faculty_name" name="faculty_name" required>

            <!-- Hidden Fields -->
            <input type="hidden" name="academic_year" value="<?php echo $academic_year; ?>">
            <input type="hidden" name="criteria" value="<?php echo $criteria; ?>">
            <input type="hidden" name="criteria_no" value="<?php echo $criteria_no; ?>">

            <input type="hidden" name="event" value="<?php echo htmlspecialchars($event); ?>">
            <input type="hidden" name="desg" value="<?php echo htmlspecialchars($designation); ?>">

            <label for="reg_no">Registration Number/Roll Number for the Exam:</label>
            <input type="text" id="reg_no" name="reg_no" required>

            <label for="exam">Name of the Exam:</label>
            <select id="exam" name="exam" required>
                <option value="">-- Select Exam --</option>
                <option value="NET">NET</option>
                <option value="IIT-JAM">IIT-JAM</option>
                <option value="SLET/SET">SLET/SET</option>
                <option value="JRF">JRF</option>
                <option value="GATE">GATE</option>
                <option value="GMAT">GMAT</option>
                <option value="CAT">CAT</option>
                <option value="GRE">GRE</option>
                <option value="JAM">JAM</option>
                <option value="IELTS">IELTS</option>
                <option value="TOEFL">TOEFL</option>
                <option value="Civil Services">Civil Services</option>
                <option value="State government examinations">State government examinations</option>
                <option value="Other">Other examinations conducted by the State / Central Government Agencies (Specify)</option>
            </select>

            <label for="exam_status">Appeared or Qualified:</label>
            <select id="exam_status" name="status" required>
                <option value="">-- Select Status --</option>
                <option value="Appeared">Appeared</option>
                <option value="Qualified">Qualified</option>
            </select>

            <label for="file_name">File Name:</label>
            <input type="text" id="file_name" name="file_name" required>

            <label for="files">Choose File:</label>
            <input type="file" id="files" name="files[]" multiple required>

            <button type="submit" name="upload">Upload</button>
        </form>
    </div>

    <script>
        function validateForm() {
            const academicYear = document.getElementById('academic_year').value;
            const criteria = document.getElementById('criteria').value;
            const criteriaNo = document.getElementById('criteria_no').value;
            const exam = document.getElementById('exam').value;
            const examStatus = document.getElementById('exam_status').value;
            const files = document.getElementById('files').files;

            if (!academicYear || !criteria || !criteriaNo) {
                alert('Please fill out the academic year, criteria, and criteria number.');
                return false;
            }
            if (!exam) {
                alert('Please select an exam.');
                return false;
            }
            if (!examStatus) {
                alert('Please select whether you appeared or qualified.');
                return false;
            }
            if (files.length === 0) {
                alert('Please upload at least one file.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
