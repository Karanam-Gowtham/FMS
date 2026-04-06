<?php
include_once "../includes/connection.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

ob_start(function ($buffer) {
    return preg_replace(
        "/value='([^']*) data-filepath='([^']*)'/",
        "value='$1' data-filepath='$2'",
        $buffer
    );
});

require_once "../includes/constants.php";
require_once __DIR__ . "/../includes/dept_scope.php";

if (!isset($_SESSION['h_username']) || $_SESSION['h_username'] === 'central' || empty($_SESSION['dept'])) {
    http_response_code(403);
    exit('Access denied');
}
$hodDept = (string) $_SESSION['dept'];
$hodUser = (string) $_SESSION['h_username'];
$ds_files = fms_hod_dept_exists_sql($conn, 'files', $hodDept);
$ds_f5112 = fms_hod_dept_exists_sql($conn, 'files5_1_1and2', $hodDept);
$ds_f513 = fms_hod_dept_exists_sql($conn, 'files5_1_3', $hodDept);
$ds_f514 = fms_hod_dept_exists_sql($conn, 'files5_1_4', $hodDept);
$ds_f521 = fms_hod_dept_exists_sql($conn, 'files5_2_1', $hodDept);
$ds_f522 = fms_hod_dept_exists_sql($conn, 'files5_2_2', $hodDept);
$ds_f523 = fms_hod_dept_exists_sql($conn, 'files5_2_3', $hodDept);
$ds_f531 = fms_hod_dept_exists_sql($conn, 'files5_3_1', $hodDept);
$ds_f533 = fms_hod_dept_exists_sql($conn, 'files5_3_3', $hodDept);

$event = isset($_GET['event']) ? htmlspecialchars($_GET['event']) : '';
$designation = isset($_GET['designation']) ? htmlspecialchars($_GET['designation']) : '';
$criteria = isset($_GET['criteria']) ? htmlspecialchars($_GET['criteria']) : 'Not Selected';
// Set default values for filtering
$academic_year = isset($_POST['academic_year']) ? $_POST['academic_year'] : '';
$criteria = isset($_POST['criteria']) ? $_POST['criteria'] : '';
$subCriteria = isset($_POST['criteria_no']) ? $_POST['criteria_no'] : '';

// Initialize $branch to avoid undefined variable warnings
$branch_s = isset($_POST['branch_s']) ? $_POST['branch_s'] : '';
if ($criteria === '1' && $branch_s !== '' && $branch_s !== $hodDept) {
    $branch_s = $hodDept;
}


