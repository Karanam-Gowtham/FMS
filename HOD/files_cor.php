<?php
    include_once "../includes/connection.php";

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $academic_year = isset($_POST['year']) ? $_POST['year'] : '';
    $criteria = isset($_POST['criteria']) ? $_POST['criteria'] : '';
?>
<?php
// Handle Excel download
if (isset($_GET['download_excel'])) {

    $academic_year = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';
    $criteria = isset($_GET['criteria']) ? $_GET['criteria'] : '';

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=my_uploads.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Start output buffering
    $output = fopen("php://output", "w");

    // Write the table headers
    fputcsv($output, [
        "Username",
        "Faculty Name",
        "Academic Year",
        "Filename",
        "Uploaded At",
        "Criteria No"
    ], "\t");

    // Fetch the user's files
    $query = "SELECT * FROM a_files WHERE academic_year=? AND criteria = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $academic_year, $criteria);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result->num_rows > 0) {
        // Write the rows to the output
        while ($row = $result->fetch_assoc()) {
            $uploadedAt = new DateTime($row['uploaded_at']);
            $formattedDateTime = $uploadedAt->format('Y/m/d') . ' ' . $uploadedAt->format('H:i:s');
            fputcsv($output, [
                $row['username'],
                $row['Faculty_name'],
                $row['academic_year'],
                $row['file_name'],
                $formattedDateTime,
                $row['criteria']
            ], "\t");
        }
    } else {
        // Write a row to indicate no data
        fputcsv($output, ["No data available for the selected criteria."], "\t");
    }

    // Close the output stream
    fclose($output);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Uploads</title>
    <style>
        /* Your CSS styles remain unchanged */
        body {
            font-family: Arial, sans-serif;
            margin-top: -40px;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container11 {
            margin-top: 100px;
            margin: 0 auto;
            width: 90%;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
        }
        h1 {
            margin-top: 100px;
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 2em;
            font-weight: bold;
        }
        table {
            margin-top: 50px;
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
        button#view:hover {
            background-color: #218838;
        }
        button#down {
            background-color: #007bff;
            color: white;
        }
        button#down:hover {
            background-color: #0056b3;
        }
        button#del {
            background-color: #dc3545;
            color: white;
        }
        button#del:hover {
            background-color: #c82333;
        }
        .back-btn {
            display: inline-block;
            position:absolute;
            right:100px;
            top:100px;
            margin: 20px 0;
            padding: 10px 20px;
            background-color:rgb(130, 6, 115);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
        }
        .back-btn:hover {
            background-color:rgb(241, 115, 214);
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
        }
        .no-files {
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<?php
    include_once "header_hod.php";
?>
<body>
    <div class="container11">
        <h1>My Uploads</h1>
        <!-- Add the Download Excel button -->
        <form action="" method="GET">
            <input type="hidden" name="academic_year" value="<?php echo htmlspecialchars($academic_year); ?>">
            <input type="hidden" name="criteria" value="<?php echo htmlspecialchars($criteria); ?>">
            <button type="submit" name="download_excel" class="back-btn">Download Excel</button>
        </form>

        <table>
            <tr>
                <th>Id</th> <!-- Added column for numbering -->
                <th>Username</th>
                <th>Faculty Name</th>
                <th>Academic Year</th>
                <th>Filename</th>
                <th>Uploaded At</th>
                <th>Criteria No</th>
                <th>View</th>
                <th>Download</th>
            </tr>
            <?php
            
            // Fetch the user's files from the database, ordered by uploaded_at in descending order
            $query = "SELECT * FROM a_files WHERE academic_year=? AND criteria = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ss", $academic_year, $criteria);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $index = 1; // Initialize index for numbering
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $uploadedAt = new DateTime($row['uploaded_at']);
                    $formattedDateTime = $uploadedAt->format('Y/m/d') . ' & ' . $uploadedAt->format('H:i:s');

                    echo "<tr>";
                    echo "<td>" . $index++ . "</td>"; // Increment index for each row
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Faculty_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['academic_year']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['file_name']) . "</td>";
                    echo "<td>" . $formattedDateTime . "</td>";
                    echo "<td>" . htmlspecialchars($row['criteria']) . "</td>"; // Display criteria_no
                    echo "<td><a href='../admin/view_file.php?id=" . htmlspecialchars($row['id']) . "'><button class='btn1' id='view'>View</button></a></td>";
                    echo "<td><a href='" . htmlspecialchars('../admin/' . $row['file_path']) . "' download><button class='btn1' id='down'>Download</button></a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No files found</td></tr>"; // Updated colspan for new column
            }

            // Close the database connection
            $conn->close();
            ?>
        </table>
    </div>
</body>
</html>



