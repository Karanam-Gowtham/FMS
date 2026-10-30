<?php
namespace App\Controllers;

use mysqli;

/**
 * Controller for Managing Institutional Academic Years
 */
class AcademicYearController {

    public function list(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();

        $auth = auth_context();
        $active_role = auth_active_role();

        if (!$active_role) {
            header("Location: " . BASE_URL . "/public/index.php?route=auth/select_role");
            exit();
        }

        global $conn;
        
        $role_id = (int)$active_role['role_id'];
        $can_manage = in_array($role_id, [ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_HOD], true);
        $is_admin = ($role_id === ROLE_ADMIN);

        // Handle POST actions if submitted directly to academic_years/list
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $subaction = $_POST['subaction'] ?? 'create';
            if ($subaction === 'set_current') {
                $this->setCurrent();
                return;
            } elseif ($subaction === 'delete') {
                $this->delete();
                return;
            } else {
                $this->create();
                return;
            }
        }
        
        $raw_error   = $_SESSION['error_msg']   ?? ($_GET['err'] ?? '');
        $raw_success = $_SESSION['success_msg'] ?? ($_GET['msg'] ?? '');
        $error_msg   = is_string($raw_error)   ? $raw_error   : '';
        $success_msg = is_string($raw_success) ? $raw_success : '';
        unset($_SESSION['error_msg'], $_SESSION['success_msg']);
        
        // Fetch academic years with mapped document counts
        $sql = "
            SELECT ay.year_id, ay.year_label, ay.is_current, ay.created_at,
                   COUNT(d.doc_id) AS doc_count
            FROM academic_years ay
            LEFT JOIN documents d ON ay.year_id = d.academic_year_id
            GROUP BY ay.year_id, ay.year_label, ay.is_current, ay.created_at
            ORDER BY ay.year_label DESC
        ";
        $result = $conn->query($sql);
        $academic_years = [];
        $total_documents_mapped = 0;
        $active_year_label = 'None Set';

