<?php
include_once "../../includes/connection.php";
session_start();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['a_username']) && !isset($_SESSION['j_username'])) {
    die("Please login to access this page.");
}
$username = isset($_SESSION['a_username']) ? $_SESSION['a_username'] : $_SESSION['j_username'];

// Get file_type from previous page
$action1 = $_GET['file_type1'] ?? $_POST['file_type1'] ?? '';

// Get selected branch from current page
$selected_branch = $_GET['dept'] ?? $_POST['selected_branch'] ?? '';
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

    function getSafePathDcDown($fileStr) {
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
            $safePath = getSafePathDcDown($files[0]);
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
                    $safePath = getSafePathDcDown($file);
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
            $safePath = getSafePathDcDown($file);
            $stmt = $conn->prepare("DELETE FROM dept_files WHERE file_path = ?");
            $stmt->bind_param("s", $decoded);
            $stmt->execute();
            if ($safePath) {
                unlink($safePath);
            }
        }
        echo "<script>alert('Selected files deleted.'); </script>";
    }
}


if (isset($_POST['export_excel']) && !empty($selected_branch)) {
    ob_end_clean();
    ob_start();
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=files_export.xls");
    echo "Username\tBranch\tFile Type\tSub File Type\tFile Name\n";

    $sql = "SELECT dept, file_type, sub_file_type, file_name, file_path FROM dept_files WHERE dept = ? AND file_type = ?" . SQL_AND_STATUS_EQ . STATUS_ACCEPTED . "'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $selected_branch, $action1);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "$username\t{$row['dept']}\t{$row['file_type']}\t{$row['sub_file_type']}\t{$row['file_name']}\n";
    }

    exit;
}

