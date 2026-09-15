<?php
declare(strict_types=1);

/**
 * FMS Workflow Engine
 *
 * Fully data-driven workflow engine. All step sequences, role assignments,
 * scope rules, and transition logic come from database configuration:
 *
 *   workflows → workflow_steps → workflow_transitions → workflow_actions
 *
 * This file contains NO hard-coded role names, department names, step sequences,
 * or document category logic. The engine purely reads the database to determine
 * valid transitions and resulting states.
 */

// ============================================================
// WORKFLOW RESOLUTION
// ============================================================

/**
 * Resolves the workflow for a document type.
 *
 * @param mysqli $conn
 * @param int    $doc_type_id
 * @return array|null Workflow record, or null if not found/inactive
 */
function wf_get_workflow_for_type(mysqli $conn, int $doc_type_id): ?array
{
    $stmt = $conn->prepare(
        "SELECT w.workflow_id, w.workflow_key, w.label
         FROM workflows w
         JOIN document_types dt ON dt.workflow_id = w.workflow_id
         WHERE dt.type_id = ?"
    );
    $stmt->bind_param('i', $doc_type_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/**
 * Gets all steps for a workflow, ordered by step_order.
 *
 * @param mysqli $conn
 * @param int    $workflow_id
 * @return array List of step records
 */
function wf_get_steps(mysqli $conn, int $workflow_id): array
{
    $stmt = $conn->prepare(
        "SELECT ws.step_id, ws.step_order, ws.step_label,
                ws.responsible_role_id, ws.scope,
                r.role_name
         FROM workflow_steps ws
         JOIN roles r ON r.role_id = ws.responsible_role_id
         WHERE ws.workflow_id = ?
         ORDER BY ws.step_order ASC"
    );
    $stmt->bind_param('i', $workflow_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $steps = [];
    while ($row = $result->fetch_assoc()) {
        $steps[] = $row;
    }
    $stmt->close();
    return $steps;
}

/**
 * Gets the first step of a workflow.
 *
 * @param mysqli $conn
 * @param int    $workflow_id
 * @return array|null First step record, or null if zero-step workflow
 */
function wf_get_first_step(mysqli $conn, int $workflow_id): ?array
{
    $steps = wf_get_steps($conn, $workflow_id);
    return $steps[0] ?? null;
}

/**
 * Gets a single workflow step by its ID.
 *
 * @param mysqli $conn
 * @param int    $step_id
 * @return array|null Step record with role_name
 */
function wf_get_step(mysqli $conn, int $step_id): ?array
{
    $stmt = $conn->prepare(
        "SELECT ws.step_id, ws.workflow_id, ws.step_order, ws.step_label,
                ws.responsible_role_id, ws.scope,
                r.role_name
         FROM workflow_steps ws
         JOIN roles r ON r.role_id = ws.responsible_role_id
         WHERE ws.step_id = ?"
    );
    $stmt->bind_param('i', $step_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

// ============================================================
// TRANSITION LOOKUP
// ============================================================

/**
 * Gets the transition for a given step + action combination.
 *
 * @param mysqli $conn
 * @param int    $step_id
 * @param string $action_key  'approve', 'reject', or 'resubmit'
 * @return array|null Transition record, or null if no valid transition
 */
function wf_get_transition(mysqli $conn, int $step_id, string $action_key): ?array
{
    $stmt = $conn->prepare(
        "SELECT wt.transition_id, wt.step_id, wt.action_id, wt.to_step_id, wt.resulting_status,
                wa.action_key, wa.label AS action_label
         FROM workflow_transitions wt
         JOIN workflow_actions wa ON wa.action_id = wt.action_id
         WHERE wt.step_id = ? AND wa.action_key = ?"
    );
    $stmt->bind_param('is', $step_id, $action_key);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/**
 * Gets all transitions available at a given step.
 *
 * @param mysqli $conn
 * @param int    $step_id
 * @return array List of transition records with action details
 */
function wf_get_transitions_for_step(mysqli $conn, int $step_id): array
{
    $stmt = $conn->prepare(
        "SELECT wt.transition_id, wt.step_id, wt.action_id, wt.to_step_id, wt.resulting_status,
                wa.action_key, wa.label AS action_label
         FROM workflow_transitions wt
         JOIN workflow_actions wa ON wa.action_id = wt.action_id
         WHERE wt.step_id = ?
         ORDER BY wa.action_id ASC"
    );
    $stmt->bind_param('i', $step_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transitions = [];
    while ($row = $result->fetch_assoc()) {
        $transitions[] = $row;
    }
    $stmt->close();
    return $transitions;
}

// ============================================================
// AUTHORIZATION CHECK
// ============================================================

/**
 * Checks whether a user can perform an action on a document at its current step.
 *
 * Authorization is derived from:
 *   - workflow_steps.responsible_role_id
 *   - workflow_steps.scope
 *   - user's roles from $_SESSION['_fms_auth']['roles']
 *   - document's dept_id
 *
 * @param array  $step       Step record (from wf_get_step)
 * @param array  $auth       The $_SESSION['_fms_auth'] array
 * @param int    $doc_dept_id The document's dept_id
 * @return bool True if the user has the required role + scope
 */
function wf_can_user_act(array $step, array $auth, int $doc_dept_id): bool
{
    $required_role = (int)$step['responsible_role_id'];
    $scope = $step['scope'];

    foreach ($auth['roles'] as $user_role) {
        if ((int)$user_role['role_id'] !== $required_role) {
            continue;
        }

        // Role matches. Check scope.
        if ($scope === 'global') {
            return true;
        }

        // scope === 'department': user's dept_id for this role must match document's dept_id
        if ((int)$user_role['dept_id'] === $doc_dept_id) {
            return true;
        }
    }

    return false;
}

// ============================================================
// ACTION DETERMINATION
// ============================================================

/**
 * Determines what actions a user can perform on a document.
 *
 * Returns an array of action_key strings the user is allowed to perform.
 *
 * @param mysqli $conn
 * @param array  $document   Document record (must include: doc_id, status, current_step, uploaded_by, dept_id, doc_type_id)
 * @param array  $auth       The $_SESSION['_fms_auth'] array
 * @return array List of allowed action keys (e.g. ['approve', 'reject'] or ['resubmit'])
 */
function wf_get_allowed_actions(mysqli $conn, array $document, array $auth): array
{
    $status     = $document['status'];
    $step_id    = $document['current_step'];
    $uploader   = (int)$document['uploaded_by'];
    $user_id    = (int)$auth['user_id'];
    $doc_dept   = (int)$document['dept_id'];

    // Terminal state: no actions
    if ($status === 'accepted') {
        return [];
    }

    // Rejected: only the original uploader can resubmit
    if ($status === 'rejected') {
        if ($user_id === $uploader && $step_id !== null) {
            $transition = wf_get_transition($conn, (int)$step_id, 'resubmit');
            if ($transition) {
                return ['resubmit'];
            }
        }
        return [];
    }

    // Pending: check if user can act at the current step
    if ($status !== 'pending' || $step_id === null) {
        return [];
    }

    $step = wf_get_step($conn, (int)$step_id);
    if (!$step) {
        return [];
    }

    // Check authorization: does user have the required role + scope?
    if (!wf_can_user_act($step, $auth, $doc_dept)) {
        return [];
    }

    // User can act. Return available non-resubmit actions for this step.
    $transitions = wf_get_transitions_for_step($conn, (int)$step_id);
    $actions = [];
    foreach ($transitions as $t) {
        if ($t['action_key'] !== 'resubmit') {
            $actions[] = $t['action_key'];
        }
    }

    return $actions;
}

// ============================================================
// ACTION EXECUTION
// ============================================================

/**
 * Executes a workflow action on a document.
 *
 * The engine queries workflow_transitions for the given step + action to determine
 * the resulting state and next step. It does NOT contain any hard-coded logic
 * about which role goes to which step.
 *
 * @param mysqli  $conn
 * @param int     $doc_id
 * @param int     $actor_user_id
 * @param string  $action_key  'approve', 'reject', or 'resubmit'
 * @param string  $remarks     Optional remarks/rejection reason
 * @return array ['success' => bool, 'error' => string|null, 'new_status' => string|null]
 */
function wf_execute_action(mysqli $conn, int $doc_id, int $actor_user_id, string $action_key, string $remarks = '', string $table_name = 'documents', string $id_col = 'doc_id'): array
{
    // Validate table name to prevent SQL injection
    $allowed_tables = ['documents', 'patents_table', 'published_tab', 'conference_tab', 'fdps_tab', 'conf_org_tab', 'fdps_org_tab', 'dept_files', 's_journal_tab', 's_conference_tab', 's_bodies', 's_events'];
    if (!in_array($table_name, $allowed_tables)) {
        return ['success' => false, 'error' => 'Invalid table name.', 'new_status' => null];
    }

    // 1. Load the document
    // Legacy tables use 'username' as uploader, we'll try to get both if possible, or fallback
    // Since legacy tables don't have doc_type_id, we will assume current_step is enough for the transition.
    $stmt = $conn->prepare(
        "SELECT * FROM `$table_name` WHERE `$id_col` = ?"
    );
    $stmt->bind_param('i', $doc_id);
    $stmt->execute();
    $doc = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$doc) {
        return ['success' => false, 'error' => 'Document not found.', 'new_status' => null];
    }

    if ($doc['current_step'] === null) {
        return ['success' => false, 'error' => 'Document has no active workflow step.', 'new_status' => null];
    }

    // 2. Look up the transition
    $transition = wf_get_transition($conn, (int)$doc['current_step'], $action_key);
    if (!$transition) {
        return ['success' => false, 'error' => 'No valid transition for this action.', 'new_status' => null];
    }

    $new_step   = $transition['to_step_id'];  // may be NULL (terminal)
    $new_status = $transition['resulting_status'];

    // 3. Begin transaction
    $conn->begin_transaction();

    try {
        // 4. Update document status and current_step
        $rejection_reason = ($action_key === 'reject') ? $remarks : null;

        $upd = $conn->prepare(
            "UPDATE documents
             SET status = ?, current_step = ?, rejection_reason = ?, updated_at = NOW()
             WHERE doc_id = ?"
        );
        $upd->bind_param('sisi', $new_status, $new_step, $rejection_reason, $doc_id);
        $upd->execute();
        $upd->close();

        // 5. Record audit trail in document_actions
        $act_stmt = $conn->prepare(
            "INSERT INTO document_actions (doc_id, acted_by, action, step_id, remarks, acted_at)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $current_step_int = (int)$doc['current_step'];
        $act_stmt->bind_param('iisis', $doc_id, $actor_user_id, $action_key, $current_step_int, $remarks);
        $act_stmt->execute();
        $act_stmt->close();

        $conn->commit();

        return ['success' => true, 'error' => null, 'new_status' => $new_status];

    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'error' => 'Transaction failed: ' . $e->getMessage(), 'new_status' => null];
    }
}

// ============================================================
// DOCUMENT INITIALIZATION
// ============================================================

/**
 * Initializes a newly created document into its workflow.
 *
 * - Resolves the workflow from the document type
 * - If the workflow has steps: sets status='pending', current_step=first step
 * - If the workflow has zero steps (auto_accept): sets status='accepted', current_step=NULL
 *
 * @param mysqli $conn
 * @param int    $doc_id
 * @param int    $doc_type_id
 * @param int    $uploader_user_id
 * @return array ['success' => bool, 'status' => string, 'error' => string|null]
 */
function wf_initialize_document(mysqli $conn, int $doc_id, int $doc_type_id, int $uploader_user_id): array
{
    // 1. Resolve workflow
    $workflow = wf_get_workflow_for_type($conn, $doc_type_id);
    if (!$workflow) {
        return ['success' => false, 'status' => 'pending', 'error' => 'No active workflow found for this document type.'];
    }

    // 2. Get first step
    $first_step = wf_get_first_step($conn, (int)$workflow['workflow_id']);

    if ($first_step === null) {
        // Zero-step workflow: auto-accept
        $stmt = $conn->prepare("UPDATE documents SET status = 'accepted', current_step = NULL WHERE doc_id = ?");
        $stmt->bind_param('i', $doc_id);
        $stmt->execute();
        $stmt->close();

        // Record the upload action
        $act = $conn->prepare(
            "INSERT INTO document_actions (doc_id, acted_by, action, step_id, remarks, acted_at)
             VALUES (?, ?, 'uploaded', NULL, 'Auto-accepted (zero-step workflow)', NOW())"
        );
        $act->bind_param('ii', $doc_id, $uploader_user_id);
        $act->execute();
        $act->close();

        return ['success' => true, 'status' => 'accepted', 'error' => null];
    }

    // 3. Has steps: set to pending at first step
    $step_id = (int)$first_step['step_id'];
    $stmt = $conn->prepare("UPDATE documents SET status = 'pending', current_step = ? WHERE doc_id = ?");
    $stmt->bind_param('ii', $step_id, $doc_id);
    $stmt->execute();
    $stmt->close();

    // Record the upload action
    $act = $conn->prepare(
        "INSERT INTO document_actions (doc_id, acted_by, action, step_id, remarks, acted_at)
         VALUES (?, ?, 'uploaded', ?, NULL, NOW())"
    );
    $act->bind_param('iii', $doc_id, $uploader_user_id, $step_id);
    $act->execute();
    $act->close();

    return ['success' => true, 'status' => 'pending', 'error' => null];
}

// ============================================================
// STATUS DISPLAY HELPERS
// ============================================================

/**
 * Returns a human-readable status label for a document.
 *
 * @param mysqli $conn
 * @param array  $document  Must include: status, current_step
 * @return string E.g. "Pending - HOD Review", "Accepted", "Rejected"
 */
function wf_status_label(mysqli $conn, array $document): string
{
    $status = $document['status'];

    if ($status === 'accepted') {
        return 'Accepted';
    }

    if ($status === 'rejected') {
        return 'Rejected';
    }

    if ($status === 'pending' && $document['current_step'] !== null) {
        $step = wf_get_step($conn, (int)$document['current_step']);
        if ($step) {
            return 'Pending - ' . htmlspecialchars($step['step_label']);
        }
    }

    return ucfirst($status);
}

/**
 * Returns the audit trail (action history) for a document.
 *
 * @param mysqli $conn
 * @param int    $doc_id
 * @return array List of action records with actor and step info
 */
function wf_get_action_history(mysqli $conn, int $doc_id): array
{
    $stmt = $conn->prepare(
        "SELECT da.action_id, da.action, da.remarks, da.acted_at,
                u.full_name AS actor_name,
                ws.step_label
         FROM document_actions da
         JOIN users u ON u.user_id = da.acted_by
         LEFT JOIN workflow_steps ws ON ws.step_id = da.step_id
         WHERE da.doc_id = ?
         ORDER BY da.acted_at ASC"
    );
    $stmt->bind_param('i', $doc_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    $stmt->close();
    return $history;
}
