<?php
include "../../includes/connection.php";
session_start();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['c_cord']) && !isset($_SESSION['a_username']) && !isset($_SESSION['hod'])) {
    die("Please login to access this page.");
}

$username = $_SESSION['c_cord'] ?? $_SESSION['a_username'] ?? $_SESSION['hod'];

$event = " ";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event'])) {
    $event = $_POST['event'];
} 

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['selected_files']) &&
    is_array($_POST['selected_files']) &&
    isset($_POST['action'])
) {
    $action = $_POST['action'];
    $files = $_POST['selected_files'];

    function getSafePathCentCord($fileStr) {
        $filename = basename(htmlspecialchars_decode(urldecode($fileStr), ENT_QUOTES));
        $dirs = ['../../uploads/', '../../uploads1/', '../uploads/', '../uploads1/', 'uploads/', 'uploads1/'];
        foreach ($dirs as $dir) {
            if (file_exists($dir . $filename) && is_file($dir . $filename)) {
                return $dir . $filename;
            }
        }
        return false;
    }

    if ($action === 'download') {
        if (count($files) === 1) {
            $safePath = getSafePathCentCord($files[0]);
            if ($safePath) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Content-Disposition: attachment; filename=\"" . basename($safePath) . "\"");
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($safePath));
                readfile($safePath);
                exit();
            } else {
                echo "File not found.";
            }
        } else {
            $zip = new ZipArchive();
            $zipName = "zip_file" . time() . ".zip";
            $zipPath = sys_get_temp_dir() . "/" . $zipName;

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                foreach ($files as $file) {
                    $safePath = getSafePathCentCord($file);
                    if ($safePath) {
                        $zip->addFile($safePath, basename($safePath));
                    }
                }
                $zip->close();
                header('Content-Type: application/zip');
                header("Content-Disposition: attachment; filename=\"$zipName\"");
                header('Content-Length: ' . filesize($zipPath));
                readfile($zipPath);
                unlink($zipPath);
                exit();
            } else {
                echo "Failed to create ZIP archive.";
            }
        }
    } elseif ($action === 'delete') {
        foreach ($files as $file) {
            $decoded = htmlspecialchars_decode(urldecode($file), ENT_QUOTES);
            $safePath = getSafePathCentCord($file);
            $stmt = $conn->prepare("DELETE FROM central_files WHERE file_path = ?");
            $stmt->bind_param("s", $decoded);

            $stmt->execute();
            if ($safePath) {
                unlink($safePath);
            }
        }
        echo "<script>
                alert('Selected files deleted.');
                window.location.href = 'c_down_files.php?event=" . urlencode($event) . "';
            </script>";
    }
}

// Get the selected file type from POST

