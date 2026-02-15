<?php
session_start();
include("../../includes/connection.php");
include("../../includes/header.php");

if (!isset($_SESSION['username'])) {
    die("You need to log in to view this page.");
}

$username = $_SESSION['username'];
$dept = isset($_GET['dept']) ? $_GET['dept'] : '';

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
    $event_name = $_POST['event_name'];
    $acd_year = $_POST['year'];
    
    $target_dir = "../../uploads/student_act/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $certificate_path = "";
    if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] == 0) {
        $fileName = time() . '_' . basename($_FILES["certificate"]["name"]);
        $targetFile = $target_dir . $fileName;
        if (move_uploaded_file($_FILES["certificate"]["tmp_name"], $targetFile)) {
            $certificate_path = $targetFile;
        }
    }
    
    $status = 'Pending Dept Coordinator';
    
    // Using s_events table as seen in dashboard.php
    $sql = "INSERT INTO s_events (Username, event_name, academic_year, certificate_path, submission_time, status) 
            VALUES (?, ?, ?, ?, NOW(), ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $event_name, $acd_year, $certificate_path, $status);
    
    if ($stmt->execute()) {
        echo "<script>alert('Student activity record added successfully!'); window.location.href='acd_year.php?dept=" . urlencode($dept) . "';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Student Activity</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #0a192f 0%, #172a45 100%);
            color: white;
        }

        .cont1 {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Navigation */
        .navbar {
            font-size: larger;
        }

        #sp {
            color: blue;
        }

        .nav-container {
            background-color: white;
            width: 150vw;
            margin-top: 80px;
            padding: 0 1rem;
        }

        .nav-items {
            margin-left: 30px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            height: 4rem;
        }

        .sid {
            color: rgb(48, 30, 138);
            font-weight: 500;
        }

        .main-a {
            color: rgb(138, 30, 113);
            font-weight: 500;
        }

        .main-a:hover {
            color: rgb(182, 64, 211);
        }

        .home-icon {
            color: rgb(30, 58, 138);
            transition: color 0.2s;
        }

        .home-icon:hover {
            color: rgb(29, 78, 216);
        }

        .container11 {
            margin-top: 50px;
            background: rgba(16, 15, 15, 0.8);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.2);
            max-width: 600px;
            width: 90%;
            height: 100%;
            margin-bottom: 50px;
        }

        h1 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #fff;
        }

        .upload-form {
            margin-left: 70px;
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #fff;
            font-weight: bold;
        }

        input {
            width: 80%;
            color: white;
        }

        select {
            width: 84%;
        }

        input[type="text"],
        input[type="file"],
        select {
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
            font-weight: bold;
            font-size: 1rem;
        }

        .button {
            background: #ff6347;
            color: white;
            font-size: 1rem;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            width: 83%;
            margin-bottom: 50px;
        }

        .button:hover {
            background: #e55337;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            label {
                font-size: 1rem;
            }

            input[type="text"],
            input[type="file"],
            .button {
                width: 100%;
            }
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
                <span id="sp">&nbsp; >> &nbsp;</span><span class="sid"><a href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span id="sp">&nbsp; >> &nbsp;</span><span class="sid"><a href="acd_year.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Faculty</a></span>
                <span id="sp">&nbsp; >> &nbsp;</span><span class="main"><a href="#" class="main-a">Student Activities Files</a></span>
            </div>
        </div>
    </nav>

    <div class="cont1">
        <div class="container11">
            <h1>Upload Student Activities Files</h1>
            <form action="" method="POST" enctype="multipart/form-data" class="upload-form">
                <label for="file_name">File Name:</label>
                <input type="text" name="event_name" id="file_name" required>

                <div class="form-group">
                    <label for="academic-year">Select Academic Year:</label>
                    <select name="year" id="academic-year" required>
                        <option value="" disabled selected>Select an academic year</option>
                        <?php
                        include("../../includes/connection.php");
                        $query = "SELECT year FROM academic_year ORDER BY year DESC";
                        $result = mysqli_query($conn, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $year = htmlspecialchars($row['year']);
                                echo "<option value=\"$year\">$year</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <label for="certificate">Choose File:</label>
                <input type="file" name="certificate" id="certificate" required>

                <button type="submit" class="button">Upload File</button>
            </form>
        </div>
    </div>

</body>

</html><?php $conn->close(); ?>
