<?php
include_once "../../includes/connection.php";
require_once "../../includes/constants.php";

if (!isset($_SESSION['a_username']) && !isset($_SESSION['j_username'])) {
    die("You need to log in to view your uploads.");
}

function fixPath($p)
{
    if (empty($p)) {
        return "";
    }
    $p = htmlspecialchars_decode($p);
    $p = str_replace('\\', '/', $p);
    $finalPath = $p;
    if (preg_match('/uploads\/.*/', $p, $matches)) {
        $foundPath = $matches[0];
        if (file_exists(DIR_UP_TWO . $foundPath)) {
            $finalPath = DIR_UP_TWO . $foundPath;
        } elseif (file_exists(DIR_UP . $foundPath)) {
            $finalPath = DIR_UP . $foundPath;
        } elseif (file_exists($foundPath)) {
            $finalPath = $foundPath;
        } else {
            $finalPath = DIR_UP_TWO . $foundPath;
        }
    }
    return $finalPath;
}

$username = isset($_SESSION['a_username']) ? $_SESSION['a_username'] : $_SESSION['j_username'];
if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}
// Global fallback logic at the top


// Get the category value from the form submission
$catg = '';

// Handle POST or GET requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_name'])) {
        $catg = $_POST['action_name']; // From button click
    } elseif (isset($_POST['action_F'])) {
        $catg = $_POST['action_F'];    // From hidden input in dropdown form
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action_name'])) {
        $catg = $_GET['action_name'];
    } elseif (isset($_GET['action_F'])) {
        $catg = $_GET['action_F'];
    }
}



$action = '';
ob_start();

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['selected_files'])) {
    $action = $_POST['action'];
    $selectedFiles = $_POST['selected_files'];
    $category = $_POST['category'];

    // Determine table and file column based on category
    switch ($category) {
        case 'fdps_org':
            $tableName = 'fdps_org_tab';
            $fileColumn = 'merged_file';
            break;
        case 'published':
            $tableName = 'published_tab';
            $fileColumn = 'paper_file';
            break;
        case 'conference':
            $tableName = 'conference_tab';
            $fileColumn = 'certificate_path';
            break;
        case 'patents':
            $tableName = 'patents_table';
            $fileColumn = 'patent_file';
            break;
        case 'fdps':
        default:
            $tableName = 'fdps_tab';
            $fileColumn = 'certificate';
            break;
    }

    // DELETE ACTION
    if ($action == 'delete') {
        foreach ($selectedFiles as $fileId) {
            $sql = "SELECT $fileColumn FROM $tableName WHERE id = ? AND branch = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $fileId, $dept);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();

            if ($file && !empty($file[$fileColumn])) {
                if (file_exists($file[$fileColumn])) {
                    unlink($file[$fileColumn]);
                }

                $sql = "DELETE FROM $tableName WHERE id = ? AND branch = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("is", $fileId, $dept);
                $stmt->execute();
            }
        }
        echo "<script> alert('Records deleted successfully.'); window.location.href = '?dept=" . urlencode($dept) . "&catg=" . urlencode($catg) . "';
        function viewSingleFile(filePath) {
            window.open('../common/view_file1.php?file_path=' + encodeURIComponent(filePath), '_blank');
        }
</script>";
        exit;
    }

    // DOWNLOAD ACTION
    elseif ($action == 'download') {
        // Clean any previous output to prevent headers already sent
        if (ob_get_length()) {
            ob_end_clean();
        }

        if (count($selectedFiles) == 1) {
            $fileId = $selectedFiles[0];
            $sql = "SELECT $fileColumn FROM $tableName WHERE id = ? AND branch = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $fileId, $dept);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();

            if ($file && !empty($file[$fileColumn]) && file_exists($file[$fileColumn])) {
                $filePath = $file[$fileColumn];

                // Set headers and send file
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                header('Content-Length: ' . filesize($filePath));
                ob_clean();
                flush();
                readfile($filePath);
                exit;
            } else {
                echo "<script>alert('File not found.'); window.location.href = window.location.href;</script>";
                exit;
            }
        } else {
            // Handle multiple file download via ZIP
            $zip = new ZipArchive();
            $safe_category = preg_replace('/[^\w.-]/', '', (string)$category);
            $zipFileName = $safe_category . "_files_" . time() . ".zip";
            $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                foreach ($selectedFiles as $fileId) {
                    $sql = "SELECT $fileColumn FROM $tableName WHERE id = ? AND branch = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $fileId, $dept);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $file = $result->fetch_assoc();

                    if ($file && !empty($file[$fileColumn]) && file_exists($file[$fileColumn])) {
                        $zip->addFile($file[$fileColumn], basename($file[$fileColumn]));
                    }
                }
                $zip->close();

                // Send ZIP headers
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($zipFileName) . '"');
                header('Content-Length: ' . filesize($zipFilePath));
                ob_clean();
                flush();
                readfile($zipFilePath);
                unlink($zipFilePath); // cleanup temp file
                exit;
            } else {
                echo "<script>alert('Failed to create zip file.'); window.location.href = window.location.href;</script>";
                exit;
            }
        }
    }


}

