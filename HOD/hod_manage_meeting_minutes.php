<?php
require_once '../includes/session.php';
include_once "../includes/connection.php";
require_once "../includes/constants.php";

if (!isset($_SESSION['h_username']) && !isset($_SESSION['admin'])) {
    die("Unauthorized access.");
}

$dept = $_GET['dept'] ?? $_SESSION['dept'] ?? '';
$event = $_GET['event'] ?? 'Dept Meeting Minutes';


// Handle Accept/Reject Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_id'])) {
    $id = $_POST['action_id'];
    $action = $_POST['action'];
    $reason = $_POST['reason'] ?? '';

    if ($action === 'accept') {
        $stmt = $conn->prepare("UPDATE dept_files SET status = 'Accepted', rejection_reason = NULL WHERE id = ?");
        $stmt->bind_param("i", $id);
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE dept_files SET status = 'Rejected', rejection_reason = ? WHERE id = ?");
        $stmt->bind_param("si", $reason, $id);
    }

    if (isset($stmt) && $stmt->execute()) {
        $success_msg = "File " . htmlspecialchars(ucfirst($action)) . "ed successfully.";
    }
}

// Bulk Actions (Download)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && isset($_POST['selected_files'])) {
    $selectedFiles = $_POST['selected_files'];
    if ($_POST['bulk_action'] === 'download') {
        if (count($selectedFiles) == 1) {
            $fileId = $selectedFiles[0];
            $sql = "SELECT file_path FROM dept_files WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();

            $path = $file['file_path'];
            // Adjust path if needed. dept_meeting_minutes.php saves as '../../uploads/...'
            // From HOD directory, ../../uploads/ is correct if uploaded from modules/dept_coordinator/
            // Actually they save as '../../uploads/filename'. 
            // Relative to modules/dept_coordinator/ it is root/uploads/
            // Relative to HOD/ it should be ../uploads/
            $realPath = str_replace(PATH_DEEP_UPLOADS, PATH_UPLOADS, $path);

            if (file_exists($realPath)) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($realPath) . '"');
                readfile($realPath);
                exit;
            }
        } else {
            $zip = new ZipArchive();
            $zipName = "meeting_minutes_" . time() . ".zip";
            $zipPath = sys_get_temp_dir() . '/' . $zipName;

            if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
                foreach ($selectedFiles as $fileId) {
                    $sql = "SELECT file_path FROM dept_files WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $fileId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $file = $result->fetch_assoc();
                    $realPath = str_replace(PATH_DEEP_UPLOADS, PATH_UPLOADS, $file['file_path']);
                    if (file_exists($realPath)) {
                        $zip->addFile($realPath, basename($realPath));
                    }
                }
                $zip->close();
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $zipName . '"');
                readfile($zipPath);
                unlink($zipPath);
                exit;
            }
        }
    }
}

