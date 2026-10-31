<?php
declare(strict_types=1);

namespace App\Controllers;

use mysqli_result;
use Throwable;

/**
 * Controller for Managing Institutional Academic Years
 */
class AcademicYearController extends BaseController {

    public function list(): void {
        $this->requireRole([ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_HOD]);

        $auth = $this->auth;
        $active_role = $this->activeRole;
        $role_id = $this->activeRoleId;
        $can_manage = in_array($role_id, [ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_HOD], true);
        $is_admin = ($role_id === ROLE_ADMIN);

        // Handle POST actions if submitted directly to academic_years/list
        if ($this->isPost()) {
            $subaction = $this->stringParam('subaction', 'create', 'post');
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

        $flash = $this->getFlashMessages();
        $error_msg   = $flash['error']   ?? ($this->stringParam('err', '', 'get'));
        $success_msg = $flash['success'] ?? ($this->stringParam('msg', '', 'get'));

        // Fetch academic years with mapped document counts
        $sql = "
            SELECT ay.year_id, ay.year_label, ay.is_current, ay.created_at,
                   COUNT(d.doc_id) AS doc_count
            FROM academic_years ay
            LEFT JOIN documents d ON ay.year_id = d.academic_year_id
            GROUP BY ay.year_id, ay.year_label, ay.is_current, ay.created_at
            ORDER BY ay.year_label DESC
        ";
        $result = $this->conn->query($sql);
        $academic_years = [];
        $total_documents_mapped = 0;
        $active_year_label = 'None Set';

        if ($result instanceof mysqli_result) {
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

        $this->renderView('academic_years/list', get_defined_vars());
    }

    /**
     * Create a new academic year with format and continuity validation.
     */
    public function create(): void {
        $this->requireRole([ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_HOD]);

        $this->validateCsrf();
        $new_year = $this->stringParam('academic_year', '', 'post');

        if (empty($new_year)) {
            $this->flashError("Academic Year cannot be empty.");
            $this->redirect(BASE_URL . "/public/index.php?route=academic_years/list");
        }

        // Automatically normalize YYYY-YYYY (e.g. 2026-2027) into canonical YYYY-YY (e.g. 2026-27)
        if (preg_match('/^(\d{4})-(\d{4})$/', $new_year, $matches)) {
            $new_year = $matches[1] . '-' . substr($matches[2], -2);
        }

        if (!preg_match('/^\d{4}-\d{2}$/', $new_year)) {
            $this->flashError("Invalid Academic Year format. Please use YYYY-YY (e.g. 2025-26).");
            $this->redirect(BASE_URL . "/public/index.php?route=academic_years/list");
        }

        $start_yr = (int)substr($new_year, 0, 4);
        $end_yy = (int)substr($new_year, 5, 2);
        $expected_end_yy = ($start_yr + 1) % 100;
        if ($end_yy !== $expected_end_yy || $start_yr < 1950 || $start_yr > 2100) {
            $this->flashError("Invalid Academic Year. Years must be consecutive (e.g. " . $start_yr . "-" . sprintf('%02d', $expected_end_yy) . ").");
            $this->redirect(BASE_URL . "/public/index.php?route=academic_years/list");
        }

        $chk = $this->conn->prepare("SELECT year_id FROM academic_years WHERE year_label = ?");
        if ($chk) {
            $chk->bind_param("s", $new_year);
            $chk->execute();
            $existing = $chk->get_result()->fetch_assoc();
            $chk->close();
            if ($existing) {
                $this->flashError("Academic Year '{$new_year}' already exists.");
                $this->redirect(BASE_URL . "/public/index.php?route=academic_years/list");
            }
        }

        $stmt = $this->conn->prepare("INSERT INTO academic_years (year_label, is_current) VALUES (?, 0)");
        if ($stmt) {
            $stmt->bind_param("s", $new_year);
            if ($stmt->execute()) {
                $new_id = $this->conn->insert_id;
                system_audit_log($this->conn, 'ACADEMIC_YEAR_CREATE', "Added new academic year '{$new_year}' (ID #{$new_id})", 'academic_year', $new_id, $this->userId);
                $this->flashSuccess("Academic Year '{$new_year}' added successfully.");
            } else {
                $this->flashError("Database error while creating academic year: " . $stmt->error);
            }
            $stmt->close();
        } else {
            $this->flashError("Database error preparing statement.");
        }

        $this->redirect(BASE_URL . "/public/index.php?route=academic_years/list");
    }

    /**
     * Designate a specific academic year as the institutional active year.
     */
    public function setCurrent(): void {
        $this->requireRole([ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_HOD]);

        $this->validateCsrf();
        $year_id = $this->intParam('year_id', 0, 'post');

        if ($year_id <= 0) {
            $this->flashError("Invalid Academic Year selection.");
            $this->redirect(BASE_URL . "/public/index.php?route=academic_years/list");
        }

        $stmt = $this->conn->prepare("SELECT year_label FROM academic_years WHERE year_id = ?");
        $stmt->bind_param("i", $year_id);
        $stmt->execute();
        $target = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$target) {
            $this->flashError("Academic Year not found.");
            $this->redirect(BASE_URL . "/public/index.php?route=academic_years/list");
        }

        $this->conn->begin_transaction();
        try {
            $this->conn->query("UPDATE academic_years SET is_current = 0");
            $u_stmt = $this->conn->prepare("UPDATE academic_years SET is_current = 1 WHERE year_id = ?");
            $u_stmt->bind_param("i", $year_id);
            $u_stmt->execute();
            $u_stmt->close();

            $this->conn->commit();
            system_audit_log($this->conn, 'ACADEMIC_YEAR_SET_ACTIVE', "Designated '{$target['year_label']}' (ID #{$year_id}) as the institutional active academic year", 'academic_year', $year_id, $this->userId);
            $this->flashSuccess("Active academic year successfully changed to '{$target['year_label']}'.");
        } catch (Throwable $e) {
            $this->conn->rollback();
            $this->flashError("Failed to update active year: " . $e->getMessage());
        }

        $this->redirect(BASE_URL . "/public/index.php?route=academic_years/list");
    }

    /**
     * Delete an academic year only if it has zero linked documents and is not current.
     */
    public function delete(): void {
        $this->requireRole([ROLE_ADMIN, ROLE_IQAC, ROLE_CENTRAL_COORDINATOR, ROLE_HOD]);

        $this->validateCsrf();
        $year_id = $this->intParam('year_id', 0, 'post');

        if ($year_id <= 0) {
            $this->flashError("Invalid Academic Year ID.");
            $this->redirect(BASE_URL . "/public/index.php?route=academic_years/list");
        }

        $stmt = $this->conn->prepare("SELECT year_label, is_current FROM academic_years WHERE year_id = ?");
        $stmt->bind_param("i", $year_id);
        $stmt->execute();
        $target = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$target) {
            $this->flashError("Academic Year not found.");
            $this->redirect(BASE_URL . "/public/index.php?route=academic_years/list");
        }

        if ((int)$target['is_current'] === 1) {
            $this->flashError("Cannot delete the currently active academic year. Please designate another year as active first.");
            $this->redirect(BASE_URL . "/public/index.php?route=academic_years/list");
        }

        $can_delete = doc_can_delete_academic_year($this->conn, $year_id);
        if (!$can_delete['allowed']) {
            $this->flashError($can_delete['reason']);
            $this->redirect(BASE_URL . "/public/index.php?route=academic_years/list");
        }

        try {
            $del_stmt = $this->conn->prepare("DELETE FROM academic_years WHERE year_id = ?");
            $del_stmt->bind_param("i", $year_id);
            $del_stmt->execute();
            $del_stmt->close();

            system_audit_log($this->conn, 'ACADEMIC_YEAR_DELETE', "Deleted unused academic year '{$target['year_label']}' (ID #{$year_id})", 'academic_year', $year_id, $this->userId);
            $this->flashSuccess("Academic Year '{$target['year_label']}' was deleted successfully.");
        } catch (Throwable $e) {
            error_log("Failed to delete academic year: " . $e->getMessage());
            $this->flashError("Database error while deleting academic year: " . $e->getMessage());
        }

        $this->redirect(BASE_URL . "/public/index.php?route=academic_years/list");
    }
}
