<?php
ob_start();
ini_set('display_errors', 0);
include "../../includes/connection.php";
require_once "../../includes/constants.php";

if (!isset($_SESSION['username'])) {
    die("You need to log in to view your uploads.");
}

function fixPath($p)
{
    if (empty($p)) {
        return "";
    }
    $p = htmlspecialchars_decode($p);
    $p = str_replace('\\', '/', $p);
    if (preg_match('/uploads\/.*/', $p, $matches)) {
        $foundPath = $matches[0];
        // Relative to modules/common/
        if (file_exists("../../" . $foundPath)) {
            return "../../" . $foundPath;
        } elseif (file_exists("../" . $foundPath)) {
            return "../" . $foundPath;
        } elseif (file_exists($foundPath)) {
            return $foundPath;
        }
        return "../../" . $foundPath; // Default for common
    }
    return $p;
}

$username = $_SESSION['username'];
if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}
ob_start();

$category = "fdps";
// Handle bulk actions
/**
 * Gets the table name and file column based on the category.
 * @param string $category
 * @return array
 */
function getCategoryConfig($category) {
    switch ($category) {
        case 'fdps':
            return ['tableName' => 'fdps_tab', 'fileColumn' => 'certificate'];
        case 'fdps_org':
            return ['tableName' => 'fdps_org_tab', 'fileColumn' => 'certificate'];
        case 'published':
            return ['tableName' => 'published_tab', 'fileColumn' => 'paper_file'];
        case 'conference':
            return ['tableName' => 'conference_tab', 'fileColumn' => 'certificate_path'];
        case 'patents':
            return ['tableName' => 'patents_table', 'fileColumn' => 'patent_file'];
        default:
            return ['tableName' => 'fdps_tab', 'fileColumn' => 'certificate'];
    }
}

/**
 * Handles bulk deletion of files.
 */
function handleBulkDelete($conn, $selectedFiles, $tableName, $fileColumn, $username) {
    foreach ($selectedFiles as $fileId) {
        $sql = "SELECT $fileColumn FROM $tableName WHERE id = ? AND username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $fileId, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $file = $result->fetch_assoc();

        if ($file && !empty($file[$fileColumn])) {
            if (file_exists($file[$fileColumn])) {
                unlink($file[$fileColumn]);
            }

            $sql = "DELETE FROM $tableName WHERE id = ? AND username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $fileId, $username);
            $stmt->execute();
        }
    }
    echo "<script>alert('Records deleted successfully.'); window.location.href = window.location.href;</script>";
    exit;
}

/**
 * Handles bulk download of files.
 */
