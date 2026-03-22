<?php
include "../includes/connection.php";
require_once "../includes/constants.php";


if (!isset($_SESSION['c_username'])) {
    die("You need to log in to view your uploads.");
}

$academic_year = isset($_GET['year']) ? htmlspecialchars($_GET['year']) : '';
$event = isset($_GET['event']) ? htmlspecialchars($_GET['event']) : '';
$designation = isset($_GET['designation']) ? htmlspecialchars($_GET['designation']) : '';
$criteria = isset($_GET['criteria']) ? htmlspecialchars($_GET['criteria']) : 'Not Selected';

$c_username = $_SESSION['c_username'];

// Handle file actions
if (isset($_POST['action']) && isset($_POST['selected_files'])) {
    $selectedFiles = $_POST['selected_files'];
    $action = $_POST['action'];
    
    if ($action == 'delete') {
        foreach ($selectedFiles as $fileId) {
            $sql = "SELECT file_path FROM a_c_files WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();
            if ($file) {
                if (file_exists($file['file_path'])) {
                    unlink($file['file_path']); // Delete the actual file
                }
                $sql = "DELETE FROM a_c_files WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $fileId);
                $stmt->execute();
            }
        }
        echo "<script>alert('Files deleted successfully.'); window.location.href='my_uploads.php';</script>";
    
    
    } else if ($action == 'download') {
        if (!empty($selectedFiles)) {
            if (count($selectedFiles) == 1) {
                $fileId = $selectedFiles[0];
                $sql = "SELECT file_path FROM a_c_files WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $fileId);
                $stmt->execute();
                $result = $stmt->get_result();
    
                if ($file = $result->fetch_assoc()) {
                    $filePath = $file['file_path'];
                    $fileName = basename($filePath);
    
                    if (file_exists($filePath)) {
                        if (ob_get_length()) {
                            ob_end_clean();
                        }
                        header(TYPE_OCTET_STREAM);
                        header(HEADER_CONTENT_DISPOSITION . $fileName . '"');
                        header(HEADER_CONTENT_LENGTH . filesize($filePath));
                        flush();
                        readfile($filePath);
                        exit;
                    } else {
                        echo "File not found.";
                    }
                }
            } else {
                $zip = new ZipArchive();
                $zipFileName = "downloads.zip";
                $zipFilePath = "uploads1/" . $zipFileName;
    
                if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                    $selectedFiles = array_reverse($selectedFiles);
                    $placeholders = implode(',', array_fill(0, count($selectedFiles), '?'));
                    $sql = "SELECT file_path, file_name FROM a_c_files WHERE id IN ($placeholders)";
    
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(str_repeat("i", count($selectedFiles)), ...$selectedFiles);
                    $stmt->execute();
                    $result = $stmt->get_result();
    
                    $files = [];
                    while ($file = $result->fetch_assoc()) {
                        $files[] = $file;
                    }
    
                    foreach ($files as $file) {
                        $filePath = $file['file_path'];
                        if (file_exists($filePath)) {
                            $zip->addFile($filePath, basename($filePath));
                        }
                    }
                    $zip->close();
    
                    header('Content-Type: application/zip');
                    header(HEADER_CONTENT_DISPOSITION . $zipFileName . '"');
                    header(HEADER_CONTENT_LENGTH . filesize($zipFilePath));
                    readfile($zipFilePath);
                    unlink($zipFilePath);
                    exit;
                } else {
                    echo "Failed to create ZIP file.";
                }
            }
        }
    }
}

// Handle Excel Download
if (isset($_POST['download_excel'])) {
    header(TYPE_EXCEL);
    header('Content-Disposition: attachment;filename="my_uploads.xls"');
    header('Cache-Control: max-age=0');
    
    echo "ID\tUsername\tFaculty Name\tAcademic Year\tFilename\tUploaded At\tCriteria\tCriteria No\n";
    
    $sql = "SELECT * FROM a_c_files WHERE username = ? ORDER BY uploaded_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $c_username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $id = 1;
    while ($row = $result->fetch_assoc()) {
        $uploadedAt = new DateTime($row['uploaded_at']);
        $formattedDateTime = $uploadedAt->format('Y/m/d H:i:s');
        
        echo $id . "\t";
        echo $row['username'] . "\t";
        echo $row['Faculty_name'] . "\t";
        echo $row['academic_year'] . "\t";
        echo $row['file_name'] . "\t";
        echo $formattedDateTime . "\t";
        echo $row['criteria'] . "\t";
        echo $row['criteria_no'] . "\n";
        
        $id++;
    }
    exit();
}


include 'header_admin.php';


$mergedFolder = "uploads/merged/";

// Function to delete folder and its contents
function deleteFolder($folder) {
    if (!is_dir($folder)) {
        return;
    }
    
    $files = array_diff(scandir($folder), ['.', '..']);
    foreach ($files as $file) {
        $filePath = $folder . DIRECTORY_SEPARATOR . $file;
        if (is_dir($filePath)) {
            deleteFolder($filePath);
        } else {
            unlink($filePath);
        }
    }
    rmdir($folder);
}

