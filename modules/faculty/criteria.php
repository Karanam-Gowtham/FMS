<?php
include("../../includes/connection.php");

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
        td.description {
    text-align: left;
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
        .btn1:hover {
            background-color: #0056b3;
        }
        #cri{
            background-color: #007BFF;
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
        .my-uploads-btn {
            position: absolute; /* Allows precise positioning */
            top: 220px; /* Adjusts the vertical position */
            right: 150px; /* Adjusts the horizontal position */
            padding: 10px 20px;
            background-color: #0a640a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 200px;
        }

        .my-uploads-btn:hover {
            background-color: #065821;
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
<?php
    include '../../includes/header.php';
?>
<body>
<nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../central/c_login_n.php?event=<?php echo urlencode($event); ?>" class="home-icon">Central (<?php echo htmlspecialchars($event); ?>)</a></span>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../central/c_aqar_files.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon"><?php echo htmlspecialchars($designation); ?></a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a">Criteria <?php echo"$criteria" ?>  </a></span>
                
            </div>
        </div>
    </nav>
    <div class="cont11">
    <div class="container11">
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                $designation = isset($_GET['designation']) ? htmlspecialchars($_GET['designation']) : 'Unknown';
                $academicYear = isset($_GET['year']) ? htmlspecialchars($_GET['year']) : 'Not Selected';
                $criteria = isset($_GET['criteria']) ? htmlspecialchars($_GET['criteria']) : 'Not Selected';
                echo "<h1>AQAR - " . $academicYear . "</h1>";
            } else {
                echo "<p>No academic year or criteria was selected.</p>";
                exit;
            }
        ?>

        <table>
            <thead>
                <tr>
                    <th colspan="4" id="th1">Criteria <?php echo $criteria; ?></th>
                </tr>
                <tr id="tr2">
                    <th>Criteria No</th>
                    <th>Description</th>
                    <th>Templates</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if($academicYear=='2020-21'){
                        $sql = "SELECT * FROM criteria1 WHERE SI_no='$criteria' order by Sub_no";
                    } else if($academicYear=='2021-22'){
                        $sql = "SELECT * FROM criteria2 WHERE SI_no='$criteria' order by Sub_no";
                    } else {
                        $sql = "SELECT * FROM criteria WHERE SI_no='$criteria' order by Sub_no";
                    }
                    
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $criteriaNo = $row['Sub_no'];  
                            $description = $row['Des'];
                        
                            echo "<tr>";
                            echo "<td class='criteria-no'>$criteriaNo</td>";
                            echo "<td class='description'>$description</td>";
                        
                            // Search for matching files
                            $templateFolder = '../../assets/templates/templates_docs/';
                            $pattern = $templateFolder . preg_replace('/[^A-Za-z0-9()._-]/', '_', $criteriaNo) . '.*';
                            $files = glob($pattern);
                        
                            echo "<td>";
                                if (!empty($files)) {
                                    if (count($files) == 1) {
                                        $file = $files[0];
                                        $fileName = basename($file);
                                        echo "<a href='$file' target='_blank'>Template</a><br>";
                                    } else {
                                        $count = 1;
                                        foreach ($files as $file) {
                                            $fileName = basename($file);
                                            echo "<a href='$file' target='_blank'>Template{$count}</a><br>";
                                            $count++;
                                        }
                                    }
                                } else {
                                    echo "No Template";
                                }
                                echo "</td>";
                        
                            // Determine upload script
                            $uploadScript = 'upload.php'; // default
                            switch ($criteriaNo) {
                                case '5.1.1':
                                case '5.1.2':
                                    $uploadScript = 'up5.1.1&2.php';
                                    break;
                                case '5.1.3':
                                    $uploadScript = 'up5.1.3.php';
                                    break;
                                case '5.1.4(':
                                    $uploadScript = 'up5.1.4.php';
                                    break;
                                case '5.2.1':
                                    $uploadScript = 'up5.2.1.php';
                                    break;
                                case '5.2.2':
                                    $uploadScript = 'up5.2.2.php';
                                    break;
                                case '5.2.3':
                                    $uploadScript = 'up5.2.3.php';
                                    break;
                                case '5.3.1':
                                    $uploadScript = 'up5.3.1.php';
                                    break;
                                case '5.3.3':
                                    $uploadScript = 'up5.3.3.php';
                                    break;
                            }
                        
                            // Action button
                            echo "<td>";
                            echo "<form action='$uploadScript' method='POST'>";
                            echo "<input type='hidden' name='academic_year' value='$academicYear'>";
                            echo "<input type='hidden' name='criteria' value='$criteria'>";
                            echo "<input type='hidden' name='criteria_no' value='$criteriaNo'>";
                            echo "<input type='hidden' name='event' value='$event'>";
                            echo "<input type='hidden' name='desg' value='$designation'>";
                            echo "<button type='submit' class='btn1'>Upload Files</button>";
                            echo "</form>";
                            echo "</td>";
                        
                            echo "</tr>";
                        }
                    }                        
                ?>
            </tbody>
        </table>
        <form action='my_uploads_new.php' method='GET'>
            <input type='hidden' name='a_year' value='<?php echo $academicYear; ?>'>
            <input type='hidden' name='criteria' value='<?php echo $criteria; ?>'>
            <button type="button" class="my-uploads-btn" onclick="redirectToUploads()">My Uploads</button>

            <script>
                function redirectToUploads() {
                    const event = "<?php echo $event; ?>";  
                    const acd_year = "<?php echo $academicYear; ?>"; 
                    const criteria = "<?php echo $criteria; ?>";         // replace with your event variable
                    const designation = "faculty";      // replace with your designation variable

                    window.location.href = "my_uploads_new.php?event=" + encodeURIComponent(event) + "&designation=" + encodeURIComponent(designation)+ "&cri=" + encodeURIComponent(criteria)+ "&ac_year=" + encodeURIComponent(acd_year);
                }
            </script>
        </form>
    </div>
    </div>
</body>
</html>
