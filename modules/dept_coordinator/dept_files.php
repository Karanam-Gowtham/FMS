<?php
// Start session
session_start();

// Check if the user is logged in and retrieve username
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $role = 'faculty';
} elseif (isset($_SESSION['h_username'])) {
    $username = $_SESSION['h_username'];
    $role = 'faculty';
} elseif (isset($_SESSION['j_username'])) {
    $username = $_SESSION['j_username'];
    $role = 'jr_assistant';
} else {
    die("Unauthorized access. Please log in first.");
}

if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}

// Restrict Jr Assistant from accessing Admin Files (Removed as per request)
$event = $_GET['event'] ?? '';

// Connect to the database
include("../../includes/connection.php");
include("../../includes/header.php");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the department based on role
if ($role === 'faculty') {
    $sql_dept = "SELECT dept FROM reg_tab WHERE userid = ?";
    $stmt_dept = $conn->prepare($sql_dept);
    $stmt_dept->bind_param("s", $username);
    $stmt_dept->execute();
    $result_dept = $stmt_dept->get_result();
    if ($row = $result_dept->fetch_assoc()) {
        $rdept = $row['dept'];
    }
} else {
    $sql_dept = "SELECT department FROM reg_jr_assistant WHERE userid = ?";
    $stmt_dept = $conn->prepare($sql_dept);
    $stmt_dept->bind_param("s", $username);
    $stmt_dept->execute();
    $result_dept = $stmt_dept->get_result();
    if ($row = $result_dept->fetch_assoc()) {
        $rdept = $row['department'];
    }
}

