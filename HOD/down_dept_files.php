<?php
include "../includes/connection.php";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the event and department from the previous page or from the form
$event = isset($_GET['event']) ? $_GET['event'] : null;
$department = isset($_POST['department']) ? $_POST['department'] : null;

// Check if event is set, if not, prompt user to go back
if (!$event) {
    die("Event not specified. Please go back and select an event.");
}


if (isset($_POST['download_excel'])) {
    if (isset($file_type)) {

    }
    $department = $_POST['branch'] ?? '';

    // Set headers for Excel file download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=dept_files_{$event}_{$department}.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Query to fetch filtered records
    $stmt = $conn->prepare("SELECT username, file_type, sub_file_type, file_name, file_path FROM dept_files WHERE file_type = ? AND dept = ? AND status = 'Accepted'");
    $stmt->bind_param("ss", $event, $department);
    $stmt->execute();
    $result = $stmt->get_result();

    // Print column headers
    echo "Username\tFile Type\tSub File Type\tFile Name\n";

    // Print data rows
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "{$row['username']}\t"
                . "{$row['file_type']}\t"
                . "{$row['sub_file_type']}\t"
                . "{$row['file_name']}\n";
        }
    } else {
        // In case of no records
        echo "No records found for the selected filters.\n";
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
    <title>Retrieve Files</title>
    <style>
        /* Base Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        .container11 {
            margin-top: 100px;
            margin-bottom: 70px;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            width: 80vw;
            height: 100%;
        }

        h1 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #fff;
        }

        h2 {
            margin-top: 20px;
            font-size: 1.8rem;
            text-align: center;
            color: #ffdf6c;
        }

        /* Table Styles */
        .styled-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            text-align: left;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }

        .styled-table thead tr {
            background: rgb(50, 29, 90);
            color: white;
            font-weight: bold;
        }

        .styled-table th,
        .styled-table td {
            padding: 15px;
            text-align: center;
        }

        .styled-table tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .styled-table tbody tr:nth-of-type(even) {
            background: rgba(255, 255, 255, 0.1);
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid rgb(93, 35, 147);
        }

        /* Button Styles */
        .btn {
            display: inline-block;
            padding: 8px 12px;
            font-size: 1rem;
            text-decoration: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }

        .view-btn {
            background: #42a5f5;
        }

        .view-btn:hover {
            background: #1e88e5;
        }

        .download-btn {
            background: #66bb6a;
        }

        .download-btn:hover {
            background: #43a047;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            h2 {
                font-size: 1.5rem;
            }
        }

        .form-container {
            margin-top: 20px;
            background: linear-gradient(135deg, rgb(56, 173, 209), rgb(16, 78, 125));
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 500px;
            margin-left: 350px;
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
            background-color: rgb(67, 188, 103);
            color: white;
        }

        .form-container button:hover {
            background-color: rgb(40, 156, 98);
        }

        .btn-download {
            width: 300px;
            margin-top: 10px;
            margin-left: 28vw;
            margin-bottom: 15px;
            font-size: 16px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            background-color: rgb(88, 7, 74);
            color: white;

        }

        .btn-download:hover {
            background-color: rgb(204, 84, 200);
        }
    </style>
</head>

<body>
    <div class="container11">
        <h1>Retrieve Department Files</h1>
        <div class="form-container">
            <form method="POST" action="">
                <label for="department">Select Department:</label>
                <select name="department" id="department">
                    <option value="" disabled selected>--Select Department--</option>
                    <option value="AIDS" <?php if ($department == 'AIDS')
                        echo 'selected'; ?>>AIDS</option>
                    <option value="AIML" <?php if ($department == 'AIML')
                        echo 'selected'; ?>>AIML</option>
                    <option value="CSE" <?php if ($department == 'CSE')
                        echo 'selected'; ?>>CSE</option>
                    <option value="CIVIL" <?php if ($department == 'CIVIL')
                        echo 'selected'; ?>>CIVIL</option>
                    <option value="MECH" <?php if ($department == 'MECH')
                        echo 'selected'; ?>>MECH</option>
                    <option value="EEE" <?php if ($department == 'EEE')
                        echo 'selected'; ?>>EEE</option>
                    <option value="ECE" <?php if ($department == 'ECE')
                        echo 'selected'; ?>>ECE</option>
                    <option value="IT" <?php if ($department == 'IT')
                        echo 'selected'; ?>>IT</option>
                    <option value="BSH" <?php if ($department == 'BSH')
                        echo 'selected'; ?>>BSH</option>
                </select><br>
                <button type="submit">Filter</button>
            </form>
        </div>

        <?php
        if ($department) {
            // Prepare the SQL query to fetch records filtered by event and department
            $sql = "SELECT username, file_type, sub_file_type, file_name, file_path FROM dept_files WHERE file_type = ? AND dept = ? AND status = 'Accepted'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $event, $department);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Start HTML output
                ?>
                <form action="" method="POST">
                    <input type="hidden" name="branch" value="<?php echo htmlspecialchars($department); ?>">
                    <button type="submit" name="download_excel" class="btn-download">Download Excel</button>
                </form>
                <?php
                // Display records
                echo "<h2>" . ucfirst(str_replace("_", " ", $event)) . " Files for " . ucfirst($department) . "</h2>";
                echo "<table class='styled-table'>
                            <thead>
                                <tr>
                                    <th>User Name</th>
                                    <th>File Type</th>
                                    <th>File Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>";

                // Iterate through the results and display them in the table
                while ($row = $result->fetch_assoc()) {
                    $filePath = "../" . htmlspecialchars($row['file_path']);
                    echo "<tr>
                                <td>" . htmlspecialchars($row['username']) . "</td>
                                <td>" . htmlspecialchars($row['file_type']) . " (" . htmlspecialchars($row['sub_file_type']) . ")</td>
                                <td>" . htmlspecialchars($row['file_name']) . "</td>
                                <td>
                                    <a href='$filePath' target='_blank' class='btn view-btn'>View</a>
                                    <a href='$filePath' download class='btn download-btn'>Download</a>
                                </td>
                            </tr>";
                }

                echo "</tbody>" ?>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['username']) . "</td>
                            <td>" . htmlspecialchars($row['sub_file_type']) . "</td>
                            <td>" . htmlspecialchars($row['file_name']) . "</td>
                            <td>
                                <a href='../" . htmlspecialchars($row['file_path']) . "' target='_blank' class='btn view-btn'>View</a>
                                <a href='../" . htmlspecialchars($row['file_path']) . "' download class='btn download-btn'>Download</a>
                            </td>
                        </tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>No records found for the selected event and department.</p>";
            }

            $stmt->close();
        }
        ?>

    </div>
</body>

</html>

<?php
$conn->close();
?>