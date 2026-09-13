<?php
    include("../../includes/connection.php");
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['username'])) {
        die("You need to log in to view your uploads.");
    }

    $username = $_SESSION['username'];
    if (isset($_GET['dept'])) {
        $dept = $_GET['dept']; // Get the 'dept' value from the URL
    } else {
        echo "Department not set.";
    }

    if (isset($_GET['type'])) {
        $type = $_GET['type']; // Get the 'dept' value from the URL
    } else {
        echo "desg not set.";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get session username
        $user = $_SESSION['username'];

        $patent_title = $_POST['patent_title'];
        $patent_no = $_POST['patent_no'] ?? '';
        $type_input = $_POST['type'];
        $date_of_issue = $_POST['date_of_issue'];
        $year = $_POST['year'];

        // Process Investors JSON
        $investors_array = [];
        if (isset($_POST['investor_name']) && is_array($_POST['investor_name'])) {
            for ($i = 0; $i < count($_POST['investor_name']); $i++) {
                if (!empty(trim($_POST['investor_name'][$i]))) {
                    $investors_array[] = [
                        'name' => trim($_POST['investor_name'][$i]),
                        'affiliation' => trim($_POST['investor_affiliation'][$i] ?? ''),
                        'position' => trim($_POST['investor_position'][$i] ?? '')
                    ];
                }
            }
        }
        $investors_json = json_encode($investors_array, JSON_UNESCAPED_UNICODE);

        // Handle file upload
        $patent_file = $_FILES['patent_file']['name'];
        $target_dir = "uploads/patents/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $target_file = $target_dir . basename($patent_file);

        if (move_uploaded_file($_FILES['patent_file']['tmp_name'], $target_file)) {
            date_default_timezone_set('Asia/Kolkata');

            $submission_time = date('Y-m-d H:i:s');

            // Insert query
            $stmt = $conn->prepare("INSERT INTO patents_table (Username, branch, patent_title, patent_no, type, date_of_issue, investors, patent_file, submission_time, year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssss", $user, $dept, $patent_title, $patent_no, $type_input, $date_of_issue, $investors_json, $target_file, $submission_time, $year);

            if ($stmt->execute()) {
                echo "<script>alert('Details uploaded successfully');</script>";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading the patent file.";
        }
    }
    
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patents</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0a192f 0%, #172a45 100%);
            background-size: cover;
            background-position: center;
            justify-content: center;
            height: 100%;
            margin: 0;
        }

        

        .container {
            margin-top: 30px;

            margin-bottom: 50px;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.2);
            width: 800px;
            max-width: 95%;
            color: white;
        }

        .cont1{
            display: flex;
            justify-content: center;
            align-items: center;
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

        h1 {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #84fab0;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 16px;
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 0.2px solid rgb(165, 225, 239);
            background-color: #1c1c1c;
            color: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #84fab0;
        }

        .investor-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .investor-table th, .investor-table td {
            border: 1px solid rgb(165, 225, 239);
            padding: 8px;
            text-align: left;
        }
        .investor-table input, .investor-table select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #444;
            background: #2a2a2a;
            color: white;
        }

        .btn-add {
            background-color: #4ca1af;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .btn1 {
            padding: 15px;
            font-size: 18px;
            background-color: #84fab0;
            color: black;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn1:hover {
            background-color: #4ca1af;
        }

        .btn1:active {
            transform: scale(0.98);
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 12px 20px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #c0392b;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                width: 90%;
            }
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php include "../../includes/header.php"; ?>

<div class="cont1">
    <div class="container">
        <div class="contact-wrapper">
            <h1>Patents</h1>
            <form action="" method="post" enctype="multipart/form-data">
                <!-- Patent Title -->
                <div class="form-group">    
                    <label for="patent_title">Patent Title:</label>
                    <input type="text" id="patent_title" name="patent_title" placeholder="Enter Patent Title" required>
                </div>

                <!-- Patent No -->
                <div class="form-group">    
                    <label for="patent_no">Patent No:</label>
                    <input type="text" id="patent_no" name="patent_no" placeholder="Enter Patent Number" required>
                </div>

                <!-- Name of Investors -->
                <div class="form-group">
                    <label>Name of Investors:</label>
                    <table class="investor-table" id="investorTable">
                        <thead>
                            <tr>
                                <th>Name of the Investor</th>
                                <th>Name of Affiliation</th>
                                <th>Position of the Investor</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="investor_name[]" required></td>
                                <td><input type="text" name="investor_affiliation[]" required></td>
                                <td>
                                    <select name="investor_position[]" required>
                                        <option value="First investor">First investor</option>
                                        <option value="First investor with equal contribution">First investor with equal contribution</option>
                                        <option value="Corresponding Investor">Corresponding Investor</option>
                                        <option value="Co-investor">Co-investor</option>
                                    </select>
                                </td>
                                <td><button type="button" onclick="removeInvestorRow(this)" style="padding: 5px 10px; background: #ff4d4d; color: white; border: none; border-radius: 4px; cursor: pointer;">-</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn-add" onclick="addInvestorRow()">+ Add Investor</button>
                </div>

                <!-- Patent Type -->
                <div class="form-group">
                    <label for="type">Type:</label>
                    <select id="type" name="type" required>
                        <option value="" disabled selected>Select Type</option>
                        <option value="published">Published</option>
                        <option value="granted">Granted</option>
                    </select>
                </div>
                <div class="form-group">
                        <label for="academic-year">Select Academic Year:</label>
                        <select name="year" id="academic-year" required>
                            <option value="" disabled selected>Select an academic year</option>
                            <?php
                            include("../../includes/connection.php"); // Must be before this code

                            $query = "SELECT year FROM academic_year ORDER BY year DESC";
                            $result = mysqli_query($conn, $query);

                            if (!$result) {
                                die("Query Failed: " . mysqli_error($conn)); // Debug error
                            }

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $year = htmlspecialchars($row['year']);
                                    echo "<option value=\"$year\">$year</option>";
                                }
                            } else {
                                echo '<option value="" disabled>No years found</option>';
                            }
                            ?>
                        </select>
                    </div>

                <!-- Date of Issue -->
                <div class="form-group">
                    <label for="date_of_issue">Date of Issue:</label>
                    <input type="date" id="date_of_issue" name="date_of_issue" required>
                </div>

                <!-- Patent File -->
                <div class="form-group">
                    <label for="patent_file">Upload Patent File:</label>
                    <input type="file" id="patent_file" name="patent_file" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn1">Submit</button>
            </form>
        </div>
    </div>
</div>

<script>
function addInvestorRow() {
    var table = document.getElementById("investorTable").getElementsByTagName('tbody')[0];
    var newRow = table.insertRow(table.rows.length);
    
    var cell1 = newRow.insertCell(0);
    var cell2 = newRow.insertCell(1);
    var cell3 = newRow.insertCell(2);
    
    cell1.innerHTML = '<input type="text" name="investor_name[]" required>';
    cell2.innerHTML = '<input type="text" name="investor_affiliation[]" required>';
    cell3.innerHTML = `<select name="investor_position[]" required>
                        <option value="First investor">First investor</option>
                        <option value="First investor with equal contribution">First investor with equal contribution</option>
                        <option value="Corresponding Investor">Corresponding Investor</option>
                        <option value="Co-investor">Co-investor</option>
                      </select>`;
    var cell4 = newRow.insertCell(3);
    cell4.innerHTML = '<button type="button" onclick="removeInvestorRow(this)" style="padding: 5px 10px; background: #ff4d4d; color: white; border: none; border-radius: 4px; cursor: pointer;">-</button>';
}

function removeInvestorRow(button) {
    var row = button.parentNode.parentNode;
    row.parentNode.removeChild(row);
}
</script>
</body>
</html>
