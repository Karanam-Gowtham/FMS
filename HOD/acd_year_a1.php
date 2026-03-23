<?php
require_once '../includes/session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AQARs Supporting Documents</title>
    <style>
        body {
            background-image: url('../assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            background-color: white;
            display: flex;
            justify-content: center;
            height: 100vh;
            margin-top: 0px;
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

        .container11 {
            margin-top: 150px;
            text-align: center;
            background-color: rgb(217, 242, 247,0.6); /* White with 50% transparency */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px black;
            width: 700px;
            height: 400px;
        }

        h1 {
            color: #333;
        }
        select {
            padding: 10px;
            font-size: 16px;
            margin-left: 20px;
        }
        button {
            
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #0056b3;
            border-radius: 5px;
            color:white;
        }
        label{
            font-size: 20px;
            font-weight: bold;
        }
        #academic-year{
            margin-top: 60px;
        }
        #acd{
            margin-top: 60px;
        }
        #criteria{
            margin-left: 28px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .home-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .home-button:hover {
            background-color: rgb(86, 5, 5);
        }
        .home-button1 {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .home-button1:hover {
            background-color: blueviolet;
        }
        #btn1:hover{
            background-color:aqua;
            color: black;
        }
    </style>
</head>
<body>
<?php include_once 'header_hod.php'; ?>
    <div class="container11">
        <h1>AQARs Supporting Documents</h1>
        <form action="files_cor.php" method="POST">
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
            <button type="submit" id='btn1'>Enter</button>
        </form>
    </div>

</body>
</html>

