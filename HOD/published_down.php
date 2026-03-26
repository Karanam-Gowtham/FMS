<?php
include_once "../includes/connection.php";

session_start();



$branch = ""; // Initialize the variable
$records = []; // Initialize records array

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $branch = $_POST['branch'] ?? '';

    if (empty($branch)) {
        die("Please select a branch.");
    }

    // Query to fetch records based on the branch from the published_tab
    $stmt = $conn->prepare("SELECT * FROM published_tab WHERE branch = ? AND status = 'Accepted'");
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
    header("Content-Disposition: attachment; filename=published_tab_$branch.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Query to fetch records for the branch
    $stmt = $conn->prepare("SELECT * FROM published_tab WHERE branch = ? AND status = 'Accepted'");
    $stmt->bind_param("s", $branch);
    $stmt->execute();
    $result = $stmt->get_result();

    // Print column headers
    echo "Username\tBranch\tPaper Title\tJournal Name\tIndexing\tDate of Submission\tQuality Factor\tImpact Factor\tPayment\tSubmission Time\n";

    // Print data rows
    while ($row = $result->fetch_assoc()) {
        echo "{$row['username']}\t"
            . "{$row['branch']}\t"
            . "{$row['paper_title']}\t"
            . "{$row['journal_name']}\t"
            . "{$row['indexing']}\t"
            . "{$row['date_of_submission']}\t"
            . "{$row['quality_factor']}\t"
            . "{$row['impact_factor']}\t"
            . "{$row['payment']}\t"
            . "{$row['submission_time']}\n";
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
    <title>Branch & Publication Records</title>
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
            min-height: 100vh;
        }

        h1 {
            margin-top: 20px;
            margin-bottom: 50px;
            color: #fff;
        }

        .form-container {
            position: fixed;
            top: 170px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
        }

        .cont11 {
            margin-top: 100px;
            text-align: center;
        }

        .form-container select {
            width: 300px;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            background-color: #4facfe;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-container button:hover {
            background-color: #3583f6;
        }

        .table-container {
            margin-top: 200px;
            width: 100%;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #4facfe;
            color: white;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .no-records {
            text-align: center;
            color: #555;
        }

        .btn-view,
        .btn-download {
            background-color: rgb(194, 130, 217);
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            margin-right: 5px;
            transition: background-color 0.3s;
        }

        .btn-view:hover,
        .btn-download:hover {
            background-color: rgb(88, 21, 113);
        }

        .btn-download {
            background-color: rgb(9, 111, 28);
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            margin-right: 5px;
            transition: background-color 0.3s;
        }

        .btn-download:hover {
            background-color: rgb(134, 216, 131);
        }
    </style>
</head>

<body>
    <div class="cont11">
        <h1>Branch and Publication Records</h1>
        <div class="form-container">
            <form action="" method="POST">
                <label for="branch">Select Branch:</label><br>
                <select name="branch" id="branch" required>
                    <option value="">--Select Branch--</option>
                    <option value="AIDS">AIDS</option>
                    <option value="AIML">AIML</option>
                    <option value="CSE">CSE</option>
                    <option value="CIVIL">CIVIL</option>
                    <option value="MECH">MECH</option>
                    <option value="EEE">EEE</option>
                    <option value="ECE">ECE</option>
                    <option value="IT">IT</option>
                    <option value="BSH">BSH</option>
                </select><br>
                <button type="submit">Submit</button>
            </form>
        </div>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST') { ?>
            <div class="table-container">
                <h2>Patent Records for Branch: <?php echo htmlspecialchars($branch); ?></h2>
                <?php if (!empty($records)) { ?>
                    <form action="" method="POST">
                        <input type="hidden" name="branch" value="<?php echo htmlspecialchars($branch); ?>">
                        <button type="submit" name="download_excel" class="btn-download">Download Excel</button>
                    </form>
                    <table>
                        <thead>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $record) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($record['username']); ?></td>
                                    <td><?php echo htmlspecialchars($record['branch']); ?></td>
                                    <td><?php echo htmlspecialchars($record['paper_title']); ?></td>
                                    <td><?php echo htmlspecialchars($record['journal_name']); ?></td>
                                    <td><?php echo htmlspecialchars($record['indexing']); ?></td>
                                    <td><?php echo htmlspecialchars($record['date_of_submission']); ?></td>
                                    <td><?php echo htmlspecialchars($record['quality_factor']); ?></td>
                                    <td><?php echo htmlspecialchars($record['impact_factor']); ?></td>
                                    <td><?php echo htmlspecialchars($record['payment']); ?></td>
                                    <td><?php echo htmlspecialchars($record['submission_time']); ?></td>
                                    <td>
                                        <?php if (!empty($record['paper_file'])) {
                                            $patentFilePath = "../" . htmlspecialchars($record['paper_file']);
                                            ?>
                                            <a href="<?php echo $patentFilePath; ?>" target="_blank" class="btn btn-view">View</a><br>
                                            <a href="<?php echo $patentFilePath; ?>" download class="btn btn-download">Download</a>
                                        <?php } else { ?>
                                            No File
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p class="no-records">No records found for the selected branch.</p>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</body>

</html>