function handleBulkDownload($conn, $selectedFiles, $tableName, $fileColumn, $username, $category) {
    if (ob_get_length()) {
        ob_end_clean();
    }

    if (count($selectedFiles) == 1) {
        $fileId = $selectedFiles[0];
        $sql = "SELECT $fileColumn FROM $tableName WHERE id = ? AND username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $fileId, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $file = $result->fetch_assoc();

        if ($file && !empty($file[$fileColumn])) {
            $filePathRaw = $file[$fileColumn];
            $p = str_replace('\\', '/', $filePathRaw);
            if (preg_match('/uploads\/.*/', $p, $matches)) {
                $p = "../../" . $matches[0];
            }

            $finalPath = file_exists($p) ? $p : (file_exists($filePathRaw) ? $filePathRaw : null);
            if ($finalPath) {
                header(TYPE_OCTET_STREAM);
                header(HEADER_CONTENT_DISPOSITION . basename($finalPath) . '"');
                header(HEADER_CONTENT_LENGTH . filesize($finalPath));
                ob_clean();
                flush();
                readfile($finalPath);
                exit;
            } else {
                $msg = 'File not found. Searched path: ' . $p;
                echo "<script>alert(" . json_encode($msg) . "); window.location.href = window.location.href;</script>";
                exit;
            }
        }
    } else {
        $zip = new ZipArchive();
        $zipFileName = $category . "_files_" . time() . ".zip";
        $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;
        $filesAdded = 0;

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($selectedFiles as $fileId) {
                $sql = "SELECT $fileColumn FROM $tableName WHERE id = ? AND username = ? AND status = 'Accepted'";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("is", $fileId, $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $file = $result->fetch_assoc();

                if ($file && !empty($file[$fileColumn])) {
                    $f = $file[$fileColumn];
                    $p = str_replace('\\', '/', $f);
                    if (preg_match('/uploads\/.*/', $p, $matches)) {
                        $p = "../../" . $matches[0];
                    }
                    if (file_exists($p)) {
                        $zip->addFile($p, basename($p));
                        $filesAdded++;
                    } elseif (file_exists($f)) {
                        $zip->addFile($f, basename($f));
                        $filesAdded++;
                    }
                }
            }
            $zip->close();

            if ($filesAdded > 0) {
                header('Content-Type: application/zip');
                header(HEADER_CONTENT_DISPOSITION . basename($zipFileName) . '"');
                header(HEADER_CONTENT_LENGTH . filesize($zipFilePath));
                ob_clean();
                flush();
                readfile($zipFilePath);
                unlink($zipFilePath);
                exit;
            } else {
                echo "<script>alert('No valid files were found to add to the ZIP.'); window.location.href = window.location.href;</script>";
                if (file_exists($zipFilePath)) unlink($zipFilePath);
                exit;
            }
        } else {
            echo "<script>alert('Failed to create zip file.'); window.location.href = window.location.href;</script>";
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['selected_files'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $action = $_POST['action'];
    $selectedFiles = $_POST['selected_files'];
    $category = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['category']);
    $config = getCategoryConfig($category);

    if ($action == 'delete') {
        handleBulkDelete($conn, $selectedFiles, $config['tableName'], $config['fileColumn'], $username);
    } elseif ($action == 'download') {
        handleBulkDownload($conn, $selectedFiles, $config['tableName'], $config['fileColumn'], $username, $category);
    }
}

///------------------------------------------------------------------------------------------------------------
// SQL query to fetch all records from the fdps_tab table

if (isset($_POST['export_fdps'])) {
    ob_end_clean();//End previous buffer
    ob_start();// start new buffer for excel
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=fdps_records.xls");

    // Write the Excel content header
    echo "Username\tBranch\tTitle\tDate From\tDate To\tOrganised By\tLocation\tSubmission Time\n";

    // Prepare and execute the SQL query
    $sql = "SELECT * FROM fdps_tab WHERE username = ? AND status = 'Accepted'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch and write data rows
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo $row['username'] . "\t" .
                $row['branch'] . "\t" .
                $row['title'] . "\t" .
                $row['date_from'] . "\t" .
                $row['date_to'] . "\t" .
                $row['organised_by'] . "\t" .
                $row['location'] . "\t" .
                $row['submission_time'] . "\n";
        }
    }

    // End script execution
    ob_end_flush();//End current buffer
    exit;
}

if (isset($_POST['export_fdps_org'])) {
    ob_end_clean();
    ob_start();
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=fdps_organized.xls");

    echo "Username\tBranch\tAcademic Year\tTitle\tDate From\tDate To\tOrganised By\tLocation\tSubmission Time\n";

    $sql = "SELECT * FROM fdps_org_tab WHERE username = ? AND status = 'Accepted'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo implode("\t", [
            $row['username'],
            $row['branch'],
            $row['year'],
            $row['title'],
            $row['date_from'],
            $row['date_to'],
            $row['organised_by'],
            $row['location'],
            $row['submission_time']
        ]) . "\n";
    }
    ob_end_flush();
    exit;
}

// Handle export for Published Papers
if (isset($_POST['export_published'])) {
    ob_end_clean();
    ob_start();
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=published_papers.xls");

    echo "Username\tBranch\tPaper Title\tJournal Name\tIndexing\tDate of Submission\tQuality Factor\tImpact Factor\tPayment\tSubmission Time\n";

    $sql = "SELECT * FROM published_tab WHERE username = ? AND status = 'Accepted'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo implode("\t", [
            $row['username'],
            $row['branch'],
            $row['paper_title'],
            $row['journal_name'],
            $row['indexing'],
            $row['date_of_submission'],
            $row['quality_factor'],
            $row['impact_factor'],
            $row['payment'],
            $row['submission_time']
        ]) . "\n";
    }
    ob_end_flush();
    exit;
}

