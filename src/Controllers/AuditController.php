<?php
declare(strict_types=1);

namespace App\Controllers;

use mysqli;

/**
 * Controller for College-wide Audit Trail & Activity Monitoring.
 * Allows Administrators and IQAC coordinators to inspect, filter,
 * and export all historical document workflows and system events.
 */
class AuditController extends BaseController {

    public function index(): void {
        $this->requireRole([ROLE_ADMIN, ROLE_IQAC, ROLE_RND_DEAN]);

        // 1. Parse Filter Parameters
        $source_type = isset($_GET['type']) ? trim((string)$_GET['type']) : 'all';
        if (!in_array($source_type, ['all', 'document', 'system'], true)) {
            $source_type = 'all';
        }

        $filter_action = isset($_GET['action']) ? trim((string)$_GET['action']) : '';
        $filter_dept   = isset($_GET['dept_id']) ? (int)$_GET['dept_id'] : 0;
        $date_from     = isset($_GET['date_from']) ? trim((string)$_GET['date_from']) : '';
        $date_to       = isset($_GET['date_to']) ? trim((string)$_GET['date_to']) : '';
        $search        = isset($_GET['search']) ? trim((string)$_GET['search']) : '';

        $page = max(1, (int)($_GET['page'] ?? 1));
        $per_page_param = isset($_GET['per_page']) ? trim((string)$_GET['per_page']) : '25';
        $is_export = (isset($_GET['export']) && $_GET['export'] === 'csv');

        if ($per_page_param === 'all' || $is_export) {
            $per_page = 10000;
        } else {
            $per_page = max(10, min(200, (int)$per_page_param));
        }
        $offset = ($page - 1) * $per_page;

        // 2. Build Query
        $data = $this->fetchAuditLogs($this->conn, $source_type, $filter_action, $filter_dept, $date_from, $date_to, $search, $per_page, $offset);
        $logs = $data['rows'];
        $total_records = $data['total'];
        $total_pages = (int)ceil($total_records / $per_page);

        // 3. Handle CSV Export
        if ($is_export) {
            $this->exportCsv($logs);
            return;
        }

        // 4. Fetch Summary Statistics
        $stats = $this->fetchAuditStats($this->conn);

        // 5. Fetch Departments for Dropdown
        $departments = [];
        $d_res = $this->conn->query("SELECT dept_id, dept_name FROM departments WHERE is_academic = 1 ORDER BY dept_name ASC");
        if ($d_res) {
            while ($row = $d_res->fetch_assoc()) {
                $departments[] = $row;
            }
        }

        // 6. Distinct Action Names for Dropdown
        $available_actions = [
            'document' => [
                'uploaded'    => 'Document Uploaded / Submitted',
                'approved'    => 'Document Approved',
                'rejected'    => 'Document Rejected',
                'resubmitted' => 'Document Resubmitted',
            ],
            'system' => [
                'HOD_ASSIGN'         => 'HOD Role Assigned / Transferred',
                'COORDINATOR_APPOINT' => 'Department Coordinator Appointed',
                'COORDINATOR_REVOKE'  => 'Department Coordinator Revoked',
                'USER_STATUS_CHANGE' => 'Account Status Toggle (Active/Inactive)',
                'USER_CREATE'        => 'New Faculty Account Created',
                'PASSWORD_RESET'     => 'Password Reset to Default',
                'USER_DELETE'        => 'Test User Account Deleted',
                'DOCUMENT_PURGE'     => 'Document & Storage Files Purged',
            ]
        ];

        $this->renderView('admin/audit_logs', get_defined_vars());
    }

