<?php
require_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/connection.php';
require_once __DIR__ . '/../includes/csrf.php';

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$logged = isset($_SESSION['username'])
    || isset($_SESSION['a_username'])
    || isset($_SESSION['j_username'])
    || isset($_SESSION['h_username'])
    || isset($_SESSION['admin'])
    || isset($_SESSION['c_cord'])
    || isset($_SESSION['c_username'])
    || isset($_SESSION['cri_username']);
if (!$logged) {
    http_response_code(403);
    exit('Access denied');
}

require_once "../includes/constants.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidate();
}

$event = isset($_GET['event']) ? htmlspecialchars($_GET['event']) : '';
$designation = isset($_GET['designation']) ? htmlspecialchars($_GET['designation']) : '';
$criteria = isset($_GET['criteria']) ? htmlspecialchars($_GET['criteria']) : NOT_SELECTED;
// Set default values for filtering
$academic_year = isset($_POST['academic_year']) ? $_POST['academic_year'] : '';
$criteria = isset($_POST['criteria']) ? $_POST['criteria'] : '';
$subCriteria = isset($_POST['criteria_no']) ? $_POST['criteria_no'] : '';

// Initialize $branch to avoid undefined variable warnings
$branch_s = isset($_POST['branch_s']) ? $_POST['branch_s'] : '';