        if ($result instanceof \mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $academic_years[] = $row;
                $total_documents_mapped += (int)$row['doc_count'];
                if ((int)$row['is_current'] === 1) {
                    $active_year_label = $row['year_label'];
                }
            }
            $result->close();
        }

        $stats = [
            'total_years' => count($academic_years),
            'active_year' => $active_year_label,
            'total_docs'  => $total_documents_mapped
        ];

        include __DIR__ . '/../Views/academic_years/list.php';
    }

    /**
     * Create a new academic year with format and continuity validation.
     */
    public function create(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        global $conn;

        $auth = auth_context();
        $active_role = auth_active_role();
        $role_id = $active_role ? (int)$active_role['role_id'] : 0;

        if (!in_array($role_id, [ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_HOD], true)) {
            $_SESSION['error_msg'] = "You do not have permission to add academic years.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
            return;
        }

        csrfValidate();
        $new_year = (isset($_POST['academic_year']) && is_string($_POST['academic_year'])) ? trim($_POST['academic_year']) : '';
        
        if (empty($new_year)) {
            $_SESSION['error_msg'] = "Academic Year cannot be empty.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
            return;
        }

        // Automatically normalize YYYY-YYYY (e.g. 2026-2027) into canonical YYYY-YY (e.g. 2026-27)
        if (preg_match('/^(\d{4})-(\d{4})$/', $new_year, $matches)) {
            $new_year = $matches[1] . '-' . substr($matches[2], -2);
        }

        if (!preg_match('/^\d{4}-\d{2}$/', $new_year)) {
            $_SESSION['error_msg'] = "Invalid Academic Year format. Please use YYYY-YY (e.g. 2025-26).";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
            return;
        }

        $start_yr = (int)substr($new_year, 0, 4);
        $end_yy = (int)substr($new_year, 5, 2);
        $expected_end_yy = ($start_yr + 1) % 100;
        if ($end_yy !== $expected_end_yy || $start_yr < 1950 || $start_yr > 2100) {
            $_SESSION['error_msg'] = "Invalid Academic Year. Years must be consecutive (e.g. " . $start_yr . "-" . sprintf('%02d', $expected_end_yy) . ").";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
            return;
        }

        $chk = $conn->prepare("SELECT year_id FROM academic_years WHERE year_label = ?");
        if ($chk) {
            $chk->bind_param("s", $new_year);
            $chk->execute();
            $existing = $chk->get_result()->fetch_assoc();
            $chk->close();
            if ($existing) {
                $_SESSION['error_msg'] = "Academic Year '{$new_year}' already exists.";
                $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
                return;
            }
        }

        try {
            $stmt = $conn->prepare("INSERT INTO academic_years (year_label, is_current) VALUES (?, 0)");
            if ($stmt) {
                $stmt->bind_param("s", $new_year);
                $stmt->execute();
                $new_id = $conn->insert_id;
                $stmt->close();

                if (function_exists('system_audit_log')) {
                    $actor_uid = (int)($auth['user_id'] ?? 0);
                    system_audit_log($conn, 'ACADEMIC_YEAR_CREATE', "Added new academic year '{$new_year}' (ID #{$new_id})", 'academic_year', $new_id, $actor_uid);
                }

                $_SESSION['success_msg'] = "Academic Year '{$new_year}' added successfully.";
            } else {
                $_SESSION['error_msg'] = "Database error: unable to prepare insert statement.";
            }
        } catch (\Throwable $e) {
            error_log("Failed to add academic year: " . $e->getMessage());
            $_SESSION['error_msg'] = "Failed to add academic year: " . $e->getMessage();
        }

        $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
    }

    /**
     * Designate an academic year as the active / current academic year for the college.
     */
    public function setCurrent(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        global $conn;

        $auth = auth_context();
        $active_role = auth_active_role();
        $role_id = $active_role ? (int)$active_role['role_id'] : 0;

        if (!in_array($role_id, [ROLE_ADMIN, ROLE_IQAC], true)) {
            $_SESSION['error_msg'] = "Only Administrators and IQAC can designate the active academic year.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
            return;
        }

        csrfValidate();
        $year_id = (int)($_POST['year_id'] ?? 0);

        if ($year_id <= 0) {
            $_SESSION['error_msg'] = "Invalid academic year ID.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
            return;
        }

        // Verify target exists
        $stmt = $conn->prepare("SELECT year_label FROM academic_years WHERE year_id = ?");
        $stmt->bind_param("i", $year_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            $_SESSION['error_msg'] = "Academic Year not found.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
            return;
        }

        $year_label = $row['year_label'];

        $conn->begin_transaction();
        try {
            $conn->query("UPDATE academic_years SET is_current = 0");
            $u_stmt = $conn->prepare("UPDATE academic_years SET is_current = 1 WHERE year_id = ?");
            $u_stmt->bind_param("i", $year_id);
            $u_stmt->execute();
            $u_stmt->close();

            $conn->commit();

            if (function_exists('system_audit_log')) {
                $actor_uid = (int)($auth['user_id'] ?? 0);
                system_audit_log($conn, 'ACADEMIC_YEAR_SET_CURRENT', "Designated Academic Year '{$year_label}' as the current active institutional year", 'academic_year', $year_id, $actor_uid);
            }

            $_SESSION['success_msg'] = "Academic Year '{$year_label}' is now designated as the active institutional year.";
        } catch (\Throwable $t) {
            $conn->rollback();
            error_log("Failed to set current academic year: " . $t->getMessage());
            $_SESSION['error_msg'] = "Failed to update active academic year: " . $t->getMessage();
        }

        $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
    }

    /**
     * Delete an academic year with strict protection against deleting years that have documents.
     */
    public function delete(): void {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        global $conn;

        $auth = auth_context();
        $active_role = auth_active_role();
        $role_id = $active_role ? (int)$active_role['role_id'] : 0;

        if ($role_id !== ROLE_ADMIN) {
            $_SESSION['error_msg'] = "Only Administrators can delete academic years.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
            return;
        }

        csrfValidate();
        $year_id = (int)($_POST['year_id'] ?? 0);

        if ($year_id <= 0) {
            $_SESSION['error_msg'] = "Invalid academic year ID.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
            return;
        }

        // Fetch year info
        $stmt = $conn->prepare("SELECT year_label, is_current FROM academic_years WHERE year_id = ?");
        $stmt->bind_param("i", $year_id);
        $stmt->execute();
        $target = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$target) {
            $_SESSION['error_msg'] = "Academic Year not found.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
            return;
        }

        if ((int)$target['is_current'] === 1) {
            $_SESSION['error_msg'] = "Cannot delete the currently active academic year. Please designate another year as active first.";
            $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
            return;
        }

        // Safe integrity check via doc_can_delete_academic_year
        $can_delete = doc_can_delete_academic_year($conn, $year_id);
        if (!$can_delete['allowed']) {
            $_SESSION['error_msg'] = $can_delete['reason'];
            $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
            return;
        }

        try {
            $del_stmt = $conn->prepare("DELETE FROM academic_years WHERE year_id = ?");
            $del_stmt->bind_param("i", $year_id);
            $del_stmt->execute();
            $del_stmt->close();

            if (function_exists('system_audit_log')) {
                $actor_uid = (int)($auth['user_id'] ?? 0);
                system_audit_log($conn, 'ACADEMIC_YEAR_DELETE', "Deleted unused academic year '{$target['year_label']}' (ID #{$year_id})", 'academic_year', $year_id, $actor_uid);
            }

            $_SESSION['success_msg'] = "Academic Year '{$target['year_label']}' was deleted successfully.";
        } catch (\Throwable $e) {
            error_log("Failed to delete academic year: " . $e->getMessage());
            $_SESSION['error_msg'] = "Database error while deleting academic year: " . $e->getMessage();
        }

        $this->safeRedirect(BASE_URL . "/public/index.php?route=academic_years/list");
    }

    /**
     * Environment-aware redirect that doesn't terminate PHP process under CLI / unit tests.
     */
    protected function safeRedirect(string $url): void {
        if (php_sapi_name() === 'cli') {
            return;
        }
        header("Location: " . $url);
        exit;
    }
}
