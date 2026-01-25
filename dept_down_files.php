<?php
include "connection.php";
session_start();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    die("Please login to access this page.");
}
$username = $_SESSION['username'];
if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['selected_files']) &&
    is_array($_POST['selected_files']) &&
    isset($_POST['action'])
) {
    $action = $_POST['action'];
    $files = $_POST['selected_files'];

    if ($action === 'download') {
        if (count($files) === 1) {
            // Download single file directly
            $decoded = urldecode($files[0]);
            if (file_exists($decoded)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Content-Disposition: attachment; filename=\"" . basename($decoded) . "\"");
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($decoded));
                readfile($decoded);
                exit();
            } else {
                echo "File not found.";
            }
        } else {
            // Download multiple files as a ZIP
            $zip = new ZipArchive();
            $zipName = "zip_file" . time() . ".zip";
            $zipPath = sys_get_temp_dir() . "/" . $zipName;
    
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                foreach ($files as $file) {
                    $decoded = urldecode($file);
                    if (file_exists($decoded)) {
                        $zip->addFile($decoded, basename($decoded));
                    }
                }
                $zip->close();
                header('Content-Type: application/zip');
                header("Content-Disposition: attachment; filename=\"$zipName\"");
                header('Content-Length: ' . filesize($zipPath));
                readfile($zipPath);
                unlink($zipPath);
                exit();
            } else {
                echo "Failed to create ZIP archive.";
            }
        }
    }
     elseif ($action === 'delete') {
        foreach ($files as $file) {
            $decoded = urldecode($file);
            $stmt = $conn->prepare("DELETE FROM dept_files WHERE file_path = ? AND username = ?");
            $stmt->bind_param("ss", $decoded, $username);
            $stmt->execute();
            if (file_exists($decoded)) {
                unlink($decoded);
            }
        }
        echo "<script>alert('Selected files deleted.'); </script>";
    }
}

include "./header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Retrieve Files</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color: #fff;
            margin: 0;
            padding: 0;
        }
        header { position: sticky; }
        h1 {
            text-align: center;
            font-size: 2rem;
            font-weight: 600;
            margin-top: 00px;
            color: darkblue;
        }
        .container11, .container111 {
            margin-top: 50px;
            margin-left: 50px;
            width: 90%;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: #333;
        }
        .container111{
            margin-bottom: 50px;
        }/* Navigation */
.navbar {
    background-color: white;
    font-size: larger;
}

.nav-container {
    margin-left:100px;
    max-width: 80rem;
    padding: 0 1rem;
}
span{
    color:#0056b3;
}

.nav-items {
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
        .filter-section {
            margin-top: 30px;
            width: 90%;
            padding: 15px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .filter-form {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 15px;
        }
        select {
            padding: 10px;
            border: 2px solid #1e3c72;
            border-radius: 6px;
            font-size: 14px;
            min-width: 220px;
            color: #333;
        }
        .filter-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s ease-in-out;
        }
        .filter-button:hover { background-color: #0056b3; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 2px solid #ddd;
        }
        th {
            background: #1e3c72;
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) { background: #f0f5ff; }
        tr:hover { background: #d6e4ff; transition: 0.3s; }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s ease-in-out;
            text-decoration: none;
            display: inline-block;
        }
        .view-btn { background: #42a5f5; color: white; margin-right: 10px; }
        .view-btn:hover { background: #1e88e5; }
        .download-btn { background: #66bb6a; color: white; }
        .download-btn:hover { background: #43a047; }
        .delete-btn { background: #e74c3c; color: white; }
        .delete-btn:hover { background: #c0392b; }
    </style>
</head>
<body>
<nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="acd_year.php?dept=<?php echo "$dept" ?>" class="home-icon"> Faculty </a></span>
                <span>&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a"> My Dept Files </a></span>
                <span>&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>

<div class="container11">
    <h1>Retrieve Files</h1>

    <?php $selected_file_type = $_POST['selected_file_type'] ?? ''; ?>
    <div class="filter-section">
        <form method="POST" class="filter-form">
            <label for="selected_file_type">Select File Type:</label>
            <select name="selected_file_type" id="selected_file_type" required>
                <option value="" disabled selected>-- Select File Type --</option>
                <option value="admin" <?= $selected_file_type === 'admin' ? 'selected' : '' ?>>Admin Files</option>
                <option value="student" <?= $selected_file_type === 'student' ? 'selected' : '' ?>>Student Files</option>
                <option value="faculty" <?= $selected_file_type === 'faculty' ? 'selected' : '' ?>>Faculty Files</option>
                <option value="exam_section" <?= $selected_file_type === 'exam_section' ? 'selected' : '' ?>>Exam Section Files</option>
            </select>
            <button type="submit" class="filter-button">Show Files</button>
        </form>
    </div>
</div>

<div class="container111">
<?php
$file_types = ['admin', 'faculty', 'student', 'exam_section'];
$file_types_to_show = $selected_file_type ? [$selected_file_type] : [];

foreach ($file_types_to_show as $file_type) {
    $sql = "SELECT file_type, dept, academic_year, sub_file_type, file_name, file_path FROM dept_files WHERE username = ? AND file_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $file_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>" . ucfirst(str_replace("_", " ", $file_type)) . " Files</h2>";
        echo "<form method='post' action=''>";

        echo "<table border='1'>
                <thead>
                    <tr>
                        <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Dept</th>
                        <th>Academic Year</th>
                        <th>File Type</th>
                        <th>Sub File Type </th>
                        <th>File Name</th>
                    </tr>
                </thead>
                <tbody>";
        $id = 1;
        while ($row = $result->fetch_assoc()) {
            $file_path = htmlspecialchars($row['file_path'], ENT_QUOTES);
            echo "<tr>
                    <td><input type='checkbox' name='selected_files[]' value='" . urlencode($file_path) . "' data-filepath=\"$file_path\"></td>
                    <td>$id</td>
                    <td>$username</td>
                    <td>" . $row['dept'] . "</td>
                    <td>" . $row['academic_year'] . "</td>
                    <td>" . $row['file_type'] . "</td>
                    <td>" . $row['sub_file_type'] . "</td>
                    <td>" . $row['file_name'] . "</td>
                </tr>";
            $id++;
        }
        echo" <input type='hidden' name='file_type' value='$file_type'>
                <button type='button' onclick='viewSelectedFiles()' class='btn view-btn'>View Selected</button>
                <button type='submit' name='action' value='download' class='btn download-btn'>Download Selected</button>&nbsp
                <button type='submit' name='action' value='delete' class='btn delete-btn'>Delete Selected</button>";
        echo "</tbody></table></form>";
    } else {
        echo "<p class='no-files'>No files found for " . ucfirst($file_type) . ".</p>";
    }

    $stmt->close();
}
$conn->close();
?>
</div>

<script>
function viewSelectedFiles() {
    const checkboxes = document.querySelectorAll("input[name='selected_files[]']:checked");
    if (checkboxes.length === 0) {
        alert("Please select at least one file to view.");
        return;
    }

    checkboxes.forEach(cb => {
        const filePath = cb.dataset.filepath;
        window.open(filePath, '_blank');
    });
}

function toggleSelectAll(source) {
    const checkboxes = document.getElementsByName('selected_files[]');
    for (const checkbox of checkboxes) {
        checkbox.checked = source.checked;
    }
}
</script>

</body>
</html>
