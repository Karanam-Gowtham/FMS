<?php
/**
 * Phase 5A — Workflow Configuration Verification
 *
 * Proves the workflow engine is truly data-driven by creating a 5-step
 * approval chain using ONLY database records. No PHP code is changed.
 *
 * Chain: Faculty → Dept Coordinator → HOD → Central Coordinator → R&D Dean → Accepted
 */
require_once __DIR__ . '/../core/bootstrap.php';

echo "=== Phase 5A: Workflow Configuration Verification ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

$pass = 0; $fail = 0;
function test(string $name, bool $ok, string $detail = ''): void {
    global $pass, $fail;
    if ($ok) { $pass++; echo "  PASS: $name"; } else { $fail++; echo "  FAIL: $name"; }
    if ($detail) echo " — $detail";
    echo "\n";
}

function build_auth(mysqli $conn, int $user_id): array {
    $user = $conn->query("SELECT user_id, full_name, email FROM users WHERE user_id = $user_id")->fetch_assoc();
    $roles_q = $conn->query("SELECT ur.user_role_id, ur.role_id, ur.dept_id, r.role_name, COALESCE(d.dept_name,'') as dept_name FROM user_roles ur JOIN roles r ON r.role_id = ur.role_id LEFT JOIN dept d ON d.dept_id = ur.dept_id WHERE ur.user_id = $user_id");
    $roles = [];
    while ($r = $roles_q->fetch_assoc()) $roles[] = $r;
    return ['user_id' => $user['user_id'], 'full_name' => $user['full_name'], 'email' => $user['email'], 'roles' => $roles];
}

function reload(mysqli $conn, int $id): array {
    return $conn->query("SELECT * FROM documents WHERE doc_id = $id")->fetch_assoc();
}

// ============================================================
// STEP 1: CREATE TEST WORKFLOW (pure SQL)
// ============================================================
echo "--- STEP 1: Create 5-step test workflow ---\n\n";

// 1a. Insert workflow
$conn->query("INSERT INTO workflows (workflow_key, label, description, is_active) VALUES ('test_5step', 'TEST: 5-Step Full Chain', 'Faculty → DC → HOD → Central Coord → R&D Dean → Accepted', 1)");
$wf_id = $conn->insert_id;
echo "  Workflow: id=$wf_id, key=test_5step\n";

// 1b. Insert 5 steps
// Role IDs: 5=Dept Coordinator, 3=HOD, 6=Central Coordinator, 8=RnD_Dean
$steps = [
    ['order' => 1, 'label' => 'Dept Coordinator Review', 'role_id' => ROLE_DEPT_COORDINATOR, 'scope' => 'department'],
    ['order' => 2, 'label' => 'HOD Review',              'role_id' => ROLE_HOD,              'scope' => 'department'],
    ['order' => 3, 'label' => 'Central Coordinator Review', 'role_id' => ROLE_CENTRAL_COORDINATOR, 'scope' => 'global'],
    ['order' => 4, 'label' => 'R&D Dean Final Review',   'role_id' => ROLE_RND_DEAN,         'scope' => 'global'],
];

