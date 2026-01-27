<?php 
       
        include "connection.php";
    
    include './header.php';
        ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journals and Conferences</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-image: url('./stuff/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
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
            margin-top:150px;
        }

        h1 {
            font-size: 3em;
            margin-bottom: 20px;
            text-align: center;
        }

        .button-container {
            display: flex;
            gap: 20px;
        }

        .button {
            margin-left: 100px;
            padding: 15px 30px;
            font-size: 1.2em;
            color: #fff;
            background: #007BFF;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s, background-color 0.3s;
        }

        .button:hover {
            background: #0056b3;
            transform: translateY(-4px);
        }

        .button:active {
            transform: translateY(2px);
        }
    </style>
</head>
<body>
    <div class="cont11">
    <h1>Explore Academic Resources</h1>
    <div class="button-container">
        <a href="s_journal.php" class="button">Journals</a>
        <a href="s_conference.php" class="button">Conferences</a>
    </div>
    </div>
</body>
</html>

