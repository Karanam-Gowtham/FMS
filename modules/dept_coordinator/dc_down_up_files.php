<?php
include "../../includes/connection.php";
session_start();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['a_username']) && !isset($_SESSION['j_username'])) {
    die("Please login to access this page.");
}
$username = isset($_SESSION['a_username']) ? $_SESSION['a_username'] : $_SESSION['j_username'];

$event = $_GET['event'] ?? '';

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

    function getSafePathDcUp($fileStr) {
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
            $safePath = getSafePathDcUp($files[0]);
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
            $zip = new ZipArchive();
            $zipName = "zip_file" . time() . ".zip";
            $zipPath = sys_get_temp_dir() . "/" . $zipName;

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                foreach ($files as $file) {
                    $safePath = getSafePathDcUp($file);
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
            $safePath = getSafePathDcUp($file);
            $stmt = $conn->prepare("DELETE FROM dc_up_files WHERE file_path = ? AND Username = ?");
            $stmt->bind_param("ss", $decoded, $username);
            $stmt->execute();
            if ($safePath) {
                unlink($safePath);
            }
        }
        echo "<script>alert('Selected files deleted.');</script>";
    }
}

include "../../includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Retrieve Files</title>
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>
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

        /* Navigation */
        .navbar {
            margin-top: -80px;
            font-size: larger;
        }

        #sp {
            color: blue;
        }

        .nav-container {
            background-color: white;
            width: 150vw;
            /* margin-top moved to .navbar */
            padding: 0 1rem;
        }

        .nav-items {
            margin-left: 30px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
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

        #mergeBtn.active {
            color: white;
            font-weight: bold;
            background-color: rgb(59, 40, 167);
            cursor: pointer;
        }

        #mergeBtn:disabled {
            opacity: 0.6;
        }

        .merge {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: rgb(131, 116, 214);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
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
                <span id="sp">&nbsp; >> &nbsp; </span><span class="sid"><a
                        href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>"
                        class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <?php if (isset($_SESSION['j_username'])): ?>
                    <span id="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                            href="../jr_assistant/jr_acd_year.php?dept=<?php echo urlencode((string)$dept); ?>"
                            class="home-icon">jr_assistant</a></span>
                <?php else: ?>
                    <span id="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                            href="dc_acd_year.php?dept=<?php echo urlencode((string)$dept); ?>" class="home-icon">dept_coordinator</a></span>
                <?php endif; ?>
                <span id="sp">&nbsp; >> &nbsp;</span><span class="main"><a href="#"
                        class="main-a"><?php echo "$event" ?>_Files</a></span>
                <span id="sp">&nbsp; >> &nbsp;</span>
            </div>
        </div>
    </nav>
    <div class="container11">
        <h1>Retrieve <?php echo "$event"; ?> Files</h1>

        <?php
        $selected_file_type = $_POST['selected_file_type'] ?? '';
        $file_options = [];

        if ($event === 'Achievements') {
            $file_options = [
                "FDPS Attended",
                "Papers Published",
                "Patents",
                "FDPS Organized",
                "Conferences Published"
            ];
        } elseif ($event === 'dept') {
            $file_options = [
                "Admin Files",
                "Student Files",
                "Student Activities Files",
                "Faculty Files",
                "Exam Section Files",
                "AMC Meeting Minutes",
                "Board Of Studies"
            ];
        }
        ?>

        <div class="filter-section">
            <form method="POST" class="filter-form">
                <label for="selected_file_type">Select File Type:</label>
                <select name="selected_file_type" id="selected_file_type" required>
                    <option value="" disabled selected>-- Select File Type --</option>
                    <?php
                    foreach ($file_options as $option) {
                        $selected = $selected_file_type === $option ? 'selected' : '';
                        echo "<option value=\"$option\" $selected>$option</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="filter-button">Show Files</button>
            </form>
        </div>
    </div>

    <div class="container111">
        <?php
        if (!empty($selected_file_type)) {
            $sql = "SELECT Username, file_name,acd_year, file_type, file_path FROM dc_up_files WHERE Username = ? AND Main_file_type = ? AND file_type = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $event, $selected_file_type);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<h2>Viewing: " . htmlspecialchars($selected_file_type) . "</h2>";
                echo "<form method='post' action=''>";
                echo "<table border='1'>
                    <thead>
                        <tr>
                            <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                        
                            <th>Username</th>
                            <th>Academic Year</th>
                            <th>File Type</th>
                            <th>File Name</th>
                        </tr>
                    </thead>
                    <tbody>";

                $id = 1;
                while ($row = $result->fetch_assoc()) {
                    $file_path = htmlspecialchars($row['file_path'], ENT_QUOTES);
                    echo "<tr>
                        <td><input type='checkbox' name='selected_files[]' value='" . urlencode($file_path) . "' data-filepath=\"$file_path\" onclick='trackOrder(event)'></td>
                        
                        <td>" . htmlspecialchars($row['Username']) . "</td>
                        <td>" . htmlspecialchars($row['acd_year']) . "</td>
                        <td>" . htmlspecialchars($row['file_type']) . "</td>
                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                    </tr>";
                    $id++;
                }

                echo "</tbody></table>";
                echo "<br>";
                echo "<input type='hidden' name='file_type' value='" . htmlspecialchars($selected_file_type) . "'>";
                echo "<button type='button' onclick='viewSelectedFiles()' class='btn view-btn'>View Selected</button> ";
                echo "<button type='submit' name='action' value='download' class='btn download-btn'>Download Selected</button> ";
                echo "<button type='button' id='mergeBtn' class='merge' onclick='mergePDFs()' disabled>Merge PDFs</button>&nbsp";
                echo "<button type='submit' name='action' value='delete' class='btn delete-btn'>Delete Selected</button>";
                echo "<button type='button' class='merge' id='mergedFileButton' onclick='viewMergedFile()' style='display:none;'>View Merged File</button>";

                echo "</form>";
            } else {
                echo "<p class='no-files'>No files found for '$selected_file_type'.</p>";
            }

            $stmt->close();
        }
        $conn->close();
        ?>
    </div>

    <script>
        let selectedOrder = [];

        function trackOrder(event) {
            const filePath = event.target.dataset.filepath;
            if (event.target.checked) {
                selectedOrder.push(filePath);
            } else {
                selectedOrder = selectedOrder.filter(path => path !== filePath);
            }
            updateMergeButton();
        }

        function updateMergeButton() {
            const mergeBtn = document.getElementById('mergeBtn');
            if (selectedOrder.length > 1) {
                mergeBtn.disabled = false;
                mergeBtn.classList.add("active");
            } else {
                mergeBtn.disabled = true;
                mergeBtn.classList.remove("active");
            }
        }

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
            selectedOrder = [];

            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
                trackOrder({ target: checkbox });
            });

            updateMergeButton();
        }

        async function mergePDFs() {
            const { PDFDocument } = PDFLib;
            const mergedPdf = await PDFDocument.create();

            for (const url of selectedOrder) {
                const response = await fetch(url);
                const pdfBytes = await response.arrayBuffer();
                const pdf = await PDFDocument.load(pdfBytes);
                const copiedPages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                copiedPages.forEach((page) => mergedPdf.addPage(page));
            }

            const mergedPdfBytes = await mergedPdf.save();
            const blob = new Blob([mergedPdfBytes], { type: 'application/pdf' });
            const url = URL.createObjectURL(blob);

            // Store in a hidden input or global variable
            window.mergedPdfUrl = url;

            // Enable "View Merged File" button
            document.getElementById("mergedFileButton").style.display = "inline-block";
            document.getElementById("mergedFileButton").disabled = false;

            // Optionally: directly open the merged file
            // window.open(url, '_blank');
        }

        function viewMergedFile() {
            if (window.mergedPdfUrl) {
                window.open(window.mergedPdfUrl, '_blank');
            } else {
                alert("Merged file not available.");
            }
        }
    </script>

</body>

</html>