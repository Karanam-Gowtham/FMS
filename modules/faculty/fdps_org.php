<?php

include "../../includes/connection.php";
include "../../includes/header.php";

if (!isset($_SESSION['username'])) {
    die("You need to log in to view this page.");
}

$username = $_SESSION['username'];
$dept = isset($_GET['dept']) ? $_GET['dept'] : '';

// Fetch branch/dept if not set
if (!$dept) {
    $stmt = $conn->prepare("SELECT dept FROM reg_tab WHERE userid = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $dept = $row['dept'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $organised_by = $_POST['organised_by'];
    $location = $_POST['location'];
    $year = $_POST['year'];
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];

    // Upload Dir
    $target_dir = "../../uploads/fdps_org/";
    if (!is_dir($target_dir))
        mkdir($target_dir, 0777, true);

    // Helper to upload file
    function uploadFile($fileInputName, $target_dir)
    {
        if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == 0) {
            $allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg'];
            $fileExtension = strtolower(pathinfo($_FILES[$fileInputName]["name"], PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedExtensions)) {
                return ""; // Skip invalid files
            }

            $fileName = time() . '_' . $fileInputName . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES[$fileInputName]["name"]));
            $targetFile = $target_dir . $fileName;
            if (move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $targetFile)) {
                return $targetFile;
            }
        }
        return "";
    }

    $certificate = uploadFile('certificate', $target_dir);
    $brochure = uploadFile('brochure', $target_dir);
    $schedule = uploadFile('schedule', $target_dir);
    $attendance = uploadFile('attendance', $target_dir);
    $feedback = uploadFile('feedback', $target_dir);
    $report = uploadFile('report', $target_dir);
    $photo1 = uploadFile('photo1', $target_dir);
    $photo2 = uploadFile('photo2', $target_dir);
    $photo3 = uploadFile('photo3', $target_dir);

    // Handle Merged PDF from Frontend
    $merged_file_path = "";
    if (isset($_POST['merged_pdf_data']) && !empty($_POST['merged_pdf_data'])) {
        $data = $_POST['merged_pdf_data'];
        if (preg_match('/^data:application\/pdf;base64,/', $data)) {
            $data = substr($data, strpos($data, ',') + 1);
        }
        $decoded_data = base64_decode($data);
        $merged_file_name = time() . '_merged_fdp.pdf';
        $merged_file_path = $target_dir . $merged_file_name;
        file_put_contents($merged_file_path, $decoded_data);
    }

    $status = 'Pending HOD';

    $sql = "INSERT INTO fdps_org_tab (username, branch, title, date_from, date_to, organised_by, location, year, certificate, brochure, fdp_schedule_invitation, attendance_forms, feedback_forms, fdp_report, photo1, photo2, photo3, merged_file, submission_time, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssssssss", $username, $dept, $title, $date_from, $date_to, $organised_by, $location, $year, $certificate, $brochure, $schedule, $attendance, $feedback, $report, $photo1, $photo2, $photo3, $merged_file_path, $status);

    if ($stmt->execute()) {
        echo "<script>alert('FDP Organized Record added successfully!'); window.location.href='acd_year.php?dept=" . urlencode($dept) . "';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Upload FDP Organized</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #0a192f 0%, #172a45 100%);
            color: white;
            min-height: 100vh;
            margin: 0;
            padding-bottom: 50px;
        }

        .navbar {
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 100px;
            border-bottom: 1px solid #eee;
            background-color: white;
            font-size: larger;
        }

        .nav-container {
            margin-left: 100px;
            max-width: 80rem;
            padding: 0 1rem;
        }

        .nav-items {
            display: flex;
            align-items: center;
            height: 4rem;
        }

        .sid {
            color: rgb(48, 30, 138);
            font-weight: 500;
        }

        .main-a {
            color: rgb(138, 30, 113);
            font-weight: 500;
        }

        #sp {
            color: blue;
        }

        .container11 {
            margin: 50px auto;
            background: rgba(16, 15, 15, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.2);
            max-width: 800px;
            width: 90%;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
            color: #fff;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-weight: bold;
            color: #ccc;
        }

        input[type="text"],
        input[type="date"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1rem;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus,
        input[type="file"]:focus {
            background: rgba(255, 255, 255, 0.2);
            outline: none;
            border-color: rgba(255, 255, 255, 0.5);
        }

        .full-width {
            grid-column: span 2;
        }

        button {
            width: 100%;
            padding: 15px;
            background: #ff6347;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s, transform 0.1s;
        }

        button:hover {
            background: #e55337;
            transform: translateY(-2px);
        }

        option {
            background-color: #333;
            color: white;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-items">
                <a href="../../index.php" class="home-icon" style="color: rgb(30, 58, 138); text-decoration: none;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                <span id="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                        href="../../admin/admins.php?dept=<?php echo urlencode($dept); ?>"
                        style="text-decoration: none; color: inherit;">Department(<?php echo htmlspecialchars($dept); ?>)</a></span>
                <span id="sp">&nbsp; >> &nbsp;</span><span class="sid"><a
                        href="acd_year.php?dept=<?php echo urlencode($dept); ?>"
                        style="text-decoration: none; color: inherit;">Faculty</a></span>
                <span id="sp">&nbsp; >> &nbsp;</span><span class="main"><a href="#" class="main-a"
                        style="text-decoration: none;">FDPs Organized</a></span>
            </div>
        </div>
    </nav>

    <div class="container11">
        <h2>Upload FDPs Organized Details</h2>
        <form id="fdpForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="merged_pdf_data" id="merged_pdf_data">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Title of FDP:</label>
                    <input type="text" name="title" required placeholder="Enter FDP Title">
                </div>

                <div class="form-group full-width">
                    <label>Organised By:</label>
                    <input type="text" name="organised_by" required placeholder="Instituion/Organization Name">
                </div>

                <div class="form-group full-width">
                    <label>Location:</label>
                    <input type="text" name="location" required placeholder="City, State">
                </div>

                <div class="form-group full-width">
                    <label>Academic Year:</label>
                    <select name="year" required>
                        <option value="">Select Year</option>
                        <?php
                        $y_sql = "SELECT year FROM academic_year ORDER BY year DESC";
                        $y_res = $conn->query($y_sql);
                        if ($y_res) {
                            while ($y = $y_res->fetch_assoc()) {
                                echo "<option value='" . $y['year'] . "'>" . $y['year'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date From:</label>
                    <input type="date" name="date_from" required>
                </div>

                <div class="form-group">
                    <label>Date To:</label>
                    <input type="date" name="date_to" required>
                </div>

                <div class="form-group">
                    <label>Certificate:</label>
                    <input type="file" name="certificate" accept=".pdf,.png,.jpg,.jpeg">
                </div>

                <div class="form-group">
                    <label>Brochure:</label>
                    <input type="file" name="brochure" accept=".pdf,.png,.jpg,.jpeg">
                </div>

                <div class="form-group">
                    <label>Schedule/Invitation:</label>
                    <input type="file" name="schedule" accept=".pdf,.png,.jpg,.jpeg">
                </div>

                <div class="form-group">
                    <label>Attendance Forms:</label>
                    <input type="file" name="attendance" accept=".pdf,.png,.jpg,.jpeg">
                </div>

                <div class="form-group">
                    <label>Feedback Forms:</label>
                    <input type="file" name="feedback" accept=".pdf,.png,.jpg,.jpeg">
                </div>

                <div class="form-group">
                    <label>Report:</label>
                    <input type="file" name="report" accept=".pdf,.doc,.docx">
                </div>

                <div class="form-group">
                    <label>Photo 1:</label>
                    <input type="file" name="photo1" accept=".png,.jpg,.jpeg">
                </div>

                <div class="form-group">
                    <label>Photo 2:</label>
                    <input type="file" name="photo2" accept=".png,.jpg,.jpeg">
                </div>

                <div class="form-group full-width">
                    <label>Photo 3 (Optional):</label>
                    <input type="file" name="photo3" accept=".png,.jpg,.jpeg">
                </div>

                <div class="full-width">
                    <button type="submit">Submit Details</button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://unpkg.com/pdf-lib/dist/pdf-lib.min.js"></script>
    <script>
        document.getElementById('fdpForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Merging Files... Please Wait';

            try {
                const { PDFDocument } = PDFLib;
                const mergedPdf = await PDFDocument.create();
                let addedAny = false;

                // Ordered inputs
                const inputNames = ['brochure', 'schedule', 'attendance', 'feedback', 'report', 'photo1', 'photo2', 'photo3', 'certificate'];

                for (const name of inputNames) {
                    const input = this.querySelector(`input[name="${name}"]`);
                    if (input && input.files.length > 0) {
                        const file = input.files[0];
                        const arrayBuffer = await file.arrayBuffer();
                        const extension = file.name.split('.').pop().toLowerCase();

                        if (extension === 'pdf') {
                            const pdf = await PDFDocument.load(arrayBuffer);
                            const pages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                            pages.forEach(p => mergedPdf.addPage(p));
                            addedAny = true;
                        } else if (['jpg', 'jpeg', 'png'].includes(extension)) {
                            let image;
                            if (extension === 'png') image = await mergedPdf.embedPng(arrayBuffer);
                            else image = await mergedPdf.embedJpg(arrayBuffer);

                            const { width, height } = image.scale(1);
                            const page = mergedPdf.addPage([width, height]);
                            page.drawImage(image, { x: 0, y: 0, width, height });
                            addedAny = true;
                        }
                    }
                }

                if (addedAny) {
                    const pdfBytes = await mergedPdf.save();
                    const base64String = await new Promise((resolve) => {
                        const reader = new FileReader();
                        reader.onloadend = () => resolve(reader.result);
                        reader.readAsDataURL(new Blob([pdfBytes]));
                    });
                    document.getElementById('merged_pdf_data').value = base64String;
                }
            } catch (err) {
                console.error("Merging error:", err);
            }

            this.submit();
        });
    </script>
</body>

</html>
<?php $conn->close(); ?>