<?php
include("../../includes/connection.php");
session_start();

if (!isset($_SESSION['username'])) {
    die("You need to log in to view your uploads.");
}

$event = isset($_POST['event']) ? $_POST['event'] : (isset($_GET['event']) ? $_GET['event'] : '');
$designation = isset($_POST['desg']) ? $_POST['desg'] : (isset($_GET['desg']) ? $_GET['desg'] : '');

// Get the academic year, criteria, and criteria number from the POST request
$academic_year = isset($_POST['academic_year']) ? $_POST['academic_year'] : '';
$criteria = isset($_POST['criteria']) ? $_POST['criteria'] : '';
$criteria_no = isset($_POST['criteria_no']) ? $_POST['criteria_no'] : '';

date_default_timezone_set('Asia/Kolkata');

if (isset($_POST['upload'])) {
    // Retrieve the username from the session
    $username = $_SESSION['username'];
    $faculty_name = $_POST['faculty_name'];
    $username = $_SESSION['username'];
    $desc = $_POST['event_details'];
    $filename = $_POST['file_name'];
    $branch = isset($_POST['branch']) ? $_POST['branch'] : '';
    $sem = isset($_POST['sem']) ? $_POST['sem'] : ''; // Semester is optional and set based on criteria_no
    $section = isset($_POST['section']) ? $_POST['section'] : '';
    $ext_or_int = isset($_POST['ext_or_int']) ? $_POST['ext_or_int'] : ''; // For Ext or Int
    $targetDir = "../../uploads/";
    $currentDateTime = date('Y-m-d H:i:s'); // Get current date and time

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
            $sql = "INSERT INTO files (UserName,description, academic_year, branch, sem, section, faculty_name, ext_or_int, uploaded_at, file_name, file_path, criteria, criteria_no) 
                    VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssssssss", $username, $desc, $academic_year, $branch, $sem, $section, $faculty_name, $ext_or_int, $currentDateTime, $filename, $filepath, $criteria, $criteria_no);

            if ($stmt->execute()) {
                $uploadSuccess = true;
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "<p class='error-message'>Error moving uploaded file: $filename</p>";
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
    include '../../includes/header.php';
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <link rel='stylesheet' href="../../assets/css/upload_aaa.css">
    <style>
        .lg{
            background-color: red;
        }
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
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="c_login_n.php?event=<?php echo urlencode($event); ?>" class="home-icon">Central (<?php echo htmlspecialchars($event); ?>)</a></span>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="c_aqar_files.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon"><?php echo htmlspecialchars($designation); ?></a></span>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="criteria.php?year=<?php echo urlencode($academic_year); ?>&criteria=<?php echo urlencode($criteria); ?>&designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon">Criteria <?php echo htmlspecialchars($criteria); ?></a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a">Upload Files  </a></span>
                
            </div>
        </div>
    </nav>
    

    <div class="upload-container">
        <h1>Upload Files</h1>
        <form action="upload.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <!-- Hidden fields for academic_year, criteria, and criteria_no -->
            <input type="hidden" name="academic_year" id="academic_year" value="<?php echo $academic_year; ?>">
            <input type="hidden" name="criteria" id="criteria" value="<?php echo $criteria; ?>">
            <input type="hidden" name="criteria_no" id="criteria_no" value="<?php echo $criteria_no; ?>">
            <input type="hidden" name="event" value="<?php echo htmlspecialchars($event); ?>">
            <input type="hidden" name="desg" value="<?php echo htmlspecialchars($designation); ?>">


            <label for="faculty_name">Faculty Name:</label>
            <input type="text" id="faculty_name" name="faculty_name" required>

            <?php if ($criteria == '1'|| $criteria_no == '6.1.1(A)' || $criteria_no == '6.1.1(F)' || $criteria_no == '6.1.1(I)' ): ?>
            <label for="branch">Branch:</label>
            <select id="branch" name="branch" onchange="updateSemOptions()" required>
                <option value="" disabled selected>Select the branch</option>
                <option value="AIDS">AIDS</option>
                <option value="AIML">AIML</option>
                <option value="CSE">CSE</option>
                <option value="CIVIL">CIVIL</option>
                <option value="MECH">MECH</option>
                <option value="EEE">EEE</option>
                <option value="ECE">ECE</option>
                <option value="IT">IT</option>
                <option value="BSH">BSH</option>
            </select>
            <?php endif; ?>

            <?php if ($criteria_no == '6.1.1(A)'):?>
            <label for="sem">Semester:</label>
            <select id="sem" name="sem" required>
                <!-- Options will be dynamically populated based on branch selection -->
            </select>

            <label for="section">Section:</label>
            <input type="text" id="section" name="section" required>
            <?php endif; ?>

            <?php if ($criteria_no == '6.1.1(F)'): ?>
            <label for="ext_or_int">Ext or Int:</label>
            <select id="ext_or_int" name="ext_or_int" required>
                <option value="" disabled selected>Choose one</option>
                <option value="Internal">Internal</option>
                <option value="External">External</option>
            </select>
            <?php endif; ?>
            
            <label for="event_details">Event Description:</label>
            <textarea id="event_details" name="event_details" rows="4" cols="50" placeholder="Enter details about the event..." required></textarea>


            <label for="file_name">File Name:</label>
            <input type="text" id="file_name" name="file_name" required>

            <label for="file">Choose files to upload:</label>
            <input type="file" id="file" name="files[]" multiple required>

            <button type="submit" class="button1" name="upload" id='but'>Upload</button>
        </form>

    </div>

</body>
<script>
    function updateSemOptions() {
        var branch = document.getElementById('branch').value;
        var sem = document.getElementById('sem');
        sem.innerHTML = ''; // Clear previous options

        if (branch === 'BSH') {
            sem.innerHTML += '<option value="" disabled selected>select sem</option>';
            sem.innerHTML += '<option value="1">1</option>';
            sem.innerHTML += '<option value="2">2</option>';
        } else {
            sem.innerHTML += '<option value="" disabled selected>select sem</option>';
            for (var i = 3; i <= 8; i++) {
                sem.innerHTML += '<option value="' + i + '">' + i + '</option>';
            }
        }
    }

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
