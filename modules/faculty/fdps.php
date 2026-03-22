<?php

include "../../includes/connection.php";
include "../../includes/header.php";

if (!isset($_SESSION['username'])) {
    die("You need to log in to view this page.");
}

$username = $_SESSION['username'];
$dept = isset($_GET['dept']) ? $_GET['dept'] : '';

// Fetch branch/dept if not set or just to be sure
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
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];
    $organised_by = $_POST['organised_by'];
    $location = $_POST['location'];
    $year = $_POST['year'];

    // File Upload
    $target_dir = "../../uploads/fdps/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . '_' . basename($_FILES["certificate"]["name"]);
    $target_file = $target_dir . $file_name;
    $db_path = "uploads/fdps/" . $file_name; // Relative path for DB if that's the convention
    // Actually look at dc_up_files.php: '../../uploads/' . $_FILES['file']['name'];
    // It stores full path? 
    // Let's use the convention seen in download_papers1.php which uses file_exists($file[$fileColumn]).
    // In dashboard.php reupload: $fs_path = __DIR__ . '/' . $relative;
    // Let's store "../../uploads/fdps/..." to be safe or relative.
    // dc_up_files stores '../../uploads/...'

    if (move_uploaded_file($_FILES["certificate"]["tmp_name"], $target_file)) {
        $status = 'Pending HOD';
        $sql = "INSERT INTO fdps_tab (username, branch, title, date_from, date_to, organised_by, location, certificate, submission_time, year, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $username, $dept, $title, $date_from, $date_to, $organised_by, $location, $target_file, $year, $status);

        if ($stmt->execute()) {
            echo "<script>alert('Record added successfully!'); window.location.href='acd_year.php?dept=" . urlencode($dept) . "';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Upload FDP Attended</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #0a192f 0%, #172a45 100%);
            color: white;
            min-height: 100vh;
            margin: 0;
        }

        .navbar {
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 100px;
            border-bottom: 1px solid #eee;
            background-color: white;
            font-size: larger;
        }

        .nav-container {
            /* margin-top moved to .navbar */
            margin-left: 100px;
            max-width: 80rem;
            padding: 0 1rem;
        }

        .nav-items {
            display: flex;
            align-items: center;
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

        .sp {
            color: blue;
        }

        .container11 {
            margin: 50px auto;
            background: rgba(16, 15, 15, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.2);
            max-width: 600px;
            width: 90%;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="date"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #ff6347;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }

        button:hover {
            background: #e55337;
        }

        option {
            background-color: #333;
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
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                        href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>"
                        class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span class="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                        href="acd_year.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Faculty</a></span>
                <span class="sp">&nbsp; >> &nbsp;</span><span class="main"><a href="#" class="main-a">FDPs
                        Attended</a></span>
            </div>
        </div>
    </nav>

    <div class="container11">
        <h2>FDPs Attended</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="title">Title of FDP:</label>
            <input type="text" id="title" name="title" required>

            <label for="organised_by">Organised By:</label>
            <input type="text" id="organised_by" name="organised_by" required>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>

            <label for="year">Academic Year:</label>
            <select id="year" name="year" required>
                <option value="">Select Year</option>
                <?php
                $y_sql = "SELECT year FROM academic_year ORDER BY year DESC";
                $y_res = $conn->query($y_sql);
                if ($y_res) {
                    while ($y = $y_res->fetch_assoc()) {
                        echo "<option value='" . $y['year'] . "'>" . $y['year'] . "</option>";
                    }
                }
                ?>
            </select>

            <label for="date_from">Date From:</label>
            <input type="date" id="date_from" name="date_from" required>

            <label for="date_to">Date To:</label>
            <input type="date" id="date_to" name="date_to" required>

            <label for="certificate">Certificate:</label>
            <input type="file" id="certificate" name="certificate" required>

            <button type="submit">Submit</button>
        </form>
    </div>

</body>

</html>
<?php $conn->close(); ?>