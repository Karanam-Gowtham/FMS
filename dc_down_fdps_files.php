<?php
include("connection.php");
session_start();

if (!isset($_SESSION['a_username'])) {
    die("You need to log in to view your uploads.");
}

$username = $_SESSION['a_username'];
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



$action='';
ob_start();

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['selected_files'])) {
    $action = $_POST['action'];
    $selectedFiles = $_POST['selected_files'];
    $category = $_POST['category'];

    // Determine table and file column based on category
    switch($category) {
        case 'fdps':
            $tableName = 'fdps_tab';
            $fileColumn = 'certificate';
            break;
        case 'fdps_org':
            $tableName = 'fdps_org_tab';
            $fileColumn = 'certificate'; // Default, can be changed via select
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
        default:
        $tableName = 'fdps_tab';
        $fileColumn = 'certificate';
    }

    // DELETE ACTION
    if ($action == 'delete') {
        foreach ($selectedFiles as $fileId) {
            $sql = "SELECT $fileColumn FROM $tableName WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();
            
            if ($file && !empty($file[$fileColumn])) {
                if (file_exists($file[$fileColumn])) {
                    unlink($file[$fileColumn]);
                }
                
                $sql = "DELETE FROM $tableName WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $fileId);
                $stmt->execute();
            }
        }
        echo "<script> alert('Records deleted successfully.'); window.location.href = '?dept=" . urlencode($dept) . "&catg=" . urlencode($catg) . "';</script>";
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
            $sql = "SELECT $fileColumn FROM $tableName WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
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
            $zipFileName = $category . "_files_" . time() . ".zip";
            $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;
    
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($selectedFiles as $fileId) {
                    $sql = "SELECT $fileColumn FROM $tableName WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $fileId);
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

if (isset($_POST['export_fdps'])) {
    ob_end_clean();//End previous buffer
   ob_start();// start new buffer for excel
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=fdps_records.xls");

    // Write the Excel content header
    echo "Username\tBranch\tTitle\tDate From\tDate To\tOrganised By\tLocation\n";

    $dept = $_POST['dept'];
    // Prepare and execute the SQL query
    $sql = "SELECT * FROM fdps_tab WHERE branch = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dept);
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
                $row['location'] . "\n";
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

    echo "Username\tBranch\tTitle\tDate From\tDate To\tOrganised By\tLocation\n";

    $sql = "SELECT * FROM fdps_org_tab WHERE branch = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dept);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        echo implode("\t", [
            $row['username'],
            $row['branch'],
            $row['title'],
            $row['date_from'],
            $row['date_to'],
            $row['organised_by'],
            $row['location']
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

    echo "Username\tBranch\tPaper Title\tJournal Name\tIndexing\tDate of Submission\tQuality Factor\tImpact Factor\tPayment\n";

    $sql = "SELECT * FROM published_tab WHERE branch = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dept);
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
            $row['payment']
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

    echo "Username\tBranch\tPaper Title\tFrom Date\tTo Date\tOrganised By\tLocation\tPaper Type\n";

    $sql = "SELECT * FROM conference_tab WHERE branch = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dept);
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
            $row['paper_type']
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

    echo "Username\tBranch\tPatent Title\tDate of Issue\n"; // fixed "Issuen"


    $sql = "SELECT * FROM patents_table WHERE branch = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dept);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        echo implode("\t", [
            $row['Username'],
            $row['branch'],
            $row['patent_title'],
            $row['date_of_issue']
        ]) . "\n";
    }
    ob_end_flush();
    exit;
}
//---------------------------------------------------------------------------------------------------------------------------------