if (isset($_POST['action']) && isset($_POST['selected_files'])) {
    $criteria = $_POST['criteria'];
    $subCriteria = $_POST['subCriteria'];
    $selectedFiles = $_POST['selected_files'];
    $action = $_POST['action'];

    if ($action == 'delete') {
        foreach ($selectedFiles as $fileId) {
            $sql = "SELECT file_path FROM " . TABLE_FILES . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();
            if ($file) {
                unlink('../' . $file['file_path']); // Delete the actual file
                $sql = "DELETE FROM " . TABLE_FILES . " WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $fileId);
                $stmt->execute();
            }
        }
        echo "<script>alert('Files deleted successfully.');</script>";
    } elseif ($action == 'download') {
        if (!empty($selectedFiles)) {
            if (count($selectedFiles) == 1) {
                $fileId = $selectedFiles[0];

                // Determine the table based on criteria and sub-criteria
                if (
                    $criteria == '1' || $criteria == '2' || $criteria == '3' || $criteria == '4' || $criteria == '7' ||
                    ($criteria == '5' && in_array($subCriteria, [CRIT_5_1_5, CRIT_5_3_2, CRIT_5_4_1, CRIT_5_4_2])) ||
                    ($criteria == '6')
                ) {
                    $tableName = TABLE_FILES;
                } elseif ($criteria == '5' && in_array($subCriteria, [CRIT_5_1_1, CRIT_5_1_2])) {
                    $tableName = "files5_1_1and2";
                } elseif ($criteria == '5' && $subCriteria == CRIT_5_1_3) {
                    $tableName = "files5_1_3";
                } elseif ($criteria == '5' && $subCriteria == CRIT_5_1_4) {
                    $tableName = "files5_1_4";
                } elseif ($criteria == '5' && $subCriteria == CRIT_5_2_1) {
                    $tableName = "files5_2_1";
                } else {
                    die(ERR_INVALID_CRIT);
                }

                $sql = "SELECT file_path FROM $tableName WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $fileId);
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
                $zip = new ZipArchive();
                $zipFileName = "downloads.zip";
                $zipFilePath = "../uploads/" . $zipFileName;

                if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    $selectedFiles = array_reverse($selectedFiles);

                    // Determine the table based on criteria and sub-criteria
                    if (
                        $criteria == '1' || $criteria == '2' || $criteria == '3' || $criteria == '4' || $criteria == '7' ||
                        ($criteria == '5' && in_array($subCriteria, [CRIT_5_1_5, CRIT_5_3_2, CRIT_5_4_1, CRIT_5_4_2])) ||
                        ($criteria == '6')
                    ) {
                        $tableName = TABLE_FILES;
                    } elseif ($criteria == '5' && in_array($subCriteria, [CRIT_5_1_1, CRIT_5_1_2])) {
                        $tableName = "files5_1_1and2";
                    } elseif ($criteria == '5' && $subCriteria == CRIT_5_1_3) {
                        $tableName = "files5_1_3";
                    } elseif ($criteria == '5' && $subCriteria == CRIT_5_1_4) {
                        $tableName = "files5_1_4";
                    } elseif ($criteria == '5' && $subCriteria == CRIT_5_2_1) {
                        $tableName = "files5_2_1";
                    } else {
                        die(ERR_INVALID_CRIT);
                    }

                    // Convert selected file IDs to placeholders for SQL
                    $placeholders = implode(',', array_fill(0, count($selectedFiles), '?'));
                    $sql = "SELECT file_path, file_name FROM $tableName WHERE id IN ($placeholders)";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(str_repeat("i", count($selectedFiles)), ...$selectedFiles);
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

        $sql = "SELECT * FROM $tableName  WHERE criteria = ? AND criteria_no = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $criteria, $subCriteria);
    } elseif ($criteria == '1') {
        // For files table with specific criteria_no
        $tableName = TABLE_FILES;
        $columns = [COL_FACULTY, COL_YEAR, COL_FILE, COL_DESC, "Branch", "Criteria No"];
        $sql = "SELECT * FROM $tableName WHERE criteria = ? AND branch = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $criteria, $branch_s);
    } elseif ($criteria == '5' && in_array($subCriteria, [CRIT_5_1_1, CRIT_5_1_2])) {
        // For files5_1_1and2 table
        $tableName = "files5_1_1and2";
        $columns = [COL_FACULTY, COL_YEAR, "Scheme Name", "Gov Students", "Gov Amount", "Inst Students", "Inst Amount", "NGO Students", "NGO Amount", "NGO Name", "file_name"];
        $sql = "SELECT * FROM $tableName WHERE criteria_no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $subCriteria);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_1_3) {
        // For files5_1_3 table
        $tableName = "files5_1_3";
        $columns = [COL_FACULTY, COL_YEAR, "Programme Name", "Year", "Students Enrolled", "Agency Details", COL_FILE];
        $sql = "SELECT * FROM $tableName ";
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_1_4) {
        // For files5_1_4 table
        $tableName = "files5_1_4";
        $columns = [COL_FACULTY, COL_YEAR, "Activity Exam", "Students Exam", "Career Details", "Students Career", "Students Placed", COL_FILE];
        $sql = "SELECT * FROM $tableName ";
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_2_1) {
        // For files5_2_1 table
        $tableName = "files5_2_1";
        $columns = [COL_FACULTY, COL_YEAR, COL_STUDENT_NAME, "Programme", "Employer", "Pay", COL_FILE];
        $sql = "SELECT * FROM $tableName";
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_2_2) {
        // For files5_2_2 table
        $tableName = "files5_2_2";
        $columns = [COL_FACULTY, COL_YEAR, COL_STUDENT_NAME, "Programme", "Institution", "Admitted Programme", COL_FILE];
        $sql = "SELECT * FROM $tableName ";
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_2_3) {
        // For files5_2_3 table
        $tableName = "files5_2_3";
        $columns = [COL_FACULTY, COL_YEAR, "Reg No", "Exam", "Exam Status", COL_FILE];
        $sql = "SELECT * FROM $tableName";
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_3_1) {
        // For files5_3_1 table
        $tableName = "files5_3_1";
        $columns = [COL_FACULTY, COL_YEAR, "Award Name", "Participation Type", COL_STUDENT_NAME, "Competition Level", "Event Name", "Month Year", COL_FILE];
        $sql = "SELECT * FROM $tableName";
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '5' && $subCriteria == CRIT_5_3_3) {
        // For files5_3_3 table
        $tableName = "files5_3_3";
        $columns = [COL_FACULTY, COL_YEAR, "Event Name", "Event Date", COL_FILE];
        $sql = "SELECT * FROM $tableName";
        $stmt = $conn->prepare($sql);
    } elseif ($criteria == '6' && $subCriteria == CRIT_6_1_1_A) {
        // For files table with specific criteria_no
        $tableName = TABLE_FILES;
        $columns = [COL_FACULTY, COL_YEAR, "Branch", COL_DESC, "Sem", "Section", COL_FILE];
        $sql = "SELECT * FROM $tableName WHERE criteria = ? AND criteria_no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $criteria, $subCriteria);
    } elseif ($criteria == '6' && $subCriteria == CRIT_6_1_1_F) {
        // For files table with specific criteria_no
        $tableName = TABLE_FILES;
        $columns = [COL_FACULTY, COL_YEAR, "Branch", COL_DESC, "ext_or_int", COL_FILE];
        $sql = "SELECT * FROM $tableName WHERE  criteria = ? AND criteria_no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $criteria, $subCriteria);
    } elseif ($criteria == '6' && $subCriteria == CRIT_6_1_1_I) {
        // For files table with specific criteria_no
        $tableName = TABLE_FILES;
        $columns = [COL_FACULTY, COL_YEAR, "Branch", COL_DESC, COL_FILE, "Branch", "Criteria No"];
        $sql = "SELECT * FROM $tableName WHERE criteria = ? AND criteria_no = ?";
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
<?php
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
            window.open('view_file.php?file_path=' + encodeURIComponent(filePath), '_blank');
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
                    const fixedFileUrl = `../${fileUrl}`;
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

            fetch("../save_merged_pdf.php", {
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
                        href="../modules/central/c_login_n.php?event=<?php echo urlencode($event); ?>"
                        class="home-icon">Central (<?php echo htmlspecialchars($event); ?>)</a></span>
                <span class="sid">&nbsp; >> &nbsp; </span><span class="sid"><a
                        href="../modules/central/c_aqar_files.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>"
                        class="home-icon"><?php echo htmlspecialchars($designation); ?></a></span>
                <span class="sid">&nbsp; >> &nbsp; </span><span class="sid"><a
                        href="criteria_a.php?year=<?php echo urlencode($academic_year); ?>&criteria=<?php echo urlencode($criteria); ?>&designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>"
                        class="home-icon">Criteria <?php echo htmlspecialchars($criteria); ?></a></span>
                <span class="sid">&nbsp; >> &nbsp; </span><span class="main"> <a href="#" class="main-a">Uploaded
                        Files</a></span>

            </div>
        </div>
    </nav>
    <div class="cont">
        <div class="container11">
            <div class="header-section">
                <h1>Uploaded Files</h1>
                <form method="POST">
                <?php echo csrfField(); ?>
                    <input type="hidden" name="criteria" value="<?= htmlspecialchars($criteria) ?>">
                    <input type="hidden" name="subCriteria" value="<?= htmlspecialchars($subCriteria) ?>">
                    <input type="hidden" name="branch_s" value="<?= htmlspecialchars($branch_s) ?>">
                    <button type="submit" name="download_excel" class="excel-btn">Download Excel</button>
                </form>
            </div>
            <!-- Form for branch selection -->
            <form method="POST" action="">
                <?php echo csrfField(); ?>
                <input type="hidden" name="criteria" value="<?= htmlspecialchars($criteria) ?>">
                <input type="hidden" name="criteria_no" value="<?= htmlspecialchars($subCriteria) ?>">

                <?php if ($criteria == '1'): ?>
                    <label for="branch_s">Select Branch:</label>
                    <select name="branch_s" id="branch_s">
                        <option value="" disabled <?= empty($branch_s) ? 'selected' : '' ?>>-- Select Branch --</option>
                        <option value="CSE" <?= $branch_s == 'CSE' ? 'selected' : '' ?>>CSE</option>
                        <option value="AIML" <?= $branch_s == 'AIML' ? 'selected' : '' ?>>AIML</option>
                        <option value="AIDS" <?= $branch_s == 'AIDS' ? 'selected' : '' ?>>AIDS</option>
                        <option value="IT" <?= $branch_s == 'IT' ? 'selected' : '' ?>>IT</option>
                        <option value="ECE" <?= $branch_s == 'ECE' ? 'selected' : '' ?>>ECE</option>
                        <option value="EEE" <?= $branch_s == 'EEE' ? 'selected' : '' ?>>EEE</option>
                        <option value="MECH" <?= $branch_s == 'MECH' ? 'selected' : '' ?>>MECH</option>
                        <option value="CIVIL" <?= $branch_s == 'CIVIL' ? 'selected' : '' ?>>CIVIL</option>
                        <option value="BSH" <?= $branch_s == 'BSH' ? 'selected' : '' ?>>BSH</option>
                    </select>
                    <button type="submit" name="submit_branch">Submit</button>
                <?php endif; ?>
            </form>

            <table>
                <tr>
                    <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
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
                            WHERE criteria = ? AND criteria_no = ? AND branch = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("sss", $criteria, $subCriteria, $branch_s);
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
                            <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . QUOTE_SPACE . 
                                ATTR_DATA_FILEPATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown=\"if(event.key==='Enter')this.click()\"></td>
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

                            $sql = "SELECT id, faculty_name, academic_year, file_name, file_path, description,criteria_no FROM files where criteria = ? and criteria_no =?";
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
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . 
                                        ATTR_DATA_FILEPATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)' onkeydown=\"if(event.key==='Enter')this.click()\"></td>
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
                                        FROM files5_1_1and2 WHERE criteria_no=?";
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
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)'></td>
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
                                        FROM files5_1_3";
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
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)'></td>
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
                                        FROM files5_1_4 WHERE username = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("s", $username);
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
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)'></td>
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
                                        FROM files5_2_1 ";
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
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)'></td>
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
                                        FROM files5_2_2 ";
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
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)'></td>
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
                                        FROM files5_2_3";
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
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)'></td>
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
                                        FROM files5_3_1";
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
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)'></td>
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
                                        FROM files5_3_3";
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
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)'></td>
                                        <td>" . $id++ . "</td>
                                        <td>" . htmlspecialchars($row['faculty_name']) . "</td>
                                        <td>" . htmlspecialchars($row['academic_year']) . "</td>
                                        <td>" . htmlspecialchars($row['file_name']) . "</td>
                                        <td>" . htmlspecialchars($row['event_name']) . "</td>
                                        <td>" . htmlspecialchars($row['event_date']) . "</td>
                                    </tr>";
                            }
                        } elseif ($criteria == '6' && $subCriteria == CRIT_6_1_1_A) {

                            $sql = "SELECT id, faculty_name, academic_year,branch,description, sem, section, file_name, file_path FROM files where criteria = ? and criteria_no =?";
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
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)'></td>
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

                            $sql = "SELECT id, faculty_name, academic_year,branch,description, ext_or_int, file_name, file_path FROM files where criteria = ? and criteria_no =?";
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
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)'></td>
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

                            $sql = "SELECT id, faculty_name, academic_year,branch,description, file_name, file_path,criteria_no FROM files where criteria = ? and criteria_no =?";
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
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . ATTR_VAL_DATA_PATH . htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8') . QUOTE_SPACE . " onchange='trackOrder(event)'></td>
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