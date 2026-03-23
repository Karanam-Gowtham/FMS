<?php
include_once "../includes/connection.php";
include_once "./header_hod.php";
?>
<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $criteriaNo = htmlspecialchars($_POST['criteria_no']);
    $academicYear = htmlspecialchars($_POST['year']);
    $criteria = htmlspecialchars($_POST['criteria']);

    // Select correct table based on academic year
    if ($academicYear == '2020-21') {
        $table = "criteria1";
    } elseif ($academicYear == '2021-22') {
        $table = "criteria2";
    } else {
        $table = "criteria";
    }

    // Fetch the existing record
    $sql = "SELECT * FROM $table WHERE SI_no='$criteria' AND Sub_no='$criteriaNo'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $description = $row['Des'];

    if (isset($_POST['update'])) {
        $newDescription = htmlspecialchars($_POST['description']);

        // Update query
        $updateSql = "UPDATE $table SET Des=? WHERE SI_no=? AND Sub_no=?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sis", $newDescription, $criteria, $criteriaNo);

        if ($stmt->execute()) {
            echo "<script>alert('Record updated successfully!');</script>";
            
            // Redirect using a hidden form to pass POST data
            echo "
            <form id='redirectForm' action='admin_criteria.php' method='POST'>
                <input type='hidden' name='year' value='$academicYear'>
                <input type='hidden' name='criteria' value='$criteria'>
            </form>
            <script>
                document.getElementById('redirectForm').submit();
            </script>";
            exit();
        } else {
            echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
        }
    }
}
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AQAR Criteria Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(54, 180, 226);
            display: flex;
            justify-content: center;
            margin: 0;
        }
        .container {
            text-align: center;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 80%;
            margin:90px 0px;
        }
        

        /* Title */
        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        /* Form inputs */
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Buttons */
        button {
            width: 100%;
            padding: 10px;
            background:rgb(1, 36, 59);
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s ease-in-out;
        }

        .button:hover {
            background:rgb(88, 183, 246);
        }

        /* Responsive design */
        @media (max-width: 400px) {
            .update-container {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
    <h2>Update Criteria</h2>
    <form method="POST">
        <input type="hidden" name="criteria_no" value="<?php echo $criteriaNo; ?>">
        <input type="hidden" name="year" value="<?php echo $academicYear; ?>">
        <input type="hidden" name="criteria" value="<?php echo $criteria; ?>">
        <label for="description">Description:</label><br><br>
        <input type="text" name="description" value="<?php echo $description; ?>" required><br><br>
        <button type="submit" name="update">Update</button>
    </form></div>
</body>
</html>

