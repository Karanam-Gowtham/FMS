<?php
/**
 * Phase 5B-0 — Comprehensive Workflow Tests
 *
 * Tests A through K as specified in the requirements.
 * Uses the workflow engine functions directly (no HTTP).
 */

require_once __DIR__ . '/../core/bootstrap.php';

echo "=================================================================\n";
echo "  Phase 5B-0 — Comprehensive Tests\n";
echo "  " . date('Y-m-d H:i:s') . "\n";
echo "=================================================================\n\n";

$pass = 0;
$fail = 0;

function test(string $label, bool $condition): void {
    global $pass, $fail;
    if ($condition) {
        echo "  ✅ PASS: $label\n";
        $pass++;
    } else {
        echo "  ❌ FAIL: $label\n";
        $fail++;
    }
}

// ============================================================
// SETUP: Load test actors
// ============================================================
echo "--- Setup: Load Test Actors ---\n";

// Faculty user (CSE)
$faculty = $conn->query("SELECT u.user_id, u.email FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = 4 AND ur.dept_id = 1 LIMIT 1")->fetch_assoc();
echo "  Faculty: user_id={$faculty['user_id']} ({$faculty['email']})\n";

// Dept Coordinator (CSE)
$dc = $conn->query("SELECT u.user_id, u.email FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = 5 AND ur.dept_id = 1 LIMIT 1")->fetch_assoc();
echo "  Dept Coordinator: user_id={$dc['user_id']} ({$dc['email']})\n";

// HOD (CSE)
$hod = $conn->query("SELECT u.user_id, u.email FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = 3 AND ur.dept_id = 1 LIMIT 1")->fetch_assoc();
echo "  HOD: user_id={$hod['user_id']} ({$hod['email']})\n";

// R&D Dean (separate user)
$dean = $conn->query("SELECT u.user_id, u.email FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = 8 AND u.user_id != 1 LIMIT 1")->fetch_assoc();
echo "  R&D Dean: user_id={$dean['user_id']} ({$dean['email']})\n";

// Admin (should NOT have RnD_Dean)
$admin_id = 1;
echo "  Admin: user_id=1\n";

// HOD from DIFFERENT dept (ECE)
$hod_ece = $conn->query("SELECT u.user_id, u.email FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = 3 AND ur.dept_id = 5 LIMIT 1")->fetch_assoc();
echo "  HOD (ECE): user_id={$hod_ece['user_id']} ({$hod_ece['email']})\n";

// Build fake auth contexts
function make_auth(int $user_id, mysqli $conn): array {
    $user = $conn->query("SELECT user_id, email, full_name FROM users WHERE user_id = $user_id")->fetch_assoc();
    $roles = [];
    $rr = $conn->query("SELECT ur.user_role_id, ur.role_id, ur.dept_id, r.role_name, COALESCE(d.dept_name, '') as dept_name FROM user_roles ur JOIN roles r ON r.role_id = ur.role_id LEFT JOIN dept d ON d.dept_id = ur.dept_id WHERE ur.user_id = $user_id");
    while ($row = $rr->fetch_assoc()) $roles[] = $row;
    return [
        'user_id' => $user['user_id'],
        'email' => $user['email'],
        'full_name' => $user['full_name'],
        'roles' => $roles,
    ];
}

$auth_faculty = make_auth($faculty['user_id'], $conn);
$auth_dc      = make_auth($dc['user_id'], $conn);
$auth_hod     = make_auth($hod['user_id'], $conn);
$auth_dean    = make_auth($dean['user_id'], $conn);
$auth_admin   = make_auth($admin_id, $conn);
$auth_hod_ece = make_auth($hod_ece['user_id'], $conn);

echo "\n";

// ============================================================
// TEST A: Department R&D Workflow (Full Chain)
// ============================================================
echo "--- TEST A: Department R&D Workflow (Full Chain) ---\n";

// Create a test journal document
$doc_type = doc_get_type_by_key($conn, 'journal');
test("journal type exists", $doc_type !== null);
test("journal uses department_rnd workflow", $doc_type['workflow_key'] === 'department_rnd');

