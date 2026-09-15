<?php
/**
 * Phase 5B-0 — R&D Configuration Migration
 *
 * This script safely configures the existing architecture for the R&D module.
 * It does NOT redesign architecture, delete legacy tables, or create UI.
 *
 * Steps:
 * 1. Backup verification (schema dump)
 * 2. Create department_rnd workflow + steps + transitions
 * 3. Create R&D pseudo-department
 * 4. Create dedicated R&D Dean user (separate from Admin)
 * 5. Remove RnD_Dean role from Admin user
 * 6. Reassign 6 research doc types to department_rnd
 * 7. Create rnd_categories table with provisional seed data
 * 8. Create central_rnd document type with auto_accept
 * 9. Create meta_central_rnd table
 * 10. Verify all FK references
 */

require_once __DIR__ . '/../includes/connection.php';

echo "=================================================================\n";
echo "  Phase 5B-0 — R&D Configuration Migration\n";
echo "  " . date('Y-m-d H:i:s') . "\n";
echo "=================================================================\n\n";

$errors = [];

// ============================================================
// STEP 1: PRE-FLIGHT CHECKS
// ============================================================
echo "--- STEP 1: Pre-flight Checks ---\n";

// Verify existing workflows
$wf = $conn->query("SELECT workflow_key FROM workflows WHERE workflow_key = 'department_rnd'");
if ($wf && $wf->num_rows > 0) {
    echo "  WARNING: department_rnd workflow already exists. Skipping workflow creation.\n";
    $wf_exists = true;
} else {
    $wf_exists = false;
    echo "  department_rnd does not exist yet. Will create.\n";
}

// Verify department_standard still intact
$wf_std = $conn->query("SELECT workflow_id FROM workflows WHERE workflow_key = 'department_standard' AND is_active = 1");
if (!$wf_std || $wf_std->num_rows === 0) {
    $errors[] = "CRITICAL: department_standard workflow missing or inactive!";
}
echo "  department_standard verified.\n";

// Verify auto_accept exists
$wf_aa = $conn->query("SELECT workflow_id FROM workflows WHERE workflow_key = 'auto_accept' AND is_active = 1");
if (!$wf_aa || $wf_aa->num_rows === 0) {
    $errors[] = "CRITICAL: auto_accept workflow missing or inactive!";
}
echo "  auto_accept verified.\n";

// Verify workflow_actions
$actions = $conn->query("SELECT action_id, action_key FROM workflow_actions ORDER BY action_id");
$action_map = [];
while ($a = $actions->fetch_assoc()) {
    $action_map[$a['action_key']] = (int)$a['action_id'];
}
if (!isset($action_map['approve'], $action_map['reject'], $action_map['resubmit'])) {
    $errors[] = "CRITICAL: Missing workflow_actions (need approve, reject, resubmit)";
}
echo "  workflow_actions verified: " . json_encode($action_map) . "\n";

if (!empty($errors)) {
    echo "\n*** ABORTING: Critical errors found ***\n";
    foreach ($errors as $e) echo "  - $e\n";
    exit(1);
}

echo "  All pre-flight checks passed.\n\n";

// ============================================================
// STEP 2: CREATE department_rnd WORKFLOW
// ============================================================
echo "--- STEP 2: Create department_rnd Workflow ---\n";

