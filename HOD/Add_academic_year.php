<?php
// Include database connection
include_once '../includes/connection.php';
include_once "./header_hod.php";

$success = "";
$error = "";
$event = isset($_GET['event']) ? htmlspecialchars($_GET['event']) : '';
$designation = isset($_GET['designation']) ? htmlspecialchars($_GET['designation']) : '';
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $year = trim($_POST["year"]);

    if (!empty($year)) {
        $check = "SELECT * FROM academic_year WHERE year = ?";
        $stmt_check = mysqli_prepare($conn, $check);
        mysqli_stmt_bind_param($stmt_check, "s", $year);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error = "Academic year '" . htmlspecialchars($year) . "' already exists.";
        } else {
            $insert = "INSERT INTO academic_year (year) VALUES (?)";
            $stmt_insert = mysqli_prepare($conn, $insert);
            mysqli_stmt_bind_param($stmt_insert, "s", $year);
            if (mysqli_stmt_execute($stmt_insert)) {
                $success = "Academic year '" . htmlspecialchars($year) . "' added successfully!";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt_insert);
        }
        mysqli_stmt_close($stmt_check);
    } else {
        $error = "Please enter an academic year.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Academic Year</title>
    <style>
        /* Make header sticky */
        header {
            position: sticky;
            top: 0;
        }

        /* Full page layout */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #74ebd5, #9face6);
        }

        body {
            display: flex;
            flex-direction: column;
        }
        .btn11 {
            position: absolute;
            top: 350px;  /* adjust as needed */
            right: 450px; /* adjust as needed */
            background-color:rgb(191, 82, 218);
        }
        .btn11:hover{
            background-color: rgb(190, 20, 176);
        }
        /* Wrapper for main content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center; /* space for sticky header */
        }

        .container11 {
            margin-top: -80px;
            width: 100%;
            max-width: 600px;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 16px;
        }

        button {
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .success, .error {
            text-align: center;
            padding: 10px;
            margin-bottom: 15px;
            font-weight: bold;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        /* Navigation */
    .navbar { 
        font-size: larger;
    }

    .nav-container {
        background-color: white;
        width:150vw;
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
    </style>
</head>
<body>
<nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="acd_year_aa.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon"><?php echo htmlspecialchars($designation); ?></a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a">Add Academic Year</a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>

<div class="main-content">
<button class="btn11" onclick="location.href='edit_acd_year.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>'">
        Edit the Academic Year
    </button>
    <div class="container11">
        <h2>Add Academic Year</h2>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
            <script>alert("<?php echo $success; ?>");</script>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
            <script>alert("<?php echo $error; ?>");</script>
        <?php endif; ?>

        <form method="POST">
            <label for="year">Enter Academic Year (e.g., 2024-25):</label>
            <input type="text" id="year" name="year" placeholder="YYYY-YY" required>
            <button type="submit">Add Academic Year</button>
        </form>
    </div>
</div>

</body>
</html>

