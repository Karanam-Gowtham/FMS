<?php
// Start session

include_once "../../includes/connection.php";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Check if the user is logged in (Coordinator or Jr Assistant)
if (!isset($_SESSION['a_username']) && !isset($_SESSION['j_username'])) {
    die("Unauthorized access. Please log in first.");
}
$username = isset($_SESSION['a_username']) ? $_SESSION['a_username'] : $_SESSION['j_username'];
if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}

// Connect to the database

include_once "../../includes/header.php";




// Retrieve event from GET request
if (isset($_GET['event'])) {
    $event = $_GET['event'];
} else {
    $event = ''; // Default value if no event is provided
}

$file_options = [];
switch ($event) {
    case 'Achievements':
        $file_options = [
            'FDPS Attended',
            'Papers Published',
            'Patents',
            'FDPS Organized',
            'Conference'
        ];
        break;
    case 'dept':
        $file_options = [
            'Admin Files',
            'Student Files',
            'Student Activities Files',
            'Faculty Files',
            'Exam Section Files',
            'AMC Meeting Minutes',
            'Board Of Studies'
        ];
        break;
    default:
        $file_options = [];
        break;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $file_type = $_POST['file_type'];
    $file_name = $_POST['file_name'];
    $acd_year = $_POST['year'];
    $file_path = '../../uploads/' . $_FILES['file']['name'];

    if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
        $sql = "INSERT INTO dc_up_files (Username,file_name, acd_year,Main_file_type, file_type, file_path) VALUES (?, ?, ?, ?, ?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $username, $file_name, $acd_year, $event, $file_type, $file_path);

        if ($stmt->execute()) {
            echo "<script>alert('File uploaded successfully!');</script>";
        } else {
            echo "<script>alert('Database error: File not uploaded.');</script>";
        }
    } else {
        echo "<p>Error uploading file.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Department File</title>
    <style>
        /* Styles unchanged */
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
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 80px;
            border-bottom: 1px solid #eee;

            font-size: larger;
        }

        #sp {
            color: blue;
        }

        .nav-container {
            background-color: white;
            width: 150vw;
             /* margin-top moved to .navbar */
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

        a,
        button,
        .btn {
            text-decoration: none !important;
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
            button {
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sp">&nbsp; >> &nbsp; </span><span class="sid"><a
                        href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>"
                        class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <?php if (isset($_SESSION['j_username'])): ?>
                    <span class="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                            href="../jr_assistant/jr_acd_year.php?dept=<?php echo urlencode((string)$dept); ?>"
                            class="home-icon">jr_assistant</a></span>
                <?php else: ?>
                    <span class="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                            href="dc_acd_year.php?dept=<?php echo urlencode((string)$dept); ?>" class="home-icon">dept_coordinator</a></span>
                <?php endif; ?>
                <span class="sp">&nbsp; >> &nbsp;</span><span class="main"><a href="#"
                        class="main-a"><?php echo htmlspecialchars($event); ?>_Files</a></span>
                <span class="sp">&nbsp; >> &nbsp;</span>
            </div>
        </div>
    </nav>

    <div class="cont1">
        <div class="container11">
            <h1>Upload <?php echo htmlspecialchars(ucfirst($event)); ?> Files</h1>
            <form action="" method="POST" enctype="multipart/form-data" class="upload-form">
                <label for="file_name">File Name:</label>
                <input type="text" name="file_name" id="file_name" required>

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

                <label for="file_type">Select File Category:</label>
                <select name="file_type" id="file_type" required>
                    <option value="" disabled selected>Select File Category</option>
                    <?php
                    foreach ($file_options as $option) {
                        echo "<option value='$option'>$option</option>";
                    }
                    ?>
                </select>

                <label for="file">Choose File:</label>
                <input type="file" name="file" id="file" required>

                <button type="submit" class="button" name="submit">Upload File</button>
            </form>
        </div>
    </div>

</body>

</html>

?>
