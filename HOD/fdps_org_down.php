<?php
include_once "../includes/connection.php";
session_start();

function fixPath($p) {
    if (empty($p)) {
        return "";
    }
    $p = htmlspecialchars_decode($p);
    if (preg_match('/uploads[\/\\\\].*/', $p, $matches)) {
        return "../" . $matches[0];
    }
    return $p;
}

$branch = ""; // Initialize the variable
$records = []; // Initialize records array

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $branch = $_POST['branch'] ?? '';

    if (empty($branch)) {
        die("Please select a branch.");
    }

    // Query to fetch records based on the branch
    $stmt = $conn->prepare("SELECT * FROM fdps_org_tab WHERE branch = ? AND status = 'Accepted'");
    $stmt->bind_param("s", $branch);
    $stmt->execute();
    $result = $stmt->get_result();
    $records = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
}

// Handle Excel Download
if (isset($_POST['download_excel'])) {
    $branch = $_POST['branch'] ?? '';

    // Set headers for Excel file download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=fdp_records_$branch.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Query to fetch records for the branch
    $stmt = $conn->prepare("SELECT * FROM fdps_org_tab WHERE branch = ? AND status = 'Accepted'");
    $stmt->bind_param("s", $branch);
    $stmt->execute();
    $result = $stmt->get_result();

    // Print column headers
    echo "Username\tBranch\tTitle\tOrganised By\tLocation\tDate From\tDate To\tSubmission Time\n";

    // Print data rows
    while ($row = $result->fetch_assoc()) {
        echo "{$row['username']}\t{$row['branch']}\t{$row['title']}\t{$row['organised_by']}\t{$row['location']}\t{$row['date_from']}\t{$row['date_to']}\t{$row['submission_time']}\n";
    }

    $stmt->close();
    $conn->close();
    exit;
}

