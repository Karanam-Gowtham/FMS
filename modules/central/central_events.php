<?php
    include '../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Events</title>
    <style>
        body {
            background-image: url('../../assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            color: #fff;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 110vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .container {
            margin-top: 150px;
            text-align: center;
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6);
            margin-bottom: 50px;
        }

        h1 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 2.5em;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .button-container {
            display: grid;
            gap: 30px;
        }

        .button {
            background: linear-gradient(to right, #4facfe, #00f2fe);
            border: none;
            padding: 10px 15px;
            font-size: 1em;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            text-decoration: none;
            color: blue;
            text-align: center;
            font-weight: bold;
        }

        .button:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
        }

        .button:active {
            transform: translateY(0);
        }
        .btn_u {
            background: linear-gradient(to right,rgb(254, 79, 239),rgb(76, 3, 72));
            position:absolute;
            top: 150px;
            right:300px;
            border: none;
            padding: 10px 15px;
            font-size: 1em;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            text-decoration: none;
            color: white;
            text-align: center;
        }

        .btn_u:hover {
            background: linear-gradient(to right,rgb(76, 3, 72)),rgb(254, 79, 239);
            transform: translateY(-3px);
        }

        .btn_u:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="c_down_files.php" class="upload-btn"><button class="btn_u">Uploads</button></a>
        <h1>Central Events</h1>
        <div class="button-container">
            <a href="c_login.php?event=NCC" class="button">NCC</a>
            <a href="c_login.php?event=Sports" class="button">Sports</a>
            <a href="c_login.php?event=NSS" class="button">NSS</a>
            <a href="c_login.php?event=Women Empowerment" class="button">Women Empowerment</a>
            <a href="c_login.php?event=IIC" class="button">IIC</a>
            <a href="c_login.php?event=PASH" class="button">PASH</a>
            <a href="c_login.php?event=Antiragging" class="button">Antiragging</a>
            <a href="c_login.php?event=SAC" class="button">SAC</a>
        </div>
    </div>
</body>
</html>
