<?php

include_once "../../includes/connection.php";
include_once "../../includes/header.php";

if (!isset($_SESSION['username'])) {
    die("You need to log in to view this page.");
}

$username = $_SESSION['username'];
$dept = isset($_GET['dept']) ? $_GET['dept'] : '';

if (!$dept) {
    $stmt = $conn->prepare("SELECT dept FROM reg_tab WHERE userid = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $dept = $row['dept'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $paper_title = $_POST['paper_title'];
    $conference_name = $_POST['conference_name'];
    $published_journal_name = $_POST['published_journal_name'];
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

    // Handle file upload
    $target_dir = "../../uploads/conference/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $paper_file_path = '';
    if (isset($_FILES['paper_file']) && $_FILES['paper_file']['error'] == 0) {
        $file_name = time() . '_' . basename($_FILES['paper_file']['name']);
        $targetFile = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['paper_file']['tmp_name'], $targetFile)) {
            $paper_file_path = $targetFile;
        }
    }

    $status = 'Pending HOD';

    $sql = "INSERT INTO conference_tab (username, branch, paper_title, authors, conference_name, published_journal_name, issn_no, volume_no, issue_no, page_no, doi, indexing, publication_link, paper_file_path, submission_time, year, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssssss", $username, $dept, $paper_title, $authors_json, $conference_name, $published_journal_name, $issn_no, $volume_no, $issue_no, $page_no, $doi, $indexing, $publication_link, $paper_file_path, $year, $status);

    if ($stmt->execute()) {
        echo "<script>alert('Conference paper submitted successfully!'); window.location.href='acd_year.php?dept=" . urlencode($dept) . "';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Conference Proceedings Published</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #0a192f 0%, #172a45 100%);
            color: white;
            min-height: 100vh;
            margin: 0;
            padding-bottom: 50px;
        }

        .navbar {
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 0;
            border-bottom: 1px solid #eee;
            background-color: white;
            font-size: larger;
        }

        .nav-container {
            margin-left: 100px;
            max-width: 80rem;
            padding: 0 1rem;
        }

        .nav-items {
            display: flex;
            align-items: center;
            height: 4rem;
        }

        .sid {
            color: rgb(48, 30, 138);
            font-weight: 500;
        }

        .main-a {
            color: rgb(138, 30, 113);
            font-weight: 500;
        }

        .sp {
            color: blue;
        }

        .container11 {
            margin: 50px auto;
            background: rgba(16, 15, 15, 0.8);
            padding: 40px;
            border-radius: 15px;
            max-width: 800px;
            width: 90%;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="url"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            box-sizing: border-box;
        }

        .author-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .author-table th,
        .author-table td {
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px;
            text-align: left;
        }

        .author-table input,
        .author-table select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #444;
            background: #2a2a2a;
            color: white;
            margin-bottom: 0;
        }

        .btn-add {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .btn-add:hover {
            background-color: #0056b3;
        }

        .btn-remove {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .btn-remove:hover {
            background-color: #c82333;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #ff6347;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            background: #e55337;
        }

        option {
            background-color: #333;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                        href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>"
                        class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span class="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                        href="acd_year.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Faculty</a></span>
                <span class="sp">&nbsp; >> &nbsp;</span><span class="main"><span class="main-a">Conference Proceedings Published</span></span>
            </div>
        </div>
    </nav>

    <div class="container11">
        <h2>Conference Proceedings Published Form</h2>
        <form method="POST" enctype="multipart/form-data">

            <!-- Paper Title -->
            <label for="paper_title">Paper Title:</label>
            <input type="text" id="paper_title" name="paper_title" required>

            <!-- Authors Table -->
            <label>Name of Authors:</label>
            <table class="author-table" id="authorTable">
                <thead>
                    <tr>
                        <th>Name of the Author</th>
                        <th>Name of Affiliation</th>
                        <th>Position of the Author</th>
                        <th></th>
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
                        <td>
                            <button type="button" class="btn-remove" onclick="removeAuthorRow(this)">-</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn-add" onclick="addAuthorRow()">+ Add Author</button>

            <!-- Conference Name -->
            <label for="conference_name">Name of The Conference:</label>
            <input type="text" id="conference_name" name="conference_name" required>

            <!-- Published Journal Name -->
            <label for="published_journal_name">Name of the Journal Published:</label>
            <input type="text" id="published_journal_name" name="published_journal_name" required>

            <label for="issn_no">ISSN No:</label>
            <input type="text" id="issn_no" name="issn_no" required>

            <label for="volume_no">Volume No:</label>
            <input type="text" id="volume_no" name="volume_no" required>

            <label for="issue_no">Issue No:</label>
            <input type="text" id="issue_no" name="issue_no" required>

            <label for="page_no">Page No:</label>
            <input type="text" id="page_no" name="page_no" required>

            <label for="doi">DOI:</label>
            <input type="text" id="doi" name="doi" required>

            <label for="year">Year of Publication:</label>
            <select id="year" name="year" required>
                <option value="">Select Year</option>
                <?php
                $y_sql = "SELECT year FROM academic_year ORDER BY year DESC";
                $y_res = $conn->query($y_sql);
                if ($y_res) {
                    while ($y = $y_res->fetch_assoc()) {
                        echo "<option value='" . $y['year'] . "'>" . $y['year'] . "</option>";
                    }
                }
                ?>
            </select>

            <label for="indexing">Indexing:</label>
            <select id="indexing" name="indexing" required>
                <option value="SCI">SCI</option>
                <option value="SCIE">SCIE</option>
                <option value="ESCI">ESCI</option>
                <option value="SCOPUS">SCOPUS</option>
                <option value="WOS">WOS</option>
                <option value="NON-INDEXED">NON-INDEXED</option>
            </select>

            <label for="publication_link">Publication Link:</label>
            <input type="url" id="publication_link" name="publication_link" required>

            <!-- Paper File -->
            <label for="paper_file">Upload Full Paper (PDF):</label>
            <input type="file" id="paper_file" name="paper_file" accept=".pdf" required>

            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
    function addAuthorRow() {
        var table = document.getElementById("authorTable").getElementsByTagName('tbody')[0];
        var newRow = table.insertRow(table.rows.length);

        var cell1 = newRow.insertCell(0);
        var cell2 = newRow.insertCell(1);
        var cell3 = newRow.insertCell(2);
        var cell4 = newRow.insertCell(3);

        cell1.innerHTML = '<input type="text" name="author_name[]" required>';
        cell2.innerHTML = '<input type="text" name="author_affiliation[]" required>';
        cell3.innerHTML = `<select name="author_position[]" required>
                            <option value="First author">First author</option>
                            <option value="First author with equal contribution">First author with equal contribution</option>
                            <option value="Corresponding Author">Corresponding Author</option>
                            <option value="Co-author">Co-author</option>
                           </select>`;
        cell4.innerHTML = '<button type="button" class="btn-remove" onclick="removeAuthorRow(this)">-</button>';
    }

    function removeAuthorRow(button) {
        var row = button.parentNode.parentNode;
        if(document.getElementById("authorTable").getElementsByTagName('tbody')[0].rows.length > 1) {
            row.parentNode.removeChild(row);
        } else {
            alert("At least one author is required.");
        }
    }
    </script>
</body>

</html>
<?php $conn->close(); ?>
