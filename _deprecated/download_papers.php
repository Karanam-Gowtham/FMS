<?php
include("connection.php");
session_start();

if (!isset($_SESSION['username'])) {
    die("You need to log in to view your uploads.");
}

$username = $_SESSION['username'];
ob_start();

// Handle file deletion logic (keeping your existing delete logic here)
if (isset($_GET['delete_id']) && isset($_GET['table'])) {
    $delete_id = intval($_GET['delete_id']);
    $table = $_GET['table']; // The table name passed in the URL
    $file_column = isset($_GET['file_column']) ? $_GET['file_column'] : ''; // The file column, if applicable

    // SQL query to fetch the file path from the database
    if ($file_column) {
        $sql = "SELECT $file_column FROM $table WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $file = $result->fetch_assoc();

        if ($file && !empty($file[$file_column])) {
            // Attempt to delete the file from the server
            if (file_exists($file[$file_column]))
            {
              if (unlink($file[$file_column])) {
                  // If file deletion is successful, delete the record from the database
                  $delete_sql = "DELETE FROM $table WHERE id = ?";
                  $delete_stmt = $conn->prepare($delete_sql);
                  $delete_stmt->bind_param("i", $delete_id);
                  $delete_stmt->execute();
                  $delete_stmt->close();
              }
            }
            else{
                // If file does not exist, just delete the record
                    $delete_sql = "DELETE FROM $table WHERE id = ?";
                    $delete_stmt = $conn->prepare($delete_sql);
                    $delete_stmt->bind_param("i", $delete_id);
                    $delete_stmt->execute();
                    $delete_stmt->close();
            }
        } else {
            // If the file does not exist, just delete the record
            $delete_sql = "DELETE FROM $table WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $delete_id);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
    } else {
        // If no file column is provided, just delete the record
        $delete_sql = "DELETE FROM $table WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $delete_id);
        $delete_stmt->execute();
        $delete_stmt->close();
    }
   header("Location: ".$_SERVER['PHP_SELF']);
    exit();
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
    $sql = "SELECT * FROM fdps_tab WHERE username = ?";
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

    echo "Username\tBranch\tTitle\tDate From\tDate To\tOrganised By\tLocation\tSubmission Time\n";

    $sql = "SELECT * FROM fdps_org_tab WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
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

    $sql = "SELECT * FROM published_tab WHERE username = ?";
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

    $sql = "SELECT * FROM conference_tab WHERE username = ?";
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

    $sql = "SELECT * FROM patents_table WHERE username = ?";
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


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Uploads</title>
    <link rel="stylesheet" href="css/download_pap.css">
</head>
<?php include "header.php"; ?>
<body>
    
   
    <div class="div1">
        

        <!-- Add filter section -->
        <div class="filter-section">
        <h1>My Achievements</h1>
            <form method="POST" class="filter-form">
                <select name="category" id="category">
                    <option value="">Select Category</option>
                    <option value="fdps" <?php echo isset($_POST['category']) && $_POST['category'] == 'fdps' ? 'selected' : ''; ?>>FDPs Attended</option>
                    <option value="fdps_org" <?php echo isset($_POST['category']) && $_POST['category'] == 'fdps_org' ? 'selected' : ''; ?>>FDPs Organised</option>
                    <option value="published" <?php echo isset($_POST['category']) && $_POST['category'] == 'published' ? 'selected' : ''; ?>>Published Papers</option>
                    <option value="conference" <?php echo isset($_POST['category']) && $_POST['category'] == 'conference' ? 'selected' : ''; ?>>Conference Papers</option>
                    <option value="patents" <?php echo isset($_POST['category']) && $_POST['category'] == 'patents' ? 'selected' : ''; ?>>Patents</option>
                </select>
                <button type="submit" class="filter-button">Show Results</button>
            </form>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category'])) {
            $category = $_POST['category'];

            switch($category) {
                case 'fdps':
                    // Display FDPs Attended
                    echo "<div class='container11'>
                            <h2>FDPs Attended</h2>";
                    echo "<form method='POST' class='ex_b'>
                            <button type='submit' class='ex_bt' name='export_fdps'>Export to Excel</button>
                          </form>";
                
                    $sql_fdps = "SELECT * FROM fdps_tab WHERE username = ?";
                    $stmt_fdps = $conn->prepare($sql_fdps);
                    $stmt_fdps->bind_param("s", $username);
                    $stmt_fdps->execute();
                    $result_fdps = $stmt_fdps->get_result();
                
                    if ($result_fdps->num_rows > 0) {
                        echo "<table border='1'>
                                <tr>
                                    <th>Username</th>
                                    <th>Branch</th>
                                    <th>Title</th>
                                    <th>Date From</th>
                                    <th>Date To</th>
                                    <th>Organised By</th>
                                    <th>Location</th>
                                    <th>Certificate</th>
                                    <th>Submission Time</th>
                                    <th>Action</th>
                                </tr>";
                
                        while ($row = $result_fdps->fetch_assoc()) {
                            $certificatePath = htmlspecialchars($row["certificate"]);
                            $deleteButton = "<form method='GET' action='' onsubmit='return confirm(\"Are you sure you want to delete this record?\")'>
                                                <input type='hidden' name='delete_id' value='" . $row["id"] . "'>
                                                <input type='hidden' name='table' value='fdps_tab'>
                                                <input type='hidden' name='file_column' value='certificate'>
                                                <input type='submit' id='del' value='Delete'>
                                            </form>";
                
                            echo "<tr>
                                    <td>" . $row["username"] . "</td>
                                    <td>" . $row["branch"] . "</td>
                                    <td>" . $row["title"] . "</td>
                                    <td>" . $row["date_from"] . "</td>
                                    <td>" . $row["date_to"] . "</td>
                                    <td>" . $row["organised_by"] . "</td>
                                    <td>" . $row["location"] . "</td>
                                    <td>
                                        <a href='view_file1.php?file_path=" . urlencode($certificatePath) . "' target='_blank'>
                                            <button id='view'>View</button>
                                        </a>
                                        <a href='" . htmlspecialchars($certificatePath) . "' download>
                                            <button id='down'>Download</button>
                                        </a>
                                    </td>
                                    <td>" . $row["submission_time"] . "</td>
                                    <td>$deleteButton</td>
                                </tr>";
                        }
                        echo "</table></div>";
                    } else {
                        echo "<p class='no-files'>No FDPs attended found.</p>";
                    }
                    break;
                

                    case 'fdps_org':
                        // Display FDPs Organised
                        echo "<div class='container11'>
                                <h2>FDPs Organised</h2>";
                        echo "<form method='POST'>
                                <button type='submit' class='ex_bt' name='export_fdps_org'>Export to Excel</button>
                              </form>";
                        
                        $sql_fdps_org = "SELECT * FROM fdps_org_tab WHERE username = ?";
                        $stmt_fdps_org = $conn->prepare($sql_fdps_org);
                        $stmt_fdps_org->bind_param("s", $username);
                        $stmt_fdps_org->execute();
                        $result_fdps_org = $stmt_fdps_org->get_result();
                    
                        if ($result_fdps_org->num_rows > 0) {
                            echo "<table border='1'>
                                    <tr>
                                        <th>Username</th>
                                        <th>Branch</th>
                                        <th>Title</th>
                                        <th>Date From</th>
                                        <th>Date To</th>
                                        <th>Organised By</th>
                                        <th>Location</th>
                                        <th>Submission Time</th>
                                        <th>Select File</th>
                                        <th>View</th>
                                        <th>Download</th>
                                        <th>Action</th>
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
                                        <td>" . $row["username"] . "</td>
                                        <td>" . $row["branch"] . "</td>
                                        <td>" . $row["title"] . "</td>
                                        <td>" . $row["date_from"] . "</td>
                                        <td>" . $row["date_to"] . "</td>
                                        <td>" . $row["organised_by"] . "</td>
                                        <td>" . $row["location"] . "</td>
                                        <td>" . $row["submission_time"] . "</td>
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
                                        <td class='center-cell view-cell'></td>
                                        <td class='center-cell download-cell'></td>
                                        <td class='center-cell'>$deleteButton</td>
                                    </tr>";
                            }
                            echo "</table>";
                        } else {
                            echo "<p class='no-files'>No FDPs organised found.</p>";
                        }
                        echo "</div>";
                        break;

                        case 'fdps_org':
                            // Display FDPs Organised
                            echo "<div class='container11'>
                                    <h2>FDPs Organised</h2>";
                            echo "<form method='POST'>
                                    <button type='submit' class='ex_bt' name='export_fdps_org'>Export to Excel</button>
                                  </form>";
                        
                            $sql_fdps_org = "SELECT * FROM fdps_org_tab WHERE username = ?";
                            $stmt_fdps_org = $conn->prepare($sql_fdps_org);
                            $stmt_fdps_org->bind_param("s", $username);
                            $stmt_fdps_org->execute();
                            $result_fdps_org = $stmt_fdps_org->get_result();
                        
                            if ($result_fdps_o
                            rg->num_rows > 0) {
                                echo "<table border='1'>
                                        <tr>
                                            <th>Username</th>
                                            <th>Branch</th>
                                            <th>Title</th>
                                            <th>Date From</th>
                                            <th>Date To</th>
                                            <th>Organised By</th>
                                            <th>Location</th>
                                            <th>Submission Time</th>
                                            <th>Select File</th>
                                            <th>View</th>
                                            <th>Download</th>
                                            <th>Action</th>
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
                                            <td>" . $row["username"] . "</td>
                                            <td>" . $row["branch"] . "</td>
                                            <td>" . $row["title"] . "</td>
                                            <td>" . $row["date_from"] . "</td>
                                            <td>" . $row["date_to"] . "</td>
                                            <td>" . $row["organised_by"] . "</td>
                                            <td>" . $row["location"] . "</td>
                                            <td>" . $row["submission_time"] . "</td>
                                            <td class='center-cell'>
                                                <select class='file-select' onchange='updateFileLinks(this, " . $row["id"] . ")'>
                                                    <option value=''>Select File</option>
                                                    <option value='" . $certificatePath . "'>Certificate</option>
                                                    <option value='" . $brochurePath . "'>Brochure</option>
                                                    <option value='" . $schedulePath . "'>Schedule/Invitation</option>
                                                    <option value='" . $attendancePath . "'>Attendance Forms</option>
                                                    <option value='" . $feedbackPath . "'>Feedback Forms</option>
                                                    <option value='" . $reportPath . "'>Report</option>
                                                    <option value='" . $photo1Path . "'>Photo 1</option>
                                                    <option value='" . $photo2Path . "'>Photo 2</option>
                                                    <option value='" . $photo3Path . "'>Photo 3</option>
                                                </select>
                                            </td>
                                            <td class='center-cell' id='view-" . $row["id"] . "'>
                                                <a href='#' target='_blank' class='btn view-btn'>View</a>
                                            </td>
                                            <td class='center-cell' id='download-" . $row["id"] . "'>
                                                <a href='#' download class='btn download-btn'>Download</a>
                                            </td>
                                            <td class='center-cell'>$deleteButton</td>
                                        </tr>";
                                }
                                echo "</table>";
                            } else {
                                echo "<p class='no-files'>No FDPs organised found.</p>";
                            }
                            echo "</div>";
                            break;
                        
                            case 'published':
                                // Display Published Papers
                                echo "
                                <div class='container11'>
                                <h2>Published Papers</h2>";
                                echo "<form method='POST'>
                                        <button type='submit' class='ex_bt' name='export_published'>Export to Excel</button>
                                      </form>";
                            
                                $sql_published = "SELECT * FROM published_tab WHERE username = ?";
                                $stmt_published = $conn->prepare($sql_published);
                                $stmt_published->bind_param("s", $username);
                                $stmt_published->execute();
                                $result_published = $stmt_published->get_result();
                            
                                if ($result_published->num_rows > 0) {
                                    // Published Papers table display
                                    echo "
                                            <table border='1'>
                                                <tr>
                                                    <th>Username</th>
                                                    <th>Branch</th>
                                                    <th>Paper Title</th>
                                                    <th>Journal Name</th>
                                                    <th>Indexing</th>
                                                    <th>Date of Submission</th>
                                                    <th>Quality Factor</th>
                                                    <th>Impact Factor</th>
                                                    <th>Payment</th>
                                                    <th>Submission Time</th>
                                                    <th>Paper</th>
                                                    <th>Action</th>
                                                </tr>";
                            
                                    while ($row = $result_published->fetch_assoc()) {
                                        $paperFilePath = htmlspecialchars($row["paper_file"]);
                                        $deleteButton = "<form method='GET' action='' onsubmit='return confirm(\"Are you sure you want to delete this record?\")'>
                                                            <input type='hidden' name='delete_id' value='" . $row["id"] . "'>
                                                            <input type='hidden' name='table' value='published_tab'>
                                                            <input type='hidden' name='file_column' value='paper_file'>
                                                            <input type='submit' id='del' value='Delete'>
                                                        </form>";
                            
                                        $paperFileButtons = !empty($paperFilePath) ? "<div class='a_div'>
                                            <a href='" . htmlspecialchars($paperFilePath) . "' target='_blank' class='btn view-btn'>View</a><br>
                                            <a href='" . htmlspecialchars($paperFilePath) . "' download class='btn download-btn'>Download</a></div>"
                                            : "No paper available";
                            
                                        echo "<tr>
                                                <td>" . $row["username"] . "</td>
                                                <td>" . $row["branch"] . "</td>
                                                <td>" . $row["paper_title"] . "</td>
                                                <td>" . $row["journal_name"] . "</td>
                                                <td>" . $row["indexing"] . "</td>
                                                <td>" . $row["date_of_submission"] . "</td>
                                                <td>" . $row["quality_factor"] . "</td>
                                                <td>" . $row["impact_factor"] . "</td>
                                                <td>" . $row["payment"] . "</td>
                                                <td>" . $row["submission_time"] . "</td>
                                                <td>$paperFileButtons</td>
                                                <td>$deleteButton</td>
                                            </tr>";
                                    }
                                    echo "</table></div>";
                                } else {
                                    echo "<p class='no-files'>No published papers found.</p>";
                                }
                                break;
                                

                    case 'conference':
                        // Display Conference Papers
                        echo "<div class='container11'>
                        <h2>Conference Papers</h2>";
                        echo "<form method='POST'>
                                <button type='submit' class='ex_bt' name='export_conference'>Export to Excel</button>
                              </form>";
                    
                        $sql_conference = "SELECT * FROM conference_tab WHERE username = ?";
                        $stmt_conference = $conn->prepare($sql_conference);
                        $stmt_conference->bind_param("s", $username);
                        $stmt_conference->execute();
                        $result_conference = $stmt_conference->get_result();
                    
                        if ($result_conference->num_rows > 0) {
                            // Conference Papers table display
                            echo "
                                    <table border='1'>
                                        <tr>
                                            <th>Username</th>
                                            <th>Branch</th>
                                            <th>Paper Title</th>
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Organised By</th>
                                            <th>Location</th>
                                            <th>Certificate</th>
                                            <th>Paper Type</th>
                                            <th>Paper File</th>
                                            <th>Submission Time</th>
                                            <th>Action</th>
                                        </tr>";
                    
                            while ($row = $result_conference->fetch_assoc()) {
                                $certificatePath = htmlspecialchars($row["certificate_path"]);
                                $paperFilePath = htmlspecialchars($row["paper_file_path"]);
                                $deleteButton = "<form method='GET' action='' onsubmit='return confirm(\"Are you sure you want to delete this record?\")'>
                                                    <input type='hidden' name='delete_id' value='" . $row["id"] . "'>
                                                    <input type='hidden' name='table' value='conference_tab'>
                                                    <input type='hidden' name='file_column' value='certificate_path'>
                                                    <input type='submit' id='del' value='Delete'>
                                                </form>";
                    
                                if ($row["paper_type"] == "paper publication") {
                                    $paperFileButtons = "
                                        <a href='" . htmlspecialchars($paperFilePath) . "' target='_blank' class='btn view-btn'>View</a>
                                        <a href='" . htmlspecialchars($paperFilePath) . "' download class='btn download-btn'>Download</a>";
                                } else {
                                    $paperFileButtons = "No papers";
                                }
                    
                                $certificateButtons = !empty($certificatePath) ? "<div class='a_div'>
                                    <a href='" . htmlspecialchars($certificatePath) . "' target='_blank' class='btn view-btn'>View</a>
                                    <a href='" . htmlspecialchars($certificatePath) . "' download class='btn download-btn'>Download</a></div>"
                                    : "No certificate available";
                    
                                echo "<tr>
                                        <td>" . $row["username"] . "</td>
                                        <td>" . $row["branch"] . "</td>
                                        <td>" . $row["paper_title"] . "</td>
                                        <td>" . $row["from_date"] . "</td>
                                        <td>" . $row["to_date"] . "</td>
                                        <td>" . $row["organised_by"] . "</td>
                                        <td>" . $row["location"] . "</td>
                                        <td>$certificateButtons</td>
                                        <td>" . $row["paper_type"] . "</td>
                                        <td>$paperFileButtons</td>
                                        <td>" . $row["submission_time"] . "</td>
                                        <td>$deleteButton</td>
                                    </tr>";
                            }
                            echo "</table></div>";
                        } else {
                            echo "<p class='no-files'>No conference papers found.</p>";
                        }
                        break;
                    

                case 'patents':
                    // Display Patents
                    echo "<div class='container11'>
                    <h2>Patents</h2>";
                    echo "<form method='POST'>
                            <button type='submit' class='ex_bt' name='export_patent'>Export to Excel</button>
                          </form>";
                    
                    $sql_patents = "SELECT * FROM patents_table WHERE username = ?";
                    $stmt_patents = $conn->prepare($sql_patents);
                    $stmt_patents->bind_param("s", $username);
                    $stmt_patents->execute();
                    $result_patents = $stmt_patents->get_result();

                    if ($result_patents->num_rows > 0) {
                        // Your existing Patents table display code
                        echo "
                            <table border='1'>
                                <tr>
                                    <th>Username</th>
                                    <th>Branch</th>
                                    <th>Patent Title</th>
                                    <th>Date of Issue</th>
                                    <th>Patent File</th>
                                    <th>Submission Time</th>
                                    <th>Action</th>
                                </tr>";
                    
                        while ($row = $result_patents->fetch_assoc()) {
                            $patentFilePath = htmlspecialchars($row["patent_file"]);
                            $deleteButton = "<form method='GET' action='' onsubmit='return confirm(\"Are you sure you want to delete this record?\")'>
                                                <input type='hidden' name='delete_id' value='" . $row["id"] . "'>
                                                <input type='hidden' name='table' value='patents_table'>
                                                <input type='hidden' name='file_column' value='patent_file'>
                                                <input type='submit' id='del' value='Delete'>
                                             </form>";
                    
                            if (!empty($patentFilePath)) {
                                $patentFileButtons = "<div class='a_div'>
                                    <a href='" . htmlspecialchars($patentFilePath) . "' target='_blank' class='btn view-btn'>View</a><br>
                                    <a href='" . htmlspecialchars($patentFilePath) . "' download class='btn download-btn'>Download</a></div>";
                            } else {
                                $patentFileButtons = "No patent file";
                            }
                    
                            echo "<tr>
                                    <td>" . $row["Username"] . "</td>
                                    <td>" . $row["branch"] . "</td>
                                    <td>" . $row["patent_title"] . "</td>
                                    <td>" . $row["date_of_issue"] . "</td>
                                    <td>$patentFileButtons</td>
                                    <td>" . $row["submission_time"] . "</td>
                                    <td>$deleteButton</td>
                                </tr>";
                        }
                        echo "</table></div>";
                    }
                    
                     else {
                        echo "<p class='no-files'>No patents found.</p>";
                    }
                    break;
            }
        }
        ?>
    </div>
