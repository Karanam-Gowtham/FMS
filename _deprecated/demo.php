<?php
include "connection.php";
define('SQL_FILE_PATH', "SELECT file_path FROM files WHERE id = ?");

if (!isset($_SESSION['username'])) {
    die("You need to log in to view your uploads.");
}

$username = $_SESSION['username'];

// Handle file actions
if (isset($_POST['action']) && isset($_POST['selected_files'])) {
    $selectedFiles = $_POST['selected_files'];
    $action = $_POST['action'];
    
    if ($action == 'delete') {
        foreach ($selectedFiles as $fileId) {
            $sql = SQL_FILE_PATH;
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
    
    
    } elseif ($action == 'download') {
            $fileId = $selectedFiles[0];
            $sql = "SELECT file_path FROM files WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();
            if ($file) {
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename=" . basename($file['file_path']));
                header("Content-Length: " . filesize($file['file_path']));
                readfile($file['file_path']);
                exit;
            }
    } elseif ($action == 'view') {
        foreach ($selectedFiles as $fileId) {
            $sql = SQL_FILE_PATH;
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();
            if ($file) {
                echo "<script>window.open('" . $file['file_path'] . "', '_blank');</script>";
            }
        }
    }
    
    
}


if (isset($_GET['download_excel'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=my_uploads.xls");
    $output = fopen("php://output", "w");
    fputcsv($output, ["Username", "Faculty Name", "Academic Year", "Branch", "Semester", "Section", "Filename", "Uploaded At", "Criteria No"], "\t");
    $sql = "SELECT * FROM files WHERE username = ? ORDER BY uploaded_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $uploadedAt = new DateTime($row['uploaded_at']);
        $formattedDateTime = $uploadedAt->format('Y/m/d') . ' ' . $uploadedAt->format('H:i:s');
        fputcsv($output, [$username, $row['faculty_name'], $row['academic_year'], $row['branch'], $row['sem'], $row['section'], $row['file_name'], $formattedDateTime, $row['criteria_no']], "\t");
    }
    fclose($output);
    exit;
}
?>
<?php
        include "header.php";
        ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Uploads</title>
    <style>
    body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .cont {
            width: 100%;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .container11{
            margin-top:100px;
            width:90%;
            margin-left:50px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 2em;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: #fff;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        button {
            border: none;
            border-radius: 5px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            text-transform: uppercase;
            font-weight: bold;
        }
        button#view {
            background-color: #28a745;
            color: white;
        }
        button#down {
            background-color: #007bff;
            color: white;
        }
        button#del {
            background-color: #dc3545;
            color: white;
        }
        .merge{
            margin-top: 10px;
            background-color: #28a745;
        }
        .excel-btn {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
        }
        .excel-btn:hover {
            background-color: #218838;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>
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
                document.getElementById('mergeBtn').disabled = selectedOrder.length < 2;
            }

            // Function to view selected file
            function openFile() {
                let checkboxes = document.querySelectorAll("input[name='selected_files[]']:checked");
                if (checkboxes.length === 0) {
                    alert("Please select a file to view.");
                    return;
                }
                let filePath = checkboxes[0].dataset.filepath;
                window.open(filePath, '_blank');
            }

            // Select all checkboxes
            function toggleSelectAll(source) {
                let checkboxes = document.getElementsByName('selected_files[]');
                for (let checkbox of checkboxes) {
                    checkbox.checked = source.checked;
                    trackOrder({ target: checkbox });
                }
            }



        function updateMergeButton() {
            document.getElementById('mergeBtn').disabled = selectedOrder.length < 2;
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
                    console.log("Fetching file:", fileUrl);
                    const response = await fetch(fileUrl);
                    if (!response.ok) {
                        throw new Error(`Failed to fetch ${fileUrl}`);
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
            const url = document.getElementById("mergedFileButton").getAttribute("data-url");
            if (url) {
                window.open(url, "_blank");
            } else {
                alert("No merged file found.");
            }
        }

        
    </script>
</head>
<body>
<div class="cont">
    <div class="container11">
    <div >
        <a href="my_uploads.php?download_excel=1" class="excel-btn">Download Excel</a>
        <h1>My Uploads</h1>
        <form method="POST" action="">
            <table>
                <tr>
                    <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
                    <th>Username</th>
                    <th>Faculty Name</th>
                    <th>Academic Year</th>
                    <th>Branch</th>
                    <th>Semester</th>
                    <th>Section</th>
                    <th>Filename</th>
                    <th>Uploaded At</th>
                    <th>Criteria No</th>
                </tr>
                <?php
                $sql = "SELECT * FROM files WHERE username = ? ORDER BY uploaded_at DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $fileUrl =  $row['file_path'];
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='selected_files[]' value='" . $row['id'] . "' 
                    data-filepath='" . htmlspecialchars($fileUrl, ENT_QUOTES, 'UTF-8') . "' 
                    onchange='trackOrder(event)'></td>";


                    echo "<td>" . htmlspecialchars($row['faculty_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['academic_year']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['branch']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['sem']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['section']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['file_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['uploaded_at']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['criteria_no']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
            <div>
            <button type="submit"  id="view" name="action" onclick="openFile()">view</button>
                <button type="submit" id="down" name="action" value="download">Download</button>
                <button type="button" id="mergeBtn" class="merg" onclick="mergePDFs()" disabled>Merge PDFs</button>
                <button type="submit" id="del"  name="action" value="delete">Delete</button>
                <button type="button" class="merge" id="mergedFileButton" onclick="viewMergedFile()" style="display:none;">View Merged File</button>
                
            </div>
        </form>
    </div>
    </div>
</body>
</html>
