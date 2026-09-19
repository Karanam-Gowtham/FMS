<?php
namespace App\Controllers;

class DocumentController {
    
    public function index() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        $user_id = (int)$auth['user_id'];
        $active_role = $auth['active_role'] ?? null;
        $active_role_id = $active_role ? (int)$active_role['role_id'] : 0;
        $active_dept_id = $active_role ? (int)$active_role['dept_id'] : 0;
        $conn = db_connect();

        $scope_label = 'Documents';
        $forced_filters = [];

        switch ($active_role_id) {
            case ROLE_ADMIN:
            case ROLE_RND_DEAN:
                $scope_label = 'All Documents';
                break;
            case ROLE_IQAC:
                $scope_label = 'All Documents (IQAC)';
                break;
            case ROLE_HOD:
            case ROLE_DEPT_COORDINATOR:
            case ROLE_JUNIOR_ASSISTANT:
                $forced_filters['dept_id'] = $active_dept_id;
                $dept_stmt = $conn->prepare("SELECT dept_name FROM departments WHERE dept_id = ?");
                $dept_stmt->bind_param('i', $active_dept_id);
                $dept_stmt->execute();
                $dept_name = $dept_stmt->get_result()->fetch_assoc()['dept_name'] ?? 'Unknown';
                $dept_stmt->close();
                $scope_label = htmlspecialchars($dept_name) . ' Documents';
                break;
            case ROLE_CENTRAL_COORDINATOR:
                $forced_filters['category'] = 'central';
                $scope_label = 'Central Documents';
                break;
            case ROLE_FACULTY:
            default:
                $forced_filters['uploaded_by'] = $user_id;
                $scope_label = 'My Documents';
                break;
        }

        $filter_type   = isset($_GET['type'])   ? trim($_GET['type'])   : '';
        $filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $filter_year   = isset($_GET['year'])   ? (int)$_GET['year']   : 0;
        $filter_search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $page_num      = max(1, (int)($_GET['page'] ?? 1));
        $per_page      = 25;
        $offset        = ($page_num - 1) * $per_page;

        $filters = $forced_filters;
        if ($filter_type !== '')   $filters['type_key'] = $filter_type;
        if ($filter_status !== '') $filters['status'] = $filter_status;
        if ($filter_year > 0)     $filters['year_id'] = $filter_year;
        if ($filter_search !== '') $filters['search'] = $filter_search;

        $result = doc_list($conn, $filters, $per_page, $offset);
        $documents = $result['rows'];
        $total = $result['total'];
        $total_pages = (int)ceil($total / $per_page);

        $all_types = doc_get_types($conn);
        $academic_years = doc_get_academic_years($conn);

        $pending_result = doc_list_pending_for_user($conn, $auth, 100, 0);
        $pending_count = $pending_result['total'];

        include __DIR__ . '/../Views/documents/list.php';
    }

    public function myUploads() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        $user_id = (int)$auth['user_id'];
        $conn = db_connect();

        $page_num = max(1, (int)($_GET['page'] ?? 1));
        $per_page = 20;
        $offset = ($page_num - 1) * $per_page;

        $filters = ['uploaded_by' => $user_id];
        $result = doc_list($conn, $filters, $per_page, $offset);
        $documents = $result['rows'];
        $total = $result['total'];
        $total_pages = (int)ceil($total / $per_page);

        $pending_result = doc_list_pending_for_user($conn, $auth, 100, 0);
        $pending_count = $pending_result['total'];

        include __DIR__ . '/../Views/documents/my_uploads.php';
    }

    public function uploadForm() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        $conn = db_connect();

        $types = doc_get_types($conn);
        $years = doc_get_academic_years($conn);

        // Fetch central categories for CC role
        $central_categories = [];
        $res = $conn->query("SELECT * FROM central_categories ORDER BY category_name");
        while ($row = $res->fetch_assoc()) {
            $central_categories[] = $row;
        }

        include __DIR__ . '/../Views/documents/upload.php';
    }

    public function view() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        $doc_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $conn = db_connect();

        if ($doc_id <= 0) {
            die("Invalid document ID.");
        }

        $doc = doc_get_by_id($conn, $doc_id);
        if (!$doc) {
            die("Document not found.");
        }

        if (!doc_can_view($conn, $auth, $doc)) {
            die("You do not have permission to view this document.");
        }

        $history = doc_get_history($conn, $doc_id);
        $can_approve = doc_can_approve($conn, $auth, $doc);

        include __DIR__ . '/../Views/documents/view.php';
    }
}