</body>
<script>

        // Function to handle file type selection
        function handleFileTypeChange(selectElement, rowId) {
            const fileType = selectElement.value;
            const row = selectElement.closest('tr');
            
            // Get the view, download buttons cells
            const viewCell = row.querySelector('.view-cell');
            const downloadCell = row.querySelector('.download-cell');
            
            if (!fileType) {
                viewCell.innerHTML = '';
                downloadCell.innerHTML = '';
                return;
            }
            
            // Get the file path from the data attribute
            const filePath = selectElement.options[selectElement.selectedIndex].getAttribute('data-path');
            
            if (filePath) {
                // Update view button
                viewCell.innerHTML = `<a href="view_file1.php?file_path=${encodeURIComponent(filePath)}"><button id="view">View</button></a>`;
                
                // Update download button
                downloadCell.innerHTML = `<a href="${filePath}" download><button id="down">Download</button></a>`;
            } else {
                viewCell.innerHTML = 'No file available';
                downloadCell.innerHTML = 'No file available';
            }
        }

        function updateFileLinks(selectElement, rowId) {
            var selectedValue = selectElement.value;
            var viewLink = document.querySelector('#view-' + rowId + ' a');
            var downloadLink = document.querySelector('#download-' + rowId + ' a');
            
            if (selectedValue) {
                viewLink.href = selectedValue;
                downloadLink.href = selectedValue;
            } else {
                viewLink.href = '#';
                downloadLink.href = '#';
            }
        }
        

    </script>
</html>