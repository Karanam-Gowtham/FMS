<?php
if (!isset($conn)) {
    include_once __DIR__ . '/connection.php';
}
if (!isset($user_id) && isset($_SESSION['username'])) {
    // Attempt to get user_id from session or database
    $email_to_check = $_SESSION['username'] ?? $_SESSION['h_username'] ?? $_SESSION['a_username'] ?? $_SESSION['j_username'] ?? $_SESSION['admin'] ?? null;
    if ($email_to_check) {
        $stmt_u = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
        $stmt_u->bind_param("s", $email_to_check);
        $stmt_u->execute();
        $res_u = $stmt_u->get_result();
        if ($row_u = $res_u->fetch_assoc()) {
            $user_id = $row_u['user_id'];
        }
        $stmt_u->close();
    }
}

// Fallbacks if variables aren't strictly defined by the legacy pages
$user_id = $user_id ?? ($_SESSION['user_id'] ?? 0);
$role_id = $role_id ?? ($_SESSION['role_id'] ?? 0);
$dept_id = $dept_id ?? ($_SESSION['dept_id'] ?? 0);

// If we still don't have role_id, try to infer it from session vars based on legacy auth
if (!$role_id) {
    if (isset($_SESSION['username'])) $role_id = 3; // Faculty (assuming 3 based on temp DB)
    elseif (isset($_SESSION['a_username'])) $role_id = 4; // Coordinator
    elseif (isset($_SESSION['h_username'])) $role_id = 2; // HOD
    elseif (isset($_SESSION['admin'])) $role_id = 1; // Admin
}

// Ensure the Documents table exists before querying
$documents = [];
$table_check = $conn->query("SHOW TABLES LIKE 'Documents'");
if ($table_check && $table_check->num_rows > 0) {
    if ($role_id == 3) { // ROLE_FACULTY
        $stmt = $conn->prepare("
            SELECT d.*, dt.type_name, dc.category_name, dep.dept_name, ay.year_name,
                   u.full_name as uploader_name
            FROM Documents d
            JOIN Document_Types dt ON d.type_id = dt.type_id
            JOIN Document_Categories dc ON dt.category_id = dc.category_id
            JOIN Dept dep ON d.dept_id = dep.dept_id
            JOIN Academic_Years ay ON d.academic_year_id = ay.academic_year_id
            JOIN Users u ON d.uploaded_by = u.user_id
            WHERE d.uploaded_by = ?
            ORDER BY d.updated_at DESC
        ");
        $stmt->bind_param("i", $user_id);
    } elseif ($role_id == 2) { // ROLE_HOD
        $stmt = $conn->prepare("
            SELECT d.*, dt.type_name, dc.category_name, dep.dept_name, ay.year_name,
                   u.full_name as uploader_name
            FROM Documents d
            JOIN Document_Types dt ON d.type_id = dt.type_id
            JOIN Document_Categories dc ON dt.category_id = dc.category_id
            JOIN Dept dep ON d.dept_id = dep.dept_id
            JOIN Academic_Years ay ON d.academic_year_id = ay.academic_year_id
            JOIN Users u ON d.uploaded_by = u.user_id
            WHERE d.current_role_id = ? AND d.dept_id = ? AND d.status = 'Pending'
            ORDER BY d.updated_at DESC
        ");
        $stmt->bind_param("ii", $role_id, $dept_id);
    } elseif ($role_id == 1) { // ROLE_ADMIN
        $stmt = $conn->prepare("
            SELECT d.*, dt.type_name, dc.category_name, dep.dept_name, ay.year_name,
                   u.full_name as uploader_name
            FROM Documents d
            JOIN Document_Types dt ON d.type_id = dt.type_id
            JOIN Document_Categories dc ON dt.category_id = dc.category_id
            JOIN Dept dep ON d.dept_id = dep.dept_id
            JOIN Academic_Years ay ON d.academic_year_id = ay.academic_year_id
            JOIN Users u ON d.uploaded_by = u.user_id
            ORDER BY d.updated_at DESC
        ");
    } else {
        $stmt = $conn->prepare("
            SELECT d.*, dt.type_name, dc.category_name, dep.dept_name, ay.year_name,
                   u.full_name as uploader_name
            FROM Documents d
            JOIN Document_Types dt ON d.type_id = dt.type_id
            JOIN Document_Categories dc ON dt.category_id = dc.category_id
            JOIN Dept dep ON d.dept_id = dep.dept_id
            JOIN Academic_Years ay ON d.academic_year_id = ay.academic_year_id
            JOIN Users u ON d.uploaded_by = u.user_id
            WHERE d.uploaded_by = ?
            ORDER BY d.updated_at DESC
        ");
        $stmt->bind_param("i", $user_id);
    }

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $documents[] = $row;
        }
        $stmt->close();
    }
}
?>

<link rel="stylesheet" href="<?php echo (isset($base_url) ? $base_url : '../') . '../temp/includes/css/dashboard.css'; ?>">
<style>
/* Scoped overrides to prevent dashboard.css from breaking legacy grid */
.dash-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    margin-top: 40px;
    margin-bottom: 40px;
}
.dash-card h2 {
    color: #1e293b;
    margin-bottom: 20px;
    font-size: 1.5rem;
}
</style>

<div class="dash-card">
    <h2>Recent Uploads & Pending Actions</h2>

    <?php if (empty($documents)): ?>
        <div class="empty-state" style="text-align: center; padding: 40px; color: #64748b;">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 15px auto;">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
            </svg>
            <p>No modern documents found in this view.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
        <table class="dash-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; text-align: left; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px 15px; color: #475569;">File</th>
                    <th style="padding: 12px 15px; color: #475569;">Type</th>
                    <th style="padding: 12px 15px; color: #475569;">Uploaded By</th>
                    <th style="padding: 12px 15px; color: #475569;">Date</th>
                    <th style="padding: 12px 15px; color: #475569;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                    <?php
                    $badge_class = 'badge-pending';
                    $color = '#eab308';
                    $bg = '#fef9c3';
                    if ($doc['status'] === 'Approved') {
                        $badge_class = 'badge-approved';
                        $color = '#22c55e';
                        $bg = '#dcfce7';
                    } elseif ($doc['status'] === 'Rejected') {
                        $badge_class = 'badge-rejected';
                        $color = '#ef4444';
                        $bg = '#fee2e2';
                    }
                    $file_url = (isset($base_url) ? $base_url : '../') . htmlspecialchars($doc['file_path']);
                    // If it was uploaded from temp/, the path might be correct. If it breaks, it can be adjusted.
                    ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px 15px;">
                            <a href="<?php echo $file_url; ?>" target="_blank" style="color: #3b82f6; text-decoration: none; font-weight: 500;">
                                <?php echo htmlspecialchars($doc['original_file_name']); ?>
                            </a>
                        </td>
                        <td style="padding: 12px 15px;"><?php echo htmlspecialchars($doc['type_name']); ?></td>
                        <td style="padding: 12px 15px;"><?php echo htmlspecialchars($doc['uploader_name']); ?></td>
                        <td style="padding: 12px 15px;"><?php echo date('M d, Y', strtotime($doc['updated_at'])); ?></td>
                        <td style="padding: 12px 15px;">
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 0.85rem; font-weight: 600; color: <?php echo $color; ?>; background-color: <?php echo $bg; ?>;">
                                <?php echo htmlspecialchars($doc['status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>