include_once "./header_hod.php";
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch & Achievement Selector</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 1200px;
            overflow-x: hidden;
        }

        h1 {
            margin-top: 100px;
            color: #fff;
        }

        .form-container {
            margin-top: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .form-container select,
        .form-container button {
            width: 300px;
            margin-bottom: 15px;
            font-size: 16px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button {
            background-color: #4facfe;
            color: white;
        }

        .form-container button:hover {
            background-color: #3583f6;
        }

        .table-container {
            text-align: center;
            margin-top: 30px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 95%;
            overflow-x: auto;
            white-space: nowrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap;
        }

        table th {
            background-color: #4facfe;
            color: white;
        }

        .btn-view,
        .btn-download {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            margin-right: 5px;
            color: white;
            transition: background-color 0.3s;
        }

        .btn-view {
            background-color: rgb(194, 130, 217);
        }

        .btn-download {
            background-color: rgb(9, 111, 28);
        }

        .btn-view:hover {
            background-color: rgb(88, 21, 113);
        }

        .btn-download:hover {
            background-color: rgb(134, 216, 131);
        }
    </style>
</head>

<body>
    <h1>Branch and Achievements Selector for FDPs Organised</h1>
    <div class="form-container">
        <form action="" method="POST">
            <label for="branch">Select Branch:</label><br>
            <select name="branch" id="branch" required>
                <option value="">--Select Branch--</option>
                <option value="AIDS" <?php if ($branch == 'AIDS') { echo 'selected'; } ?>>AIDS</option>
                <option value="AIML" <?php if ($branch == 'AIML') { echo 'selected'; } ?>>AIML</option>
                <option value="CSE" <?php if ($branch == 'CSE') { echo 'selected'; } ?>>CSE</option>
                <option value="CIVIL" <?php if ($branch == 'CIVIL') { echo 'selected'; } ?>>CIVIL</option>
                <option value="MECH" <?php if ($branch == 'MECH') { echo 'selected'; } ?>>MECH</option>
                <option value="EEE" <?php if ($branch == 'EEE') { echo 'selected'; } ?>>EEE</option>
                <option value="ECE" <?php if ($branch == 'ECE') { echo 'selected'; } ?>>ECE</option>
                <option value="IT" <?php if ($branch == 'IT') { echo 'selected'; } ?>>IT</option>
                <option value="BSH" <?php if ($branch == 'BSH') { echo 'selected'; } ?>>BSH</option>
            </select><br>
            <button type="submit">Submit</button>
        </form>
    </div>

    <div class="table-container">
        <h2>FDP Records for Branch: <?php echo htmlspecialchars($branch); ?></h2>
        <?php if (!empty($records)) { ?>
            <form action="" method="POST">
                <input type="hidden" name="branch" value="<?php echo htmlspecialchars($branch); ?>">
                <button type="submit" name="download_excel" class="btn-download">Download Excel</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th scope="col">Username</th>
                        <th scope="col">Branch</th>
                        <th scope="col">Title</th>
                        <th scope="col">Organised By</th>
                        <th scope="col">Location</th>
                        <th scope="col">Date From</th>
                        <th scope="col">To Date</th>
                        <th scope="col">Submission Time</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record) {
                        $files_to_merge = [
                            fixPath($record['brochure']),
                            fixPath($record['fdp_schedule_invitation']),
                            fixPath($record['attendance_forms']),
                            fixPath($record['feedback_forms']),
                            fixPath($record['fdp_report']),
                            fixPath($record['photo1']),
                            fixPath($record['photo2']),
                            fixPath($record['photo3']),
                            fixPath($record['certificate'])
                        ];
                        $files_to_merge = array_filter($files_to_merge, function ($f) {
                            return strlen($f) > 3;
                        });
                        $files_json = htmlspecialchars(json_encode(array_values($files_to_merge)), ENT_QUOTES, 'UTF-8');
                        $record_title = htmlspecialchars($record['title'], ENT_QUOTES, 'UTF-8');
                        
                        $merged_path = fixPath($record['merged_file']);
                        $has_merged = (!empty($merged_path) && file_exists($merged_path));
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['username']); ?></td>
                            <td><?php echo htmlspecialchars($record['branch']); ?></td>
                            <td><?php echo htmlspecialchars($record['title']); ?></td>
                            <td><?php echo htmlspecialchars($record['organised_by']); ?></td>
                            <td><?php echo htmlspecialchars($record['location']); ?></td>
                            <td><?php echo htmlspecialchars($record['date_from']); ?></td>
                            <td><?php echo htmlspecialchars($record['date_to']); ?></td>
                            <td><?php echo htmlspecialchars($record['submission_time']); ?></td>
                            <td>
                                <?php if ($has_merged): ?>
                                    <a href="view_file_hod.php?file_path=<?php echo urlencode($merged_path); ?>" target="_blank"
                                        class="btn btn-view">View</a>
                                    <a href="<?php echo htmlspecialchars($merged_path); ?>" download
                                        class="btn btn-download">Download</a>
                                <?php else: ?>
                                    <button type="button" class="btn" style="background-color: #ff6347; color: white;"
                                        onclick='mergeRecordFiles("<?php echo $files_json; ?>", "<?php echo $record_title; ?>")'>Get
                                        Files</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="no-records">No records found for the selected branch.</p>
        <?php } ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"
        integrity="sha256-D5pcrQeUHwgmWGyU4InYm5GMRuXBfPLVo8b2ZuO8aU8="
        crossorigin="anonymous"></script>
    <script>
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
                        continue;
                    }
                    const fileArrayBuffer = await response.arrayBuffer();
                    const ext = fileUrl.split('.').pop().toLowerCase();

                    if (ext === 'pdf') {
                        const pdf = await PDFDocument.load(fileArrayBuffer);
                        const pages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                        pages.forEach(p => mergedPdf.addPage(p));
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
                } catch (e) { console.error(e); }
            }

            if (addedPages === 0) {
                alert("Could not merge any files. Ensure they are valid PDFs or Images.");
                return;
            }

            const mergedPdfBytes = await mergedPdf.save();
            const blob = new Blob([mergedPdfBytes], { type: 'application/pdf' });
            const url = URL.createObjectURL(blob);
            window.open(url, '_blank');
        }
    </script>
</body>
</html>