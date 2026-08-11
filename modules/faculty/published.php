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
        $type = $_GET['type']; // Get the 'type' value from the URL
    } else {
        echo "desg not set.";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get session username
        $user = $_SESSION['username'];

        $branch_query = "SELECT dept FROM reg_tab WHERE userid = '$user'";
        $branch_result = $conn->query($branch_query);

        if ($branch_result && $branch_result->num_rows > 0) {
            $branch_row = $branch_result->fetch_assoc();
            $branch = $branch_row['dept'];
        } else {
            die("Branch not found for the user.");
        }

        $paper_title = $_POST['paper_title'];
        $journal_name = $_POST['journal_name'];
        $issn_no = $_POST['issn_no'];
        $volume_no = $_POST['volume_no'];
        $issue_no = $_POST['issue_no'];
        $page_no = $_POST['page_no'];
        $doi = $_POST['doi'];
        $year = $_POST['year'];
        $indexing = $_POST['indexing'];
        $impact_factor = $_POST['impact_factor'];
        $jcr_quartile = $_POST['jcr_quartile'];
        $scopus_quartile = $_POST['scopus_quartile'];
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

        // Handle file upload
        $file_name = $_FILES['paper_file']['name'];
        $file_tmp_name = $_FILES['paper_file']['tmp_name'];
        $file_size = $_FILES['paper_file']['size'];
        $file_error = $_FILES['paper_file']['error'];

        if ($file_error === 0) {
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_extensions = ['pdf', 'doc', 'docx'];

            if (in_array(strtolower($file_extension), $allowed_extensions)) {
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $file_destination = $upload_dir . $file_new_name;
                
                if (move_uploaded_file($file_tmp_name, $file_destination)) {
                    date_default_timezone_set('Asia/Kolkata');
                    $submission_time = date('Y-m-d H:i:s');
                    
                    // SQL query to insert data into published_tab table
                    $stmt = $conn->prepare("INSERT INTO published_tab (username, branch, paper_title, authors, journal_name, issn_no, volume_no, issue_no, page_no, doi, indexing, impact_factor, jcr_quartile, scopus_quartile, publication_link, submission_time, paper_file, year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssssssssssssss", $user, $branch, $paper_title, $authors_json, $journal_name, $issn_no, $volume_no, $issue_no, $page_no, $doi, $indexing, $impact_factor, $jcr_quartile, $scopus_quartile, $publication_link, $submission_time, $file_destination, $year);

                    if ($stmt->execute()) {
                        echo "<script>alert('Details and paper uploaded successfully');</script>";
                    } else {
                        echo "Error: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    echo "<script>alert('There was an error uploading your file.');</script>";
                }
            } else {
                echo "<script>alert('Invalid file type. Only PDF, DOC, DOCX files are allowed.');</script>";
            }
        } else {
            echo "<script>alert('There was an error with the file upload.');</script>";
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
    <title>Research Papers Published</title>
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
        <div class="contact-wrapper">
        <div class="contact-form">
            <h1>Research Papers Published Form</h1>
            <form action="" method="POST" id="contactForm" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label for="paper_title">Paper Title:</label>
                    <input type="text" id="paper_title" name="paper_title" required>
                </div>
                
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

                <div class="form-group">
                    <label for="journal_name">Journal Name:</label>
                    <input type="text" id="journal_name" name="journal_name" required>
                </div>

                <div class="form-group">
                    <label for="issn_no">ISSN no:</label>
                    <input type="text" id="issn_no" name="issn_no" required>
                </div>
                
                <div class="form-group">
                    <label for="volume_no">Volume no:</label>
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
                    <label for="impact_factor">Impact Factor:</label>
                    <input type="number" id="impact_factor" name="impact_factor" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="jcr_quartile">JCR Quartile:</label>
                    <select id="jcr_quartile" name="jcr_quartile" required>
                        <option value="Q1">Q1</option>
                        <option value="Q2">Q2</option>
                        <option value="Q3">Q3</option>
                        <option value="Q4">Q4</option>
                        <option value="None">None</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="scopus_quartile">Scopus Quartile:</label>
                    <select id="scopus_quartile" name="scopus_quartile" required>
                        <option value="Q1">Q1</option>
                        <option value="Q2">Q2</option>
                        <option value="Q3">Q3</option>
                        <option value="Q4">Q4</option>
                        <option value="None">None</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="publication_link">Publication Link:</label>
                    <input type="url" id="publication_link" name="publication_link" required>
                </div>

                <div class="form-group">
                    <label for="paper_file">Upload full paper (pdf):</label>
                    <input type="file" id="paper_file" name="paper_file" accept=".pdf" required>
                </div>
                
                <button type="submit" class="btn1 btn-outline">Submit</button>
            </form>
        </div>
        </div>
    </div>
</div>

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
