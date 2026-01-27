<?php
    include 'header_admin.php';
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
                background-image: url('../assets/img/gmr_landing_page.jpg');
                background-size: cover;
                background-position: center;
                color: #ffffff;
                display: flex;
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

            .container {
                margin-top:200px;
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

            .btn1 {
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

            .btn1:hover {
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
</style>

</head>
<body>
    <?php
         $designation = isset($_GET['designation']) ? htmlspecialchars($_GET['designation']) : 'Unknown';
         ?>
    <div class="container">
        <h1>AQARs Supporting Documents</h1>
        <form action="criteria_a.php" method="POST">
            <input name="designation" hidden > <?php echo"welcome $designation" ?> </input>
            <label for="academic-year" id="acd">Select Academic Year:</label>
            <select name="year" id="academic-year" required>
                <option value="" disabled selected>Select an academic year</option>
                <option value="2022-23">2022-23</option>
                <option value="2021-22">2021-22</option>
                <option value="2020-21">2020-21</option>
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
                <option value="7">7</option>
            </select>
            <br><br>
            <button type="submit" class="btn1">Enter</button>
        </form>
    </div>

</body>
</html>
