<?php
session_start();
include 'includes/connection.php';
require_once 'includes/constants.php';

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
} elseif (isset($_SESSION['j_username'])) {
    $role = 'Jr_Assistant';
    $user_id = $_SESSION['j_username'];
    $stmt = $conn->prepare("SELECT department FROM reg_jr_assistant WHERE userid = ?");
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
    if (isset($_SESSION['admin'])) {
        $role = 'Central_Coordinator'; // Admin sees everything
    }
}

if (!$role) {
    echo json_encode(['count' => 0]);
    exit();
}

// Helper to build partial query (Count version)
function build_count_query($conn, $table, $user_col, $role, $user_id, $dept)
{
    $user_esc = mysqli_real_escape_string($conn, (string) $user_id);
    $dept_esc = mysqli_real_escape_string($conn, (string) $dept);
    $q = "SELECT COUNT(*) as cnt FROM $table";
    $join = "";
    $where = " WHERE 1=1";

    if ($role == 'Faculty') {
        // Faculty: Notification for pending or rejected files
        $where .= " AND $table.$user_col = '$user_esc' AND (status LIKE '%Pending%' OR status LIKE '%Rejected%')";
    } elseif ($role == 'Dept_Coordinator' || $role == 'Jr_Assistant') {
        // Dept Coordinator / Jr Assistant: Waiting for their approval from THEIR department
        $where .= SQL_AND_STATUS_EQ . STATUS_PENDING_DEPT_COORD . "'";
        if (!empty($dept)) {
            // Join with reg_tab to verify department of the uploader
            $join = " LEFT JOIN reg_tab ON $table.$user_col = reg_tab.userid";
            $where .= " AND reg_tab.dept = '$dept_esc'";
        }
    } elseif ($role == 'HOD') {
        // HOD: Waiting for their approval from THEIR department
        $where .= SQL_AND_STATUS_EQ . STATUS_PENDING_HOD . "'";
        if (!empty($dept)) {
            $join = " LEFT JOIN reg_tab ON $table.$user_col = reg_tab.userid";
            $where .= " AND reg_tab.dept = '$dept_esc'";
        }
    } elseif ($role == 'Central_Coordinator') {
        // Central Coordinator now just views accepted files; no approval notifications.
        $where .= " AND 1=0";
    }

    return $q . $join . $where;
}

// Replicate the list of tables from dashboard.php
$queries = [];

// 1. files
$queries[] = build_count_query($conn, 'files', 'UserName', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 'files5_1_1and2', 'UserName', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 'files5_1_3', 'username', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 'files5_1_4', 'username', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 'fdps_tab', 'username', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 'fdps_org_tab', 'username', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 'conference_tab', 'username', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 'published_tab', 'username', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 'patents_table', 'Username', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 'files5_2_1', 'username', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 'files5_2_2', 'username', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 's_journal_tab', 'Username', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 's_conference_tab', 'Username', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 's_events', 'Username', $role, $user_id, $dept);
$queries[] = build_count_query($conn, 's_bodies', 'Username', $role, $user_id, $dept);

// Special case for dept_files
$user_esc_df = mysqli_real_escape_string($conn, (string) $user_id);
$dept_esc_df = mysqli_real_escape_string($conn, (string) $dept);
$dept_files_q = "SELECT COUNT(*) as cnt FROM dept_files WHERE (status != '" . STATUS_ACCEPTED . "' OR status IS NULL)";
if ($role == 'Faculty') {
    $dept_files_q .= " AND username = '$user_esc_df' AND (status LIKE '%Pending%' OR status LIKE '%Rejected%')";
} elseif ($role == 'Dept_Coordinator' || $role == 'Jr_Assistant') {
    $dept_files_q .= " AND username = '$user_esc_df' AND (status LIKE '%Pending%' OR status LIKE '%Rejected%')";
} elseif ($role == 'HOD') {
    $dept_files_q .= SQL_AND_STATUS_EQ . STATUS_PENDING_HOD . "'";
    if (!empty($dept)) {
        $dept_files_q .= " AND dept = '$dept_esc_df'";
    }
} elseif ($role == 'Central_Coordinator') {
    // Hidden for central since no approval steps
    $dept_files_q .= " AND 1=0";
}
$queries[] = $dept_files_q;


// Aggregate total count from all tables
$full_query = implode(" UNION ALL ", $queries);
$final_query = "SELECT SUM(cnt) as total_count FROM ($full_query) as t";

$result = $conn->query($final_query);
$count = 0;
if ($result && $row = $result->fetch_array()) {
    $count = (int) $row['total_count'];
}


// -- Email Notification Logic --
// Only proceed if there are pending files and we have a valid role/user
if ($count > 0 && !empty($role) && !empty($user_id)) {

    $shouldSendEmail = false;
    $email = '';
    $recipientName = $user_id;

    // USER REQUIREMENT: "for now i want you to implement it for the user in the cse department (faculty role)"
    if ($role == 'Faculty') {
        // We need to check if this faculty is from CSE
        // We can check `reg_tab` which likely links `userid` to `dept`
        // Or if the session has dept info? Usually faculty session just has username.
        $stmt_dept = $conn->prepare("SELECT dept FROM reg_tab WHERE userid = ?");
        $stmt_dept->bind_param("s", $user_id);
        $stmt_dept->execute();
        $res_dept = $stmt_dept->get_result();
        if (($row_dept = $res_dept->fetch_assoc()) && $row_dept['dept'] == 'CSE') {
            $shouldSendEmail = true;
        }

        if ($shouldSendEmail) {
            // Fetch email from table where it exists. 
            // User asked to add email to `admin_login` previously, but `reg_tab` naturally has email too.
            // Let's use `reg_tab` email if available as it's more likely to be the personal/official one.
            // Or `admin_login` if that was the instruction.
            // Let's check `admin_login` first as per recent instruction.
            $stmt_e = $conn->prepare("SELECT email FROM admin_login WHERE Username = ?");
            $stmt_e->bind_param("s", $user_id);
            $stmt_e->execute();
            $res_e = $stmt_e->get_result();
            if ($r_e = $res_e->fetch_assoc()) {
                $email = $r_e['email'];
            }
            // Fallback to reg_tab if empty
            if (empty($email)) {
                $stmt_e2 = $conn->prepare("SELECT email FROM reg_tab WHERE userid = ?");
                $stmt_e2->bind_param("s", $user_id);
                $stmt_e2->execute();
                $res_e2 = $stmt_e2->get_result();
                if ($r_e2 = $res_e2->fetch_assoc()) {
                    $email = $r_e2['email'];
                }
            }
        }
    }
    // Commented out other roles for now based on strict user request
    /*
    elseif ($role == 'Dept_Coordinator') {
        $stmt_e = $conn->prepare("SELECT email FROM reg_dept_cord WHERE userid = ?");
        $stmt_e->bind_param("s", $user_id);
        // ... execute and fetch
        $shouldSendEmail = true; 
    } 
    */

    // 2. Send Email if justified and email found
    if ($shouldSendEmail && !empty($email)) {
        $session_key = "last_email_sent_" . $role . "_" . $user_id;
        $throttle_time = 3600; // 1 hour

        $last_sent = isset($_SESSION[$session_key]) ? $_SESSION[$session_key] : 0;

        if (time() - $last_sent > $throttle_time) {
            require_once 'includes/send_email.php';
            // For testing: ensuring we are calling the function correctly
            // sendNotificationEmail($toEmail, $recipientName, $pendingCount)
            if (sendNotificationEmail($email, $recipientName, $count)) {
                $_SESSION[$session_key] = time();
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode(['count' => $count]);
?>