<?php
declare(strict_types=1);

namespace App\Controllers;

class StudentActivityDashboardController extends BaseController
{
    /**
     * Dispatcher for Student Activities Dashboard route.
     * Publicly accessible: redirects to main portal section or serves standalone/JSON.
     */
    public function index(): void
    {
        if ($this->isAjax() || (isset($_GET['format']) && $_GET['format'] === 'json')) {
            $this->json([
                'status' => 'success',
                'data' => self::calculateStats($_GET)
            ]);
            return;
        }

        // Standalone view without staff header
        if (isset($_GET['standalone']) && $_GET['standalone'] === '1') {
            $filter_data = self::getFilterData();
            $years = $filter_data['years'];
            $depts = $filter_data['depts'];
            $global_filter_options = $filter_data['options'];

            $year_filter = (isset($_GET['year_id']) && is_string($_GET['year_id'])) ? trim($_GET['year_id']) : 'all';
            $dept_filter = (isset($_GET['dept_id']) && is_string($_GET['dept_id'])) ? trim($_GET['dept_id']) : 'all';
            $scope_filter = (isset($_GET['scope']) && is_string($_GET['scope'])) ? trim($_GET['scope']) : 'all';

            $filter_global_modes = isset($_GET['global_modes']) ? (array)$_GET['global_modes'] : [];
            $filter_global_domains = isset($_GET['global_domains']) ? (array)$_GET['global_domains'] : [];
            $filter_global_event_types = isset($_GET['global_event_types']) ? (array)$_GET['global_event_types'] : [];

            $stats = self::calculateStats($_GET);

            $this->renderView('student_activities/dashboard', get_defined_vars());
            return;
        }

        // Default: Smooth redirect to the public home portal section
        $this->redirect(BASE_URL . "/index.php#student-activities");
    }

    /**
     * Fetch all available filter lists (years, departments, modes, domains, categories).
     */
    public static function getFilterData(): array
    {
        global $conn;

        $years = [];
        $y_res = $conn->query("SELECT year_id, year_label FROM academic_years ORDER BY year_label DESC");
        if ($y_res instanceof \mysqli_result) {
            while ($row = $y_res->fetch_assoc()) {
                $years[] = $row;
            }
            $y_res->close();
        }

        $depts = [];
        $d_res = $conn->query("SELECT dept_id, dept_name FROM departments WHERE is_academic = 1 ORDER BY dept_name ASC");
        if ($d_res instanceof \mysqli_result) {
            while ($row = $d_res->fetch_assoc()) {
                $depts[] = $row;
            }
            $d_res->close();
        }

        $global_filter_options = ['modes' => [], 'domains' => [], 'event_types' => []];

        $res = $conn->query("SELECT DISTINCT event_mode FROM meta_student_activity_file WHERE event_mode IS NOT NULL AND event_mode != '' ORDER BY event_mode ASC");
        if ($res instanceof \mysqli_result) {
            while ($row = $res->fetch_assoc()) {
                $global_filter_options['modes'][] = $row['event_mode'];
            }
            $res->close();
        }
        foreach (['Online', 'Offline', 'Hybrid'] as $m) {
            if (!in_array($m, $global_filter_options['modes'], true)) {
                $global_filter_options['modes'][] = $m;
            }
        }
        $global_filter_options['modes'] = array_values(array_unique($global_filter_options['modes']));
        sort($global_filter_options['modes']);

        $res = $conn->query("SELECT DISTINCT topic_domain FROM meta_student_activity_file WHERE topic_domain IS NOT NULL AND topic_domain != '' ORDER BY topic_domain ASC");
        if ($res instanceof \mysqli_result) {
            while ($row = $res->fetch_assoc()) {
                $global_filter_options['domains'][] = $row['topic_domain'];
            }
            $res->close();
        }
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
        if ($res instanceof \mysqli_result) {
            while ($row = $res->fetch_assoc()) {
                $global_filter_options['event_types'][] = $row['event_type'];
            }
            $res->close();
        }

        $res = $conn->query("SELECT DISTINCT activity_category FROM meta_student_activity_file WHERE activity_category IS NOT NULL AND activity_category != '' ORDER BY activity_category ASC");
        if ($res instanceof \mysqli_result) {
            while ($row = $res->fetch_assoc()) {
                $global_filter_options['event_types'][] = $row['activity_category'];
            }
            $res->close();
        }
        
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

        return [
            'years' => $years,
            'depts' => $depts,
            'options' => $global_filter_options
        ];
    }

