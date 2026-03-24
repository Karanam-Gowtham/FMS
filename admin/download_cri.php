<?php
include_once "../includes/connection.php";
require_once "../includes/constants.php";


if (!isset($_SESSION['cri_username'])) {
    die("You need to log in to view your uploads.");
}
// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$event = isset($_GET['event']) ? htmlspecialchars($_GET['event']) : '';
$designation = isset($_GET['designation']) ? htmlspecialchars($_GET['designation']) : '';
$criteria = isset($_GET['criteria']) ? htmlspecialchars($_GET['criteria']) : 'Not Selected';

// Set default values for filtering
$academic_year = isset($_POST['academic_year']) ? $_POST['academic_year'] : '';
$criteria = isset($_POST['criteria']) ? $_POST['criteria'] : '';
$subCriteria = isset($_POST['criteria_no']) ? $_POST['criteria_no'] : '';
$branch_s = isset($_POST['branch_s']) ? $_POST['branch_s'] : '';

if (isset($_POST['action']) && isset($_POST['selected_files'])) {
    $criteria = $_POST['criteria'];
    $subCriteria = $_POST['subCriteria'];
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

            // if not found in a_c_files, check a_files
            if (!$file) {
                $sql = "SELECT file_path FROM a_files WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $fileId);
                $stmt->execute();
                $result = $stmt->get_result();
                $file = $result->fetch_assoc();
            }

            if ($file) {
                unlink($file['file_path']);
                $sql = "DELETE FROM a_c_files WHERE id = ?"; 
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $fileId);
                $stmt->execute();

                // Also delete from a_files if present
                $sql = "DELETE FROM a_files WHERE id = ?"; 
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $fileId);
                $stmt->execute();
            }
        }
        echo "<script>alert('Files deleted successfully.');</script>";
    } elseif ($action == 'download' && count($selectedFiles) == 1) {
        $fileId = $selectedFiles[0];
        $sql = "SELECT file_path FROM a_c_files WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $fileId);
        $stmt->execute();
        $result = $stmt->get_result();
        $file = $result->fetch_assoc();

        // if not found, fallback to a_files
        if (!$file) {
            $sql = "SELECT file_path FROM a_files WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();
        }

        if ($file) {
            $filePath = $file['file_path'];
            $fileName = basename($filePath);

            if (file_exists($filePath)) {
                if (ob_get_length()) {
                    ob_end_clean();
                }
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Content-Length: ' . filesize($filePath));
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Content-Transfer-Encoding: binary');

                flush();
                readfile($filePath);
                exit;
            } else {
                echo "File not found.";
            }
        }
    } elseif ($action == 'download' && count($selectedFiles) > 1) {
        $zip = new ZipArchive();
        $zipFileName = "downloads.zip";
        $zipFilePath = "Uploads1/" . $zipFileName;

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $selectedFiles = array_reverse($selectedFiles);
            $placeholders = implode(',', array_fill(0, count($selectedFiles), '?'));

            // first try a_c_files
            $sql = "SELECT file_path, file_name FROM a_c_files WHERE id IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(str_repeat("i", count($selectedFiles)), ...$selectedFiles);
            $stmt->execute();
            $result = $stmt->get_result();

            // if none, fallback to a_files
            if ($result->num_rows === 0) {
                $sql = "SELECT file_path, file_name FROM a_files WHERE id IN ($placeholders)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(str_repeat("i", count($selectedFiles)), ...$selectedFiles);
                $stmt->execute();
                $result = $stmt->get_result();
            }

            $filesAdded = false;

            while ($file = $result->fetch_assoc()) {
                $filePath = $file['file_path'];
                if (file_exists($filePath)) {
                    if ($zip->addFile($filePath, basename($filePath))) {
                        $filesAdded = true;
                    }
                }
            }

            $zip->close();

            if ($filesAdded) {
                if (ob_get_length()) {
                    ob_clean();
                }
                ob_end_flush();

                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
                header('Content-Length: ' . filesize($zipFilePath));
                readfile($zipFilePath);

                unlink($zipFilePath);
                exit;
            } else {
                echo "No files were added to the ZIP archive.";
            }
        } else {
            echo "Failed to create ZIP file.";
        }
    }
}

if (isset($_POST['download_excel'])) {
    $criteria = $_POST['criteria'];
    $subCriteria = $_POST['subCriteria'];
    
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=my_Uploads.xls");

    $output = fopen("php://output", "w");

    $headers = ["Faculty Name", "Academic Year", "Description", "File Name","Criteria", "Criteria No"];
    fputcsv($output, $headers, "\t");

    $sql = "SELECT faculty_name, academic_year, Description, file_name, criteria, criteria_no 
            FROM a_c_files 
            WHERE criteria = ? AND criteria_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $criteria, $subCriteria);
    $stmt->execute();
    $result = $stmt->get_result();

    // fallback to a_files if empty
    if ($result->num_rows === 0) {
        $sql = "SELECT faculty_name, academic_year, Description, file_name, criteria, criteria_no 
                FROM a_files 
                WHERE criteria = ? AND criteria_no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $criteria, $subCriteria);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    while ($row = $result->fetch_assoc()) {
        $data = [
            $row["faculty_name"],
            $row["academic_year"],
            $row["Description"],
            $row["file_name"],
            $row["criteria"],
            $row["criteria_no"]
        ];
        fputcsv($output, $data, "\t");
    }

    fclose($output);
    exit;
}

