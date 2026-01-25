<?php
include("connection.php");
session_start();

if (!isset($_SESSION['username'])) {
    die("You need to log in to view your uploads.");
}

$event = isset($_POST['event']) ? $_POST['event'] : (isset($_GET['event']) ? $_GET['event'] : '');
$designation = isset($_POST['desg']) ? $_POST['desg'] : (isset($_GET['desg']) ? $_GET['desg'] : '');

// Get academic year, criteria, and criteria number from POST request
$academic_year = $_POST['academic_year'] ?? '';
$criteria = $_POST['criteria'] ?? '';
$criteria_no = $_POST['criteria_no'] ?? '';

date_default_timezone_set('Asia/Kolkata');

if (isset($_POST['upload'])) {
    $username = $_SESSION['username'];
    $faculty_name = $_POST['faculty_name'];
    $filename = $_POST['file_name'];
    
    // Scholarship-related fields
    $scheme_name = $_POST['scheme_name'];
    $gov_students = $_POST['gov_students'];
    $gov_amount = $_POST['gov_amount'];
    $inst_students = $_POST['inst_students'];
    $inst_amount = $_POST['inst_amount'];
    $ngo_students = $_POST['ngo_students'];
    $ngo_amount = $_POST['ngo_amount'];
    $ngo_name = $_POST['ngo_name'];

    $targetDir = "uploads/";
    $currentDateTime = date('Y-m-d H:i:s'); // Get current date and time

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    foreach ($_FILES['files']['name'] as $key => $file_name) {
        $filepath = $targetDir . basename($file_name);

        if (move_uploaded_file($_FILES['files']['tmp_name'][$key], $filepath)) {
            $sql = "INSERT INTO files5_1_1and2 (UserName, academic_year, faculty_name, uploaded_at, file_name, file_path, 
                                       scheme_name, gov_students, gov_amount, inst_students, inst_amount, 
                                       ngo_students, ngo_amount, ngo_name, criteria, criteria_no) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssssssssss", 
                              $username, $academic_year, $faculty_name, $currentDateTime, $filename, $filepath, 
                              $scheme_name, $gov_students, $gov_amount, $inst_students, $inst_amount, 
                              $ngo_students, $ngo_amount, $ngo_name, $criteria, $criteria_no);

            if (!$stmt->execute()) {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "<p class='error-message'>Error moving uploaded file: $file_name</p>";
        }
    }

    $stmt->close();
    $conn->close();
    echo "<script>alert('File(s) uploaded successfully.');</script>";
}
?>
<?php include './header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <link rel="stylesheet" href="css/upload_aaa.css">
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
                <a href="index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="c_login_n.php?event=<?php echo urlencode($event); ?>" class="home-icon">Central (<?php echo htmlspecialchars($event); ?>)</a></span>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="c_aqar_files.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon"><?php echo htmlspecialchars($designation); ?></a></span>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="criteria.php?year=<?php echo urlencode($academic_year); ?>&criteria=<?php echo urlencode($criteria); ?>&designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon">Criteria <?php echo htmlspecialchars($criteria); ?></a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a">Upload Files  </a></span>
                
            </div>
        </div>
    </nav>
    <div class="upload-container">
        <h1>Scholarship and Freeship Form</h1>
        <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <input type="hidden" name="academic_year" value="<?php echo $academic_year; ?>">
            <input type="hidden" name="criteria" value="<?php echo $criteria; ?>">
            <input type="hidden" name="criteria_no" value="<?php echo $criteria_no; ?>">
            <input type="hidden" name="event" value="<?php echo htmlspecialchars($event); ?>">
            <input type="hidden" name="desg" value="<?php echo htmlspecialchars($designation); ?>">

            <label for="faculty_name">Faculty Name:</label>
            <input type="text" id="faculty_name" name="faculty_name" required>

            <label for="scheme_name">Scheme Name:</label>
            <input type="text" id="scheme_name" name="scheme_name" required>

            <h3>Government Scholarships</h3>
            <label for="gov_students">Number of Students:</label>
            <input type="number" id="gov_students" name="gov_students" required>
            <label for="gov_amount">Amount:</label>
            <input type="number" id="gov_amount" name="gov_amount" required>

            <h3>Institution Scholarships</h3>
            <label for="inst_students">Number of Students:</label>
            <input type="number" id="inst_students" name="inst_students" required>
            <label for="inst_amount">Amount:</label>
            <input type="number" id="inst_amount" name="inst_amount" required>

            <h3>Non-Government Scholarships</h3>
            <label for="ngo_students">Number of Students:</label>
            <input type="number" id="ngo_students" name="ngo_students" required>
            <label for="ngo_amount">Amount:</label>
            <input type="number" id="ngo_amount" name="ngo_amount" required>
            <label for="ngo_name">NGO/Agency Name:</label>
            <input type="text" id="ngo_name" name="ngo_name" required>

            <label for="file_name">File Name:</label>
            <input type="text" id="file_name" name="file_name" required>

            <label for="file">Choose files to upload:</label>
            <input type="file" id="file" name="files[]" multiple required>

            <button type="submit" name="upload">Upload</button>
        </form>
    </div>

    <script>
        function validateForm() {
            var academic_year = document.getElementsByName('academic_year')[0].value;
            var criteria = document.getElementsByName('criteria')[0].value;
            var criteria_no = document.getElementsByName('criteria_no')[0].value;

            if (!academic_year || !criteria || !criteria_no) {
                alert('Please fill out the academic year, criteria, and criteria number.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
