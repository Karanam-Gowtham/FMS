<?php
require_once '../includes/session.php';
include "../includes/connection.php";

if (!isset($_SESSION['admin'])) {
    die("Unauthorized access. Please log in as Admin.");
}

$designation = "admin";
$event = "aa";

include 'header_hod.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criteria Management - Admin</title>
    <style>
        body {
            background-image: url('../assets/img/gmr_landing_page.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .container11 {
            margin-top: 150px;
            text-align: center;
            background-color: rgba(217, 242, 247, 0.85);
            padding: 30px;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            width: 90%;
            max-width: 600px;
            height: auto;
        }

        h1 {
            color: #1e3a8a;
            margin-bottom: 2rem;
            font-weight: 700;
        }

        label {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-align: left;
            color: #374151;
        }

        select {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            margin-bottom: 1.5rem;
            background: white;
        }

        button {
            width: 100%;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            background-color: #2563eb;
            border: none;
            border-radius: 0.5rem;
            color: white;
            transition: background 0.2s;
        }

        button:hover {
            background-color: #1d4ed8;
        }

        .navbar {
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 80px;
            border-bottom: 1px solid #eee;

            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        /* Navigation styles to match */
        .nav-container {
            background-color: white;
            padding: 0.5rem 2rem;
             /* margin-top moved to .navbar */
        }

        .nav-items {
            display: flex;
            align-items: center;
            height: 3rem;
        }

        .sid { color: #1e40af; font-weight: 500; }
        .main-a { color: #86198f; font-weight: 600; }
        .home-icon { color: #1e40af; }

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
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="acd_year_aa.php" class="home-icon">Admin</a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a">Criteria Select</a></span>
            </div>
        </div>
    </nav>

    <div class="container11">
        <h1>AQAR Criteria Selection</h1>
        <form action="admin_criteria.php" method="POST">
            <input type="hidden" name="designation" value="admin">
            <input type="hidden" name="event" value="aa">

            <label for="year">Select Academic Year:</label>
            <select name="year" id="year" required>
                <option value="" disabled selected>-- Choose Year --</option>
                <?php
                $res = mysqli_query($conn, "SELECT year FROM academic_year ORDER BY year DESC");
                while($row = mysqli_fetch_assoc($res)) {
                    echo "<option value='".$row['year']."'>".$row['year']."</option>";
                }
                ?>
            </select>

            <label for="criteria">Select Criteria:</label>
            <select name="criteria" id="criteria" required>
                <option value="" disabled selected>-- Choose Criteria --</option>
                <?php for($i=1; $i<=7; $i++) { echo "<option value='$i'>Criteria $i</option>"; } ?>
            </select>

            <button type="submit">Manage Criteria</button>
        </form>
    </div>
</body>
</html>