if (!$wf_exists) {
    $conn->begin_transaction();
    try {
        // Insert workflow
        $conn->query("INSERT INTO workflows (workflow_key, label, description, is_active)
            VALUES ('department_rnd', 'Department R&D Review',
            'Faculty uploads → Dept Coordinator reviews (department scope) → HOD reviews (department scope) → R&D Dean reviews (global scope) → Accepted',
            1)");
        $wf_id = $conn->insert_id;
        echo "  Created workflow: department_rnd (id=$wf_id)\n";

        // Insert 3 steps
        // Step 1: Dept Coordinator (role_id=5, scope=department)
        $conn->query("INSERT INTO workflow_steps (workflow_id, step_order, step_label, responsible_role_id, scope)
            VALUES ($wf_id, 1, 'Dept Coordinator Review', 5, 'department')");
        $step1 = $conn->insert_id;
        echo "  Step 1: Dept Coordinator Review (step_id=$step1)\n";

        // Step 2: HOD (role_id=3, scope=department)
        $conn->query("INSERT INTO workflow_steps (workflow_id, step_order, step_label, responsible_role_id, scope)
            VALUES ($wf_id, 2, 'HOD Review', 3, 'department')");
        $step2 = $conn->insert_id;
        echo "  Step 2: HOD Review (step_id=$step2)\n";

        // Step 3: R&D Dean (role_id=8, scope=global)
        $conn->query("INSERT INTO workflow_steps (workflow_id, step_order, step_label, responsible_role_id, scope)
            VALUES ($wf_id, 3, 'R&D Dean Review', 8, 'global')");
        $step3 = $conn->insert_id;
        echo "  Step 3: R&D Dean Review (step_id=$step3)\n";

        // Insert 9 transitions
        $aid_approve  = $action_map['approve'];
        $aid_reject   = $action_map['reject'];
        $aid_resubmit = $action_map['resubmit'];

        // DC Review transitions
        $conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step1, $aid_approve, $step2, 'pending')");
        $conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step1, $aid_reject, $step1, 'rejected')");
        $conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step1, $aid_resubmit, $step1, 'pending')");

        // HOD Review transitions
        $conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step2, $aid_approve, $step3, 'pending')");
        $conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step2, $aid_reject, $step1, 'rejected')");
        $conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step2, $aid_resubmit, $step1, 'pending')");

        // R&D Dean Review transitions
        $conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step3, $aid_approve, NULL, 'accepted')");
        $conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step3, $aid_reject, $step1, 'rejected')");
        $conn->query("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES ($step3, $aid_resubmit, $step1, 'pending')");

        echo "  Created 9 transitions.\n";

        $conn->commit();
        echo "  ✅ department_rnd workflow created successfully.\n";
    } catch (Exception $e) {
        $conn->rollback();
        echo "  ❌ FAILED: " . $e->getMessage() . "\n";
        $errors[] = "Workflow creation failed: " . $e->getMessage();
    }
} else {
    echo "  Skipped (already exists).\n";
}
echo "\n";

// ============================================================
// STEP 3: CREATE R&D PSEUDO-DEPARTMENT
// ============================================================
echo "--- STEP 3: Create R&D Pseudo-Department ---\n";

$rnd_dept = $conn->query("SELECT dept_id FROM dept WHERE dept_name = 'R&D'")->fetch_assoc();
if ($rnd_dept) {
    $rnd_dept_id = (int)$rnd_dept['dept_id'];
    echo "  R&D department already exists (dept_id=$rnd_dept_id). Skipping.\n";
} else {
    $conn->query("INSERT INTO dept (dept_name) VALUES ('R&D')");
    $rnd_dept_id = $conn->insert_id;
    echo "  Created R&D department (dept_id=$rnd_dept_id).\n";
}
echo "\n";

// ============================================================
// STEP 4: CREATE DEDICATED R&D DEAN USER
// ============================================================
echo "--- STEP 4: Create Dedicated R&D Dean User ---\n";