///------------------------------------------------------------------------------------------------------------
// SQL query to fetch all records from the fdps_tab table

/**
 * Exports FDPs Attended records to Excel.
 */
function exportFdpsAttended($conn, $dept) {
    ob_end_clean();
    ob_start();
    header(TYPE_EXCEL);
    header('Content-Disposition: attachment; filename=fdps_records.xls');

    echo "Username\tBranch\tTitle\tDate From\tDate To\tOrganised By\tLocation\n";

    $sql = "SELECT * FROM fdps_tab WHERE branch = ?" . SQL_AND_STATUS_EQ . STATUS_ACCEPTED . "'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dept);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo implode("\t", [
            $row['username'], $row['branch'], $row['title'], $row['date_from'],
            $row['date_to'], $row['organised_by'], $row['location']
        ]) . "\n";
    }
    ob_end_flush();
    exit;
}

/**
 * Exports FDPs Organized records to Excel.
 */
function exportFdpsOrganized($conn, $dept) {
    ob_end_clean();
    ob_start();
    header(TYPE_EXCEL);
    header('Content-Disposition: attachment; filename=fdps_organized.xls');

    echo "Username\tBranch\tTitle\tDate From\tDate To\tOrganised By\tLocation\n";

    $sql = "SELECT * FROM fdps_org_tab WHERE branch = ?" . SQL_AND_STATUS_EQ . STATUS_ACCEPTED . "'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dept);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo implode("\t", [
            $row['username'], $row['branch'], $row['title'], $row['date_from'],
            $row['date_to'], $row['organised_by'], $row['location']
        ]) . "\n";
    }
    ob_end_flush();
    exit;
}

/**
 * Exports Published Papers records to Excel.
 */
function exportPublishedPapers($conn, $dept) {
    ob_end_clean();
    ob_start();
    header(TYPE_EXCEL);
    header('Content-Disposition: attachment; filename=published_papers.xls');

    echo "Username\tBranch\tPaper Title\tJournal Name\tIndexing\tDate of Submission\tQuality Factor\tImpact Factor\tPayment\n";

    $sql = "SELECT * FROM published_tab WHERE branch = ?" . SQL_AND_STATUS_EQ . STATUS_ACCEPTED . "'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dept);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo implode("\t", [
            $row['username'], $row['branch'], $row['paper_title'], $row['journal_name'],
            $row['indexing'], $row['date_of_submission'], $row['quality_factor'],
            $row['impact_factor'], $row['payment']
        ]) . "\n";
    }
    ob_end_flush();
    exit;
}

/**
 * Exports Conference Papers records to Excel.
 */
function exportConferencePapers($conn, $dept) {
    ob_end_clean();
    ob_start();
    header(TYPE_EXCEL);
    header('Content-Disposition: attachment; filename=conference_papers.xls');

    echo "Username\tBranch\tPaper Title\tFrom Date\tTo Date\tOrganised By\tLocation\tPaper Type\n";

    $sql = "SELECT * FROM conference_tab WHERE branch = ?" . SQL_AND_STATUS_EQ . STATUS_ACCEPTED . "'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dept);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo implode("\t", [
            $row['username'], $row['branch'], $row['paper_title'], $row['from_date'],
            $row['to_date'], $row['organised_by'], $row['location'], $row['paper_type']
        ]) . "\n";
    }
    ob_end_flush();
    exit;
}

/**
 * Exports Patents records to Excel.
 */
function exportPatents($conn, $dept) {
    ob_end_clean();
    ob_start();
    header(TYPE_EXCEL);
    header('Content-Disposition: attachment; filename=patents.xls');

    echo "Username\tBranch\tPatent Title\tDate of Issue\n";

    $sql = "SELECT * FROM patents_table WHERE branch = ?" . SQL_AND_STATUS_EQ . STATUS_ACCEPTED . "'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dept);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo implode("\t", [
            $row['Username'], $row['branch'], $row['patent_title'], $row['date_of_issue']
        ]) . "\n";
    }
    ob_end_flush();
    exit;
}

if (isset($_POST['export_fdps'])) {
    exportFdpsAttended($conn, $_POST['dept']);
}
if (isset($_POST['export_fdps_org'])) {
    exportFdpsOrganized($conn, $_POST['dept'] ?? $dept);
}
if (isset($_POST['export_published'])) {
    exportPublishedPapers($conn, $dept);
}
if (isset($_POST['export_conference'])) {
    exportConferencePapers($conn, $dept);
}
if (isset($_POST['export_patent'])) {
    exportPatents($conn, $dept);
}
//---------------------------------------------------------------------------------------------------------------------------------

