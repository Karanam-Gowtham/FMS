<?php
include("../includes/connection.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['h_username']) && !isset($_SESSION['admin'])) {
    die("You need to log in to view uploads.");
}

$dept = "";
if (isset($_GET['dept'])) {
    $dept = $_GET['dept'];
} else {
    $dept = $_SESSION['dept'] ?? '';
}

$catg = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['action_name'])){
        $catg = $_POST['action_name'];
    } elseif(isset($_POST['action_F'])){
        $catg = $_POST['action_F'];
    }
} elseif($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(isset($_GET['action_name'])){
        $catg = $_GET['action_name'];
    } elseif(isset($_GET['action_F'])){
        $catg = $_GET['action_F'];
    }
}

$action='';
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['selected_files'])) {
    $action = $_POST['action'];
    $selectedFiles = $_POST['selected_files'];
    $category = $_POST['category'];

    switch($category) {
        case 'fdps': $tableName = 'fdps_tab'; $fileColumn = 'certificate'; break;
        case 'fdps_org': $tableName = 'fdps_org_tab'; $fileColumn = 'certificate'; break;
        case 'published': $tableName = 'published_tab'; $fileColumn = 'paper_file'; break;
        case 'conference': $tableName = 'conference_tab'; $fileColumn = 'certificate_path'; break;
        case 'patents': $tableName = 'patents_table'; $fileColumn = 'patent_file'; break;
        default: $tableName = 'fdps_tab'; $fileColumn = 'certificate';
    }

    // HOD actions: mainly download. Delete is disabled for safety unless requested.
    if ($action == 'download') {
        if (ob_get_length()) ob_end_clean();
        
        if (count($selectedFiles) == 1) {
            $fileId = $selectedFiles[0];
            $sql = "SELECT $fileColumn FROM $tableName WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();
    
            if ($file && !empty($file[$fileColumn]) && file_exists("../" . $file[$fileColumn])) {
                $filePath = "../" . $file[$fileColumn];
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                exit;
            } else {
                echo "<script>alert('File not found.'); window.location.href = window.location.href;</script>";
                exit;
            }
        } else {
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
    
                    if ($file && !empty($file[$fileColumn]) && file_exists("../" . $file[$fileColumn])) {
                        $zip->addFile("../" . $file[$fileColumn], basename($file[$fileColumn]));
                    }
                }
                $zip->close();
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($zipFileName) . '"');
                header('Content-Length: ' . filesize($zipFilePath));
                readfile($zipFilePath);
                unlink($zipFilePath);
                exit;
            } else {
                echo "<script>alert('Failed to create zip file.'); window.location.href = window.location.href;</script>";
                exit;
            }
        }
    }
}

