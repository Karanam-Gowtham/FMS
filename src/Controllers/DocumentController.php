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
                    $forced_filters['type_keys'] = ['dept_file', 'student_activity_file', 'student_journal', 'student_conference', 'student_body', 'exam_qual'];
                    $scope_label = 'My Dept Files';
                } else {
                    $forced_filters['category'] = 'research';
                    $scope_label = 'My Achievements';
                }
                break;
        }

        $filter_type   = isset($_GET['type'])   ? trim($_GET['type'])   : '';
        $filter_type_keys = isset($_GET['type_keys']) && is_array($_GET['type_keys']) ? $_GET['type_keys'] : [];
        $filter_subtype= isset($_GET['sub_type']) ? trim($_GET['sub_type']) : '';
        $filter_sub_types = isset($_GET['sub_types']) && is_array($_GET['sub_types']) ? $_GET['sub_types'] : [];
        $filter_sub_file_types = isset($_GET['sub_file_types']) && is_array($_GET['sub_file_types']) ? $_GET['sub_file_types'] : [];
        $filter_global_modes = isset($_GET['global_modes']) && is_array($_GET['global_modes']) ? $_GET['global_modes'] : [];
        $filter_global_domains = isset($_GET['global_domains']) && is_array($_GET['global_domains']) ? $_GET['global_domains'] : [];
        $filter_global_event_types = isset($_GET['global_event_types']) && is_array($_GET['global_event_types']) ? $_GET['global_event_types'] : [];
        
        if ($filter_subtype !== '') {
            $scope_label = htmlspecialchars($filter_subtype) . ' - ' . $scope_label;
        } elseif (!empty($filter_sub_types)) {
            $scope_label = 'Multiple Subtypes - ' . $scope_label;
        }

        $filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $filter_statuses = isset($_GET['statuses']) && is_array($_GET['statuses']) ? $_GET['statuses'] : [];
        $filter_year   = isset($_GET['year'])   ? (int)$_GET['year']   : 0;
        $filter_year_ids = isset($_GET['year_ids']) && is_array($_GET['year_ids']) ? array_map('intval', $_GET['year_ids']) : [];
        $filter_search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $filter_dept   = isset($_GET['dept_id']) ? (int)$_GET['dept_id'] : 0;
        $filter_dept_ids = isset($_GET['dept_ids']) && is_array($_GET['dept_ids']) ? array_map('intval', $_GET['dept_ids']) : [];
        $page_num      = max(1, (int)($_GET['page'] ?? 1));
        $per_page      = 25;
        $offset        = ($page_num - 1) * $per_page;

        $filters = $forced_filters;
        if ($filter_type !== '')   $filters['type_key'] = $filter_type;
        if (!empty($filter_type_keys)) $filters['type_keys'] = $filter_type_keys;
        if ($filter_subtype !== '') $filters['sub_type'] = $filter_subtype;
        if (!empty($filter_sub_types)) $filters['sub_types'] = $filter_sub_types;
        if (!empty($filter_sub_file_types)) $filters['sub_file_types'] = $filter_sub_file_types;
        if (!empty($filter_global_modes)) $filters['global_event_mode'] = $filter_global_modes;
        if (!empty($filter_global_domains)) $filters['global_topic_domain'] = $filter_global_domains;
        if (!empty($filter_global_event_types)) $filters['global_event_type'] = $filter_global_event_types;
        if ($filter_status !== '') $filters['status'] = $filter_status;
        if (!empty($filter_statuses)) $filters['statuses'] = $filter_statuses;
        if ($filter_year > 0)     $filters['year_id'] = $filter_year;
        if (!empty($filter_year_ids)) $filters['year_ids'] = $filter_year_ids;
        if ($filter_search !== '') $filters['search'] = $filter_search;
        if ($filter_dept > 0)      $filters['dept_id'] = $filter_dept;
        if (!empty($filter_dept_ids)) $filters['dept_ids'] = $filter_dept_ids;
        
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

        // Fix for missing category filter in doc_list
        if (!empty($filters['category']) && empty($filters['type_key']) && empty($filters['type_keys'])) {
            $cat_types = doc_get_types($conn);
            $type_keys = [];
            foreach ($cat_types as $t) {
                if ($t['category'] === $filters['category']) {
                    $type_keys[] = $t['type_key'];
                }
            }
            if (!empty($type_keys)) {
                $filters['type_keys'] = $type_keys;
            } else {
                $filters['type_key'] = 'none_match_category';
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

        $available_sub_file_types = [];
        $global_filter_options = ['modes' => [], 'domains' => [], 'event_types' => []];
        if (!empty($_GET['context']) && $_GET['context'] === 'dept_file') {
            $available_sub_file_types = [
                'Admin Files' => ['Course Structure', 'Result Analysis', 'Course Schedule', "Faculty's Feedback by Students", 'Feedback from Parents', 'Employer Feedback', 'Department Area Details', 'Departmental Laboratory Details', 'Major Equipment in the Laboratories', 'List of Experiments', 'Major Equipment Utilization Record', 'Equipment Maintenance Record', 'Courses Linked with Employability', 'Financial Statement/Budget Status', 'Departmental Library Details', 'Seminars/Workshops/Conferences Organized', 'Industrial Visits', 'Guest Lectures', 'List of Projects', 'Add-on Course/Training Conducted', 'Consultancy-New', 'External Sports and Projects', 'Transferrable and Life Skills Courses', 'Remedial Classes', 'Course End Feedback Form', 'Class Time Table', 'Faculty Time Table', 'Classroom Time Table', 'Lab Time Table', 'Student Progression to Higher Education', 'Feedback from Students/Alumni/Academic Peer', 'Course File-Index', 'Feedback on Curriculum from Students/Employer/Alumni', 'Workshops-Seminars on Research Methodology', 'Intellectual Property Rights (IPR)', 'Entrepreneurship-New', 'Professional Societies Chapters', 'Engineering Events Organized', 'Product Development Activities', 'Collaborative Activities', 'Functional MoUs with Ongoing Activities', 'Mini Project Work', 'Term Paper Work', 'Mentoring'],
                'Faculty Files' => ['Faculty List', 'Faculty Profile', 'Academic Research', 'Books and Chapters Published', 'Faculty in Inter-Departmental/Institutional Activities', 'Faculty for Higher Studies', 'Faculty Attended Seminars/Internships', 'Faculty Self-Appraisal', 'Non-Teaching Staff Skill Upgradation', 'Observations on Student Feedback', 'Full-Time Teachers with PhD Guidance', 'Consultancy and Corporate Training', 'Financial Support to Faculty', 'Publication of Technical Magazines/Newsletters'],
                'Student Related Files' => ['List of Forms', 'Student Addresses', 'Cumulative Monthly Attendance', 'Semester End Attendance', 'Condonation List', 'Detention List', 'Papers Published by Students', 'Students in Competitive Exams', 'Co-Curricular/Extra-Curricular Activities', 'Placement Record', 'Alumni Interaction', 'Field Projects/Internships', 'List of Seminars/Workshops Attended', 'Online Courses Completed', 'Coding/Hardware Competitions', 'Capacity Development Activities', 'Guidance for Competitive Exams', 'Career Counselling'],
                'Exam Section Files' => ['Notice for Internal Lab Exams', 'Invigilation Schedule', 'Absentee Statement', 'Sessional Marks Record', 'Final Sessional Marks'],
                'Student Activities Files' => ['Grad Talks', 'Expert Talks / Guest Lectures', 'Soft skills', 'Language and communication skills', 'Life skills', 'Professional Societies', 'Clubs', 'IIC']
            ];

            // Fetch dynamic global options
            $res = $conn->query("SELECT DISTINCT event_mode FROM meta_student_activity_file WHERE event_mode IS NOT NULL AND event_mode != '' ORDER BY event_mode ASC");
            if ($res) { while ($row = $res->fetch_assoc()) { $global_filter_options['modes'][] = $row['event_mode']; } }
            
            $res = $conn->query("SELECT DISTINCT topic_domain FROM meta_student_activity_file WHERE topic_domain IS NOT NULL AND topic_domain != '' ORDER BY topic_domain ASC");
            if ($res) { while ($row = $res->fetch_assoc()) { $global_filter_options['domains'][] = $row['topic_domain']; } }
            
            $res = $conn->query("SELECT DISTINCT event_type FROM meta_student_activity_file WHERE event_type IS NOT NULL AND event_type != '' ORDER BY event_type ASC");
            if ($res) { while ($row = $res->fetch_assoc()) { $global_filter_options['event_types'][] = $row['event_type']; } }
            
            $saf_cats = ['Grad Talks', 'Expert Talks / Guest Lectures', 'Soft skills', 'Language and communication skills', 'Life skills', 'Workshop', 'Seminar', 'Conference', 'Hackathon'];
            foreach ($saf_cats as $cat) {
                if (!in_array($cat, $global_filter_options['event_types'])) {
                    $global_filter_options['event_types'][] = $cat;
                }
            }
            sort($global_filter_options['event_types']);
        }

        $academic_years = doc_get_academic_years($conn);

        $meta_fields = [];
        if ($filter_type !== '') {
            $meta_fields = meta_get_fields($filter_type);
        }

        $pending_result = doc_list_pending_for_user($conn, $auth, 100, 0);
        $pending_count = $pending_result['total'];

        // Handle CSV Export for Filtered Lists
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            $export_result = doc_list($conn, $filters, 999999, 0);
            $export_docs = $export_result['rows'];
            
            $filename = "FMS_Export_" . date('Ymd_His') . ".csv";
            header("Content-Type: text/csv; charset=utf-8");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            
            $output = fopen("php://output", "w");
            
            // Define standard columns
            $columns = ['ID', 'Title', 'Type', 'Uploader', 'Department', 'Academic Year', 'Status', 'Date'];
            
            // Collect all unique meta keys across the result set
            $all_meta_keys = [];
            foreach ($export_docs as $doc) {
                $doc_id = $doc['doc_id'];
                $stmt = $conn->prepare("SELECT meta_key FROM document_metadata WHERE doc_id = ?");
                $stmt->bind_param('i', $doc_id);
                $stmt->execute();
                $mres = $stmt->get_result();
                while ($row = $mres->fetch_assoc()) {
                    if (!in_array($row['meta_key'], $all_meta_keys)) {
                        $all_meta_keys[] = $row['meta_key'];
                    }
                }
                $stmt->close();
            }
            
            // Append meta columns to headers
            foreach ($all_meta_keys as $key) {
                $columns[] = 'Meta: ' . ucfirst(str_replace('_', ' ', $key));
            }
            
            fputcsv($output, $columns);
            
            foreach ($export_docs as $doc) {
                $doc_id = $doc['doc_id'];
                
                // Fetch metadata for this doc
                $meta_values = [];
                $stmt = $conn->prepare("SELECT meta_key, meta_value FROM document_metadata WHERE doc_id = ?");
                $stmt->bind_param('i', $doc_id);
                $stmt->execute();
                $mres = $stmt->get_result();
                while($row = $mres->fetch_assoc()) {
                    $meta_values[$row['meta_key']] = $row['meta_value'];
                }
                $stmt->close();
                
                $row_data = [
                    $doc['doc_id'],
                    $doc['title'],
                    $doc['type_label'] ?? $doc['type_name'] ?? 'Unknown',
                    $doc['uploader_name'] ?? 'Unknown',
                    $doc['dept_name'] ?? 'Unknown',
                    $doc['year_label'] ?? 'Unknown',
                    ucfirst($doc['status']),
                    $doc['created_at']
                ];
                
                foreach ($all_meta_keys as $key) {
                    $row_data[] = $meta_values[$key] ?? '';
                }
                
                fputcsv($output, $row_data);
            }
            
            fclose($output);
            exit;
        }

        include __DIR__ . '/../Views/documents/list.php';
    }

    public function myUploads() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        $auth = auth_context();
        $user_id = (int)$auth['user_id'];
        global $conn;

        $filter_type   = isset($_GET['type'])   ? trim($_GET['type'])   : '';
        $filter_type_keys = isset($_GET['type_keys']) && is_array($_GET['type_keys']) ? $_GET['type_keys'] : [];
        $filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $filter_statuses = isset($_GET['statuses']) && is_array($_GET['statuses']) ? $_GET['statuses'] : [];
        $filter_year   = isset($_GET['year'])   ? (int)$_GET['year']   : 0;
        $filter_year_ids = isset($_GET['year_ids']) && is_array($_GET['year_ids']) ? array_map('intval', $_GET['year_ids']) : [];
        $filter_sub_file_types = isset($_GET['sub_file_types']) && is_array($_GET['sub_file_types']) ? $_GET['sub_file_types'] : [];

        $page_num = max(1, (int)($_GET['page'] ?? 1));
        $per_page = 20;
        $offset = ($page_num - 1) * $per_page;

        $filters = ['uploaded_by' => $user_id];
        if ($filter_type !== '')   $filters['type_key'] = $filter_type;
        if (!empty($filter_type_keys)) $filters['type_keys'] = $filter_type_keys;
        if ($filter_status !== '') $filters['status'] = $filter_status;
        if (!empty($filter_statuses)) $filters['statuses'] = $filter_statuses;
        if ($filter_year > 0)     $filters['year_id'] = $filter_year;
        if (!empty($filter_year_ids)) $filters['year_ids'] = $filter_year_ids;
        if (!empty($filter_sub_file_types)) $filters['sub_file_types'] = $filter_sub_file_types;

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
        if (empty($auth['active_role'])) {
            header("Location: " . BASE_URL . "/public/index.php?route=auth/select_role");
            exit;
        }
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
        $old_input = $_SESSION['_old_input'] ?? [];
        unset($_SESSION['success_msg'], $_SESSION['error_msg'], $_SESSION['_old_input']);

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

        // Only uploader can edit
        if ((int)$document['uploaded_by'] !== (int)$auth['user_id']) {
            die("Unauthorized to edit this document.");
        }
        if (strtolower($document['status']) === 'accepted') {
            die("Cannot edit an accepted document.");
        }
        
        // Workflow bypass fix: Prevent editing if the document is pending but has already been partially approved
        if (strtolower($document['status']) === 'pending') {
            $history = doc_get_history($conn, $doc_id);
            foreach ($history as $h) {
                if ($h['action'] === 'approve') {
                    die("Cannot edit a document that is already partially approved. Please request a rejection first.");
                }
            }
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