$extra_head = '<link rel="stylesheet" href="../../assets/css/download_pap.css"><script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>';
include_once "../../includes/header.php";

?>

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
                <span class="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                        href="../jr_assistant/jr_acd_year.php?dept=<?php echo urlencode((string)$dept); ?>"
                        class="home-icon">jr_assistant</a></span>
            <?php else: ?>
                <span class="sp">&nbsp; >> &nbsp;</span><span class="sid"><a href="dc_acd_year.php?dept=<?php echo urlencode((string)$dept); ?>"
                        class="home-icon">dept_coordinator</a></span>
            <?php endif; ?>
            <span class="sp">&nbsp; >> &nbsp;</span><span class="main"><a href="#"
                    class="main-a"><?php echo htmlspecialchars(($catg === 'fdps') ? 'fdps_attended' : (string)$catg); ?>_Files</a></span>
            <span class="sp">&nbsp; >> &nbsp;</span>
        </div>
    </div>
</nav>

<div class="div1">
    <div class="filter-section">

        <h1><?php echo htmlspecialchars(($catg === 'fdps') ? 'fdps_attended' : (string)$catg); ?> Files</h1>
        <form method="POST" class="filter-form">
            <input type="hidden" name="action_F" value="<?php echo htmlspecialchars($catg); ?>">
            <?php
            $preselected_dept = $_GET['dept'] ?? $_POST['dept'] ?? '';

            if ($preselected_dept) {
                echo '<input type="hidden" name="dept" value="' . htmlspecialchars($preselected_dept) . '">';
                // echo '<h2>Department: ' . htmlspecialchars($preselected_dept) . '</h2>'; // Optional: Display dept name
            } else {
                ?>
                <select name="dept" id="dept">
                    <option value="" disabled selected>Select Department</option>
                    <option value="CSE" <?= isset($_POST['dept']) && $_POST['dept'] == 'CSE' ? 'selected' : '' ?>>CSE
                    </option>
                    <option value="AIML" <?= isset($_POST['dept']) && $_POST['dept'] == 'AIML' ? 'selected' : '' ?>>AIML
                    </option>
                    <option value="AIDS" <?= isset($_POST['dept']) && $_POST['dept'] == 'AIDS' ? 'selected' : '' ?>>AIDS
                    </option>
                    <option value="IT" <?= isset($_POST['dept']) && $_POST['dept'] == 'IT' ? 'selected' : '' ?>>IT</option>
                    <option value="ECE" <?= isset($_POST['dept']) && $_POST['dept'] == 'ECE' ? 'selected' : '' ?>>ECE
                    </option>
                    <option value="EEE" <?= isset($_POST['dept']) && $_POST['dept'] == 'EEE' ? 'selected' : '' ?>>EEE
                    </option>
                    <option value="MECH" <?= isset($_POST['dept']) && $_POST['dept'] == 'MECH' ? 'selected' : '' ?>>MECH
                    </option>
                    <option value="CIVIL" <?= isset($_POST['dept']) && $_POST['dept'] == 'CIVIL' ? 'selected' : '' ?>>CIVIL
                    </option>
                    <option value="BSH" <?= isset($_POST['dept']) && $_POST['dept'] == 'BSH' ? 'selected' : '' ?>>BSH
                    </option>
                </select>
            <?php } ?>
            <button type="submit" name="sel_btn" class="filter-button">Show Results</button>
        </form>
    </div>

    <?php
    $preselected_dept = $_GET['dept'] ?? $_POST['dept'] ?? '';
    $preselected_catg = $_GET['action_F'] ?? $_POST['action_F'] ?? $catg;

    if (($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sel_btn']) && isset($_POST['dept']) && isset($_POST['action_F'])) || ($preselected_dept && $preselected_catg)) {
            $dept = $preselected_dept;
            $category = $preselected_catg;

            switch ($category) {
                case 'fdps':
                    // Display FDPs Attended
                    echo "<div class='container11'>
                            <h2>FDPs Attended</h2>";
                    echo "<form method='POST' class='ex_b'>
                            <input type='hidden' name='dept' value='" . htmlspecialchars($dept) . "'>
                            <button type='submit' class='ex_bt' name='export_fdps'>Export to Excel</button>
                          </form>";

                    $sql_fdps = "SELECT * FROM fdps_tab WHERE branch = ? AND status = 'Accepted'";
                    $stmt_fdps = $conn->prepare($sql_fdps);
                    $stmt_fdps->bind_param("s", $dept);
                    $stmt_fdps->execute();
                    $result_fdps = $stmt_fdps->get_result();

                    if ($result_fdps->num_rows > 0) {
                        echo "<form method='POST' action=''>
                                <input type='hidden' name='category' value='fdps'>
                                <table border='1'>
                                    <tr>
                                        <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                                        <th>Username</th>
                                        <th>Branch</th>
                                        <th>Title</th>
                                        <th>Date From</th>
                                        <th>Date To</th>
                                        <th>Organised By</th>
                                        <th>Location</th>
                                    </tr>";

                        while ($row = $result_fdps->fetch_assoc()) {
                            $certificatePath = fixPath($row["certificate"]);
                            $fdps_raw = json_encode(array_values(array_filter([$certificatePath], fn($f) => strlen($f) > 3)), JSON_UNESCAPED_SLASHES);
                            $fdps_json = str_replace('"', HTM_QUOT, $fdps_raw);
                            echo "<td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "'
                                                " . ATTR_DATA_FILEPATH . $certificatePath . "'
                                                " . DATA_FILES_PREFIX . $fdps_json . "' onchange='trackOrder(event)'></td>
                                    <td>" . htmlspecialchars($row["username"]) . "</td>
                                    <td>" . htmlspecialchars($row["branch"]) . "</td>
                                    <td>" . htmlspecialchars($row["title"]) . "</td>
                                    <td>" . htmlspecialchars($row["date_from"]) . "</td>
                                    <td>" . htmlspecialchars($row["date_to"]) . "</td>
                                    <td>" . htmlspecialchars($row["organised_by"]) . "</td>
                                    <td>" . htmlspecialchars($row["location"]) . "</td>
                                  </tr>";
                        }
                        echo "</table><br>
                                <div class='bulk-actions'>
                                    <button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button>
                                    <button type='submit' name='action' class='btn download-btn' value='download'>Download Selected</button>
                                    <button type='button' id='mergeBtn' class='merge' onclick='mergePDFs()' disabled>Merge PDFs</button>&nbsp
                                    <button type='submit' name='action' class='btn delete-btn' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button>
                                    <br>
                                    <button type='button' class='merge' id='mergedFileButton' onclick='viewMergedFile()' style='display:none;'>View Merged File</button>

                              </form>";
                    } else {
                        echo "<p class='no-files'>No FDPs attended found.</p>";
                    }
                    echo "</div>";
                    break;


                case 'fdps_org':
                    // Display FDPs Organised
                    echo "<div class='container11'>
                            <h2>FDPs Organised</h2>";
                    echo "<form method='POST' class='ex_b'>
                            <input type='hidden' name='dept' value='" . htmlspecialchars($dept) . "'>
                            <button type='submit' class='ex_bt' name='export_fdps_org'>Export to Excel</button>
                          </form>";

                    $sql_fdps_org = "SELECT * FROM fdps_org_tab WHERE branch = ? AND status = 'Accepted'";
                    $stmt_fdps_org = $conn->prepare($sql_fdps_org);
                    $stmt_fdps_org->bind_param("s", $dept);
                    $stmt_fdps_org->execute();
                    $result_fdps_org = $stmt_fdps_org->get_result();

                    if ($result_fdps_org->num_rows > 0) {
                        echo "<form method='POST' action=''>
                            <input type='hidden' name='category' value='fdps_org'>
                                <table border='1'>
                                    <tr>
                                        <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                                        <th>Username</th>
                                        <th>Branch</th>
                                        <th>Title</th>
                                        <th>Date From</th>
                                        <th>Date To</th>
                                        <th>Organised By</th>
                                        <th>Location</th>
                                    </tr>";

                        while ($row = $result_fdps_org->fetch_assoc()) {
                            $certificatePath = htmlspecialchars($row["certificate"]);
                            $brochurePath = htmlspecialchars($row["brochure"]);
                            $schedulePath = htmlspecialchars($row["fdp_schedule_invitation"]);
                            $attendancePath = htmlspecialchars($row["attendance_forms"]);
                            $feedbackPath = htmlspecialchars($row["feedback_forms"]);
                            $reportPath = htmlspecialchars($row["fdp_report"]);
                            $photo1Path = htmlspecialchars($row["photo1"]);
                            $photo2Path = htmlspecialchars($row["photo2"]);
                            $photo3Path = htmlspecialchars($row["photo3"]);

                            // Prepare files for merging in specific order
                            $files_to_merge = [
                                $brochurePath,
                                $schedulePath,
                                $attendancePath,
                                $feedbackPath,
                                $reportPath,
                                $photo1Path,
                                $photo2Path,
                                $photo3Path,
                                $certificatePath
                            ];
                            $files_to_merge = array_filter($files_to_merge, function ($f) {
                                return strlen($f) > 3;
                            });
                            $files_json = htmlspecialchars(json_encode(array_values($files_to_merge)), ENT_QUOTES, 'UTF-8');
                            $record_title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');

                            $hasMerged = (!empty($row['merged_file']) && file_exists("../../" . $row['merged_file']));
                            $mergedPath = $hasMerged ? "../../" . htmlspecialchars($row['merged_file']) : "";

                            echo "<tr>
                                     <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "'
                                         data-filepath='$mergedPath'
                                         data-files='$files_json'
                                         data-title='$record_title'></td>
                                     <td>" . htmlspecialchars($row["username"]) . "</td>
                                     <td>" . htmlspecialchars($row["branch"]) . "</td>
                                     <td>" . htmlspecialchars($row["title"]) . "</td>
                                     <td>" . htmlspecialchars($row["date_from"]) . "</td>
                                     <td>" . htmlspecialchars($row["date_to"]) . "</td>
                                     <td>" . htmlspecialchars($row["organised_by"]) . "</td>
                                     <td>" . htmlspecialchars($row["location"]) . "</td>
                                 </tr>";
                        }
                        echo "</table>
                                <div class='bulk-actions'>
                                    <button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button>
                                    <button type='button' class='btn download-btn' onclick='bulkDownload()'>Download Selected</button>
                                     <button type='button' id='mergeBtn' class='merge' onclick='mergePDFs()' disabled>Merge PDFs</button>&nbsp
                                    <button type='submit' name='action' class='btn delete-btn' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button>
                                    <br>
                                    <button type='button' class='merge' id='mergedFileButton' onclick='viewMergedFile()' style='display:none;'>View Merged File</button>
                                </div>
                              </form>";
                    } else {
                        echo "<p class='no-files'>No FDPs organised found.</p>";
                    }
                    echo "</div>";
                    break;

                case 'published':
                    echo "<div class='container11'>
                                <h2>Published Papers</h2>";
                    echo "<form method='POST' class='ex_b'>
                                <input type='hidden' name='dept' value='" . htmlspecialchars($dept) . "'>
                                <button type='submit' class='ex_bt' name='export_published'>Export to Excel</button>
                              </form>";

                    $sql_published = "SELECT * FROM published_tab WHERE branch = ? AND status = 'Accepted'";
                    $stmt_published = $conn->prepare($sql_published);
                    $stmt_published->bind_param("s", $dept);
                    $stmt_published->execute();
                    $result_published = $stmt_published->get_result();

                    if ($result_published->num_rows > 0) {
                        echo "<form method='POST' action='../common/download_papers1.php'>
                                    <input type='hidden' name='category' value='published'>
                                    <input type='hidden' name='table' value='published_tab'>
                                    <input type='hidden' name='file_column' value='paper_file'>
                                    <table border='1'>
                                        <tr>
                                            <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                                            <th>Username</th>
                                            <th>Branch</th>
                                            <th>Paper Title</th>
                                            <th>Journal Name</th>
                                            <th>Indexing</th>
                                            <th>Date of Submission</th>
                                            <th>Quality Factor</th>
                                            <th>Impact Factor</th>
                                            <th>Payment</th>

                                        </tr>";

                        while ($row = $result_published->fetch_assoc()) {
                            $paperFilePath = fixPath($row["paper_file"]);
                            $pub_raw = json_encode(array_values(array_filter([$paperFilePath], fn($f) => strlen($f) > 3)), JSON_UNESCAPED_SLASHES);
                            $pub_json = str_replace('"', HTM_QUOT, $pub_raw);


                            echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "'
                                        data-filepath='" . $paperFilePath . "'
                                        data-files='" . $pub_json . "'></td>
                                        <td>" . htmlspecialchars($row["username"]) . "</td>
                                        <td>" . htmlspecialchars($row["branch"]) . "</td>
                                        <td>" . htmlspecialchars($row["paper_title"]) . "</td>
                                        <td>" . htmlspecialchars($row["journal_name"]) . "</td>
                                        <td>" . htmlspecialchars($row["indexing"]) . "</td>
                                        <td>" . htmlspecialchars($row["date_of_submission"]) . "</td>
                                        <td>" . htmlspecialchars($row["quality_factor"]) . "</td>
                                        <td>" . htmlspecialchars($row["impact_factor"]) . "</td>
                                        <td>" . htmlspecialchars($row["payment"]) . "</td>
                                     </tr>";
                        }

                        echo "</table>
                                    <div class='bulk-actions'>
                                    <button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button>
                                    <button type='submit' class='btn download-btn' name='action' value='download'>Download Selected</button>
                                    <button type='button' id='mergeBtn' class='merge' onclick='mergePDFs()' disabled>Merge PDFs</button>&nbsp
                                    <button type='submit' class='btn delete-btn' name='action' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button>
                                    <br>
                                    <button type='button' class='merge' id='mergedFileButton' onclick='viewMergedFile()' style='display:none;'>View Merged File</button>

                                  </form>";
                    } else {
                        echo "<p class='no-files'>No published papers found.</p>";
                    }
                    echo "</div>";
                    break;


                case 'conference':
                    // Display Conference Papers
                    echo "<div class='container11'>
                                    <h2>Conference Papers</h2>";
                    echo "<form method='POST' class='ex_b'>
                                    <input type='hidden' name='dept' value='" . htmlspecialchars($dept) . "'>
                                    <button type='submit' class='ex_bt' name='export_conference'>Export to Excel</button>
                                  </form>";

                    $sql_conference = "SELECT * FROM conference_tab WHERE Branch = ? AND status = 'Accepted'";
                    $stmt_conference = $conn->prepare($sql_conference);
                    $stmt_conference->bind_param("s", $dept);
                    $stmt_conference->execute();
                    $result_conference = $stmt_conference->get_result();

                    if ($result_conference->num_rows > 0) {
                        echo "<form method='POST' action=''>
                                        <input type='hidden' name='category' value='conference'>
                                        <table border='1'>
                                            <tr>
                                                <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                                                <th>Username</th>
                                                <th>Branch</th>
                                                <th>Paper Title</th>
                                                <th>From Date</th>
                                                <th>To Date</th>
                                                <th>Organised By</th>
                                                <th>Location</th>
                                                <th>Paper Type</th>
                                             </tr>";

                        while ($row = $result_conference->fetch_assoc()) {
                            $certificatePath = fixPath($row["certificate_path"]);
                            $paperFilePath = fixPath($row["paper_file_path"]);
                            $cf_arr = array_values(array_filter([$certificatePath, $paperFilePath], fn($f) => strlen($f) > 3));
                            $cf_raw = json_encode($cf_arr, JSON_UNESCAPED_SLASHES);
                            $cf_json = str_replace('"', '&quot;', $cf_raw);

                            echo "<tr>
                                            <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "'
                                                data-filepath='" . $certificatePath . "'
                                                data-files='" . $cf_json . "'></td>
                                            <td>" . htmlspecialchars($row["username"]) . "</td>
                                            <td>" . htmlspecialchars($row["branch"]) . "</td>
                                            <td>" . htmlspecialchars($row["paper_title"]) . "</td>
                                            <td>" . htmlspecialchars($row["from_date"]) . "</td>
                                            <td>" . htmlspecialchars($row["to_date"]) . "</td>
                                            <td>" . htmlspecialchars($row["organised_by"]) . "</td>
                                            <td>" . htmlspecialchars($row["location"]) . "</td>
                                            <td>" . htmlspecialchars($row["paper_type"]) . "</td>
                                         </tr>";
                        }

                        echo "</table>
                                        <div class='bulk-actions'>
                                            <button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button>
                                    <button type='button' class='btn download-btn' onclick='bulkDownload()'>Download Selected</button>
                                        <button type='button'  id='mergeBtn' class='merge' onclick='mergePDFs()' disabled>Merge PDFs</button>&nbsp
                                    <button type='submit' class='btn delete-btn' name='action' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button>
                                    <br>
                                    <button type='button'  class='merge' id='mergedFileButton' onclick='viewMergedFile()' style='display:none;'>View Merged File</button>

                                      </form>";
                    } else {
                        echo "<p class='no-files'>No conference papers found.</p>";
                    }
                    echo "</div>";
                    break;


                case 'patents':
                    // Display Patents
                    echo "<div class='container11'>
                                <h2>Patents</h2>";
                    echo "<form method='POST' class='ex_b'>
                                <input type='hidden' name='dept' value='" . htmlspecialchars($dept) . "'>
                                <button type='submit' class='ex_bt' name='export_patent'>Export to Excel</button>
                              </form>";

                    $sql_patents = "SELECT * FROM patents_table WHERE branch = ? AND status = 'Accepted'";
                    $stmt_patents = $conn->prepare($sql_patents);
                    $stmt_patents->bind_param("s", $dept);
                    $stmt_patents->execute();
                    $result_patents = $stmt_patents->get_result();

                    if ($result_patents->num_rows > 0) {
                        echo "<form method='POST' action='' enctype='multipart/form-data'>
                                    <input type='hidden' name='category' value='patents'>
                                    <input type='hidden' name='table_name' value='patents_table'>
                                    <input type='hidden' name='file_column' value='patent_file'>
                                    <table border='1'>
                                        <tr>
                                            <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                                            <th>Username</th>
                                            <th>Branch</th>
                                            <th>Patent Title</th>
                                            <th>Date of Issue</th>

                                        </tr>";

                        while ($row = $result_patents->fetch_assoc()) {
                            $patentFilePath = fixPath($row["patent_file"]);
                            $pat_raw = json_encode(array_values(array_filter([$patentFilePath], fn($f) => strlen($f) > 3)), JSON_UNESCAPED_SLASHES);
                            $pat_json = str_replace('"', '&quot;', $pat_raw);


                            echo "<tr>
                                            <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "'
                                                data-filepath='" . $patentFilePath . "'
                                                data-files='" . $pat_json . "' onchange='trackOrder(event)'></td>
                                            <td>" . htmlspecialchars($row["Username"]) . "</td>
                                            <td>" . htmlspecialchars($row["branch"]) . "</td>
                                            <td>" . htmlspecialchars($row["patent_title"]) . "</td>
                                            <td>" . htmlspecialchars($row["date_of_issue"]) . "</td>
                                    </tr>";
                        }

                        echo "</table>
                                    <div class='bulk-actions'>
                                        <button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button>
                                        <button type='submit' class='btn download-btn' name='action' value='download'>Download Selected</button>
                                        <button type='button' id='mergeBtn' class='merge' onclick='mergePDFs()' disabled>Merge PDFs</button>&nbsp
                                    <button type='submit' class='btn delete-btn' name='action' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button>
                                    <br>
                                    <button type='button' class='merge' id='mergedFileButton' onclick='viewMergedFile()' style='display:none;'>View Merged File</button>

                                  </form>";
                    } else {
                        echo "<p class='no-files'>No patents found.</p>";
                    }
                    echo "</div>";
                    break;
                default:
                    echo "<div class='container11'><p class='no-files'>Invalid category selected.</p></div>";
                    break;

            }
        }
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

    function handleFileTypeChange(selectElem, rowId) {
        const selectedOption = selectElem.options[selectElem.selectedIndex];
        const selectedPath = selectedOption.getAttribute("data-path");

        // Find the corresponding checkbox by rowId
        const checkbox = document.querySelector(`input[name="selected_files[]"][value="${rowId}"]`);
        if (checkbox) {
            checkbox.setAttribute("data-filepath", selectedPath);
        }
    }

    function toggleSelectAll(source) {
        const checkboxes = document.querySelectorAll('input[name="selected_files[]"]');
        selectedOrder = []; // Reset list

        checkboxes.forEach(cb => {
            cb.checked = source.checked;

            const filePath = cb.dataset.filepath;
            if (source.checked && filePath) {
                selectedOrder.push(filePath);
            }
        });

        updateMergeButton();
    }


    async function mergeAndAct(cb, action) {
        const filePath = cb.getAttribute('data-filepath');
        const filesJson = cb.getAttribute('data-files');
        const title = cb.getAttribute('data-title') || 'record';

        if (filePath && filePath !== '') {
            if (action === 'view') {
                window.open('../common/view_file1.php?file_path=' + encodeURIComponent(filePath), '_blank');
            } else {
                let link = document.createElement('a');
                link.href = filePath;
                link.download = '';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
            return;
        }

        if (filesJson && filesJson !== '') {
            const files = JSON.parse(filesJson);
            const { PDFDocument } = PDFLib;
            const mergedPdf = await PDFDocument.create();
            let addedPages = 0;

            for (const fileUrl of files) {
                try {
                    const response = await fetch(fileUrl);
                    if (!response.ok) continue;
                    const fileArrayBuffer = await response.arrayBuffer();
                    const ext = fileUrl.split('.').pop().toLowerCase();

                    if (ext === 'pdf') {
                        const pdf = await PDFDocument.load(fileArrayBuffer);
                        const pages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                        pages.forEach(page => mergedPdf.addPage(page));
                        addedPages += pages.length;
                    } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
                        let image;
                        if (ext === 'png') image = await mergedPdf.embedPng(fileArrayBuffer);
                        else image = await mergedPdf.embedJpg(fileArrayBuffer);
                        const { width, height } = image.scale(1);
                        const page = mergedPdf.addPage([width, height]);
                        page.drawImage(image, { x: 0, y: 0, width, height });
                        addedPages++;
                    }
                } catch (e) { console.error(e); }
            }

            if (addedPages > 0) {
                const mergedPdfBytes = await mergedPdf.save();
                const url = URL.createObjectURL(new Blob([mergedPdfBytes], { type: 'application/pdf' }));
                if (action === 'view') {
                    window.open(url, '_blank');
                } else {
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = `FDP_Organized_${title.replace(/\W/gi, '_')}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
        } else if (filePath) {
            if (action === 'view') {
                window.open('../common/view_file1.php?file_path=' + encodeURIComponent(filePath), '_blank');
            } else {
                let link = document.createElement('a');
                link.href = filePath;
                link.download = '';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    }

    async function bulkView() {
        const checkboxes = document.querySelectorAll('input[name="selected_files[]"]:checked');
        if (checkboxes.length === 0) {
            alert('Please select at least one file to view.');
            return;
        }

        // Single item with no data-files → direct view via view_file1.php
        if (checkboxes.length === 1 && !checkboxes[0].getAttribute('data-files')) {
            await mergeAndAct(checkboxes[0], 'view');
            return;
        }

        // Merge ALL selected records' files into one PDF
        const { PDFDocument } = PDFLib;
        const mergedPdf = await PDFDocument.create();
        let addedPages = 0;

        for (const cb of checkboxes) {
            const filesJson = cb.getAttribute('data-files');
            const filePath = cb.getAttribute('data-filepath');

            let filesToProcess = [];
            if (filesJson && filesJson !== '') {
                const decodedJson = filesJson.replace(/&quot;/g, '"').replace(/&#x2F;/g, '/').replace(/&amp;/g, '&');
                filesToProcess = JSON.parse(decodedJson).filter(f => f && f.length > 0);
            }
            if (filesToProcess.length === 0 && filePath && filePath !== '') {
                filesToProcess = [filePath];
            }

            for (const fileUrl of filesToProcess) {
                try {
                    const response = await fetch(fileUrl);
                    if (!response.ok) { console.warn('Cannot fetch:', fileUrl); continue; }
                    const buf = await response.arrayBuffer();
                    const ext = fileUrl.split('.').pop().toLowerCase().split('?')[0];

                    if (ext === 'pdf') {
                        const pdf = await PDFDocument.load(buf);
                        const pages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                        pages.forEach(p => mergedPdf.addPage(p));
                        addedPages += pages.length;
                    } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
                        const img = ext === 'png' ? await mergedPdf.embedPng(buf) : await mergedPdf.embedJpg(buf);
                        const { width, height } = img.scale(1);
                        const page = mergedPdf.addPage([width, height]);
                        page.drawImage(img, { x: 0, y: 0, width, height });
                        addedPages++;
                    }
                } catch (e) { console.error('Error merging:', fileUrl, e); }
            }
        }

        if (addedPages > 0) {
            const bytes = await mergedPdf.save();
            const url = URL.createObjectURL(new Blob([bytes], { type: 'application/pdf' }));
            window.open(url, '_blank');
        } else {
            alert('Could not process any files. Ensure the selected files are valid PDFs or images.');
        }
    }

    async function bulkDownload() {
        const checkboxes = document.querySelectorAll('input[name="selected_files[]"]:checked');
        if (checkboxes.length === 0) {
            alert('Please select at least one file to download.');
            return;
        }
        for (const cb of checkboxes) {
            await mergeAndAct(cb, 'download');
        }
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
                const response = await fetch(fileUrl);
                if (!response.ok) throw new Error(`Failed to fetch ${fileUrl}`);
                const fileArrayBuffer = await response.arrayBuffer();
                const ext = fileUrl.split('.').pop().toLowerCase();

                if (ext === 'pdf') {
                    const pdf = await PDFDocument.load(fileArrayBuffer);
                    const pages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                    pages.forEach(page => mergedPdf.addPage(page));
                } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
                    let image;
                    if (ext === 'png') {
                        image = await mergedPdf.embedPng(fileArrayBuffer);
                    } else {
                        image = await mergedPdf.embedJpg(fileArrayBuffer);
                    }
                    const { width, height } = image.scale(1);
                    const page = mergedPdf.addPage([width, height]);
                    page.drawImage(image, { x: 0, y: 0, width, height });
                }
            } catch (error) {
                console.error("Error processing file:", fileUrl, error);
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
                    const btn = document.getElementById("mergedFileButton");
                    btn.style.display = "block";
                    btn.setAttribute("data-url", data.fileUrl);
                } else {
                    alert("Failed to save the merged file.");
                }
            })
            .catch(error => {
                alert("Failed to send the merged file.");
            });
    }

    async function mergeRecordFiles(filesJson, title) {
        const files = JSON.parse(filesJson);
        if (files.length === 0) {
            alert("No files found for this record.");
            return;
        }

        const { PDFDocument } = PDFLib;
        const mergedPdf = await PDFDocument.create();
        let addedPages = 0;

        for (const fileUrl of files) {
            try {
                const response = await fetch(fileUrl);
                if (!response.ok) {
                    console.warn("Could not fetch file:", fileUrl);
                    continue;
                }
                const fileArrayBuffer = await response.arrayBuffer();
                const ext = fileUrl.split('.').pop().toLowerCase();

                if (ext === 'pdf') {
                    const pdf = await PDFDocument.load(fileArrayBuffer);
                    const pages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                    pages.forEach(page => mergedPdf.addPage(page));
                    addedPages += pages.length;
                } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
                    let image;
                    if (ext === 'png') {
                        image = await mergedPdf.embedPng(fileArrayBuffer);
                    } else {
                        image = await mergedPdf.embedJpg(fileArrayBuffer);
                    }
                    const { width, height } = image.scale(1);
                    const page = mergedPdf.addPage([width, height]);
                    page.drawImage(image, { x: 0, y: 0, width, height });
                    addedPages++;
                }
            } catch (e) {
                console.error("Error merging file:", fileUrl, e);
            }
        }

        if (addedPages === 0) {
            alert("Could not merge any files. Ensure they are valid PDFs or Images.");
            return;
        }

        const mergedPdfBytes = await mergedPdf.save();
        const blob = new Blob([mergedPdfBytes], { type: 'application/pdf' });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.download = `FDP_Organized_${title.replace(/\W/gi, '_')}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
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

</body>

</html>