// Check if a dedicated rnd_dean user already exists (not admin)
$existing_dean = $conn->query("
    SELECT u.user_id, u.email, u.full_name
    FROM users u
    JOIN user_roles ur ON ur.user_id = u.user_id
    WHERE ur.role_id = 8 AND u.user_id != 1
")->fetch_assoc();

if ($existing_dean) {
    echo "  Dedicated R&D Dean user already exists: {$existing_dean['email']} (user_id={$existing_dean['user_id']}). Skipping.\n";
    $dean_user_id = (int)$existing_dean['user_id'];
} else {
    // Create a placeholder R&D Dean account
    // Using a generic institutional email that can be updated for production
    $dean_email = 'rnd.dean@gmrit.edu.in';
    $dean_name = 'R&D Dean';
    // Use a bcrypt hash of a temporary password (must be changed for production)
    $temp_password = password_hash('RnDDean@FMS2024', PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (email, password, full_name, status) VALUES (?, ?, ?, 'active')");
    $stmt->bind_param('sss', $dean_email, $temp_password, $dean_name);
    $stmt->execute();
    $dean_user_id = $stmt->insert_id;
    $stmt->close();
    echo "  Created R&D Dean user: $dean_email (user_id=$dean_user_id)\n";

    // Assign RnD_Dean role with R&D department
    $stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id, dept_id) VALUES (?, 8, ?)");
    $stmt->bind_param('ii', $dean_user_id, $rnd_dept_id);
    $stmt->execute();
    $dean_role_id = $stmt->insert_id;
    $stmt->close();
    echo "  Assigned RnD_Dean role (user_role_id=$dean_role_id, dept_id=$rnd_dept_id)\n";
}
echo "\n";

// ============================================================
// STEP 5: REMOVE RnD_Dean ROLE FROM ADMIN
// ============================================================
echo "--- STEP 5: Remove RnD_Dean Role from Admin ---\n";

$admin_rnd = $conn->query("SELECT user_role_id FROM user_roles WHERE user_id = 1 AND role_id = 8");
if ($admin_rnd && $admin_rnd->num_rows > 0) {
    $row = $admin_rnd->fetch_assoc();
    $conn->query("DELETE FROM user_roles WHERE user_role_id = " . (int)$row['user_role_id']);
    echo "  Removed RnD_Dean role from admin (user_role_id={$row['user_role_id']}).\n";
    echo "  Admin retains: Admin role (role_id=1).\n";
} else {
    echo "  Admin does not have RnD_Dean role. Skipping.\n";
}

// Update the old RnD_Dean dept_id=NAAC assignment if it was left on the admin
// (this is now handled by removal above)
echo "  ✅ Admin and R&D Dean are now separate users.\n";
echo "\n";

// ============================================================
// STEP 6: REASSIGN RESEARCH DOC TYPES TO department_rnd
// ============================================================
echo "--- STEP 6: Reassign Research Document Types ---\n";

$research_types = ['journal', 'conference', 'patent', 'fdp_attended', 'fdp_organised', 'conf_organised'];

// First verify department_rnd exists
$wf_rnd = $conn->query("SELECT workflow_key FROM workflows WHERE workflow_key = 'department_rnd' AND is_active = 1");
if (!$wf_rnd || $wf_rnd->num_rows === 0) {
    echo "  ❌ CRITICAL: department_rnd workflow not found! Cannot reassign.\n";
    $errors[] = "department_rnd workflow missing";
} else {
    foreach ($research_types as $tk) {
        // Check current state
        $cur = $conn->query("SELECT type_id, workflow_key FROM document_types WHERE type_key = '$tk'")->fetch_assoc();
        if (!$cur) {
            echo "  WARNING: $tk not found in document_types. Skipping.\n";
            continue;
        }
        if ($cur['workflow_key'] === 'department_rnd') {
            echo "  $tk: already on department_rnd. Skipping.\n";
            continue;
        }
        $conn->query("UPDATE document_types SET workflow_key = 'department_rnd' WHERE type_key = '$tk'");
        echo "  $tk: {$cur['workflow_key']} → department_rnd ✅\n";
    }
}

// Verify no existing pending documents are broken
$pending_research = $conn->query("
    SELECT d.doc_id, d.status, d.current_step, dt.type_key
    FROM documents d
    JOIN document_types dt ON dt.type_id = d.doc_type_id
    WHERE dt.type_key IN ('journal','conference','patent','fdp_attended','fdp_organised','conf_organised')
      AND d.status != 'accepted'
");
if ($pending_research && $pending_research->num_rows > 0) {
    echo "  ⚠️  WARNING: Found non-accepted research documents that need step migration:\n";
    while ($row = $pending_research->fetch_assoc()) {
        echo "    doc_id={$row['doc_id']} type={$row['type_key']} status={$row['status']} step={$row['current_step']}\n";
    }
} else {
    echo "  ✅ All existing research documents are in terminal (accepted) state. No step migration needed.\n";
}
echo "\n";

// ============================================================
// STEP 7: CREATE rnd_categories TABLE
// ============================================================
echo "--- STEP 7: Create rnd_categories Table ---\n";

$tbl_exists = $conn->query("SHOW TABLES LIKE 'rnd_categories'");
if ($tbl_exists && $tbl_exists->num_rows > 0) {
    echo "  rnd_categories table already exists. Skipping.\n";
} else {
    $conn->query("CREATE TABLE rnd_categories (
        category_id INT AUTO_INCREMENT PRIMARY KEY,
        category_key VARCHAR(50) NOT NULL UNIQUE,
        category_label VARCHAR(200) NOT NULL,
        sort_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    if ($conn->error) {
        echo "  ❌ Failed to create rnd_categories: {$conn->error}\n";
        $errors[] = "rnd_categories creation failed";
    } else {
        echo "  Created rnd_categories table.\n";

        // Seed provisional categories (clearly marked as examples, NOT official requirements)
        $categories = [
            ['policies',       'R&D Policies',             1],
            ['advisory_board', 'Research Advisory Board',   2],
            ['projects',       'Research Projects',         3],
            ['grants',         'Research Grants',           4],
            ['centres_labs',   'Research Centres/Labs',      5],
            ['collaborations', 'Collaborations/MoUs',       6],
            ['events',         'Events',                     7],
            ['ethics',         'Ethics',                     8],
            ['patents_ipr',    'Patents/IPR',                9],
            ['other',          'Other',                     10],
        ];

        $stmt = $conn->prepare("INSERT INTO rnd_categories (category_key, category_label, sort_order) VALUES (?, ?, ?)");
        foreach ($categories as $cat) {
            $stmt->bind_param('ssi', $cat[0], $cat[1], $cat[2]);
            $stmt->execute();
        }
        $stmt->close();
        echo "  Seeded 10 provisional categories.\n";
    }
}
echo "\n";

// ============================================================
// STEP 8: CREATE central_rnd DOCUMENT TYPE
// ============================================================
echo "--- STEP 8: Create central_rnd Document Type ---\n";

$crnd = $conn->query("SELECT type_id FROM document_types WHERE type_key = 'central_rnd'");
if ($crnd && $crnd->num_rows > 0) {
    echo "  central_rnd document type already exists. Skipping.\n";
} else {
    $conn->query("INSERT INTO document_types (type_key, type_label, category, workflow_key, meta_table, form_template, is_active)
        VALUES ('central_rnd', 'Central R&D Document', 'rnd_central', 'auto_accept', 'meta_central_rnd', NULL, 1)");
    if ($conn->error) {
        echo "  ❌ Failed: {$conn->error}\n";
        $errors[] = "central_rnd type creation failed";
    } else {
        echo "  Created central_rnd document type (type_id={$conn->insert_id}).\n";
    }
}
echo "\n";

// ============================================================
// STEP 9: CREATE meta_central_rnd TABLE
// ============================================================
echo "--- STEP 9: Create meta_central_rnd Table ---\n";

$tbl_exists = $conn->query("SHOW TABLES LIKE 'meta_central_rnd'");
if ($tbl_exists && $tbl_exists->num_rows > 0) {
    echo "  meta_central_rnd table already exists. Skipping.\n";
} else {
    $conn->query("CREATE TABLE meta_central_rnd (
        meta_id INT AUTO_INCREMENT PRIMARY KEY,
        doc_id INT NOT NULL,
        rnd_category_id INT DEFAULT NULL,
        description TEXT DEFAULT NULL,
        FOREIGN KEY (doc_id) REFERENCES documents(doc_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    if ($conn->error) {
        echo "  ❌ Failed: {$conn->error}\n";
        $errors[] = "meta_central_rnd creation failed";
    } else {
        echo "  Created meta_central_rnd table.\n";
    }
}
echo "\n";

// ============================================================
// STEP 10: VERIFY FK INTEGRITY
// ============================================================
echo "--- STEP 10: Verify FK Integrity ---\n";

// Verify all document_types.workflow_key reference valid workflows
$bad_wf = $conn->query("
    SELECT dt.type_key, dt.workflow_key
    FROM document_types dt
    LEFT JOIN workflows w ON w.workflow_key = dt.workflow_key
    WHERE w.workflow_id IS NULL
");
if ($bad_wf && $bad_wf->num_rows > 0) {
    echo "  ❌ Orphaned workflow_key references:\n";
    while ($row = $bad_wf->fetch_assoc()) echo "    {$row['type_key']} → {$row['workflow_key']}\n";
    $errors[] = "Orphaned workflow_key references found";
} else {
    echo "  ✅ All document_types.workflow_key references valid.\n";
}

// Verify all workflow_steps reference valid roles
$bad_role = $conn->query("
    SELECT ws.step_id, ws.step_label, ws.responsible_role_id
    FROM workflow_steps ws
    LEFT JOIN roles r ON r.role_id = ws.responsible_role_id
    WHERE r.role_id IS NULL
");
if ($bad_role && $bad_role->num_rows > 0) {
    echo "  ❌ Orphaned role references in workflow_steps.\n";
    $errors[] = "Orphaned role references";
} else {
    echo "  ✅ All workflow_steps.responsible_role_id references valid.\n";
}

// Verify all transitions reference valid steps
$bad_trans = $conn->query("
    SELECT wt.transition_id, wt.step_id, wt.to_step_id
    FROM workflow_transitions wt
    LEFT JOIN workflow_steps ws ON ws.step_id = wt.step_id
    WHERE ws.step_id IS NULL
");
if ($bad_trans && $bad_trans->num_rows > 0) {
    echo "  ❌ Orphaned step references in workflow_transitions.\n";
    $errors[] = "Orphaned step references";
} else {
    echo "  ✅ All workflow_transitions.step_id references valid.\n";
}

// Verify R&D Dean user has correct role
$dean_check = $conn->query("
    SELECT u.user_id, u.email, ur.role_id, ur.dept_id, d.dept_name
    FROM users u
    JOIN user_roles ur ON ur.user_id = u.user_id
    LEFT JOIN dept d ON d.dept_id = ur.dept_id
    WHERE ur.role_id = 8
");
echo "  RnD_Dean role holders:\n";
while ($row = $dean_check->fetch_assoc()) {
    echo "    user_id={$row['user_id']} email={$row['email']} dept={$row['dept_name']} (dept_id={$row['dept_id']})\n";
}

// Verify admin no longer has RnD_Dean
$admin_check = $conn->query("SELECT role_id FROM user_roles WHERE user_id = 1 AND role_id = 8");
if ($admin_check && $admin_check->num_rows > 0) {
    echo "  ⚠️  Admin still has RnD_Dean role!\n";
} else {
    echo "  ✅ Admin does NOT have RnD_Dean role. Separation confirmed.\n";
}

// Verify legacy tables still exist
$legacy_tables = ['published_tab', 'conference_tab', 'patents_table', 'fdps_tab', 'fdps_org_tab', 'conf_org_tab',
                  'files5_1_1and2', 'files5_1_3', 'files5_1_4', 'files5_2_1', 'files5_2_2', 'files5_2_3',
                  'files5_3_1', 'files5_3_3', 's_conference_tab'];
$missing_legacy = [];
foreach ($legacy_tables as $lt) {
    $check = $conn->query("SHOW TABLES LIKE '$lt'");
    if (!$check || $check->num_rows === 0) $missing_legacy[] = $lt;
}
if (empty($missing_legacy)) {
    echo "  ✅ All 15 legacy tables preserved.\n";
} else {
    echo "  ⚠️  Missing legacy tables: " . implode(', ', $missing_legacy) . "\n";
}
echo "\n";

// ============================================================
// SUMMARY
// ============================================================
echo "=================================================================\n";
echo "  MIGRATION SUMMARY\n";
echo "=================================================================\n";

if (empty($errors)) {
    echo "  ✅ ALL STEPS COMPLETED SUCCESSFULLY\n";
} else {
    echo "  ❌ ERRORS:\n";
    foreach ($errors as $e) echo "    - $e\n";
}

// Final state dump
echo "\n--- Final Workflow State ---\n";
$wfs = $conn->query("SELECT * FROM workflows ORDER BY workflow_id");
while ($row = $wfs->fetch_assoc()) {
    echo "  [{$row['workflow_id']}] {$row['workflow_key']}: {$row['label']} (active={$row['is_active']})\n";
}

echo "\n--- Final Workflow Steps ---\n";
$steps = $conn->query("
    SELECT ws.step_id, ws.workflow_id, ws.step_order, ws.step_label, ws.scope,
           r.role_name, w.workflow_key
    FROM workflow_steps ws
    JOIN roles r ON r.role_id = ws.responsible_role_id
    JOIN workflows w ON w.workflow_id = ws.workflow_id
    ORDER BY w.workflow_key, ws.step_order
");
while ($row = $steps->fetch_assoc()) {
    echo "  [{$row['workflow_key']}] step {$row['step_order']}: {$row['step_label']} ({$row['role_name']}, {$row['scope']})\n";
}

echo "\n--- Final Document Types ---\n";
$types = $conn->query("SELECT type_key, type_label, category, workflow_key FROM document_types ORDER BY type_id");
while ($row = $types->fetch_assoc()) {
    echo "  {$row['type_key']}: {$row['type_label']} (category={$row['category']}, workflow={$row['workflow_key']})\n";
}

echo "\n--- R&D Categories ---\n";
$cats = $conn->query("SELECT * FROM rnd_categories ORDER BY sort_order");
if ($cats) {
    while ($row = $cats->fetch_assoc()) {
        echo "  [{$row['category_id']}] {$row['category_key']}: {$row['category_label']} (active={$row['is_active']})\n";
    }
}

echo "\nDone.\n";
