<?php
include_once '../config.php';
include_once CONNECTION_PATH;
include_once INCLUDES_PATH . '/helpers.php';

$dept_param = isset($_GET['dept']) ? trim($_GET['dept']) : '';

// Lookup department in database
$dept_info = null;
if ($dept_param !== '') {
    $stmt = $conn->prepare("SELECT * FROM departments WHERE dept_name = ?");
    $stmt->bind_param("s", $dept_param);
    $stmt->execute();
    $dept_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// If dept not found, handle it properly rather than falling back to random department
if (!$dept_info) {
    echo "<div style='text-align:center; padding: 50px; font-family:sans-serif;'>";
    echo "<h2>Department Not Found</h2>";
    echo "<p>The department '" . htmlspecialchars($dept_param) . "' does not exist in our records.</p>";
    echo "<a href='../index.php' style='color:#3b82f6;'>Return to Home</a>";
    echo "</div>";
    exit;
}

$dept_id = $dept_info ? (int) $dept_info['dept_id'] : 0;
$dept_name = $dept_info ? $dept_info['dept_name'] : 'Department';

// Fetch active faculty members in this department
$faculty = [];
if ($dept_id > 0) {
    $stmt = $conn->prepare("
        SELECT u.*, r.role_name
        FROM users u
        JOIN user_roles ur ON u.user_id = ur.user_id
        JOIN roles r ON ur.role_id = r.role_id
        WHERE ur.dept_id = ? AND u.status = 'active' AND r.role_name IN ('Faculty', 'HOD')
        GROUP BY u.user_id
        ORDER BY u.full_name
    ");
    $stmt->bind_param("i", $dept_id);
    $stmt->execute();
    $fres = $stmt->get_result();
    while ($row = $fres->fetch_assoc()) {
        $faculty[] = $row;
    }
    $stmt->close();
}

$public_docs = [];
if ($dept_id > 0) {
    $stmt = $conn->prepare("
        SELECT dm.meta_value as original_file_name, dt.label as type_name, 
               u.full_name as uploader_name, ay.year_label as year_name, 
               d.created_at, d.file_path 
        FROM documents d
        LEFT JOIN document_types dt ON d.type_id = dt.type_id
        LEFT JOIN users u ON d.uploaded_by = u.user_id
        LEFT JOIN academic_years ay ON d.academic_year_id = ay.year_id
        LEFT JOIN document_metadata dm ON d.doc_id = dm.doc_id AND dm.meta_key = 'title'
        WHERE d.dept_id = ? AND d.status = 'accepted'
        ORDER BY d.created_at DESC
    ");
    $stmt->bind_param("i", $dept_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) { 
        if(empty($row['file_path'])) $row['file_path'] = '#';
        $public_docs[] = $row; 
    }
    $stmt->close();
}

// Calculate research stats
$papers_count = 0;
$patents_count = 0;
$fdps_count = 0;

foreach ($public_docs as $doc) {
    if (stripos($doc['type_name'], 'Paper') !== false || stripos($doc['type_name'], 'Conference') !== false) {
        $papers_count++;
    } elseif (stripos($doc['type_name'], 'Patent') !== false) {
        $patents_count++;
    } elseif (stripos($doc['type_name'], 'FDP') !== false) {
        $fdps_count++;
    }
}

include_once HEADER;
?>
<link rel="stylesheet" href="<?php echo CSS_PATH . '/portal.css'; ?>">
<link rel="stylesheet" href="<?php echo CSS_PATH . '/dashboard.css'; ?>">
<style>
    .dept-hero {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.92), rgba(30, 41, 59, 0.95)), url('../assets/images/Landing.jpg');
        background-size: cover;
        background-position: center;
        padding: 50px 30px;
        border-radius: 16px;
        border: 1px solid #334155;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .dept-hero h1 {
        font-size: 2.4em;
        color: #f8fafc;
        margin: 0 0 10px 0;
    }
    .dept-hero h1 span {
        color: #60a5fa;
        font-weight: 400;
    }
    .dept-hero p {
        color: #94a3b8;
        font-size: 1.1em;
        margin: 0;
        max-width: 700px;
        line-height: 1.6;
    }
    .faculty-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 20px;
        margin-top: 15px;
    }
    .faculty-card {
        background: #0f172a;
        border: 1px solid #334155;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: transform 0.2s, border-color 0.2s;
    }
    .faculty-card:hover {
        transform: translateY(-3px);
        border-color: #60a5fa;
    }
    .faculty-card h4 {
        margin: 10px 0 4px 0;
        color: #f1f5f9;
        font-size: 1.05em;
    }
    .faculty-card p {
        margin: 0;
        color: #60a5fa;
        font-size: 0.85em;
        font-weight: 500;
    }
    .faculty-card span {
        color: #64748b;
        font-size: 0.8em;
        display: block;
        margin-top: 6px;
    }
