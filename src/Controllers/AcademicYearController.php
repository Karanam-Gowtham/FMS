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
        
        // Only Admin, HOD, IQAC, and Central Coordinator can manage academic years globally
        $can_manage = in_array((int)$active_role['role_id'], [ROLE_ADMIN, ROLE_HOD, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR], true);

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

            // Automatically normalize YYYY-YYYY (e.g. 2026-2027) into canonical YYYY-YY (e.g. 2026-27)
            if (preg_match('/^(\d{4})-(\d{4})$/', $new_year, $matches)) {
                $new_year = $matches[1] . '-' . substr($matches[2], -2);
            }

            if (!preg_match('/^\d{4}-\d{2}$/', $new_year)) {
                $_SESSION['error_msg'] = "Invalid Academic Year format. Please use YYYY-YY (e.g. 2025-26).";
                header("Location: " . BASE_URL . "/public/index.php?route=academic_years/list");
                exit();
            }

            $start_yr = (int)substr($new_year, 0, 4);
            $end_yy = (int)substr($new_year, 5, 2);
            $expected_end_yy = ($start_yr + 1) % 100;
            if ($end_yy !== $expected_end_yy || $start_yr < 1950 || $start_yr > 2100) {
                $_SESSION['error_msg'] = "Invalid Academic Year. Years must be consecutive (e.g. " . $start_yr . "-" . sprintf('%02d', $expected_end_yy) . ").";
                header("Location: " . BASE_URL . "/public/index.php?route=academic_years/list");
                exit();
            }

            $existing = null;
            $chk = $conn->prepare("SELECT year_id FROM academic_years WHERE year_label = ?");
            if ($chk) {
                $chk->bind_param("s", $new_year);
                $chk->execute();
                $existing = $chk->get_result()->fetch_assoc();
                $chk->close();
            }

            if ($existing) {
                $_SESSION['error_msg'] = "Academic Year '{$new_year}' already exists.";
            } else {
                try {
                    $stmt = $conn->prepare("INSERT INTO academic_years (year_label) VALUES (?)");
                    if ($stmt) {
                        $stmt->bind_param("s", $new_year);
                        $stmt->execute();
                        $stmt->close();
                        $_SESSION['success_msg'] = "Academic Year '{$new_year}' added successfully.";
                    } else {
                        $_SESSION['error_msg'] = "Database error: unable to prepare statement.";
                    }
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
        
        $sql = "SELECT * FROM academic_years ORDER BY year_label DESC";
        $result = $conn->query($sql);
        $academic_years = [];
        if ($result instanceof \mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $academic_years[] = $row;
            }
            $result->close();
        }

        include __DIR__ . '/../Views/academic_years/list.php';
    }
}
