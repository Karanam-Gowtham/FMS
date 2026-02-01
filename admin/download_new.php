<?php
include("../includes/connection.php");

$criteria = isset($_POST['criteria']) ? $_POST['criteria'] : '';
$subCriteria = isset($_POST['subCriteria']) ? $_POST['subCriteria'] : '';
$data = [];

if ($criteria && in_array($criteria, ["1", "2", "3", "4", "7"])) {
    $query = "SELECT id, faculty_name, academic_year, file_name, file_path, criteria_no 
              FROM files 
              WHERE criteria = ?
              ORDER BY criteria_no ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $criteria);
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
?>

<?php 
include("header_admin.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dynamic Dropdown with Table</title>
    <link rel="stylesheet" href="../css/downloads_new.css">
</head>
<body>

<div class="container">
    <h2>Select Criteria</h2>
    
    <form method="POST" action="">
        <!-- Main Criteria Dropdown -->
        <select name="criteria" id="criteria" onchange="showSubCriteria()" required>
            <option value="">Select the Criteria</option>
            <option value="1" <?= $criteria == '1' ? 'selected' : '' ?>>1</option>
            <option value="2" <?= $criteria == '2' ? 'selected' : '' ?>>2</option>
            <option value="3" <?= $criteria == '3' ? 'selected' : '' ?>>3</option>
            <option value="4" <?= $criteria == '4' ? 'selected' : '' ?>>4</option>
            <option value="5" <?= $criteria == '5' ? 'selected' : '' ?>>5</option>
            <option value="6" <?= $criteria == '6' ? 'selected' : '' ?>>6</option>
            <option value="7" <?= $criteria == '7' ? 'selected' : '' ?>>7</option>
        </select>

        <!-- Sub-Criteria Dropdown -->
        <select name="subCriteria" id="subCriteria" style="display:none;">
            <option value="">Select the Sub-Criteria</option>
            <?php if ($criteria == '5'): ?>
                <option value="5.1.1">5.1.1</option>
                <option value="5.1.2">5.1.2</option>
                <option value="5.1.3">5.1.3</option>
                <option value="5.1.3(A)">5.1.3(A)</option>
                <option value="5.1.3(B)">5.1.3(B)</option>
                <option value="5.1.3(C)">5.1.3(C)</option>
                <option value="5.1.4">5.1.4</option>
                <option value="5.1.5">5.1.5</option>
                <option value="5.2.1">5.2.1</option>
                <option value="5.2.2">5.2.2</option>
                <option value="5.2.3">5.2.3</option>
                <option value="5.3.1">5.3.1</option>
                <option value="5.3.2">5.3.2</option>
                <option value="5.3.3">5.3.3</option>
                <option value="5.4.1">5.4.1</option>
                <option value="5.4.2">5.4.2</option>
            <?php elseif ($criteria == '6'): ?>
                <option value="6.1.1(A)">6.1.1(A)</option>
                <option value="6.1.1(F)">6.1.1(F)</option>
                <option value="6.1.1(I)">6.1.1(I)</option>
                <option value="Others">Others</option>
            <?php endif; ?>
        </select>

        <button class="b1" type="submit">Submit</button>
    </form>
</div>

<!-- Table to Display Results -->
<?php if (!empty($data)): ?>
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
            <?php foreach ($data as $row): ?>
                <tr>
                    <td>
                        <input type="checkbox" name="selected_files[]" data-filepath="../<?= $row['file_path'] ?>" onchange="trackOrder(event)">
                    </td>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['faculty_name'] ?></td>
                    <td><?= $row['academic_year'] ?></td>
                    <td><?= $row['file_name'] ?></td>
                    <td><?= $row['criteria_no'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="bdiv">
    <button id="view" onclick="openFile()">View Selected File</button>
    <button id="mergeBtn" onclick="mergePDFs()" disabled>Merge PDFs</button>
    <button id="mergedFileButton" class="merge" onclick="viewMergedFile()" style="display:none;">View Merged File</button>
    </div>
<?php endif; ?>

<!-- JS for PDF merging -->
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
        const mergeBtn = document.getElementById('mergeBtn');
        mergeBtn.disabled = selectedOrder.length < 2;
    }

    function openFile() {
        let filePath = selectedOrder[0];
        if (!filePath) {
            alert("Please select a file to view.");
            return;
        }
        window.open(filePath, '_blank');
    }

    function toggleSelectAll(source) {
        const checkboxes = document.getElementsByName('selected_files[]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
            trackOrder({ target: checkbox });
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

            fetch("../save_merged_pdf.php", {
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
                url = "../" + url; // Add '../' to the URL
                window.open(url, "_blank");
            } else {
                alert("No merged file found.");
            }
        }

        </script>

</body>
</html>
