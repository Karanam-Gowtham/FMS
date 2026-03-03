<?php
include("../includes/connection.php");
include "header_admin.php";

$event = isset($_GET['event']) ? htmlspecialchars($_GET['event']) : '';
$designation = isset($_GET['designation']) ? htmlspecialchars($_GET['designation']) : '';
$criteria = isset($_GET['criteria']) ? htmlspecialchars($_GET['criteria']) : 'Not Selected';
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
        .container11 {
        margin-left:200px;
        text-align: center;
        background-color: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        width: 80%;
        margin: 50px 0px;
        position: relative;
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
    #tr2 {
        background-color: rgb(3, 2, 71);
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
    .btn_u{
        background-color: green;
    }
    .btn_u:hover{
        background-color:rgb(53, 167, 72)
    }
    button {
        background-color: #007BFF;
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
    button:hover {
        background-color: #0056b3;
    }
    .home-button {
        position: absolute;
        top: 30px;
        left: 20px;
        padding: 10px 20px;
        background-color: rgb(12, 90, 4);
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        width: 150px;
    }
    .home-button:hover {
        background-color: rgb(68, 191, 93);
    }
    .home-button2 {
        position: absolute;
        top: 30px;
        right: 20px;
        padding: 10px 20px;
        background-color: rgb(61, 5, 124);
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        width: 150px;
    }
    .home-button2:hover {
        background-color: rgb(102, 19, 204);
    }
    @media (max-width: 768px) {
        .home-button,
        .home-button2 {
            width: 120px;
            font-size: 14px;
            padding: 8px 16px;
        }
        h1 {
            font-size: 20px;
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
                <a href="../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../modules/central/c_login_n.php?event=<?php echo urlencode($event); ?>" class="home-icon">Central (<?php echo htmlspecialchars($event); ?>)</a></span>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../modules/central/c_aqar_files.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon"><?php echo htmlspecialchars($designation); ?></a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a">Criteria <?php echo"$criteria" ?>  </a></span>
                
            </div>
        </div>
    </nav>
    <div class="cont11">
    <div class="container11">
    <?php
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            // Get values from POST request
            $designation = isset($_GET['designation']) ? htmlspecialchars($_GET['designation']) : 'Unknown';
            $academicYear = isset($_GET['year']) ? htmlspecialchars($_GET['year']) : 'Not Selected';
            $criteria = isset($_GET['criteria']) ? htmlspecialchars($_GET['criteria']) : 'Not Selected';
                

            // Display the AQAR heading and selected details
            echo "<h1>AQAR - " . $academicYear . "</h1>";
        }else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get values from POST request
            $designation = isset($_POST['designation']) ? htmlspecialchars($_POST['designation']) : 'Unknown';
            $academicYear = isset($_POST['year']) ? htmlspecialchars($_POST['year']) : 'Not Selected';
            $criteria = isset($_POST['criteria']) ? htmlspecialchars($_POST['criteria']) : 'Not Selected';
                

            // Display the AQAR heading and selected details
            echo "<h1>AQAR - " . $academicYear . "</h1>";
        } else {
            echo "<p>No academic year or criteria was selected.</p>";
            exit; // Stop further execution if GET data is not available
        }
        ?>


        <!-- Create the table with the heading and rows -->
        <table>
            <thead>
                <tr>
                    <th colspan="4" id="th1">Criteria <?php echo $criteria; ?></th>
                </tr>
                <tr id="tr2">
                    <th>Criteria No</th>
                    <th>Description</th>
                    <th>Action</th>
                    <th>ACtion2</th>
                </tr>
            </thead>
                <tbody>
                <?php
                    // Define the data
                    if($academicYear=='2020-21'){
                        $sql = "SELECT * FROM criteria1 WHERE SI_no='$criteria' order by 'Sub_no'";
                    }else if($academicYear=='2021-22'){
                        $sql = "SELECT * FROM criteria2 WHERE SI_no='$criteria' order by 'Sub_no'";
                    }else{
                        $sql = "SELECT * FROM criteria WHERE SI_no='$criteria' order by 'Sub_no'";
                    }

                            $result = $conn->query($sql);

                            // Check if any rows are returned
                            if ($result->num_rows > 0) {
                                // Loop through the result set and display rows
                                while ($row = $result->fetch_assoc()) {
                                    $criteriaNo = $row['Sub_no'];  
                                    $description = $row['Des'];

                                    // Display rows
                                    echo "<tr>";
                                    echo "<td class='criteria-no'>$criteriaNo</td>";
                                    echo "<td>$description</td>";
                                    echo "<td>";
                                    echo "<form action='download.php?year=" . urlencode($academicYear) . "&criteria=" . urlencode($criteria) . "&designation=" . urlencode($designation) . "&event=" . urlencode($event) . "' method='POST'>";
                                    echo "<input type='hidden' name='academic_year' value='" . htmlspecialchars($academicYear) . "'>";
                                    echo "<input type='hidden' name='criteria' value='" . htmlspecialchars($criteria) . "'>";
                                    echo "<input type='hidden' name='criteria_no' value='" . htmlspecialchars($criteriaNo) . "'>";
                                    echo "<button type='submit'>View Files</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "<td>";
                                    echo "<form action='upload.php?year=" . urlencode($academicYear) . "&criteria=" . urlencode($criteria) . "&designation=" . urlencode($designation) . "&event=" . urlencode($event) . "' method='POST'>";
                                    echo "<input type='hidden' name='academic_year' value='" . htmlspecialchars($academicYear) . "'>";
                                    echo "<input type='hidden' name='criteria' value='" . htmlspecialchars($criteria) . "'>";
                                    echo "<input type='hidden' name='criteria_no' value='" . htmlspecialchars($criteriaNo) . "'>";
                                    echo "<button class='btn_u' type='submit'>Upload Files</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    
                                    echo "<form action='my_uploads.php?year=" . urlencode($academicYear) . "&criteria=" . urlencode($criteria) . "&designation=" . urlencode($designation) . "&event=" . urlencode($event) . "' method='POST'>";
                                    echo "<input type='hidden' name='academic_year' value='$academicYear'>";
                                    echo "<input type='hidden' name='criteria' value='$criteria'>";
                                    echo "<input type='hidden' name='criteria_no' value='$criteriaNo'>";
                                    echo "<button class='home-button2' type='submit'>my Uploads</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>No data found for the specified criteria.</td></tr>";
                            }
                ?>
            </tbody>

        </table>
    </div>
    </div>

</body>
</html>
