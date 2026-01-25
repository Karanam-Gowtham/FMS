<?php
include("connection.php");
ob_start();

session_start();


if (!isset($_SESSION['username'])) {
    die("You need to log in to view your uploads.");
}

$username = $_SESSION['username'];

$criteria = isset($_POST['criteria']) ? $_POST['criteria'] : '';
$subCriteria = isset($_POST['subCriteria']) ? $_POST['subCriteria'] : '';
$data = [];

if ($criteria && in_array($criteria, ["1", "2", "3", "4", "5", "6", "7"])) {
    $query = "SELECT id, faculty_name, academic_year, file_name, file_path, criteria_no 
              FROM files 
              WHERE criteria = ? and UserName = ?
              ORDER BY criteria_no ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $criteria, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $counter = 1;
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $counter++,
            'faculty_name' => $row['faculty_name'],
            'academic_year' => $row['academic_year'],
            'file_name' => $row['file_name'],
            'file_path' => $row['file_path'],
            'criteria_no' => $row['criteria_no']
        ];
    }
}




//----------------- for delete and download-------------------------------------------------------
if (isset($_POST['action']) && isset($_POST['selected_files'])) {
    $selectedFiles = $_POST['selected_files'];
    $action = $_POST['action'];
    
    if ($action == 'delete') {
        foreach ($selectedFiles as $fileId) {
            $sql = "SELECT file_path FROM files WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();
            if ($file) {
                unlink($file['file_path']); // Delete the actual file
                $sql = "DELETE FROM files WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $fileId);
                $stmt->execute();
            }
        }
        echo "<script>alert('Files deleted successfully.'); window.location.href='my_uploads.php';</script>";
    
    
    }else if ($action == 'download') {
        if (!empty($selectedFiles)) {
            $zip = new ZipArchive();
            $zipFileName = "downloads.zip";
            $zipFilePath = "uploads/" . $zipFileName;
    
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                // Reverse the order of selected file IDs
                $selectedFiles = array_reverse($selectedFiles);
    
                // Convert selected file IDs to placeholders for SQL
                $placeholders = implode(',', array_fill(0, count($selectedFiles), '?'));
                $sql = "SELECT file_path, file_name FROM files WHERE id IN ($placeholders)";
    
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(str_repeat("i", count($selectedFiles)), ...$selectedFiles);
                $stmt->execute();
                $result = $stmt->get_result();
    
                // Store files in an array
                $files = [];
                while ($file = $result->fetch_assoc()) {
                    $files[] = $file;
                }
    
                // Maintain the reversed order while adding files to the zip
                foreach ($files as $file) {
                    $filePath = $file['file_path'];
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, basename($filePath)); // Adds files in reversed selection order
                    }
                }
                $zip->close();
    
                // Set headers for ZIP download
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
                header('Content-Length: ' . filesize($zipFilePath));
                readfile($zipFilePath);
    
                // Clean up
                unlink($zipFilePath);
                exit;
            } else {
                echo "Failed to create ZIP file.";
            }
        }
    }
    
}
    
    
    
//---------------------completed-----------------------
?>

<?php 
include("header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dynamic Dropdown with Table</title>
    <link rel="stylesheet" href="./css/my_uploads_1.css">
    <script>
        function showSubCriteria() {
            let criteria = document.getElementById("criteria").value;
            let subCriteria = document.getElementById("subCriteria");
            subCriteria.style.display = "none";
            subCriteria.innerHTML = "<option value=''>Select the Sub-Criteria</option>";

            if (criteria === "5") {
                subCriteria.style.display = "block";
                let options = ["5.1.1", "5.1.2", "5.1.3", "5.1.3(A)", "5.1.3(B)", "5.1.3(C)", "5.1.4", "5.1.5", "5.2.1", "5.2.2", "5.2.3", "5.3.1", "5.3.2", "5.3.3", "5.4.1", "5.4.2"];
                options.forEach(opt => {
                    let option = document.createElement("option");
                    option.value = opt;
                    option.textContent = opt;
                    subCriteria.appendChild(option);
                });
            } else if (criteria === "6") {
                subCriteria.style.display = "block";
                let options = ["6.1.1(A)", "6.1.1(F)", "6.1.1(I)", "Others"];
                options.forEach(opt => {
                    let option = document.createElement("option");
                    option.value = opt;
                    option.textContent = opt;
                    subCriteria.appendChild(option);
                });
            }
        }
    </script>
</head>
<body>

<div class="container11">
<div class="header-section">
    <h1>My Uploads</h1>
    <form method="POST">
        <button type="submit" name="download_excel" class="download-excel">Download Excel</button>
    </form>
</div>

<div class="container111">
    <h2>Select Criteria</h2>
    <form method="POST" action="">
        <select name="criteria" id="criteria" onchange="showSubCriteria()" required>
            <option value="">Select the Criteria</option>
            <option value="1" <?= $criteria == '1' ? 'selected' : '' ?>>1</option>
            <option value="2" <?= $criteria == '2' ? 'selected' : '' ?>>2</option>
            <option value="3" <?= $criteria == '3' ? 'selected' : '' ?>>3</option>
            <option value="4" <?= $criteria == '4' ? 'selected' : '' ?>>4</option>
            <option value="5" <?= $criteria == '5' ? 'selected' : '' ?>>5</option>
            <option value="6" <?= $criteria == '6' ? 'selected' : '' ?>>6</option>
            <option value="7" <?= $criteria == '7' ? 'selected' : '' ?>>7</option>
        </select><br>

        <select name="subCriteria" id="subCriteria" style="display:none;"></select><br>
        <button class="b1" type="submit">Submit</button>
    </form>
</div>


<form method="POST" action="">
<table id="resultTable">
    <thead>
        <tr>
            <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
            <th>ID</th>
            <th>Faculty Name</th>
            <th>Academic Year</th>
            <th>File Name</th>
            <th>Criteria No</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($data)): ?>
        <td colspan='6'>No data</td>
    <?php else: ?>
        <?php foreach ($data as $row): ?>
            <tr>
                <td><input type="checkbox" name="selected_files[]" data-filepath="<?= $row['file_path'] ?>" onchange="trackOrder(event)"></td>
                <td><?= $row['id'] ?></td>
                <td><?= $row['faculty_name'] ?></td>
                <td><?= $row['academic_year'] ?></td>
                <td><?= $row['file_name'] ?></td>
                <td><?= $row['criteria_no'] ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<div class="bdiv">
    <button id="view" onclick="openFile()">View Selected File</button>
    <button type="submit" id="down" name="action" value="download">Download</button>
    <button type="submit" id="del"  name="action" value="delete">Delete</button>
</div>
</div>
        </form>




<!-- JS for PDF merging -->
<script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>
<script>
let selectedOrder = [];
        
        function toggleSelectAll(source) {
            let checkboxes = document.querySelectorAll("tbody input[type='checkbox']");
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
        }
        function trackOrder(event) {
            let checkbox = event.target;
            let filePath = checkbox.getAttribute("data-filepath");

            if (checkbox.checked) {
                selectedOrder.push(filePath);
            } else {
                selectedOrder = selectedOrder.filter(file => file !== filePath);
            }
        }

        function openFile() {
            if (selectedOrder.length === 0) {
                alert("Please select a file to view.");
                return;
            }
            
            let filePath = selectedOrder[0]; // Open the first selected file
            window.open(filePath, "_blank");
        }

        </script>

</body>
</html>
