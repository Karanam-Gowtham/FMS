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
            SELECT dm.meta_value as original_file_name, dt.label as type_name, 
                   u.full_name as uploader_name, ay.year_label as year_name, 
                   d.created_at, d.file_path 
            FROM documents d
            LEFT JOIN document_types dt ON d.type_id = dt.type_id
            LEFT JOIN users u ON d.uploaded_by = u.user_id
            LEFT JOIN academic_years ay ON d.academic_year_id = ay.year_id
            LEFT JOIN document_metadata dm ON d.doc_id = dm.doc_id AND dm.meta_key = 'title'
            WHERE d.dept_id = ? AND d.status = 'accepted'
            ORDER BY d.created_at DESC
        ");
        if ($stmt) {
            $stmt->bind_param("i", $dept_id);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) { 
                if(empty($row['file_path'])) $row['file_path'] = '#';
                $public_docs[] = $row; 
            }
            $stmt->close();
        }

        // Calculate research stats
        $papers_count = 0;
        $patents_count = 0;
        $fdps_count = 0;

        foreach ($public_docs as $doc) {
            if (stripos($doc['type_name'], 'Paper') !== false || stripos($doc['type_name'], 'Conference') !== false) {
                $papers_count++;
            } elseif (stripos($doc['type_name'], 'Patent') !== false) {
                $patents_count++;
            } elseif (stripos($doc['type_name'], 'FDP') !== false) {
                $fdps_count++;
            }
        }

        include __DIR__ . '/../Views/public/department.php';
    }
}
