<?php
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activities Navigation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            background-image: url('./stuff/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6); /* Increased opacity for better text contrast */
            z-index: -1;
        }

        .container11 {
            text-align: center;
            background-color: rgba(0, 0, 0, 0.6); /* Slightly darker for better readability */
            padding: 30px; /* Increased padding for better spacing */
            border-radius: 15px; /* Slightly more rounded corners */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.6); /* Softer shadow */
            max-width: 80%; /* Responsive design for smaller screens */
            width: 600px;
            margin-top:200px;
            color: white; /* Changed text color for contrast */
        }

        h1 {
            color: rgb(56, 197, 236);
            margin-bottom: 30px; /* Reduced margin for compactness */
            font-size: 2rem; /* Adjusted size for consistency across screens */
        }

        .button-container {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        a {
            text-decoration: none;
        }

        button {
            padding: 12px 25px; /* Reduced padding for better alignment */
            font-size: 1rem;
            font-weight: bold;
            color: white;
            background-color: #2e7d32;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #1b5e20;
        }

        button:active {
            transform: scale(0.95); /* Slightly smaller scale for better feedback */
        }

    </style>
</head>
<body>
    <div class="container11">
    <h1>Welcome to the Activities Page</h1>
    <div class="button-container">
        <a href="./admin/admins.php"><button>Faculty Activities</button></a>
        <a href="student_act.php"><button>Student Activities</button></a>
    </div>
    </div>
</body>
</html>
