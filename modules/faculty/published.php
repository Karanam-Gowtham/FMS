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
    $paper_title = $_POST['paper_title'];
    $journal_name = $_POST['journal_name'];
    $indexing = $_POST['indexing'];
    $date_of_submission = $_POST['date_of_submission'];
    $quality_factor = $_POST['quality_factor'];
    $impact_factor = $_POST['impact_factor'];
    $payment = $_POST['payment'];
    $year = $_POST['year'];
    
    $target_dir = "../../uploads/published/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_name = time() . '_' . basename($_FILES["paper_file"]["name"]);
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($_FILES["paper_file"]["tmp_name"], $target_file)) {
        $status = 'Pending Dept Coordinator';
        $sql = "INSERT INTO published_tab (username, branch, paper_title, journal_name, indexing, date_of_submission, quality_factor, impact_factor, payment, paper_file, submission_time, year, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssss", $username, $dept, $paper_title, $journal_name, $indexing, $date_of_submission, $quality_factor, $impact_factor, $payment, $target_file, $year, $status);
        
        if ($stmt->execute()) {
            echo "<script>alert('Paper added successfully!'); window.location.href='acd_year.php?dept=" . urlencode($dept) . "';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error uploading file.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Published Paper</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #0a192f 0%, #172a45 100%);
            color: white;
            min-height: 100vh;
            margin: 0;
            padding-bottom: 50px;
        }
        .navbar { background-color: white; font-size: larger; }
        .nav-container { margin-top: 100px; margin-left:100px; max-width: 80rem; padding: 0 1rem; }
        .nav-items { display: flex; align-items: center; height: 4rem; }
        .sid { color: rgb(48, 30, 138); font-weight: 500; }
        .main-a { color: rgb(138, 30, 113); font-weight: 500; }
        #sp { color: blue; }
        .container11 {
            margin: 50px auto;
            background: rgba(16, 15, 15, 0.8);
            padding: 40px;
            border-radius: 15px;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.2);
        }
        h2 { text-align: center; margin-bottom: 20px; }
        input, select {
            width: 100%; padding: 10px; margin-bottom: 15px;
            border-radius: 5px; border: none;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        button {
            width: 100%; padding: 10px; background: #ff6347; color: white;
            border: none; border-radius: 5px; cursor: pointer;
        }
        button:hover { background: #e55337; }
        option { background-color: #333; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <div class="nav-items">
            <a href="../../index.php" class="home-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </a>
            <span id="sp">&nbsp; >> &nbsp;</span><span class="sid"><a href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
            <span id="sp">&nbsp; >> &nbsp;</span><span class="sid"><a href="acd_year.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Faculty</a></span>
            <span id="sp">&nbsp; >> &nbsp;</span><span class="main"><a href="#" class="main-a">Published Papers</a></span>
        </div>
    </div>
</nav>

<div class="container11">
    <h2>Upload Published Paper</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Paper Title:</label>
        <input type="text" name="paper_title" required>
        
        <label>Journal Name:</label>
        <input type="text" name="journal_name" required>
        
        <label>Indexing (SCI/SCOPUS/UGC):</label>
        <select name="indexing" required>
            <option value="">Select Indexing</option>
            <option value="SCI">SCI</option>
            <option value="SCOPUS">SCOPUS</option>
            <option value="UGC">UGC</option>
            <option value="Other">Other</option>
        </select>
        
        <label>Academic Year:</label>
        <select name="year" required>
            <option value="">Select Year</option>
            <?php
            $y_sql = "SELECT year FROM academic_year ORDER BY year DESC";
            $y_res = $conn->query($y_sql);
            if ($y_res) {
                while($y = $y_res->fetch_assoc()) {
                    echo "<option value='".$y['year']."'>".$y['year']."</option>";
                }
            }
            ?>
        </select>
        
        <label>Date of Submission:</label>
        <input type="date" name="date_of_submission" required>
        
        <label>Quality Factor:</label>
        <input type="text" name="quality_factor" placeholder="e.g. 1.5">
        
        <label>Impact Factor:</label>
        <input type="text" name="impact_factor" placeholder="e.g. 2.0">
        
        <label>Payment:</label>
        <select name="payment">
            <option value="No">No</option>
            <option value="Yes">Yes</option>
        </select>
        
        <label>Paper File:</label>
        <input type="file" name="paper_file" required>
        
        <button type="submit">Submit</button>
    </form>
</div>

</body>
</html>
<?php $conn->close(); ?>