$step_ids = [];
foreach ($steps as $s) {
    $stmt = $conn->prepare("INSERT INTO workflow_steps (workflow_id, step_order, step_label, responsible_role_id, scope) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('iisis', $wf_id, $s['order'], $s['label'], $s['role_id'], $s['scope']);
    $stmt->execute();
    $step_ids[$s['order']] = $stmt->insert_id;
    $stmt->close();
    echo "  Step {$s['order']}: id={$step_ids[$s['order']]}, {$s['label']} (role_id={$s['role_id']}, scope={$s['scope']})\n";
}

// 1c. Get action IDs
$actions = [];
$r = $conn->query("SELECT action_id, action_key FROM workflow_actions");
while ($row = $r->fetch_assoc()) $actions[$row['action_key']] = (int)$row['action_id'];
echo "\n  Actions: " . json_encode($actions) . "\n";

// 1d. Insert transitions
$transitions = [
    // Step 1 (DC): approve→step2, reject→step1, resubmit→step1
    [$step_ids[1], $actions['approve'],  $step_ids[2], 'pending'],
    [$step_ids[1], $actions['reject'],   $step_ids[1], 'rejected'],
    [$step_ids[1], $actions['resubmit'], $step_ids[1], 'pending'],
    // Step 2 (HOD): approve→step3, reject→step1, resubmit→step1
    [$step_ids[2], $actions['approve'],  $step_ids[3], 'pending'],
    [$step_ids[2], $actions['reject'],   $step_ids[1], 'rejected'],
    [$step_ids[2], $actions['resubmit'], $step_ids[1], 'pending'],
    // Step 3 (Central Coord): approve→step4, reject→step1, resubmit→step1
    [$step_ids[3], $actions['approve'],  $step_ids[4], 'pending'],
    [$step_ids[3], $actions['reject'],   $step_ids[1], 'rejected'],
    [$step_ids[3], $actions['resubmit'], $step_ids[1], 'pending'],
    // Step 4 (R&D Dean): approve→TERMINAL(accepted), reject→step1, resubmit→step1
    [$step_ids[4], $actions['approve'],  null,          'accepted'],
    [$step_ids[4], $actions['reject'],   $step_ids[1], 'rejected'],
    [$step_ids[4], $actions['resubmit'], $step_ids[1], 'pending'],
];

echo "\n  Transitions:\n";
foreach ($transitions as $t) {
    $stmt = $conn->prepare("INSERT INTO workflow_transitions (step_id, action_id, to_step_id, resulting_status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiis', $t[0], $t[1], $t[2], $t[3]);
    $stmt->execute();
    $tid = $stmt->insert_id;
    $stmt->close();
    $to_label = $t[2] === null ? 'TERMINAL' : "step_id={$t[2]}";
    echo "    transition_id=$tid: step_id={$t[0]} + action_id={$t[1]} → $to_label (status={$t[3]})\n";
}

// 1e. Insert test document type linked to this workflow
$conn->query("INSERT INTO document_types (type_key, type_label, category, workflow_key, meta_table, is_active) VALUES ('test_5step_doc', 'TEST 5-Step Document', 'test', 'test_5step', NULL, 1)");
$test_type_id = $conn->insert_id;
echo "\n  Document type: id=$test_type_id, key=test_5step_doc, workflow=test_5step\n";

// ============================================================
// STEP 2: RESOLVE TEST USERS
// ============================================================
echo "\n--- STEP 2: Resolve test users ---\n";

// Faculty (CSE, dept_id=1)
$faculty = $conn->query("SELECT u.user_id FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = " . ROLE_FACULTY . " AND ur.dept_id = 1 LIMIT 1")->fetch_assoc();
$faculty_uid = (int)$faculty['user_id'];
echo "  Faculty: user_id=$faculty_uid (CSE)\n";

// Dept Coordinator (CSE, dept_id=1)
$dc = $conn->query("SELECT u.user_id FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = " . ROLE_DEPT_COORDINATOR . " AND ur.dept_id = 1 LIMIT 1")->fetch_assoc();
$dc_uid = (int)$dc['user_id'];
echo "  Dept Coordinator: user_id=$dc_uid (CSE)\n";

// HOD (CSE, dept_id=1)
$hod = $conn->query("SELECT u.user_id FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = " . ROLE_HOD . " AND ur.dept_id = 1 LIMIT 1")->fetch_assoc();
$hod_uid = (int)$hod['user_id'];
echo "  HOD: user_id=$hod_uid (CSE)\n";

// Central Coordinator (global)
$cc = $conn->query("SELECT u.user_id FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = " . ROLE_CENTRAL_COORDINATOR . " LIMIT 1")->fetch_assoc();
$cc_uid = (int)$cc['user_id'];
echo "  Central Coordinator: user_id=$cc_uid\n";

// R&D Dean (global)
$dean = $conn->query("SELECT u.user_id FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = " . ROLE_RND_DEAN . " LIMIT 1")->fetch_assoc();
$dean_uid = (int)$dean['user_id'];
echo "  R&D Dean: user_id=$dean_uid\n";

$auth_faculty = build_auth($conn, $faculty_uid);
$auth_dc      = build_auth($conn, $dc_uid);
$auth_hod     = build_auth($conn, $hod_uid);
$auth_cc      = build_auth($conn, $cc_uid);
$auth_dean    = build_auth($conn, $dean_uid);

// ============================================================
// HELPER: Create and initialize a test document
// ============================================================
function create_test_doc(mysqli $conn, int $type_id, int $faculty_uid, string $title): int {
    $dept_id = 1; // CSE
    $stmt = $conn->prepare("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at) VALUES (?, ?, ?, NULL, ?, 'pending', NULL, NOW(), NOW())");
    $stmt->bind_param('iiis', $type_id, $faculty_uid, $dept_id, $title);
    $stmt->execute();
    $doc_id = $stmt->insert_id;
    $stmt->close();
    wf_initialize_document($conn, $doc_id, $type_id, $faculty_uid);
    return $doc_id;
}

// ============================================================
// TEST A: Full 5-step approval chain (Verifications 1-6)
// ============================================================
echo "\n--- TEST A: Full 5-step approval chain ---\n";

$docA = create_test_doc($conn, $test_type_id, $faculty_uid, 'TEST-5A-FullChain');
$d = reload($conn, $docA);
echo "  Created doc_id=$docA\n";

// V1: Faculty uploads → pending at step 1 (DC)
test("V1: Doc starts pending at DC step", $d['status'] === 'pending' && (int)$d['current_step'] === $step_ids[1], "step={$d['current_step']}, expected={$step_ids[1]}");

// V2: DC is the pending approver
$acts = wf_get_allowed_actions($conn, $d, $auth_dc);
test("V2: DC sees approve/reject", in_array('approve', $acts) && in_array('reject', $acts), json_encode($acts));

// Faculty cannot approve at DC step
$faculty_acts = wf_get_allowed_actions($conn, $d, $auth_faculty);
test("V10a: Faculty cannot approve at DC step", empty($faculty_acts), json_encode($faculty_acts));

// HOD cannot approve at DC step
$hod_acts = wf_get_allowed_actions($conn, $d, $auth_hod);
test("V10b: HOD cannot approve at DC step", empty($hod_acts), json_encode($hod_acts));

// V3: DC approves → moves to HOD (step 2)
$res = wf_execute_action($conn, $docA, $dc_uid, 'approve', 'DC approves');
$d = reload($conn, $docA);
test("V3: DC approve → HOD step", $d['status'] === 'pending' && (int)$d['current_step'] === $step_ids[2], "step={$d['current_step']}, expected={$step_ids[2]}");

// HOD can now act
$hod_acts = wf_get_allowed_actions($conn, $d, $auth_hod);
test("HOD sees approve/reject at step 2", in_array('approve', $hod_acts), json_encode($hod_acts));

// DC cannot act at HOD step
$dc_acts = wf_get_allowed_actions($conn, $d, $auth_dc);
test("V10c: DC cannot approve at HOD step", empty($dc_acts), json_encode($dc_acts));

// V4: HOD approves → moves to Central Coordinator (step 3)
$res = wf_execute_action($conn, $docA, $hod_uid, 'approve', 'HOD approves');
$d = reload($conn, $docA);
test("V4: HOD approve → CC step", $d['status'] === 'pending' && (int)$d['current_step'] === $step_ids[3], "step={$d['current_step']}, expected={$step_ids[3]}");

// CC can act (global scope)
$cc_acts = wf_get_allowed_actions($conn, $d, $auth_cc);
test("CC sees approve/reject at step 3", in_array('approve', $cc_acts), json_encode($cc_acts));

// V5: CC approves → moves to R&D Dean (step 4)
$res = wf_execute_action($conn, $docA, $cc_uid, 'approve', 'CC approves');
$d = reload($conn, $docA);
test("V5: CC approve → Dean step", $d['status'] === 'pending' && (int)$d['current_step'] === $step_ids[4], "step={$d['current_step']}, expected={$step_ids[4]}");

// Dean can act
$dean_acts = wf_get_allowed_actions($conn, $d, $auth_dean);
test("Dean sees approve/reject at step 4", in_array('approve', $dean_acts), json_encode($dean_acts));

// V6: Dean approves → Accepted (terminal)
$res = wf_execute_action($conn, $docA, $dean_uid, 'approve', 'Dean final approval');
$d = reload($conn, $docA);
test("V6: Dean approve → Accepted", $d['status'] === 'accepted', "status={$d['status']}");
test("V6: No actions on accepted doc", empty(wf_get_allowed_actions($conn, $d, $auth_dean)));

// ============================================================
// TEST B: Reject at every stage (Verification 7)
// ============================================================
echo "\n--- TEST B: Reject at every stage ---\n";

// B1: DC rejects
$docB1 = create_test_doc($conn, $test_type_id, $faculty_uid, 'TEST-5A-RejectAtDC');
$res = wf_execute_action($conn, $docB1, $dc_uid, 'reject', 'DC rejects');
$d = reload($conn, $docB1);
test("V7a: DC reject → rejected", $d['status'] === 'rejected', "status={$d['status']}");

// B2: HOD rejects (after DC approves)
$docB2 = create_test_doc($conn, $test_type_id, $faculty_uid, 'TEST-5A-RejectAtHOD');
wf_execute_action($conn, $docB2, $dc_uid, 'approve', 'DC passes');
$res = wf_execute_action($conn, $docB2, $hod_uid, 'reject', 'HOD rejects');
$d = reload($conn, $docB2);
test("V7b: HOD reject → rejected", $d['status'] === 'rejected', "status={$d['status']}");

// B3: CC rejects (after DC+HOD approve)
$docB3 = create_test_doc($conn, $test_type_id, $faculty_uid, 'TEST-5A-RejectAtCC');
wf_execute_action($conn, $docB3, $dc_uid, 'approve', 'DC passes');
wf_execute_action($conn, $docB3, $hod_uid, 'approve', 'HOD passes');
$res = wf_execute_action($conn, $docB3, $cc_uid, 'reject', 'CC rejects');
$d = reload($conn, $docB3);
test("V7c: CC reject → rejected", $d['status'] === 'rejected', "status={$d['status']}");

// B4: Dean rejects (after DC+HOD+CC approve)
$docB4 = create_test_doc($conn, $test_type_id, $faculty_uid, 'TEST-5A-RejectAtDean');
wf_execute_action($conn, $docB4, $dc_uid, 'approve', 'DC passes');
wf_execute_action($conn, $docB4, $hod_uid, 'approve', 'HOD passes');
wf_execute_action($conn, $docB4, $cc_uid, 'approve', 'CC passes');
$res = wf_execute_action($conn, $docB4, $dean_uid, 'reject', 'Dean rejects');
$d = reload($conn, $docB4);
test("V7d: Dean reject → rejected", $d['status'] === 'rejected', "status={$d['status']}");

// ============================================================
// TEST C: Resubmit after rejection (Verifications 8-9)
// ============================================================
echo "\n--- TEST C: Resubmit after rejection ---\n";

// Resubmit after DC reject
$d = reload($conn, $docB1);
$acts = wf_get_allowed_actions($conn, $d, $auth_faculty);
test("V8a: Faculty sees resubmit after DC reject", in_array('resubmit', $acts), json_encode($acts));

$res = wf_execute_action($conn, $docB1, $faculty_uid, 'resubmit', 'Faculty resubmits');
$d = reload($conn, $docB1);
test("V9a: Resubmit returns to step 1 (DC)", $d['status'] === 'pending' && (int)$d['current_step'] === $step_ids[1], "step={$d['current_step']}");

// Resubmit after Dean reject (should also go back to step 1)
$d = reload($conn, $docB4);
$acts = wf_get_allowed_actions($conn, $d, $auth_faculty);
test("V8b: Faculty sees resubmit after Dean reject", in_array('resubmit', $acts), json_encode($acts));

$res = wf_execute_action($conn, $docB4, $faculty_uid, 'resubmit', 'Faculty resubmits after Dean rejection');
$d = reload($conn, $docB4);
test("V9b: Resubmit after Dean reject returns to step 1", $d['status'] === 'pending' && (int)$d['current_step'] === $step_ids[1], "step={$d['current_step']}");

// ============================================================
// TEST D: Unauthorized role checks (Verification 10)
// ============================================================
echo "\n--- TEST D: Unauthorized role checks ---\n";

$docD = create_test_doc($conn, $test_type_id, $faculty_uid, 'TEST-5A-UnauthorizedCheck');
$d = reload($conn, $docD);

// At step 1 (DC): only DC should be able to act
test("V10d: Faculty cannot act at DC step", empty(wf_get_allowed_actions($conn, $d, $auth_faculty)));
test("V10e: HOD cannot act at DC step", empty(wf_get_allowed_actions($conn, $d, $auth_hod)));
test("V10f: CC cannot act at DC step", empty(wf_get_allowed_actions($conn, $d, $auth_cc)));
test("V10g: Dean cannot act at DC step", empty(wf_get_allowed_actions($conn, $d, $auth_dean)));
test("V10h: DC CAN act at DC step", !empty(wf_get_allowed_actions($conn, $d, $auth_dc)));

// Move to step 2 (HOD)
wf_execute_action($conn, $docD, $dc_uid, 'approve', 'DC passes');
$d = reload($conn, $docD);
test("V10i: DC cannot act at HOD step", empty(wf_get_allowed_actions($conn, $d, $auth_dc)));
test("V10j: HOD CAN act at HOD step", !empty(wf_get_allowed_actions($conn, $d, $auth_hod)));
test("V10k: CC cannot act at HOD step", empty(wf_get_allowed_actions($conn, $d, $auth_cc)));

// Move to step 3 (CC)
wf_execute_action($conn, $docD, $hod_uid, 'approve', 'HOD passes');
$d = reload($conn, $docD);
test("V10l: HOD cannot act at CC step", empty(wf_get_allowed_actions($conn, $d, $auth_hod)));
test("V10m: CC CAN act at CC step", !empty(wf_get_allowed_actions($conn, $d, $auth_cc)));

// Move to step 4 (Dean)
wf_execute_action($conn, $docD, $cc_uid, 'approve', 'CC passes');
$d = reload($conn, $docD);
test("V10n: CC cannot act at Dean step", empty(wf_get_allowed_actions($conn, $d, $auth_cc)));
test("V10o: Dean CAN act at Dean step", !empty(wf_get_allowed_actions($conn, $d, $auth_dean)));

// ============================================================
// TEST E: Audit trail for full chain
// ============================================================
echo "\n--- TEST E: Audit trail ---\n";
$trail = wf_get_action_history($conn, $docA);
test("Full chain has audit trail", count($trail) >= 5, "entries=" . count($trail));
$trail_actions = array_column($trail, 'action');
test("Trail includes uploaded", in_array('uploaded', $trail_actions));
test("Trail includes approve", in_array('approve', $trail_actions));

// ============================================================
// CLEANUP: Remove test workflow data
// ============================================================
echo "\n--- CLEANUP ---\n";

// Collect all test doc IDs
$test_docs = [$docA, $docB1, $docB2, $docB3, $docB4, $docD];
$doc_list = implode(',', $test_docs);

$conn->query("DELETE FROM document_actions WHERE doc_id IN ($doc_list)");
echo "  Removed document_actions for test docs\n";

$conn->query("DELETE FROM documents WHERE doc_id IN ($doc_list)");
echo "  Removed test documents: $doc_list\n";

// Remove test document type
$conn->query("DELETE FROM document_types WHERE type_key = 'test_5step_doc'");
echo "  Removed test document type\n";

// Remove transitions
$step_list = implode(',', $step_ids);
$conn->query("DELETE FROM workflow_transitions WHERE step_id IN ($step_list)");
echo "  Removed test transitions\n";

// Remove steps
$conn->query("DELETE FROM workflow_steps WHERE workflow_id = $wf_id");
echo "  Removed test steps\n";

// Remove workflow
$conn->query("DELETE FROM workflows WHERE workflow_id = $wf_id");
echo "  Removed test workflow (id=$wf_id)\n";

// ============================================================
// SUMMARY
// ============================================================
echo "\n=== SUMMARY ===\n";
echo "PASSED: $pass\n";
echo "FAILED: $fail\n";
echo ($fail === 0) ? "ALL TESTS PASSED ✓\n" : "SOME TESTS FAILED ✗\n";
echo "\nPHP workflow engine files modified: ZERO\n";
echo "Done.\n";
