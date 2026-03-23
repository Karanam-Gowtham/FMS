<?php
// Include database connection
include_once '../includes/connection.php';
include_once "./header_hod.php";


$event = isset($_GET['event']) ? htmlspecialchars($_GET['event']) : '';
$designation = isset($_GET['designation']) ? htmlspecialchars($_GET['designation']) : '';

if (isset($_GET['delete'])) {
    $yearToDelete = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM academic_year WHERE year = ?");
    $stmt->bind_param("s", $yearToDelete);
    $stmt->execute();
    $stmt->close();
    $safe_url = htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8');
    header("Location: " . $safe_url);
    exit;
}

// Handle Edit action
if (isset($_POST['edit_submit'])) {
    $oldYear = $_POST['old_year'];
    $newYear = $_POST['new_year'];
    $stmt = $conn->prepare("UPDATE academic_year SET year = ? WHERE year = ?");
    $stmt->bind_param("ss", $newYear, $oldYear);
    $stmt->execute();
    $stmt->close();
    $safe_url = htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8');
    header("Location: " . $safe_url);
    exit;
}

// Fetch data
$result = $conn->query("SELECT * FROM academic_year ORDER BY year DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Academic Years</title>
    <style>
         body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #74ebd5, #9face6);
        }
        body {
            display: flex;
            flex-direction: column;
        }
        table {
            width: 60%;
            margin: 30px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn11 {
            height: 40px;
            width:80px;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }
        .edit-btn11 { background-color: #2e7d32; }
        .delete-btn11 { background-color: #c62828; }

        .popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fff;
        border-radius: 12px;
        padding: 30px 40px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        z-index: 1000;
        text-align: center;
        width: 350px;
    }

    .popup h2 {
        margin-top: 0;
        color: #333;
        font-size: 22px;
        margin-bottom: 20px;
    }

    .popup input[type="text"] {
        width: 80%;
        padding: 8px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
    }

    .popup button {
        padding: 8px 16px;
        margin: 0 10px;
        font-size: 14px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .popup button[type="submit"] {
        background-color: #4CAF50;
        color: white;
    }

    .popup button[type="button"] {
        background-color: #f44336;
        color: white;
    }

    .overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }
              /* Navigation */
  .navbar {
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 80px;
            border-bottom: 1px solid #eee;
 
        font-size: larger;
    }

    .nav-container {
         /* margin-top moved to .navbar */
        background-color: white;
        width:150vw;
        padding: 0 1rem;
    }

    .nav-items {
        margin-left: 70px;
        display: flex;
        align-items: center;
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
    </style>

    <script>
        function showEditPopup(year) {
            document.getElementById('editYearInput').value = year;
            document.getElementById('oldYearInput').value = year;
            document.getElementById('popup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function hidePopup() {
            document.getElementById('popup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        function confirmDelete(year) {
            if (confirm("Are you sure you want to delete the academic year: " + year + "?")) {
                window.location.href = "<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>?delete=" + encodeURIComponent(year);
            }
        }
    </script>
</head>
<body>
<nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../index.php" class="home-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="acd_year_aa.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon"><?php echo htmlspecialchars($designation); ?>  </a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="Add_academic_year.php?designation=<?php echo urlencode($designation); ?>&event=<?php echo urlencode($event); ?>" class="home-icon">Add Academic Year</a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span><span class="main"> <a href="#" class="main-a">Edit Academic Year  </a></span>
                <span class="sid">&nbsp;  >> &nbsp; </span>
            </div>
        </div>
    </nav>

<div class="overlay" id="overlay" onclick="hidePopup()" onKeyDown="if(event.key === 'Enter' || event.keyCode === 13 || event.key === ' ') hidePopup()" role="button" tabindex="0"></div>

<table>
    <thead>
        <tr>
            <th scope="col">S.No</th>
            <th scope="col">Academic Year</th>
            <th scope="col">Edit</th>
            <th scope="col">Delete</th>
        </tr>
    </thead>

    <?php
    $sno = 1;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $sno++ . "</td>";
            echo "<td>" . htmlspecialchars($row['year']) . "</td>";
            echo "<td><button class='btn11 edit-btn11' onclick='showEditPopup(\"" . htmlspecialchars($row['year']) . "\")'>Edit</button></td>";
            echo "<td><button class='btn11 delete-btn11' onclick='confirmDelete(\"" . htmlspecialchars($row['year']) . "\")'>Delete</button></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No academic years found.</td></tr>";
    }
    ?>
</table>

<!-- Edit Popup -->
<div class="popup" id="popup">
    <form method="POST">
        <label for="editYearInput">Edit Academic Year:</label><br>
        <input type="text" name="new_year" id="editYearInput" required>
        <input type="hidden" name="old_year" id="oldYearInput">
        <br>
        <button type="submit" name="edit_submit">Update</button>
        <button type="button" onclick="hidePopup()">Cancel</button>
    </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
