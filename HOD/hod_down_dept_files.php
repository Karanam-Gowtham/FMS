<?php
include_once "../includes/connection.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['h_username']) && !isset($_SESSION['admin'])) {
    die("Please login to access this page.");
}

function fixPath($p)
{
    if (empty($p)) {
        return "";
    }
    $p = htmlspecialchars_decode($p);
    $p = str_replace('\\', '/', $p);
    if (preg_match('/uploads\/.*/', $p, $matches)) {
        return "../" . $matches[0];
    }
    return $p;
}

$username = $_SESSION['h_username'] ?? $_SESSION['admin'];

// Get selected branch
if (isset($_GET['dept'])) {
    $selected_branch = $_GET['dept'];
} elseif (isset($_POST['selected_branch'])) {
    $selected_branch = $_POST['selected_branch'];
} else {
    $selected_branch = $_SESSION['dept'] ?? '';
}

// Get file_type1 (action/category)
if (isset($_GET['event'])) {
    $action1 = $_GET['event'];
} elseif (isset($_GET['file_type1'])) {
    $action1 = $_GET['file_type1'];
} else {
    $action1 = isset($_POST['file_type1']) ? $_POST['file_type1'] : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_files']) && isset($_POST['action'])) {
    $action = $_POST['action'];
    $files = $_POST['selected_files'];

    function getSafePathHodDeptDown($fileStr) {
        $filename = basename(htmlspecialchars_decode(urldecode($fileStr), ENT_QUOTES));
        $dirs = ['../../uploads/', '../../uploads1/', '../uploads/', '../uploads1/', 'uploads/', 'uploads1/'];
        foreach ($dirs as $dir) {
            if (file_exists($dir . $filename) && is_file($dir . $filename)) {
                return $dir . $filename;
            }
        }
        return false;
    }

    if ($action === 'download') {
        if (count($files) === 1) {
            $safePath = getSafePathHodDeptDown($files[0]);

            if ($safePath) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Content-Disposition: attachment; filename=\"" . basename($safePath) . "\"");
                header('Content-Length: ' . filesize($safePath));
                readfile($safePath);
                exit();
            } else {
                echo "File not found";
            }
        } else {
            $zip = new ZipArchive();
            $zipName = "zip_file" . time() . ".zip";
            $zipPath = sys_get_temp_dir() . "/" . $zipName;

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                foreach ($files as $file) {
                    $safePath = getSafePathHodDeptDown($file);
                    if ($safePath) {
                        $zip->addFile($safePath, basename($safePath));
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
}

include_once "header_hod.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Retrieve Files</title>
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js" integrity="sha256-D5pcrQeUHwgmWGyU4InYm5GMRuXBfPLVo8b2ZuO8aU8=" crossorigin="anonymous"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color: #fff;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            font-weight: 600;
            margin-top: 0;
            color: darkblue;
        }

        .container11,
        .container111 {
            margin-left: 50px;
            width: 90%;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: #333;
        }

        .container11 {
            margin-top: 100px;
        }

        .container111 {
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .filter-section {
            margin-top: 30px;
            margin-left: 50px;
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

        .filter-button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px;
            text-align: center;
            border-bottom: 2px solid #ddd;
            color: #333;
        }

        th {
            background: #1e3c72;
            color: white;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background: #f0f5ff;
        }

        tr:hover {
            background: #d6e4ff;
            transition: 0.3s;
        }

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

        .view-btn {
            background: #42a5f5;
            color: white;
            margin-right: 10px;
        }

        .view-btn:hover {
            background: #1e88e5;
        }

        .download-btn {
            background: #66bb6a;
            color: white;
        }

        .download-btn:hover {
            background: #43a047;
        }

        .navbar {
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 100px;
            border-bottom: 1px solid #eee;

            background-color: white;
            font-size: larger;
        }

        .nav-container {
            /* margin-top moved to .navbar */
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

        .main-a:hover {
            color: rgb(182, 64, 211);
        }

        .home-icon {
            color: rgb(30, 58, 138);
            transition: color 0.2s;
        }

        .home-icon:hover {
            color: rgb(29, 78, 216);
        }

        #sp {
            color: blue;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span>&nbsp; >> &nbsp; </span><span class="sid"><a
                        href="../admin/admins.php?dept=<?php echo urlencode($selected_branch); ?>"
                        class="home-icon">Department(<?php echo htmlspecialchars($selected_branch); ?>)</a></span>
                <span class="sp-divider">&nbsp; >> &nbsp;</span><span class="sid"><a href="see_uploads.php"
                        class="home-icon">HOD</a></span>
                <span class="sp-divider">&nbsp; >> &nbsp;</span><span class="main"><a href="#"
                        class="main-a">Dept_Files(<?php echo htmlspecialchars($action1); ?>) </a></span>
            </div>
        </div>
    </nav>

    <div class="container11">
        <h1>Retrieve <?php echo htmlspecialchars($action1); ?> Files</h1>
        <div class="filter-section">
            <form method="POST" class="filter-form">
                <?php if ($selected_branch): ?>
                    <input type="hidden" name="selected_branch" value="<?php echo htmlspecialchars($selected_branch); ?>">
                <?php else: ?>
                    <label for="selected_branch">Select Department:</label>
                    <select name="selected_branch" id="selected_branch" required>
                        <option value="" disabled selected>-- Select Branch --</option>
                        <option value="CSE">CSE</option>
                        <option value="AIML">AIML</option>
                        <option value="AIDS">AIDS</option>
                        <option value="IT">IT</option>
                        <option value="ECE">ECE</option>
                        <option value="EEE">EEE</option>
                        <option value="MECH">MECH</option>
                        <option value="CIVIL">CIVIL</option>
                        <option value="BSH">BSH</option>
                    </select>
                <?php endif; ?>
                <input type="hidden" name="file_type1" value="<?= htmlspecialchars($action1) ?>">
                <button type="submit" class="filter-button">Show Files</button>
            </form>
        </div>
    </div>

    <div class="container111">
        <?php
        if (!empty($selected_branch)) {
            $sql = "SELECT username, file_type, sub_file_type, file_name, file_path FROM dept_files WHERE dept = ? AND file_type = ? AND status = 'Accepted'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $selected_branch, $action1);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<h2>" . htmlspecialchars(ucfirst((string)$selected_branch)) . " Department - " . htmlspecialchars(ucfirst((string)$action1)) . " Files</h2>";
                echo "<form method='post' action=''>";

                echo "<table border='1'>
                <thead>
                    <tr>
                        <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                        <th>Username</th>
                        <th>Branch</th>
                        <th>File Type</th>
                        <th>File Name</th>
                    </tr>
                </thead>
                <tbody>";
                while ($row = $result->fetch_assoc()) {
                    $file_path = $row['file_path'];
                    $fixed_path = fixPath($file_path);
                    echo "<tr>
                    <td><input type='checkbox' name='selected_files[]' value='" . urlencode($file_path) . "' data-filepath=\"$fixed_path\"></td>
                    <td>" . htmlspecialchars($row['username']) . "</td>
                    <td>" . htmlspecialchars((string)$selected_branch) . "</td>
                    <td>" . htmlspecialchars($row['sub_file_type']) . "</td>
                    <td>" . htmlspecialchars($row['file_name']) . "</td>
                </tr>";
                }

                echo "<input type='hidden' name='selected_branch' value='" . htmlspecialchars($selected_branch) . "'>";
                echo "<input type='hidden' name='file_type1' value='" . htmlspecialchars($action1) . "'>";
                echo "</tbody></table><br>";
                echo "<div style='text-align:center;'>";
                echo "<button type='button' onclick='viewSelectedFiles()' class='btn view-btn'>View Selected</button>
              <button type='submit' name='action' value='download' class='btn download-btn'>Download Selected</button>";
                echo "</div>";
                echo "</form>";
            } else {
                echo "<p class='no-files'>No files found for " . htmlspecialchars(ucfirst((string)$selected_branch)) . " department in " . htmlspecialchars(ucfirst((string)$action1)) . " category.</p>";
            }
            $stmt->close();
        }
        $conn->close();
        ?>
    </div>

    <script>
        async function viewSelectedFiles() {
            const checkboxes = document.querySelectorAll("input[name='selected_files[]']:checked");
            if (checkboxes.length === 0) {
                alert("Please select at least one file to view.");
                return;
            }
            for (const cb of checkboxes) {
                const filePath = cb.dataset.filepath;
                window.open('view_file_hod.php?file_path=' + encodeURIComponent(filePath), '_blank');
                await new Promise(r => setTimeout(r, 100));
            }
        }
        function toggleSelectAll(source) {
            const checkboxes = document.getElementsByName('selected_files[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
</body>

</html>