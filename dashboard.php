<?php
session_start();
// Fallback: If session is empty but cookie exists, try to re-attach (though session_start does this usually)
// The issue might be that iframe considers it a separate context if cookies are strict.
// For now, let's assume standard session behavior.
include 'includes/connection.php';

// Auto-migrate tables
$tables = [
    'files', 
    'files5_1_1and2', 
    'files5_1_3', 
    'files5_1_4', 
    'files5_2_1',
    'files5_2_2',
    's_journal_tab',
    's_conference_tab',
    's_events',
    's_bodies',
    'fdps_tab', 
    'fdps_org_tab', 
    'conference_tab', 
    'published_tab', 
    'patents_table'
];

foreach ($tables as $table) {
    // Check if table exists to avoid errors
    $check_table = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check_table->num_rows > 0) {
        $check_col = $conn->query("SHOW COLUMNS FROM $table LIKE 'status'");
        if ($check_col->num_rows == 0) {
            $conn->query("ALTER TABLE $table ADD COLUMN status VARCHAR(50) DEFAULT 'Pending Dept Coordinator'");
        }
        $check_col = $conn->query("SHOW COLUMNS FROM $table LIKE 'rejection_reason'");
        if ($check_col->num_rows == 0) {
            $conn->query("ALTER TABLE $table ADD COLUMN rejection_reason TEXT");
        }
    }
}

