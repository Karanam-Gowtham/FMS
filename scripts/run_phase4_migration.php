<?php
/**
 * FMS Phase 4 Migration Runner
 *
 * 1. Creates a database backup
 * 2. Runs the workflow state machine migration (002_workflow_state_machine.sql)
 * 3. Verifies the results
 *
 * Usage: php scripts/run_phase4_migration.php
 */

require_once __DIR__ . '/../includes/connection.php';

echo "=== FMS Phase 4 Migration Runner ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// ============================================================
// STEP 1: Database Backup
// ============================================================
echo "--- STEP 1: Database Backup ---\n";
$backup_file = __DIR__ . '/../database/backup_pre_phase4_' . date('Ymd_His') . '.sql';
$fp = fopen($backup_file, 'w');
fwrite($fp, "-- FMS Database Backup (Pre-Phase 4)\n");
fwrite($fp, "-- Date: " . date('Y-m-d H:i:s') . "\n\n");
fwrite($fp, "SET FOREIGN_KEY_CHECKS=0;\n\n");

$res = $conn->query("SHOW TABLES");
$table_count = 0;
while ($r = $res->fetch_row()) {
    $table = $r[0];
    $create = $conn->query("SHOW CREATE TABLE `$table`")->fetch_row();
    fwrite($fp, "DROP TABLE IF EXISTS `$table`;\n");
    fwrite($fp, $create[1] . ";\n\n");

    $data = $conn->query("SELECT * FROM `$table`");
    while ($row = $data->fetch_row()) {
        $values = [];
        foreach ($row as $val) {
            $values[] = ($val === null) ? "NULL" : "'" . $conn->real_escape_string($val) . "'";
        }
        fwrite($fp, "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n");
    }
    fwrite($fp, "\n");
    $table_count++;
}
fwrite($fp, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($fp);
echo "Backup created: $backup_file ($table_count tables)\n\n";

// ============================================================
// STEP 2: Pre-migration checks
// ============================================================
echo "--- STEP 2: Pre-migration checks ---\n";

// Verify documents and document_actions are empty
$doc_count = (int)$conn->query("SELECT COUNT(*) as c FROM documents")->fetch_assoc()['c'];
$act_count = (int)$conn->query("SELECT COUNT(*) as c FROM document_actions")->fetch_assoc()['c'];
echo "documents rows: $doc_count\n";
echo "document_actions rows: $act_count\n";

if ($doc_count > 0 || $act_count > 0) {
    die("ERROR: documents or document_actions are not empty. Migration aborted.\n");
}
echo "Pre-checks passed.\n\n";

// ============================================================
// STEP 3: Execute migration
// ============================================================
echo "--- STEP 3: Execute migration ---\n";

$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// 3a: Drop FK on documents.current_step
$fk_result = $conn->query(
    "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'documents'
       AND COLUMN_NAME = 'current_step'
       AND REFERENCED_TABLE_NAME = 'workflow_steps'
     LIMIT 1"
);
$fk_row = $fk_result->fetch_assoc();
if ($fk_row) {
    $conn->query("ALTER TABLE documents DROP FOREIGN KEY `{$fk_row['CONSTRAINT_NAME']}`");
    echo "Dropped FK {$fk_row['CONSTRAINT_NAME']} on documents.current_step\n";
}

// 3b: Drop FK on document_actions.step_id
$fk_result2 = $conn->query(
    "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'document_actions'
       AND COLUMN_NAME = 'step_id'
       AND REFERENCED_TABLE_NAME = 'workflow_steps'
     LIMIT 1"
);
$fk_row2 = $fk_result2->fetch_assoc();
if ($fk_row2) {
    $conn->query("ALTER TABLE document_actions DROP FOREIGN KEY `{$fk_row2['CONSTRAINT_NAME']}`");
    echo "Dropped FK {$fk_row2['CONSTRAINT_NAME']} on document_actions.step_id\n";
}

// 3c: Drop old workflow_steps
$conn->query("DROP TABLE IF EXISTS workflow_steps");
echo "Dropped old workflow_steps table\n";

// 3d: Create workflows table
$conn->query("
    CREATE TABLE workflows (
        workflow_id   INT AUTO_INCREMENT PRIMARY KEY,
        workflow_key  VARCHAR(50) NOT NULL UNIQUE,
        label         VARCHAR(200) NOT NULL,
        description   TEXT DEFAULT NULL,
        is_active     TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "Created workflows table\n";

// 3e: Create new workflow_steps
$conn->query("
    CREATE TABLE workflow_steps (
        step_id             INT AUTO_INCREMENT PRIMARY KEY,
        workflow_id         INT NOT NULL,
        step_order          INT NOT NULL,
        step_label          VARCHAR(100) NOT NULL,
        responsible_role_id INT NOT NULL,
        scope               ENUM('department','global') NOT NULL DEFAULT 'department',
        FOREIGN KEY (workflow_id) REFERENCES workflows(workflow_id),
        FOREIGN KEY (responsible_role_id) REFERENCES roles(role_id),
        UNIQUE KEY uq_workflow_step (workflow_id, step_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "Created workflow_steps table\n";

// 3f: Create workflow_actions
$conn->query("
    CREATE TABLE workflow_actions (
        action_id   INT AUTO_INCREMENT PRIMARY KEY,
        action_key  VARCHAR(50) NOT NULL UNIQUE,
        label       VARCHAR(100) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "Created workflow_actions table\n";

// 3g: Create workflow_transitions
$conn->query("
    CREATE TABLE workflow_transitions (
        transition_id    INT AUTO_INCREMENT PRIMARY KEY,
        step_id          INT NOT NULL,
        action_id        INT NOT NULL,
        to_step_id       INT DEFAULT NULL,
        resulting_status VARCHAR(50) NOT NULL,
        FOREIGN KEY (step_id) REFERENCES workflow_steps(step_id),
        FOREIGN KEY (action_id) REFERENCES workflow_actions(action_id),
        FOREIGN KEY (to_step_id) REFERENCES workflow_steps(step_id),
        UNIQUE KEY uq_step_action (step_id, action_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "Created workflow_transitions table\n";

// 3h: Seed workflow_actions
$conn->query("INSERT INTO workflow_actions (action_key, label) VALUES ('approve', 'Approve'), ('reject', 'Reject'), ('resubmit', 'Resubmit')");
echo "Seeded workflow_actions (3 rows)\n";

// 3i: Seed department_standard workflow
$conn->query("INSERT INTO workflows (workflow_key, label, description) VALUES ('department_standard', 'Standard Department Review', 'Faculty uploads → HOD reviews (department scope) → R&D Dean reviews (global scope) → Accepted')");
$wf_dept = $conn->insert_id;

$conn->query("INSERT INTO workflow_steps (workflow_id, step_order, step_label, responsible_role_id, scope) VALUES ($wf_dept, 1, 'HOD Review', 3, 'department')");
$step_hod = $conn->insert_id;
$conn->query("INSERT INTO workflow_steps (workflow_id, step_order, step_label, responsible_role_id, scope) VALUES ($wf_dept, 2, 'R&D Dean Review', 8, 'global')");
$step_dean = $conn->insert_id;

// Transitions for department_standard
$conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step_hod, 1, $step_dean, 'pending')");
$conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step_hod, 2, NULL, 'rejected')");
$conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step_hod, 3, $step_hod, 'pending')");
$conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step_dean, 1, NULL, 'accepted')");
$conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step_dean, 2, NULL, 'rejected')");
$conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step_dean, 3, $step_hod, 'pending')");
echo "Seeded department_standard workflow (2 steps, 6 transitions)\n";

// 3j: Seed central workflow
$conn->query("INSERT INTO workflows (workflow_key, label, description) VALUES ('central', 'Central Event Review', 'Central Coordinator uploads → R&D Dean reviews (global scope) → Accepted')");
$wf_central = $conn->insert_id;

$conn->query("INSERT INTO workflow_steps (workflow_id, step_order, step_label, responsible_role_id, scope) VALUES ($wf_central, 1, 'R&D Dean Review', 8, 'global')");
$step_central_dean = $conn->insert_id;

$conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step_central_dean, 1, NULL, 'accepted')");
$conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step_central_dean, 2, NULL, 'rejected')");
$conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step_central_dean, 3, $step_central_dean, 'pending')");
echo "Seeded central workflow (1 step, 3 transitions)\n";

// 3k: Seed auto_accept workflow
$conn->query("INSERT INTO workflows (workflow_key, label, description) VALUES ('auto_accept', 'No Approval Required', 'Documents are accepted immediately upon upload.')");
echo "Seeded auto_accept workflow (0 steps)\n";

// 3l: Update document_types.workflow_key
$conn->query("UPDATE document_types SET workflow_key = 'department_standard' WHERE workflow_key = 'department'");
$updated = $conn->affected_rows;
echo "Updated document_types.workflow_key: $updated rows changed from 'department' to 'department_standard'\n";

// 3m: Re-add FKs
$conn->query("ALTER TABLE documents ADD CONSTRAINT fk_documents_current_step FOREIGN KEY (current_step) REFERENCES workflow_steps(step_id)");
echo "Re-added FK on documents.current_step\n";

$conn->query("ALTER TABLE document_actions ADD CONSTRAINT fk_doc_actions_step FOREIGN KEY (step_id) REFERENCES workflow_steps(step_id)");
echo "Re-added FK on document_actions.step_id\n";

$conn->query("SET FOREIGN_KEY_CHECKS = 1");
echo "\n";

// ============================================================
// STEP 4: Verification
// ============================================================
echo "--- STEP 4: Verification ---\n";

$checks = [
    ['workflows', 3],
    ['workflow_steps', 3],
    ['workflow_actions', 3],
    ['workflow_transitions', 9],
];

$all_pass = true;
foreach ($checks as [$table, $expected]) {
    $actual = (int)$conn->query("SELECT COUNT(*) as c FROM `$table`")->fetch_assoc()['c'];
    $status = ($actual === $expected) ? 'PASS' : 'FAIL';
    if ($status === 'FAIL') $all_pass = false;
    echo "$table: $actual rows (expected $expected) — $status\n";
}

// Check document_types workflow_key values
$wf_keys = $conn->query("SELECT workflow_key, COUNT(*) as cnt FROM document_types GROUP BY workflow_key ORDER BY workflow_key");
echo "\ndocument_types workflow_key distribution:\n";
while ($r = $wf_keys->fetch_assoc()) {
    echo "  {$r['workflow_key']}: {$r['cnt']} types\n";
}

// Verify workflow step details
echo "\nWorkflow step details:\n";
$steps = $conn->query("SELECT w.workflow_key, ws.step_order, ws.step_label, r.role_name, ws.scope FROM workflow_steps ws JOIN workflows w ON w.workflow_id = ws.workflow_id JOIN roles r ON r.role_id = ws.responsible_role_id ORDER BY w.workflow_key, ws.step_order");
while ($r = $steps->fetch_assoc()) {
    echo "  {$r['workflow_key']} step {$r['step_order']}: {$r['step_label']} ({$r['role_name']}, {$r['scope']})\n";
}

echo "\n" . ($all_pass ? "✓ ALL CHECKS PASSED" : "✗ SOME CHECKS FAILED") . "\n";
echo "Phase 4 migration " . ($all_pass ? "COMPLETE" : "NEEDS REVIEW") . ".\n";
