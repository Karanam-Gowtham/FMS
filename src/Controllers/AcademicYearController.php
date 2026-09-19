<?php
namespace App\Controllers;

class AcademicYearController {
    public function list() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();

        $auth = auth_context();
        $active_role = auth_active_role();

        if (!$active_role) {
            header("Location: " . BASE_URL . "/public/index.php?route=dashboard");
            exit();
        }

        $conn = db_connect();
        
        // Only Admin and HOD can manage academic years globally
        $can_manage = in_array($active_role['role_id'], [ROLE_ADMIN, ROLE_HOD]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $can_manage) {
            csrfValidate();
            $new_year = trim($_POST['academic_year'] ?? '');
            if (!empty($new_year)) {
                $stmt = $conn->prepare("INSERT INTO academic_years (year_range) VALUES (?)");
                $stmt->bind_param("s", $new_year);
                $stmt->execute();
                $stmt->close();
                header("Location: " . BASE_URL . "/public/index.php?route=academic_years/list");
                exit();
            }
        }
        
        $sql = "SELECT * FROM academic_years ORDER BY id DESC";
        $result = $conn->query($sql);
        $academic_years = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $academic_years[] = $row;
            }
        }

        include __DIR__ . '/../Views/academic_years/list.php';
    }
}