if (isset($_POST['action']) && isset($_POST['selected_files'])) {
    $criteria = $_POST['criteria'];
    $subCriteria = $_POST['subCriteria'];
    $selectedFiles = $_POST['selected_files'];
    $action = $_POST['action'];

    if ($action == 'delete') {
        $tableForAction = fms_upload_table_for_criteria($criteria, $subCriteria);
        if ($tableForAction === null) {
            echo "<script>alert('Invalid criteria for delete.');</script>";
        } else {
            $pkDel = fms_table_pk_column($tableForAction);
            foreach ($selectedFiles as $fileId) {
                $fid = (int) $fileId;
                if ($fid < 1 || !fms_dashboard_row_in_scope($conn, $tableForAction, $fid, 'HOD', $hodUser, $hodDept)) {
                    continue;
                }
                $sql = "SELECT file_path FROM `$tableForAction` WHERE `$pkDel` = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $fid);
                $stmt->execute();
                $result = $stmt->get_result();
                $file = $result->fetch_assoc();
                $stmt->close();
                if ($file) {
                    $disk = '../' . $file['file_path'];
                    if (is_file($disk)) {
                        unlink($disk);
                    }
                    $sql = "DELETE FROM `$tableForAction` WHERE `$pkDel` = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $fid);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            echo "<script>alert('Files deleted successfully.');</script>";
        }
    } elseif ($action == 'download') {
        if (!empty($selectedFiles)) {
            if (count($selectedFiles) == 1) {
                $fileId = (int) $selectedFiles[0];
                $tableName = fms_upload_table_for_criteria($criteria, $subCriteria);
                if ($tableName === null) {
                    die(ERR_INVALID_CRIT);
                }
                if ($fileId < 1 || !fms_dashboard_row_in_scope($conn, $tableName, $fileId, 'HOD', $hodUser, $hodDept)) {
                    http_response_code(403);
                    exit('Access denied');
                }
                $pkDn = fms_table_pk_column($tableName);
                $sql = "SELECT file_path FROM `$tableName` WHERE `$pkDn` = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $fileId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($file = $result->fetch_assoc()) {
                    $filePath = '../' . $file['file_path'];
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
            } else {
                // Create a zip only if multiple files are selected
                $tableName = fms_upload_table_for_criteria($criteria, $subCriteria);
                if ($tableName === null) {
                    die(ERR_INVALID_CRIT);
                }
                $pkZip = fms_table_pk_column($tableName);
                $allowedIds = [];
                foreach ($selectedFiles as $sid) {
                    $sid = (int) $sid;
                    if ($sid > 0 && fms_dashboard_row_in_scope($conn, $tableName, $sid, 'HOD', $hodUser, $hodDept)) {
                        $allowedIds[] = $sid;
                    }
                }
                if ($allowedIds === []) {
                    http_response_code(403);
                    exit('Access denied');
                }

                $zip = new ZipArchive();
                $zipFileName = "downloads.zip";
                $zipFilePath = "../uploads/" . $zipFileName;

                if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $allowedIds = array_reverse($allowedIds);

                    // Convert selected file IDs to placeholders for SQL
                    $placeholders = implode(',', array_fill(0, count($allowedIds), '?'));
                    $sql = "SELECT file_path, file_name FROM `$tableName` WHERE `$pkZip` IN ($placeholders)";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(str_repeat('i', count($allowedIds)), ...$allowedIds);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $filesAdded = false; // Track if any files were added

                    while ($file = $result->fetch_assoc()) {
                        $filePath = '../' . $file['file_path']; // Ensure correct relative path
                        if (file_exists($filePath)) {
                            if ($zip->addFile($filePath, basename($filePath))) {
                                echo "File added to ZIP: $filePath<br>";
                                $filesAdded = true;
                            } else {
                                echo "Failed to add file to ZIP: $filePath<br>";
                            }
                        } else {
                            echo "File does not exist or path is invalid: $filePath<br>";
                        }
                    }

                    $zip->close();

                    if ($filesAdded) {
                        // Clear output buffer
                        if (ob_get_length()) {
                            ob_clean();
                        }
                        ob_end_flush();

                        // Set headers for ZIP download
                        header('Content-Type: application/zip');
                        header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
                        header('Content-Length: ' . filesize($zipFilePath));
                        readfile($zipFilePath);

                        // Clean up
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
    }
}

if (isset($_POST['download_excel'])) {

    $criteria = $_POST['criteria'];
    $subCriteria = $_POST['subCriteria'];
    $branch_s = $_POST['branch_s'];
    if ($criteria === '1' && $branch_s !== '' && $branch_s !== $hodDept) {
        $branch_s = $hodDept;
    }


    // Set headers for Excel download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=my_uploads.xls");

    // Open output stream for writing Excel data
    $output = fopen("php://output", "w");

    // Determine the table and columns based on criteria and subCriteria
    if (
        ($criteria == '2' || $criteria == '3' || $criteria == '4' || $criteria == '7') ||
        ($criteria == '5' && in_array($subCriteria, [CRIT_5_1_5, CRIT_5_3_2, CRIT_5_4_1, CRIT_5_4_2])) ||
        ($criteria == '6' && !in_array($subCriteria, [CRIT_6_1_1_A, CRIT_6_1_1_F, CRIT_6_1_1_I]))
    ) {
        // For files table
        $tableName = TABLE_FILES;
        $columns = [COL_FACULTY, COL_YEAR, COL_FILE, COL_DESC];

        $sql = "SELECT * FROM $tableName  WHERE criteria = ? AND criteria_no = ?" . $ds_files;

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $criteria, $subCriteria);
    } elseif ($criteria == '1') {
        // For files table with specific criteria_no
        $tableName = TABLE_FILES;
        $columns = [COL_FACULTY, COL_YEAR, COL_FILE, COL_DESC, "Branch", "Criteria No"];
        $sql = "SELECT * FROM $tableName WHERE criteria = ? AND branch = ?" . $ds_files;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $criteria, $hodDept);
    } elseif ($criteria == '5' && in_array($subCriteria, [CRIT_5_1_1, CRIT_5_1_2])) {
        // For files5_1_1and2 table
        $tableName = "files5_1_1and2";
        $columns = [COL_FACULTY, COL_YEAR, "Scheme Name", "Gov Students", "Gov Amount", "Inst Students", "Inst Amount", "NGO Students", "NGO Amount", "NGO Name", COL_FILE];
        $sql = "SELECT * FROM $tableName WHERE criteria_no = ?" . $ds_f5112;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $subCriteria);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_1_3) {
        // For files5_1_3 table
        $tableName = "files5_1_3";
        $columns = [COL_FACULTY, COL_YEAR, "Programme Name", "Year", "Students Enrolled", "Agency Details", COL_FILE];
        $sql = "SELECT * FROM $tableName WHERE 1=1 " . $ds_f513;
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_1_4) {
        // For files5_1_4 table
        $tableName = "files5_1_4";
        $columns = [COL_FACULTY, COL_YEAR, "Activity Exam", "Students Exam", "Career Details", "Students Career", "Students Placed", COL_FILE];
        $sql = "SELECT * FROM $tableName WHERE 1=1 " . $ds_f514;
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_2_1) {
        // For files5_2_1 table
        $tableName = "files5_2_1";
        $columns = [COL_FACULTY, COL_YEAR, COL_STUDENT_NAME, "Programme", "Employer", "Pay", COL_FILE];
        $sql = "SELECT * FROM $tableName WHERE 1=1 " . $ds_f521;
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_2_2) {
        // For files5_2_2 table
        $tableName = "files5_2_2";
        $columns = [COL_FACULTY, COL_YEAR, COL_STUDENT_NAME, "Programme", "Institution", "Admitted Programme", COL_FILE];
        $sql = "SELECT * FROM $tableName WHERE 1=1 " . $ds_f522;
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_2_3) {
        // For files5_2_3 table
        $tableName = "files5_2_3";
        $columns = [COL_FACULTY, COL_YEAR, "Reg No", "Exam", "Exam Status", COL_FILE];
        $sql = "SELECT * FROM $tableName WHERE 1=1 " . $ds_f523;
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_3_1) {
        // For files5_3_1 table
        $tableName = "files5_3_1";
        $columns = [COL_FACULTY, COL_YEAR, "Award Name", "Participation Type", COL_STUDENT_NAME, "Competition Level", "Event Name", "Month Year", COL_FILE];
        $sql = "SELECT * FROM $tableName WHERE 1=1 " . $ds_f531;
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_3_3) {
        // For files5_3_3 table
        $tableName = "files5_3_3";
        $columns = [COL_FACULTY, COL_YEAR, "Event Name", "Event Date", COL_FILE];
        $sql = "SELECT * FROM $tableName WHERE 1=1 " . $ds_f533;
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '6' && $subCriteria == CRIT_6_1_1_A) {
        // For files table with specific criteria_no
        $tableName = TABLE_FILES;
        $columns = [COL_FACULTY, COL_YEAR, "Branch", COL_DESC, "Sem", "Section", COL_FILE];
        $sql = "SELECT * FROM $tableName WHERE criteria = ? AND criteria_no = ?" . $ds_files;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $criteria, $subCriteria);
    } elseif ($criteria == '6' && $subCriteria == CRIT_6_1_1_F) {
        // For files table with specific criteria_no
        $tableName = TABLE_FILES;
        $columns = [COL_FACULTY, COL_YEAR, "Branch", COL_DESC, "ext_or_int", COL_FILE];
        $sql = "SELECT * FROM $tableName WHERE  criteria = ? AND criteria_no = ?" . $ds_files;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $criteria, $subCriteria);
    } elseif ($criteria == '6' && $subCriteria == CRIT_6_1_1_I) {
        // For files table with specific criteria_no
        $tableName = TABLE_FILES;
        $columns = [COL_FACULTY, COL_YEAR, "Branch", COL_DESC, COL_FILE, "Branch", "Criteria No"];
        $sql = "SELECT * FROM $tableName WHERE criteria = ? AND criteria_no = ?" . $ds_files;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $criteria, $subCriteria);
    } else {
        die(ERR_INVALID_CRIT);
    }

    // Write column headers to Excel
    fputcsv($output, $columns, "\t");

    // Execute the query and fetch data
    $stmt->execute();
    $result = $stmt->get_result();

    // Write data rows to Excel
    while ($row = $result->fetch_assoc()) {
        $data = [];
        foreach ($columns as $column) {
            $data[] = $row[strtolower(str_replace(' ', '_', $column))] ?? ''; // Map column names to database fields
        }
        fputcsv($output, $data, "\t");
    }

    // Close the output stream
    fclose($output);
    exit;
}

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

        function normalizeFilePath(filePath) {
            if (!filePath) {
                return '';
            }
            return String(filePath).replace(/\\/g, '/');
        }

        function viewSingleFile(filePath) {
            const normalizedPath = normalizeFilePath(filePath);
            if (!normalizedPath) {
                alert('Please select a file to view.');
                return;
            }
            window.open('view_file_hod.php?file_path=' + encodeURIComponent(normalizedPath), '_blank');
        }

        function trackOrder(event) {
            const filePath = normalizeFilePath(event.target.dataset.filepath);
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
            const filePath = normalizeFilePath(selectedOrder[0]);
            if (!filePath) {
                alert("Please select a file to view.");
                return;
            }
            viewSingleFile(filePath);
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
                    // Add ../ to the file path
                    const fixedFileUrl = `../${normalizeFilePath(fileUrl)}`;
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

            fetch("../admin/save_merged_pdf.php", {
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
                url = "../" + url; // Add '../' to the URL
                window.open(url, "_blank");
            } else {
                alert("No merged file found.");
            }
        }
    </script>
    <style>
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
    <?php include_once 'header_hod.php'; ?>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sid">&nbsp; >> &nbsp; </span><span class="sid"><a
                        href="../c_login_n.php?event=<?php echo urlencode($event); ?>" class="home-icon">Central
                        (<?php echo htmlspecialchars($event); ?>)</a></span>
                <span class="sid">&nbsp; >> &nbsp; </span><span class="sid"><a
                        href="../c_aqar_files.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>"
                        class="home-icon"><?php echo htmlspecialchars($designation); ?></a></span>
                <span class="sid">&nbsp; >> &nbsp; </span><span class="sid"><a
                        href="criteria_a.php?year=<?php echo urlencode($academic_year); ?>&criteria=<?php echo urlencode($criteria); ?>&designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>"
                        class="home-icon">Criteria <?php echo htmlspecialchars($criteria); ?></a></span>
                <span class="sid">&nbsp; >> &nbsp; </span><span class="main"><span class="main-a">Uploaded
                        Files</span></span>

            </div>
        </div>
    </nav>
    <div class="cont">
        <div class="container11">
            <div class="header-section">
                <h1>Uploaded Files</h1>
                <form method="POST">
                    <input type="hidden" name="criteria" value="<?= htmlspecialchars($criteria) ?>">
                    <input type="hidden" name="subCriteria" value="<?= htmlspecialchars($subCriteria) ?>">
                    <input type="hidden" name="branch_s" value="<?= htmlspecialchars($branch_s) ?>">
                    <button type="submit" name="download_excel" class="excel-btn">Download Excel</button>
                </form>
            </div>
            <!-- Form for branch selection -->
            <form method="POST" action="">
                <input type="hidden" name="criteria" value="<?= htmlspecialchars($criteria) ?>">
                <input type="hidden" name="criteria_no" value="<?= htmlspecialchars($subCriteria) ?>">

                <?php if ($criteria == '1'): ?>
                    <label for="branch_s">Department:</label>
                    <select name="branch_s" id="branch_s" onkeydown="if(event.key === 'Enter') this.click()">
                        <option value="<?php echo htmlspecialchars($hodDept); ?>" selected><?php echo htmlspecialchars($hodDept); ?></option>
                    </select>
                    <button type="submit" name="submit_branch" onkeydown="if(event.key === 'Enter') this.click()">Submit</button>
                <?php endif; ?>
            </form>

            <table>
                <tr>
                    <th><input type="checkbox" onclick="toggleSelectAll(this)" onkeydown="if(event.key === 'Enter') this.click()"></th>
                    <th>SI NO</th>
                    <th>Faculty Name</th>
                    <th>Academic Year</th>
                    <th>Filename</th>



                    <?php
                    $id = 1;

                    if (!empty($criteria) && isset($_POST['submit_branch'])) {
                        // Fetch files based on selected branch
                        if ($criteria == '1' && !empty($branch_s)) {

                            $sql = "SELECT id, faculty_name, academic_year, branch, description, file_name, file_path, criteria_no
                            FROM files
                            WHERE criteria = ? AND criteria_no = ? AND branch = ?" . $ds_files;
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("sss", $criteria, $subCriteria, $hodDept);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            ?>
                            <th>Description</th>
                            <th>Branch</th>
                            <th>Criteria No</th>
                        </tr>
                        <?php
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown='if(event.key === \"Enter\") this.click()'></td>
                            <td>" . $id++ . "</td>
                            <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                            <td>" . htmlspecialchars($row['academic_year']) . "</td>
                            <td>" . htmlspecialchars($row['file_name']) . "</td>
                            <td>" . htmlspecialchars($row['description']) . "</td>
                            <td>" . htmlspecialchars($row['branch']) . "</td>
                            <td>" . htmlspecialchars($row['criteria_no']) . "</td>
                        </tr>";
                        }
                        }
                    } else {
                        if (
                            $criteria == '2' || $criteria == '3' || $criteria == '4' || $criteria == '7' ||
                            ($criteria == '5' && in_array($subCriteria, [CRIT_5_1_5, CRIT_5_3_2, CRIT_5_4_1, CRIT_5_4_2])) ||
                            ($criteria == '6' && !in_array($subCriteria, [CRIT_6_1_1_A, CRIT_6_1_1_F, CRIT_6_1_1_I]))
                        ) {

                            $sql = "SELECT id, faculty_name, academic_year, file_name, file_path, description,criteria_no FROM files where criteria = ? and criteria_no =?" . $ds_files;
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ss", $criteria, $subCriteria);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>
                        <th>Description</th>
                        <th>Criteria No</th>
                        </tr>
                        <?php
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown='if(event.key === \"Enter\") this.click()'></td>
                                        <td>" . $id++ . "</td>
                                        <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                                        <td>" . htmlspecialchars($row['academic_year']) . "</td>
                                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                                        <td>" . htmlspecialchars($row['description']) . "</td>
                                        <td>" . htmlspecialchars($row['criteria_no']) . "</td>
                                    </tr>";
                        }
                        } elseif ($criteria == '5' && in_array($subCriteria, [CRIT_5_1_1, CRIT_5_1_2])) {

                            $sql = "SELECT id, faculty_name, academic_year, scheme_name, gov_students, gov_amount, inst_students, inst_amount, ngo_students, ngo_amount, ngo_name, file_name,file_path
                                        FROM files5_1_1and2 WHERE criteria_no=?" . $ds_f5112;
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("s", $subCriteria);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>
                            <th>scheme name</th>
                            <th>gov students</th>
                            <th>gov amount</th>
                            <th>inst students</th>
                            <th>inst amount</th>
                            <th>ngo students</th>
                            <th>ngo amount</th>
                            <th>ngo name</th>
                            </tr>
                            <?php
                             while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown='if(event.key === \"Enter\") this.click()'></td>
                                        <td>" . $id++ . "</td>
                                        <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                                        <td>" . htmlspecialchars($row['academic_year']) . "</td>
                                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                                        <td>" . htmlspecialchars($row['scheme_name']) . "</td>
                                        <td>" . htmlspecialchars($row['gov_students']) . "</td>
                                        <td>" . htmlspecialchars($row['gov_amount']) . "</td>
                                        <td>" . htmlspecialchars($row['inst_students']) . "</td>
                                        <td>" . htmlspecialchars($row['inst_amount']) . "</td>
                                        <td>" . htmlspecialchars($row['ngo_students']) . "</td>
                                        <td>" . htmlspecialchars($row['ngo_amount']) . "</td>
                                        <td>" . htmlspecialchars($row['ngo_name']) . "</td>
                                    </tr>";
                            }
                        } elseif ($criteria == '5' && $subCriteria == CRIT_5_1_3) {

                            $sql = "SELECT id, faculty_name, academic_year, programme_name, year, students_enrolled, agency_details, file_name, file_path
                                        FROM files5_1_3 WHERE 1=1 " . $ds_f513;
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>
                                <th>programme name</th>
                                <th>year</th>
                                <th>students enrolled</th>
                                <th>agency details</th>
                                </tr>
                            <?php
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown='if(event.key === \"Enter\") this.click()'></td>
                                        <td>" . $id++ . "</td>
                                        <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                                        <td>" . htmlspecialchars($row['academic_year']) . "</td>
                                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                                        <td>" . htmlspecialchars($row['programme_name']) . "</td>
                                        <td>" . htmlspecialchars($row['year']) . "</td>
                                        <td>" . htmlspecialchars($row['students_enrolled']) . "</td>
                                        <td>" . htmlspecialchars($row['agency_details']) . "</td>
                                    </tr>";
                            }
                        } elseif ($criteria == '5' && $subCriteria == CRIT_5_1_4) {

                            $sql = "SELECT id, faculty_name, academic_year, activity_exam, students_exam, career_details, students_career, students_placed, file_name,file_path
                                        FROM files5_1_4 WHERE 1=1 " . $ds_f514;
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>
                                    <th>activity exam</th>
                                    <th>students exam</th>
                                    <th>career details</th>
                                    <th>students career</th>
                                    <th>students placed</th>
                                    </tr>
                            <?php
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown='if(event.key === \"Enter\") this.click()'></td>
                                        <td>" . $id++ . "</td>
                                        <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                                        <td>" . htmlspecialchars($row['academic_year']) . "</td>
                                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                                        <td>" . htmlspecialchars($row['activity_exam']) . "</td>
                                        <td>" . htmlspecialchars($row['students_exam']) . "</td>
                                        <td>" . htmlspecialchars($row['career_details']) . "</td>
                                        <td>" . htmlspecialchars($row['students_career']) . "</td>
                                        <td>" . htmlspecialchars($row['students_placed']) . "</td>
                                    </tr>";
                            }
                        } elseif ($criteria == '5' && $subCriteria == CRIT_5_2_1) {

                            $sql = "SELECT id, faculty_name, academic_year, student_name, programme, employer, pay, file_name ,file_path
                                        FROM files5_2_1 WHERE 1=1 " . $ds_f521;
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>
                                        <th>student name</th>
                                        <th>programme</th>
                                        <th>employer</th>
                                        <th>pay</th>
                                        </tr>
                            <?php
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown='if(event.key === \"Enter\") this.click()'></td>
                                        <td>" . $id++ . "</td>
                                        <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                                        <td>" . htmlspecialchars($row['academic_year']) . "</td>
                                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                                        <td>" . htmlspecialchars($row['student_name']) . "</td>
                                        <td>" . htmlspecialchars($row['programme']) . "</td>
                                        <td>" . htmlspecialchars($row['employer']) . "</td>
                                        <td>" . htmlspecialchars($row['pay']) . "</td>
                                    </tr>";
                            }
                        } elseif ($criteria == '5' && $subCriteria == CRIT_5_2_2) {

                            $sql = "SELECT id, faculty_name, academic_year, student_name, programme, institution, admitted_programme, file_name, file_path
                                        FROM files5_2_2 WHERE 1=1 " . $ds_f522;
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>
                                            <th>student name</th>
                                            <th>programme</th>
                                            <th>institution</th>
                                            <th>admitted_programme</th>
                                            </tr>
                            <?php
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown='if(event.key === \"Enter\") this.click()'></td>
                                        <td>" . $id++ . "</td>
                                        <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                                        <td>" . htmlspecialchars($row['academic_year']) . "</td>
                                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                                        <td>" . htmlspecialchars($row['student_name']) . "</td>
                                        <td>" . htmlspecialchars($row['programme']) . "</td>
                                        <td>" . htmlspecialchars($row['institution']) . "</td>
                                        <td>" . htmlspecialchars($row['admitted_programme']) . "</td>
                                    </tr>";
                            }
                        } elseif ($criteria == '5' && $subCriteria == CRIT_5_2_3) {

                            $sql = "SELECT id, username, faculty_name, academic_year, reg_no, exam, exam_status, file_name, file_path
                                        FROM files5_2_3 WHERE 1=1 " . $ds_f523;
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>
                                                <th>reg_no</th>
                                                <th>exam</th>
                                                <th>exam_status</th>
                                                </tr>
                            <?php
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown='if(event.key === \"Enter\") this.click()'></td>
                                        <td>" . $id++ . "</td>
                                        <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                                        <td>" . htmlspecialchars($row['academic_year']) . "</td>
                                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                                        <td>" . htmlspecialchars($row['reg_no']) . "</td>
                                        <td>" . htmlspecialchars($row['exam']) . "</td>
                                        <td>" . htmlspecialchars($row['exam_status']) . "</td>
                                    </tr>";
                            }
                        } elseif ($criteria == '5' && $subCriteria == CRIT_5_3_1) {

                            $sql = "SELECT id, username, faculty_name, academic_year, award_name, participation_type, student_name, competition_level, event_name, month_year, file_name, file_path
                                        FROM files5_3_1 WHERE 1=1 " . $ds_f531;
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>
                                                    <th>award_name</th>
                                                    <th>participation_type</th>
                                                    <th>student_name</th>
                                                    <th>competition_level</th>
                                                    <th>event_name</th>
                                                    <th>month_year</th>
                                                    </tr>
                            <?php
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown='if(event.key === \"Enter\") this.click()'></td>
                                        <td>" . $id++ . "</td>
                                        <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                                        <td>" . htmlspecialchars($row['academic_year']) . "</td>
                                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                                        <td>" . htmlspecialchars($row['award_name']) . "</td>
                                        <td>" . htmlspecialchars($row['participation_type']) . "</td>
                                        <td>" . htmlspecialchars($row['student_name']) . "</td>
                                        <td>" . htmlspecialchars($row['competition_level']) . "</td>
                                        <td>" . htmlspecialchars($row['event_name']) . "</td>
                                        <td>" . htmlspecialchars($row['month_year']) . "</td>
                                    </tr>";
                            }
                        } elseif ($criteria == '5' && $subCriteria == CRIT_5_3_3) {

                            $sql = "SELECT id, username, faculty_name, academic_year, event_name, event_date, file_name, file_path
                                        FROM files5_3_3 WHERE 1=1 " . $ds_f533;
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>
                                                        <th>event_name</th>
                                                        <th>event_date</th>
                                                        </tr>
                            <?php
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown='if(event.key === \"Enter\") this.click()'></td>
                                        <td>" . $id++ . "</td>
                                        <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                                        <td>" . htmlspecialchars($row['academic_year']) . "</td>
                                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                                        <td>" . htmlspecialchars($row['event_name']) . "</td>
                                        <td>" . htmlspecialchars($row['event_date']) . "</td>
                                    </tr>";
                            }
                        } elseif ($criteria == '6' && $subCriteria == CRIT_6_1_1_A) {

                            $sql = "SELECT id, faculty_name, academic_year,branch,description, sem, section, file_name, file_path FROM files where criteria = ? and criteria_no =?" . $ds_files;
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ss", $criteria, $subCriteria);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>

                                                            <th>Description</th>
                                                            <th>Branch</th>
                                                            <th>Semister</th>
                                                            <th>Section</th>
                                                            </tr>
                            <?php
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown='if(event.key === \"Enter\") this.click()'></td>
                                        <td>" . $id++ . "</td>
                                        <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                                        <td>" . htmlspecialchars($row['academic_year']) . "</td>
                                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                                        <td>" . htmlspecialchars($row['description']) . "</td>
                                        <td>" . htmlspecialchars($row['branch']) . "</td>
                                        <td>" . htmlspecialchars($row['sem']) . "</td>
                                        <td>" . htmlspecialchars($row['section']) . "</td>
                                    </tr>";
                            }
                        } elseif ($criteria == '6' && $subCriteria == CRIT_6_1_1_F) {

                            $sql = "SELECT id, faculty_name, academic_year,branch,description, ext_or_int, file_name, file_path FROM files where criteria = ? and criteria_no =?" . $ds_files;
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ss", $criteria, $subCriteria);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>

                                                                <th>Description</th>
                                                                <th>Branch</th>
                                                                <th>Ext or Int</th>
                                                                </tr>
                            <?php
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown='if(event.key === \"Enter\") this.click()'></td>
                                        <td>" . $id++ . "</td>
                                        <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                                        <td>" . htmlspecialchars($row['academic_year']) . "</td>
                                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                                        <td>" . htmlspecialchars($row['description']) . "</td>
                                        <td>" . htmlspecialchars($row['branch']) . "</td>
                                        <td>" . htmlspecialchars($row['ext_or_int']) . "</td>
                                    </tr>";
                            }
                        } elseif ($criteria == '6' && $subCriteria == CRIT_6_1_1_I) {

                            $sql = "SELECT id, faculty_name, academic_year,branch,description, file_name, file_path,criteria_no FROM files where criteria = ? and criteria_no =?" . $ds_files;
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ss", $criteria, $subCriteria);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>

                                                                    <th>Description</th>
                                                                    <th>Branch</th>
                                                                    <th>Criteria No</th>
                                                                    </tr>
                            <?php
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown='if(event.key === \"Enter\") this.click()'></td>
                                        <td>" . $id++ . "</td>
                                        <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                                        <td>" . htmlspecialchars($row['academic_year']) . "</td>
                                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                                        <td>" . htmlspecialchars($row['description']) . "</td>
                                        <td>" . htmlspecialchars($row['branch']) . "</td>
                                        <td>" . htmlspecialchars($row['criteria_no']) . "</td>
                                    </tr>";
                            }
                        }
                    }


                    ?>
            </table>
            <div>
                <button type="button" id="view" onclick="openFile(event)">View</button>
                <button type="submit" id="down" name="action" value="download">Download</button>
                <button type="button" id="mergeBtn" class="merg" onclick="mergePDFs()" disabled>Merge PDFs</button>
                <button type="submit" id="del" name="action" value="delete">Delete</button>
                <button type="button" class="merge" id="mergedFileButton" onclick="viewMergedFile()"
                    style="display:none;">View Merged File</button>

            </div>
            </form>

        </div>
    </div>
</body>

</html>
