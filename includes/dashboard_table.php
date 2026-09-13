<?php
if (!isset($conn)) {
    include_once __DIR__ . '/connection.php';
}
include_once __DIR__ . '/helpers.php';
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
    if (isset($_SESSION['admin'])) $role_id = ROLE_ADMIN;
    elseif (isset($_SESSION['cri_username'])) $role_id = ROLE_IQAC;
    elseif (isset($_SESSION['h_username'])) $role_id = ROLE_HOD;
    elseif (isset($_SESSION['username'])) $role_id = ROLE_FACULTY;
    elseif (isset($_SESSION['a_username'])) $role_id = ROLE_COORDINATOR;
    elseif (isset($_SESSION['c_username']) || isset($_SESSION['c_cord'])) $role_id = ROLE_CENTRAL_COORDINATOR;
    elseif (isset($_SESSION['j_username'])) $role_id = ROLE_JUNIOR_ASSISTANT;
}

// Ensure the Documents table exists before querying
$documents = [];
$table_check = $conn->query("SHOW TABLES LIKE 'Documents'");
if ($table_check && $table_check->num_rows > 0) {
    if ($role_id == ROLE_FACULTY) { // Faculty
        $stmt = $conn->prepare("
            SELECT d.*, dt.type_label as type_name, dt.category as category_name, dep.dept_name, ay.year_label as year_name,
                   u.full_name as uploader_name
            FROM documents d
            LEFT JOIN document_types dt ON d.doc_type_id = dt.type_id
            LEFT JOIN dept dep ON d.dept_id = dep.dept_id
            LEFT JOIN academic_years ay ON d.year_id = ay.year_id
            LEFT JOIN users u ON d.uploaded_by = u.user_id
            WHERE d.uploaded_by = ?
            ORDER BY d.updated_at DESC
        ");
        $stmt->bind_param("i", $user_id);
    } elseif ($role_id == ROLE_HOD || $role_id == ROLE_COORDINATOR) { // HOD or Dept Coordinator
        $stmt = $conn->prepare("
            SELECT d.*, dt.type_label as type_name, dt.category as category_name, dep.dept_name, ay.year_label as year_name,
                   u.full_name as uploader_name
            FROM documents d
            LEFT JOIN document_types dt ON d.doc_type_id = dt.type_id
            LEFT JOIN dept dep ON d.dept_id = dep.dept_id
            LEFT JOIN academic_years ay ON d.year_id = ay.year_id
            LEFT JOIN users u ON d.uploaded_by = u.user_id
            LEFT JOIN workflow_steps ws ON d.current_step = ws.step_id
            WHERE ws.responsible_role_id = ? AND d.dept_id = ? AND d.status = 'pending'
            ORDER BY d.updated_at DESC
        ");
        $stmt->bind_param("ii", $role_id, $dept_id);
    } elseif ($role_id == ROLE_ADMIN || $role_id == ROLE_IQAC) { // Admin or IQAC
        $stmt = $conn->prepare("
            SELECT d.*, dt.type_label as type_name, dt.category as category_name, dep.dept_name, ay.year_label as year_name,
                   u.full_name as uploader_name
            FROM documents d
            LEFT JOIN document_types dt ON d.doc_type_id = dt.type_id
            LEFT JOIN dept dep ON d.dept_id = dep.dept_id
            LEFT JOIN academic_years ay ON d.year_id = ay.year_id
            LEFT JOIN users u ON d.uploaded_by = u.user_id
            ORDER BY d.updated_at DESC
        ");
    } else {
        $stmt = $conn->prepare("
            SELECT d.*, dt.type_label as type_name, dt.category as category_name, dep.dept_name, ay.year_label as year_name,
                   u.full_name as uploader_name
            FROM documents d
            LEFT JOIN document_types dt ON d.doc_type_id = dt.type_id
            LEFT JOIN dept dep ON d.dept_id = dep.dept_id
            LEFT JOIN academic_years ay ON d.year_id = ay.year_id
            LEFT JOIN users u ON d.uploaded_by = u.user_id
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
    
    // FETCH LEGACY PENDING DOCUMENTS
    if (function_exists('getLegacyPendingDocs')) {
        $d_name = '';
        if ($role_id == ROLE_HOD || $role_id == ROLE_COORDINATOR) {
            $stmt = $conn->prepare("SELECT dept_name FROM Dept WHERE dept_id = ?");
            $stmt->bind_param("i", $dept_id);
            $stmt->execute();
            $dres = $stmt->get_result()->fetch_assoc();
            if ($dres) {
                $d_name = $dres['dept_name'];
            }
            $stmt->close();
        }
        
        $email = $_SESSION['email'] ?? '';
        $username = $_SESSION['username'] ?? $_SESSION['h_username'] ?? $_SESSION['a_username'] ?? '';
        
        $legacy_docs = getLegacyPendingDocs($conn, $role_id, $d_name, $email, $username);
        
        // Append legacy docs to the main documents array
        foreach ($legacy_docs as $ldoc) {
            $documents[] = $ldoc;
        }
    }
}
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dashboard.css">
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
                <tr style="border-bottom: 2px solid #e2e8f0; text-align: left;">
                    <th style="padding: 12px 15px; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Document</th>
                    <th style="padding: 12px 15px; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Type</th>
                    <th style="padding: 12px 15px; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Uploader</th>
                    <th style="padding: 12px 15px; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Date</th>
                    <th style="padding: 12px 15px; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Status</th>
                    <th style="padding: 12px 15px; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Action</th>
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
                    $is_legacy = isset($doc['source_table']);
                    $doc_title = $is_legacy ? ($doc['original_file_name'] ?? 'Untitled') : ($doc['title'] ?? 'Untitled');
                    $did = $is_legacy ? ($doc['document_id'] ?? $doc['id'] ?? 0) : ($doc['doc_id'] ?? 0);
                    
                    if ($is_legacy) {
                        $raw_file = $doc['file_path'] ?? '';
                        $view_url = BASE_URL . '/modules/common/view_file1.php?file_path=' . urlencode($raw_file);
                    } else {
                        $view_url = BASE_URL . '/pages/documents/view.php?id=' . $did;
                    }
                    ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px 15px;">
                            <a href="<?php echo $view_url; ?>" target="_blank" style="color: #3b82f6; text-decoration: underline; font-weight: 500;" title="Click to view file">
                                <?php echo htmlspecialchars($doc_title); ?>
                            </a>
                        </td>
                        <td style="padding: 12px 15px;"><?php echo htmlspecialchars($doc['type_name']); ?></td>
                        <td style="padding: 12px 15px;"><?php echo htmlspecialchars($doc['uploader_name']); ?></td>
                        <td style="padding: 12px 15px;"><?php echo date('M d, Y', strtotime($doc['updated_at'])); ?></td>
                        <td style="padding: 12px 15px;">
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 0.85rem; font-weight: 600; color: <?php echo $color; ?>; background-color: <?php echo $bg; ?>;">
                                <?php echo htmlspecialchars(ucfirst($doc['status'])); ?>
                            </span>
                        </td>
                        <td style="padding: 12px 15px; white-space: nowrap;">
                            <a href="<?php echo $view_url; ?>" target="_blank" style="background:#3b82f6; color:white; border:none; padding:5px 10px; border-radius:4px; text-decoration:none; font-size:0.85rem; margin-right:5px; display:inline-block; font-weight:600;">View</a>
                            <?php 
                            $is_approver = ($role_id == ROLE_HOD || $role_id == ROLE_COORDINATOR || $role_id == ROLE_CENTRAL_COORDINATOR);
                            $can_approve = $is_approver && (stripos($doc['status'], 'pending') !== false);
                            
                            // Only show legacy inline approve/reject buttons for legacy documents
                            // Modern documents must be approved via the new view.php interface
                            if ($can_approve && $is_legacy): 
                                $table = $doc['source_table'];
                            ?>
                                <form action="<?php echo BASE_URL; ?>/includes/process_approval.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="document_id" value="<?php echo htmlspecialchars($did); ?>">
                                    <input type="hidden" name="source_table" value="<?php echo htmlspecialchars($table); ?>">
                                    <input type="hidden" name="action" value="Accept">
                                    <button type="submit" style="background:#22c55e; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer; font-size:0.85rem; margin-right:5px; font-weight:600;">Accept</button>
                                </form>
                                <form action="<?php echo BASE_URL; ?>/includes/process_approval.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="document_id" value="<?php echo htmlspecialchars($did); ?>">
                                    <input type="hidden" name="source_table" value="<?php echo htmlspecialchars($table); ?>">
                                    <input type="hidden" name="action" value="Reject">
                                    <button type="submit" style="background:#ef4444; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer; font-size:0.85rem; font-weight:600;">Reject</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>
