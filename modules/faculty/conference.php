<?php

include "../../includes/connection.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
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
    $type = $_GET['type']; // Get the 'type' value from the URL
} else {
    echo "desg not set.";
}

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user = $_SESSION['username'];

    $paper_title = $_POST['paper_title'];
    $conference_name = $_POST['conference_name'];
    $published_paper_name = $_POST['published_paper_name'];
    $issn_no = $_POST['issn_no'];
    $volume_no = $_POST['volume_no'];
    $issue_no = $_POST['issue_no'];
    $page_no = $_POST['page_no'];
    $doi = $_POST['doi'];
    $year = $_POST['year'];
    $indexing = $_POST['indexing'];
    $publication_link = $_POST['publication_link'];

    // Process Authors JSON
    $authors_array = [];
    if (isset($_POST['author_name']) && is_array($_POST['author_name'])) {
        for ($i = 0; $i < count($_POST['author_name']); $i++) {
            if (!empty(trim($_POST['author_name'][$i]))) {
                $authors_array[] = [
                    'name' => trim($_POST['author_name'][$i]),
                    'affiliation' => trim($_POST['author_affiliation'][$i]),
                    'position' => trim($_POST['author_position'][$i])
                ];
            }
        }
    }
    $authors_json = json_encode($authors_array, JSON_UNESCAPED_UNICODE);

    // Handle file uploads
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $paper_file_path = '';
    if (isset($_FILES['paper_file']) && $_FILES['paper_file']['error'] == 0) {
        $paper_file_path = $upload_dir . basename($_FILES['paper_file']['name']);
        move_uploaded_file($_FILES['paper_file']['tmp_name'], $paper_file_path);
    }

    date_default_timezone_set('Asia/Kolkata');
    $submission_time = date('Y-m-d H:i:s');

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO conference_tab (username, branch, paper_title, authors, conference_name, published_paper_name, issn_no, volume_no, issue_no, page_no, doi, indexing, publication_link, paper_file_path, submission_time, year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssssss", $user, $dept, $paper_title, $authors_json, $conference_name, $published_paper_name, $issn_no, $volume_no, $issue_no, $page_no, $doi, $indexing, $publication_link, $paper_file_path, $submission_time, $year);

    if ($stmt->execute()) {
        echo "<script>alert('Submission successful!');</script>";
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<?php 
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Proceedings Published</title>
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
        
        .author-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .author-table th, .author-table td {
            border: 1px solid rgb(165, 225, 239);
            padding: 8px;
            text-align: left;
        }
        .author-table input, .author-table select {
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
            margin-top: 20px;
        }

        .btn1:hover {
            background-color: #4ca1af;
        }

        .btn1:active {
            transform: scale(0.98);
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
        <h1>Conference Proceedings Published Form</h1>

        <form id="conference-form" method="POST" enctype="multipart/form-data" action="">

            <!-- Paper Title -->
            <div class="form-group">
                <label for="paper-title">Paper Title:</label>
                <input type="text" id="paper-title" name="paper_title" required>
            </div>

            <!-- Authors -->
            <div class="form-group">
                <label>Name of Authors:</label>
                <table class="author-table" id="authorTable">
                    <thead>
                        <tr>
                            <th>Name of the Author</th>
                            <th>Name of Affiliation</th>
                            <th>Position of the Author</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="author_name[]" required></td>
                            <td><input type="text" name="author_affiliation[]" required></td>
                            <td>
                                <select name="author_position[]" required>
                                    <option value="First author">First author</option>
                                    <option value="First author with equal contribution">First author with equal contribution</option>
                                    <option value="Corresponding Author">Corresponding Author</option>
                                    <option value="Co-author">Co-author</option>
                                </select>
                            </td>
                            <td><button type="button" onclick="removeAuthorRow(this)" style="padding: 5px 10px; background: #ff4d4d; color: white; border: none; border-radius: 4px; cursor: pointer;">-</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn-add" onclick="addAuthorRow()">+ Add Author</button>
            </div>

            <!-- Conference Name -->
            <div class="form-group">
                <label for="conference_name">Name of The Conference:</label>
                <input type="text" id="conference_name" name="conference_name" required>
            </div>

            <!-- Published Paper Name -->
            <div class="form-group">
                <label for="published_paper_name">Name of Journal Published:</label>
                <input type="text" id="published_paper_name" name="published_paper_name" required>
            </div>
            
            <div class="form-group">
                <label for="issn_no">ISSN no:</label>
                <input type="text" id="issn_no" name="issn_no" required>
            </div>
            
            <div class="form-group">
                <label for="volume_no">Volume No:</label>
                <input type="text" id="volume_no" name="volume_no" required>
            </div>
            
            <div class="form-group">
                <label for="issue_no">Issue No:</label>
                <input type="text" id="issue_no" name="issue_no" required>
            </div>
            
            <div class="form-group">
                <label for="page_no">Page No:</label>
                <input type="text" id="page_no" name="page_no" required>
            </div>

            <div class="form-group">
                <label for="doi">DOI(Date):</label>
                <input type="text" id="doi" name="doi" required>
            </div>

            <div class="form-group">
                <label for="academic-year">Year of Publication:</label>
                <select name="year" id="academic-year" required>
                    <option value="" disabled selected>Select an academic year</option>
                    <?php
                    include("../../includes/connection.php");

                    $query = "SELECT year FROM academic_year ORDER BY year DESC";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
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
            
            <div class="form-group">
                <label for="indexing">Indexing:</label>
                <select id="indexing" name="indexing" required>
                    <option value="SCI">SCI</option>
                    <option value="SCIE">SCIE</option>
                    <option value="ESCI">ESCI</option>
                    <option value="SCOPUS">SCOPUS</option>
                    <option value="WOS">WOS</option>
                    <option value="NON-INDEXED">NON-INDEXED</option>
                </select>
            </div>

            <div class="form-group">
                <label for="publication_link">Publication Link:</label>
                <input type="url" id="publication_link" name="publication_link" required>
            </div>

            <!-- Paper File -->
            <div id="paper-upload-div" class="form-group">
                <label for="paper-file">Upload full paper (pdf):</label>
                <input type="file" id="paper-file" name="paper_file" accept=".pdf" required>
            </div>

            <button class="btn1" type="submit">Submit</button>

        </form>
    </div></div>

    <script>
    function addAuthorRow() {
        var table = document.getElementById("authorTable").getElementsByTagName('tbody')[0];
        var newRow = table.insertRow(table.rows.length);
        
        var cell1 = newRow.insertCell(0);
        var cell2 = newRow.insertCell(1);
        var cell3 = newRow.insertCell(2);
        
        cell1.innerHTML = '<input type="text" name="author_name[]" required>';
        cell2.innerHTML = '<input type="text" name="author_affiliation[]" required>';
        cell3.innerHTML = `<select name="author_position[]" required>
                            <option value="First author">First author</option>
                            <option value="First author with equal contribution">First author with equal contribution</option>
                            <option value="Corresponding Author">Corresponding Author</option>
                            <option value="Co-author">Co-author</option>
                          </select>`;
        var cell4 = newRow.insertCell(3);
        cell4.innerHTML = '<button type="button" onclick="removeAuthorRow(this)" style="padding: 5px 10px; background: #ff4d4d; color: white; border: none; border-radius: 4px; cursor: pointer;">-</button>';
    }

    function removeAuthorRow(button) {
        var row = button.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }
    </script>
</body>
</html>
