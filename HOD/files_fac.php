<?php
include_once "../includes/connection.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
            display: flex;
            justify-content: center;
            margin: 0;
        }
        .container {
            text-align: center;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 80%;
            margin:90px 0px;
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
        .home-button1 {
            position: absolute;
            top: 20px;
            left: 680px;
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
        .home-button2 {
            position: absolute;
            top: 20px;
            left: 1330px;
            padding: 10px 20px;
            background-color: rgb(61, 5, 124);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width:150px;
        }

        .home-button2:hover {
            background-color: rgb(61, 5, 124);
        }
    </style>
</head>
<body>
<?php include_once 'header_hod.php'; ?>
    <div class="container">
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Get the academic year and criteria from the POST request
                $academicYear = htmlspecialchars($_POST['year']);
                $criteria = htmlspecialchars($_POST['criteria']);
                
                // Display the AQAR heading
                echo "<h1>AQAR - " . $academicYear . "</h1>";
            } else {
                echo "<p>No academic year or criteria was selected.</p>";
                exit; // Stop further execution if POST data is not available
            }
        ?>

        <!-- Create the table with the heading and rows -->
        <table>
            <thead>
                <tr>
                    <th colspan="4" id="th1">Criteria <?php echo $criteria; ?></th>
                </tr>
                <tr id="tr2">
                    <th scope="col">Criteria No</th>
                    <th scope="col">Description</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
                <tbody>
                <?php
                    // Define the data
                    if ($academicYear == '2020-21') {
                        $sql = "SELECT * FROM criteria1 WHERE SI_no='$criteria' order by 'Sub_no'";
                    } elseif ($academicYear == '2021-22') {
                        $sql = "SELECT * FROM criteria2 WHERE SI_no='$criteria' order by 'Sub_no'";
                    } else {
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
                                    echo "<form action='files_view_fac.php' method='POST'>";
                                    echo "<input type='hidden' name='academic_year' value='$academicYear'>";
                                    echo "<input type='hidden' name='criteria' value='$criteria'>";
                                    echo "<input type='hidden' name='criteria_no' value='$criteriaNo'>";
                                    echo "<button type='submit'>view files</button>";
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

</body>
</html>