if (!isset($rdept)) {
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
        if ($role === 'jr_assistant') {
            $file_options = [
                'List of Forms',
                'Result Analysis',
                'Faculty\'s Feedback by Students',
                'Students Progression Report'
            ];
        } else {
            $file_options = [
                'Course Structure',
                'Course Schedule',
                'Feedback from Parents',
                'Employer Feedback',
                'Department Area Details',
                'Departmental Laboratory Details',
                'Major Equipment in the Laboratories',
                'List of Experiments',
                'Major Equipment Utilization Record',
                'Equipment Maintenance Record',
                'Courses Linked with Employability',
                'Financial Statement/Budget Status',
                'Departmental Library Details',
                'Seminars/Workshops/Conferences Organized',
                'Industrial Visits',
                'Guest Lectures',
                'List of Projects',
                'Add-on Course/Training Conducted',
                'Consultancy-New',
                'External Sports and Projects',
                'Transferrable and Life Skills Courses',
                'Remedial Classes',
                'Course End Feedback Form',
                'Class Time Table',
                'Faculty Time Table',
                'Classroom Time Table',
                'Lab Time Table',
                'Feedback from Students/Alumni/Academic Peer',
                'Course File-Index',
                'Feedback on Curriculum from Students/Employer/Alumni',
                'Workshops-Seminars on Research Methodology',
                'Intellectual Property Rights (IPR)',
                'Entrepreneurship-New',
                'Professional Societies Chapters',
                'Engineering Events Organized',
                'Product Development Activities',
                'Collaborative Activities',
                'Functional MoUs with Ongoing Activities',
                'Mini Project Work',
                'Term Paper Work',
                'Mentoring',
                'Remedial Classes(Schedules/list of students/Attendance/Assignments given/Tests Conducted)',
                'Slow Learners(Schedules/list of students/Attendance/Assignments given/Tests Conducted)',
                'Make Up Classes(Schedules/list of students/Attendance/Assignments given/Tests Conducted)'
            ];
        }
        break;
    case 'faculty':
        $file_options = [
            'Faculty List',
            'Faculty Profile',
            'Academic Research',
            'Books and Chapters Published',
            'Faculty in Inter-Departmental/Institutional Activities',
            'Faculty for Higher Studies',
            'Faculty Attended Seminars/Internships',
            'Faculty Self-Appraisal',
            'Non-Teaching Staff Skill Upgradation',
            'Observations on Student Feedback',
            'Full-Time Teachers with PhD Guidance',
            'Consultancy and Corporate Training',
            'Financial Support to Faculty',
            'Publication of Technical Magazines/Newsletters'
        ];
        break;
    case 'student':
        if ($role === 'jr_assistant') {
            $file_options = [
                'Student Addresses',
                'Cumulative Monthly Attendance',
                'Semester End Attendance',
                'Condonation List',
                'Detention List'
            ];
        } else {
            $file_options = [
                'Papers Published by Students',
                'Students in Competitive Exams',
                'Co-Curricular/Extra-Curricular Activities',
                'Placement Record',
                'Alumni Interaction',
                'Field Projects/Internships',
                'List of Seminars/Workshops Attended',
                'Online Courses Completed',
                'Coding/Hardware Competitions',
                'Capacity Development Activities',
                'Guidance for Competitive Exams',
                'Career Counselling'
            ];
        }
        break;
    case 'exam':
        $file_options = [
            'Notice for Internal Lab Exams',
            'Invigilation Schedule',
            'Absentee Statement',
            'Sessional Marks Record',
            'Final Sessional Marks'
        ];
        break;
    case 'calendar':
        $file_options = [
            'Department Academic Calendar',
            'Follow up of planned activities & with reasons for non conducted activities if any'
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
    $study_year = $_POST['study_year'] ?? NULL;
    $semester = $_POST['semester'] ?? NULL;
    $review_period = $_POST['review_period'] ?? NULL;
    $file_path = '../../uploads/' . $_FILES['file']['name']; // Store file path

    // Upload the file to the server
    if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
        // Prepare the SQL query to insert the data into the database
        // Final authority is HOD, so status starts as 'Pending HOD'
        $sql = "INSERT INTO dept_files (username, dept, academic_year, study_year, semester, review_period, file_type, sub_file_type, file_name, file_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending HOD')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiisssss", $username, $dept, $acd_year, $study_year, $semester, $review_period, $event, $file_type, $file_name, $file_path);

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
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        label {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #fff;
            font-weight: bold;
        }

        input[type="text"],
        input[type="file"],
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #ff6347;
            outline: none;
            box-shadow: 0 0 10px rgba(255, 99, 71, 0.2);
        }

        option {
            background-color: #172a45;
            color: white;
            padding: 10px;
        }

        .infotext {
            font-size: 0.85rem;
            font-weight: normal;
            color: #ccc;
            display: block;
            margin-top: 4px;
            line-height: 1.4;
        }

        .button {
            background: #ff6347;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        .button:hover {
            background: #e55337;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(229, 83, 55, 0.4);
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
<div>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span id="sp">&nbsp; >> &nbsp; </span><span class="sid"><a
                        href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>"
                        class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <?php if ($role === 'faculty'): ?>
                    <span id="sp">&nbsp; >> &nbsp; </span><span class="sid"><a
                            href="../faculty/acd_year.php?dept=<?php echo "$dept" ?>" class="home-icon"> Faculty </a></span>
                <?php else: ?>
                    <span id="sp">&nbsp; >> &nbsp; </span><span class="sid"><a
                            href="../jr_assistant/jr_acd_year.php?dept=<?php echo "$dept" ?>" class="home-icon"> Jr
                            Assistant
                        </a></span>
                <?php endif; ?>
                <?php
                $display_event = $event;
                if ($event === 'calendar')
                    $display_event = 'Academic Calendar';
                else
                    $display_event = ucfirst($event) . ' Files';
                ?>
                <span id="sp">&nbsp; >> &nbsp; </span><span class="main"> <a href="#" class="main-a">
                        <?php echo $display_event; ?> </a></span>
                <span id="sp">&nbsp; >> &nbsp; </span>
            </div>
        </div>
    </nav>
    <div class="cont1">
        <div class="container11">
            <h1>Upload <?php echo $display_event; ?></h1>
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

                <?php if ($event === 'student' || ($role === 'jr_assistant' && $event === 'admin')): ?>
                    <label for="study_year">Select Year:</label>
                    <select name="study_year" id="study_year" required>
                        <option value="" disabled selected>Select Year</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>

                    <label for="semester">Select Semester:</label>
                    <select name="semester" id="semester" required>
                        <option value="" disabled selected>Select Semester</option>
                        <?php
                        for ($i = 1; $i <= 8; $i++) {
                            echo "<option value='$i'>$i</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php if ($role === 'jr_assistant' && $event === 'admin'): ?>
                    <label for="review_period">Select Review Period:</label>
                    <select name="review_period" id="review_period">
                        <option value="" selected>None/Not Applicable</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                    </select>
                <?php endif; ?>


                <label for="file_type">Select File Category:</label>
                <select name="file_type" id="file_type" required>
                    <option value="" disabled selected>Select File Category</option>
                    <?php
                    foreach ($file_options as $option) {
                        $clean_option = htmlspecialchars($option);
                        echo "<option value=\"$clean_option\">$clean_option</option>";
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