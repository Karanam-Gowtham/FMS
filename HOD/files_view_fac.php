<?php
include "../includes/connection.php";

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Set default values for filtering
$academic_year = isset($_POST['academic_year']) ? $_POST['academic_year'] : '';
$criteria = isset($_POST['criteria']) ? $_POST['criteria'] : '';
$criteria_no = isset($_POST['criteria_no']) ? $_POST['criteria_no'] : '';

$show_section = ($criteria_no == '6.1.1(A)') ? true : false;
$show_ext_or_Int = ($criteria_no == '6.1.1(I)') ? true : false; //ext or int
$show_semester = ($criteria_no == '6.1.1(A)') ? true : false; // Show semester-wise for 6.1.1(A)
$show_branch_dropdown = ($criteria_no == '6.1.1(A)' || $criteria_no == '6.1.1(F)' || $criteria_no == '6.1.1(I)' ) ? true : false; // Display branch dropdown for specific criteria_no
if (isset($_POST['download_excel'])) {
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="downloaded_files.xls"');
    header('Cache-Control: max-age=0');
    
    // Output Excel file header
    echo "Username\tFaculty Name\tAcademic Year\t";
    if ($show_branch_dropdown) {
        echo "Branch\t";
    }
    if ($show_semester) {
        echo "Semester\t";
    }
    if ($show_section) {
        echo "Section\t";
    }
    echo "Filename\tUploaded At\n";

    // Fetch data based on filters
    if ($show_branch_dropdown && isset($_POST['branch'])) {
        $branch = $_POST['branch'];
        if ($show_semester) {
            if ($branch == 'BSH') {
                $semesters = range(1, 2);
            } else {
                $semesters = range(3, 8);
            }
            foreach ($semesters as $sem) {
                $sql = "SELECT * FROM files WHERE academic_year = ? AND branch = ? AND sem = ? AND criteria = ? AND criteria_no = ? ORDER BY uploaded_at DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssiss', $academic_year, $branch, $sem, $criteria, $criteria_no);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    outputExcelRow($row, $show_section, $show_semester, true);
                }
            }
        } else {
            $sql = "SELECT * FROM files WHERE academic_year = ? AND branch = ? AND criteria = ? AND criteria_no = ? ORDER BY uploaded_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $academic_year, $branch, $criteria, $criteria_no);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                outputExcelRow($row, $show_section, $show_semester, true);
            }
        }
    } else {
        $sql = "SELECT * FROM files WHERE academic_year = ? AND criteria = ? AND criteria_no = ? ORDER BY uploaded_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $academic_year, $criteria, $criteria_no);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            outputExcelRow($row, $show_section, false, false);
        }
    }
    exit();
}