</style>
<body class="dashboard-page">
    <div class="dash-container">
        <!-- Department Banner -->
        <div class="dept-hero">
            <h1><?php echo htmlspecialchars(str_replace('_', ' ', $dept_name)); ?> <span>Department</span></h1>
            <p>Welcome to the official repository and research achievements page for the Department of <?php echo htmlspecialchars(str_replace('_', ' ', $dept_name)); ?> at GMRIT.</p>

            <?php if (isLoggedIn() && (int)($_SESSION['dept_id'] ?? 0) === $dept_id && in_array((int)($_SESSION['role_id'] ?? 0), [ROLE_COORDINATOR, ROLE_HOD])): ?>
                <div style="margin-top:20px;">
                    <a href="<?php echo PORTAL_PATH; ?>/dept_coordinator/dc_acd_year.php" class="btn-primary" style="text-decoration:none; font-size:0.9em; padding:10px 22px; display:inline-block;">
                        ⚙️ Manage Department Repository
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Research Stats -->
        <div class="dash-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($faculty); ?></div>
                <div class="stat-label">Faculty Members</div>
            </div>
            <div class="stat-card approved">
                <div class="stat-number"><?php echo $papers_count; ?></div>
                <div class="stat-label">Published Papers & Conf</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color:#a855f7;"><?php echo $patents_count; ?></div>
                <div class="stat-label">Patents Filed / Granted</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number"><?php echo $fdps_count; ?></div>
                <div class="stat-label">FDPS Attended / Org</div>
            </div>
        </div>

        <!-- Faculty Members Section -->
        <div class="dash-card">
            <h2>Faculty Members (<?php echo count($faculty); ?>)</h2>
            <?php if (empty($faculty)): ?>
                <div class="empty-state"><p>No faculty members listed for this department yet.</p></div>
            <?php else: ?>
                <div class="faculty-grid">
                    <?php foreach ($faculty as $f): ?>
                        <div class="faculty-card">
                            <?php if (!empty($f['profile_photo'])): ?>
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($f['profile_photo']); ?>" 
                                     alt="<?php echo htmlspecialchars($f['full_name']); ?>" 
                                     style="width:70px; height:70px; border-radius:50%; object-fit:cover; border:2px solid #3b82f6; margin:0 auto;">
                            <?php else: ?>
                                <div style="width:70px; height:70px; border-radius:50%; background:#1e293b; display:inline-flex; align-items:center; justify-content:center; font-size:1.8em; color:#60a5fa; border:2px solid #334155; margin:0 auto;">
                                    <?php echo strtoupper(substr($f['full_name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <h4><?php echo htmlspecialchars($f['full_name']); ?></h4>
                            <p><?php echo htmlspecialchars($f['role_name']); ?></p>
                            <span><?php echo htmlspecialchars($f['email']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Public Research & Achievements -->
        <div class="dash-card">
            <h2>Published Research & Achievements (<?php echo count($public_docs); ?>)</h2>
            <p style="color:#94a3b8; font-size:0.9em; margin-top:-10px; margin-bottom:20px;">
                Showing verified, publicly accessible publications, patents, and faculty development records.
            </p>

            <?php if (empty($public_docs)): ?>
                <div class="empty-state">
                    <p>No public research documents approved for this department yet.</p>
                </div>
            <?php else: ?>
                <div style="overflow-x:auto;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Document Title / File</th>
                            <th>Category Type</th>
                            <th>Faculty Member</th>
                            <th>Academic Year</th>
                            <th>Verified Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($public_docs as $doc): ?>
                            <tr>
                                <td style="font-weight:500;">
                                    <?php echo htmlspecialchars($doc['original_file_name']); ?>
                                </td>
                                <td>
                                    <span class="badge badge-approved" style="background:rgba(59,130,246,0.15); color:#60a5fa; border-color:rgba(59,130,246,0.3);">
                                        <?php echo htmlspecialchars($doc['type_name']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($doc['uploader_name']); ?></td>
                                <td><?php echo htmlspecialchars($doc['year_name']); ?></td>
                                <td><?php echo date('d M Y', strtotime($doc['created_at'])); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL . '/public/view_public_file.php?file_path=' . urlencode($doc['file_path']); ?>" 
                                       target="_blank" class="btn-action btn-reupload" style="text-decoration:none; padding:6px 14px;">
                                       📄 View File
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>