
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>See the Uploaded Files</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('../assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            justify-content: center;
            align-items: center;
            margin-top: 0px;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 120vh;
            background: rgba(0, 0, 0, 0.5); /* Adjust the opacity as needed */
            z-index: -1;
        }
        .con{
            margin-top:50px;
        }
        h1 {
            color: #fff;
            font-size: 3rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
            text-align: center;
            padding-top:150px;
            color:greenyellow;
        }

        .button-container {
            justify-content: center;
            gap: 30px;
            margin-left: 400px;
            width: 700px;
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(8.5px);
            -webkit-backdrop-filter: blur(8.5px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .btn1 {
            background: linear-gradient(45deg, #ff6f61, #de1b6a);
            color: white;
            font-size: 1.2rem;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom:30px;
        }

        .btn1:hover {
            transform: translateY(-5px);
            background: linear-gradient(45deg, #ff7f50, #e3497b);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .btn1:focus {
            outline: none;
        }
        .b1{
            margin:20px;
        }
    </style>
</head>
<?php
    include 'header_hod.php';
?>
<body>
    <div class="container111">
        
    <h1>See the Uploaded Files</h1>
        <div class="con">
        <div class="button-container">
            <button class='btn1' onclick="location.href='acd_year_a.php'">Faculty uploaded files</button><br>
            <button class='btn1' onclick="location.href='acd_year_a1.php'">Dept co-ordinates uploaded files</button>
        </div>
        </div>
    </div>
</body>
</html>
