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

// Retrieve event from GET request
if (isset($_GET['event'])) {
    $event = $_GET['event'];
} else {
    $event = ''; // Default value if no event is provided
}

$file_options = [];
// Set file options based on the even
    // Define cases for file options (same as original code)
    switch ($event) {
        case 'admin':
            $file_options = [
                'Course Structure', 'Result Analysis', 'Course Schedule', "Faculty's Feedback by Students", 'Feedback from Parents', 
                'Employer Feedback', 'Department Area Details', 'Departmental Laboratory Details', 'Major Equipment in the Laboratories',
                'List of Experiments', 'Major Equipment Utilization Record', 'Equipment Maintenance Record', 'Courses Linked with Employability',
                'Financial Statement/Budget Status', 'Departmental Library Details', 'Seminars/Workshops/Conferences Organized',
                'Industrial Visits', 'Guest Lectures', 'List of Projects', 'Add-on Course/Training Conducted', 'Consultancy-New', 
                'External Sports and Projects', 'Transferrable and Life Skills Courses', 'Remedial Classes', 'Course End Feedback Form',
                'Class Time Table', 'Faculty Time Table', 'Classroom Time Table', 'Lab Time Table', 'Student Progression to Higher Education',
                'Feedback from Students/Alumni/Academic Peer', 'Course File-Index', 'Feedback on Curriculum from Students/Employer/Alumni',
                'Workshops-Seminars on Research Methodology', 'Intellectual Property Rights (IPR)', 'Entrepreneurship-New', 
                'Professional Societies Chapters', 'Engineering Events Organized', 'Product Development Activities', 'Collaborative Activities',
                'Functional MoUs with Ongoing Activities', 'Mini Project Work', 'Term Paper Work', 'Mentoring'
            ];
            break;
        case 'faculty':
            $file_options = [
                'Faculty List', 'Faculty Profile', 'Academic Research', 'Books and Chapters Published', 
                'Faculty in Inter-Departmental/Institutional Activities', 'Faculty for Higher Studies', 'Faculty Attended Seminars/Internships',
                'Faculty Self-Appraisal', 'Non-Teaching Staff Skill Upgradation', 'Observations on Student Feedback',
                'Full-Time Teachers with PhD Guidance', 'Consultancy and Corporate Training', 'Financial Support to Faculty',
                'Publication of Technical Magazines/Newsletters'
            ];
            break;
        case 'student':
            $file_options = [
                'List of Forms', 'Student Addresses', 'Cumulative Monthly Attendance', 'Semester End Attendance', 'Condonation List', 
                'Detention List', 'Papers Published by Students', 'Students in Competitive Exams', 'Co-Curricular/Extra-Curricular Activities',
                'Placement Record', 'Alumni Interaction', 'Field Projects/Internships', 'List of Seminars/Workshops Attended', 
                'Online Courses Completed', 'Coding/Hardware Competitions', 'Capacity Development Activities', 'Guidance for Competitive Exams',
                'Career Counselling'
            ];
            break;
        case 'exam':
            $file_options = [
                'Notice for Internal Lab Exams', 'Invigilation Schedule', 'Absentee Statement', 'Sessional Marks Record', 'Final Sessional Marks'
            ];
            break;
        default:
            $file_options = []; // Empty if no event is provided
            break;
    }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acd_year = $_POST['year'];
    $file_type = $_POST['file_type'];
    $file_name = $_POST['file_name'];
    $file_path = '../../uploads/' . $_FILES['file']['name']; // Store file path
    
    // Upload the file to the server
    if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
        // Prepare the SQL query to insert the data into the database
        $sql = "INSERT INTO dept_files (username, dept, academic_year, file_type, sub_file_type, file_name, file_path) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $username, $dept, $acd_year, $event, $file_type, $file_name, $file_path);
        
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
                <span id="sp">&nbsp; >> &nbsp;  </span><span class="sid"><a href="acd_year.php?dept=<?php echo"$dept" ?>" class="home-icon"> Faculty </a></span>
                <span id="sp">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a"> <?php echo"$event" ?>_Files </a></span>
                <span id="sp">&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>
<div class="cont1">
    <div class="container11">
        <h1>Upload <?php echo ucfirst($event); ?> Files</h1>
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
