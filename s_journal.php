<?php
    include 'connection.php'; // Include database connection
session_start();
if (!isset($_SESSION['username'])) {
    die("You need to log in to view your uploads.");
}

$username = $_SESSION['username'];
if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}

if (isset($_GET['activity'])) {
    $activity = urldecode($_GET['activity']); // Decode the activity
} else {
    $activity = 'Unknown'; // Fallback if no activity is provided
}



    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get session username
        

        $uploaded_by = $_POST['uploaded_by'];
        $year =  $_POST['year'];
        $paper_title = $_POST['paper_title'];
        $journal_name = $_POST['journal_name'];
        $indexing = $_POST['indexing'];
        $date_of_submission = $_POST['date_of_submission'];
        $quality_factor = $_POST['quality_factor'];
        $impact_factor = $_POST['impact_factor'];
        $payment = $_POST['payment'];
        $Branch = $_POST['Branch'];

        // Handle file upload
        $file_name = $_FILES['paper_file']['name'];
        $file_tmp_name = $_FILES['paper_file']['tmp_name'];
        $file_size = $_FILES['paper_file']['size'];
        $file_error = $_FILES['paper_file']['error'];

        if ($file_error === 0) {
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_extensions = ['pdf', 'doc', 'docx'];

            if (in_array(strtolower($file_extension), $allowed_extensions)) {
                $file_new_name = uniqid('', true) . "." . $file_extension;
                $file_destination = 'uploads/' . $file_new_name;
                
                if (move_uploaded_file($file_tmp_name, $file_destination)) {
                    date_default_timezone_set('Asia/Kolkata');

                    $submission_time = date('Y-m-d H:i:s');
                    // SQL query to insert data into published_tab table
                    $sql = "INSERT INTO s_journal_tab (Username,uploaded_by, branch, acd_year, paper_title, journal_name, indexing, date_of_submission, quality_factor, impact_factor, payment, submission_time, paper_file)
                            VALUES ('$username' ,'$uploaded_by','$Branch','$year', '$paper_title', '$journal_name', '$indexing', '$date_of_submission', '$quality_factor', '$impact_factor', '$payment', '$submission_time', '$file_destination')";

                    if ($conn->query($sql) === TRUE) {
                        echo "<script>alert('Details and paper uploaded successfully');</script>";
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }
                } else {
                    echo "<script>alert('There was an error uploading your file.');</script>";
                }
            } else {
                echo "<script>alert('Invalid file type. Only PDF, DOC, DOCX files are allowed.');</script>";
            }
        } else {
            echo "<script>alert('There was an error with the file upload.');</script>";
        }
    }
    include 'header.php';
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papers Published</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #0a192f 0%, #172a45 100%);
            background-size: cover;
            background-position: center;
            justify-content: center;
            height: 100%;
            margin: 0;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 178%;
            background: rgba(0, 0, 0, 0.5); /* Adjust overlay opacity */
            z-index: -1;
        }

        .container {
            margin-top: 30px;

            margin-bottom: 50px;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.2);
            width: 600px;
            max-width: 100%;
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

    .nav-container {
        background-color: white;
        width:150vw;
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

        h1 {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #84fab0;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 16px;
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 0.2px solid rgb(165, 225, 239);
            background-color: #1c1c1c;
            color: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #84fab0;
        }

        .btn1 {
            padding: 15px;
            font-size: 18px;
            background-color: #84fab0;
            color: black;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn1:hover {
            background-color: #4ca1af;
        }

        .btn1:active {
            transform: scale(0.98);
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 12px 20px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #c0392b;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                width: 90%;
            }
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
<nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="acd_year.php?dept=<?php echo "$dept" ?>" class="home-icon"> Faculty </a></span>
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="student_act.php?event=student_act&dept=<?php echo $dept ?>" class="home-icon"> student_activities </a></span>
                <span>&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a"> Journal_papers </a></span>
                <span>&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>
<div class="cont1">
    <div class="container">
        <div class="contact-wrapper">
        <div class="contact-form">
            <h1>Enter Journal Paper Details</h1>
            <form action="" method="POST" id="contactForm" enctype="multipart/form-data">
            <div class="form-group">
                    <label for="paper_title">Uploaded By</label>
                    <input type="text" id="uploaded_by" name="uploaded_by" required>
                </div>
                <div class="form-group">
                        <label for="academic-year">Select Academic Year:</label>
                        <select name="year" id="academic-year" required>
                            <option value="" disabled selected>Select an academic year</option>
                            <?php
                            include("connection.php"); // Must be before this code

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
                    <label for="indexing">Branch:</label>
                    <select id="indexing" name="Branch" required>
                        <option value="" selected disabled>Choose one</option>
                        <option value="CSE">CSE</option>
                        <option value="AIML">AIML</option>
                        <option value="AIDS">AIDS</option>
                        <option value="IT">IT</option>
                        <option value="ECE">ECE</option>
                        <option value="EEE">EEE</option>
                        <option value="MECH">MECH</option>
                        <option value="CIVIL">CIVIL</option>
                        <option value="BSH">BSH</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="paper_title">Paper Title</label>
                    <input type="text" id="paper_title" name="paper_title" required>
                </div>
                <div class="form-group">
                    <label for="journal_name">Journal Name</label>
                    <input type="text" id="journal_name" name="journal_name" required>
                </div>
                <div class="form-group">
                    <label for="indexing">Indexing</label>
                    <select id="indexing" name="indexing" required>
                        <option value="scopus">Scopus</option>
                        <option value="sci">SCI</option>
                        <option value="scie">SCIE</option>
                        <option value="ugc_care">UGC Care</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date_of_submission">Date of Submission</label>
                    <input type="date" id="date_of_submission" name="date_of_submission" required>
                </div>
                <div class="form-group">
                    <label for="quality_factor">Quality Factor</label>
                    <input type="number" id="quality_factor" name="quality_factor" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="impact_factor">Impact Factor</label>
                    <input type="number" id="impact_factor" name="impact_factor" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="payment">Payment</label>
                    <select id="payment" name="payment" required>
                        <option value="free">Free</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="paper_file">Upload Paper</label>
                    <input type="file" id="paper_file" name="paper_file" accept=".pdf, .doc, .docx" required>
                </div>
                <button type="submit" class="btn1 btn-outline">Submit</button>
            </form>
        </div>
        </div>
    </div>
</body>
</html>
