<?php
// Start session
session_start();

// Check if the user is logged in (Jr Assistant)
if (!isset($_SESSION['j_username'])) {
    die("Unauthorized access. Only Jr Assistants can upload these files.");
}
$username = $_SESSION['j_username'];
if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}

// Connect to the database
include_once "../../includes/connection.php";
include_once "../../includes/header.php";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the department from reg_jr_assistant using username
$sql_dept = "SELECT department FROM reg_jr_assistant WHERE userid = ?";
$stmt_dept = $conn->prepare($sql_dept);
$stmt_dept->bind_param("s", $username);
$stmt_dept->execute();
$result_dept = $stmt_dept->get_result();

if ($result_dept->num_rows > 0) {
    $row = $result_dept->fetch_assoc();
    $rdept = $row['department']; // Store the retrieved department
} else {
    die("Department not found for the user.");
}

// Hardcoded event for this specific program
$event = 'Dept Meeting Minutes';

// File options specific to Dept Meeting Minutes
$file_options = [
    'Agenda',
    'Faculty Attended',
    'Meeting Minutes',
    'Followup'
];

// Retrieve the next meeting number
$sql_max = "SELECT MAX(meeting_no) as max_no FROM dept_files WHERE dept = ? AND file_type = 'Dept Meeting Minutes'";
$stmt_max = $conn->prepare($sql_max);
$stmt_max->bind_param("s", $dept);
$stmt_max->execute();
$res_max = $stmt_max->get_result();
$row_max = $res_max->fetch_assoc();
$next_meeting_no = ($row_max['max_no'] ?? 0) + 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acd_year = ""; // Removed from form
    $file_type = $event; // Automatically set category to event name
    $file_name = $_POST['file_name'];
    $student_year = 0; // Removed from form
    $semester = 0; // Removed from form
    $review_period = NULL; // Removed from form
    $meeting_no = $_POST['meeting_no'] ?? $next_meeting_no;
    $file_path = '../../uploads/' . $_FILES['file']['name']; // Store file path
    
    // Upload the file to the server
    if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
        // Prepare the SQL query to insert the data into the database
        $sql = "INSERT INTO dept_files (username, dept, academic_year, file_type, sub_file_type, file_name, file_path, semester, study_year, review_period, status, meeting_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending HOD', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssiisi", $username, $dept, $acd_year, $event, $file_type, $file_name, $file_path, $semester, $student_year, $review_period, $meeting_no);
        
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
    <title>Upload <?php echo htmlspecialchars($event); ?></title>
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
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 80px;
            border-bottom: 1px solid #eee;
 
        font-size: larger;
    }

    #sp{
        color:blue;
    }
    
    .nav-container {
        background-color: white;
        width:150vw;
         /* margin-top moved to .navbar */
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
        input[type="number"],
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
        input[type="number"]:focus,
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

        .infotext {
            font-size: 0.85rem;
            font-weight: normal;
            color: #ccc;
            display: block;
            margin-top: 4px;
            line-height: 1.4;
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
                <span class="sp">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span class="sp">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../jr_assistant/jr_acd_year.php?dept=<?php echo urlencode((string)$dept); ?>" class="home-icon"> Jr Assistant </a></span>
                <span class="sp">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a"> <?php echo htmlspecialchars($event); ?> </a></span>
                <span class="sp">&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>
<div class="cont1">
    <div class="container11">
        <h1>Upload <?php echo htmlspecialchars($event); ?></h1>
        <form action="" method="POST" enctype="multipart/form-data" class="upload-form">
            <label for="file_name">File Name:</label>
            <input type="text" name="file_name" id="file_name" required>

            <label for="meeting_no">Meeting No:</label>
            <input type="number" name="meeting_no" id="meeting_no" value="<?php echo $next_meeting_no; ?>" readonly required style="background: rgba(255, 255, 255, 0.1);">

                    
    

            <label for="file">
                Dept Meeting Minutes:
                <span class="infotext">(<?php echo implode(', ', $file_options); ?>)</span>
            </label>
            <input type="file" name="file" id="file" required>

            <button type="submit" class="button" name="submit">Upload File</button>
        </form>
    </div>
</div>
            
</body>
</html>
<?php
$conn->close();