include "header_hod.php";
function outputExcelRow($row, $show_section, $show_semester, $show_branch) {
    echo $row['UserName'] . "\t";
    echo $row['faculty_name'] . "\t";
    echo $row['academic_year'] . "\t";
    if ($show_branch) {
        echo $row['branch'] . "\t";
    }
    if ($show_semester) {
        echo $row['sem'] . "\t";
    }
    if ($show_section) {
        echo $row['section'] . "\t";
    }
    echo $row['file_name'] . "\t";
    $uploadedAt = new DateTime($row['uploaded_at']);
    echo $uploadedAt->format('d/m/Y H:i:s') . "\n";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Files</title>
    <link rel="stylesheet" href="../css/download_hod1.css">
    <style>
        .download-excel {
            background-color:rgb(86, 222, 147);
            position: absolute;
            color: white;
            width:200px;
            top:150px;
            right:200px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 10px 0;
        }
        .download-excel:hover {
            background-color: #1a5c38;
        }
    </style>
</head>
<body>

    <h1>Download Files</h1>

    <!-- Dropdown Form -->
    <form method="POST" action="">
        <input type="hidden" name="academic_year" value="<?php echo htmlspecialchars($academic_year); ?>">
        <input type="hidden" name="criteria" value="<?php echo htmlspecialchars($criteria); ?>">
        <input type="hidden" name="criteria_no" value="<?php echo htmlspecialchars($criteria_no); ?>">

        <?php if ($show_branch_dropdown): ?>
            <label for="branch">Select Branch:</label>
            <select name="branch" id="branch" required>
                <option value="" disabled selected>Select Branch</option>
                <option value="AIDS">AIDS</option>
                <option value="AIML">AIML</option>
                <option value="CSE">CSE</option>
                <option value="CIVIL">CIVIL</option>
                <option value="MECH">MECH</option>
                <option value="EEE">EEE</option>
                <option value="ECE">ECE</option>
                <option value="IT">IT</option>
                <option value="BSH">BSH</option>
            </select>
        

        <button class='btn1' type="submit" name="upload" id='filter'>Filter Files</button>
        <?php endif; ?>
    </form>
    <?php if (isset($_POST['upload']) || !$show_branch_dropdown): ?>
            <form method="POST" action="">
                <input type="hidden" name="academic_year" value="<?php echo htmlspecialchars($academic_year); ?>">
                <input type="hidden" name="criteria" value="<?php echo htmlspecialchars($criteria); ?>">
                <input type="hidden" name="criteria_no" value="<?php echo htmlspecialchars($criteria_no); ?>">
                <?php if (isset($_POST['branch'])): ?>
                    <input type="hidden" name="branch" value="<?php echo htmlspecialchars($_POST['branch']); ?>">
                <?php endif; ?>
                <button type="submit" name="download_excel" class="download-excel">Download Excel</button>
            </form>
        <?php endif; ?>

<?php
if ($show_branch_dropdown ) {
    if (isset($_POST['upload'])) {
        $academic_year = $_POST['academic_year'];
        $criteria = $_POST['criteria'];
        $criteria_no = $_POST['criteria_no'];
        $branch = isset($_POST['branch']) ? $_POST['branch'] : null;  // Branch is only set if the dropdown is visible

        // If branch dropdown is shown, process branch filtering; otherwise, continue without branch
        if($show_semester){
            // Process with the branch dropdown logic
            if ($show_semester && $branch == 'BSH') {
                for ($sem = 1; $sem <= 2; $sem++) {
                    // SQL query for files filtering based on semester, academic year, branch, criteria, and criteria_no
                    $sql = "SELECT * FROM files WHERE academic_year = ? AND branch = ? AND sem = ? AND criteria = ? AND criteria_no = ? ORDER BY uploaded_at DESC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ssiss', $academic_year, $branch, $sem, $criteria, $criteria_no);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    displayFiles($result, $show_section, $show_semester, true);
                }
            } else {
                // Process for other semesters or criteria
                for ($sem = 3; $sem <= 8; $sem++) {
                    $query = "SELECT * FROM files WHERE academic_year = ? AND branch = ? AND sem = ? AND criteria = ? AND criteria_no = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('ssiss', $academic_year, $branch, $sem, $criteria, $criteria_no);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    ?><h3>SEMISTER - <?php echo "$sem"?></h3><?php
                    displayFiles($result, $show_section, $show_semester, true);
                }
            }
        }else if($show_ext_or_Int){
            $query = "SELECT * FROM files WHERE academic_year = ? AND branch = ? AND criteria = ? AND criteria_no = ? AND ext_or_int = ?" ;
            $ext_or_int1 = 'Internal';
            $stmt = $conn->prepare($query);
            // Change 'ssiss' to 'ssss' if all are strings
            $stmt->bind_param('sssss', $academic_year, $branch, $criteria, $criteria_no,$ext_or_int1);
            $stmt->execute();
            $result = $stmt->get_result();
            $query1 = "SELECT * FROM files WHERE academic_year = ? AND branch = ? AND criteria = ? AND criteria_no = ? AND ext_or_int = ?" ;
            $ext_or_int2 = 'External';
            $stmt1 = $conn->prepare($query1);
            // Change 'ssiss' to 'ssss' if all are strings
            $stmt1->bind_param('sssss', $academic_year, $branch, $criteria, $criteria_no,$ext_or_int2);
            $stmt1->execute();
            $result1 = $stmt1->get_result();
            ?> <h3>Internal</h3>  <?php

            echo "<table><tr>
            <th>Username</th>
            <th>Faculty Name</th>
            <th>Academic Year</th>
            <th>Filename</th>
            <th>Ext_or_Int</th>
            <th>Uploaded At</th>
            <th>View</th>
            <th>Download</th>
            </tr>";

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['UserName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['faculty_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['academic_year']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ext_or_int']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['file_name']) . "</td>";

                    $uploadedAt = new DateTime($row['uploaded_at']);
                    $formattedDateTime = $uploadedAt->format('d/m/Y') . ' & ' . $uploadedAt->format('H:i:s');
                    echo "<td>" . $formattedDateTime . "</td>";

                    echo "<td><a href='../view_file.php?id=" . htmlspecialchars($row['id']) . "'><button id='view' class='btn1'>View</button></a></td>";
                    echo "<td><a href='" . htmlspecialchars('../'.$row['file_path']) . "' download><button id='down' class='btn1'>Download</button></a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='" . ($show_section ? "9" : "8") . "' id='nod'>No files found</td></tr>";
            }
            echo "</table>";

            ?> <h3>External</h3>  <?php
            echo "<table><tr>
            <th>Username</th>
            <th>Faculty Name</th>
            <th>Academic Year</th>
            <th>Filename</th>
            <th>Ext_or_Int</th>
            <th>Uploaded At</th>
            <th>View</th>
            <th>Download</th>
            </tr>";

            if ($result1->num_rows > 0) {
                while ($row = $result1->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['UserName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['faculty_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['academic_year']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ext_or_int']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['file_name']) . "</td>";

                    $uploadedAt = new DateTime($row['uploaded_at']);
                    $formattedDateTime = $uploadedAt->format('d/m/Y') . ' & ' . $uploadedAt->format('H:i:s');
                    echo "<td>" . $formattedDateTime . "</td>";

                    echo "<td><a href='../view_file.php?id=" . htmlspecialchars($row['id']) . "'><button id='view' class='btn1'>View</button></a></td>";
                    echo "<td><a href='" . htmlspecialchars('../'.$row['file_path']) . "' download><button id='down' class='btn1'>Download</button></a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='" . ($show_section ? "9" : "8") . "' id='nod'>No files found</td></tr>";
            }
            echo "</table>";
        }
        else{
            $sql = "SELECT * FROM files WHERE academic_year = ?  AND criteria = ? AND criteria_no = ? AND branch = ? ORDER BY uploaded_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $academic_year, $criteria, $criteria_no);
            $stmt->execute();
            $result = $stmt->get_result();
            displayFiles($result, $show_section, false, $show_branch_dropdown);
        }
        }
    } else {
        // Logic when the branch dropdown is not visible (no branch filtering)
        $sql = "SELECT * FROM files WHERE academic_year = ?  AND criteria = ? AND criteria_no = ? ORDER BY uploaded_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $academic_year, $criteria, $criteria_no);
        $stmt->execute();
        $result = $stmt->get_result();
        displayFiles($result, $show_section, false, false);
    }

function displayFiles($result, $show_section, $show_semester,$show_branch_dropdown1){
    echo "<table><tr>
            <th>Username</th>
            <th>Faculty Name</th>
            <th>Academic Year</th>";
    if ($show_branch_dropdown1) {
        echo    "<th>Branch</th>";
    }
    if ($show_semester) {
        echo "<th>Semester</th>";
    }
    if ($show_section) {
        echo "<th>Section</th>";
    }
    echo "<th>Filename</th>
          <th>Uploaded At</th>
          <th>View</th>
          <th>Download</th>
          </tr>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['UserName']) . "</td>";
            echo "<td>" . htmlspecialchars($row['faculty_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['academic_year']) . "</td>";
            if ($show_branch_dropdown1) {
                echo "<td>" . htmlspecialchars($row['branch']) . "</td>";
            }
            if ($show_semester) {
                echo "<td>" . htmlspecialchars($row['sem']) . "</td>";
            }
            if ($show_section) {
                echo "<td>" . htmlspecialchars($row['section']) . "</td>";
            }
            echo "<td>" . htmlspecialchars($row['file_name']) . "</td>";

            $uploadedAt = new DateTime($row['uploaded_at']);
            $formattedDateTime = $uploadedAt->format('d/m/Y') . ' & ' . $uploadedAt->format('H:i:s');
            echo "<td>" . $formattedDateTime . "</td>";

            echo "<td><a href='../view_file.php?id=" . htmlspecialchars($row['id']) . "'><button id='view' class='btn1'>View</button></a></td>";
            echo "<td><a href='" . htmlspecialchars('../'.$row['file_path']) . "' download><button id='down' class='btn1'>Download</button></a></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='" . ($show_section ? "9" : "8") . "' id='nod'>No files found</td></tr>";
    }
    echo "</table>";
}
?>
</body>
</html>