// Export logic (same as DC but path adapted if needed)
if (isset($_POST['export_fdps']) || isset($_POST['export_fdps_org']) || isset($_POST['export_published']) || isset($_POST['export_conference']) || isset($_POST['export_patent'])) {
    // ... Implement export logic if critical, but for brevity relying on generic export or View functionality
    // Since the code is long, I'll include the export handling
    ob_end_clean();
    ob_start();
    header("Content-Type: application/vnd.ms-excel");
    
    $export_dept = $_POST['dept'];
    
    if(isset($_POST['export_fdps'])){
        header("Content-Disposition: attachment; filename=fdps_records.xls");
        echo "Username\tBranch\tTitle\tDate From\tDate To\tOrganised By\tLocation\n";
        $sql = "SELECT * FROM fdps_tab WHERE branch = ?";
        $tableName = 'fdps_tab';
    } elseif(isset($_POST['export_fdps_org'])){
        header("Content-Disposition: attachment; filename=fdps_organized.xls");
        echo "Username\tBranch\tTitle\tDate From\tDate To\tOrganised By\tLocation\n";
        $sql = "SELECT * FROM fdps_org_tab WHERE branch = ?";
    } elseif(isset($_POST['export_published'])){
        header("Content-Disposition: attachment; filename=published_papers.xls");
        echo "Username\tBranch\tPaper Title\tJournal Name\tIndexing\tDate of Submission\tQuality Factor\tImpact Factor\tPayment\n";
        $sql = "SELECT * FROM published_tab WHERE branch = ?";
    } elseif(isset($_POST['export_conference'])){
        header("Content-Disposition: attachment; filename=conference_papers.xls");
        echo "Username\tBranch\tPaper Title\tFrom Date\tTo Date\tOrganised By\tLocation\tPaper Type\n";
        $sql = "SELECT * FROM conference_tab WHERE branch = ?";
    } elseif(isset($_POST['export_patent'])){
        header("Content-Disposition: attachment; filename=patents.xls");
        echo "Username\tBranch\tPatent Title\tDate of Issue\n";
        $sql = "SELECT * FROM patents_table WHERE branch = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $export_dept);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Output fields based on type
        // Simplified for brevity, normally we'd separate this
        // But since we know the table, we can output generic or specific
        if(isset($_POST['export_fdps'])){
             echo "{$row['username']}\t{$row['branch']}\t{$row['title']}\t{$row['date_from']}\t{$row['date_to']}\t{$row['organised_by']}\t{$row['location']}\n";
        } elseif(isset($_POST['export_fdps_org'])){
             echo "{$row['username']}\t{$row['branch']}\t{$row['title']}\t{$row['date_from']}\t{$row['date_to']}\t{$row['organised_by']}\t{$row['location']}\n";
        } elseif(isset($_POST['export_published'])){
             echo "{$row['username']}\t{$row['branch']}\t{$row['paper_title']}\t{$row['journal_name']}\t{$row['indexing']}\t{$row['date_of_submission']}\t{$row['quality_factor']}\t{$row['impact_factor']}\t{$row['payment']}\n";
        } elseif(isset($_POST['export_conference'])){
             echo "{$row['username']}\t{$row['branch']}\t{$row['paper_title']}\t{$row['from_date']}\t{$row['to_date']}\t{$row['organised_by']}\t{$row['location']}\t{$row['paper_type']}\n";
        } elseif(isset($_POST['export_patent'])){
             echo "{$row['Username']}\t{$row['branch']}\t{$row['patent_title']}\t{$row['date_of_issue']}\n";
        }
    }
    ob_end_flush();
    exit;
}

