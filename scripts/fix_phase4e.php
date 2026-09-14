<?php
/**
 * Phase 4E Fixes — Database Corrections
 *
 * 1. Backup before changes
 * 2. Fix reject transitions: set to_step_id to first step (enables resubmit)
 * 3. Fix document_actions.action ENUM → VARCHAR
 * 4. Assign RnD_Dean role to admin user
 *
 * READ-THEN-WRITE. All changes are verified.
 */
require_once __DIR__ . '/../includes/connection.php';

echo "=== Phase 4E Fixes ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// ============================================================
// STEP 0: BACKUP
// ============================================================
echo "--- STEP 0: Backup ---\n";
$backup_file = __DIR__ . '/../database/backup_pre_4e_fixes_' . date('Ymd_His') . '.sql';
$fp = fopen($backup_file, 'w');
fwrite($fp, "-- FMS Backup (Pre-4E Fixes)\n-- Date: " . date('Y-m-d H:i:s') . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n");

$tables_to_backup = ['workflow_transitions', 'document_actions', 'documents', 'user_roles'];
foreach ($tables_to_backup as $table) {
    $create = $conn->query("SHOW CREATE TABLE `$table`")->fetch_row();
    fwrite($fp, "DROP TABLE IF EXISTS `$table`;\n{$create[1]};\n\n");
    $data = $conn->query("SELECT * FROM `$table`");
    while ($row = $data->fetch_row()) {
        $vals = [];
        foreach ($row as $v) $vals[] = ($v === null) ? "NULL" : "'" . $conn->real_escape_string($v) . "'";
        fwrite($fp, "INSERT INTO `$table` VALUES (" . implode(', ', $vals) . ");\n");
    }
    fwrite($fp, "\n");
}
fwrite($fp, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($fp);
echo "Backup: $backup_file\n\n";

// ============================================================
// FIX 1: Reject transitions — set to_step_id to first step
// ============================================================
echo "--- FIX 1: Reject transitions ---\n";

// For each workflow, find the first step
$workflows = $conn->query("SELECT workflow_id, workflow_key FROM workflows WHERE is_active = 1");
while ($wf = $workflows->fetch_assoc()) {
    $wf_id = (int)$wf['workflow_id'];
    $wf_key = $wf['workflow_key'];

    // Get first step of this workflow
    $first_step_q = $conn->prepare(
        "SELECT step_id FROM workflow_steps WHERE workflow_id = ? ORDER BY step_order ASC LIMIT 1"
    );
    $first_step_q->bind_param('i', $wf_id);
    $first_step_q->execute();
    $first_row = $first_step_q->get_result()->fetch_assoc();
    $first_step_q->close();

    if (!$first_row) {
        echo "  $wf_key: no steps (auto_accept), skipping\n";
        continue;
    }

    $first_step_id = (int)$first_row['step_id'];

    // Get reject action_id
    $reject_action = $conn->query("SELECT action_id FROM workflow_actions WHERE action_key = 'reject'")->fetch_assoc();
    $reject_action_id = (int)$reject_action['action_id'];

    // Find all reject transitions for this workflow where to_step_id IS NULL
    $find = $conn->prepare(
        "SELECT wt.transition_id, wt.step_id, ws.step_label
         FROM workflow_transitions wt
         JOIN workflow_steps ws ON ws.step_id = wt.step_id
         WHERE ws.workflow_id = ? AND wt.action_id = ? AND wt.to_step_id IS NULL"
    );
    $find->bind_param('ii', $wf_id, $reject_action_id);
    $find->execute();
    $rejects = $find->get_result();

    while ($rt = $rejects->fetch_assoc()) {
        $tid = (int)$rt['transition_id'];
        $upd = $conn->prepare("UPDATE workflow_transitions SET to_step_id = ? WHERE transition_id = ?");
        $upd->bind_param('ii', $first_step_id, $tid);
        $upd->execute();
        $upd->close();
        echo "  $wf_key: transition $tid ({$rt['step_label']} → reject) now points to step $first_step_id\n";
    }
    $find->close();
}

// Verify
echo "\n  Verification — reject transitions:\n";
$verify = $conn->query(
    "SELECT w.workflow_key, ws_from.step_label AS from_step, wt.to_step_id,
            COALESCE(ws_to.step_label, 'NULL') AS to_step, wt.resulting_status
     FROM workflow_transitions wt
     JOIN workflow_steps ws_from ON ws_from.step_id = wt.step_id
     JOIN workflows w ON w.workflow_id = ws_from.workflow_id
     JOIN workflow_actions wa ON wa.action_id = wt.action_id
     LEFT JOIN workflow_steps ws_to ON ws_to.step_id = wt.to_step_id
     WHERE wa.action_key = 'reject'
     ORDER BY w.workflow_key, ws_from.step_order"
);
while ($r = $verify->fetch_assoc()) {
    $ok = ($r['to_step_id'] !== null) ? 'OK' : 'STILL NULL';
    echo "    {$r['workflow_key']}: {$r['from_step']} → reject → {$r['to_step']} (status={$r['resulting_status']}) [$ok]\n";
}

// ============================================================
// FIX 2: document_actions.action ENUM → VARCHAR
// ============================================================
echo "\n--- FIX 2: document_actions.action column ---\n";

// Check current type
$col_info = $conn->query(
    "SELECT COLUMN_TYPE FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'document_actions' AND COLUMN_NAME = 'action'"
)->fetch_assoc();
echo "  Before: {$col_info['COLUMN_TYPE']}\n";

$conn->query("ALTER TABLE document_actions MODIFY COLUMN action VARCHAR(50) NOT NULL");
echo "  Changed to: VARCHAR(50) NOT NULL\n";

// Verify
$col_info2 = $conn->query(
    "SELECT COLUMN_TYPE FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'document_actions' AND COLUMN_NAME = 'action'"
)->fetch_assoc();
echo "  After: {$col_info2['COLUMN_TYPE']}\n";

// ============================================================
// FIX 3: Assign RnD_Dean role
// ============================================================
echo "\n--- FIX 3: RnD_Dean role assignment ---\n";

// Check if any user already has RnD_Dean (role_id=8)
$existing = $conn->query("SELECT ur.user_role_id, u.email FROM user_roles ur JOIN users u ON u.user_id = ur.user_id WHERE ur.role_id = 8");
if ($existing->num_rows > 0) {
    echo "  RnD_Dean already assigned to:\n";
    while ($r = $existing->fetch_assoc()) echo "    {$r['email']}\n";
} else {
    // Assign to admin@gmrit.edu (user_id lookup)
    $admin = $conn->query("SELECT user_id, email FROM users WHERE email = 'admin@gmrit.edu' LIMIT 1")->fetch_assoc();
    if ($admin) {
        // Use dept_id=10 (NAAC) for global role — RnD Dean is global scope
        $stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, 8, 10)");
        $stmt->bind_param('i', $admin['user_id']);
        $stmt->execute();
        echo "  Assigned RnD_Dean role to {$admin['email']} (user_id={$admin['user_id']}, dept_id=10)\n";
        $stmt->close();
    } else {
        echo "  ERROR: admin@gmrit.edu not found\n";
    }
}

// Verify
echo "\n  Verification — RnD_Dean assignments:\n";
$check = $conn->query("SELECT ur.user_role_id, u.full_name, u.email, ur.dept_id FROM user_roles ur JOIN users u ON u.user_id = ur.user_id WHERE ur.role_id = 8");
while ($r = $check->fetch_assoc()) {
    echo "    {$r['email']} (dept_id={$r['dept_id']})\n";
}

// ============================================================
// FIX 4: Fix migrated rejected doc — ensure current_step is set correctly
// ============================================================
echo "\n--- FIX 4: Verify rejected docs have valid current_step ---\n";
$rejected = $conn->query("SELECT doc_id, title, current_step, status FROM documents WHERE status = 'rejected'");
while ($r = $rejected->fetch_assoc()) {
    if ($r['current_step'] === null) {
        echo "  doc_id={$r['doc_id']}: current_step is NULL — needs fix\n";
    } else {
        echo "  doc_id={$r['doc_id']}: current_step={$r['current_step']} — OK\n";
    }
}

echo "\n=== All database fixes applied ===\n";