include_once "../../includes/header.php";
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php
    echo htmlspecialchars($action1); ?>

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
            margin-top: 00px;
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

        /* Navigation */
        .navbar {
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 100px;
            border-bottom: 1px solid #eee;

            margin-top: 56px;
            background-color: white;
            font-size: larger;
        }

        .nav-container {
            margin-left: 100px;
            max-width: 80rem;
            padding: 0 1rem;
        }

        #filter {
            background-color: rgb(138, 30, 113);
        }

        .sp {
            color: blue;
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

        #mergeBtn {
            color: black;
            font-weight: normal;
            background-color: #d4edda;
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: not-allowed;
            transition: all 0.3s ease;
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
            background-color: rgb(145, 30, 158);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
        }

        .excel-btn {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
        }

        .excel-btn:hover {
            background-color: #218838;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
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
                <span class="sp">&nbsp; >> &nbsp; </span><span class="sid"><a
                        href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>"
                        class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <?php if (isset($_SESSION['j_username'])): ?>
                    <span class="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                            href="../jr_assistant/jr_acd_year.php?dept=<?php echo urlencode((string)$dept); ?>"
                            class="home-icon">jr_assistant</a></span>
                <?php else: ?>
                    <span class="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                            href="dc_acd_year.php?dept=<?php echo urlencode((string)$dept); ?>" class="home-icon">dept_coordinator</a></span>
                <?php endif; ?>
                <span class="sp">&nbsp; >> &nbsp;</span><span class="main"><a href="#"
                        class="main-a">Dept_Files(<?php echo htmlspecialchars($action1); ?>) </a></span>
                <span class="sp">&nbsp; >> &nbsp;</span>
            </div>
        </div>
    </nav>

    <div class="container11">
        <h1>Retrieve <?php echo htmlspecialchars($action1); ?> Files</h1>

        <div class="filter-section">
            <form method="POST" class="filter-form">
                <?php
                $preselected_branch = $_GET['dept'] ?? $_POST['selected_branch'] ?? '';
                if ($preselected_branch) {
                    echo '<input type="hidden" name="selected_branch" value="' . htmlspecialchars($preselected_branch) . '">';
                } else {
                    ?>
                    <label for="selected_branch">Select Department:</label>
                    <select name="selected_branch" id="selected_branch" required>
                        <option value="" disabled selected>-- Select Branch --</option>
                        <option value="CSE" <?= $selected_branch === 'CSE' ? 'selected' : '' ?>>CSE</option>
                        <option value="AIML" <?= $selected_branch === 'AIML' ? 'selected' : '' ?>>AIML</option>
                        <option value="AIDS" <?= $selected_branch === 'AIDS' ? 'selected' : '' ?>>AIDS</option>
                        <option value="IT" <?= $selected_branch === 'IT' ? 'selected' : '' ?>>IT</option>
                        <option value="ECE" <?= $selected_branch === 'ECE' ? 'selected' : '' ?>>ECE</option>
                        <option value="EEE" <?= $selected_branch === 'EEE' ? 'selected' : '' ?>>EEE</option>
                        <option value="MECH" <?= $selected_branch === 'MECH' ? 'selected' : '' ?>>MECH</option>
                        <option value="CIVIL" <?= $selected_branch === 'CIVIL' ? 'selected' : '' ?>>CIVIL</option>
                        <option value="BSH" <?= $selected_branch === 'BSH' ? 'selected' : '' ?>>BSH</option>
                    </select>
                <?php } ?>
                <input type="hidden" name="file_type1" value="<?= htmlspecialchars($action1) ?>">
                <button type="submit" class="filter-button">Show Files</button>
            </form>
        </div>
    </div>

    <div class="container111">
        <?php
        if (!empty($selected_branch)) {
            $sql = "SELECT file_type, sub_file_type, file_name, file_path FROM dept_files WHERE dept = ? AND file_type = ?" . SQL_AND_STATUS_EQ . STATUS_ACCEPTED . "'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $selected_branch, $action1);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<h2>" . htmlspecialchars(ucfirst($selected_branch)) . " Department - " . htmlspecialchars(ucfirst($action1)) . " Files</h2>";
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
                $id = 1;
                while ($row = $result->fetch_assoc()) {
                    $file_path = htmlspecialchars($row['file_path'], ENT_QUOTES);
                    echo "<tr>
                    <td><input type='checkbox' name='selected_files[]' value='" . urlencode($file_path) . "' data-filepath=\"$file_path\" onchange='trackOrder(event)'></td>

                    
                    <td>" . htmlspecialchars($username) . "</td>
                    <td>" . htmlspecialchars($selected_branch) . "</td>
                    <td>" . htmlspecialchars($row['sub_file_type']) . "</td>
                    <td>" . htmlspecialchars($row['file_name']) . "</td>
                </tr>";
                    $id++;
                }

                echo "<input type='hidden' name='selected_branch' value='" . htmlspecialchars($selected_branch) . "'>";
                echo "<input type='hidden' name='file_type1' value='" . htmlspecialchars($action1) . "'>";
                echo " <button type='submit' name='export_excel' class='btn excel-btn'>Export to Excel</button> <br>";
                echo "<button type='button' onclick='viewSelectedFiles()' class='btn view-btn'>View Selected</button>
              <button type='submit' name='action' value='download' class='btn download-btn'>Download Selected</button>&nbsp
              <button type='button' id='mergeBtn' class='merge' onclick='mergePDFs()' disabled>Merge PDFs</button>&nbsp
              <button type='submit' name='action' value='delete' class='btn delete-btn'>Delete Selected</button><br>";
                echo "<button type='button' class='merge' id='mergedFileButton' onclick='viewMergedFile()' style='display:none;'>View Merged File</button>";
                echo "</tbody></table></form>";
            } else {
                echo "<p class='no-files'>No files found for " . htmlspecialchars(ucfirst($selected_branch)) . " department in " . htmlspecialchars(ucfirst($action1)) . " category.</p>";
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
            console.log("Merging PDFs:", selectedOrder);
            if (selectedOrder.length < 2) {
                alert("Please select at least two PDFs to merge.");
                return;
            }

            const { PDFDocument } = PDFLib;
            const mergedPdf = await PDFDocument.create();

            for (const fileUrl of selectedOrder) {
                try {
                    // Add ../ to the file path
                    const fixedFileUrl = `${fileUrl}`;
                    console.log("Fetching file:", fixedFileUrl);
                    const response = await fetch(fixedFileUrl);
                    if (!response.ok) {
                        throw new Error(`Failed to fetch ${fixedFileUrl}`);
                    }
                    const fileArrayBuffer = await response.arrayBuffer();
                    const pdf = await PDFDocument.load(fileArrayBuffer);
                    const pages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                    pages.forEach(page => mergedPdf.addPage(page));
                } catch (error) {
                    console.error("Error fetching file:", fileUrl, error);
                    alert("Failed to load " + fileUrl);
                    return;
                }
            }

            const mergedPdfFile = await mergedPdf.save();

            // Send merged file to PHP backend
            const formData = new FormData();
            formData.append("merged_pdf", new Blob([mergedPdfFile], { type: "application/pdf" }));

            fetch("save_merged_pdf.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.fileUrl) {
                        console.log("Merged file saved at:", data.fileUrl);
                        document.getElementById("mergedFileButton").style.display = "block";
                        document.getElementById("mergedFileButton").setAttribute("data-url", data.fileUrl);
                    } else {
                        console.error("Error saving merged PDF:", data.error);
                        alert("Failed to save the merged file.");
                    }
                })
                .catch(error => {
                    console.error("Error sending merged PDF:", error);
                    alert("Failed to send the merged file.");
                });
        }

        function viewMergedFile() {
            let url = document.getElementById("mergedFileButton").getAttribute("data-url");
            if (url) {
                url = url; // Add '../' to the URL
                window.open(url, "_blank");
            } else {
                alert("No merged file found.");
            }
        }
    </script>


</body>

</html>