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
        global $conn;

        $scope_label = 'Documents';
        $forced_filters = [];

        switch ($active_role_id) {
            case ROLE_ADMIN:
                $scope_label = 'All Documents';
                break;
            case ROLE_RND_DEAN:
                $scope_label = 'Research Documents';
                $forced_filters['category'] = 'research';
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
                if (isset($_GET['context']) && $_GET['context'] === 'dept_file') {
                    $forced_filters['type_key'] = 'dept_file';
                    $scope_label = htmlspecialchars($dept_name) . ' Department Files';
                }
                break;
            case ROLE_CENTRAL_COORDINATOR:
                $forced_filters['category'] = 'central';
                $scope_label = 'Central Documents';
                break;
            case ROLE_FACULTY:
            default:
                $forced_filters['uploaded_by'] = $user_id;
                // If they specifically requested dept_file from the dashboard, show Dept Files.
                // Otherwise, the default context for Faculty list views is ALWAYS 'My Achievements'.
                if (isset($_GET['context']) && $_GET['context'] === 'dept_file') {
                    $forced_filters['type_key'] = 'dept_file';
                    $scope_label = 'My Dept Files';
                } else {
                    $forced_filters['category'] = 'research';
                    $scope_label = 'My Achievements';
                }
                break;
        }

        $filter_type   = isset($_GET['type'])   ? trim($_GET['type'])   : '';
        $filter_subtype= isset($_GET['sub_type']) ? trim($_GET['sub_type']) : '';
        
        if ($filter_subtype !== '') {
            $scope_label = htmlspecialchars($filter_subtype) . ' - ' . $scope_label;
        }
        $filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $filter_year   = isset($_GET['year'])   ? (int)$_GET['year']   : 0;
        $filter_search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $filter_dept   = isset($_GET['dept_id']) ? (int)$_GET['dept_id'] : 0;
        $page_num      = max(1, (int)($_GET['page'] ?? 1));
        $per_page      = 25;
        $offset        = ($page_num - 1) * $per_page;

        $filters = $forced_filters;
        if ($filter_type !== '')   $filters['type_key'] = $filter_type;
        if ($filter_subtype !== '') $filters['sub_type'] = $filter_subtype;
        if ($filter_status !== '') $filters['status'] = $filter_status;
        if ($filter_year > 0)     $filters['year_id'] = $filter_year;
        if ($filter_search !== '') $filters['search'] = $filter_search;
        if ($filter_dept > 0)      $filters['dept_id'] = $filter_dept;
        
        // Fetch all departments for filtering if central role
        $all_departments = [];
        if (in_array($active_role_id, [ROLE_ADMIN, ROLE_RND_DEAN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR])) {
            $d_res = $conn->query("SELECT dept_id, dept_name FROM departments ORDER BY dept_name ASC");
            if ($d_res) {
                while($row = $d_res->fetch_assoc()) {
                    $all_departments[] = $row;
                }
            }
        }

        $mode = isset($_GET['mode']) ? trim($_GET['mode']) : '';
        if ($mode === 'pending_approval') {
            $result = doc_list_pending_for_user($conn, $auth, $per_page, $offset);
            $documents = $result['rows'];
            $scope_label = 'Pending My Approval';
        } else {
            $result = doc_list($conn, $filters, $per_page, $offset);
            $documents = $result['rows'];
        }

        $total = $result['total'];
        $total_pages = (int)ceil($total / $per_page);

        $full_types = doc_get_types($conn);
        $all_types = [];
        
        // If a specific category or type is forced, filter the dropdown accordingly
        if (!empty($forced_filters['type_key'])) {
            foreach ($full_types as $t) {
                if ($t['type_key'] === $forced_filters['type_key'] && $t['is_active'] == 1) {
                    $all_types[] = $t;
                }
            }
        } elseif (!empty($forced_filters['category'])) {
            foreach ($full_types as $t) {
                if ($t['category'] === $forced_filters['category'] && $t['is_active'] == 1) {
                    $all_types[] = $t;
                }
            }
        } else {
            // Otherwise, show all active types
            foreach ($full_types as $t) {
                if ($t['is_active'] == 1) {
                    $all_types[] = $t;
                }
            }
        }

        $academic_years = doc_get_academic_years($conn);

        $meta_fields = [];
        if ($filter_type !== '') {
            $meta_fields = meta_get_fields($filter_type);
        }

        $pending_result = doc_list_pending_for_user($conn, $auth, 100, 0);
        $pending_count = $pending_result['total'];

        include __DIR__ . '/../Views/documents/list.php';
    }

    public function myUploads() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        $user_id = (int)$auth['user_id'];
        global $conn;

        $filter_type   = isset($_GET['type'])   ? trim($_GET['type'])   : '';
        $filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $filter_year   = isset($_GET['year'])   ? (int)$_GET['year']   : 0;

        $page_num = max(1, (int)($_GET['page'] ?? 1));
        $per_page = 20;
        $offset = ($page_num - 1) * $per_page;

        $filters = ['uploaded_by' => $user_id];
        if ($filter_type !== '')   $filters['type_key'] = $filter_type;
        if ($filter_status !== '') $filters['status'] = $filter_status;
        if ($filter_year > 0)     $filters['year_id'] = $filter_year;

        $result = doc_list($conn, $filters, $per_page, $offset);
        $documents = $result['rows'];
        $total = $result['total'];
        $total_pages = (int)ceil($total / $per_page);

        // Fetch active types to populate dropdown
        $full_types = doc_get_types($conn);
        $all_types = [];
        foreach ($full_types as $t) {
            if ($t['is_active'] == 1) {
                $all_types[] = $t;
            }
        }

        $academic_years = doc_get_academic_years($conn);

        $pending_result = doc_list_pending_for_user($conn, $auth, 100, 0);
        $pending_count = $pending_result['total'];

        include __DIR__ . '/../Views/documents/my_uploads.php';
    }

    public function uploadForm() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        global $conn;

        $types = doc_get_types($conn);
        $academic_years = doc_get_academic_years($conn);

        // Fetch central categories for CC role
        $central_categories = [];
        $res = $conn->query("SELECT * FROM central_categories ORDER BY category_name");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $central_categories[] = $row;
            }
        }

        $preselected_type = isset($_GET['type']) ? trim($_GET['type']) : '';
        $preselected_subtype = isset($_GET['sub_type']) ? trim($_GET['sub_type']) : '';
        
        $doc_type = null;
        $type_key = '';
        $meta_fields = [];
        $file_slots = [];
        $page_title = 'Upload Document';

        if ($preselected_type !== '') {
            foreach ($types as $t) {
                if ($t['type_key'] === $preselected_type) {
                    $doc_type = $t;
                    $type_key = $t['type_key'];
                    $page_title = 'Upload: ' . $t['type_label'];
                    if (isset($_GET['activity'])) {
                        $page_title = 'Upload: ' . trim($_GET['activity']);
                    } elseif (isset($_GET['exam'])) {
                        $page_title = 'Upload: ' . trim($_GET['exam']);
                    } elseif ($preselected_subtype !== '' && $preselected_subtype !== $t['type_label']) {
                        $page_title .= ' - ' . $preselected_subtype;
                    }
                    break;
                }
            }
        }

        if ($doc_type) {
            $meta_fields = meta_get_fields($type_key);
            
            $file_slots = meta_get_file_slots($type_key);
            
            $user_depts = [];
            foreach ($auth['roles'] as $r) {
                if (!empty($r['dept_id']) && $r['dept_id'] > 0 && !empty($r['dept_name'])) {
                    $user_depts[$r['dept_id']] = $r['dept_name'];
                }
            }
            if (empty($user_depts)) {
                $dres = $conn->query("SELECT dept_id, dept_name FROM departments");
                if ($dres) {
                    while($row = $dres->fetch_assoc()) {
                        $user_depts[$row['dept_id']] = $row['dept_name'];
                    }
                }
            }
            
            $active_year = null;
            foreach ($academic_years as $y) {
                if (!empty($y['is_active']) || !empty($y['current'])) {
                    $active_year = $y;
                    break;
                }
            }
        }

        $success_msg = $_SESSION['success_msg'] ?? '';
        $error_msg = $_SESSION['error_msg'] ?? '';
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        include __DIR__ . '/../Views/documents/upload.php';
    }

    public function edit() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        global $conn;

        $doc_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$doc_id) {
            die("Invalid document ID.");
        }

        $document = doc_get($conn, $doc_id);
        if (!$document) {
            die("Document not found.");
        }

        // Only uploader can edit, and only if pending or rejected
        if ((int)$document['uploaded_by'] !== (int)$auth['user_id']) {
            die("Unauthorized to edit this document.");
        }
        if (strtolower($document['status']) === 'accepted') {
            die("Cannot edit an accepted document.");
        }

        $type_key = $document['type_key'];
        $meta_data = doc_get_meta($conn, $doc_id, $type_key) ?? [];
        require_once __DIR__ . '/../../core/file_service.php';
        $files = file_get_by_document($conn, $doc_id);
        
        // Build map of existing slots
        $existing_files_map = [];
        foreach ($files as $f) {
            $existing_files_map[$f['file_label']] = $f;
        }

        $meta_fields = meta_get_fields($type_key);
        $file_slots = meta_get_file_slots($type_key);
        
        $user_depts = [];
        foreach ($auth['roles'] as $r) {
            if (!empty($r['dept_id']) && $r['dept_id'] > 0 && !empty($r['dept_name'])) {
                $user_depts[$r['dept_id']] = $r['dept_name'];
            }
        }
        if (empty($user_depts)) {
            $dres = $conn->query("SELECT dept_id, dept_name FROM departments");
            if ($dres) {
                while($row = $dres->fetch_assoc()) {
                    $user_depts[$row['dept_id']] = $row['dept_name'];
                }
            }
        }
        
        $academic_years = doc_get_academic_years($conn);

        $success_msg = $_SESSION['success_msg'] ?? '';
        $error_msg = $_SESSION['error_msg'] ?? '';
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        include __DIR__ . '/../Views/documents/edit.php';
    }

    public function view() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        $doc_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        global $conn;

        if ($doc_id <= 0) {
            die("Invalid document ID.");
        }

        $document = doc_get($conn, $doc_id);
        if (!$document) {
            die("Document not found.");
        }

        if (!doc_can_view($conn, $auth, $document)) {
            die("You do not have permission to view this document.");
        }

        $type_key = $document['type_key'];
        $page_title = 'View: ' . $document['title'];

        // Get meta data
        $meta_data = doc_get_meta($conn, $doc_id, $type_key);
        $meta_fields = meta_get_fields($type_key);

        // Get files
        require_once __DIR__ . '/../../core/file_service.php';
        $files = file_get_by_document($conn, $doc_id);

        // Get workflow history and actions
        $action_history = doc_get_history($conn, $doc_id);
        
        require_once __DIR__ . '/../../core/workflow_engine.php';
        $allowed_actions = wf_get_allowed_actions($conn, $document, $auth);
        $status_label = wf_status_label($conn, $document);

        include __DIR__ . '/../Views/documents/view.php';
    }
}
