<?php
namespace App\Controllers;

class StudentActivityDashboardController
{
    public function index()
    {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        
        global $conn;
        
        // Fetch Academic Years for filter
        $years = [];
        $y_res = $conn->query("SELECT year_id, year_label FROM academic_years ORDER BY year_id DESC");
        if ($y_res) {
            while ($row = $y_res->fetch_assoc()) {
                $years[] = $row;
            }
        }
        
        // Fetch Departments for filter
        $depts = [];
        $d_res = $conn->query("SELECT dept_id, dept_name FROM departments WHERE is_academic = 1 ORDER BY dept_name");
        if ($d_res) {
            while ($row = $d_res->fetch_assoc()) {
                $depts[] = $row;
            }
        }

        // Default Filters
        $year_filter = $_GET['year_id'] ?? 'all';
        $dept_filter = $_GET['dept_id'] ?? 'all';

        // Query conditions
        $where = "dt.type_code = 'student_activity_file' AND d.status = 'accepted'";
        if ($year_filter !== 'all') {
            $where .= " AND d.academic_year_id = " . (int)$year_filter;
        }
        if ($dept_filter !== 'all') {
            $where .= " AND d.dept_id = " . (int)$dept_filter;
        }

        // Fetch all relevant documents metadata
        $sql = "
            SELECT d.doc_id, d.title, d.created_at, dp.dept_name,
                   msaf.activity_category, msaf.event_mode, msaf.topic_domain, msaf.target_audience, msaf.sub_category, msaf.participant_count 
            FROM documents d
            JOIN document_types dt ON d.type_id = dt.type_id
            JOIN departments dp ON d.dept_id = dp.dept_id
            LEFT JOIN meta_student_activity_file msaf ON d.doc_id = msaf.doc_id
            WHERE $where
        ";
        
        $res = $conn->query($sql);
        
        // Restructure metadata into doc objects
        $docs = [];
        if ($res) {
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
                            'participant_count' => $row['participant_count']
                        ]
                    ];
                }
            }
        }

        // Process data for charts
        $stats = [
            'total_events' => count($docs),
            'total_participants' => 0,
            'category_dist' => [],
            'mode_dist' => [],
            'topic_dist' => [],
            'audience_dist' => [],
            'club_activity' => [],
            'timeline' => [],
            'dept_compare' => []
        ];

        foreach ($docs as $doc) {
            $meta = $doc['meta'];
            
            // Participants
            $count = (int)($meta['participant_count'] ?? 0);
            $stats['total_participants'] += $count;

            // Department Comparison
            $dept = $doc['dept_name'];
            if (!isset($stats['dept_compare'][$dept])) {
                $stats['dept_compare'][$dept] = ['events' => 0, 'participants' => 0];
            }
            $stats['dept_compare'][$dept]['events'] += 1;
            $stats['dept_compare'][$dept]['participants'] += $count;

            // Category Distribution
            $cat = !empty($meta['activity_category']) ? $meta['activity_category'] : 'Uncategorized';
            $stats['category_dist'][$cat] = ($stats['category_dist'][$cat] ?? 0) + 1;

            // Mode Distribution
            $mode = !empty($meta['event_mode']) ? $meta['event_mode'] : 'Unknown';
            $stats['mode_dist'][$mode] = ($stats['mode_dist'][$mode] ?? 0) + 1;

            // Topic Domain
            $topic = !empty($meta['topic_domain']) ? $meta['topic_domain'] : 'Other';
            $stats['topic_dist'][$topic] = ($stats['topic_dist'][$topic] ?? 0) + 1;

            // Target Audience
            $audience = !empty($meta['target_audience']) ? $meta['target_audience'] : 'Internal';
            $stats['audience_dist'][$audience] = ($stats['audience_dist'][$audience] ?? 0) + 1;

            // Club Activity
            $club = !empty($meta['sub_category']) ? trim($meta['sub_category']) : '';
            if (!empty($club)) {
                $stats['club_activity'][$club] = ($stats['club_activity'][$club] ?? 0) + 1;
            }

            // Timeline (by month)
            $month = date('Y-m', strtotime($doc['created_at']));
            $stats['timeline'][$month] = ($stats['timeline'][$month] ?? 0) + $count;
        }

        ksort($stats['timeline']); // Sort timeline chronologically

        // Render View
        require_once __DIR__ . '/../Views/student_activities/dashboard.php';
    }
}
