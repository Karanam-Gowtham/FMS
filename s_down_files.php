<?php
include("connection.php");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_GET['dept'])) {
    $dept = $_GET['dept']; // Get the 'dept' value from the URL
} else {
    echo "Department not set.";
}
// Initialize variables
// Initialize variables
$main_select = $_POST['main_select'] ?? '';
$papers_sub_select = $_POST['papers_sub_select'] ?? '';
$bodies_sub_select = $_POST['bodies_sub_select'] ?? '';
$branch_select = $_POST['branch_select'] ?? ''; // New branch variable

$records = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_button']) || isset($_POST['export'])) {
        if ($main_select === 'Journals') {
                $query = "SELECT uploaded_by, branch, paper_title, journal_name, indexing, date_of_submission, quality_factor, 
                        impact_factor, payment, submission_time, paper_file  
                        FROM s_journal_tab 
                        WHERE branch = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $branch_select);
                $stmt->execute();
                $result = $stmt->get_result();
                $records = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
        } elseif ($main_select === 'Conferences') {
                $query = "SELECT uploaded_by, branch, paper_title, from_date, to_date, organised_by, location, certificate_path, 
                        paper_type, paper_file_path, submission_time  
                        FROM s_conference_tab 
                        WHERE branch = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $branch_select);
                $stmt->execute();
                $result = $stmt->get_result();
                $records = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
            
        } elseif ($main_select === 'Professional Bodies') {
            $query = "SELECT Body, event_name, from_date, to_date, organised_by, location, participation_status, 
                        certificate_path, uploaded_by, branch, submission_time
                        FROM s_bodies 
                        WHERE Body = ? AND branch = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $bodies_sub_select, $branch_select);
            $stmt->execute();
            $result = $stmt->get_result();
            $records = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } else {
            if (isset($main_select) && !empty($main_select)) {
                $query = "SELECT activity, event_name, from_date, to_date, organised_by, location, participation_status, 
                            certificate_path, uploaded_by,branch , submission_time
                            FROM s_events 
                            WHERE activity = ? AND branch = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $main_select, $branch_select);
                $stmt->execute();
                $result = $stmt->get_result();
                $records = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
            } else {
                $records = [];
            }
        }
    }
}

function getHeadings($main_select, $sub_select = null) {
    if ($main_select === 'Journals') {
            return ['ID', 'Uploaded By', 'Branch', 'Paper Title', 'Journal Name', 'Indexing', 'Date of Submission', 'Quality Factor', 'Impact Factor', 'Payment', 'Submission Time', 'Paper File'];
    } elseif ($main_select === 'Conferences') {
            return ['ID', 'Uploaded By', 'Branch', 'Paper Title', 'From Date', 'To Date', 'Organised By', 'Location', 'Certificate', 'Paper Type', 'Paper File', 'Submission Time'];
    }
     elseif ($main_select === 'Professional Bodies') {
        return ['ID', 'Body', 'Event Name', 'From Date', 'To Date', 'Organised By', 'Location', 'Participation Status', 'Certificate', 'Uploaded By', 'Branch', 'Submission Time'];
    } else {
            return ['ID', 'Activity', 'Event Name', 'From Date', 'To Date', 'Organised By', 'Location', 'Participation Status', 'Certificate', 'Uploaded By', 'Branch', 'Submission Time'];
        }
}