    /**
     * Retrieve audit logs with multi-table UNION support and parameterized filtering.
     */
    private function fetchAuditLogs(
        mysqli $conn,
        string $source_type,
        string $action,
        int $dept_id,
        string $date_from,
        string $date_to,
        string $search,
        int $limit,
        int $offset
    ): array {
        $subqueries = [];

        // --- SUBQUERY 1: Document Workflow Actions ---
        if ($source_type === 'all' || $source_type === 'document') {
            $doc_where = ["1=1"];
            $doc_params = [];
            $doc_types = "";

            if (!empty($action)) {
                $doc_where[] = "da.action = ?";
                $doc_params[] = $action;
                $doc_types .= "s";
            }
            if ($dept_id > 0) {
                $doc_where[] = "d.dept_id = ?";
                $doc_params[] = $dept_id;
                $doc_types .= "i";
            }
            if (!empty($date_from)) {
                $doc_where[] = "DATE(da.acted_at) >= ?";
                $doc_params[] = $date_from;
                $doc_types .= "s";
            }
            if (!empty($date_to)) {
                $doc_where[] = "DATE(da.acted_at) <= ?";
                $doc_params[] = $date_to;
                $doc_types .= "s";
            }
            if (!empty($search)) {
                $s_like = '%' . $search . '%';
                $doc_where[] = "(d.title LIKE ? OR u.full_name LIKE ? OR u.email LIKE ? OR da.remarks LIKE ?)";
                $doc_params[] = $s_like;
                $doc_params[] = $s_like;
                $doc_params[] = $s_like;
                $doc_params[] = $s_like;
                $doc_types .= "ssss";
            }

            $doc_where_sql = implode(" AND ", $doc_where);
            $subqueries[] = [
                'sql' => "
                    SELECT 
                        'document' AS source_type,
                        da.action_id AS record_id,
                        da.acted_at AS event_time,
                        CAST(COALESCE(NULLIF(da.action, ''), 'approved') AS CHAR) COLLATE utf8mb4_unicode_ci AS action_name,
                        u.user_id AS actor_id,
                        u.full_name AS actor_name,
                        u.email AS actor_email,
                        dep.dept_name AS dept_name,
                        d.doc_id AS target_id,
                        d.title AS target_title,
                        dt.label AS target_type_label,
                        CAST(da.remarks AS CHAR) COLLATE utf8mb4_unicode_ci AS event_details,
                        CAST(COALESCE(ws.step_label, 'Workflow Action') AS CHAR) COLLATE utf8mb4_unicode_ci AS stage_label
                    FROM document_actions da
                    JOIN users u ON da.acted_by = u.user_id
                    JOIN documents d ON da.doc_id = d.doc_id
                    JOIN document_types dt ON d.type_id = dt.type_id
                    JOIN departments dep ON d.dept_id = dep.dept_id
                    LEFT JOIN workflow_steps ws ON da.step_id = ws.step_id
                    WHERE {$doc_where_sql}
                ",
                'params' => $doc_params,
                'types' => $doc_types
            ];
        }

        // --- SUBQUERY 2: System Administrative Actions ---
        if ($source_type === 'all' || $source_type === 'system') {
            $sys_where = ["1=1"];
            $sys_params = [];
            $sys_types = "";

            if (!empty($action)) {
                $sys_where[] = "sa.action = ?";
                $sys_params[] = $action;
                $sys_types .= "s";
            }
            // System actions are central, but if dept_id is specified and source is 'system', filter if target is user with dept
            if ($dept_id > 0) {
                $sys_where[] = "sa.actor_id IN (SELECT ur.user_id FROM user_roles ur WHERE ur.dept_id = ?)";
                $sys_params[] = $dept_id;
                $sys_types .= "i";
            }
            if (!empty($date_from)) {
                $sys_where[] = "DATE(sa.created_at) >= ?";
                $sys_params[] = $date_from;
                $sys_types .= "s";
            }
            if (!empty($date_to)) {
                $sys_where[] = "DATE(sa.created_at) <= ?";
                $sys_params[] = $date_to;
                $sys_types .= "s";
            }
            if (!empty($search)) {
                $s_like = '%' . $search . '%';
                $sys_where[] = "(sa.details LIKE ? OR u.full_name LIKE ? OR u.email LIKE ? OR sa.action LIKE ?)";
                $sys_params[] = $s_like;
                $sys_params[] = $s_like;
                $sys_params[] = $s_like;
                $sys_params[] = $s_like;
                $sys_types .= "ssss";
            }

            $sys_where_sql = implode(" AND ", $sys_where);
            $subqueries[] = [
                'sql' => "
                    SELECT 
                        'system' AS source_type,
                        sa.log_id AS record_id,
                        sa.created_at AS event_time,
                        CAST(sa.action AS CHAR) COLLATE utf8mb4_unicode_ci AS action_name,
                        COALESCE(u.user_id, sa.actor_id) AS actor_id,
                        CAST(COALESCE(u.full_name, 'System Administrator') AS CHAR) COLLATE utf8mb4_unicode_ci AS actor_name,
                        CAST(COALESCE(u.email, 'system@internal') AS CHAR) COLLATE utf8mb4_unicode_ci AS actor_email,
                        CAST('Administration' AS CHAR) COLLATE utf8mb4_unicode_ci AS dept_name,
                        sa.target_id AS target_id,
                        CONCAT(UPPER(sa.target_type), ' #', COALESCE(sa.target_id, '')) COLLATE utf8mb4_unicode_ci AS target_title,
                        CONCAT('System / ', UPPER(sa.target_type)) COLLATE utf8mb4_unicode_ci AS target_type_label,
                        CAST(sa.details AS CHAR) COLLATE utf8mb4_unicode_ci AS event_details,
                        CAST(COALESCE(sa.ip_address, 'System') AS CHAR) COLLATE utf8mb4_unicode_ci AS stage_label
                    FROM system_audit_logs sa
                    LEFT JOIN users u ON sa.actor_id = u.user_id
                    WHERE {$sys_where_sql}
                ",
                'params' => $sys_params,
                'types' => $sys_types
            ];
        }

        // Combine SQL
        $combined_sqls = [];
        $all_params = [];
        $all_types = "";

        foreach ($subqueries as $sq) {
            $combined_sqls[] = $sq['sql'];
            foreach ($sq['params'] as $p) {
                $all_params[] = $p;
            }
            $all_types .= $sq['types'];
        }

        if (empty($combined_sqls)) {
            return ['rows' => [], 'total' => 0];
        }

        $union_sql = implode(" UNION ALL ", $combined_sqls);

        // 1. Get Total Count
        $count_sql = "SELECT COUNT(*) AS total FROM ({$union_sql}) AS unified_stream";
        $count_stmt = $conn->prepare($count_sql);
        if (!empty($all_params)) {
            $count_stmt->bind_param($all_types, ...$all_params);
        }
        $count_stmt->execute();
        $total = (int)($count_stmt->get_result()->fetch_assoc()['total'] ?? 0);
        $count_stmt->close();

        // 2. Get Paginated Records
        $page_sql = "SELECT * FROM ({$union_sql}) AS unified_stream ORDER BY event_time DESC LIMIT ? OFFSET ?";
        $page_params = $all_params;
        $page_params[] = $limit;
        $page_params[] = $offset;
        $page_types = $all_types . "ii";

        $stmt = $conn->prepare($page_sql);
        $stmt->bind_param($page_types, ...$page_params);
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
        $stmt->close();

        return ['rows' => $rows, 'total' => $total];
    }

