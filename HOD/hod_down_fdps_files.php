<?php
ob_start();
ini_set('display_errors', 0);
include_once "../includes/connection.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('REGEX_UPLOADS', '/uploads\/.*/');
define('PATH_UP_UP', '../../');
define('ATTR_DATA_FILEPATH', "' data-filepath='");
define('ATTR_DATA_FILES', "' data-files='");
define('HTML_QUOT', '&quot;');
define('HTML_AMP', '&amp;');
define('HTML_X2F', '&#x2F;');

if (!isset($_SESSION['h_username']) && !isset($_SESSION['admin'])) {
    die("You need to log in to view uploads.");
}

if (isset($_GET['dept'])) {
    $dept = $_GET['dept'];
} elseif (isset($_POST['dept'])) {
    $dept = $_POST['dept'];
} else {
    $dept = $_SESSION['dept'] ?? '';
}

function fixPath($p)
{
    if (empty($p)) {
        return "";
    }
    $p = htmlspecialchars_decode($p);
    $p = str_replace('\\', '/', $p);
    $resultPath = $p;
    if (preg_match(REGEX_UPLOADS, $p, $matches)) {
        $foundPath = $matches[0];
        if (file_exists("../" . $foundPath)) {
            $resultPath = "../" . $foundPath;
        } elseif (file_exists($foundPath)) {
            $resultPath = $foundPath;
        } elseif (file_exists(PATH_UP_UP . $foundPath)) {
            $resultPath = PATH_UP_UP . $foundPath;
        } else {
            $resultPath = "../" . $foundPath; // Default
        }
    }
    return $resultPath;
}

