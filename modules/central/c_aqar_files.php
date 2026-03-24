<?php
ob_start(); // Start output buffering
include_once '../../includes/connection.php';

// Retrieve designation from URL or POST
$event = isset($_REQUEST['event']) ? htmlspecialchars($_REQUEST['event']) : '';
$designation = isset($_REQUEST['designation']) ? htmlspecialchars($_REQUEST['designation']) : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $year = isset($_POST['year']) ? $_POST['year'] : '';
    $criteria = isset($_POST['criteria']) ? $_POST['criteria'] : '';

    // Clean buffer before redirect
    ob_end_clean();

    // Redirect based on designation
    switch ($designation) {
        case 'faculty':
            header("Location: ../faculty/criteria.php?year=$year&criteria=$criteria&designation=$designation&event=$event");
            exit();
        case 'dept_coordinator':
            header("Location: ../../admin/criteria_a.php?year=$year&criteria=$criteria&designation=$designation&event=$event");
            exit();
        
        case 'central_coordinator':
            header("Location: ../../admin/criteria_cent_a.php?year=$year&criteria=$criteria&designation=$designation&event=$event");
            exit();
        case 'criteria_coordinator':
            header("Location: ../../admin/criteria_cri_a.php?year=$year&criteria=$criteria&designation=$designation&event=$event");
            exit();
        case 'hod':
            header("Location: ../../HOD/hod_faculty_files.php?year=$year&criteria=$criteria&designation=$designation&event=$event");
            exit();
        case 'admin':
            header("Location: ../../HOD/acd_year_aa.php?year=$year&criteria=$criteria&designation=$designation&event=$event");
            exit();
        default:
            // If designation is invalid, we might want to show an alert, but since we are before HTML, 
            // we can trigger a script via echo, but we cleaned the buffer.
            // Be careful not to break HTML structure later.
            // Better to handle default case by falling through to page render with an error message variable.
            echo "<script>alert('Invalid Designation!');</script>";
            // Re-start buffering since we continued
            ob_start();
    }
}

include_once '../../includes/header.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AQARs Supporting Documents</title>
    <style>
            body {
                font-family: 'Poppins', sans-serif;
                background-image: url('../../assets/img/gmr_landing_page.jpg');
                background-size: cover;
                background-position: center;
                color: #ffffff;
                height: 100vh;
                margin: 0;
                padding: 0;
                overflow: hidden;
            }
            
            body::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5); /* Adjust the opacity as needed */
                z-index: -1;
            }
            .container{
                margin-left:500px;
            }

            .container {
                margin-top:100px;
                margin-bottom:150px;
                left:200px;
                background: rgba(0, 0, 0, 0.7);
                box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(10px);
                padding: 2rem;
                border-radius: 1rem;
                text-align: center;
                width: 400px;
                animation: fadeIn 1s ease-in-out;
            }
            

            h1 {
                font-size: 1.8rem;
                font-weight: 600;
                margin-bottom: 1rem;
                color: #ffffff;
                text-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3);
            }

            label {
                display: block;
                margin-bottom: 0.5rem;
                font-size: 1.2rem;
                font-weight: 500;
                text-align: left;
                color: #ffffff;
            }

            select {
                width: 100%;
                padding: 10px;
                font-size: 1rem;
                border-radius: 5px;
                border: none;
                background-color: #ffffff;
                color: #333;
                outline: none;
                margin-bottom: 1.5rem;
                cursor: pointer;
                box-shadow: inset 0px 2px 5px rgba(0, 0, 0, 0.1);
            }

            .button1 {
                padding: 10px 20px;
                font-size: 1rem;
                font-weight: 600;
                color: #ffffff;
                background-color: #5C67F2;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: all 0.3s ease-in-out;
                width: 100%;
            }

            .button1:hover {
                background-color: #3B4FE0;
                transform: translateY(-2px);
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            }
            .button12 {
                position: relative;
                top:150px;
                left:50px;
                padding: 10px 20px;
                font-size: 1rem;
                font-weight: 600;
                color: #ffffff;
                background-color:rgb(22, 146, 34);
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: all 0.3s ease-in-out;
                width: 100%;
            }

            .button12:hover {
                background-color:rgb(5, 95, 19);
                transform: translateY(-2px);
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            }

            .home-button {
                position: absolute;
                top: 20px;
                right: 20px;
                padding: 10px 20px;
                font-size: 1rem;
                font-weight: 600;
                background-color: #FF5252;
                color: #ffffff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: all 0.3s ease-in-out;
            }

            .home-button:hover {
                background-color: #E43B3B;
                transform: translateY(-2px);
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: scale(0.9);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
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

    .nav-container {
        background-color: white;
        width:150vw;
         /* margin-top moved to .navbar */
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
                <a href="../../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="c_login_n.php?event=<?php echo urlencode($event); ?>" class="home-icon">Central (<?php echo htmlspecialchars($event); ?>)</a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a"><?php echo htmlspecialchars($designation); ?>  </a></span>

            </div>
        </div>
    </nav>
        <div class="container">
            <div class="contact-wrapper">
                <!-- AQARs Supporting Documents Section -->
                <div class="contact-form">
                    <h1>AQARs Supporting Documents</h1>
                    <p><strong>Logged in as:</strong> <?php echo ucfirst(str_replace("_", " ", $designation)); ?></p>

                    <form method="POST">
                        <div class="form-group">
                            <label for="academic-year">Select Academic Year:</label>
                            <select name="year" id="academic-year" required>
                                <option value="" disabled selected>Select an academic year</option>
                                <?php
                                $query = "SELECT year FROM academic_year ORDER BY year DESC";
                                $result = mysqli_query($conn, $query);

                                if ($result && mysqli_num_rows($result) > 0) {
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
                            <label for="criteria">Select Criteria:</label>
                            <select name="criteria" id="criteria" required>
                                <option value="" disabled selected>Select a criteria</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="8">7</option>
                            </select>
                        </div>
                        <button type="submit" class="button1">Enter</button>
                    </form>
                </div>
            </div>
            </div>
</body>
</html>
