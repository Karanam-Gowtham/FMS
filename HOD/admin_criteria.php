<?php
include_once "../includes/connection.php";
include_once "./header_hod.php";

$event = isset($_GET['event']) ? htmlspecialchars($_GET['event']) : 'aa';
$designation = isset($_GET['designation']) ? htmlspecialchars($_GET['designation']) : '';

if (isset($_POST['criteria'])) {
    $criteria = htmlspecialchars($_POST['criteria']);
} else {
    $criteria = htmlspecialchars($_GET['criteria']);
}
if (isset($_POST['year'])) {
    $academicYear = htmlspecialchars($_POST['year']);
} else {
    $academicYear = htmlspecialchars($_GET['year']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AQAR Criteria Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(54, 180, 226);
            
            justify-content: center;
            margin: 0;
        }
        .cont11{
            display: flex;
            justify-content: center;
        }
        .container {
            text-align: center;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 80%;
            margin:50px 0px;
        }
        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        #tr2{
            background-color:rgb(3, 2, 71);
            color: white;
        }
        #th1 {
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f4f4f4;
        }
        .criteria-no {
            font-weight: bold;
            color: black;
        }
        .btn1 {
            background-color:red;
            color: white;
            border: none;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn1:hover {
            background-color: rgb(87, 7, 7);
        }
        .btn2 {
            background-color:blue;
            color: white;
            border: none;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn2:hover {
            background-color: rgb(58, 86, 187);
        }
        .home-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width:150px;
        }
        .home-button:hover {
            background-color: #c82333;
        }
        .home-button1 {
            position: absolute;
            top: 210px;
            right: 150px;
            padding: 10px 20px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width:150px;
        }
        .home-button1:hover {
            background-color: green;
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
                <a href="../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="acd_year_aa.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon"><?php echo htmlspecialchars($designation); ?></a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a">Criteria <?php echo"$criteria" ?>  </a></span>
                
            </div>
        </div>
    </nav>
    <div class="cont11">
<div class="container">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
        
        echo "<h1>AQAR - $academicYear </h1>";

        if (isset($_POST['delete'])) {
            $criteriaNo = htmlspecialchars($_POST['criteria_no']);

            if ($academicYear == '2020-21') {
                $sql = "DELETE FROM criteria1 WHERE SI_no=? AND Sub_no=?";
            } elseif ($academicYear == '2021-22') {
                $sql = "DELETE FROM criteria2 WHERE SI_no=? AND Sub_no=?";
            } else {
                $sql = "DELETE FROM criteria WHERE SI_no=? AND Sub_no=?";
            }

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $criteria, $criteriaNo);

            if ($stmt->execute()) {
                echo "<script>alert('Record deleted successfully.');</script>";
            } else {
                echo "<script>alert('Error deleting record: " . $conn->error . "');</script>";
            }
        }
    }
    ?>

    <table>
        <thead>
            <tr>
                <th colspan="5" id="th1">Criteria <?php echo $criteria; ?></th>
            </tr>
            <tr id="tr2">
                <th scope="col">Criteria No</th>
                <th scope="col">Description</th>
                <th scope="col">Action</th>
                <th scope="col">Update</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($academicYear == '2020-21') {
                $sql = "SELECT * FROM criteria1 WHERE SI_no='$criteria' ORDER BY Sub_no";
            } elseif ($academicYear == '2021-22') {
                $sql = "SELECT * FROM criteria2 WHERE SI_no='$criteria' ORDER BY Sub_no";
            } else {
                $sql = "SELECT * FROM criteria WHERE SI_no='$criteria' ORDER BY Sub_no";
            }
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $criteriaNo = $row['Sub_no'];  
                    $description = $row['Des'];

                    echo "<tr>";
                    echo "<td class='criteria-no'>$criteriaNo</td>";
                    echo "<td>$description</td>";
                    echo "<td>";
                    echo "<form action='' method='POST'>";
                    echo "<input type='hidden' name='criteria_no' value='$criteriaNo'>";
                    echo "<input type='hidden' name='year' value='$academicYear'>";
                    echo "<input type='hidden' name='criteria' value='$criteria'>";
                    echo "<button type='submit' class='btn1' name='delete'>DELETE</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "<td>";
                    echo "<form action='update_criteria.php' method='POST'>";
                    echo "<input type='hidden' name='criteria_no' value='$criteriaNo'>";
                    echo "<input type='hidden' name='year' value='$academicYear'>";
                    echo "<input type='hidden' name='criteria' value='$criteria'>";
                    echo "<button type='submit' class='btn2'>UPDATE</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No data found for the specified criteria.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <form action="add_cri_form.php?event=<?php echo urlencode($event); ?>&designation=<?php echo urlencode($designation); ?>&criteria=<?php echo urlencode($criteria); ?>&year=<?php echo urlencode($academicYear); ?>" method='POST'>
        <input type='hidden' name='academic_year' value='<?php echo $academicYear; ?>'>
        <input type='hidden' name='criteria' value='<?php echo $criteria; ?>'>
        <button class='home-button1' onclick="window.location.href='upload.php'">+ <br>ADD Criteria</button>
    </form>
</div>
</div>

</body>
</html>