$catg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_name'])) {
        $catg = $_POST['action_name'];
    } elseif (isset($_POST['action_F'])) {
        $catg = $_POST['action_F'];
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['selected_files'])) {
    // CSRF Check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $action = $_POST['action'];
    $selectedFiles = $_POST['selected_files'];
    $category = $_POST['category'];

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

    // HOD actions: mainly download. Delete is disabled for safety unless requested.
    if ($action == 'download') {
        if (ob_get_length()) {
            ob_end_clean();
        }

        if (count($selectedFiles) == 1) {
            $fileId = $selectedFiles[0];
            $sql = "SELECT $fileColumn FROM $tableName WHERE id = ? AND branch = ? AND status = 'Accepted'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $fileId, $dept);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();

            if ($file && !empty($file[$fileColumn])) {
                $filePath = $file[$fileColumn];
                $p = str_replace('\\', '/', $filePath);
                if (preg_match(REGEX_UPLOADS, $p, $matches)) {
                    $foundPath = $matches[0];
                    if (file_exists("../" . $foundPath)) {
                        $p = "../" . $foundPath;
                    } elseif (file_exists($foundPath)) {
                        $p = $foundPath;
                    } elseif (file_exists(PATH_UP_UP . $foundPath)) {
                        $p = PATH_UP_UP . $foundPath;
                    }
                }

                if (file_exists($p)) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($p) . '"');
                    header('Content-Length: ' . filesize($p));
                    ob_clean();
                    flush();
                    readfile($p);
                    exit;
                } else {
                    echo "<script>alert('File not found. Path: " . htmlspecialchars($p, ENT_QUOTES) . "'); window.location.href = window.location.href;
        function viewSingleFile(filePath) {
            window.open('view_file_hod.php?file_path=' + encodeURIComponent(filePath), '_blank');
        }
</script>";
                    exit;
                }
            }
        } else {
            $zip = new ZipArchive();
            $safe_category = preg_replace('/[^\w.-]/', '', (string)$category);
            $zipFileName = $safe_category . "_files_" . time() . ".zip";
            $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;

            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $filesAdded = 0;
                foreach ($selectedFiles as $fileId) {
                    $sql = "SELECT $fileColumn FROM $tableName WHERE id = ? AND branch = ? AND status = 'Accepted'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $fileId, $dept);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $file = $result->fetch_assoc();

                    if ($file && !empty($file[$fileColumn])) {
                        $f = $file[$fileColumn];
                        $p = str_replace('\\', '/', $f);
                        if (preg_match(REGEX_UPLOADS, $p, $matches)) {
                            $foundPath = $matches[0];
                            if (file_exists("../" . $foundPath)) {
                                $p = "../" . $foundPath;
                            } elseif (file_exists($foundPath)) {
                                $p = $foundPath;
                            } elseif (file_exists(PATH_UP_UP . $foundPath)) {
                                $p = PATH_UP_UP . $foundPath;
                            }
                        }
                        if (file_exists($p)) {
                            $zip->addFile($p, basename($p));
                            $filesAdded++;
                        }
                    }
                }
                $zip->close();

                if ($filesAdded > 0) {
                    header('Content-Type: application/zip');
                    header('Content-Disposition: attachment; filename="' . basename($zipFileName) . '"');
                    header('Content-Length: ' . filesize($zipFilePath));
                    ob_clean();
                    flush();
                    readfile($zipFilePath);
                    unlink($zipFilePath);
                    exit;
                } else {
                    echo "<script>alert('No valid files were found to add to the ZIP.'); window.location.href = window.location.href;</script>";
                    unlink($zipFilePath);
                    exit;
                }
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

    if (isset($_POST['export_fdps'])) {
        header("Content-Disposition: attachment; filename=fdps_records.xls");
        echo "Username\tBranch\tTitle\tDate From\tDate To\tOrganised By\tLocation\n";
        $sql = "SELECT * FROM fdps_tab WHERE branch = ? AND status = 'Accepted'";
        $tableName = 'fdps_tab';
    } elseif (isset($_POST['export_fdps_org'])) {
        header("Content-Disposition: attachment; filename=fdps_organized.xls");
        echo "Username\tBranch\tTitle\tDate From\tDate To\tOrganised By\tLocation\n";
        $sql = "SELECT * FROM fdps_org_tab WHERE branch = ? AND status = 'Accepted'";
    } elseif (isset($_POST['export_published'])) {
        header("Content-Disposition: attachment; filename=published_papers.xls");
        echo "Username\tBranch\tPaper Title\tJournal Name\tIndexing\tDate of Submission\tQuality Factor\tImpact Factor\tPayment\n";
        $sql = "SELECT * FROM published_tab WHERE branch = ? AND status = 'Accepted'";
    } elseif (isset($_POST['export_conference'])) {
        header("Content-Disposition: attachment; filename=conference_papers.xls");
        echo "Username\tBranch\tPaper Title\tFrom Date\tTo Date\tOrganised By\tLocation\tPaper Type\n";
        $sql = "SELECT * FROM conference_tab WHERE branch = ? AND status = 'Accepted'";
    } elseif (isset($_POST['export_patent'])) {
        header("Content-Disposition: attachment; filename=patents.xls");
        echo "Username\tBranch\tPatent Title\tDate of Issue\n";
        $sql = "SELECT * FROM patents_table WHERE branch = ? AND status = 'Accepted'";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $export_dept);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Output fields based on type
        // Simplified for brevity, normally we'd separate this
        // But since we know the table, we can output generic or specific
        if (isset($_POST['export_fdps'])) {
            echo "{$row['username']}\t{$row['branch']}\t{$row['title']}\t{$row['date_from']}\t{$row['date_to']}\t{$row['organised_by']}\t{$row['location']}\n";
        } elseif (isset($_POST['export_fdps_org'])) {
            echo "{$row['username']}\t{$row['branch']}\t{$row['title']}\t{$row['date_from']}\t{$row['date_to']}\t{$row['organised_by']}\t{$row['location']}\n";
        } elseif (isset($_POST['export_published'])) {
            echo "{$row['username']}\t{$row['branch']}\t{$row['paper_title']}\t{$row['journal_name']}\t{$row['indexing']}\t{$row['date_of_submission']}\t{$row['quality_factor']}\t{$row['impact_factor']}\t{$row['payment']}\n";
        } elseif (isset($_POST['export_conference'])) {
            echo "{$row['username']}\t{$row['branch']}\t{$row['paper_title']}\t{$row['from_date']}\t{$row['to_date']}\t{$row['organised_by']}\t{$row['location']}\t{$row['paper_type']}\n";
        } elseif (isset($_POST['export_patent'])) {
            echo "{$row['Username']}\t{$row['branch']}\t{$row['patent_title']}\t{$row['date_of_issue']}\n";
        }
    }
    ob_end_flush();
    exit;
}

