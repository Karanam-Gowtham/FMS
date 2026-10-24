<?php
namespace App\Controllers;

class StudentActivityDashboardController
{
    public function index()
    {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        $auth = auth_context();
        $active_role = auth_active_role();

        if (!$active_role) {
            header("Location: " . BASE_URL . "/public/index.php?route=auth/select_role");
            exit();
        }
        
        global $conn;
        
        $active_role_id = auth_active_role_id();
        $is_college_wide = in_array($active_role_id, [ROLE_ADMIN, ROLE_RND_DEAN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true);

        // Fetch Academic Years for filter
        $years = [];
        $y_res = $conn->query("SELECT year_id, year_label FROM academic_years ORDER BY year_label DESC");
        if ($y_res instanceof \mysqli_result) {
            while ($row = $y_res->fetch_assoc()) {
                $years[] = $row;
            }
            $y_res->close();
        }
        
        // Fetch Departments for filter
        $depts = [];
        $d_sql = "SELECT dept_id, dept_name FROM departments WHERE is_academic = 1";
        if (!$is_college_wide) {
            $authorized = get_authorized_depts();
            $allowed_ids = array_map('intval', array_keys($authorized));
            if (!empty($allowed_ids)) {
                $d_sql .= " AND dept_id IN (" . implode(',', $allowed_ids) . ")";
            } else {
                $d_sql .= " AND 1=0";
            }
        }
        $d_sql .= " ORDER BY dept_name";
        $d_res = $conn->query($d_sql);
        if ($d_res instanceof \mysqli_result) {
            while ($row = $d_res->fetch_assoc()) {
                $depts[] = $row;
            }
            $d_res->close();
        }

        // Default Filters
        $year_filter = (isset($_GET['year_id']) && is_string($_GET['year_id'])) ? trim($_GET['year_id']) : 'all';
        if ($year_filter !== 'all' && (int)$year_filter <= 0) {
            $year_filter = 'all';
        }
        $scope_filter = (isset($_GET['scope']) && is_string($_GET['scope'])) ? trim($_GET['scope']) : 'all';
        if (!in_array($scope_filter, ['all', 'department', 'central', 'external'], true)) {
            $scope_filter = 'all';
        }
        
        $has_dept_param = isset($_GET['dept_id']) && is_string($_GET['dept_id']);
        $requested_dept = $has_dept_param ? trim($_GET['dept_id']) : null;
        if ($is_college_wide) {
            $dept_filter = ($requested_dept !== null && $requested_dept !== 'all' && (int)$requested_dept > 0) ? (int)$requested_dept : 'all';
        } else {
            $user_dept_id = auth_active_dept_id();
            if ($requested_dept === 'all') {
                $dept_filter = 'authorized';
            } elseif ($requested_dept !== null) {
                $validated = validate_dept_filter((int)$requested_dept);
                $dept_filter = ($validated !== null) ? $validated : ($user_dept_id ?: 'authorized');
            } else {
                $dept_filter = $user_dept_id ?: 'authorized';
            }
        }
        
        $filter_global_modes = [];
        if (isset($_GET['global_modes'])) {
            $raw_modes = is_array($_GET['global_modes']) ? $_GET['global_modes'] : [$_GET['global_modes']];
            foreach ($raw_modes as $val) {
                if (is_string($val) && trim($val) !== '') {
                    $filter_global_modes[] = trim($val);
                }
            }
        }
        $filter_global_domains = [];
        if (isset($_GET['global_domains'])) {
            $raw_domains = is_array($_GET['global_domains']) ? $_GET['global_domains'] : [$_GET['global_domains']];
            foreach ($raw_domains as $val) {
                if (is_string($val) && trim($val) !== '') {
                    $filter_global_domains[] = trim($val);
                }
            }
        }
        $filter_global_event_types = [];
        if (isset($_GET['global_event_types'])) {
            $raw_types = is_array($_GET['global_event_types']) ? $_GET['global_event_types'] : [$_GET['global_event_types']];
            foreach ($raw_types as $val) {
                if (is_string($val) && trim($val) !== '') {
                    $filter_global_event_types[] = trim($val);
                }
            }
        }

        // Fetch dynamic global filter options
        $global_filter_options = ['modes' => [], 'domains' => [], 'event_types' => []];
        $res = $conn->query("SELECT DISTINCT event_mode FROM meta_student_activity_file WHERE event_mode IS NOT NULL AND event_mode != '' ORDER BY event_mode ASC");
        if ($res) { while ($row = $res->fetch_assoc()) { $global_filter_options['modes'][] = $row['event_mode']; } $res->close(); }
        $canonical_modes = ['Online', 'Offline', 'Hybrid'];
        foreach ($canonical_modes as $m) {
            if (!in_array($m, $global_filter_options['modes'], true)) {
                $global_filter_options['modes'][] = $m;
            }
        }
        $global_filter_options['modes'] = array_values(array_unique($global_filter_options['modes']));
        sort($global_filter_options['modes']);

        $res = $conn->query("SELECT DISTINCT topic_domain FROM meta_student_activity_file WHERE topic_domain IS NOT NULL AND topic_domain != '' ORDER BY topic_domain ASC");
        if ($res) { while ($row = $res->fetch_assoc()) { $global_filter_options['domains'][] = $row['topic_domain']; } $res->close(); }
        $canonical_domains = [
            'Career Counselling', 'Awareness of Trends and Technologies', 
            'Domain Specific / Technical Skill', 'Cultural / Arts', 
            'Social / Community Service', 'Other'
        ];
        foreach ($canonical_domains as $cd) {
            if (!in_array($cd, $global_filter_options['domains'], true)) {
                $global_filter_options['domains'][] = $cd;
            }
        }
        $global_filter_options['domains'] = array_values(array_unique($global_filter_options['domains']));
        sort($global_filter_options['domains']);

        $res = $conn->query("SELECT DISTINCT event_type FROM meta_student_activity_file WHERE event_type IS NOT NULL AND event_type != '' ORDER BY event_type ASC");
        if ($res) { while ($row = $res->fetch_assoc()) { $global_filter_options['event_types'][] = $row['event_type']; } $res->close(); }

        $res = $conn->query("SELECT DISTINCT activity_category FROM meta_student_activity_file WHERE activity_category IS NOT NULL AND activity_category != '' ORDER BY activity_category ASC");
        if ($res) { while ($row = $res->fetch_assoc()) { $global_filter_options['event_types'][] = $row['activity_category']; } $res->close(); }
        
        $saf_cats = [
            'Grad Talks', 'Expert Talks / Guest Lectures', 'Soft skills', 
            'Language and communication skills', 'Life skills', 'Professional Societies', 
            'Clubs', 'IIC', 'Stepcone', 'Workshop', 'Hackathon', 'Conference', 
            'Competition', 'Seminar'
        ];
        foreach ($saf_cats as $cat) {
            if (!in_array($cat, $global_filter_options['event_types'], true)) {
                $global_filter_options['event_types'][] = $cat;
            }
        }
        $global_filter_options['event_types'] = array_values(array_unique($global_filter_options['event_types']));
        sort($global_filter_options['event_types']);

        // Query conditions
        $where = "dt.type_code = 'student_activity_file' AND d.status = 'accepted'";
        if ($year_filter !== 'all') {
            $where .= " AND d.academic_year_id = " . (int)$year_filter;
        }
        if ($dept_filter === 'authorized') {
            $authorized = array_keys(get_authorized_depts());
            if (!empty($authorized)) {
                $where .= " AND d.dept_id IN (" . implode(',', array_map('intval', $authorized)) . ")";
            } else {
                $where .= " AND 1=0";
            }
        } elseif ($dept_filter !== 'all') {
            $where .= " AND d.dept_id = " . (int)$dept_filter;
        }
        if ($scope_filter === 'department') {
            $where .= " AND (msaf.target_audience = 'Department Wise' OR (msaf.target_audience IS NULL AND COALESCE(msaf.activity_category, '') NOT IN ('Clubs', 'Stepcone')))";
        } elseif ($scope_filter === 'central') {
            $where .= " AND (msaf.target_audience = 'Central / University Wide' OR msaf.activity_category = 'Clubs' OR msaf.activity_category = 'Stepcone')";
        } elseif ($scope_filter === 'external') {
            $where .= " AND msaf.target_audience = 'Inter-College / External'";
        }
        
        if (!empty($filter_global_modes)) {
            $modes_escaped = array_map(function($v) use ($conn) { return "'" . $conn->real_escape_string($v) . "'"; }, $filter_global_modes);
            $where .= " AND msaf.event_mode IN (" . implode(',', $modes_escaped) . ")";
        }
        if (!empty($filter_global_domains)) {
            $domains_escaped = array_map(function($v) use ($conn) { return "'" . $conn->real_escape_string($v) . "'"; }, $filter_global_domains);
            $where .= " AND msaf.topic_domain IN (" . implode(',', $domains_escaped) . ")";
        }
        if (!empty($filter_global_event_types)) {
            $types_escaped = array_map(function($v) use ($conn) { return "'" . $conn->real_escape_string($v) . "'"; }, $filter_global_event_types);
            $where .= " AND (msaf.event_type IN (" . implode(',', $types_escaped) . ") OR msaf.activity_category IN (" . implode(',', $types_escaped) . "))";
        }

        // Fetch all relevant documents metadata
        $sql = "
            SELECT d.doc_id, d.title, d.created_at, dp.dept_name,
                   msaf.activity_category, msaf.event_mode, msaf.topic_domain, msaf.target_audience, msaf.sub_category, msaf.participant_count,
                   msaf.date_from, msaf.date_to
            FROM documents d
            JOIN document_types dt ON d.type_id = dt.type_id
            JOIN departments dp ON d.dept_id = dp.dept_id
            LEFT JOIN meta_student_activity_file msaf ON d.doc_id = msaf.doc_id
            WHERE $where
        ";
        
        $res = $conn->query($sql);
        
        // Restructure metadata into doc objects
        $docs = [];
        if ($res instanceof \mysqli_result) {
            while ($row = $res->fetch_assoc()) {
                $id = $row['doc_id'];
                if (!isset($docs[$id])) {
                    $docs[$id] = [
                        'doc_id' => $id,
                        'title' => $row['title'],
                        'created_at' => $row['created_at'],
                        'dept_name' => $row['dept_name'],
                        'meta' => [
                            'activity_category' => $row['activity_category'],
                            'event_mode' => $row['event_mode'],
                            'topic_domain' => $row['topic_domain'],
                            'target_audience' => $row['target_audience'],
                            'sub_category' => $row['sub_category'],
                            'participant_count' => $row['participant_count'],
                            'date_from' => $row['date_from'],
                            'date_to' => $row['date_to']
                        ]
                    ];
                }
            }
            $res->close();
        }

        // Process data for charts
        $stats = [
            'total_events' => count($docs),
            'total_participants' => 0,
            'department_events' => 0,
            'central_events' => 0,
            'external_events' => 0,
            'category_dist' => [],
            'mode_dist' => [],
            'topic_dist' => [],
            'scope_dist' => [
                'Department Wise' => 0,
                'Central / University Wide' => 0,
                'Inter-College / External' => 0
            ],
            'club_activity' => [],
            'timeline' => [],
            'dept_compare' => []
        ];

        foreach ($docs as $doc) {
            $meta = $doc['meta'];
            $cat = !empty($meta['activity_category']) ? $meta['activity_category'] : 'Uncategorized';
            $raw_audience = !empty($meta['target_audience']) ? trim($meta['target_audience']) : '';
            
            // Normalize Event Scope
            if ($cat === 'Clubs' || $cat === 'Stepcone') {
                $scope = ($raw_audience === 'Inter-College / External') ? 'Inter-College / External' : 'Central / University Wide';
            } elseif ($raw_audience === 'Central / University Wide') {
                $scope = 'Central / University Wide';
            } elseif ($raw_audience === 'Inter-College / External') {
                $scope = 'Inter-College / External';
            } else {
                $scope = 'Department Wise';
            }

            if ($scope === 'Central / University Wide') {
                $stats['central_events']++;
            } elseif ($scope === 'Department Wise') {
                $stats['department_events']++;
            } elseif ($scope === 'Inter-College / External') {
                $stats['external_events']++;
            }
            $stats['scope_dist'][$scope] = ($stats['scope_dist'][$scope] ?? 0) + 1;

            // Participants
            $count = (int)($meta['participant_count'] ?? 0);
            $stats['total_participants'] += $count;

            // Department Comparison: Only include departmental initiatives
            // Central clubs & campus-wide fests belong centrally to SAC, not an individual department comparison
            if ($scope === 'Department Wise') {
                $dept = $doc['dept_name'];
                if (!isset($stats['dept_compare'][$dept])) {
                    $stats['dept_compare'][$dept] = ['events' => 0, 'participants' => 0];
                }
                $stats['dept_compare'][$dept]['events'] += 1;
                $stats['dept_compare'][$dept]['participants'] += $count;
            }

            // Category Distribution
            $stats['category_dist'][$cat] = ($stats['category_dist'][$cat] ?? 0) + 1;

            // Mode Distribution
            $mode = !empty($meta['event_mode']) ? $meta['event_mode'] : 'Unknown';
            $stats['mode_dist'][$mode] = ($stats['mode_dist'][$mode] ?? 0) + 1;

            // Topic Domain
            $topic = !empty($meta['topic_domain']) ? $meta['topic_domain'] : 'Other';
            $stats['topic_dist'][$topic] = ($stats['topic_dist'][$topic] ?? 0) + 1;

            // Club Activity
            $club = !empty($meta['sub_category']) ? trim($meta['sub_category']) : '';
            if (!empty($club)) {
                $stats['club_activity'][$club] = ($stats['club_activity'][$club] ?? 0) + 1;
            }

            // Timeline (by month based on form's event start date)
            $event_start_date = (!empty($meta['date_from']) && $meta['date_from'] !== '0000-00-00') ? $meta['date_from'] : ($doc['created_at'] ?? '');
            $ts = !empty($event_start_date) ? strtotime($event_start_date) : false;
            if ($ts === false) {
                $ts = !empty($doc['created_at']) ? (strtotime($doc['created_at']) ?: time()) : time();
            }
            $month = date('Y-m', $ts);
            $stats['timeline'][$month] = ($stats['timeline'][$month] ?? 0) + $count;
        }

        ksort($stats['timeline']); // Sort timeline chronologically

        // Render View
        include __DIR__ . '/../Views/student_activities/dashboard.php';
    }
}
