<?php
namespace App\Controllers;

class AcademicYearController {
    public function list() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();

        $auth = auth_context();
        $active_role = auth_active_role();

        if (!$active_role) {
            header("Location: " . BASE_URL . "/public/index.php?route=auth/select_role");
            exit();
        }

        global $conn;
        
        // Only Admin and HOD can manage academic years globally
        $can_manage = in_array($active_role['role_id'], [ROLE_ADMIN, ROLE_HOD]);

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            if (!$can_manage) {
                $_SESSION['error_msg'] = "You do not have permission to manage academic years.";
                header("Location: " . BASE_URL . "/public/index.php?route=academic_years/list");
                exit();
            }

            csrfValidate();
            $new_year = trim($_POST['academic_year'] ?? '');
            if (empty($new_year)) {
                $_SESSION['error_msg'] = "Academic Year cannot be empty.";
                header("Location: " . BASE_URL . "/public/index.php?route=academic_years/list");
                exit();
            }

            if (!preg_match('/^\d{4}-\d{2,4}$/', $new_year)) {
                $_SESSION['error_msg'] = "Invalid Academic Year format. Please use YYYY-YY (e.g. 2025-26).";
                header("Location: " . BASE_URL . "/public/index.php?route=academic_years/list");
                exit();
            }

            $chk = $conn->prepare("SELECT year_id FROM academic_years WHERE year_label = ?");
            $chk->bind_param("s", $new_year);
            $chk->execute();
            $existing = $chk->get_result()->fetch_assoc();
            $chk->close();

            if ($existing) {
                $_SESSION['error_msg'] = "Academic Year '{$new_year}' already exists.";
            } else {
                try {
                    $stmt = $conn->prepare("INSERT INTO academic_years (year_label) VALUES (?)");
                    $stmt->bind_param("s", $new_year);
                    $stmt->execute();
                    $stmt->close();
                    $_SESSION['success_msg'] = "Academic Year '{$new_year}' added successfully.";
                } catch (\Exception $e) {
                    $_SESSION['error_msg'] = "Failed to add academic year: " . $e->getMessage();
                }
            }
            header("Location: " . BASE_URL . "/public/index.php?route=academic_years/list");
            exit();
        }
        
        $error_msg = $_SESSION['error_msg'] ?? '';
        $success_msg = $_SESSION['success_msg'] ?? '';
        unset($_SESSION['error_msg'], $_SESSION['success_msg']);
        
        $sql = "SELECT * FROM academic_years ORDER BY year_id DESC";
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