// --- Ensure Rejection History Table Exists ---
$check_history = $conn->query("SHOW TABLES LIKE 'rejection_history'");
if ($check_history->num_rows == 0) {
    $conn->query("CREATE TABLE rejection_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        file_id INT NOT NULL,
        table_name VARCHAR(100) NOT NULL,
        rejected_by VARCHAR(100),
        rejection_reason TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

// --- AJAX Handler for History ---
if (isset($_GET['action']) && $_GET['action'] == 'get_history' && isset($_GET['file_id'], $_GET['table_name'])) {
    $fid = intval($_GET['file_id']);
    $tbl = $_GET['table_name'];
    $stmt = $conn->prepare("SELECT rejected_by, rejection_reason, created_at FROM rejection_history WHERE file_id = ? AND table_name = ? ORDER BY created_at DESC");
    $stmt->bind_param("is", $fid, $tbl);
    $stmt->execute();
    $res = $stmt->get_result();
    $history = [];
    while($r = $res->fetch_assoc()) {
        $history[] = $r;
    }
    header('Content-Type: application/json');
    echo json_encode($history);
    exit;
}


// --- Authorization & Role Detection ---
$role = '';
$user_id = '';
$dept = '';

if (isset($_SESSION['username'])) {
    $role = 'Faculty';
    $user_id = $_SESSION['username'];
} elseif (isset($_SESSION['a_username'])) {
    $role = 'Dept_Coordinator';
    $user_id = $_SESSION['a_username'];
    $stmt = $conn->prepare("SELECT department FROM reg_dept_cord WHERE userid = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($r = $res->fetch_assoc()) {
        $dept = $r['department'];
    }
} elseif (isset($_SESSION['h_username'])) {
    if ($_SESSION['h_username'] == 'central') {
        $role = 'Central_Coordinator';
        $user_id = 'central';
    } else {
        $role = 'HOD';
        $user_id = $_SESSION['h_username'];
        $dept = isset($_SESSION['dept']) ? $_SESSION['dept'] : '';
    }
} else {
    // If Admin login used specific session var?
    if (isset($_SESSION['admin'])) {
        $role = 'Admin';
        // Admin sees everything? Or nothing?
        // Let's assume Admin sees everything or is redirected.
        // For dashboard purposes, let's treat Admin as Central Coordinator or View All.
        $role = 'Central_Coordinator'; // Or View Only
    }
}

// Redirect if not logged in
if (!$role) {
    header("Location: index.php");
    exit();
}

// --- Handle Re-upload ---
// --- Handle Re-upload ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reupload') {
    $file_id = intval($_POST['file_id']);
    $table_name = $_POST['table_name'];
    
    // Schema Map for File Columns
    $table_schema_map = [
        'files' => ['path' => 'file_path', 'name' => 'file_name'],
        'files5_1_1and2' => ['path' => 'file_path', 'name' => 'file_name'],
        'files5_1_3' => ['path' => 'file_path', 'name' => 'file_name'],
        'files5_1_4' => ['path' => 'file_path', 'name' => 'file_name'],
        'files5_2_1' => ['path' => 'file_path', 'name' => 'file_name'],
        'files5_2_2' => ['path' => 'file_path', 'name' => 'file_name'],
        'fdps_tab' => ['path' => 'certificate', 'name' => null],
        'fdps_org_tab' => ['path' => 'certificate', 'name' => null],
        'conference_tab' => ['path' => 'certificate_path', 'name' => null],
        'published_tab' => ['path' => 'paper_file', 'name' => null],
        'patents_table' => ['path' => 'patent_file', 'name' => null],
        's_journal_tab' => ['path' => 'paper_file', 'name' => null],
        's_conference_tab' => ['path' => 'certificate_path', 'name' => null],
        's_events' => ['path' => 'certificate_path', 'name' => null],
        's_bodies' => ['path' => 'certificate_path', 'name' => null]
    ];

    if (!array_key_exists($table_name, $table_schema_map)) {
        echo "<script>alert('Error: Table configuration not found.');</script>";
    } elseif (isset($_FILES['new_file']) && $_FILES['new_file']['error'] === UPLOAD_ERR_OK) {
        
        $path_col = $table_schema_map[$table_name]['path'];
        $name_col = $table_schema_map[$table_name]['name'];

        // Fetch existing path
        $stmt = $conn->prepare("SELECT $path_col FROM $table_name WHERE id = ?");
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($row = $res->fetch_assoc()) {
            $old_path = $row[$path_col];
            
            // Normalize directory resolution
            $target_rel_root = str_replace('../../', '', $old_path);
            $target_dir = dirname($target_rel_root);
            
            // Fallback if directory seems empty or root
            if ($target_dir == '.' || empty($target_dir)) {
                $target_dir = 'uploads'; 
            }
            
            if (!is_dir($target_dir)) {
                @mkdir($target_dir, 0777, true);
            }
            
            $filename = basename($_FILES['new_file']['name']);
            $target_file = $target_dir . '/' . uniqid() . '_' . $filename;
            
            if (move_uploaded_file($_FILES['new_file']['tmp_name'], $target_file)) {
                // Determine DB path to save (restore ../../ if used previously)
                $db_path = $target_file;
                // If old path started with ../../, maintain that convention
                if (strpos($old_path, '../../') === 0) {
                    $db_path = '../../' . $target_file;
                } elseif (strpos($old_path, 'uploads/') === 0 && strpos($old_path, '../../') === false) {
                     // If it was just uploads/..., keep it that way (dashboard relative)
                     // But if the original script expects ../../, we might be breaking it?
                     // Usually standardizing on ../../uploads is safer if modules use it.
                     // But let's respect the current DB value's style.
                }

                // Update DB
                $update_sql = "UPDATE $table_name SET $path_col = ?";
                $params = [$db_path];
                $types = "s";

                if ($name_col) {
                    $update_sql .= ", $name_col = ?";
                    $params[] = $filename;
                    $types .= "s";
                }

                if ($role == 'Faculty') {
                    $update_sql .= ", status = ?, rejection_reason = NULL";
                    $params[] = 'Pending Dept Coordinator';
                    $types .= "s";
                }
                
                $update_sql .= " WHERE id = ?";
                $params[] = $file_id;
                $types .= "i";
                
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                
                echo "<script>alert('File re-uploaded successfully!');</script>";
            } else {
                echo "<script>alert('Failed to move uploaded file.');</script>";
            }
        } else {
            echo "<script>alert('File record not found.');</script>";
        }
    }
}

// --- Status Update Handler ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['file_id'], $_POST['table_name'])) {
    $file_id = intval($_POST['file_id']);
    $action = $_POST['action'];
    $reason = isset($_POST['reason']) ? $_POST['reason'] : '';
    $table_name = $_POST['table_name'];
    
    // Whitelist tables
    $allowed_tables = ['files', 'files5_1_1and2', 'files5_1_3', 'files5_1_4', 'fdps_tab', 'fdps_org_tab', 'conference_tab', 'published_tab', 'patents_table'];
    
    if (in_array($table_name, $allowed_tables)) {
        $new_status = '';
        
        if ($role == 'Dept_Coordinator') {
            if ($action == 'approve') $new_status = 'Pending HOD';
            elseif ($action == 'reject') $new_status = 'Rejected by Dept Coordinator'; // Sends back to Faculty
        } elseif ($role == 'HOD') {
            if ($action == 'approve') $new_status = 'Pending Central Coordinator';
            elseif ($action == 'reject') $new_status = 'Pending Dept Coordinator'; // Sends back to Dept Coordinator
        } elseif ($role == 'Central_Coordinator') {
            if ($action == 'approve') $new_status = 'Accepted';
            elseif ($action == 'reject') $new_status = 'Pending HOD'; // Sends back to HOD
        }

        if ($new_status) {
            $stmt = $conn->prepare("UPDATE $table_name SET status = ?, rejection_reason = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_status, $reason, $file_id);
            $stmt->execute();
            
            // Log to History
            if ($action == 'reject') {
                $hist_stmt = $conn->prepare("INSERT INTO rejection_history (file_id, table_name, rejected_by, rejection_reason) VALUES (?, ?, ?, ?)");
                $rejected_by_str = $role . " (" . $user_id . ")";
                $hist_stmt->bind_param("isss", $file_id, $table_name, $rejected_by_str, $reason);
                $hist_stmt->execute();
            }
        }
    }
}

// --- Fetch Files ---
$files = [];

// Helper to build partial query
function build_query($table, $id_col, $user_col, $desc_col, $date_col, $file_name_col, $file_path_col, $role, $user_id, $dept) {
    // Basic projection
    $q = "SELECT 
            $id_col as id, 
            $user_col as username, 
            $desc_col as description, 
            $date_col as uploaded_at, 
            $file_name_col as file_name, 
            $file_path_col as file_path, 
            status, 
            rejection_reason, 
            '$table' as table_name 
          FROM $table WHERE 1=1";
    
    // Filter by role
    if ($role == 'Faculty') {
        $q .= " AND $user_col = '$user_id'";
    } elseif ($role == 'Dept_Coordinator') {
        $q .= " AND status IN ('Pending Dept Coordinator', 'Rejected by Dept Coordinator')";
    } elseif ($role == 'HOD') {
        // HOD sees everything that has passed Dept Coordinator (pending HOD, or moved further)
        $q .= " AND status NOT IN ('Pending Dept Coordinator', 'Rejected by Dept Coordinator')";
    } elseif ($role == 'Central_Coordinator') {
        // Central sees everything that has passed HOD
        $q .= " AND status NOT IN ('Pending Dept Coordinator', 'Rejected by Dept Coordinator', 'Pending HOD', 'Rejected by HOD')";
    }
    
    return $q;
}

// List of queries to UNION
$queries = [];

// 1. files
$queries[] = build_query('files', 'id', 'UserName', 'description', 'uploaded_at', 'file_name', 'file_path', $role, $user_id, $dept);

// 2. files5_1_1and2
$queries[] = build_query('files5_1_1and2', 'id', 'UserName', 'scheme_name', 'uploaded_at', 'file_name', 'file_path', $role, $user_id, $dept);

// 3. files5_1_3
$queries[] = build_query('files5_1_3', 'id', 'username', 'programme_name', 'uploaded_at', 'file_name', 'file_path', $role, $user_id, $dept);

// 4. files5_1_4
$queries[] = build_query('files5_1_4', 'id', 'username', 'career_details', 'uploaded_at', 'file_name', 'file_path', $role, $user_id, $dept);

// 5. fdps_tab
$queries[] = build_query('fdps_tab', 'id', 'username', 'title', 'submission_time', 'title', 'certificate', $role, $user_id, $dept);

// 6. fdps_org_tab
$queries[] = build_query('fdps_org_tab', 'id', 'username', 'title', 'submission_time', 'title', 'certificate', $role, $user_id, $dept);

// 7. conference_tab
// Check if certificate_path exists or paper_file_path. Let's start with certificate_path.
$queries[] = build_query('conference_tab', 'id', 'username', 'paper_title', 'submission_time', 'paper_title', 'certificate_path', $role, $user_id, $dept);

// 8. published_tab
$queries[] = build_query('published_tab', 'id', 'username', 'journal_name', 'submission_time', 'paper_title', 'paper_file', $role, $user_id, $dept);

// 9. patents_table
// 9. patents_table
$queries[] = build_query('patents_table', 'id', 'Username', 'patent_title', 'submission_time', 'patent_title', 'patent_file', $role, $user_id, $dept);

// 10. files5_2_1 (Placement Details)
$queries[] = build_query('files5_2_1', 'id', 'username', 'student_name', 'uploaded_at', 'file_name', 'file_path', $role, $user_id, $dept);

// 11. files5_2_2 (Higher Education)
$queries[] = build_query('files5_2_2', 'id', 'username', 'student_name', 'uploaded_at', 'file_name', 'file_path', $role, $user_id, $dept);

// 12. s_journal_tab (Journal Papers)
$queries[] = build_query('s_journal_tab', 'id', 'Username', 'paper_title', 'submission_time', 'paper_title', 'paper_file', $role, $user_id, $dept);

// 13. s_conference_tab (Conference Papers) - Note: s_conference_tab has certificate_path and paper_file_path. Using certificate_path primarily or paper depending?
$queries[] = build_query('s_conference_tab', 'id', 'Username', 'paper_title', 'submission_time', 'paper_title', 'certificate_path', $role, $user_id, $dept);

// 14. s_events (Student Activities: Projects, Internships, etc.)
$queries[] = build_query('s_events', 'id', 'Username', 'event_name', 'submission_time', 'event_name', 'certificate_path', $role, $user_id, $dept);

// 15. s_bodies (Professional Bodies)
$queries[] = build_query('s_bodies', 'id', 'Username', 'Body', 'submission_time', 'event_name', 'certificate_path', $role, $user_id, $dept);


// Combine all
$full_query = implode(" UNION ALL ", $queries);
$full_query .= " ORDER BY uploaded_at DESC";

$result = $conn->query($full_query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $files[] = $row;
    }
} else {
    // If query fails (e.g. missing table or column), fail gracefully?
    // Display error for debug
    $error = $conn->error;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - File Status</title>
    <link rel="stylesheet" href="assets/css/header.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f9;
            color: #333;
            margin: 0;
            padding-top: <?php echo (isset($_GET['mode']) && $_GET['mode'] == 'iframe') ? '20px' : '80px'; ?>;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .dashboard-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        h2 { margin-top: 0; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; display: inline-block; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #555;
        }
        tr:hover { background-color: #f1f1f1; }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            display: inline-block;
        }
        .status-pending-dept { background-color: #fff3cd; color: #856404; }
        .status-pending-hod { background-color: #cfe2ff; color: #084298; }
        .status-pending-central { background-color: #d1e7dd; color: #0f5132; }
        .status-accepted { background-color: #d1e7dd; color: #198754; }
        .status-rejected { background-color: #f8d7da; color: #842029; }
        
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            font-size: 0.9em;
            transition: background 0.2s;
        }
        .btn-approve { background-color: #28a745; color: white; }
        .btn-approve:hover { background-color: #218838; }
        .btn-reject { background-color: #dc3545; color: white; }
        .btn-reject:hover { background-color: #c82333; }
        
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.4); 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 400px;
            border-radius: 8px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        textarea {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn-toolbar {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
    </style>
</head>
<body>

<?php 
if (!isset($_GET['mode']) || $_GET['mode'] != 'iframe') {
    include 'includes/header.php'; 
}
?>

<div class="container">
    <div class="dashboard-card">
        <h2>Dashboard - <?php echo $role; ?> View <?php if($dept) echo "($dept)"; ?></h2>
        
        <?php if (!empty($error)): ?>
            <p style="color:red;">Error fetching files: <?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <?php if (empty($files)): ?>
            <p>No files found requiring your attention.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>User</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): ?>
                        <?php 
                            $statusClass = 'status-pending-dept';
                            if ($file['status'] == 'Pending HOD') $statusClass = 'status-pending-hod';
                            elseif ($file['status'] == 'Pending Central Coordinator') $statusClass = 'status-pending-central';
                            elseif ($file['status'] == 'Accepted') $statusClass = 'status-accepted';
                            elseif (strpos($file['status'], 'Rejected') !== false) $statusClass = 'status-rejected';
                            
                            $display_path = $file['file_path'];
                            $display_path = str_replace(['../../', '../'], '', $display_path);
                        ?>
                        <tr>
                            <td>
                                <a href="<?php echo htmlspecialchars($display_path); ?>" target="_blank">
                                    <?php echo htmlspecialchars($file['file_name']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($file['username']); ?></td>
                            <td><?php echo htmlspecialchars($file['description']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($file['uploaded_at'])); ?></td>
                            <td><small><?php echo htmlspecialchars($file['table_name']); ?></small></td>
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($file['status'] ?? 'Pending Dept Coordinator'); ?>
                                </span>
                                <?php if ($file['rejection_reason']): ?>
                                    <br><small style="color:red;"><?php echo htmlspecialchars($file['rejection_reason']); ?></small>
                                <?php endif; ?>
                                <br><a href="#" onclick="openHistoryModal(<?php echo $file['id']; ?>, '<?php echo $file['table_name']; ?>'); return false;" style="font-size:0.8em; color:#666;">View History</a>
                            </td>
                            <td>
                                <?php
                                $can_act = false;
                                if ($role == 'Dept_Coordinator' && $file['status'] == 'Pending Dept Coordinator') $can_act = true;
                                if ($role == 'HOD' && $file['status'] == 'Pending HOD') $can_act = true;
                                if ($role == 'Central_Coordinator' && $file['status'] == 'Pending Central Coordinator') $can_act = true;
                                
                                // Re-upload permitted if you can act (Reviewer fixing it) OR if you are Faculty and it was rejected
                                $can_reupload = $can_act || ($role == 'Faculty' && strpos($file['status'], 'Rejected') !== false);
                                
                                $buttons_shown = false;
                                ?>
                                
                                <?php if ($can_act): $buttons_shown = true; ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="file_id" value="<?php echo $file['id']; ?>">
                                    <input type="hidden" name="table_name" value="<?php echo $file['table_name']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="action-btn btn-approve">Approve</button>
                                </form>
                                <button type="button" class="action-btn btn-reject" onclick="openRejectModal(<?php echo $file['id']; ?>, '<?php echo $file['table_name']; ?>')">Reject</button>
                                <?php endif; ?>
                                
                                <?php if ($can_reupload): $buttons_shown = true; ?>
                                <button type="button" class="action-btn" style="background-color:#17a2b8; color:white;" onclick="openReuploadModal(<?php echo $file['id']; ?>, '<?php echo $file['table_name']; ?>')">Re-upload</button>
                                <?php endif; ?>
                                
                                <?php if (!$buttons_shown): ?>
                                    <span style="color:#aaa;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div id="rejectModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeRejectModal()">&times;</span>
        <h3>Reject File</h3>
        <form method="POST">
            <input type="hidden" name="file_id" id="reject_file_id">
            <input type="hidden" name="table_name" id="reject_table_name">
            <input type="hidden" name="action" value="reject">
            <label for="reason">Reason for Rejection:</label>
            <textarea name="reason" id="reason" rows="3" required></textarea>
            <div class="btn-toolbar">
                <button type="button" class="action-btn" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="action-btn btn-reject">Confirm Reject</button>
            </div>
        </form>
    </div>
</div>

<div id="reuploadModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeReuploadModal()">&times;</span>
        <h3>Re-upload File</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="file_id" id="reupload_file_id">
            <input type="hidden" name="table_name" id="reupload_table_name">
            <input type="hidden" name="action" value="reupload">
            <label for="new_file">Select New File:</label>
            <input type="file" name="new_file" id="new_file" required style="margin: 10px 0; display:block;">
            <div class="btn-toolbar">
                <button type="button" class="action-btn" onclick="closeReuploadModal()">Cancel</button>
                <button type="submit" class="action-btn" style="background-color:#17a2b8; color:white;">Upload</button>
            </div>
        </form>
    </div>
</div>

<div id="historyModal" class="modal">
    <div class="modal-content" style="width: 500px;">
        <span class="close" onclick="closeHistoryModal()">&times;</span>
        <h3>Rejection History</h3>
        <div id="historyContent" style="max-height: 300px; overflow-y: auto;">
            <p>Loading...</p>
        </div>
        <div class="btn-toolbar">
            <button type="button" class="action-btn" onclick="closeHistoryModal()">Close</button>
        </div>
    </div>
</div>

<script>
    function openRejectModal(id, tableName) {
        document.getElementById('reject_file_id').value = id;
        document.getElementById('reject_table_name').value = tableName;
        document.getElementById('rejectModal').style.display = "block";
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = "none";
    }
    
    function openReuploadModal(id, tableName) {
        document.getElementById('reupload_file_id').value = id;
        document.getElementById('reupload_table_name').value = tableName;
        document.getElementById('reuploadModal').style.display = "block";
    }
    function closeReuploadModal() {
        document.getElementById('reuploadModal').style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('rejectModal')) {
            closeRejectModal();
        }
        if (event.target == document.getElementById('reuploadModal')) {
            closeReuploadModal();
        }
        if (event.target == document.getElementById('historyModal')) {
            closeHistoryModal();
        }
    }
    
    function openHistoryModal(id, tableName) {
        document.getElementById('historyModal').style.display = "block";
        const contentDiv = document.getElementById('historyContent');
        contentDiv.innerHTML = '<p>Loading...</p>';
        
        fetch('dashboard.php?action=get_history&file_id=' + id + '&table_name=' + tableName)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    contentDiv.innerHTML = '<p>No history found.</p>';
                } else {
                    let html = '<ul style="list-style:none; padding:0;">';
                    data.forEach(item => {
                        html += '<li style="border-bottom:1px solid #eee; padding:10px 0;">';
                        html += '<strong>' + item.rejected_by + '</strong> <small style="color:#888;">' + item.created_at + '</small><br>';
                        html += '<span style="color:#d9534f;">' + item.rejection_reason + '</span>';
                        html += '</li>';
                    });
                    html += '</ul>';
                    contentDiv.innerHTML = html;
                }
            })
            .catch(error => {
                contentDiv.innerHTML = '<p style="color:red;">Error loading history.</p>';
                console.error('Error:', error);
            });
    }
    
    function closeHistoryModal() {
        document.getElementById('historyModal').style.display = "none";
    }
</script>

</body>
</html>
