<?php
include("../../includes/connection.php");

if (isset($_GET['dept'])) {
    $dept = $_GET['dept'];
} else {
    echo "Department not set.";
}

if (isset($_GET['designation'])) {
    $desg = $_GET['designation'];
} else {
    echo "Designation not set.";
}

$selected_file_type = $_POST['file_type1'] ?? '';

$main_select = $_POST['main_select'] ?? '';
$bodies_sub_select = $_POST['bodies_sub_select'] ?? '';
$branch_select = $_POST['branch_select'] ?? '';
// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['selected_files'])) {
    $main_select = $_POST['main_select'] ?? '';
    $branch_select = $_POST['branch_select'] ?? '';
    $selectedFiles = $_POST['selected_files'];
    $action = $_POST['action'];

    // Determine table and file column based on category
    switch ($main_select) {
        case 'Journals':
            $tableName = 's_journal_tab';
            $fileColumn = 'paper_file';
            break;
        case 'Conferences':
            $tableName = 's_conference_tab';
            $fileColumn = 'certificate_path'; // Default, can be changed via select
            break;
        case 'Professional Bodies':
            $tableName = 's_bodies';
            $fileColumn = 'certificate_path';
            break;
        case 'Projects':
        case 'Internships':
        case 'SIH':
            $tableName = 's_events';
            $fileColumn = 'certificate_path';
            break;
        default:
            $tableName = 's_journal_tab';
            $fileColumn = 'paper_file';
    }

    // DELETE ACTION
    if ($action === 'delete') {
        foreach ($selectedFiles as $fileId) {
            $sql = "SELECT $fileColumn FROM $tableName WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();

            if ($file && file_exists($file[$fileColumn])) {
                unlink($file[$fileColumn]);
            }

            $sql = "DELETE FROM $tableName WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
        }

        echo "<script>alert('Records deleted successfully.'); window.location.href = window.location.href;</script>";
        exit;
    } elseif ($action === 'download') {
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

            if ($file && file_exists($file[$fileColumn])) {
                $filePath = $file[$fileColumn];
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                header('Content-Length: ' . filesize($filePath));
                ob_clean();
                flush();
                readfile($filePath);
                exit;
            } else {
                echo "<script>alert('File not found: ' + " . json_encode($file[$fileColumn] ?? '') . ");</script>";
                exit;
            }
        } else {
            $zip = new ZipArchive();
            $safe_main_select = preg_replace('/[^a-zA-Z0-9_.-]/', '', (string)$main_select);
            $zipFileName = $safe_main_select . time() . ".zip";
            $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                $fileCounter = 1;  // Counter to avoid overwriting files with the same name

                foreach ($selectedFiles as $fileId) {
                    $sql = "SELECT $fileColumn FROM $tableName WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $fileId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $file = $result->fetch_assoc();

                    if ($file && file_exists($file[$fileColumn])) {
                        // Get the base name of the file (e.g., 'report.pdf')
                        $fileName = basename($file[$fileColumn]);

                        // If the file already exists in the zip, append a unique identifier (fileCounter)
                        $newFileName = $fileName;

                        // Ensure unique filename by appending a counter if file already exists
                        while ($zip->locateName($newFileName) !== false) {
                            $newFileName = pathinfo($fileName, PATHINFO_FILENAME) . "_$fileCounter." . pathinfo($fileName, PATHINFO_EXTENSION);
                            $fileCounter++;
                        }

                        // Add file to the ZIP with the new unique name
                        $zip->addFile($file[$fileColumn], $newFileName);
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
                unlink($zipFilePath); // Delete temp zip
                exit;
            } else {
                echo "<script>alert('Failed to create ZIP.');</script>";
                exit;
            }
        }
    }
}

///------------------------------------------------------------------------------------------------------------
// SQL query to fetch all records from the fdps_tab table