include "../../includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Retrieve Files</title>
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js" integrity="sha256-D5pcrQeUHwgmWGyU4InYm5GMRuXBfPLVo8b2ZuO8aU8=" crossorigin="anonymous"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color: #fff;
            margin: 0;
            padding: 0;
        }
        header { position: sticky; }
        h1 {
            text-align: center;
            font-size: 2rem;
            font-weight: 600;
            margin-top: 00px;
            color: darkblue;
        }
        /* Navigation */
        .navbar { 
            margin-top: -80px;
            font-size: larger;
        }

        .sp{
            color:blue;
        }
        
        .nav-container {
            background-color: white;
            width:150vw;
             /* margin-top moved to .navbar */
            padding: 0 1rem;
        }

        .nav-items {
            margin-left: 30px;
            display: flex;
            align-items: center;
            justify-content:flex-start;
            height: 4rem;
        }

        .sid{
            color: rgb(48, 30, 138);
            font-weight: 500;
        }

        .main-a {
            color: rgb(138, 30, 113);
            font-weight: 500;
        }
        .main-a:hover{
            color:rgb(182, 64, 211);
        }

        .home-icon {
            color: rgb(30, 58, 138);
            transition: color 0.2s;
        }

        .home-icon:hover {
            color: rgb(29, 78, 216);
        }
        .container11, .container111 {
            margin-top: 50px;
            margin-left: 50px;
            width: 90%;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: #333;
        }
        
        .container111{
            margin-bottom: 50px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 2px solid #ddd;
        }
        th {
            background: #1e3c72;
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) { background: #f0f5ff; }
        tr:hover { background: #d6e4ff; transition: 0.3s; }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s ease-in-out;
            text-decoration: none;
            display: inline-block;
        }
        .view-btn { background: #42a5f5; color: white; margin-right: 10px; }
        .view-btn:hover { background: #1e88e5; }
        .download-btn { background: #66bb6a; color: white; }
        .download-btn:hover { background: #43a047; }
        .delete-btn { background: #e74c3c; color: white; }
        .delete-btn:hover { background: #c0392b; }
        #mergeBtn.active {
            color: white;
            font-weight: bold;
            background-color:rgb(59, 40, 167);
            cursor: pointer;
        }

        #mergeBtn:disabled {
            opacity: 0.6;
        }

        .merge {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color:rgb(131, 116, 214);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
        }
        .form1 {
                margin-top: 20px;
                padding: 20px;
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                color: #333;
                width: 50%;
                margin-left: auto;
                margin-right: auto;
                text-align: center;
            }

            .form1 label {
                font-weight: 600;
                font-size: 1.1rem;
                color: #1e3c72;
                margin-right: 10px;
            }

            select {
                padding: 8px 12px;
                border: 1px solid #ccc;
                border-radius: 6px;
                font-size: 1rem;
                outline: none;
                width: 60%;
                max-width: 300px;
            }

            select:focus {
                border-color: #2a5298;
                box-shadow: 0 0 5px rgba(42, 82, 152, 0.5);
            }

            .form1 button {
                margin-left: 15px;
                padding: 8px 20px;
                background-color: #1e3c72;
                color: white;
                font-weight: bold;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .form1 button:hover {
                background-color: #2a5298;
            }

    </style>
</head>

<body>

<div class="container11">
    <h1>Retrieve Files</h1>
    <div class="form1">
    <form method="POST">
        <label for="event">Choose Event:</label>
        <select name="event" id="event" required>
            <option value="" disabled selected>Select an Event</option>
            <option value="NCC" <?php if ($event === "NCC") { echo "selected"; } ?>>NCC</option>
            <option value="Sports" <?php if ($event === "Sports") { echo "selected"; } ?>>Sports</option>
            <option value="Clubs" <?php if ($event === "Clubs") { echo "selected"; } ?>>Clubs</option>
            <option value="NSS" <?php if ($event === "NSS") { echo "selected"; } ?>>NSS</option>
            <option value="Women Empowerment" <?php if ($event === "Women Empowerment") { echo "selected"; } ?>>Women Empowerment</option>
            <option value="IIC" <?php if ($event === "IIC") { echo "selected"; } ?>>IIC</option>
            <option value="PASH" <?php if ($event === "PASH") { echo "selected"; } ?>>PASH</option>
            <option value="Antiragging" <?php if ($event === "Antiragging") { echo "selected"; } ?>>Antiragging</option>
            <option value="SAC" <?php if ($event === "SAC") { echo "selected"; } ?>>SAC</option>
        </select>
        <button type="submit">Submit</button>
    </form>
    </div>
</div>


<div class="container111">
<?php
if (!empty($event)) {
    $sql = "SELECT * from central_files where event = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $event);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<form method='post' action=''>";
        echo "<table border='1'>
                <thead>
                    <tr>
                        <th><input type='checkbox' onclick='toggleSelectAll(this)'></th>
                        <th>Username</th>
                        <th>Academic Year</th>";
                        
        if ($event === 'Clubs') {
            echo "<th>Club Name</th>";
        }
    
        echo "      <th>Event Name</th>
                        <th>File Description</th>
                        <th>Photos</th> <!-- New column -->
                    </tr>
                </thead>
                <tbody>";
    
        $id = 1;
        while ($row = $result->fetch_assoc()) {
            $file_path = htmlspecialchars($row['file_path'], ENT_QUOTES);
            $photo1Path = htmlspecialchars($row["photo1"]);
            $photo2Path = htmlspecialchars($row["photo2"]);
            $photo3Path = htmlspecialchars($row["photo3"]);
            $photo4Path = htmlspecialchars($row["photo4"]);
        
            echo "<tr>
                    <td><input type='checkbox' name='selected_files[]' value='" . urlencode($file_path) . "' 
                        data-default='" . $file_path . "' data-id='" . $row["id"] . "' onclick='trackOrder(event)'></td>
                    <td>" . htmlspecialchars($row['uploaded_by']) . "</td>
                    <td>" . htmlspecialchars($row['acd_year']) . "</td>";
        
            if ($event === 'Clubs') {
                echo "<td>" . htmlspecialchars($row['club_name']) . "</td>";
            }
        
            echo "<td>" . htmlspecialchars($row['event_name']) . "</td>
                  <td>" . htmlspecialchars($row['file_name']) . "</td>
                  <td class='center-cell'>
                        <select class='file-select' onchange='handleFileTypeChange(this, " . $row["id"] . ")' data-id='" . $row["id"] . "'>
                            <option value=''>Select File</option>
                            <option value='file' data-path='" . $file_path . "'>file</option>
                            <option value='photo1' data-path='" . $photo1Path . "'>Photo 1</option>
                            <option value='photo2' data-path='" . $photo2Path . "'>Photo 2</option>
                            <option value='photo3' data-path='" . $photo3Path . "'>Photo 3</option>
                            <option value='photo4' data-path='" . $photo4Path . "'>Photo 4</option>
                        </select>
                  </td>
                </tr>";
        
            $id++;
        }

        echo "</tbody></table>";
        echo "<br>";
        echo "<input type='hidden' name='file_type' value='" . htmlspecialchars($event) . "'>";
        echo "<button type='button' onclick='viewSelectedFiles()' class='btn view-btn'>View Selected</button> ";
        echo "<button type='submit' name='action' value='download' class='btn download-btn'>Download Selected</button> ";
        echo "<button type='button' id='mergeBtn' class='merge' onclick='mergePDFs()' disabled>Merge PDFs</button>&nbsp";
        echo "<button type='submit' name='action' value='delete' class='btn delete-btn'>Delete Selected</button>";
        echo "<button type='button' class='merge' id='mergedFileButton' onclick='viewMergedFile()' style='display:none;'>View Merged File</button>";

        echo "</form>";
    } else {
        echo "<p class='no-files'>No files found for '" . htmlspecialchars($event) . "'.</p>";
    }

    $stmt->close();
} else {
    echo "<p class='no-files'>No file type selected.</p>";
}
    $conn->close();
    ?>
</div>

<script>

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