include_once 'header_hod.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage <?php echo htmlspecialchars((string)$event); ?></title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            color: #374151;
        }

        .main-container {
            margin-top: 100px;
            padding: 2rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th {
            background: #f9fafb;
            padding: 0.75rem;
            text-align: left;
            font-size: 0.875rem;
            font-weight: 600;
            color: #4b5563;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 1rem 0.75rem;
            font-size: 0.875rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-accepted {
            background: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-view {
            background: #3b82f6;
            color: white;
        }

        .btn-accept {
            background: #10b981;
            color: white;
        }

        .btn-reject {
            background: #ef4444;
            color: white;
        }

        .btn-bulk {
            background: #4b5563;
            color: white;
            margin-bottom: 1rem;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: white;
            margin: 15% auto;
            padding: 2rem;
            border-radius: 0.5rem;
            width: 400px;
        }

        textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            margin: 1rem 0;
        }

        .navbar {
            position: sticky;
            top: 70px;
            z-index: 99;
            margin-top: 100px;
            border-bottom: 1px solid #eee;

            background: white;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .nav-container {
            max-width: 1200px;
            margin: 80px auto 0;
            padding: 1rem;
            display: flex;
            align-items: center;
        }

        .breadcrumb {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .breadcrumb a {
            color: #3b82f6;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="nav-container">
            <div class="breadcrumb">
                <a href="../index.php">Home</a> &nbsp; >> &nbsp;
                <a href="see_uploads.php">HOD Dashboard</a> &nbsp; >> &nbsp;
                <span>Manage <?php echo htmlspecialchars((string)$event); ?></span>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="card">
            <h1><?php echo htmlspecialchars((string)$event); ?> - Pending Approval</h1>

            <?php if (isset($success_msg)): ?>
                <div
                    style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <button type="submit" name="bulk_action" value="download" class="btn btn-bulk">Download
                    Selected</button>
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Uploaded By</th>
                            <th>Acd Year</th>
                            <th>Category</th>
                            <th>File Name</th>
                            <th>Details</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM dept_files WHERE dept = ? AND file_type = ? AND status != 'Accepted' ORDER BY uploaded_at DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ss", $dept, $event);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        while ($row = $result->fetch_assoc()) {
                            $statusClass = 'status-pending';
                            if ($row['status'] === 'Accepted') {
                                $statusClass = 'status-accepted';
                            }
                            if ($row['status'] === 'Rejected') {
                                $statusClass = 'status-rejected';
                            }

                            $details = "";
                            if ($row['semester']) {
                                $details .= "Sem: " . $row['semester'] . " ";
                            }
                            if ($row['study_year']) {
                                $details .= "Year: " . $row['study_year'] . " ";
                            }
                            if ($row['meeting_no']) {
                                $details .= "Mtg #: " . $row['meeting_no'];
                            }

                            echo "<tr>";
                            echo "<td><input type='checkbox' name='selected_files[]' value='{$row['id']}'></td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            $display_year = ($row['meeting_no']) ? "Mtg #" . htmlspecialchars($row['meeting_no']) : htmlspecialchars($row['academic_year']);
                            echo "<td>$display_year</td>";
                            echo "<td>" . htmlspecialchars($row['sub_file_type']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['file_name']) . "</td>";
                            echo "<td><small>" . htmlspecialchars($details) . "</small></td>";
                            echo "<td><span class='status-badge $statusClass'>" . htmlspecialchars($row['status'] ?? 'Pending') . "</span></td>";
                            echo "<td>";
                            echo "<a href='" . htmlspecialchars(str_replace(PATH_DEEP_UPLOADS, PATH_UPLOADS, $row['file_path']), ENT_QUOTES) . "' target='_blank' class='btn btn-view' style='text-decoration:none; margin-right:5px;'>View</a>";

                            if ($row['status'] !== 'Accepted') {
                                echo "<button type='button' class='btn btn-accept' onclick='handleAction({$row['id']}, \"accept\")'>Accept</button> ";
                            }
                            if ($row['status'] !== 'Rejected') {
                                echo "<button type='button' class='btn btn-reject' onclick='openRejectModal({$row['id']})'>Reject</button>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <!-- Hidden form for actions -->
    <form id="actionForm" method="POST" style="display:none;">
        <input type="hidden" name="action_id" id="actionId">
        <input type="hidden" name="action" id="actionType">
        <input type="hidden" name="reason" id="rejectReason">
    </form>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <h2>Reject File</h2>
            <p>Please provide a reason for rejection:</p>
            <textarea id="reasonText" rows="4" placeholder="Enter reason..."></textarea>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button class="btn" onclick="closeRejectModal()">Cancel</button>
                <button class="btn btn-reject" onclick="submitReject()">Reject</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('selectAll').onclick = function () {
            var checkboxes = document.getElementsByName('selected_files[]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }

        let currentId = null;

        function handleAction(id, action) {
            if (confirm('Are you sure you want to ' + action + ' this file?')) {
                document.getElementById('actionId').value = id;
                document.getElementById('actionType').value = action;
                document.getElementById('actionForm').submit();
            }
        }

        function openRejectModal(id) {
            currentId = id;
            document.getElementById('rejectModal').style.display = 'block';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }

        function submitReject() {
            const reason = document.getElementById('reasonText').value;
            if (!reason) {
                alert('Please provide a reason.');
                return;
            }
            document.getElementById('actionId').value = currentId;
            document.getElementById('actionType').value = 'reject';
            document.getElementById('rejectReason').value = reason;
            document.getElementById('actionForm').submit();
        }
    </script>
</body>

</html>