if (isset($_POST['export_sjournal'])) {
    ob_end_clean();//End previous buffer
    ob_start();// start new buffer for excel
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=journal.xls");

    // Write the Excel content header
    echo "Username\tBranch\tAcademic_Year\tPaper Title\tJournal Name\tIndexing\tDate of Submission\tQuality Factor\tImpact Factor\tPayment\n";
    $branch_select = $_POST['branch_select'] ?? '';
    // Prepare and execute the SQL query
    $sql_sjournal = "SELECT * FROM s_journal_tab WHERE branch = ?";
    $stmt_sjournal = $conn->prepare($sql_sjournal);
    $stmt_sjournal->bind_param("s", $branch_select);
    $stmt_sjournal->execute();
    $result_sjournal = $stmt_sjournal->get_result();

    // Fetch and write data rows
    if ($result_sjournal->num_rows > 0) {
        while ($row = $result_sjournal->fetch_assoc()) {
            echo $row['Username'] . "\t" .
                $row['branch'] . "\t" .
                $row['acd_year'] . "\t" .
                $row['paper_title'] . "\t" .
                $row['journal_name'] . "\t" .
                $row['indexing'] . "\t" .
                $row['date_of_submission'] . "\t" .
                $row['quality_factor'] . "\t" .
                $row['impact_factor'] . "\t" .
                $row['payment'] . "\n";
        }
    }

    // End script execution
    ob_end_flush(); // End current buffer
    exit;
}

