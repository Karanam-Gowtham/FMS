<?php
include "header_hod.php";
include "connection.php";


$event = isset($_GET['event']) ? htmlspecialchars($_GET['event']) : '';
$designation = isset($_GET['designation']) ? htmlspecialchars($_GET['designation']) : '';
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
                background-image: url('../stuff/gmr_landing_page.jpg');
                background-size: cover;
                background-position: center;
                color: #ffffff;
                justify-content: center;
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
            .cont11{
                display: flex;
                justify-content: center;
            }

            .container {
                margin-top:100px;
                margin-bottom:150px;
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

            .btn11 {
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

            .btn11:hover {
                background-color: #3B4FE0;
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
            .btn1{
                position: absolute;
                top:0px;
                left: 500px;
                padding: 10px 20px;
                font-size: 1rem;
                font-weight: 600;
                color: #ffffff;
                background-color:rgb(32, 112, 12);
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: all 0.3s ease-in-out;
                width: 250px;
            }
            .btn1:hover{
                background-color:rgb(141, 242, 110);
                transform: translateY(-2px);
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
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
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a"><?php echo htmlspecialchars($designation); ?>  </a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>
    <div class="cont11">
    <div class="container">
    <button class="btn1" onclick="window.location.href='Add_academic_year.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>'">
    Add Academic Year
</button>
        <h1>AQARs Supporting Documents</h1>
        <form action="admin_criteria.php?event=<?php echo urlencode($event); ?>&designation=<?php echo urlencode($designation); ?>" method="POST">
            <label for="academic-year" id="acd">Select Academic Year:</label>
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
                <br><br>
                <label for="criteria" id="crit">Select criteria:</label>
            <select name="criteria" id="criteria" required>
                <option value="" disabled selected>Select a criteria</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="8">8</option>
            </select>
            <br><br>
            <button type="submit" class="btn11">Enter</button>
        </form>
    </div>
    </div>

</body>
</html>
