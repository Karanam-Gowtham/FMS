<?php
namespace App\Controllers;

class PublicController {

    public function department() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        $dept_param = isset($_GET['dept']) ? trim($_GET['dept']) : '';
        global $conn;

        // Lookup department in database with alias support (e.g. AIML <-> CSE-AI&ML)
        $dept_info = null;
        if ($dept_param !== '') {
            $stmt = $conn->prepare("SELECT * FROM departments WHERE dept_name = ?");
            if ($stmt) {
                $stmt->bind_param("s", $dept_param);
                $stmt->execute();
                $dept_info = $stmt->get_result()->fetch_assoc();
                $stmt->close();
            }

            if (!$dept_info) {
                $aliases = [
                    'CSE-AI&ML' => 'AIML',
                    'CSE-AI&DS' => 'AIDS',
                    'CSE-AIML'  => 'AIML',
                    'CSE-AIDS'  => 'AIDS',
                    'MATHEMATICS' => 'MatheMatics',
                    'PHYSICS'     => 'Physics',
                    'CHEMISTRY'   => 'Chemistry',
                ];
                $normalized = $aliases[strtoupper($dept_param)] ?? $dept_param;
                $stmt = $conn->prepare("SELECT * FROM departments WHERE LOWER(dept_name) = LOWER(?)");
                if ($stmt) {
                    $stmt->bind_param("s", $normalized);
                    $stmt->execute();
                    $dept_info = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                }
            }
        }

        // If dept not found, render 404 template
        if (!$dept_info) {
            include __DIR__ . '/../../public/404.php';
            exit;
        }

        $dept_id = (int)$dept_info['dept_id'];
        $dept_name = $dept_info['dept_name'];

        // Fetch active faculty members in this department
        $faculty = [];
        $stmt = $conn->prepare("
            SELECT u.user_id, u.full_name, u.email, u.status, u.created_at, MAX(r.role_name) as role_name
            FROM users u
            JOIN user_roles ur ON u.user_id = ur.user_id
            JOIN roles r ON ur.role_id = r.role_id
            WHERE ur.dept_id = ? AND u.status = 'active' AND r.role_name IN ('Faculty', 'HOD')
            GROUP BY u.user_id, u.full_name, u.email, u.status, u.created_at
            ORDER BY u.full_name
        ");
        if ($stmt) {
            $stmt->bind_param("i", $dept_id);
            $stmt->execute();
            $fres = $stmt->get_result();
            while ($row = $fres->fetch_assoc()) {
                $faculty[] = $row;
            }
            $stmt->close();
        }

        // Fetch accepted public documents
        $public_docs = [];
        $stmt = $conn->prepare("
            SELECT d.doc_id, COALESCE(NULLIF(d.title, ''), dm.meta_value, 'Untitled Document') as original_file_name, dt.label as type_name, dt.type_code,
                   u.full_name as uploader_name, u.user_id, ay.year_label as year_name, 
                   d.created_at, COALESCE(NULLIF(d.file_path, ''), (SELECT df.file_path FROM document_files df WHERE df.doc_id = d.doc_id LIMIT 1), '') as file_path 
            FROM documents d
            LEFT JOIN document_types dt ON d.type_id = dt.type_id
            LEFT JOIN users u ON d.uploaded_by = u.user_id
            LEFT JOIN academic_years ay ON d.academic_year_id = ay.year_id
            LEFT JOIN document_metadata dm ON d.doc_id = dm.doc_id AND dm.meta_key = 'title'
            WHERE d.dept_id = ? AND d.status = 'accepted' 
            AND dt.type_code IN ('journal', 'conference', 'patent', 'fdp', 'fdp_attended', 'fdp_organised', 'conf_organised', 'scholarship', 'placement', 'higher_ed', 'award', 'student_event', 'student_body', 'student_journal', 'student_conference', 'student_activity_file', 'dept_file')
            ORDER BY d.created_at DESC
        ");
        if ($stmt) {
            $stmt->bind_param("i", $dept_id);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) { 
                // We no longer set file_path to '#' if empty, because single-file docs have empty file_path in 'documents'
                // and the download endpoint falls back to 'document_files' table.
                $public_docs[] = $row; 
            }
            $stmt->close();
        }

        // Calculate research stats and Faculty Aggregation
        $papers_count = 0;
        $patents_count = 0;
        $fdps_count = 0;

        $faculty_performance = [];
        // Pre-fill with active faculty
        foreach($faculty as $fac) {
            $faculty_performance[$fac['full_name']] = [
                'name' => $fac['full_name'],
                'role' => $fac['role_name'],
                'total' => 0,
                'papers' => 0,
                'patents' => 0,
                'fdps' => 0,
                'others' => 0
            ];
        }

        foreach ($public_docs as $doc) {
            $uploader = $doc['uploader_name'] ?: 'Unknown';
            if (!isset($faculty_performance[$uploader])) {
                $faculty_performance[$uploader] = [
                    'name' => $uploader,
                    'role' => 'Unknown',
                    'total' => 0,
                    'papers' => 0,
                    'patents' => 0,
                    'fdps' => 0,
                    'others' => 0
                ];
            }
            
            $faculty_performance[$uploader]['total']++;
            $tCode = $doc['type_code'] ?? '';
            
            if (in_array($tCode, ['journal', 'conference', 'student_journal', 'student_conference'], true)) {
                $faculty_performance[$uploader]['papers']++;
                $papers_count++;
            } elseif ($tCode === 'patent') {
                $faculty_performance[$uploader]['patents']++;
                $patents_count++;
            } elseif (stripos($tCode, 'fdp') !== false) {
                $faculty_performance[$uploader]['fdps']++;
                $fdps_count++;
            } else {
                $faculty_performance[$uploader]['others']++;
            }
        }

        // Calculate Points (Score)
        foreach ($faculty_performance as &$perf) {
            $perf['points'] = ($perf['patents'] * 10) + ($perf['papers'] * 5) + ($perf['fdps'] * 2) + ($perf['others'] * 1);
        }
        unset($perf);

        // Sort by points descending
        usort($faculty_performance, function($a, $b) {
            return $b['points'] <=> $a['points'];
        });
        
        $top_faculty = array_slice($faculty_performance, 0, 3);

        include __DIR__ . '/../Views/public/department.php';
    }