include_once "header_hod.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Achievements</title>
    <link rel="stylesheet" href="../assets/css/download_pap.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js" integrity="sha256-D5pcrQeUHwgmWGyU4InYm5GMRuXBfPLVo8b2ZuO8aU8=" crossorigin="anonymous"></script>
    <style>
        .nav-items span,
        .nav-items a {
            font-size: 16px;
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
                <span>&nbsp; >> &nbsp; </span><span class="sid"><a
                        href="../admin/admins.php?dept=<?php echo urlencode($dept); ?>"
                        class="home-icon">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span class="sp-divider">&nbsp; >> &nbsp;</span><span class="sid"><a href="see_uploads.php"
                        class="home-icon">HOD</a></span>
                <span class="sp-divider">&nbsp; >> &nbsp;</span><span class="main"><a href="#"
                        class="main-a"><?php echo ($catg === 'fdps') ? 'fdps_attended' : htmlspecialchars($catg); ?>_Files</a></span>
            </div>
        </div>
    </nav>

    <div class="div1">
        <div class="filter-section">
            <h1><?php echo ($catg === 'fdps') ? 'fdps_attended' : htmlspecialchars($catg); ?> Files</h1>
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
                <button type="submit" name="sel_btn" class="filter-button">Show Results</button>
            </form>
        </div>

        <?php
        if ($catg) {
            $category = $catg;

            switch ($category) {
                case 'fdps':
                    echo "<div class='container11'><h2>FDPs Attended</h2>";
                    echo "<form method='POST' class='ex_b'><input type='hidden' name='dept' value='" . htmlspecialchars($dept) . "'><button type='submit' class='ex_bt' name='export_fdps'>Export to Excel</button></form>";
                    $sql = "SELECT * FROM fdps_tab WHERE branch = ? AND status = 'Accepted'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $dept);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        echo "<form method='POST' action=''>
                            <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                            <input type='hidden' name='category' value='fdps'>
                            <table border='1'>
                                <tr>
                                    <th scope='col' id='th_select_all'><input type='checkbox' onclick='toggleSelectAll(this)' onkeydown='if(event.key === \"Enter\") this.click()'></th>
                                    <th scope='col' id='th_username'>Username</th>
                                    <th scope='col' id='th_branch'>Branch</th>
                                    <th scope='col' id='th_title'>Title</th>
                                    <th scope='col' id='th_date_from'>Date From</th>
                                    <th scope='col' id='th_date_to'>Date To</th>
                                    <th scope='col' id='th_organised_by'>Organised By</th>
                                    <th scope='col' id='th_location'>Location</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            $path = fixPath($row["certificate"]);
                            $f_raw = json_encode(array_values(array_filter([$path], fn($f) => strlen($f) > 3)), JSON_UNESCAPED_SLASHES);
                            $f_json = str_replace('"', HTML_QUOT, $f_raw);
                            echo "<tr>
                                <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' " . ATTR_DATA_FILEPATH . $path . ATTR_DATA_FILES . $f_json . "'></td>
                                <td>" . htmlspecialchars($row['username']) . "</td>
                                <td>" . htmlspecialchars($row['branch']) . "</td>
                                <td>" . htmlspecialchars($row['title']) . "</td>
                                <td>" . htmlspecialchars($row['date_from']) . "</td>
                                <td>" . htmlspecialchars($row['date_to']) . "</td>
                                <td>" . htmlspecialchars($row['organised_by']) . "</td>
                                <td>" . htmlspecialchars($row['location']) . "</td>
                            </tr>";
                        }
                        echo "</table>
                        <div class='bulk-actions'>
                            <button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button>
                            <button type='submit' name='action' class='btn download-btn' value='download'>Download Selected</button>
                        </div>
                        </form>";
                    } else {
                        echo "<p class='no-files'>No FDPs attended found.</p>";
                    }
                    echo "</div>";
                    break;

                case 'fdps_org':
                    echo "<div class='container11'><h2>FDPs Organised</h2>";
                    echo "<form method='POST' class='ex_b'>
                            <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                            <input type='hidden' name='dept' value='" . htmlspecialchars($dept) . "'>
                            <button type='submit' class='ex_bt' name='export_fdps_org'>Export to Excel</button>
                          </form>";
                    $sql = "SELECT * FROM fdps_org_tab WHERE branch = ? AND status = 'Accepted'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $dept);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        echo "<form method='POST' action=''>
                            <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                            <input type='hidden' name='category' value='fdps_org'>
                            <table border='1'><tr><th><input type='checkbox' onclick='toggleSelectAll(this)'></th><th>Username</th><th>Branch</th><th>Title</th><th>Date From</th><th>Date To</th><th>Organised By</th><th>Location</th></tr>";
                        while ($row = $result->fetch_assoc()) {
                            // Prepare the files for merging in the specific order
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
                            // Remove empty entries
                            $files_to_merge = array_filter($files_to_merge, function ($f) {
                                return strlen($f) > 3;
                            });
                            $files_json_raw = json_encode(array_values($files_to_merge), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                            $files_json = str_replace('"', HTML_QUOT, $files_json_raw);

                            $record_title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');

                            $actual_merged_path = fixPath($row['merged_file']);
                            $record_has_merged = (!empty($actual_merged_path) && file_exists($actual_merged_path));
                            $mergedPath = $record_has_merged ? $actual_merged_path : "";

                            echo "<tr>
                                 <td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "'
                                     data-filepath='$mergedPath'
                                     data-files='$files_json'
                                     data-title='$record_title'></td>
                                 <td>" . htmlspecialchars($row['username']) . "</td><td>" . htmlspecialchars($row['branch']) . "</td><td>" . htmlspecialchars($row['title']) . "</td><td>" . htmlspecialchars($row['date_from']) . "</td><td>" . htmlspecialchars($row['date_to']) . "</td><td>" . htmlspecialchars($row['organised_by']) . "</td><td>" . htmlspecialchars($row['location']) . "</td>
                             </tr>";
                        }
                        echo "</table><div class='bulk-actions'><button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button><button type='button' class='btn download-btn' onclick='bulkDownload()'>Download Selected</button></div></form>";
                    } else {
                        echo "<p class='no-files'>No FDPs organised found.</p>";
                    }
                    echo "</div>";
                    break;

                case 'published':
                    echo "<div class='container11'><h2>Published Papers</h2>";
                    echo "<form method='POST' class='ex_b'><input type='hidden' name='dept' value='" . htmlspecialchars($dept) . "'><button type='submit' class='ex_bt' name='export_published'>Export to Excel</button></form>";
                    $sql = "SELECT * FROM published_tab WHERE branch = ? AND status = 'Accepted'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $dept);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        echo "<form method='POST' action=''>
                            <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                            <input type='hidden' name='category' value='published'>
                            <table border='1'><tr><th><input type='checkbox' onclick='toggleSelectAll(this)'></th><th>Username</th><th>Branch</th><th>Paper Title</th><th>Journal</th><th>Indexing</th><th>Submission</th><th>Quality</th><th>Impact</th><th>Payment</th></tr>";
                        while ($row = $result->fetch_assoc()) {
                            $path = fixPath($row["paper_file"]);
                            $pf_raw = json_encode(array_values(array_filter([$path], fn($f) => strlen($f) > 3)), JSON_UNESCAPED_SLASHES);
                            $pf_json = str_replace('"', HTML_QUOT, $pf_raw);
                            echo "<tr><td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' " . ATTR_DATA_FILEPATH . $path . ATTR_DATA_FILES . $pf_json . "'></td><td>" . htmlspecialchars($row['username']) . "</td><td>" . htmlspecialchars($row['branch']) . "</td><td>" . htmlspecialchars($row['paper_title']) . "</td><td>" . htmlspecialchars($row['journal_name']) . "</td><td>" . htmlspecialchars($row['indexing']) . "</td><td>" . htmlspecialchars($row['date_of_submission']) . "</td><td>" . htmlspecialchars($row['quality_factor']) . "</td><td>" . htmlspecialchars($row['impact_factor']) . "</td><td>" . htmlspecialchars($row['payment']) . "</td></tr>";
                        }
                        echo "</table><div class='bulk-actions'><button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button><button type='submit' name='action' class='btn download-btn' value='download'>Download Selected</button></div></form>";
                    } else {
                        echo "<p class='no-files'>No published papers found.</p>";
                    }
                    echo "</div>";
                    break;

                case 'conference':
                    echo "<div class='container11'><h2>Conference Papers</h2>";
                    echo "<form method='POST' class='ex_b'><input type='hidden' name='dept' value='" . htmlspecialchars($dept) . "'><button type='submit' class='ex_bt' name='export_conference'>Export to Excel</button></form>";
                    $sql = "SELECT * FROM conference_tab WHERE branch = ? AND status = 'Accepted'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $dept);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        echo "<form method='POST' action=''>
                            <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                            <input type='hidden' name='category' value='conference'>
                            <table border='1'><tr><th><input type='checkbox' onclick='toggleSelectAll(this)'></th><th>Username</th><th>Branch</th><th>Paper Title</th><th>From</th><th>To</th><th>Organised By</th><th>Location</th><th>Type</th></tr>";
                        while ($row = $result->fetch_assoc()) {
                            $cert_path = fixPath($row["certificate_path"]);
                            $paper_path = fixPath($row["paper_file_path"]);
                            $cf_arr = array_values(array_filter([$cert_path, $paper_path], fn($f) => strlen($f) > 3));
                            $cf_raw = json_encode($cf_arr, JSON_UNESCAPED_SLASHES);
                            $cf_json = str_replace('"', HTML_QUOT, $cf_raw);
                            echo "<tr><td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' " . ATTR_DATA_FILEPATH . $cert_path . ATTR_DATA_FILES . $cf_json . "'></td><td>" . htmlspecialchars($row['username']) . "</td><td>" . htmlspecialchars($row['branch']) . "</td><td>" . htmlspecialchars($row['paper_title']) . "</td><td>" . htmlspecialchars($row['from_date']) . "</td><td>" . htmlspecialchars($row['to_date']) . "</td><td>" . htmlspecialchars($row['organised_by']) . "</td><td>" . htmlspecialchars($row['location']) . "</td><td>" . htmlspecialchars($row['paper_type']) . "</td></tr>";
                        }
                        echo "</table><div class='bulk-actions'><button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button><button type='submit' name='action' class='btn download-btn' value='download'>Download Selected</button></div></form>";
                    } else {
                        echo "<p class='no-files'>No conference papers found.</p>";
                    }
                    echo "</div>";
                    break;

                case 'patents':
                    echo "<div class='container11'><h2>Patents</h2>";
                    echo "<form method='POST' class='ex_b'><input type='hidden' name='dept' value='" . htmlspecialchars($dept) . "'><button type='submit' class='ex_bt' name='export_patent'>Export to Excel</button></form>";
                    $sql = "SELECT * FROM patents_table WHERE branch = ? AND status = 'Accepted'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $dept);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        echo "<form method='POST' action=''>
                            <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                            <input type='hidden' name='category' value='patents'>
                            <table border='1'><tr><th><input type='checkbox' onclick='toggleSelectAll(this)'></th><th>Username</th><th>Branch</th><th>Title</th><th>Date Issue</th></tr>";
                        while ($row = $result->fetch_assoc()) {
                            $path = fixPath($row["patent_file"]);
                            $pt_raw = json_encode(array_values(array_filter([$path], fn($f) => strlen($f) > 3)), JSON_UNESCAPED_SLASHES);
                            $pt_json = str_replace('"', HTML_QUOT, $pt_raw);
                            echo "<tr><td><input type='checkbox' name='selected_files[]' value='" . $row["id"] . "' " . ATTR_DATA_FILEPATH . $path . ATTR_DATA_FILES . $pt_json . "'></td><td>" . htmlspecialchars($row['Username']) . "</td><td>" . htmlspecialchars($row['branch']) . "</td><td>" . htmlspecialchars($row['patent_title']) . "</td><td>" . htmlspecialchars($row['date_of_issue']) . "</td></tr>";
                        }
                        echo "</table><div class='bulk-actions'><button type='button' class='btn view-btn' onclick='bulkView()'>View Selected</button><button type='submit' name='action' class='btn download-btn' value='download'>Download Selected</button></div></form>";
                    } else {
                        echo "<p class='no-files'>No patents found.</p>";
                    }
                    echo "</div>";
                    break;
                default:
                    echo "<p class='no-files'>Selected category not found.</p>";
                    break;
            }
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"
        integrity="sha256-D5pcrQeUHwgmWGyU4InYm5GMRuXBfPLVo8b2ZuO8aU8="
        crossorigin="anonymous"></script>
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

        async function mergeAndAct(cb, action) {
            const filePath = cb.getAttribute('data-filepath');
            const filesJson = cb.getAttribute('data-files');
            const title = cb.getAttribute('data-title') || 'record';

            if (filePath && filePath !== '') {
                if (action === 'view') {
                    window.open('view_file_hod.php?file_path=' + encodeURIComponent(filePath), '_blank');
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
                const decodedJson = filesJson.replace(new RegExp(HTML_QUOT, 'g'), '"')
                                             .replace(new RegExp(HTML_X2F, 'g'), '/')
                                             .replace(new RegExp(HTML_AMP, 'g'), '&');
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
                    const url = URL.createObjectURL(new Blob([mergedPdfBytes], { type: 'application/pdf' }));
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
            } else if (filePath) {
                // Fallback for non-FDP records
                if (action === 'view') {
                    window.open('view_file_hod.php?file_path=' + encodeURIComponent(filePath), '_blank');
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

            // Single item with no data-files → direct view
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
                    const decodedJson = filesJson.replace(new RegExp(HTML_QUOT, 'g'), '"')
                                                 .replace(new RegExp(HTML_X2F, 'g'), '/')
                                                 .replace(new RegExp(HTML_AMP, 'g'), '&');
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
            link.download = `FDP_Organized_${title.replace(/[^a-z0-9]/gi, '_')}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>

</body>

</html>
