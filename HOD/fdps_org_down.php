<?php
include "../includes/connection.php";

session_start();

$branch = ""; // Initialize the variable
$records = []; // Initialize records array

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $branch = $_POST['branch'] ?? '';

    if (empty($branch)) {
        die("Please select a branch.");
    }

    // Query to fetch records based on the branch
    $stmt = $conn->prepare("SELECT * FROM fdps_org_tab WHERE branch = ?");
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
    $stmt = $conn->prepare("SELECT * FROM fdps_org_tab WHERE branch = ?");
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

include "./header_hod.php";
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
            overflow-x: auto; /* Enables horizontal scrolling */
            white-space: nowrap; /* Prevents table content from wrapping */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap;
        }

        table th {
            background-color: #4facfe;
            color: white;
        }

        .no-records {
            text-align: center;
            color: #555;
        }

        .btn-view, .btn-download {
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
                    <th>Username</th>
                    <th>Branch</th>
                    <th>Title</th>
                    <th>Organised By</th>
                    <th>Location</th>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Certificate</th>
                    <th>Brochure</th>
                    <th>FDP Schedule/Invitation</th>
                    <th>Attendance Forms</th>
                    <th>Feedback Forms</th>
                    <th>FDP Report</th>
                    <th>Photo 1</th>
                    <th>Photo 2</th>
                    <th>Photo 3</th>
                    <th>Submission Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['username']); ?></td>
                        <td><?php echo htmlspecialchars($record['branch']); ?></td>
                        <td><?php echo htmlspecialchars($record['title']); ?></td>
                        <td><?php echo htmlspecialchars($record['organised_by']); ?></td>
                        <td><?php echo htmlspecialchars($record['location']); ?></td>
                        <td><?php echo htmlspecialchars($record['date_from']); ?></td>
                        <td><?php echo htmlspecialchars($record['date_to']); ?></td>
                        <td>
                            <?php if (!empty($record['certificate'])) { 
                                $certificatePath = "../" . htmlspecialchars($record['certificate']);
                                ?>
                                <a href="<?php echo $certificatePath; ?>" target="_blank" class="btn btn-view">View</a><br>
                                <a href="<?php echo $certificatePath; ?>" download class="btn btn-download">Download</a>
                            <?php } else { ?>
                                No Certificate
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($record['brochure'])) { 
                                $brochurePath = "../" . htmlspecialchars($record['brochure']);
                                ?>
                                <a href="<?php echo $brochurePath; ?>" target="_blank" class="btn btn-view">View</a><br>
                                <a href="<?php echo $brochurePath; ?>" download class="btn btn-download">Download</a>
                            <?php } else { ?>
                                No Brochure
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($record['fdp_schedule_invitation'])) { 
                                $schedulePath = "../" . htmlspecialchars($record['fdp_schedule_invitation']);
                                ?>
                                <a href="<?php echo $schedulePath; ?>" target="_blank" class="btn btn-view">View</a><br>
                                <a href="<?php echo $schedulePath; ?>" download class="btn btn-download">Download</a>
                            <?php } else { ?>
                                No Schedule/Invitation
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($record['attendance_forms'])) { 
                                $attendancePath = "../" . htmlspecialchars($record['attendance_forms']);
                                ?>
                                <a href="<?php echo $attendancePath; ?>" target="_blank" class="btn btn-view">View</a><br>
                                <a href="<?php echo $attendancePath; ?>" download class="btn btn-download">Download</a>
                            <?php } else { ?>
                                No Attendance Forms
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($record['feedback_forms'])) { 
                                $feedbackPath = "../" . htmlspecialchars($record['feedback_forms']);
                                ?>
                                <a href="<?php echo $feedbackPath; ?>" target="_blank" class="btn btn-view">View</a><br>
                                <a href="<?php echo $feedbackPath; ?>" download class="btn btn-download">Download</a>
                            <?php } else { ?>
                                No Feedback Forms
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($record['fdp_report'])) { 
                                $reportPath = "../" . htmlspecialchars($record['fdp_report']);
                                ?>
                                <a href="<?php echo $reportPath; ?>" target="_blank" class="btn btn-view">View</a><br>
                                <a href="<?php echo $reportPath; ?>" download class="btn btn-download">Download</a>
                            <?php } else { ?>
                                No FDP Report
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($record['photo1'])) { 
                                $photo1Path = "../" . htmlspecialchars($record['photo1']);
                                ?>
                                <a href="<?php echo $photo1Path; ?>" target="_blank" class="btn btn-view">View</a><br>
                                <a href="<?php echo $photo1Path; ?>" download class="btn btn-download">Download</a>
                            <?php } else { ?>
                                No Photo 1
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($record['photo2'])) { 
                                $photo2Path = "../" . htmlspecialchars($record['photo2']);
                                ?>
                                <a href="<?php echo $photo2Path; ?>" target="_blank" class="btn btn-view">View</a><br>
                                <a href="<?php echo $photo2Path; ?>" download class="btn btn-download">Download</a>
                            <?php } else { ?>
                                No Photo 2
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($record['photo3'])) { 
                                $photo3Path = "../" . htmlspecialchars($record['photo3']);
                                ?>
                                <a href="<?php echo $photo3Path; ?>" target="_blank" class="btn btn-view">View</a><br>
                                <a href="<?php echo $photo3Path; ?>" download class="btn btn-download">Download</a>
                            <?php } else { ?>
                                No Photo 3
                            <?php } ?>
                        </td>
                        <td><?php echo htmlspecialchars($record['submission_time']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>


        <?php } else { ?>
        <p class="no-records">No records found for the selected branch.</p>
        <?php } ?>
    </div>
</body>
</html>