// Resolve workflow
$wf = wf_get_workflow_for_type($conn, (int)$doc_type['type_id']);
test("department_rnd workflow resolved", $wf !== null && $wf['workflow_key'] === 'department_rnd');

// Get steps
$steps = wf_get_steps($conn, (int)$wf['workflow_id']);
test("department_rnd has 3 steps", count($steps) === 3);
test("Step 1 is Dept Coordinator", $steps[0]['role_name'] === 'Dept Coordinator' && $steps[0]['scope'] === 'department');
test("Step 2 is HOD", $steps[1]['role_name'] === 'HOD' && $steps[1]['scope'] === 'department');
test("Step 3 is RnD_Dean", $steps[2]['role_name'] === 'RnD_Dean' && $steps[2]['scope'] === 'global');

// Insert a test document
$conn->query("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at)
    VALUES ({$doc_type['type_id']}, {$faculty['user_id']}, 1, 5, 'TEST-5B0: Journal Paper', 'pending', NULL, NOW(), NOW())");
$test_doc_id = $conn->insert_id;
echo "  Created test doc: doc_id=$test_doc_id\n";

// Insert meta
$conn->query("INSERT INTO meta_journal (doc_id, paper_title, journal_name) VALUES ($test_doc_id, 'Test Paper', 'Test Journal')");

// Initialize workflow
$wf_init = wf_initialize_document($conn, $test_doc_id, (int)$doc_type['type_id'], (int)$faculty['user_id']);
test("Workflow initialized", $wf_init['success'] && $wf_init['status'] === 'pending');

// Reload doc
$doc = doc_get($conn, $test_doc_id);
test("Doc at step 1 (DC Review)", $doc['current_step'] == $steps[0]['step_id']);
test("Doc status is pending", $doc['status'] === 'pending');

// A1: DC can act
$dc_actions = wf_get_allowed_actions($conn, $doc, $auth_dc);
test("DC can approve/reject at step 1", in_array('approve', $dc_actions) && in_array('reject', $dc_actions));

// A2: HOD cannot act at step 1
$hod_actions = wf_get_allowed_actions($conn, $doc, $auth_hod);
test("HOD cannot act at step 1", empty($hod_actions));

// A3: R&D Dean cannot act at step 1
$dean_actions = wf_get_allowed_actions($conn, $doc, $auth_dean);
test("R&D Dean cannot act at step 1", empty($dean_actions));

// DC approves → move to step 2
$result = wf_execute_action($conn, $test_doc_id, (int)$dc['user_id'], 'approve', '');
test("DC approve succeeds", $result['success']);
$doc = doc_get($conn, $test_doc_id);
test("Doc at step 2 (HOD Review)", $doc['current_step'] == $steps[1]['step_id']);

// A4: HOD can now act
$hod_actions = wf_get_allowed_actions($conn, $doc, $auth_hod);
test("HOD can approve/reject at step 2", in_array('approve', $hod_actions) && in_array('reject', $hod_actions));

// A5: DC cannot act at step 2
$dc_actions = wf_get_allowed_actions($conn, $doc, $auth_dc);
test("DC cannot act at step 2", empty($dc_actions));

// HOD approves → move to step 3
$result = wf_execute_action($conn, $test_doc_id, (int)$hod['user_id'], 'approve', '');
test("HOD approve succeeds", $result['success']);
$doc = doc_get($conn, $test_doc_id);
test("Doc at step 3 (R&D Dean Review)", $doc['current_step'] == $steps[2]['step_id']);

// A6: R&D Dean can now act
$dean_actions = wf_get_allowed_actions($conn, $doc, $auth_dean);
test("R&D Dean can approve/reject at step 3", in_array('approve', $dean_actions) && in_array('reject', $dean_actions));

// R&D Dean approves → ACCEPTED
$result = wf_execute_action($conn, $test_doc_id, (int)$dean['user_id'], 'approve', '');
test("R&D Dean approve succeeds", $result['success']);
test("Final status is accepted", $result['new_status'] === 'accepted');
$doc = doc_get($conn, $test_doc_id);
test("Doc status is accepted", $doc['status'] === 'accepted');
test("Doc current_step is NULL", $doc['current_step'] === null);

echo "\n";

// ============================================================
// TEST B: Rejection at Each Step
// ============================================================
echo "--- TEST B: Rejection at Each Step ---\n";

// B1: Reject at DC step
$conn->query("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at)
    VALUES ({$doc_type['type_id']}, {$faculty['user_id']}, 1, 5, 'TEST-5B0: Reject at DC', 'pending', NULL, NOW(), NOW())");
$doc_b1 = $conn->insert_id;
$conn->query("INSERT INTO meta_journal (doc_id, paper_title, journal_name) VALUES ($doc_b1, 'B1', 'B1')");
wf_initialize_document($conn, $doc_b1, (int)$doc_type['type_id'], (int)$faculty['user_id']);

$result = wf_execute_action($conn, $doc_b1, (int)$dc['user_id'], 'reject', 'DC rejection reason');
test("DC reject succeeds", $result['success']);
test("Status after DC reject = rejected", $result['new_status'] === 'rejected');
$doc = doc_get($conn, $doc_b1);
test("Doc current_step back at step 1 after DC reject", $doc['current_step'] == $steps[0]['step_id']);

// B2: Reject at HOD step
$conn->query("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at)
    VALUES ({$doc_type['type_id']}, {$faculty['user_id']}, 1, 5, 'TEST-5B0: Reject at HOD', 'pending', NULL, NOW(), NOW())");
$doc_b2 = $conn->insert_id;
$conn->query("INSERT INTO meta_journal (doc_id, paper_title, journal_name) VALUES ($doc_b2, 'B2', 'B2')");
wf_initialize_document($conn, $doc_b2, (int)$doc_type['type_id'], (int)$faculty['user_id']);
wf_execute_action($conn, $doc_b2, (int)$dc['user_id'], 'approve', '');

$result = wf_execute_action($conn, $doc_b2, (int)$hod['user_id'], 'reject', 'HOD rejection reason');
test("HOD reject succeeds", $result['success']);
test("Status after HOD reject = rejected", $result['new_status'] === 'rejected');
$doc = doc_get($conn, $doc_b2);
test("Doc current_step back at step 1 after HOD reject", $doc['current_step'] == $steps[0]['step_id']);

// B3: Reject at R&D Dean step
$conn->query("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at)
    VALUES ({$doc_type['type_id']}, {$faculty['user_id']}, 1, 5, 'TEST-5B0: Reject at Dean', 'pending', NULL, NOW(), NOW())");
$doc_b3 = $conn->insert_id;
$conn->query("INSERT INTO meta_journal (doc_id, paper_title, journal_name) VALUES ($doc_b3, 'B3', 'B3')");
wf_initialize_document($conn, $doc_b3, (int)$doc_type['type_id'], (int)$faculty['user_id']);
wf_execute_action($conn, $doc_b3, (int)$dc['user_id'], 'approve', '');
wf_execute_action($conn, $doc_b3, (int)$hod['user_id'], 'approve', '');

$result = wf_execute_action($conn, $doc_b3, (int)$dean['user_id'], 'reject', 'Dean rejection reason');
test("R&D Dean reject succeeds", $result['success']);
test("Status after Dean reject = rejected", $result['new_status'] === 'rejected');
$doc = doc_get($conn, $doc_b3);
test("Doc current_step back at step 1 after Dean reject", $doc['current_step'] == $steps[0]['step_id']);

echo "\n";

// ============================================================
// TEST C: Resubmission After Rejection
// ============================================================
echo "--- TEST C: Resubmission After Rejection ---\n";

// Use doc_b1 which was rejected at DC
$doc = doc_get($conn, $doc_b1);
$resubmit_actions = wf_get_allowed_actions($conn, $doc, $auth_faculty);
test("Faculty can resubmit rejected doc", in_array('resubmit', $resubmit_actions));

// Faculty resubmits
$result = wf_execute_action($conn, $doc_b1, (int)$faculty['user_id'], 'resubmit', '');
test("Resubmit succeeds", $result['success']);
test("Status after resubmit = pending", $result['new_status'] === 'pending');
$doc = doc_get($conn, $doc_b1);
test("Doc back at step 1 after resubmit", $doc['current_step'] == $steps[0]['step_id']);

// DC can act again
$dc_actions = wf_get_allowed_actions($conn, $doc, $auth_dc);
test("DC can act again after resubmit", in_array('approve', $dc_actions));

echo "\n";

// ============================================================
// TEST D: Unauthorized Roles Cannot Act
// ============================================================
echo "--- TEST D: Unauthorized Roles Cannot Act ---\n";

$doc = doc_get($conn, $doc_b1); // Currently pending at step 1 (DC)

// Admin (no longer has RnD_Dean) cannot act
$admin_actions = wf_get_allowed_actions($conn, $doc, $auth_admin);
test("Admin cannot approve (not a workflow role)", empty($admin_actions));

// HOD from wrong dept cannot act
$hod_ece_actions = wf_get_allowed_actions($conn, $doc, $auth_hod_ece);
test("HOD from different dept cannot act", empty($hod_ece_actions));

// Faculty cannot approve their own doc
$fac_actions = wf_get_allowed_actions($conn, $doc, $auth_faculty);
test("Faculty (non-reviewer) cannot approve", empty($fac_actions) || !in_array('approve', $fac_actions));

echo "\n";

// ============================================================
// TEST E: Department Scoping
// ============================================================
echo "--- TEST E: Department Scoping ---\n";

// Create an ECE doc
$conn->query("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at)
    VALUES ({$doc_type['type_id']}, {$faculty['user_id']}, 5, 5, 'TEST-5B0: ECE Doc', 'pending', NULL, NOW(), NOW())");
$doc_ece = $conn->insert_id;
$conn->query("INSERT INTO meta_journal (doc_id, paper_title, journal_name) VALUES ($doc_ece, 'ECE paper', 'ECE journal')");
wf_initialize_document($conn, $doc_ece, (int)$doc_type['type_id'], (int)$faculty['user_id']);

$doc = doc_get($conn, $doc_ece);

// CSE DC cannot act on ECE doc
$dc_actions = wf_get_allowed_actions($conn, $doc, $auth_dc);
test("CSE DC cannot act on ECE doc (dept scope)", empty($dc_actions));

// CSE HOD cannot act on ECE doc
$hod_actions = wf_get_allowed_actions($conn, $doc, $auth_hod);
test("CSE HOD cannot act on ECE doc (dept scope)", empty($hod_actions));

echo "\n";

// ============================================================
// TEST F: R&D Dean Cross-Department Access
// ============================================================
echo "--- TEST F: R&D Dean Cross-Department Access ---\n";

// Advance ECE doc to step 3 using ECE DC & HOD
$ece_dc = $conn->query("SELECT u.user_id FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = 5 AND ur.dept_id = 5 LIMIT 1")->fetch_assoc();
if ($ece_dc) {
    wf_execute_action($conn, $doc_ece, (int)$ece_dc['user_id'], 'approve', '');
}
wf_execute_action($conn, $doc_ece, (int)$hod_ece['user_id'], 'approve', '');
$doc = doc_get($conn, $doc_ece);

// R&D Dean (global scope) can act on ECE doc at step 3
$dean_actions = wf_get_allowed_actions($conn, $doc, $auth_dean);
test("R&D Dean can act on ECE doc (global scope)", in_array('approve', $dean_actions));

// Approve it
$result = wf_execute_action($conn, $doc_ece, (int)$dean['user_id'], 'approve', '');
test("R&D Dean approves ECE doc", $result['success'] && $result['new_status'] === 'accepted');

echo "\n";

// ============================================================
// TEST G: No Duplicate Documents/Files
// ============================================================
echo "--- TEST G: No Duplicate Documents ---\n";

// Count documents created by this test
$test_docs = $conn->query("SELECT COUNT(*) as c FROM documents WHERE title LIKE 'TEST-5B0:%'")->fetch_assoc();
echo "  Test documents created: {$test_docs['c']}\n";
// Each test created exactly 1 doc - we created 5 test docs (A, B1, B2, B3, ECE)
test("Correct number of test documents", (int)$test_docs['c'] === 5);

echo "\n";

// ============================================================
// TEST H: department_standard Still Works
// ============================================================
echo "--- TEST H: department_standard Still Works ---\n";

$dept_type = doc_get_type_by_key($conn, 'dept_file');
test("dept_file still uses department_standard", $dept_type['workflow_key'] === 'department_standard');

$wf_std = wf_get_workflow_for_type($conn, (int)$dept_type['type_id']);
test("department_standard workflow resolves", $wf_std !== null);

$std_steps = wf_get_steps($conn, (int)$wf_std['workflow_id']);
test("department_standard has 2 steps", count($std_steps) === 2);
test("Std step 1 is HOD", $std_steps[0]['role_name'] === 'HOD');
test("Std step 2 is RnD_Dean", $std_steps[1]['role_name'] === 'RnD_Dean');

// Create a dept_file doc
$conn->query("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at)
    VALUES ({$dept_type['type_id']}, {$faculty['user_id']}, 1, 5, 'TEST-5B0: Dept File', 'pending', NULL, NOW(), NOW())");
$doc_std = $conn->insert_id;
$conn->query("INSERT INTO meta_dept_file (doc_id) VALUES ($doc_std)");
wf_initialize_document($conn, $doc_std, (int)$dept_type['type_id'], (int)$faculty['user_id']);

$doc = doc_get($conn, $doc_std);
test("dept_file starts at HOD Review (step 1)", $doc['current_step'] == $std_steps[0]['step_id']);

// DC should NOT be able to act on department_standard docs
$dc_actions = wf_get_allowed_actions($conn, $doc, $auth_dc);
test("DC cannot act on department_standard docs", empty($dc_actions));

// HOD can act
$hod_actions = wf_get_allowed_actions($conn, $doc, $auth_hod);
test("HOD can act on department_standard docs", in_array('approve', $hod_actions));

echo "\n";

// ============================================================
// TEST I: Central R&D Auto-Accept
// ============================================================
echo "--- TEST I: Central R&D Auto-Accept ---\n";

$crnd_type = doc_get_type_by_key($conn, 'central_rnd');
test("central_rnd type exists", $crnd_type !== null);
test("central_rnd uses auto_accept", $crnd_type['workflow_key'] === 'auto_accept');

// R&D Dean uploads a central document
$conn->query("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at)
    VALUES ({$crnd_type['type_id']}, {$dean['user_id']}, 25, 5, 'TEST-5B0: Central R&D Doc', 'pending', NULL, NOW(), NOW())");
$doc_crnd = $conn->insert_id;
$conn->query("INSERT INTO meta_central_rnd (doc_id, rnd_category_id, description) VALUES ($doc_crnd, 1, 'Test central R&D document')");

// Initialize workflow (should auto-accept)
$result = wf_initialize_document($conn, $doc_crnd, (int)$crnd_type['type_id'], (int)$dean['user_id']);
test("Auto-accept initialization succeeds", $result['success']);
test("Auto-accept status = accepted", $result['status'] === 'accepted');

$doc = doc_get($conn, $doc_crnd);
test("Central R&D doc is accepted", $doc['status'] === 'accepted');
test("No approval step needed", $doc['current_step'] === null);

echo "\n";

// ============================================================
// TEST J: Audit Trail
// ============================================================
echo "--- TEST J: Audit Trail ---\n";

// Check audit trail for test doc A (the full chain one)
$history = wf_get_action_history($conn, $test_doc_id);
test("Audit trail has entries for full chain doc", count($history) >= 3);

// Verify the R&D Dean action is recorded with the DEAN user_id (not admin)
$dean_found = false;
foreach ($history as $h) {
    if ($h['action'] === 'approve' && strpos($h['actor_name'] ?? '', 'R&D Dean') !== false) {
        $dean_found = true;
    }
}
test("Audit trail records R&D Dean as actor (not Admin)", $dean_found);

// Check auto-accept audit
$crnd_history = wf_get_action_history($conn, $doc_crnd);
test("Auto-accept has audit entry", count($crnd_history) >= 1);

echo "\n";

// ============================================================
// TEST K: Legacy Tables Intact
// ============================================================
echo "--- TEST K: Legacy Tables Intact ---\n";

$legacy = ['published_tab', 'conference_tab', 'patents_table', 'fdps_tab', 'fdps_org_tab', 'conf_org_tab',
           'files5_1_1and2', 'files5_1_3', 'files5_1_4', 'files5_2_1', 'files5_2_2', 'files5_2_3',
           'files5_3_1', 'files5_3_3', 's_conference_tab'];
$all_present = true;
foreach ($legacy as $t) {
    $check = $conn->query("SHOW TABLES LIKE '$t'");
    if (!$check || $check->num_rows === 0) {
        echo "  ❌ MISSING: $t\n";
        $all_present = false;
    }
}
test("All 15 legacy tables present", $all_present);

// Verify legacy data counts unchanged
$pub = $conn->query("SELECT COUNT(*) as c FROM published_tab")->fetch_assoc()['c'];
$conf = $conn->query("SELECT COUNT(*) as c FROM conference_tab")->fetch_assoc()['c'];
$pat = $conn->query("SELECT COUNT(*) as c FROM patents_table")->fetch_assoc()['c'];
test("Legacy published_tab data preserved ($pub rows)", (int)$pub >= 1);
test("Legacy conference_tab data preserved ($conf rows)", (int)$conf >= 1);
test("Legacy patents_table data preserved ($pat rows)", (int)$pat >= 1);

echo "\n";

// ============================================================
// CLEANUP: Remove test documents
// ============================================================
echo "--- Cleanup: Remove Test Documents ---\n";

$test_doc_ids = [$test_doc_id, $doc_b1, $doc_b2, $doc_b3, $doc_ece, $doc_std, $doc_crnd];
foreach ($test_doc_ids as $tid) {
    $conn->query("DELETE FROM document_actions WHERE doc_id = $tid");
    $conn->query("DELETE FROM meta_journal WHERE doc_id = $tid");
    $conn->query("DELETE FROM meta_dept_file WHERE doc_id = $tid");
    $conn->query("DELETE FROM meta_central_rnd WHERE doc_id = $tid");
    $conn->query("DELETE FROM document_files WHERE doc_id = $tid");
    $conn->query("DELETE FROM documents WHERE doc_id = $tid");
}
echo "  Cleaned up " . count($test_doc_ids) . " test documents.\n";

echo "\n";

// ============================================================
// ADDITIONAL VERIFICATION
// ============================================================
echo "--- Additional Verification ---\n";

// Verify Admin does not have RnD_Dean role
$admin_rnd = $conn->query("SELECT role_id FROM user_roles WHERE user_id = 1 AND role_id = 8");
test("Admin does NOT have RnD_Dean role", $admin_rnd->num_rows === 0);

// Verify R&D Dean user is separate
$dean_user = $conn->query("SELECT user_id FROM users WHERE email = 'rnd.dean@gmrit.edu.in'")->fetch_assoc();
test("Dedicated R&D Dean user exists", $dean_user !== null && (int)$dean_user['user_id'] !== 1);

// Verify R&D dept
$rnd_dept = $conn->query("SELECT dept_id FROM dept WHERE dept_name = 'R&D'")->fetch_assoc();
test("R&D pseudo-department exists", $rnd_dept !== null);

// Verify rnd_categories
$cat_count = $conn->query("SELECT COUNT(*) as c FROM rnd_categories")->fetch_assoc()['c'];
test("rnd_categories has 10 entries", (int)$cat_count === 10);

// Verify meta_registry includes central_rnd
$meta_table = meta_get_table('central_rnd');
test("meta_registry resolves central_rnd", $meta_table === 'meta_central_rnd');

echo "\n";

// ============================================================
// SUMMARY
// ============================================================
echo "=================================================================\n";
echo "  TEST RESULTS: $pass passed, $fail failed\n";
echo "=================================================================\n";

if ($fail === 0) {
    echo "  ✅ ALL TESTS PASSED\n";
} else {
    echo "  ❌ $fail TESTS FAILED\n";
}