    /**
     * Compute summary numbers for cards.
     */
    private function fetchAuditStats(mysqli $conn): array {
        $stats = [
            'total_events' => 0,
            'doc_events'   => 0,
            'sys_events'   => 0,
            'approved'     => 0,
            'rejected'     => 0
        ];

        // Document actions
        $r1 = $conn->query("
            SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN action = 'approved' OR action = '' THEN 1 ELSE 0 END) AS approved_cnt,
                SUM(CASE WHEN action = 'rejected' THEN 1 ELSE 0 END) AS rejected_cnt
            FROM document_actions
        ");
        if ($r1 && $row = $r1->fetch_assoc()) {
            $stats['doc_events']   = (int)($row['total'] ?? 0);
            $stats['approved']     = (int)($row['approved_cnt'] ?? 0);
            $stats['rejected']     = (int)($row['rejected_cnt'] ?? 0);
        }

        // System audit logs
        $r2 = $conn->query("SELECT COUNT(*) AS total FROM system_audit_logs");
        if ($r2 && $row = $r2->fetch_assoc()) {
            $stats['sys_events'] = (int)($row['total'] ?? 0);
        }

        $stats['total_events'] = $stats['doc_events'] + $stats['sys_events'];
        return $stats;
    }

    /**
     * Stream CSV export of filtered audit logs.
     */
    private function exportCsv(array $logs): void {
        if (php_sapi_name() !== 'cli') {
            while (ob_get_level()) {
                ob_end_clean();
            }
        }

        $filename = "audit_log_report_" . date('Y_m_d_His') . ".csv";
        if (!headers_sent()) {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
        }

        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Microsoft Excel compatibility

        // Header Row
        fputcsv($out, [
            'S.No.',
            'Timestamp',
            'Stream',
            'Action',
            'Actor Name',
            'Actor Email',
            'Department',
            'Target / Entity',
            'Category',
            'Workflow Stage / Details',
            'Remarks / Notes'
        ]);

        $sno = 1;
        foreach ($logs as $log) {
            fputcsv($out, [
                $sno++,
                $log['event_time'],
                ucfirst($log['source_type']),
                strtoupper(str_replace('_', ' ', (string)$log['action_name'])),
                $log['actor_name'],
                $log['actor_email'],
                $log['dept_name'],
                $log['target_title'],
                $log['target_type_label'],
                $log['stage_label'],
                $log['event_details']
            ]);
        }

        fclose($out);
        if (php_sapi_name() !== 'cli') {
            exit;
        }
    }
}