// Delete merged folder when the page loads
if (is_dir($mergedFolder)) {
    deleteFolder($mergedFolder);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Uploads</title>
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --success-color: #16a34a;
            --danger-color: #dc2626;
            --background-color: #f1f5f9;
            --card-background: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background-color);
            color: var(--text-primary);
        }

        .container11 {
            margin: 0px auto 40px;
            max-width: 1300px;
            padding: 30px;
            background: var(--card-background);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        .header-section {
            margin-top:30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h1 {
            margin: 0;
            color: var(--text-primary);
            font-size: 2.25rem;
            font-weight: 700;
        }

        .download-excel {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .download-excel:hover {
            background-color: var(--secondary-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 20px 0;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            text-align: left;
            padding: 16px;
            border: 1px solid var(--border-color);
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        tr:hover {
            background-color: #f1f5f9;
        }

        button {
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 0.875rem;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        button#view {
            background-color: var(--success-color);
            color: white;
        }

        button#view:hover {
            filter: brightness(110%);
            transform: translateY(-1px);
        }

        button#down {
            background-color: var(--primary-color);
            color: white;
        }

        .merge{
            margin-top: 10px;
            background-color: #28a745;
        }

        button#down:hover {
            filter: brightness(110%);
            transform: translateY(-1px);
        }

        button#del {
            background-color: var(--danger-color);
            color: white;
        }

        button#del:hover {
            filter: brightness(110%);
            transform: translateY(-1px);
        }

        .no-files {
            text-align: center;
            color: var(--text-secondary);
            font-style: italic;
            padding: 24px;
        }

        .error-message {
            background-color: #fee2e2;
            color: var(--danger-color);
            padding: 12px;
            border-radius: 6px;
            margin: 16px 0;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .container11 {
                margin: 80px 20px 40px;
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .header-section {
                flex-direction: column;
                gap: 16px;
            }

            h1 {
                font-size: 1.875rem;
            }
        }
                                 /* Navigation */
                                 .navbar {
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 80px;
            border-bottom: 1px solid #eee;
 
        font-size: larger;
    }

    .nav-container {
        background-color: rgb(244, 237, 237);
        width:150vw;
         /* margin-top moved to .navbar */
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

    </style>
</head>
<body>
<nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../modules/central/c_login_n.php?event=<?php echo urlencode($event); ?>" class="home-icon">Central (<?php echo htmlspecialchars($event); ?>)</a></span>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="../modules/central/c_aqar_files.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon"><?php echo htmlspecialchars($designation); ?></a></span>
                <span class="sid">&nbsp; >> &nbsp;  </span><span class="sid"><a href="criteria_cent_a.php?year=<?php echo urlencode($academic_year); ?>&criteria=<?php echo urlencode($criteria); ?>&designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon">Criteria <?php echo htmlspecialchars($criteria); ?></a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a">My Uploads  </a></span>

            </div>
        </div>
    </nav>
    <div class="container11">

        <div class="header-section">
            <h1>My Uploads</h1>
            <form method="POST">
                <button type="submit" name="download_excel" class="download-excel">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download Excel
                </button>
            </form>
        </div>
        <form method="POST" action="">
        <table>
            <tr>
                <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
                <th>ID</th>
                <th>Faculty Name</th>
                <th>Academic Year</th>
                <th>Filename</th>
                <th>Criteria</th>
                <th>Criteria No</th>
            </tr>
            <?php
            $sql = "SELECT * FROM a_c_files WHERE username = ? ORDER BY uploaded_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $c_username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $id = 1;
                while ($row = $result->fetch_assoc()) {
                    $fileUrl = $row['file_path'];
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . "' 
                    data-filepath='" . htmlspecialchars($fileUrl, ENT_QUOTES, 'UTF-8') . "' 
                    onchange='trackOrder(event)'></td>";
                    echo "<td>" . $id . "</td>";
                    echo "<td>" . htmlspecialchars($row['Faculty_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['academic_year']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['file_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['criteria']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['criteria_no']) . "</td>";
                    echo "</tr>";
                    $id++;
                }
            } else {
                echo "<tr><td colspan='7' class='no-files'>No files found</td></tr>";
            }
            ?>

            <div>
            <button type="submit"  id="view" name="action" onclick="openFile()">view</button>
                <button type="submit" id="down" name="action" value="download">Download</button>
                <button type="button" id="mergeBtn" class="merg" onclick="mergePDFs()" disabled>Merge PDFs</button>
                <button type="submit" id="del"  name="action" value="delete">Delete</button><br><br>
                <button type="button" class="merge" id="mergedFileButton" onclick="viewMergedFile()" style="display:none;">View Merged File</button>
                
            </div>
            </form>
            <?php
            $conn->close();
            ?>
        </table>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js" integrity="sha256-D5pcrQeUHwgmWGyU4InYm5GMRuXBfPLVo8b2ZuO8aU8=" crossorigin="anonymous"></script>
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

            // Function to view selected file
            function openFile() {
                let checkboxes = document.querySelectorAll("input[name='selected_files[]']:checked");
                if (checkboxes.length === 0) {
                    alert("Please select a file to view.");
                    return;
                }
                let filePath = checkboxes[0].dataset.filepath;
                window.open(filePath, '_blank');
            }

            // Select all checkboxes
            function toggleSelectAll(source) {
                let checkboxes = document.getElementsByName('selected_files[]');
                for (let checkbox of checkboxes) {
                    checkbox.checked = source.checked;
                    trackOrder({ target: checkbox });
                }
            }



        function updateMergeButton() {
            document.getElementById('mergeBtn').disabled = selectedOrder.length < 2;
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
                    console.log("Fetching file:", fileUrl);
                    const response = await fetch(fileUrl);
                    if (!response.ok) {
                        throw new Error(`Failed to fetch ${fileUrl}`);
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

            fetch("./save_merged_pdf.php", {
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
            const url = document.getElementById("mergedFileButton").getAttribute("data-url");
            if (url) {
                window.open(url, "_blank");
            } else {
                alert("No merged file found.");
            }
        }

        
    </script>
</html>
