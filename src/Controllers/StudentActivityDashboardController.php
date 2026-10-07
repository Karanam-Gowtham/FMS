<?php
namespace App\Controllers;

class StudentActivityDashboardController
{
    public function index()
    {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
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
            SELECT d.doc_id, d.title, d.created_at, dm.meta_key, dm.meta_value 
            FROM documents d
            JOIN document_types dt ON d.type_id = dt.type_id
            LEFT JOIN document_metadata dm ON d.doc_id = dm.doc_id
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
                        'meta' => []
                    ];
                }
                if ($row['meta_key']) {
                    $docs[$id]['meta'][$row['meta_key']] = $row['meta_value'];
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
            'timeline' => []
        ];

        foreach ($docs as $doc) {
            $meta = $doc['meta'];
            
            // Participants
            $count = (int)($meta['participant_count'] ?? 0);
            $stats['total_participants'] += $count;

            // Category Distribution
            $cat = $meta['activity_category'] ?? 'Uncategorized';
            $stats['category_dist'][$cat] = ($stats['category_dist'][$cat] ?? 0) + 1;

            // Mode Distribution
            $mode = $meta['event_mode'] ?? 'Unknown';
            $stats['mode_dist'][$mode] = ($stats['mode_dist'][$mode] ?? 0) + 1;

            // Topic Domain
            $topic = $meta['topic_domain'] ?? 'Other';
            $stats['topic_dist'][$topic] = ($stats['topic_dist'][$topic] ?? 0) + 1;

            // Target Audience
            $audience = $meta['target_audience'] ?? 'Internal';
            $stats['audience_dist'][$audience] = ($stats['audience_dist'][$audience] ?? 0) + 1;

            // Club Activity
            $club = $meta['sub_category'] ?? '';
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