include_once "header_admin.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Uploads</title>
    <link rel="stylesheet" href="../css/my_uploads_.css">
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

        function updateMergeButton() {
            const mergeBtn = document.getElementById('mergeBtn');
            mergeBtn.disabled = selectedOrder.length < 2;
        }

        function openFile() {
            let filePath = selectedOrder[0];
            if (!filePath) {
                alert("Please select a file to view.");
                return;
            }
            window.open(filePath, '_blank');
        }

        function toggleSelectAll(source) {
            const checkboxes = document.getElementsByName('selected_files[]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
                trackOrder({ target: checkbox });
            });
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
                window.open(url, "_blank");
            } else {
                alert("No merged file found.");
            }
        }
    </script>
    <style>
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
            width: 150vw;
             /* margin-top moved to .navbar */
            padding: 0 1rem;
        }

        .nav-items {
            margin-left: 70px;
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
                <span class="sid">&nbsp; >> &nbsp;</span>
                <span class="sid"><a href="../modules/central/c_login_n.php?event=<?php echo urlencode($event); ?>" class="home-icon">Central (<?php echo htmlspecialchars($event); ?>)</a></span>
                <span class="sid">&nbsp; >> &nbsp;</span>
                <span class="sid"><a href="../modules/central/c_aqar_files.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon"><?php echo htmlspecialchars($designation); ?></a></span>
                <span class="sid">&nbsp; >> &nbsp;</span>
                <span class="sid"><a href="criteria_cri_a.php?year=<?php echo urlencode($academic_year); ?>&criteria=<?php echo urlencode($criteria); ?>&designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon">Criteria <?php echo htmlspecialchars($criteria); ?></a></span>
                <span class="sid">&nbsp; >> &nbsp;</span>
                <span class="main"><a href="#" class="main-a">Uploaded Files</a></span>
            </div>
        </div>
    </nav>
    <div class="cont">
        <div class="container11">
            <div class="header-section">
                <h1>Central coordinator Uploaded Files</h1>
                <form method="POST">
                    <input type="hidden" name="criteria" value="<?= htmlspecialchars($criteria) ?>">
                    <input type="hidden" name="subCriteria" value="<?= htmlspecialchars($subCriteria) ?>">
                    <input type="hidden" name="branch_s" value="<?= htmlspecialchars($branch_s) ?>">
                    <button type="submit" name="download_excel" class="excel-btn">Download Excel</button>
                </form>
            </div>
            <form method="POST">
                <input type="hidden" name="criteria" value="<?= htmlspecialchars($criteria) ?>">
                <input type="hidden" name="subCriteria" value="<?= htmlspecialchars($subCriteria) ?>">
                <input type="hidden" name="branch_s" value="<?= htmlspecialchars($branch_s) ?>">
                <table>
                    <tr>
                        <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
                        <th>SI NO</th>
                        <th>Faculty Name</th>
                        <th>Academic Year</th>
                        <th>Dept</th>
                        <th>Filename</th>
                        <th>Description</th>
                        <th>Criteria</th>
                        <th>Criteria No</th>
                    </tr>
                    <?php
                    $id = 1;
                    $sql = "SELECT id, faculty_name, academic_year, description, file_name, file_path, criteria, criteria_no 
                            FROM a_c_files 
                            WHERE criteria = ? AND criteria_no = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $criteria, $subCriteria);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // fallback if no rows
                    if ($result->num_rows === 0) {
                        $sql = "SELECT id, faculty_name, academic_year,Dept, description, file_name, file_path, criteria, criteria_no 
                                FROM a_files 
                                WHERE criteria = ? AND criteria_no = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ss", $criteria, $subCriteria);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    }

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . "' 
                                " . ATTR_DATA_FILEPATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . "
                                onchange='trackOrder(event)'></td>
                            <td>" . $id++ . "</td>
                            <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                            <td>" . htmlspecialchars($row['academic_year']) . "</td>
                            <td>" . htmlspecialchars($row['Dept']) . "</td>
                            <td>" . htmlspecialchars($row['file_name']) . "</td>
                            <td>" . htmlspecialchars($row['description']) . "</td>
                            <td>" . htmlspecialchars($row['criteria']) . "</td>
                            <td>" . htmlspecialchars($row['criteria_no']) . "</td>
                        </tr>";
                    }
                    ?>
                </table>
                <div>
                    <button type="button" id="view" onclick="openFile()">View</button>
                    <button type="submit" id="down" name="action" value="download">Download</button>
                    <button type="button" id="mergeBtn" class="merg" onclick="mergePDFs()" disabled>Merge PDFs</button>
                    <button type="submit" id="del" name="action" value="delete">Delete</button>
                    <button type="button" class="merge" id="mergedFileButton" onclick="viewMergedFile()" style="display:none;">View Merged File</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
