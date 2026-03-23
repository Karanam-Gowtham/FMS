<?php
include_once "../../includes/connection.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['username']) && !isset($_SESSION['j_username']) && !isset($_SESSION['h_username'])) {
    die("Please login to access this page.");
}
$username = $_SESSION['username'] ?? $_SESSION['h_username'] ?? $_SESSION['j_username'] ?? '';
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

    function getSafePathDeptDown($fileStr) {
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
            // Download single file directly
            $safePath = getSafePathDeptDown($files[0]);
            if ($safePath) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Content-Disposition: attachment; filename=\"" . basename($safePath) . "\"");
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($safePath));
                readfile($safePath);
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
                    $safePath = getSafePathDeptDown($file);
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
    } elseif ($action === 'delete') {
        foreach ($files as $file) {
            $decoded = htmlspecialchars_decode(urldecode($file), ENT_QUOTES);
            $safePath = getSafePathDeptDown($file);
            $stmt = $conn->prepare("DELETE FROM dept_files WHERE file_path = ? AND username = ?");
            $stmt->bind_param("ss", $decoded, $username);
            $stmt->execute();

            $stmt_ev = $conn->prepare("DELETE FROM s_events WHERE certificate_path = ? AND Username = ?");
            $stmt_ev->bind_param("ss", $decoded, $username);
            $stmt_ev->execute();

            if ($safePath) {
                unlink($safePath);
            }
        }
        echo "<script>alert('Selected files deleted.'); </script>";
    }
}