// Handle export for Conference Papers
if (isset($_POST['export_conference'])) {
    ob_end_clean();
    ob_start();
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=conference_papers.xls");

    echo "Username\tBranch\tPaper Title\tFrom Date\tTo Date\tOrganised By\tLocation\tPaper Type\tSubmission Time\n";

    $sql = "SELECT * FROM conference_tab WHERE username = ? AND status = 'Accepted'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo implode("\t", [
            $row['username'],
            $row['branch'],
            $row['paper_title'],
            $row['from_date'],
            $row['to_date'],
            $row['organised_by'],
            $row['location'],
            $row['paper_type'],
            $row['submission_time']
        ]) . "\n";
    }
    ob_end_flush();
    exit;
}

// Handle export for Patents
if (isset($_POST['export_patent'])) {
    ob_end_clean();
    ob_start();
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=patents.xls");

    echo "Username\tBranch\tPatent Title\tDate of Issue\tSubmission Time\n";

    $sql = "SELECT * FROM patents_table WHERE username = ? AND status = 'Accepted'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo implode("\t", [
            $row['Username'],
            $row['branch'],
            $row['patent_title'],
            $row['date_of_issue'],
            $row['submission_time']
        ]) . "\n";
    }
    ob_end_flush();
    exit;
}
//---------------------------------------------------------------------------------------------------------------------------------

$extra_head = '<link rel="stylesheet" href="../../assets/css/download_pap.css">';
include "../../includes/header.php";
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
            <span>&nbsp; >> &nbsp; </span><span class="sid"><a
                    href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>"
                    class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
            <span>&nbsp; >> &nbsp; </span><span class="sid"><a href="../faculty/acd_year.php?dept=<?php echo urlencode((string)$dept); ?>"
                    class="home-icon"> Faculty </a></span>
            <span>&nbsp; >> &nbsp; </span><span class="main"> <a href="#" class="main-a"> My Achievements
                </a></span>
            <span>&nbsp; >> &nbsp; </span>
        </div>
    </div>
