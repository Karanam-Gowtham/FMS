<?php
// Start session
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include("../../includes/connection.php");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Check if the user is logged in and retrieve username
if (!isset($_SESSION['a_username'])) {
    die("Unauthorized access. Please log in first.");
}
$username = $_SESSION['a_username'];
if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}

// Connect to the database

include("../../includes/header.php");




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
            'FDPS Attended', 'Papers Published', 'Patents', 'FDPS Organized', 'Conference'
        ];
        break;
    case 'dept':
        $file_options = [
            'Admin Files', 'Student Files', 'Faculty Files', 'Exam Section Files'
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
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $file_path = $upload_dir . $_FILES['file']['name'];

    if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
        $sql = "INSERT INTO dc_up_files (Username,file_name, acd_year,Main_file_type, file_type, file_path) VALUES (?, ?, ?, ?, ?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss",$username, $file_name, $acd_year,$event, $file_type, $file_path);

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
<body>
    <?php include "../../includes/header.php"; ?>


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