    public function download() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        global $conn;

        $doc_id = isset($_GET['doc_id']) ? (int)$_GET['doc_id'] : 0;
        if ($doc_id <= 0) {
            if (ob_get_level()) ob_end_clean();
            header("HTTP/1.1 400 Bad Request");
            exit("Invalid document ID.");
        }

        // Only allow downloading accepted documents belonging to publicly visible document types
        $public_types = [
            'journal', 'conference', 'patent', 'fdp', 'fdp_attended', 'fdp_organised', 
            'conf_organised', 'scholarship', 'placement', 'higher_ed', 'award', 
            'student_event', 'student_body', 'student_journal', 'student_conference',
            'student_activity_file', 'dept_file'
        ];

        $doc = null;
        $stmt = $conn->prepare("
            SELECT d.file_path, dt.type_code 
            FROM documents d
            JOIN document_types dt ON d.type_id = dt.type_id
            WHERE d.doc_id = ? AND d.status = 'accepted'
        ");
        if ($stmt) {
            $stmt->bind_param("i", $doc_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $doc = $res->fetch_assoc();
            $stmt->close();
        }

        if (!$doc || !in_array($doc['type_code'], $public_types, true)) {
            include __DIR__ . '/../../public/404.php';
            exit;
        }

        $file_path = $doc['file_path'];
        if (empty($file_path)) {
            // Fallback for single-file documents which do not have a merged file_path in documents table
            $stmt = $conn->prepare("SELECT file_path FROM document_files WHERE doc_id = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param("i", $doc_id);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    $file_path = $row['file_path'];
                }
                $stmt->close();
            }
        }

        if (empty($file_path)) {
            include __DIR__ . '/../../public/404.php';
            exit;
        }

        $allowed_base = realpath(__DIR__ . '/../../uploads');
        $raw_target = __DIR__ . '/../../' . ltrim($file_path, '/\\');
        $canonical_path = realpath($raw_target);

        if (!$canonical_path || !$allowed_base || stripos($canonical_path, $allowed_base) !== 0 || !is_file($canonical_path)) {
            include __DIR__ . '/../../public/404.php';
            exit;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($canonical_path) ?: 'application/octet-stream';
        $allowed_mimes = [
            'application/pdf',
            'image/png',
            'image/jpeg',
            'image/jpg',
            'image/gif'
        ];
        if (!in_array($mime_type, $allowed_mimes, true)) {
            $mime_type = 'application/octet-stream';
        }

        $filename = str_replace(['"', "\r", "\n"], '', basename($canonical_path));

        while (ob_get_level()) ob_end_clean();
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('X-Content-Type-Options: nosniff');
        header('Expires: 0');
        header('Cache-Control: public, must-revalidate, max-age=3600');
        header('Pragma: public');
        header('Content-Length: ' . filesize($canonical_path));
        
        readfile($canonical_path);
        exit;
    }
}