if (isset($_POST['export'])) {
    if (empty($records)) {
        die("No records to export.");
    }

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=data_export.xls");

    $excel_headings = getHeadings($main_select, $papers_sub_select);
    // Remove certificate and file columns from header for excel
    $keys_to_exclude = ['certificate_path', 'paper_file_path', 'paper_file'];
    $excel_headings = array_diff($excel_headings, $keys_to_exclude);

    echo implode("\t", $excel_headings) . "\n";

    $counter = 1;
    foreach ($records as $record) {
        $sanitized_record = [$counter++];
        foreach ($record as $key => $value) {
            if (!in_array($key, $keys_to_exclude)) {
                $sanitized_record[] = '"' . str_replace('"', '""', $value) . '"';
            }
        }
        echo implode("\t", $sanitized_record) . "\n";
    }
    exit;
}

    include "./header.php";

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Data Display</title>
    <link rel="stylesheet" href="./css/s_down_files.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="index.php" class="home-icon"> Department(<?php echo"$dept" ?>) </a></span>
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="acd_year.php?dept=<?php echo "$dept" ?>" class="home-icon"> Faculty </a></span>
                <span>&nbsp; >> &nbsp;  </span><span class="sid"><a href="student_act.php?event=student_act&dept=<?php echo $dept ?>" class="home-icon"> student_activities </a></span>
                <span>&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a">download_student_Activities </a></span>
                <span>&nbsp;  >> &nbsp; </span>
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
                        <option value="Conferences" <?= $main_select == 'Conferences' ? 'selected' : '' ?>>Conferences</option>
                        <option value="Projects" <?= $main_select == 'Projects' ? 'selected' : '' ?>>Projects</option>
                        <option value="Internships" <?= $main_select == 'Internships' ? 'selected' : '' ?>>Internships</option>
                        <option value="SIH" <?= $main_select == 'SIH' ? 'selected' : '' ?>>SIH</option>
                        <option value="Professional Bodies" <?= $main_select == 'Professional Bodies' ? 'selected' : '' ?>>Professional Bodies</option>
                    </select>
                </div>
                
                <div id="bodies-sub-select-div" style="display: <?= $main_select == 'Professional Bodies' ? 'block' : 'none' ?>;">
                    <label for="bodies-sub-select">Select Subcategory:</label>
                    <select id="bodies-sub-select" name="bodies_sub_select">
                        <option value="" disabled selected>Choose an option</option>
                        <option value="ISTE" <?= $bodies_sub_select == 'ISTE' ? 'selected' : '' ?>>ISTE</option>
                        <option value="CSI" <?= $bodies_sub_select == 'CSI' ? 'selected' : '' ?>>CSI</option>
                        <option value="ACM" <?= $bodies_sub_select == 'ACM' ? 'selected' : '' ?>>ACM</option>
                        <option value="ACMW" <?= $bodies_sub_select == 'ACMW' ? 'selected' : '' ?>>ACMW</option>
                        <option value="Coding Club" <?= $bodies_sub_select == 'Coding Club' ? 'selected' : '' ?>>Coding Club</option>
                    </select>
                </div>
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
                </div>
                <div class="btn-div">
                    <button type="submit" class="btn11" name="submit_button">Submit</button>
                </div>
            </form>
            </div>
    </div>
    </div>

    <div class="container11">
    <div class="container111">

    <form method="POST"  id="bulkActionForm">
        <input type="hidden" name="main_select" value="<?= htmlspecialchars($main_select) ?>">
        <input type="hidden" name="bodies_sub_select" value="<?= htmlspecialchars($bodies_sub_select) ?>">
        <input type="hidden" name="branch_select" value="<?= htmlspecialchars($branch_select) ?>">
        <table id="data-table" border="1">
            <thead>
            <tr>
                <th><input type="checkbox" onclick="toggleAll(this)"></th>
                <?php
                    $headings = getHeadings($main_select, $bodies_sub_select);
                    foreach ($headings as $heading) {
                        echo "<th>$heading</th>";
                    }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($records)) : ?>
                <tr>
                    <td colspan="<?= count($headings) + 1; ?>" class="no-data">No data available for the selected criteria.</td>
                </tr>
            <?php else : ?>
                <?php 
                $counter = 1;
                foreach ($records as $record) : ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="selected_files[]" value="<?= $record['submission_time'] ?>">
                        </td>
                        <td><?= $counter++; ?></td>
                        <?php
                        foreach ($record as $key => $value) {
                            if (in_array($key, ['paper_file', 'certificate_path', 'paper_file_path'])) {
                                echo "<td>";
                                if ($value) {
                                    echo "<a href='$value' target='_blank' class='btn-view'>View</a> 
                                          <a href='$value' download class='btn-download'>Download</a>";
                                } else {
                                    echo "<span class='no-file'>No File</span>";
                                }
                                echo "</td>";
                            } else {
                                echo "<td>$value</td>";
                            }
                        }
                        ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="bulk-action-buttons"><?php
        echo "<button type='button' onclick='viewSelectedFiles()' class='btn view-btn'>View Selected</button> ";
        echo "<button type='submit' name='action' value='download' class='btn download-btn'>Download Selected</button> ";
        echo "<button type='button' id='mergeBtn' class='merge' onclick='mergePDFs()' disabled>Merge PDFs</button>&nbsp";
        echo "<button type='submit' name='action' value='delete' class='btn delete-btn'>Delete Selected</button>";
        echo "<button type='button' class='merge' id='mergedFileButton' onclick='viewMergedFile()' style='display:none;'>View Merged File</button>";
                        ?>
        </div>
</form>
    </div>
</div>
</div>
<script>
    const mainSelect = document.getElementById('main-select');
    const papersSubSelectDiv = document.getElementById('papers-sub-select-div');
    const bodiesSubSelectDiv = document.getElementById('bodies-sub-select-div');
    
    mainSelect.addEventListener('change', function() {
        if (mainSelect.value === 'Professional Bodies') {
            bodiesSubSelectDiv.style.display = 'block';
            papersSubSelectDiv.style.display = 'none';
        } else {
            papersSubSelectDiv.style.display = 'none';
            bodiesSubSelectDiv.style.display = 'none';
        }
    });

    function handleFileTypeChange(selectElement, rowId) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const selectedPath = selectedOption.getAttribute('data-path');
    const checkbox = document.querySelector(`input[type='checkbox'][data-id='${rowId}']`);

    if (checkbox) {
        checkbox.value = encodeURIComponent(selectedPath);
        checkbox.dataset.filepath = selectedPath;

        if (checkbox.checked) {
            selectedOrder = selectedOrder.filter(p => p !== checkbox.dataset.default);
            selectedOrder.push(selectedPath);
        }

        updateMergeButton();
    }
}

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

    function viewSelectedFiles() {
        const checkboxes = document.querySelectorAll("input[name='selected_files[]']:checked");
        if (checkboxes.length === 0) {
            alert("Please select at least one file to view.");
            return;
        }

        checkboxes.forEach(cb => {
            const filePath = cb.dataset.filepath;
            window.open(filePath, '_blank');
        });
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
            url = url;
            window.open(url, "_blank");
        } else {
            alert("No merged file found.");
        }
    }

    function showPhotoPreview(select) {
    const img = select.nextElementSibling;
    const value = select.value;
    if (value) {
        img.src = value;
        img.style.display = 'block';
    } else {
        img.style.display = 'none';
    }
}
</script>
</body>
</html>