if (isset($_POST['export_sconference'])) {
    ob_end_clean();
    ob_start();
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=conferences.xls");

    echo "Username\tBranch\tAcademic_Year\tPaper Title\tFrom Date\tTo Date\tOrganised By\tLocation\tPaper Type\n";
    $branch_select = $_POST['branch_select'] ?? '';

    $sql_sconference = "SELECT * FROM s_conference_tab WHERE branch = ?";
    $stmt_sconference = $conn->prepare($sql_sconference);
    $stmt_sconference->bind_param("s", $branch_select);
    $stmt_sconference->execute();
    $result_sconference = $stmt_sconference->get_result();

    while ($row = $result_sconference->fetch_assoc()) {
        echo implode("\t", [
            $row['Username'],
            $row['branch'],
            $row['acd_year'],
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

// Handle export for Published Papers
if (isset($_POST['export_sbodies'])) {
    $bodies_sub_select = $_POST['bodies_sub_select'];
    ob_end_clean();
    ob_start();
    header("Content-Type: application/vnd.ms-excel");
    $safe_filename = preg_replace('/[^a-zA-Z0-9_.-]/', '', (string)$bodies_sub_select);
    header("Content-Disposition: attachment; filename=$safe_filename.xls");

    echo "Username\tBranch\tAcademic_Year\tBody\tEvent Name\tFrom Date\tTo Date\tOrganised By\tLocation\tParticipation Status\n";
    $branch_select = $_POST['branch_select'] ?? '';

    $sql_sbodies = "SELECT * FROM s_bodies WHERE Body = ? and branch = ?";
    $stmt_sbodies = $conn->prepare($sql_sbodies);
    $stmt_sbodies->bind_param("ss", $bodies_sub_select, $branch_select);
    $stmt_sbodies->execute();
    $result_sbodies = $stmt_sbodies->get_result();

    while ($row = $result_sbodies->fetch_assoc()) {
        echo implode("\t", [
            $row['Username'],
            $row['branch'],
            $row['acd_year'],
            $row['Body'],
            $row['event_name'],
            $row['from_date'],
            $row['to_date'],
            $row['organised_by'],
            $row['location'],
            $row['participation_status']
        ]) . "\n";
    }

    ob_end_flush();
    exit;

}

// Handle export for Conference Papers
if (isset($_POST['export_sevents'])) {
    $main_select = $_POST['main_select'] ?? '';
    ob_end_clean();
    ob_start();
    header("Content-Type: application/vnd.ms-excel");
    $safe_filename = preg_replace('/[^a-zA-Z0-9_.-]/', '', (string)$main_select);
    header("Content-Disposition: attachment; filename=$safe_filename.xls");

    echo "Username\tBranch\tAcademic_Year\tActivity\tEvent Name\tFrom Date\tTo Date\tOrganised By\tLocation\tParticipation Status\n";
    $branch_select = $_POST['branch_select'] ?? '';


    $sql_sevents = "SELECT * FROM s_events WHERE branch = ? and activity = ?";
    $stmt_sevents = $conn->prepare($sql_sevents);
    $stmt_sevents->bind_param("ss", $branch_select, $main_select);
    $stmt_sevents->execute();
    $result_sevents = $stmt_sevents->get_result();

    while ($row = $result_sevents->fetch_assoc()) {
        echo implode("\t", [
            $row['Username'],
            $row['branch'],
            $row['acd_year'],
            $row['activity'],
            $row['event_name'],
            $row['from_date'],
            $row['to_date'],
            $row['organised_by'],
            $row['location'],
            $row['participation_status']
        ]) . "\n";
    }

    ob_end_flush();
    exit;

}

//---------------------------------------------------------------------------------------------------------------------------------

$extra_head = '<link rel="stylesheet" href="../../assets/css/s_down_files1.css"><script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>';
include("../../includes/header.php");

?>

<script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mainSelect = document.getElementById('main-select');
        const bodiesSubSelectDiv = document.getElementById('bodies-sub-select-div');

        function toggleSubSelect() {
            if (mainSelect.value === 'Professional Bodies') {
                bodiesSubSelectDiv.style.display = 'block';
                bodiesSubSelectDiv.style.marginTop = '20px';
            } else {
                bodiesSubSelectDiv.style.display = 'none';
            }
        }

        // Initial check on page load
        toggleSubSelect();

        // Listen for changes
        mainSelect.addEventListener('change', toggleSubSelect);
    });
</script>

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
                <span id="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                        href="cc_acd_year.php?dept=<?php echo urlencode((string)$dept); ?>"
                        class="home-icon"><?php echo htmlspecialchars($desg); ?></a></span>
                <span id="sp">&nbsp; >> &nbsp;</span><span class="main"><a href="#"
                        class="main-a">student_activity_Files</a></span>
                <span id="sp">&nbsp; >> &nbsp;</span>
            </div>
        </div>
    </nav>

    <div class="container11">
        <h1>Retrieve Student Activity files</h1>
        <div class="filter-section">
            <div class="form">
                <form method="POST" action="">
                    <div class="main_select_div">
                        <label for="main-select" id="l1">Select Category:</label>
                        <select id="main-select" name="main_select">
                            <option value="" disabled selected>Choose an option</option>
                            <option value="Journals" <?= $main_select == 'Journals' ? 'selected' : '' ?>>Journals</option>
                            <option value="Conferences" <?= $main_select == 'Conferences' ? 'selected' : '' ?>>Conferences
                            </option>
                            <option value="Projects" <?= $main_select == 'Projects' ? 'selected' : '' ?>>Projects</option>
                            <option value="Internships" <?= $main_select == 'Internships' ? 'selected' : '' ?>>Internships
                            </option>
                            <option value="SIH" <?= $main_select == 'SIH' ? 'selected' : '' ?>>SIH</option>
                            <option value="Professional Bodies" <?= $main_select == 'Professional Bodies' ? 'selected' : '' ?>>Professional Bodies</option>
                        </select>
                    </div>
                    <div id="bodies-sub-select-div"
                        style="display: <?= $main_select == 'Professional Bodies' ? 'block' : 'none' ?>;">
                        <label for="bodies-sub-select">Select Subcategory:</label>
                        <select id="bodies-sub-select" name="bodies_sub_select">
                            <option value="" disabled selected>Choose an option</option>
                            <option value="ISTE" <?= $bodies_sub_select == 'ISTE' ? 'selected' : '' ?>>ISTE</option>
                            <option value="CSI" <?= $bodies_sub_select == 'CSI' ? 'selected' : '' ?>>CSI</option>
                            <option value="ACM" <?= $bodies_sub_select == 'ACM' ? 'selected' : '' ?>>ACM</option>
                            <option value="ACMW" <?= $bodies_sub_select == 'ACMW' ? 'selected' : '' ?>>ACMW</option>
                            <option value="Coding Club" <?= $bodies_sub_select == 'Coding Club' ? 'selected' : '' ?>>Coding
                                Club</option>
                        </select>
                    </div>
                    <br>
                    <div>
                        <label for="branch-select">Select Branch:</label>
                        <select id="branch-select" name="branch_select">
                            <option value="" disabled selected>Choose an option</option>
                            <option value="CSE" <?= $branch_select == 'CSE' ? 'selected' : '' ?>>CSE</option>
                            <option value="AIML" <?= $branch_select == 'AIML' ? 'selected' : '' ?>>AIML</option>
                            <option value="AIDS" <?= $branch_select == 'AIDS' ? 'selected' : '' ?>>AIDS</option>
                            <option value="IT" <?= $branch_select == 'IT' ? 'selected' : '' ?>>IT</option>
                            <option value="ECE" <?= $branch_select == 'ECE' ? 'selected' : '' ?>>ECE</option>
                            <option value="EEE" <?= $branch_select == 'EEE' ? 'selected' : '' ?>>EEE</option>
                            <option value="MECH" <?= $branch_select == 'MECH' ? 'selected' : '' ?>>MECH</option>
                            <option value="CIVIL" <?= $branch_select == 'CIVIL' ? 'selected' : '' ?>>CIVIL</option>
                            <option value="BSH" <?= $branch_select == 'BSH' ? 'selected' : '' ?>>BSH</option>
                        </select>
                    </div><br>
                    <div class="btn-div">
                        <button type="submit" class="btn11 btn" name="submit_button">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container11">
        <div class="container111">

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $main_select = $_POST['main_select'] ?? '';
                $bodies_sub_select = $_POST['bodies_sub_select'] ?? '';
                $branch_select = $_POST['branch_select'] ?? '';

                switch ($main_select) {
                    case 'Journals':
                        echo "<div class='container11'>
                        <h2>Student Journals</h2>";

                        echo "<form method='POST' class='ex_b'>
                        <input type='hidden' name='branch_select' value='" . htmlspecialchars($branch_select) . "'>
                        <button type='submit' class='ex_bt' name='export_sjournal'>Export to Excel</button>
                      </form>";

                        $sql_sjournal = "SELECT * FROM s_journal_tab WHERE branch = ?";
                        $stmt_sjournal = $conn->prepare($sql_sjournal);
                        $stmt_sjournal->bind_param("s", $branch_select);
                        $stmt_sjournal->execute();
                        $result_sjournal = $stmt_sjournal->get_result();

                        if ($result_sjournal->num_rows > 0) {
                            echo "<form method='POST' action=''>
                            <input type='hidden' name='main_select' value='" . htmlspecialchars($main_select) . "'>
                            <input type='hidden' name='branch_select' value='" . htmlspecialchars($branch_select) . "'>
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

                            while ($row = $result_sjournal->fetch_assoc()) {
                                $paperPath = htmlspecialchars($row["paper_file"]);
                                echo "<tr>
                                <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' data-filepath='" . $paperPath . "'  onchange='trackOrder(event)'></td>
                                <td>" . htmlspecialchars($row["Username"]) . "</td>
                                <td>" . htmlspecialchars($row["branch"]) . "</td>
                                <td>" . htmlspecialchars($row["acd_year"]) . "</td>
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
                                        <button type='button' id='mergeBtn' class='merge' onclick='mergePDFs()' disabled>Merge PDFs</button>&nbsp
                                        <button type='submit' class='delete-btn' name='action' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button><br>
                                        <button type='button' class='merge' id='mergedFileButton' onclick='viewMergedFile()' style='display:none;'>View Merged File</button>
                              </div>
                        </form>";
                        } else {
                            echo "<p class='no-files'>No journal entries found.</p>";
                        }

                        echo "</div>";
                        break;



                    case 'Conferences':
                        echo "<div class='container11'>
                            <h2>Student Conferences</h2>";
                        echo "<form method='POST' class='ex_b'>
                            <input type='hidden' name='branch_select' value='" . htmlspecialchars($branch_select) . "'>
                            <button type='submit' class='ex_bt' name='export_sconference'>Export to Excel</button>
                          </form>";

                        $sql_sconference = "SELECT * FROM s_conference_tab WHERE branch = ?";
                        $stmt_sconference = $conn->prepare($sql_sconference);
                        $stmt_sconference->bind_param("s", $branch_select);
                        $stmt_sconference->execute();
                        $result_sconference = $stmt_sconference->get_result();

                        if ($result_sconference->num_rows > 0) {
                            echo "<form method='POST' action=''>
                               <input type='hidden' name='main_select' value='" . htmlspecialchars($main_select) . "'>
                                <input type='hidden' name='branch_select' value='" . htmlspecialchars($branch_select) . "'>
                
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
                                        <th>Choose file<th>
                                    </tr>";

                            while ($row = $result_sconference->fetch_assoc()) {
                                $certificatepath = htmlspecialchars($row["certificate_path"]);
                                $paperPath = htmlspecialchars($row["paper_file_path"]);
                                echo "<tr>
                                    <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' 
                                        data-filepath='" . $certificatepath . "'  onchange='trackOrder(event)'></td>
                                    <td>" . htmlspecialchars($row["Username"]) . "</td>
                                    <td>" . htmlspecialchars($row["branch"]) . "</td>
                                    <td>" . htmlspecialchars($row["acd_year"]) . "</td>
                                    <td>" . htmlspecialchars($row["paper_title"]) . "</td>
                                    <td>" . htmlspecialchars($row["from_date"]) . "</td>
                                    <td>" . htmlspecialchars($row["to_date"]) . "</td>
                                    <td>" . htmlspecialchars($row["organised_by"]) . "</td>
                                    <td>" . htmlspecialchars($row["location"]) . "</td>
                                    <td>" . htmlspecialchars($row["paper_type"]) . "</td>
                                    <td class='center-cell'>
                                                <select class='file-select' onchange='handleFileTypeChange(this, " . $row["id"] . ")'>";

                                if ($row["paper_type"] === "participated") {
                                    echo "<option value='certificate' data-path='" . $certificatepath . "' selected>Certificate</option>";
                                } else {
                                    echo "<option value='' disabled selected>Choose file</option>";
                                    echo "<option value='certificate' data-path='" . $certificatepath . "'>Certificate</option>";
                                    echo "<option value='paper_file' data-path='" . $paperPath . "'>Paper File</option>";
                                }

                                echo "      </select>
                                            </td>
                                  </tr>";
                            }

                            echo "</table>
                              <br>
                              <div class='bulk-actions'>
                                  <button type='button' class='view-btn' onclick='bulkView()'>View Selected</button>
                                    <button type='button' class='download-btn' onclick='bulkDownload()'>Download Selected</button>&nbsp
                                        <button type='button' id='mergeBtn' class='merge' onclick='mergePDFs()' disabled>Merge PDFs</button>&nbsp
                                        <button type='submit' class='delete-btn' name='action' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button><br>
                                        <button type='button' class='merge' id='mergedFileButton' onclick='viewMergedFile()' style='display:none;'>View Merged File</button>
                                        </div>
                            </form>";
                        } else {
                            echo "<p class='no-files'>No conference entries found.</p>";
                        }
                        echo "</div>";
                        break;


                    case 'Professional Bodies':

                        echo "<div class='container11'>
                                <h2>Student Professional Bodies</h2>";
                        echo "<form method='POST' class='ex_b'>
                                <input type='hidden' name='bodies_sub_select' value='" . htmlspecialchars($bodies_sub_select) . "'>
                                <input type='hidden' name='branch_select' value='" . htmlspecialchars($branch_select) . "'>
                                <button type='submit' class='ex_bt' name='export_sbodies'>Export to Excel</button>
                              </form>";

                        $sql_sbodies = "SELECT * FROM s_bodies WHERE Body = ? and branch = ?";
                        $stmt_sbodies = $conn->prepare($sql_sbodies);
                        $stmt_sbodies->bind_param("ss", $bodies_sub_select, $branch_select);
                        $stmt_sbodies->execute();
                        $result_sbodies = $stmt_sbodies->get_result();

                        if ($result_sbodies->num_rows > 0) {
                            echo "<form method='POST' action=''>
                            <input type='hidden' name='main_select' value='" . htmlspecialchars($main_select) . "'>
                            <input type='hidden' name='branch_select' value='" . htmlspecialchars($branch_select) . "'>
                
                                    <table border='1'>
                                        <tr>
                                            <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                                            <th>Username</th>
                                            <th>Branch</th>
                                            <th>Academic Year</th>
                                            <th>Body</th>
                                            <th>Event Name</th>
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Organised By</th>
                                            <th>Location</th>
                                            <th>Participation Status</th>
                                            
                                        </tr>";

                            while ($row = $result_sbodies->fetch_assoc()) {
                                $certificatePath = htmlspecialchars($row["certificate_path"]);
                                echo "<tr>
                                        <td><input type='checkbox' name='selected_files[]' value='" . $row["ID"] . "' 
                                            data-filepath='" . $certificatePath . "'  onchange='trackOrder(event)'></td>
                                        <td>" . htmlspecialchars($row["Username"]) . "</td>
                                        <td>" . htmlspecialchars($row["branch"]) . "</td>
                                        <td>" . htmlspecialchars($row["acd_year"]) . "</td>
                                        <td>" . htmlspecialchars($row["Body"]) . "</td>
                                        <td>" . htmlspecialchars($row["event_name"]) . "</td>
                                        <td>" . htmlspecialchars($row["from_date"]) . "</td>
                                        <td>" . htmlspecialchars($row["to_date"]) . "</td>
                                        <td>" . htmlspecialchars($row["organised_by"]) . "</td>
                                        <td>" . htmlspecialchars($row["location"]) . "</td>
                                        <td>" . htmlspecialchars($row["participation_status"]) . "</td>
                                        
                                      </tr>";
                            }

                            echo "</table>
                                  <br>
                                  <div class='bulk-actions'>
                                      <button type='button' class='view-btn' onclick='bulkView()'>View Selected</button>
                                      <button type='submit' class='download-btn' name='action' value='download'>Download Selected</button>&
                                        <button type='button' id='mergeBtn' class='merge' onclick='mergePDFs()' disabled>Merge PDFs</button>&nbsp
                                        <button type='submit' class='delete-btn' name='action' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button><br>
                                        <button type='button' class='merge' id='mergedFileButton' onclick='viewMergedFile()' style='display:none;'>View Merged File</button>
                                  </div>
                                </form>";
                        } else {
                            echo "<p class='no-files'>No professional body entries found.</p>";
                        }
                        echo "</div>";
                        break;



                    case 'Projects':
                    case 'Internships':
                    case 'SIH':
                        echo "<div class='container11'>
                            <h2>Student Projects</h2>";
                        echo "<form method='POST' class='ex_b'>
                                    <input type='hidden' name='main_select' value='" . htmlspecialchars($main_select) . "'>
                                    <input type='hidden' name='branch_select' value='" . htmlspecialchars($branch_select) . "'>
                                    <button type='submit' class='ex_bt' name='export_sevents'>Export to Excel</button>
                                </form>";

                        $sql_sevents = "SELECT * FROM s_events WHERE branch = ? and activity = ?";
                        $stmt_sevents = $conn->prepare($sql_sevents);
                        $stmt_sevents->bind_param("ss", $branch_select, $main_select);
                        $stmt_sevents->execute();
                        $result_sevents = $stmt_sevents->get_result();

                        if ($result_sevents->num_rows > 0) {
                            echo "<form method='POST' action=''>
                                        <input type='hidden' name='main_select' value='" . htmlspecialchars($main_select) . "'>
                                        <input type='hidden' name='branch_select' value='" . htmlspecialchars($branch_select) . "'>
                
                                        <table border='1'>
                                            <tr>
                                                <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                                                <th>Username</th>
                                                <th>Branch</th>
                                                <th>Academic Year</th>
                                                <th>Activity</th>
                                                <th>Event Name</th>
                                                <th>From Date</th>
                                                <th>To Date</th>
                                                <th>Organised By</th>
                                                <th>Location</th>
                                                <th>Participation Status</th>
                                                
                                            </tr>";

                            while ($row = $result_sevents->fetch_assoc()) {
                                $certificatePath = htmlspecialchars($row["certificate_path"]);
                                echo "<tr>
                                            <td><input type='checkbox' name='selected_files[]' value='" . $row["ID"] . "' 
                                                data-filepath='" . $certificatePath . "' onchange='trackOrder(event)'></td>
                                            
                                            <td>" . htmlspecialchars($row["Username"]) . "</td>
                                            <td>" . htmlspecialchars($row["branch"]) . "</td>
                                            <td>" . htmlspecialchars($row["acd_year"]) . "</td>
                                            <td>" . htmlspecialchars($row["activity"]) . "</td>
                                            <td>" . htmlspecialchars($row["event_name"]) . "</td>
                                            <td>" . htmlspecialchars($row["from_date"]) . "</td>
                                            <td>" . htmlspecialchars($row["to_date"]) . "</td>
                                            <td>" . htmlspecialchars($row["organised_by"]) . "</td>
                                            <td>" . htmlspecialchars($row["location"]) . "</td>
                                            <td>" . htmlspecialchars($row["participation_status"]) . "</td>
                                        </tr>";
                            }

                            echo "</table>
                                    <br>
                                    <div class='bulk-actions'>
                                        <button type='button' class='view-btn' onclick='bulkView()'>View Selected</button>
                                        <button type='submit' class='download-btn' name='action' value='download'>Download Selected</button>&nbsp
                                        <button type='button' id='mergeBtn' class='merge' onclick='mergePDFs()' disabled>Merge PDFs</button>&nbsp
                                        <button type='submit' class='delete-btn' name='action' value='delete' onclick='return confirm(\"Delete selected records?\")'>Delete Selected</button><br>
                                        <button type='button' class='merge' id='mergedFileButton' onclick='viewMergedFile()' style='display:none;'>View Merged File</button>
                                    </div>
                                    </form>";
                        } else {
                            echo "<p class='no-files'>No " . htmlspecialchars($main_select) . " entries found.</p>";
                        }
                        echo "</div>";
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
                    viewCell.innerHTML = `<a href="../common/view_file1.php?file_path=${encodeURIComponent(filePath)}" target="_blank"><button id="view">View</button></a>`;
                    downloadCell.innerHTML = `<a href="${filePath}" download><button id="down">Download</button></a>`;
                } else {
                    viewCell.innerHTML = '';
                    downloadCell.innerHTML = '';
                }
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
                        window.open('../common/view_file1.php?file_path=' + encodeURIComponent(filePath), '_blank');
                    }
                });
            }
            function bulkDownload() {
                let checkboxes = document.querySelectorAll('input[name="selected_files[]"]:checked');

                if (checkboxes.length === 0) {
                    alert('Please select at least one file to download.');
                    return;
                }

                checkboxes.forEach(checkbox => {
                    let filePath = checkbox.dataset.filepath;
                    if (filePath && filePath !== '') {
                        let link = document.createElement('a');
                        link.href = filePath;
                        link.download = '';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                });

            }

        </script>
</body>

</html>