</nav>
<div class="div1">
    <div class="filter-section">
        <h1>My Achievements</h1>
        <form method="POST" class="filter-form">
            <select name="category" id="category">
                <option value="">Select Category</option>
                <option value="fdps" <?= isset($_POST['category']) && $_POST['category'] == 'fdps' ? 'selected' : '' ?>>
                    FDPs Attended</option>
                <option value="fdps_org" <?= isset($_POST['category']) && $_POST['category'] == 'fdps_org' ? 'selected' : '' ?>>FDPs Organised</option>
                <option value="published" <?= isset($_POST['category']) && $_POST['category'] == 'published' ? 'selected' : '' ?>>Published Papers</option>
                <option value="conference" <?= isset($_POST['category']) && $_POST['category'] == 'conference' ? 'selected' : '' ?>>Conference Papers</option>
                <option value="patents" <?= isset($_POST['category']) && $_POST['category'] == 'patents' ? 'selected' : '' ?>>Patents</option>
            </select>
            <button type="submit" class="filter-button">Show Results</button>
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $category = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['category']);

        switch ($category) {
            case 'fdps':
                // Display FDPs Attended
                echo "<div class='container11'>
                            <h2>FDPs Attended</h2>";
                echo "<form method='POST' class='ex_b'>
                            <button type='submit' class='ex_bt' name='export_fdps'>Export to Excel</button>
                          </form>";

                $sql_fdps = "SELECT * FROM fdps_tab WHERE username = ? AND status = 'Accepted'";
                $stmt_fdps = $conn->prepare($sql_fdps);
                $stmt_fdps->bind_param("s", $username);
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
                                        <th>Academic Year</th>
                                        <th>Title</th>
                                        <th>Date From</th>
                                        <th>Date To</th>
                                        <th>Organised By</th>
                                        <th>Location</th>
                                    </tr>";

                    while ($row = $result_fdps->fetch_assoc()) {
                        $certificatePath = fixPath($row["certificate"]);
                        $fdps_files_raw = json_encode(array_values(array_filter([$certificatePath], fn($f) => strlen($f) > 3)), JSON_UNESCAPED_SLASHES);
                        $fdps_files_json = str_replace('"', '&quot;', $fdps_files_raw);
                        echo "<tr>
                                    <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' 
                                        " . ATTR_DATA_FILEPATH . $certificatePath . QUOTE_SPACE . "
                                        data-files='" . $fdps_files_json . "'></td>
                                    <td>" . htmlspecialchars($row["username"]) . "</td>
                                    <td>" . htmlspecialchars($row["branch"]) . "</td>
                                    <td>" . htmlspecialchars($row["year"]) . "</td>
                                    <td>" . htmlspecialchars($row["title"]) . "</td>
                                    <td>" . htmlspecialchars($row["date_from"]) . "</td>
                                    <td>" . htmlspecialchars($row["date_to"]) . "</td>
                                    <td>" . htmlspecialchars($row["organised_by"]) . "</td>
                                    <td>" . htmlspecialchars($row["location"]) . "</td>
                                  </tr>";
                    }
                    echo "</table>
                                <br>
                                <div class='bulk-actions'>
                                    <button type='button' class='view-btn' onclick='bulkView()'>View Selected</button>
                                    <button type='submit' class='download-btn' name='action' value='download'>Download Selected</button>&nbsp
                                    <button type='submit' class='delete-btn' name='action' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button>
                                </div>
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
                            <button type='submit' class='ex_bt' name='export_fdps_org'>Export to Excel</button>
                          </form>";

                $sql_fdps_org = "SELECT * FROM fdps_org_tab WHERE username = ? AND status = 'Accepted'";
                $stmt_fdps_org = $conn->prepare($sql_fdps_org);
                $stmt_fdps_org->bind_param("s", $username);
                $stmt_fdps_org->execute();
                $result_fdps_org = $stmt_fdps_org->get_result();

                if ($result_fdps_org->num_rows > 0) {
                    echo "<form method='POST' action=''>
                                <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                                <input type='hidden' name='category' value='fdps_org'>
                                <table border='1'>
                                    <tr>
                                        <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                                        <th>Username</th>
                                        <th>Branch</th>
                                        <th>Academic Year</th>
                                        <th>Title</th>
                                        <th>Date From</th>
                                        <th>Date To</th>
                                        <th>Organised By</th>
                                        <th>Location</th>
                                    </tr>";

                    while ($row = $result_fdps_org->fetch_assoc()) {
                        $files_to_merge = [
                            fixPath($row['brochure']),
                            fixPath($row['fdp_schedule_invitation']),
                            fixPath($row['attendance_forms']),
                            fixPath($row['feedback_forms']),
                            fixPath($row['fdp_report']),
                            fixPath($row['photo1']),
                            fixPath($row['photo2']),
                            fixPath($row['photo3']),
                            fixPath($row['certificate'])
                        ];
                        $files_to_merge = array_filter($files_to_merge, function ($f) {
                            return strlen($f) > 3;
                        });
                        // Use JSON_UNESCAPED_SLASHES so paths like ../../uploads/... are preserved correctly.
                        // We use htmlspecialchars only on the final JSON string for safe HTML attribute embedding,
                        // BUT we must NOT encode slashes or the paths will break in JavaScript.
                        $files_json_raw = json_encode(array_values($files_to_merge), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                        // Escape only quotes for the HTML attribute (slashes must stay intact)
                        $files_json = str_replace('"', '&quot;', $files_json_raw);
                        $record_title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');


                        $merged_button = "";
                        $actual_merged_path = fixPath($row['merged_file']);
                        if (!empty($actual_merged_path) && file_exists($actual_merged_path)) {
                            $merged_button = "<a href='view_file1.php?file_path=" . urlencode($actual_merged_path) . "' target='_blank' class='download-btn' style='text-decoration: none; display: inline-block; background-color: #4CAF50;'>Merged PDF</a>";
                        } else {
                            $merged_button = "<button type='button' class='view-btn' style='background-color: #ff6347;' onclick='mergeRecordFiles(\"$files_json\", \"$record_title\")'>Merged PDF</button>";
                        }

                        $certificatePath = htmlspecialchars($row["certificate"]);
                        $brochurePath = htmlspecialchars($row["brochure"]);
                        $schedulePath = htmlspecialchars($row["fdp_schedule_invitation"]);
                        $attendancePath = htmlspecialchars($row["attendance_forms"]);
                        $feedbackPath = htmlspecialchars($row["feedback_forms"]);
                        $reportPath = htmlspecialchars($row["fdp_report"]);
                        $photo1Path = htmlspecialchars($row["photo1"]);
                        $photo2Path = htmlspecialchars($row["photo2"]);
                        $photo3Path = htmlspecialchars($row["photo3"]);

                        $deleteButton = "<form method='GET' action='' onsubmit='return confirm(\"Are you sure you want to delete this record?\")'>
                                            <input type='hidden' name='delete_id' value='" . $row["id"] . "'>
                                            <input type='hidden' name='table' value='fdps_org_tab'>
                                            <input type='hidden' name='file_column' value='certificate'>
                                            <input type='submit' id='del' value='Delete'>
                                          </form>";

                        $hasMerged = (!empty($actual_merged_path) && file_exists($actual_merged_path));
                        $mergedPath = $hasMerged ? $actual_merged_path : "";

                        echo "<tr>
                                    <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' 
                                        " . ATTR_DATA_FILEPATH . $mergedPath . QUOTE_SPACE . "
                                        data-files='" . $files_json . "'
                                        data-title='" . $record_title . "'></td>
                                    <td>" . htmlspecialchars($row["username"]) . "</td>
                                    <td>" . htmlspecialchars($row["branch"]) . "</td>
                                    <td>" . htmlspecialchars($row["year"]) . "</td>
                                    <td>" . htmlspecialchars($row["title"]) . "</td>
                                    <td>" . htmlspecialchars($row["date_from"]) . "</td>
                                    <td>" . htmlspecialchars($row["date_to"]) . "</td>
                                    <td>" . htmlspecialchars($row["organised_by"]) . "</td>
                                    <td>" . htmlspecialchars($row["location"]) . "</td>
                                </tr>";
                    }
                    echo "</table>
                        <br>
                                <div class='bulk-actions'>
                                    <button type='button' class='view-btn' onclick='bulkView()'>View Selected</button>
                                    <button type='button' class='download-btn' onclick='bulkDownload()'>Download Selected</button>&nbsp

                                    <button type='submit' class='delete-btn' name='action' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button>
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
                                <button type='submit' class='ex_bt' name='export_published'>Export to Excel</button>
                              </form>";

                $sql_published = "SELECT * FROM published_tab WHERE username = ? AND status = 'Accepted'";
                $stmt_published = $conn->prepare($sql_published);
                $stmt_published->bind_param("s", $username);
                $stmt_published->execute();
                $result_published = $stmt_published->get_result();

                if ($result_published->num_rows > 0) {
                    echo "<form method='POST' action=''>
                                    <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                                    <input type='hidden' name='category' value='published'>
                                    <input type='hidden' name='table' value='published_tab'>
                                    <input type='hidden' name='file_column' value='paper_file'>
                                    <table border='1'>
                                        <tr>
                                            <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                                            <th>Username</th>
                                            <th>Branch</th>
                                            <th>Academic Year</th>
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
                        $pub_files_raw = json_encode(array_values(array_filter([$paperFilePath], fn($f) => strlen($f) > 3)), JSON_UNESCAPED_SLASHES);
                        $pub_files_json = str_replace('"', '&quot;', $pub_files_raw);

                        echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' 
                                        " . ATTR_DATA_FILEPATH . $paperFilePath . QUOTE_SPACE . "
                                        data-files='" . $pub_files_json . "'></td>
                                        <td>" . htmlspecialchars($row["username"]) . "</td>
                                        <td>" . htmlspecialchars($row["branch"]) . "</td>
                                        <td>" . htmlspecialchars($row["year"]) . "</td>
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
                                    <br>
                                    <div class='bulk-actions'>
                                    <button type='button' class='view-btn' onclick='bulkView()'>View Selected</button>
                                    <button type='submit' class='download-btn' name='action' value='download'>Download Selected</button>&nbsp
                                    <button type='submit' class='delete-btn'  name='action' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button>
                                </div>
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
                                    <button type='submit' class='ex_bt' name='export_conference'>Export to Excel</button>
                                  </form>";

                $sql_conference = "SELECT * FROM conference_tab WHERE username = ? AND status = 'Accepted'";
                $stmt_conference = $conn->prepare($sql_conference);
                $stmt_conference->bind_param("s", $username);
                $stmt_conference->execute();
                $result_conference = $stmt_conference->get_result();

                if ($result_conference->num_rows > 0) {
                    echo "<form method='POST' action=''>
                                        <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                                     <input type='hidden' name='category' value='conference'>
                                        <table border='1'>
                                            <tr>
                                                <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                                                <th>Username</th>
                                                <th>Branch</th>
                                                <th>Academic Year</th>
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
                        $conf_files = array_values(array_filter([$certificatePath, $paperFilePath], fn($f) => strlen($f) > 3));
                        $conf_files_raw = json_encode($conf_files, JSON_UNESCAPED_SLASHES);
                        $conf_files_json = str_replace('"', '&quot;', $conf_files_raw);

                        echo "<tr>
                                            <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' 
                                                " . ATTR_DATA_FILEPATH . $certificatePath . QUOTE_SPACE . "
                                                data-files='" . $conf_files_json . "'></td>
                                            <td>" . htmlspecialchars($row["username"]) . "</td>
                                            <td>" . htmlspecialchars($row["branch"]) . "</td>
                                            <td>" . htmlspecialchars($row["year"]) . "</td>
                                            <td>" . htmlspecialchars($row["paper_title"]) . "</td>
                                            <td>" . htmlspecialchars($row["from_date"]) . "</td>
                                            <td>" . htmlspecialchars($row["to_date"]) . "</td>
                                            <td>" . htmlspecialchars($row["organised_by"]) . "</td>
                                            <td>" . htmlspecialchars($row["location"]) . "</td>
                                            <td>" . htmlspecialchars($row["paper_type"]) . "</td>
                                         </tr>";
                    }

                    echo "</table>
                                        <br>
                                        <div class='bulk-actions'>
                                            <button type='button' class='view-btn' onclick='bulkView()'>View Selected</button>
                                    <button type='button' class='download-btn' onclick='bulkDownload()'>Download Selected</button>&nbsp
                                            <button type='submit' class='delete-btn' name='action' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button>
                                        </div>
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
                                <button type='submit' class='ex_bt' name='export_patent'>Export to Excel</button>
                              </form>";

                $sql_patents = "SELECT * FROM patents_table WHERE username = ? AND status = 'Accepted'";
                $stmt_patents = $conn->prepare($sql_patents);
                $stmt_patents->bind_param("s", $username);
                $stmt_patents->execute();
                $result_patents = $stmt_patents->get_result();

                if ($result_patents->num_rows > 0) {
                    echo "<form method='POST' action='' enctype='multipart/form-data'>
                                    <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                                    <input type='hidden' name='category' value='patents'>
                                    <input type='hidden' name='table_name' value='patents_table'>
                                    <input type='hidden' name='file_column' value='patent_file'>
                                    <table border='1'>
                                        <tr>
                                            <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                                            <th>Username</th>
                                            <th>Branch</th>
                                            <th>Academic Year</th>
                                            <th>Patent Title</th>
                                            <th>Date of Issue</th>
                                            
                                        </tr>";

                    while ($row = $result_patents->fetch_assoc()) {
                        $patentFilePath = fixPath($row["patent_file"]);
                        $pat_files_raw = json_encode(array_values(array_filter([$patentFilePath], fn($f) => strlen($f) > 3)), JSON_UNESCAPED_SLASHES);
                        $pat_files_json = str_replace('"', '&quot;', $pat_files_raw);

                        echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' 
                                            " . ATTR_DATA_FILEPATH . $patentFilePath . QUOTE_SPACE . "
                                            data-files='" . $pat_files_json . "'></td>
                                        <td>" . htmlspecialchars($row["Username"]) . "</td>
                                        <td>" . htmlspecialchars($row["branch"]) . "</td>
                                        <td>" . htmlspecialchars($row["year"]) . "</td>
                                        <td>" . htmlspecialchars($row["patent_title"]) . "</td>
                                        <td>" . htmlspecialchars($row["date_of_issue"]) . "</td>
                                        
                                    </tr>";
                    }

                    echo "</table>
                            <br>
                                    <div class='bulk-actions'>
                                        <button type='button' class='view-btn' onclick='bulkView()'>View Selected</button>
                                        <button type='submit' class='download-btn' name='action' value='download'>Download Selected</button> &nbsp
                                        <button type='submit' class='delete-btn' name='action' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button>
                                    </div>
                                  </form>";
                } else {
                    echo "<p class='no-files'>No patents found.</p>";
                }
                echo "</div>";
                break;

        }
    }
    ?>
</div>

<script>
    function toggleSelectAll(source) {
        const checkboxes = document.querySelectorAll('input[name="selected_files[]"]');
        checkboxes.forEach(cb => cb.checked = source.checked);
    }

    function handleFileTypeChange(selectElement, rowId) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const filePath = selectedOption.getAttribute('data-path');
        const row = selectElement.closest('tr');

        const viewCell = row.querySelector('.view-cell');
        const downloadCell = row.querySelector('.download-cell');
        const checkbox = row.querySelector('input[type="checkbox"]');

        // Update checkbox with current file path
        checkbox.setAttribute('data-filepath', filePath || '');

        // Update View and Download buttons
        if (filePath && filePath !== '') {
            viewCell.innerHTML = `<a href="view_file1.php?file_path=${encodeURIComponent(filePath)}" target="_blank"><button id="view">View</button></a>`;
            downloadCell.innerHTML = `<a href="${filePath}" download><button id="down">Download</button></a>`;
        } else {
            viewCell.innerHTML = '';
            downloadCell.innerHTML = '';
        }
    }

    async function mergeAndAct(cb, action) {
        const filePath = cb.getAttribute('data-filepath');
        const filesJson = cb.getAttribute('data-files');
        const title = cb.getAttribute('data-title') || 'record';

        // If a pre-merged file exists, prioritize it
        if (filePath && filePath !== '') {
            if (action === 'view') {
                window.open('view_file1.php?file_path=' + encodeURIComponent(filePath), '_blank');
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

        // Otherwise, if files are provided for on-the-fly merging
        if (filesJson && filesJson !== '') {
            // Decode HTML entities (our PHP uses &quot; for safe embedding)
            const decodedJson = filesJson.replace(/&quot;/g, '"').replace(/&#x2F;/g, '/').replace(/&amp;/g, '&');
            const files = JSON.parse(decodedJson);
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
                const blob = new Blob([mergedPdfBytes], { type: 'application/pdf' });
                const url = URL.createObjectURL(blob);

                if (action === 'view') {
                    window.open(url, '_blank');
                } else {
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = `FDP_Organized_${title.replace(/[^a-z0-9]/gi, '_')}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
        }
    }

    async function bulkView() {
        const checkboxes = document.querySelectorAll('input[name="selected_files[]"]:checked');
        if (checkboxes.length === 0) {
            alert('Please select at least one file to view.');
            return;
        }

        // Single selection with no data-files → direct view
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
        let checkboxes = document.querySelectorAll('input[name="selected_files[]"]:checked');
        if (checkboxes.length === 0) {
            alert('Please select at least one file to download.');
            return;
        }

        for (const checkbox of checkboxes) {
            await mergeAndAct(checkbox, 'download');
        }
    }

</script>
<script src="https://unpkg.com/pdf-lib/dist/pdf-lib.min.js"></script>
<script>
    async function mergeRecordFiles(filesJson, title) {
        const files = JSON.parse(filesJson);
        if (files.length === 0) {
            alert("No files found for this record.");
            return;
        }

        // In this page, paths seem to be relative to root (from faculty/common perspective usually)
        // But let's check if they need ../ prefix. 
        // In uploadFile they were saved with ../../uploads path (relative to faculty dir).
        // In download_papers1.php (in modules/common), the path is stored as ../../uploads/...

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
        link.download = `FDP_Organized_${title.replace(/[^a-z0-9]/gi, '_')}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
</body>

</html>