include("header_hod.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Achievements</title>
    <link rel="stylesheet" href="../assets/css/download_pap.css">
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>
    <style>
        .nav-items span, .nav-items a { font-size: 16px; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <div class="nav-items">
            <a href="../index.php" class="home-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </a>
            <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="../admin/admins.php?dept=<?php echo urlencode($dept); ?>" class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
            <span id="sp">&nbsp; >> &nbsp;</span><span class="sid"><a href="see_uploads.php" class="home-icon">HOD</a></span>
            <span id="sp">&nbsp; >> &nbsp;</span><span class="main"><a href="#" class="main-a"><?php echo ($catg === 'fdps') ? 'fdps_attended' : "$catg"; ?>_Files</a></span>
        </div>
    </div>
</nav>
   
    <div class="div1">
        <div class="filter-section">
            <h1><?php echo ($catg === 'fdps') ? 'fdps_attended' : "$catg"; ?> Files</h1>
            <form method="POST" class="filter-form">
            <input type="hidden" name="action_F" value="<?php echo htmlspecialchars($catg); ?>">
            <?php 
                if ($dept) {
                    echo '<input type="hidden" name="dept" value="' . htmlspecialchars($dept) . '">';
                } else {
            ?>
                <select name="dept" id="dept">
                    <option value="" disabled selected>Select Department</option>
                    <option value="CSE">CSE</option>
                    <option value="AIML">AIML</option>
                    <option value="AIDS">AIDS</option>
                    <option value="IT">IT</option>
                    <option value="ECE">ECE</option>
                    <option value="EEE">EEE</option>
                    <option value="MECH">MECH</option>
                    <option value="CIVIL">CIVIL</option>
                    <option value="BSH">BSH</option>
                </select>
            <?php } ?>
                <button type="submit" name = "sel_btn" class="filter-button">Show Results</button>
            </form>
        </div>

        <?php
        if ($catg) {
            $category = $catg;
            
            switch($category) {
                case 'fdps':
                    echo "<div class='container11'><h2>FDPs Attended</h2>";
                    echo "<form method='POST' class='ex_b'><input type='hidden' name='dept' value='$dept'><button type='submit' class='ex_bt' name='export_fdps'>Export to Excel</button></form>";
                    $sql = "SELECT * FROM fdps_tab WHERE branch = ? AND status = 'Accepted'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $dept);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        echo "<form method='POST' action=''><input type='hidden' name='category' value='fdps'><table border='1'><tr><th><input type='checkbox' onclick='toggleSelectAll(this)'></th><th>Username</th><th>Branch</th><th>Title</th><th>Date From</th><th>Date To</th><th>Organised By</th><th>Location</th></tr>";
                        while ($row = $result->fetch_assoc()) {
                            $path = htmlspecialchars($row["certificate"]);
                            echo "<tr><td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' data-filepath='../" . $path . "'></td><td>{$row['username']}</td><td>{$row['branch']}</td><td>{$row['title']}</td><td>{$row['date_from']}</td><td>{$row['date_to']}</td><td>{$row['organised_by']}</td><td>{$row['location']}</td></tr>";
                        }
                        echo "</table><div class='bulk-actions'><button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button><button type='submit' name='action' class='btn download-btn' value='download'>Download Selected</button></div></form>";
                    } else { echo "<p class='no-files'>No FDPs attended found.</p>"; }
                    echo "</div>";
                    break;

                case 'fdps_org':
                    echo "<div class='container11'><h2>FDPs Organised</h2>";
                    echo "<form method='POST' class='ex_b'><input type='hidden' name='dept' value='$dept'><button type='submit' class='ex_bt' name='export_fdps_org'>Export to Excel</button></form>";
                    $sql = "SELECT * FROM fdps_org_tab WHERE branch = ? AND status = 'Accepted'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $dept);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        echo "<form method='POST' action=''><input type='hidden' name='category' value='fdps_org'><table border='1'><tr><th><input type='checkbox' onclick='toggleSelectAll(this)'></th><th>Username</th><th>Branch</th><th>Title</th><th>Date From</th><th>Date To</th><th>Organised By</th><th>Location</th><th>Select File</th></tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr><td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' data-filepath='../" . $row["certificate"] . "'></td><td>{$row['username']}</td><td>{$row['branch']}</td><td>{$row['title']}</td><td>{$row['date_from']}</td><td>{$row['date_to']}</td><td>{$row['organised_by']}</td><td>{$row['location']}</td>
                            <td><select class='file-select' onchange='handleFileTypeChange(this, " . $row["id"] . ")'>
                                <option value='certificate' data-path='../" . htmlspecialchars($row["certificate"]) . "'>Certificate</option>
                                <option value='brochure' data-path='../" . htmlspecialchars($row["brochure"]) . "'>Brochure</option>
                                <option value='report' data-path='../" . htmlspecialchars($row["fdp_report"]) . "'>Report</option>
                            </select></td></tr>";
                        }
                        echo "</table><div class='bulk-actions'><button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button><button type='submit' name='action' class='btn download-btn' value='download'>Download Selected</button></div></form>";
                    } else { echo "<p class='no-files'>No FDPs organised found.</p>"; }
                    echo "</div>";
                    break;

                case 'published':
                    echo "<div class='container11'><h2>Published Papers</h2>";
                    echo "<form method='POST' class='ex_b'><input type='hidden' name='dept' value='$dept'><button type='submit' class='ex_bt' name='export_published'>Export to Excel</button></form>";
                    $sql = "SELECT * FROM published_tab WHERE branch = ? AND status = 'Accepted'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $dept);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        echo "<form method='POST' action=''><input type='hidden' name='category' value='published'><table border='1'><tr><th><input type='checkbox' onclick='toggleSelectAll(this)'></th><th>Username</th><th>Branch</th><th>Paper Title</th><th>Journal</th><th>Indexing</th><th>Submission</th><th>Quality</th><th>Impact</th><th>Payment</th></tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr><td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' data-filepath='../" . $row["paper_file"] . "'></td><td>{$row['username']}</td><td>{$row['branch']}</td><td>{$row['paper_title']}</td><td>{$row['journal_name']}</td><td>{$row['indexing']}</td><td>{$row['date_of_submission']}</td><td>{$row['quality_factor']}</td><td>{$row['impact_factor']}</td><td>{$row['payment']}</td></tr>";
                        }
                        echo "</table><div class='bulk-actions'><button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button><button type='submit' name='action' class='btn download-btn' value='download'>Download Selected</button></div></form>";
                    } else { echo "<p class='no-files'>No published papers found.</p>"; }
                    echo "</div>";
                    break;

                case 'conference':
                    echo "<div class='container11'><h2>Conference Papers</h2>";
                    echo "<form method='POST' class='ex_b'><input type='hidden' name='dept' value='$dept'><button type='submit' class='ex_bt' name='export_conference'>Export to Excel</button></form>";
                    $sql = "SELECT * FROM conference_tab WHERE branch = ? AND status = 'Accepted'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $dept);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        echo "<form method='POST' action=''><input type='hidden' name='category' value='conference'><table border='1'><tr><th><input type='checkbox' onclick='toggleSelectAll(this)'></th><th>Username</th><th>Branch</th><th>Paper Title</th><th>From</th><th>To</th><th>Organised By</th><th>Location</th><th>Type</th><th>File</th></tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr><td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' data-filepath='../" . $row["certificate_path"] . "'></td><td>{$row['username']}</td><td>{$row['branch']}</td><td>{$row['paper_title']}</td><td>{$row['from_date']}</td><td>{$row['to_date']}</td><td>{$row['organised_by']}</td><td>{$row['location']}</td><td>{$row['paper_type']}</td>
                            <td><select class='file-select' onchange='handleFileTypeChange(this, " . $row["id"] . ")'>
                                <option value='certificate' data-path='../" . htmlspecialchars($row["certificate_path"]) . "'>Certificate</option>
                                <option value='paper' data-path='../" . htmlspecialchars($row["paper_file_path"]) . "'>Paper</option>
                            </select></td></tr>";
                        }
                        echo "</table><div class='bulk-actions'><button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button><button type='submit' name='action' class='btn download-btn' value='download'>Download Selected</button></div></form>";
                    } else { echo "<p class='no-files'>No conference papers found.</p>"; }
                    echo "</div>";
                    break;
                
                case 'patents':
                    echo "<div class='container11'><h2>Patents</h2>";
                    echo "<form method='POST' class='ex_b'><input type='hidden' name='dept' value='$dept'><button type='submit' class='ex_bt' name='export_patent'>Export to Excel</button></form>";
                    $sql = "SELECT * FROM patents_table WHERE branch = ? AND status = 'Accepted'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $dept);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        echo "<form method='POST' action=''><input type='hidden' name='category' value='patents'><table border='1'><tr><th><input type='checkbox' onclick='toggleSelectAll(this)'></th><th>Username</th><th>Branch</th><th>Title</th><th>Date Issue</th></tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr><td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' data-filepath='../" . $row["patent_file"] . "'></td><td>{$row['Username']}</td><td>{$row['branch']}</td><td>{$row['patent_title']}</td><td>{$row['date_of_issue']}</td></tr>";
                        }
                        echo "</table><div class='bulk-actions'><button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button><button type='submit' name='action' class='btn download-btn' value='download'>Download Selected</button></div></form>";
                    } else { echo "<p class='no-files'>No patents found.</p>"; }
                    echo "</div>";
                    break;
            }
        }
        ?>
    </div>

<script>
function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('input[name="selected_files[]"]');
    checkboxes.forEach(cb => {
        cb.checked = source.checked;
    });
}

function handleFileTypeChange(selectElem, rowId) {
    const selectedOption = selectElem.options[selectElem.selectedIndex];
    const selectedPath = selectedOption.getAttribute("data-path");
    const checkbox = document.querySelector(`input[name="selected_files[]"][value="${rowId}"]`);
    if (checkbox) {
        checkbox.setAttribute("data-filepath", selectedPath);
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
            window.open('view_file_hod.php?file_path=' + encodeURIComponent(filePath), '_blank');
        }
    });
}
</script>

</body>
</html>