include("header.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Achievements</title>
    <link rel="stylesheet" href="css/download_pap.css">
        <script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <div class="nav-items">
            <a href="index.php" class="home-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </a>
            <span id="sp">&nbsp; >> &nbsp;  </span><span class="sid"><a href="admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
            <span id="sp">&nbsp; >> &nbsp;</span><span class="sid"><a href="dc_acd_year.php?dept=<?php echo "$dept" ?>" class="home-icon">dept_coordinator</a></span>
            <span id="sp">&nbsp; >> &nbsp;</span><span class="main"><a href="#" class="main-a"><?php echo ($catg === 'fdps') ? 'fdps_attended' : "$catg"; ?>_Files</a></span>
            <span id="sp">&nbsp; >> &nbsp;</span>
        </div>
    </div>
</nav>
   
    <div class="div1">
        <div class="filter-section">
        
            <h1><?php echo ($catg === 'fdps') ? 'fdps_attended' : "$catg"; ?> Files</h1>
            <form method="POST" class="filter-form">
            <input type="hidden" name="action_F" value="<?php echo htmlspecialchars($catg); ?>">
                <select name="dept" id="dept">
                    <option value="" disabled selected>Select Department</option>
                    <option value="CSE" <?= isset($_POST['dept']) && $_POST['dept'] == 'CSE' ? 'selected' : '' ?>>CSE</option>
                    <option value="AIML" <?= isset($_POST['dept']) && $_POST['dept'] == 'AIML' ? 'selected' : '' ?>>AIML</option>
                    <option value="AIDS" <?= isset($_POST['dept']) && $_POST['dept'] == 'AIDS' ? 'selected' : '' ?>>AIDS</option>
                    <option value="IT" <?= isset($_POST['dept']) && $_POST['dept'] == 'IT' ? 'selected' : '' ?>>IT</option>
                    <option value="ECE" <?= isset($_POST['dept']) && $_POST['dept'] == 'ECE' ? 'selected' : '' ?>>ECE</option>
                    <option value="EEE" <?= isset($_POST['dept']) && $_POST['dept'] == 'EEE' ? 'selected' : '' ?>>EEE</option>
                    <option value="MECH" <?= isset($_POST['dept']) && $_POST['dept'] == 'MECH' ? 'selected' : '' ?>>MECH</option>
                    <option value="CIVIL" <?= isset($_POST['dept']) && $_POST['dept'] == 'CIVIL' ? 'selected' : '' ?>>CIVIL</option>
                    <option value="BSH" <?= isset($_POST['dept']) && $_POST['dept'] == 'BSH' ? 'selected' : '' ?>>BSH</option>

                </select>
                <button type="submit" name = "sel_btn" class="filter-button">Show Results</button>
            </form>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['sel_btn']) && isset($_POST['dept']) && isset($_POST['action_F'])) {
                $dept = $_POST['dept'];
                $category = $_POST['action_F'];
            

            switch($category) {
                case 'fdps':
                    // Display FDPs Attended
                    echo "<div class='container11'>
                            <h2>FDPs Attended</h2>";
                    echo "<form method='POST' class='ex_b'>
                            <input type='hidden' name='dept' value='" . htmlspecialchars($dept) . "'>
                            <button type='submit' class='ex_bt' name='export_fdps'>Export to Excel</button>
                          </form>";
                
                    $sql_fdps = "SELECT * FROM fdps_tab WHERE branch = ?";
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
                            $certificatePath = htmlspecialchars($row["certificate"]);
                            echo "<td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' 
                                                data-filepath='" . $certificatePath . "' onchange='trackOrder(event)'></td>
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
                    
                    $sql_fdps_org = "SELECT * FROM fdps_org_tab WHERE branch = ?";
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
                                        <th>Select File</th>
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
                
                            $deleteButton = "<form method='GET' action='' onsubmit='return confirm(\"Are you sure you want to delete this record?\")'>
                                            <input type='hidden' name='delete_id' value='" . $row["id"] . "'>
                                            <input type='hidden' name='table' value='fdps_org_tab'>
                                            <input type='hidden' name='file_column' value='certificate'>
                                            <input type='submit' id='del' value='Delete'>
                                          </form>";
                
                            echo "<tr>
                                    <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' 
                                        data-filepath='" . $certificatePath . "'></td>
                                    <td>" . htmlspecialchars($row["username"]) . "</td>
                                    <td>" . htmlspecialchars($row["branch"]) . "</td>
                                    <td>" . htmlspecialchars($row["title"]) . "</td>
                                    <td>" . htmlspecialchars($row["date_from"]) . "</td>
                                    <td>" . htmlspecialchars($row["date_to"]) . "</td>
                                    <td>" . htmlspecialchars($row["organised_by"]) . "</td>
                                    <td>" . htmlspecialchars($row["location"]) . "</td>
                                    <td class='center-cell'>
                                        <select class='file-select' onchange='handleFileTypeChange(this, " . $row["id"] . ")'>
                                            <option value=''>Select File</option>
                                            <option value='certificate' data-path='" . $certificatePath . "'>Certificate</option>
                                            <option value='brochure' data-path='" . $brochurePath . "'>Brochure</option>
                                            <option value='schedule' data-path='" . $schedulePath . "'>Schedule/Invitation</option>
                                            <option value='attendance' data-path='" . $attendancePath . "'>Attendance Forms</option>
                                            <option value='feedback' data-path='" . $feedbackPath . "'>Feedback Forms</option>
                                            <option value='report' data-path='" . $reportPath . "'>Report</option>
                                            <option value='photo1' data-path='" . $photo1Path . "'>Photo 1</option>
                                            <option value='photo2' data-path='" . $photo2Path . "'>Photo 2</option>
                                            <option value='photo3' data-path='" . $photo3Path . "'>Photo 3</option>
                                        </select>
                                    </td>
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
                    
                        $sql_published = "SELECT * FROM published_tab WHERE branch = ?";
                        $stmt_published = $conn->prepare($sql_published);
                        $stmt_published->bind_param("s", $dept);
                        $stmt_published->execute();
                        $result_published = $stmt_published->get_result();
                    
                        if ($result_published->num_rows > 0) {
                            echo "<form method='POST' action='download_papers1.php'>
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
                                $paperFilePath = htmlspecialchars($row["paper_file"]);
                                
                    
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' 
                                        data-filepath='" . $paperFilePath . "'></td>
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
                        
                            $sql_conference = "SELECT * FROM conference_tab WHERE Branch = ?";
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
                                                <th>Choose File</th>
                                            </tr>";
                        
                                while ($row = $result_conference->fetch_assoc()) {
                                    $certificatePath = htmlspecialchars($row["certificate_path"]);
                                    $paperFilePath = htmlspecialchars($row["paper_file_path"]);
                        
                                    echo "<tr>
                                            <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' 
                                                data-filepath='" . $certificatePath . "'></td>
                                            <td>" . htmlspecialchars($row["username"]) . "</td>
                                            <td>" . htmlspecialchars($row["branch"]) . "</td>
                                            <td>" . htmlspecialchars($row["paper_title"]) . "</td>
                                            <td>" . htmlspecialchars($row["from_date"]) . "</td>
                                            <td>" . htmlspecialchars($row["to_date"]) . "</td>
                                            <td>" . htmlspecialchars($row["organised_by"]) . "</td>
                                            <td>" . htmlspecialchars($row["location"]) . "</td>
                                            <td>" . htmlspecialchars($row["paper_type"]) . "</td>
                                            
                                            <td class='center-cell'>
                                                <select class='file-select' onchange='handleFileTypeChange(this, " . $row["id"] . ")'>";
                                    
                                                    if ($row["paper_type"] === "participated") {
                                                        echo "<option value='certificate' data-path='" . $certificatePath . "' selected>Certificate</option>";
                                                    } else {
                                                        echo "<option value='' disabled selected>Choose file</option>";
                                                        echo "<option value='certificate' data-path='" . $certificatePath . "'>Certificate</option>";
                                                        echo "<option value='paper_file' data-path='" . $paperFilePath . "'>Paper File</option>";
                                                    }
                        
                                    echo "      </select>
                                            </td>
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
                    
                        $sql_patents = "SELECT * FROM patents_table WHERE branch = ?";
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
                                $patentFilePath = htmlspecialchars($row["patent_file"]);
                                
                    
                                echo "<tr>
                                            <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' 
                                                data-filepath='" . $patentFilePath . "' onchange='trackOrder(event)'></td>
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
                    
            }
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


function bulkView() {
    const checkboxes = document.querySelectorAll('input[name="selected_files[]"]:checked');
    if (checkboxes.length === 0) {
        alert('Please select at least one file to view.');
        return;
    }
    checkboxes.forEach(cb => {
        const filePath = cb.getAttribute('data-filepath');
        if (filePath) {
            window.open('view_file1.php?file_path=' + encodeURIComponent(filePath), '_blank');
        }
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
            const response = await fetch(fileUrl);
            if (!response.ok) throw new Error(`Failed to fetch ${fileUrl}`);
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
            const btn = document.getElementById("mergedFileButton");
            btn.style.display = "block";
            btn.setAttribute("data-url", data.fileUrl);

            // Optional reset
            selectedOrder = [];
            document.querySelectorAll('input[name="selected_files[]"]').forEach(cb => cb.checked = false);
            updateMergeButton();
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

</body>
</html>