    /**
     * Calculate all KPI numbers and Chart.js datasets based on filter criteria.
     */
    public static function calculateStats(array $params = []): array
    {
        global $conn;

        $year_filter = isset($params['year_id']) && is_string($params['year_id']) ? trim($params['year_id']) : 'all';
        if ($year_filter !== 'all' && (int)$year_filter <= 0) {
            $year_filter = 'all';
        }

        $dept_filter = isset($params['dept_id']) && is_string($params['dept_id']) ? trim($params['dept_id']) : 'all';
        if ($dept_filter !== 'all' && (int)$dept_filter <= 0) {
            $dept_filter = 'all';
        }

        $scope_filter = isset($params['scope']) && is_string($params['scope']) ? trim($params['scope']) : 'all';
        if (!in_array($scope_filter, ['all', 'department', 'central', 'external'], true)) {
            $scope_filter = 'all';
        }

        // Support single or array inputs for modes, domains, types
        $filter_modes = [];
        $raw_modes = $params['global_modes'] ?? ($params['mode'] ?? []);
        $raw_modes = is_array($raw_modes) ? $raw_modes : [$raw_modes];
        foreach ($raw_modes as $val) {
            if (is_string($val) && trim($val) !== '' && trim($val) !== 'all') {
                $filter_modes[] = trim($val);
            }
        }

        $filter_domains = [];
        $raw_domains = $params['global_domains'] ?? ($params['domain'] ?? []);
        $raw_domains = is_array($raw_domains) ? $raw_domains : [$raw_domains];
        foreach ($raw_domains as $val) {
            if (is_string($val) && trim($val) !== '' && trim($val) !== 'all') {
                $filter_domains[] = trim($val);
            }
        }

        $filter_types = [];
        $raw_types = $params['global_event_types'] ?? ($params['event_type'] ?? []);
        $raw_types = is_array($raw_types) ? $raw_types : [$raw_types];
        foreach ($raw_types as $val) {
            if (is_string($val) && trim($val) !== '' && trim($val) !== 'all') {
                $filter_types[] = trim($val);
            }
        }

        $where = "dt.type_code = 'student_activity_file' AND d.status = 'accepted'";
        if ($year_filter !== 'all') {
            $where .= " AND d.academic_year_id = " . (int)$year_filter;
        }
        if ($dept_filter !== 'all') {
            $where .= " AND d.dept_id = " . (int)$dept_filter;
        }

        if ($scope_filter === 'department') {
            $where .= " AND (msaf.target_audience = 'Department Wise' OR (msaf.target_audience IS NULL AND COALESCE(msaf.activity_category, '') NOT IN ('Clubs', 'Stepcone')))";
        } elseif ($scope_filter === 'central') {
            $where .= " AND (msaf.target_audience = 'Central / University Wide' OR msaf.activity_category = 'Clubs' OR msaf.activity_category = 'Stepcone')";
        } elseif ($scope_filter === 'external') {
            $where .= " AND msaf.target_audience = 'Inter-College / External'";
        }

        if (!empty($filter_modes)) {
            $modes_escaped = array_map(function($v) use ($conn) { return "'" . $conn->real_escape_string($v) . "'"; }, $filter_modes);
            $where .= " AND msaf.event_mode IN (" . implode(',', $modes_escaped) . ")";
        }
        if (!empty($filter_domains)) {
            $domains_escaped = array_map(function($v) use ($conn) { return "'" . $conn->real_escape_string($v) . "'"; }, $filter_domains);
            $where .= " AND msaf.topic_domain IN (" . implode(',', $domains_escaped) . ")";
        }
        if (!empty($filter_types)) {
            $types_escaped = array_map(function($v) use ($conn) { return "'" . $conn->real_escape_string($v) . "'"; }, $filter_types);
            $where .= " AND (msaf.event_type IN (" . implode(',', $types_escaped) . ") OR msaf.activity_category IN (" . implode(',', $types_escaped) . "))";
        }

        $sql = "
            SELECT d.doc_id, d.title, d.created_at, dp.dept_name, ay.year_label,
                   msaf.activity_category, msaf.event_mode, msaf.topic_domain, msaf.target_audience, msaf.sub_category, msaf.participant_count,
                   msaf.date_from, msaf.date_to
            FROM documents d
            JOIN document_types dt ON d.type_id = dt.type_id
            JOIN departments dp ON d.dept_id = dp.dept_id
            LEFT JOIN academic_years ay ON d.academic_year_id = ay.year_id
            LEFT JOIN meta_student_activity_file msaf ON d.doc_id = msaf.doc_id
            WHERE $where
        ";

        $res = $conn->query($sql);
        $docs = [];
        if ($res instanceof \mysqli_result) {
            while ($row = $res->fetch_assoc()) {
                $id = (int)$row['doc_id'];
                if (!isset($docs[$id])) {
                    $docs[$id] = [
                        'doc_id' => $id,
                        'title' => $row['title'],
                        'created_at' => $row['created_at'],
                        'dept_name' => $row['dept_name'],
                        'year_label' => $row['year_label'] ?? '',
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
            
            // Normalize Scope
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

            $count = (int)($meta['participant_count'] ?? 0);
            $stats['total_participants'] += $count;

            if ($scope === 'Department Wise') {
                $dept = $doc['dept_name'];
                if (!isset($stats['dept_compare'][$dept])) {
                    $stats['dept_compare'][$dept] = ['events' => 0, 'participants' => 0];
                }
                $stats['dept_compare'][$dept]['events'] += 1;
                $stats['dept_compare'][$dept]['participants'] += $count;
            }

            $stats['category_dist'][$cat] = ($stats['category_dist'][$cat] ?? 0) + 1;

            $mode = (!empty($meta['event_mode']) && in_array(trim($meta['event_mode']), ['Online', 'Offline', 'Hybrid'], true)) ? trim($meta['event_mode']) : 'Offline';
            $stats['mode_dist'][$mode] = ($stats['mode_dist'][$mode] ?? 0) + 1;

            $topic = !empty($meta['topic_domain']) ? $meta['topic_domain'] : 'Other';
            $stats['topic_dist'][$topic] = ($stats['topic_dist'][$topic] ?? 0) + 1;

            $club = !empty($meta['sub_category']) ? trim($meta['sub_category']) : '';
            if (!empty($club)) {
                $stats['club_activity'][$club] = ($stats['club_activity'][$club] ?? 0) + 1;
            }

            if ($year_filter !== 'all') {
                $event_start_date = (!empty($meta['date_from']) && $meta['date_from'] !== '0000-00-00') ? $meta['date_from'] : ($doc['created_at'] ?? '');
                $ts = !empty($event_start_date) ? strtotime($event_start_date) : false;
                if ($ts === false) {
                    $ts = !empty($doc['created_at']) ? (strtotime($doc['created_at']) ?: time()) : time();
                }
                $time_key = date('M Y', $ts);
            } else {
                if (!empty($doc['year_label'])) {
                    $time_key = $doc['year_label'];
                } else {
                    $event_start_date = (!empty($meta['date_from']) && $meta['date_from'] !== '0000-00-00') ? $meta['date_from'] : ($doc['created_at'] ?? '');
                    $ts = !empty($event_start_date) ? strtotime($event_start_date) : false;
                    if ($ts === false) {
                        $ts = !empty($doc['created_at']) ? (strtotime($doc['created_at']) ?: time()) : time();
                    }
                    $cal_yr = (int)date('Y', $ts);
                    $cal_mo = (int)date('n', $ts);
                    $start_yr = ($cal_mo >= 6) ? $cal_yr : ($cal_yr - 1);
                    $time_key = $start_yr . '-' . substr((string)($start_yr + 1), -2);
                }
            }
            $stats['timeline'][$time_key] = ($stats['timeline'][$time_key] ?? 0) + $count;
        }

        ksort($stats['timeline']);
        return $stats;
    }
}
