<?php
include "../connection.php";

session_start();

$branch = ""; // Initialize the variable
$records = []; // Initialize records array

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['download_excel'])) {
    $branch = $_POST['branch'] ?? '';

    if (empty($branch)) {
        die("Please select a branch.");
    }

    // Query to fetch records based on the branch
    $stmt = $conn->prepare("SELECT * FROM fdps_tab WHERE branch = ?");
    $stmt->bind_param("s", $branch);
    $stmt->execute();
    $result = $stmt->get_result();
    $records = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
}
// Handle Excel Download
if (isset($_POST['download_excel'])) {
    $branch = $_POST['branch'] ?? '';

    // Open a new database connection
    include "../connection.php";

    // Set headers for Excel file download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=fdp_records_$branch.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Query to fetch records for the branch
    $stmt = $conn->prepare("SELECT * FROM fdps_tab WHERE branch = ?");
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
            min-height: 100vh;
        }

        h1 {
            margin-top: 20px;
            margin-bottom:50px;
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
        .cont11{
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

        table th, table td {
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

        .btn-view, .btn-download {
            background-color:rgb(194, 130, 217);
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            margin-right: 5px;
            transition: background-color 0.3s;
        }   

        .btn-view:hover, .btn-download:hover {
            background-color: rgb(88, 21, 113);
        }
        .btn-download {
            background-color:rgb(9, 111, 28);
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
        <h1>Branch and Achievements Selector for FDPs</h1>
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
                            <td><?php echo htmlspecialchars($record['submission_time']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else{ ?>
            <p class="no-records">No records found for the selected branch.</p>
        <?php } ?>
        </div>
        
    </div>
</body>
</html>


