<?php
namespace App\Controllers;

class PublicController {

    public function department() {
        $dept_param = isset($_GET['dept']) ? trim($_GET['dept']) : '';
        global $conn;

        // Lookup department in database
        $dept_info = null;
        if ($dept_param !== '') {
            $stmt = $conn->prepare("SELECT * FROM departments WHERE dept_name = ?");
            if ($stmt) {
                $stmt->bind_param("s", $dept_param);
                $stmt->execute();
                $dept_info = $stmt->get_result()->fetch_assoc();
                $stmt->close();
            }
        }

        // If dept not found, handle it gracefully
        if (!$dept_info) {
            echo "<div style='text-align:center; padding: 50px; font-family:sans-serif;'>";
            echo "<h2>Department Not Found</h2>";
            echo "<p>The department '" . htmlspecialchars($dept_param) . "' does not exist in our records.</p>";
            echo "<a href='" . BASE_URL . "/index.php' style='color:#3b82f6;'>Return to Home</a>";
            echo "</div>";
            exit;
        }

        $dept_id = (int)$dept_info['dept_id'];
        $dept_name = $dept_info['dept_name'];

        // Fetch active faculty members in this department
        $faculty = [];
        $stmt = $conn->prepare("
            SELECT u.*, r.role_name
            FROM users u
            JOIN user_roles ur ON u.user_id = ur.user_id
            JOIN roles r ON ur.role_id = r.role_id
            WHERE ur.dept_id = ? AND u.status = 'active' AND r.role_name IN ('Faculty', 'HOD')
            GROUP BY u.user_id
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
            SELECT d.doc_id, dm.meta_value as original_file_name, dt.label as type_name, dt.type_code,
                   u.full_name as uploader_name, u.user_id, ay.year_label as year_name, 
                   d.created_at, d.file_path 
            FROM documents d
            LEFT JOIN document_types dt ON d.type_id = dt.type_id
            LEFT JOIN users u ON d.uploaded_by = u.user_id
            LEFT JOIN academic_years ay ON d.academic_year_id = ay.year_id
            LEFT JOIN document_metadata dm ON d.doc_id = dm.doc_id AND dm.meta_key = 'title'
            WHERE d.dept_id = ? AND d.status = 'accepted' 
            AND dt.type_code IN ('journal', 'conference', 'patent', 'fdp_attended', 'fdp_organised', 'conf_organised', 'scholarship', 'placement', 'higher_ed', 'award', 'student_event', 'student_body', 'student_journal', 'student_conference')
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
            
            if (in_array($tCode, ['journal', 'conference', 'student_journal', 'student_conference'])) {
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
        if ($doc_id <= 0) die("Invalid document ID.");

        // Only allow downloading accepted documents
        $stmt = $conn->prepare("SELECT file_path FROM documents WHERE doc_id = ? AND status = 'accepted'");
        $stmt->bind_param("i", $doc_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $doc = $res->fetch_assoc();
        $stmt->close();

        if (!$doc) die("File not found or not available publicly.");

        $file_path = $doc['file_path'];
        if (empty($file_path)) {
            // Fallback for single-file documents which do not have a merged file_path in documents table
            $stmt = $conn->prepare("SELECT file_path FROM document_files WHERE doc_id = ? LIMIT 1");
            $stmt->bind_param("i", $doc_id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $file_path = $row['file_path'];
            }
            $stmt->close();
        }

        if (empty($file_path)) die("File not found or not available publicly.");

        $real_path = __DIR__ . '/../../' . ltrim($file_path, '/\\');
        if (!file_exists($real_path)) die("The physical file is missing from the server.");

        $mime_type = 'application/pdf';
        if (strtolower(pathinfo($real_path, PATHINFO_EXTENSION)) === 'png') $mime_type = 'image/png';
        if (strtolower(pathinfo($real_path, PATHINFO_EXTENSION)) === 'jpg' || strtolower(pathinfo($real_path, PATHINFO_EXTENSION)) === 'jpeg') $mime_type = 'image/jpeg';

        if (ob_get_level()) ob_end_clean();
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: inline; filename="' . basename($real_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($real_path));
        
        readfile($real_path);
        exit;
    }
}
