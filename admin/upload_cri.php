<?php
include_once "../includes/connection.php";


if (!isset($_SESSION['cri_username'])) {
    die("You need to log in to view your uploads.");
}

$event = $_POST['event'] ?? $_GET['event'] ?? '';
$designation = $_POST['designation'] ?? $_GET['designation'] ?? '';

$criteria = isset($_GET['criteria']) ? htmlspecialchars($_GET['criteria']) : 'Not Selected';

// Get the academic year, criteria, and criteria number from the POST request
$academic_year = isset($_POST['academic_year']) ? $_POST['academic_year'] : '';
$criteria = isset($_POST['criteria']) ? $_POST['criteria'] : '';
$criteria_no = isset($_POST['criteria_no']) ? $_POST['criteria_no'] : '';

date_default_timezone_set('Asia/Kolkata');

if (isset($_POST['upload'])) {
    // Retrieve the username from the session
    $username = $_SESSION['cri_username'];
    $faculty_name = $_POST['faculty_name'];
    $filename = $_POST['file_name'];
    $targetDir = "uploads1/";
    $currentDateTime = date('Y-m-d H:i:s');
    $criteria = $_POST['criteria'];
    $criteria_no = $_POST['criteria_no'];
    $academic_year = $_POST['academic_year'];


    // Create directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $uploadSuccess = false;
    foreach ($_FILES['files']['name'] as $key => $file_name) {
        $filepath = $targetDir . basename($file_name);

        if (move_uploaded_file($_FILES['files']['tmp_name'][$key], $filepath)) {
            // Use prepared statements to prevent SQL injection
            $sql = "INSERT INTO a_cri_files (username, academic_year, criteria,criteria_no, uploaded_at, Faculty_name, file_name, file_path) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss", $username, $academic_year, $criteria, $criteria_no, $currentDateTime, $faculty_name, $filename, $filepath);

            if ($stmt->execute()) {
                $uploadSuccess = true;
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "<p class='error-message'>Error moving uploaded file: " . htmlspecialchars($filename) . "</p>";
        }
    }

    $stmt->close();
    $conn->close();
    if ($uploadSuccess) {
        echo "<script>alert('File(s) uploaded successfully.');</script>";
    }
}
?>

<?php 
include_once 'header_admin.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <link rel='stylesheet' href="../css/upload_a1.css">
    <style>
                                 /* Navigation */
                                 .navbar {
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 80px;
            border-bottom: 1px solid #eee;
 
        font-size: larger;
    }

    .nav-container {
        background-color: rgb(244, 237, 237);
        width:100vw;
         /* margin-top moved to .navbar */
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
                <a href="../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../modules/central/c_login_n.php?event=<?php echo urlencode($event); ?>" class="home-icon">Central (<?php echo htmlspecialchars($event); ?>)</a></span>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../modules/central/c_aqar_files.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon"><?php echo htmlspecialchars($designation); ?></a></span>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="criteria_cri_a.php?year=<?php echo urlencode($academic_year); ?>&criteria=<?php echo urlencode($criteria); ?>&designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon">Criteria <?php echo htmlspecialchars($criteria); ?></a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a">Upload Files</a></span>

            </div>
        </div>
    </nav>

    <div class="upload-container">
        <h1>Upload Files</h1>
        <form action="upload_cri.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <!-- Hidden fields for academic_year, criteria, and criteria_no -->
            <input type="hidden" name="academic_year" id="academic_year" value="<?php echo htmlspecialchars((string)$academic_year); ?>">
            <input type="hidden" name="criteria" id="criteria" value="<?php echo htmlspecialchars((string)$criteria); ?>">
            <input type="hidden" name="criteria_no" id="criteria_no" value="<?php echo htmlspecialchars((string)$criteria_no); ?>">
            <input type="hidden" name="event" value="<?php echo htmlspecialchars($event); ?>">
            <input type="hidden" name="designation" value="<?php echo htmlspecialchars($designation); ?>">


            <label for="faculty_name">Faculty Name:</label>
            <input type="text" id="faculty_name" name="faculty_name" required>

            <label for="file_name">File Name:</label>
            <input type="text" id="file_name" name="file_name" required>

            <label for="file">Choose files to upload:</label>
            <input type="file" id="file" name="files[]" multiple required>

            <button type="submit" name="upload" id='but'>Upload</button>
        </form>

    </div>

</body>
<script>

    function validateForm() {
        var academic_year = document.getElementById('academic_year').value;
        var criteria = document.getElementById('criteria').value;
        var criteria_no = document.getElementById('criteria_no').value;

        if (academic_year === '' || criteria === '' || criteria_no === '') {
            alert('Please fill out the academic year, criteria, and criteria number.');
            return false; // Prevent form submission
        }

        return true; // Proceed with form submission if validation passes
    }
</script>
</html>
