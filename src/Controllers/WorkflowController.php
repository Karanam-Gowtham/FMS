<?php
namespace App\Controllers;

use mysqli;

/**
 * WorkflowController
 * 
 * Provides visual dynamic workflow administration for FMS Administrators:
 * - View and configure multi-step approval pipelines
 * - Reassign document types to workflows
 * - Add/update/reorder steps with automated deterministic transitions
 * - Institutional audit logging for all changes
 */
class WorkflowController {

    private mysqli $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    /**
     * Enforces Administrator authentication.
     */
    private function ensureAdmin(): array {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        $auth = auth_context();
        $active_role = auth_active_role();
        $role_id = $active_role ? (int)$active_role['role_id'] : 0;

        if ($role_id !== ROLE_ADMIN) {
            $_SESSION['error_msg'] = "Access denied. Workflow management is restricted to Administrators.";
            header("Location: " . BASE_URL . "/public/index.php?route=dashboard");
            exit;
        }

        return $auth;
    }

    /**
     * Main Workflow Management Dashboard
     */
    public function index(): void {
        $auth = $this->ensureAdmin();

        $success_msg = $_SESSION['success_msg'] ?? null;
        $error_msg   = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['success_msg'], $_SESSION['error_msg']);

        // 1. Fetch all workflows with step count & doc type count
        $workflows = [];
        $w_stmt = $this->conn->query("
            SELECT w.workflow_id, w.workflow_key, w.label, w.description,
                   COUNT(DISTINCT ws.step_id) as step_count,
                   COUNT(DISTINCT dt.type_id) as doc_type_count
            FROM workflows w
            LEFT JOIN workflow_steps ws ON ws.workflow_id = w.workflow_id
            LEFT JOIN document_types dt ON dt.workflow_id = w.workflow_id
            GROUP BY w.workflow_id
            ORDER BY w.workflow_id ASC
        ");
        if ($w_stmt) {
            while ($row = $w_stmt->fetch_assoc()) {
                // Fetch ordered steps for this workflow
                $step_stmt = $this->conn->prepare("
                    SELECT ws.step_id, ws.step_order, ws.step_label, ws.responsible_role_id, ws.scope,
                           r.role_name
                    FROM workflow_steps ws
                    JOIN roles r ON r.role_id = ws.responsible_role_id
                    WHERE ws.workflow_id = ?
                    ORDER BY ws.step_order ASC
                ");
                $steps = [];
                if ($step_stmt) {
                    $step_stmt->bind_param("i", $row['workflow_id']);
                    $step_stmt->execute();
                    $s_res = $step_stmt->get_result();
                    while ($s_row = $s_res->fetch_assoc()) {
                        $steps[] = $s_row;
                    }
                    $step_stmt->close();
                }
                $row['steps'] = $steps;
                $workflows[] = $row;
            }
        }

        // 2. Fetch all document types and their mapped workflows
        $doc_types = [];
        $dt_stmt = $this->conn->query("
            SELECT dt.type_id, dt.type_code, dt.label, dt.workflow_id,
                   w.workflow_key, w.label as workflow_label
            FROM document_types dt
            LEFT JOIN workflows w ON w.workflow_id = dt.workflow_id
            ORDER BY dt.label ASC
        ");
        if ($dt_stmt) {
            while ($row = $dt_stmt->fetch_assoc()) {
                $doc_types[] = $row;
            }
        }

        // 3. Fetch available roles for workflow step reviewers
        $roles = [];
        $r_stmt = $this->conn->query("SELECT role_id, role_name FROM roles ORDER BY role_name ASC");
        if ($r_stmt) {
            while ($row = $r_stmt->fetch_assoc()) {
                $roles[] = $row;
            }
        }

        require __DIR__ . '/../Views/admin/workflows.php';
    }

    /**
     * Reassign a document type to a different workflow
     */
    public function assignType(): void {
        $auth = $this->ensureAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        csrfValidate();

        $type_id = isset($_POST['type_id']) ? (int)$_POST['type_id'] : 0;
        $workflow_id = isset($_POST['workflow_id']) ? (int)$_POST['workflow_id'] : 0;

        if ($type_id <= 0 || $workflow_id <= 0) {
            $_SESSION['error_msg'] = "Invalid document type or workflow selection.";
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        // Verify workflow exists
        $wf_stmt = $this->conn->prepare("SELECT label, workflow_key FROM workflows WHERE workflow_id = ?");
        $wf_stmt->bind_param("i", $workflow_id);
        $wf_stmt->execute();
        $wf_res = $wf_stmt->get_result();
        $wf = $wf_res->fetch_assoc();
        $wf_stmt->close();

        if (!$wf) {
            $_SESSION['error_msg'] = "Target workflow not found.";
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        // Get doc type info
        $dt_stmt = $this->conn->prepare("SELECT label FROM document_types WHERE type_id = ?");
        $dt_stmt->bind_param("i", $type_id);
        $dt_stmt->execute();
        $dt_res = $dt_stmt->get_result();
        $dt = $dt_res->fetch_assoc();
        $dt_stmt->close();

        if (!$dt) {
            $_SESSION['error_msg'] = "Document type not found.";
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        // Update document_types
        $up_stmt = $this->conn->prepare("UPDATE document_types SET workflow_id = ? WHERE type_id = ?");
        $up_stmt->bind_param("ii", $workflow_id, $type_id);
        if ($up_stmt->execute()) {
            system_audit_log(
                $this->conn,
                'WORKFLOW_ASSIGN_TYPE',
                "Reassigned document category '{$dt['label']}' to workflow '{$wf['label']}'",
                'workflow',
                $workflow_id
            );
            $_SESSION['success_msg'] = "Document category '{$dt['label']}' successfully mapped to '{$wf['label']}'.";
        } else {
            $_SESSION['error_msg'] = "Failed to update workflow mapping: " . $this->conn->error;
        }
        $up_stmt->close();

        header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows&tab=mappings");
        exit;
    }

    /**
     * Create or edit workflow basic metadata
     */
    public function saveWorkflow(): void {
        $auth = $this->ensureAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        csrfValidate();

        $workflow_id = isset($_POST['workflow_id']) ? (int)$_POST['workflow_id'] : 0;
        $label = trim($_POST['label'] ?? '');
        $workflow_key = trim($_POST['workflow_key'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($label)) {
            $_SESSION['error_msg'] = "Workflow title is required.";
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        // Sanitize key
        if (empty($workflow_key)) {
            $workflow_key = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($label)));
        } else {
            $workflow_key = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $workflow_key));
        }

        if ($workflow_id > 0) {
            // Update
            $check_stmt = $this->conn->prepare("SELECT workflow_id FROM workflows WHERE workflow_key = ? AND workflow_id != ?");
            $check_stmt->bind_param("si", $workflow_key, $workflow_id);
            $check_stmt->execute();
            if ($check_stmt->get_result()->num_rows > 0) {
                $check_stmt->close();
                $_SESSION['error_msg'] = "A workflow with key '{$workflow_key}' already exists.";
                header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
                exit;
            }
            $check_stmt->close();

            $up_stmt = $this->conn->prepare("UPDATE workflows SET label = ?, workflow_key = ?, description = ? WHERE workflow_id = ?");
            $up_stmt->bind_param("sssi", $label, $workflow_key, $description, $workflow_id);
            if ($up_stmt->execute()) {
                system_audit_log(
                    $this->conn,
                    'WORKFLOW_UPDATE',
                    "Updated workflow metadata for '{$label}' (ID: {$workflow_id})",
                    'workflow',
                    $workflow_id
                );
                $_SESSION['success_msg'] = "Workflow '{$label}' updated successfully.";
            } else {
                $_SESSION['error_msg'] = "Database error: " . $this->conn->error;
            }
            $up_stmt->close();
        } else {
            // Insert
            $check_stmt = $this->conn->prepare("SELECT workflow_id FROM workflows WHERE workflow_key = ?");
            $check_stmt->bind_param("s", $workflow_key);
            $check_stmt->execute();
            if ($check_stmt->get_result()->num_rows > 0) {
                $check_stmt->close();
                $_SESSION['error_msg'] = "A workflow with key '{$workflow_key}' already exists.";
                header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
                exit;
            }
            $check_stmt->close();

            $ins_stmt = $this->conn->prepare("INSERT INTO workflows (workflow_key, label, description) VALUES (?, ?, ?)");
            $ins_stmt->bind_param("sss", $workflow_key, $label, $description);
            if ($ins_stmt->execute()) {
                $new_id = $this->conn->insert_id;
                system_audit_log(
                    $this->conn,
                    'WORKFLOW_CREATE',
                    "Created new workflow '{$label}' (Key: {$workflow_key})",
                    'workflow',
                    $new_id
                );
                $_SESSION['success_msg'] = "Workflow '{$label}' created. You can now configure its approval steps.";
            } else {
                $_SESSION['error_msg'] = "Database error: " . $this->conn->error;
            }
            $ins_stmt->close();
        }

        header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
        exit;
    }

    /**
     * Configure steps for a workflow with deterministic transition generation
     */
    public function saveSteps(): void {
        $auth = $this->ensureAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        csrfValidate();

        $workflow_id = isset($_POST['workflow_id']) ? (int)$_POST['workflow_id'] : 0;
        if ($workflow_id <= 0) {
            $_SESSION['error_msg'] = "Invalid workflow ID.";
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        // Get workflow
        $wf_stmt = $this->conn->prepare("SELECT label FROM workflows WHERE workflow_id = ?");
        $wf_stmt->bind_param("i", $workflow_id);
        $wf_stmt->execute();
        $wf_row = $wf_stmt->get_result()->fetch_assoc();
        $wf_stmt->close();

        if (!$wf_row) {
            $_SESSION['error_msg'] = "Workflow not found.";
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        $step_labels = $_POST['step_labels'] ?? [];
        $role_ids    = $_POST['role_ids'] ?? [];
        $scopes      = $_POST['scopes'] ?? [];

        // Fetch existing steps for this workflow
        $old_steps = [];
        $os_stmt = $this->conn->prepare("SELECT step_id, step_order, step_label FROM workflow_steps WHERE workflow_id = ? ORDER BY step_order ASC");
        $os_stmt->bind_param("i", $workflow_id);
        $os_stmt->execute();
        $os_res = $os_stmt->get_result();
        while ($r = $os_res->fetch_assoc()) {
            $old_steps[] = $r;
        }
        $os_stmt->close();

        $new_count = count($step_labels);
        $old_count = count($old_steps);

        // Safety check: if steps are being removed, verify no pending documents are on those removed steps
        if ($old_count > $new_count) {
            for ($k = $new_count; $k < $old_count; $k++) {
                $removed_step_id = (int)$old_steps[$k]['step_id'];
                $chk_doc = $this->conn->prepare("SELECT COUNT(*) as pending_cnt FROM documents WHERE current_step = ? AND status = 'pending'");
                $chk_doc->bind_param("i", $removed_step_id);
                $chk_doc->execute();
                $doc_res = $chk_doc->get_result()->fetch_assoc();
                $chk_doc->close();

                if (($doc_res['pending_cnt'] ?? 0) > 0) {
                    $_SESSION['error_msg'] = "Cannot remove step '{$old_steps[$k]['step_label']}' because {$doc_res['pending_cnt']} pending document(s) are currently awaiting review at this step. Please process those documents before altering the pipeline.";
                    header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
                    exit;
                }
            }
        }

        // Start transaction
        $this->conn->begin_transaction();
        try {
            // First, delete all transitions for the current steps of this workflow to prevent foreign key blocks
            if (!empty($old_steps)) {
                $old_ids = array_column($old_steps, 'step_id');
                $placeholders = implode(',', array_fill(0, count($old_ids), '?'));
                $types = str_repeat('i', count($old_ids));
                
                $del_trans = $this->conn->prepare("DELETE FROM workflow_transitions WHERE step_id IN ($placeholders) OR to_step_id IN ($placeholders)");
                $del_trans->bind_param($types . $types, ...array_merge($old_ids, $old_ids));
                $del_trans->execute();
                $del_trans->close();
            }

            // Remove steps that are trimmed off if new_count < old_count
            if ($old_count > $new_count) {
                for ($k = $new_count; $k < $old_count; $k++) {
                    $del_s = $this->conn->prepare("DELETE FROM workflow_steps WHERE step_id = ?");
                    $del_s->bind_param("i", $old_steps[$k]['step_id']);
                    $del_s->execute();
                    $del_s->close();
                }
            }

            // Process each new step
            $active_step_ids = [];
            for ($i = 0; $i < $new_count; $i++) {
                $order = $i + 1;
                $label = trim($step_labels[$i] ?? "Step $order");
                $role_id = (int)($role_ids[$i] ?? 0);
                $scope = in_array(strtolower($scopes[$i] ?? ''), ['department', 'global'], true) ? strtolower($scopes[$i]) : 'department';

                if ($i < $old_count) {
                    // Update existing step row
                    $current_step_id = (int)$old_steps[$i]['step_id'];
                    $up_s = $this->conn->prepare("
                        UPDATE workflow_steps 
                        SET step_order = ?, step_label = ?, responsible_role_id = ?, scope = ? 
                        WHERE step_id = ?
                    ");
                    $up_s->bind_param("isisi", $order, $label, $role_id, $scope, $current_step_id);
                    $up_s->execute();
                    $up_s->close();
                    $active_step_ids[] = $current_step_id;
                } else {
                    // Insert new step row
                    $ins_s = $this->conn->prepare("
                        INSERT INTO workflow_steps (workflow_id, step_order, step_label, responsible_role_id, scope) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $ins_s->bind_param("iisis", $workflow_id, $order, $label, $role_id, $scope);
                    $ins_s->execute();
                    $active_step_ids[] = (int)$this->conn->insert_id;
                    $ins_s->close();
                }
            }

            // Deterministic Transitions Generation
            // Action IDs: approve = 1, reject = 2, resubmit = 3
            if (!empty($active_step_ids)) {
                $step_count = count($active_step_ids);
                $first_step_id = $active_step_ids[0];

                for ($idx = 0; $idx < $step_count; $idx++) {
                    $curr_id = $active_step_ids[$idx];
                    $is_last = ($idx === $step_count - 1);

                    // 1. Approve transition
                    if (!$is_last) {
                        $next_id = $active_step_ids[$idx + 1];
                        $tr_app = $this->conn->prepare("
                            INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) 
                            VALUES (?, 1, ?, 'pending')
                        ");
                        $tr_app->bind_param("ii", $curr_id, $next_id);
                        $tr_app->execute();
                        $tr_app->close();
                    } else {
                        // Final step approve -> Accepted
                        $tr_app = $this->conn->prepare("
                            INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) 
                            VALUES (?, 1, NULL, 'accepted')
                        ");
                        $tr_app->bind_param("i", $curr_id);
                        $tr_app->execute();
                        $tr_app->close();
                    }

                    // 2. Reject transition -> Rejected
                    $tr_rej = $this->conn->prepare("
                        INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) 
                        VALUES (?, 2, NULL, 'rejected')
                    ");
                    $tr_rej->bind_param("i", $curr_id);
                    $tr_rej->execute();
                    $tr_rej->close();

                    // 3. Resubmit transition -> Returns to step 1
                    $tr_res = $this->conn->prepare("
                        INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) 
                        VALUES (?, 3, ?, 'pending')
                    ");
                    $tr_res->bind_param("ii", $curr_id, $first_step_id);
                    $tr_res->execute();
                    $tr_res->close();
                }
            }

            $this->conn->commit();

            system_audit_log(
                $this->conn,
                'WORKFLOW_UPDATE_STEPS',
                "Updated pipeline steps for workflow '{$wf_row['label']}' ($new_count steps configured)",
                'workflow',
                $workflow_id
            );

            $_SESSION['success_msg'] = "Approval pipeline for '{$wf_row['label']}' updated successfully with $new_count step(s).";
        } catch (\Exception $e) {
            $this->conn->rollback();
            $_SESSION['error_msg'] = "Failed to update workflow steps: " . $e->getMessage();
        }

        header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
        exit;
    }

    /**
     * Delete a custom workflow safely
     */
    public function delete(): void {
        $auth = $this->ensureAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        csrfValidate();

        $workflow_id = isset($_POST['workflow_id']) ? (int)$_POST['workflow_id'] : 0;
        if ($workflow_id <= 0) {
            $_SESSION['error_msg'] = "Invalid workflow ID.";
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        // Get workflow
        $wf_stmt = $this->conn->prepare("SELECT label, workflow_key FROM workflows WHERE workflow_id = ?");
        $wf_stmt->bind_param("i", $workflow_id);
        $wf_stmt->execute();
        $wf = $wf_stmt->get_result()->fetch_assoc();
        $wf_stmt->close();

        if (!$wf) {
            $_SESSION['error_msg'] = "Workflow not found.";
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        // System core workflows protection
        if (in_array($wf['workflow_key'], ['department_standard', 'auto_accept'], true)) {
            $_SESSION['error_msg'] = "System core workflow '{$wf['label']}' cannot be deleted.";
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        // Check if any document type is assigned to this workflow
        $dt_chk = $this->conn->prepare("SELECT COUNT(*) as cnt FROM document_types WHERE workflow_id = ?");
        $dt_chk->bind_param("i", $workflow_id);
        $dt_chk->execute();
        $dt_row = $dt_chk->get_result()->fetch_assoc();
        $dt_chk->close();

        if (($dt_row['cnt'] ?? 0) > 0) {
            $_SESSION['error_msg'] = "Cannot delete workflow '{$wf['label']}' because {$dt_row['cnt']} document category(s) are currently assigned to it. Please reassign them first.";
            header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
            exit;
        }

        // Start transaction
        $this->conn->begin_transaction();
        try {
            // Get steps
            $st_stmt = $this->conn->prepare("SELECT step_id FROM workflow_steps WHERE workflow_id = ?");
            $st_stmt->bind_param("i", $workflow_id);
            $st_stmt->execute();
            $st_res = $st_stmt->get_result();
            $step_ids = [];
            while ($s = $st_res->fetch_assoc()) {
                $step_ids[] = (int)$s['step_id'];
            }
            $st_stmt->close();

            // Check if any documents reference these steps
            if (!empty($step_ids)) {
                $placeholders = implode(',', array_fill(0, count($step_ids), '?'));
                $types = str_repeat('i', count($step_ids));
                
                $doc_chk = $this->conn->prepare("SELECT COUNT(*) as doc_cnt FROM documents WHERE current_step IN ($placeholders)");
                $doc_chk->bind_param($types, ...$step_ids);
                $doc_chk->execute();
                $dc_row = $doc_chk->get_result()->fetch_assoc();
                $doc_chk->close();

                if (($dc_row['doc_cnt'] ?? 0) > 0) {
                    throw new \Exception("Cannot delete workflow because documents currently reference its steps.");
                }

                // Delete transitions
                $del_tr = $this->conn->prepare("DELETE FROM workflow_transitions WHERE step_id IN ($placeholders) OR to_step_id IN ($placeholders)");
                $del_tr->bind_param($types . $types, ...array_merge($step_ids, $step_ids));
                $del_tr->execute();
                $del_tr->close();

                // Delete steps
                $del_st = $this->conn->prepare("DELETE FROM workflow_steps WHERE workflow_id = ?");
                $del_st->bind_param("i", $workflow_id);
                $del_st->execute();
                $del_st->close();
            }

            // Delete workflow
            $del_wf = $this->conn->prepare("DELETE FROM workflows WHERE workflow_id = ?");
            $del_wf->bind_param("i", $workflow_id);
            $del_wf->execute();
            $del_wf->close();

            $this->conn->commit();

            system_audit_log(
                $this->conn,
                'WORKFLOW_DELETE',
                "Deleted workflow '{$wf['label']}' (Key: {$wf['workflow_key']})",
                'workflow',
                $workflow_id
            );

            $_SESSION['success_msg'] = "Workflow '{$wf['label']}' deleted successfully.";
        } catch (\Exception $e) {
            $this->conn->rollback();
            $_SESSION['error_msg'] = "Deletion failed: " . $e->getMessage();
        }

        header("Location: " . BASE_URL . "/public/index.php?route=admin/workflows");
        exit;
    }
}
