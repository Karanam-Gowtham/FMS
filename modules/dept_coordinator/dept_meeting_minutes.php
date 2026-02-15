<?php
// Start session
session_start();

// Check if the user is logged in and retrieve username
if (!isset($_SESSION['username'])) {
    die("Unauthorized access. Please log in first.");
}
$username = $_SESSION['username'];
if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}

// Connect to the database
include("../../includes/connection.php");
include("../../includes/header.php");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the department from reg_tab using username
$sql_dept = "SELECT dept FROM reg_tab WHERE userid = ?";
$stmt_dept = $conn->prepare($sql_dept);
$stmt_dept->bind_param("s", $username);
$stmt_dept->execute();
$result_dept = $stmt_dept->get_result();

if ($result_dept->num_rows > 0) {
    $row = $result_dept->fetch_assoc();
    $rdept = $row['dept']; // Store the retrieved department
} else {
    die("Department not found for the user.");
}

// Hardcoded event for this specific program
$event = 'Dept Meeting Minutes';

// File options specific to Dept Meeting Minutes
$file_options = [
    'Meeting Minutes',
    'Action Taken Report',
    'Attendance Sheet',
    'Agenda',
    'Other'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acd_year = $_POST['year'];
    $file_type = $_POST['file_type']; // This maps to sub_file_type in DB
    $file_name = $_POST['file_name'];
    $student_year = $_POST['student_year'];
    $semester = $_POST['semester'];
    $file_path = '../../uploads/' . $_FILES['file']['name']; // Store file path
    
    // Upload the file to the server
    if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
        // Prepare the SQL query to insert the data into the database
        // mapping $event to file_type column, and $file_type (option) to sub_file_type column
        // Using backticks for `year` as it is a reserved word
        $sql = "INSERT INTO dept_files (username, dept, academic_year, file_type, sub_file_type, file_name, file_path, semester, `year`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssii", $username, $dept, $acd_year, $event, $file_type, $file_name, $file_path, $semester, $student_year);
        
        if ($stmt->execute()) {
            echo "<script>alert('File uploaded successfully!'); </script>";
        } else {
            echo "<script>alert('File was not uploaded!');</script>";
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
    <title>Upload <?php echo $event; ?></title>
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

        .cont1{
            display: flex;
            justify-content: center;
            align-items: center;
            
        }
        
          /* Navigation */
    .navbar { 
        font-size: larger;
    }

    #sp{
        color:blue;
    }
    
    .nav-container {
        background-color: white;
        width:150vw;
        margin-top: 80px;
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
        input{
            
            width: 80%;
            color:white;
        }
        select{
            width:84%;
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
            margin-bottom:50px;
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
            button {
                width: 100%;
            }
        }
    </style>
</head>
<div>
<nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span id="sp">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span id="sp">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../faculty/acd_year.php?dept=<?php echo"$dept" ?>" class="home-icon"> Faculty </a></span>
                <span id="sp">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a"> <?php echo"$event" ?> </a></span>
                <span id="sp">&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>
<div class="cont1">
    <div class="container11">
        <h1>Upload <?php echo $event; ?></h1>
        <form action="" method="POST" enctype="multipart/form-data" class="upload-form">
            <label for="file_name">File Name:</label>
            <input type="text" name="file_name" id="file_name" required>

            <div class="form-group">
                        <label for="academic-year">Select Academic Year:</label>
                        <select name="year" id="academic-year" required>
                            <option value="" disabled selected>Select an academic year</option>
                            <?php
                            include("../../includes/connection.php"); // Must be before this code

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
                <label for="student-year">Select Year:</label>
                <select name="student_year" id="student-year" required>
                    <option value="" disabled selected>Select Year</option>
                    <?php
                    for ($i = 1; $i <= 4; $i++) {
                        echo "<option value=\"$i\">Year $i</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="semester">Select Semester:</label>
                <select name="semester" id="semester" required>
                    <option value="" disabled selected>Select Semester</option>
                    <?php
                    for ($i = 1; $i <= 8; $i++) {
                        echo "<option value=\"$i\">Semester $i</option>";
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

<?php
$conn->close();
?>
