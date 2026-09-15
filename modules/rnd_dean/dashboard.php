<?php
include_once '../../config.php';
include_once CONNECTION_PATH;
include_once INCLUDES_PATH . '/helpers.php';
requireLogin();

// Verify role
$role_id = $_SESSION['role_id'] ?? 0;
if (!$role_id) {
    if (isset($_SESSION['roles'])) {
        foreach ($_SESSION['roles'] as $r) {
            if ($r['role_id'] == ROLE_RND_DEAN) {
                $role_id = ROLE_RND_DEAN;
                break;
            }
        }
    }
}
if ($role_id != ROLE_RND_DEAN) {
    die("Access denied. R&D Dean role required.");
}

$departments = getDepartments($conn);
$years = getAcademicYears($conn);
$active_year = getActiveAcademicYear($conn);

$filter_dept = $_GET['dept'] ?? 'All';
$filter_year = $_GET['year'] ?? ($active_year['year_name'] ?? '');

$pending_docs = [];
$central_docs = [];

// Fetch pending legacy R&D docs
$dept_filter_sql = ($filter_dept !== 'All') ? "AND branch = '" . $conn->real_escape_string($filter_dept) . "'" : "";
$year_filter_sql = ($filter_year !== '') ? "AND year = '" . $conn->real_escape_string($filter_year) . "'" : "";

// Published Tab
$res = $conn->query("SELECT id, paper_title as title, 'Journal' as type, branch, year, status, paper_file as file_path, username as author FROM published_tab WHERE status = 'Pending Dean' $dept_filter_sql $year_filter_sql");
if($res) while($r = $res->fetch_assoc()) { $r['table'] = 'published_tab'; $pending_docs[] = $r; }

// Conference Tab
$res = $conn->query("SELECT id, paper_title as title, 'Conference' as type, branch, year, status, paper_file_path as file_path, username as author FROM conference_tab WHERE status = 'Pending Dean' $dept_filter_sql $year_filter_sql");
if($res) while($r = $res->fetch_assoc()) { $r['table'] = 'conference_tab'; $pending_docs[] = $r; }

// Patents Table
$res = $conn->query("SELECT id, patent_title as title, 'Patent' as type, branch, year, status, patent_file as file_path, Username as author FROM patents_table WHERE status = 'Pending Dean' $dept_filter_sql $year_filter_sql");
if($res) while($r = $res->fetch_assoc()) { $r['table'] = 'patents_table'; $pending_docs[] = $r; }

$approved_docs = [];
// Fetch approved legacy R&D docs
$res = $conn->query("SELECT id, paper_title as title, 'Journal' as type, branch, year, status, paper_file as file_path, username as author FROM published_tab WHERE status IN ('Approved by Dean', 'Accepted') $dept_filter_sql $year_filter_sql");
if($res) while($r = $res->fetch_assoc()) { $r['table'] = 'published_tab'; $approved_docs[] = $r; }

$res = $conn->query("SELECT id, paper_title as title, 'Conference' as type, branch, year, status, paper_file_path as file_path, username as author FROM conference_tab WHERE status IN ('Approved by Dean', 'Accepted') $dept_filter_sql $year_filter_sql");
if($res) while($r = $res->fetch_assoc()) { $r['table'] = 'conference_tab'; $approved_docs[] = $r; }

$res = $conn->query("SELECT id, patent_title as title, 'Patent' as type, branch, year, status, patent_file as file_path, Username as author FROM patents_table WHERE status IN ('Approved by Dean', 'Accepted') $dept_filter_sql $year_filter_sql");
if($res) while($r = $res->fetch_assoc()) { $r['table'] = 'patents_table'; $approved_docs[] = $r; }

// Fetch Central Documents
$res = $conn->query("SELECT * FROM rnd_central_documents ORDER BY upload_date DESC");
if($res) while($r = $res->fetch_assoc()) { $central_docs[] = $r; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>R&D Dean Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 10px; border: none; }
        .tab-content { border-radius: 0 0 10px 10px; }
    </style>