include_once "../../includes/header.php";
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

        header {
            position: sticky;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            font-weight: 600;
            margin-top: 00px;
            color: darkblue;
        }

        .container11,
        .container111 {
            margin-top: 50px;
            margin-left: 50px;
            width: 90%;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: #333;
        }

        .container111 {
            margin-bottom: 50px;
        }

        /* Navigation */
        .navbar {
            background-color: white;
            font-size: larger;
        }

        .nav-container {
            margin-left: 100px;
            max-width: 80rem;
            padding: 0 1rem;
        }

        span {
            color: #0056b3;
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
            background: #1e3c72 !important;
            color: white !important;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background: #f0f5ff;
        }

        tr:hover {
            background: #007bff;
            transition: 0.3s;
        }

        tr:hover td {
            color: #fff !important;
        }

        a,
        button,
        .btn {
            text-decoration: none !important;
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

        .delete-btn {
            background: #e74c3c;
            color: white;
        }

        .delete-btn:hover {
            background: #c0392b;
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
                <span>&nbsp; >> &nbsp; </span><span class="sid"><a
                        href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>"
                        class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <?php if (isset($_SESSION['j_username'])): ?>
                    <span class="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                            href="../jr_assistant/jr_acd_year.php?dept=<?php echo urlencode((string)$dept); ?>" class="home-icon"> Jr
                            Assistant </a></span>
                <?php else: ?>
                    <span>&nbsp; >> &nbsp; </span><span class="sid"><a
                            href="../faculty/acd_year.php?dept=<?php echo urlencode($dept); ?>" class="home-icon"> Faculty
                        </a></span>
                <?php endif; ?>
                <span>&nbsp; >> &nbsp; </span><span class="main"> <a href="#" class="main-a"> My Dept Files </a></span>
                <span>&nbsp; >> &nbsp; </span>
            </div>
        </div>
    </nav>

    <div class="container11">
        <h1>Retrieve Files</h1>

        <?php
        $selected_file_type = $_POST['selected_file_type'] ?? '';
        $is_jr_assistant = isset($_SESSION['j_username']);
        $allowed_file_types = $is_jr_assistant
            ? ['Dept Meeting Minutes', 'admin', 'student', 'calendar']
            : ['admin', 'faculty', 'student', 'exam', 'student_act', AMC_MTG_MINS, BOS_MTG_MINS];
        ?>
        <div class="filter-section">
            <form method="POST" class="filter-form">
                <label for="selected_file_type">Select File Type:</label>
                <select name="selected_file_type" id="selected_file_type" required>
                    <option value="" disabled selected>-- Select File Type --</option>
                    <?php
                    foreach ($allowed_file_types as $type) {
                        $selected = ($selected_file_type === $type) ? 'selected' : '';
                        $label = $type;
                        if ($type === 'admin') {
                            $label = "Admin Files";
                        }
                        if ($type === 'faculty') {
                            $label = "Faculty Files";
                        }
                        if ($type === 'student') {
                            $label = "Student Related Files";
                        }
                        if ($type === 'exam') {
                            $label = "Exam Section Files";
                        }
                        if ($type === 'student_act') {
                            $label = "Student Activities Files";
                        }
                        if ($type === AMC_MTG_MINS) {
                            $label = AMC_MTG_MINS;
                        }
                        if ($type === BOS_MTG_MINS) {
                            $label = "Board Of Studies (BOS)";
                        }
                        if ($type === 'calendar') {
                            $label = "Academic Calendar";
                        }
                        echo "<option value='$type' $selected>$label</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="filter-button">Show Files</button>
            </form>
        </div>
    </div>

    <div class="container111">
        <?php
        $file_types_to_show = $selected_file_type ? [$selected_file_type] : [];
        foreach ($file_types_to_show as $file_type) {
            if ($file_type === 'student_act') {
                $sql = "SELECT 'student_act' as file_type, branch as dept, acd_year as academic_year, event_name as sub_file_type, event_name as file_name, certificate_path as file_path, status, '' as meeting_no FROM s_events WHERE Username = ? AND status = 'Accepted'";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
            } else {
                $sql = "SELECT file_type, dept, academic_year, sub_file_type, file_name, file_path, status, meeting_no FROM
        dept_files WHERE username = ? AND file_type = ? AND status = 'Accepted'";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $username, $file_type);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Determine display label for heading
                $heading = $file_type;
                if ($file_type === 'admin') {
                    $heading = "Admin Files";
                }
                if ($file_type === 'faculty') {
                    $heading = "Faculty Files";
                }
                if ($file_type === 'student') {
                    $heading = "Student Related Files";
                }
                if ($file_type === 'exam') {
                    $heading = "Exam Section Files";
                }
                if ($file_type === 'student_act') {
                    $heading = "Student Activities Files";
                }
                if ($file_type === AMC_MTG_MINS) {
                    $heading = AMC_MTG_MINS;
                }
                if ($file_type === BOS_MTG_MINS) {
                    $heading = "Board Of Studies (BOS)";
                }

                echo "<h2>" . htmlspecialchars($heading) . "</h2>";
                echo "<form method='post' action=''>";

                echo "<table border='1'>
                <thead>
                    <tr>
                        <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Dept</th>
                        <th>Academic Year / Meeting No</th>
                        <th>File Type</th>
                        <th>Sub File Type </th>
                        <th>File Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";
                $id = 1;
                while ($row = $result->fetch_assoc()) {
                    $file_path = htmlspecialchars($row['file_path'], ENT_QUOTES);
                    $status = $row['status'] ?? 'Pending';
                    $statusColor = 'orange';
                    if ($status === 'Accepted') {
                        $statusColor = 'green';
                    } elseif ($status === 'Rejected') {
                        $statusColor = 'red';
                    }

                    echo "<tr>
                        <td><input type='checkbox' name='selected_files[]' value='" . urlencode($file_path) . "'
                                data-filepath=\"$file_path\"></td>
                        <td>" . htmlspecialchars($id) . "</td>
                        <td>" . htmlspecialchars($username) . "</td>
                        <td>" . htmlspecialchars($row['dept']) . "</td>
                        <td>" . htmlspecialchars(($row['file_type'] === 'Dept Meeting Minutes' && $row['meeting_no']) ? "Meeting " .
                        $row['meeting_no'] : $row['academic_year']) . "</td>
                        <td>" . htmlspecialchars($row['file_type']) . "</td>
                        <td>" . htmlspecialchars($row['sub_file_type']) . "</td>
                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                        <td style='color: $statusColor; font-weight: bold;'>" . htmlspecialchars($status) . "</td>
                    </tr>";
                    $id++;
                }
                echo " <input type='hidden' name='file_type' value='" . htmlspecialchars($file_type) . "'>
                    <button type='button' onclick='viewSelectedFiles()' class='btn view-btn'>View Selected</button>
                    <button type='submit' name='action' value='download' class='btn download-btn'>Download
                        Selected</button>&nbsp
                    <button type='submit' name='action' value='delete' class='btn delete-btn'>Delete Selected</button>";
                echo "
                </tbody>
            </table>
        </form>";
            } else {
                echo "<p class='no-files'>No files found for " . htmlspecialchars(ucfirst($file_type)) . ".</p>";
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