</head>
<body>
<?php include_once HEADER; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>R&D Dean Dashboard</h2>
        <a href="upload.php" class="btn btn-primary">Upload Central Document</a>
    </div>

    <!-- Filters -->
    <div class="card p-3 mb-4 shadow-sm">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold">Department</label>
                <select name="dept" class="form-select">
                    <option value="All" <?php echo $filter_dept === 'All' ? 'selected' : ''; ?>>All Departments</option>
                    <?php foreach($departments as $d): ?>
                        <option value="<?php echo htmlspecialchars($d['dept_name']); ?>" <?php echo $filter_dept === $d['dept_name'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($d['dept_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Academic Year</label>
                <select name="year" class="form-select">
                    <?php foreach($years as $y): ?>
                        <option value="<?php echo htmlspecialchars($y['year_name']); ?>" <?php echo $filter_year === $y['year_name'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($y['year_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
            </div>
        </form>
    </div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending" type="button">Pending Review (<?php echo count($pending_docs); ?>)</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#approved" type="button">Approved Documents (<?php echo count($approved_docs); ?>)</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#central" type="button">Central Documents (<?php echo count($central_docs); ?>)</button>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content border border-top-0 p-3 bg-white" id="myTabContent">
        <!-- Pending Review Tab -->
        <div class="tab-pane fade show active" id="pending">
            <?php if(empty($pending_docs)): ?>
                <div class="alert alert-info">No pending documents for review.</div>
            <?php else: ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Dept</th>
                            <th>Year</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pending_docs as $doc): ?>
                            <tr>
                                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($doc['type']); ?></span></td>
                                <td>
                                    <?php if(!empty($doc['file_path'])): ?>
                                        <a href="<?php echo BASE_URL . '/' . htmlspecialchars($doc['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($doc['title']); ?></a>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($doc['title']); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($doc['author']); ?></td>
                                <td><?php echo htmlspecialchars($doc['branch']); ?></td>
                                <td><?php echo htmlspecialchars($doc['year']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="reviewDoc('<?php echo $doc['table']; ?>', <?php echo $doc['id']; ?>, 'accept')">Accept</button>
                                    <button class="btn btn-sm btn-danger" onclick="reviewDoc('<?php echo $doc['table']; ?>', <?php echo $doc['id']; ?>, 'reject')">Reject</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Approved Documents Tab -->
        <div class="tab-pane fade" id="approved">
            <?php if(empty($approved_docs)): ?>
                <div class="alert alert-info">No approved documents found for this filter.</div>
            <?php else: ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Dept</th>
                            <th>Year</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($approved_docs as $doc): ?>
                            <tr>
                                <td><span class="badge bg-success"><?php echo htmlspecialchars($doc['type']); ?></span></td>
                                <td>
                                    <?php if(!empty($doc['file_path'])): ?>
                                        <a href="<?php echo BASE_URL . '/' . htmlspecialchars($doc['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($doc['title']); ?></a>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($doc['title']); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($doc['author']); ?></td>
                                <td><?php echo htmlspecialchars($doc['branch']); ?></td>
                                <td><?php echo htmlspecialchars($doc['year']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($doc['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Central Documents Tab -->
        <div class="tab-pane fade" id="central">
            <?php if(empty($central_docs)): ?>
                <div class="alert alert-info">No central documents found.</div>
            <?php else: ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Title</th>
                            <th>Year</th>
                            <th>Date</th>
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($central_docs as $doc): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($doc['category']); ?></span></td>
                                <td><?php echo htmlspecialchars($doc['title']); ?></td>
                                <td><?php echo htmlspecialchars($doc['academic_year']); ?></td>
                                <td><?php echo htmlspecialchars($doc['upload_date']); ?></td>
                                <td><a href="<?php echo BASE_URL . '/' . htmlspecialchars($doc['file_path']); ?>" class="btn btn-sm btn-outline-primary" target="_blank">View File</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function reviewDoc(table, id, action) {
    let reason = '';
    if (action === 'reject') {
        reason = prompt("Please provide a reason for rejection:");
        if (reason === null) return; 
    }
    
    if (confirm("Are you sure you want to " + action + " this document?")) {
        let fd = new FormData();
        fd.append('table', table);
        fd.append('id', id);
        fd.append('action', action);
        fd.append('reason', reason);

        fetch('review_action.php', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(err => alert("Request failed."));
    }
}
</script>

</script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
