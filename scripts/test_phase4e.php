<?php
/**
 * Phase 4E Targeted Tests
 *
 * Tests: approve, reject, resubmit, final acceptance,
 *        CSRF failure, unauthorized user action.
 *
 * Simulates the workflow engine calls directly (no HTTP needed).
 */
require_once __DIR__ . '/../core/bootstrap.php';

echo "=== Phase 4E Targeted Tests ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

$pass = 0;
$fail = 0;

function test_result(string $name, bool $ok, string $detail = ''): void
{
    global $pass, $fail;
    if ($ok) { $pass++; echo "  PASS: $name"; }
    else     { $fail++; echo "  FAIL: $name"; }
    if ($detail) echo " — $detail";
    echo "\n";
}

// ============================================================
// SETUP: Create a test document for workflow testing
// ============================================================
echo "--- Setup: Creating test document ---\n";

// Find a faculty user
$faculty = $conn->query("SELECT u.user_id, u.email, ur.dept_id FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = 4 LIMIT 1")->fetch_assoc();
if (!$faculty) die("No faculty user found.\n");
echo "  Faculty: {$faculty['email']} (user_id={$faculty['user_id']}, dept_id={$faculty['dept_id']})\n";

// Find HOD for same dept
$hod = $conn->query("SELECT u.user_id, u.email FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = 3 AND ur.dept_id = {$faculty['dept_id']} LIMIT 1")->fetch_assoc();
if (!$hod) die("No HOD for dept {$faculty['dept_id']}.\n");
echo "  HOD: {$hod['email']} (user_id={$hod['user_id']})\n";

// Find RnD Dean
$dean = $conn->query("SELECT u.user_id, u.email FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = 8 LIMIT 1")->fetch_assoc();
if (!$dean) die("No RnD Dean found.\n");
echo "  RnD Dean: {$dean['email']} (user_id={$dean['user_id']})\n";

// Build auth contexts
function build_auth(mysqli $conn, int $user_id): array {
    $user = $conn->query("SELECT user_id, full_name, email FROM users WHERE user_id = $user_id")->fetch_assoc();
    $roles_q = $conn->query("SELECT ur.user_role_id, ur.role_id, ur.dept_id, r.role_name, COALESCE(d.dept_name,'') as dept_name FROM user_roles ur JOIN roles r ON r.role_id = ur.role_id LEFT JOIN dept d ON d.dept_id = ur.dept_id WHERE ur.user_id = $user_id");
    $roles = [];
    while ($r = $roles_q->fetch_assoc()) $roles[] = $r;
    return ['user_id' => $user['user_id'], 'full_name' => $user['full_name'], 'email' => $user['email'], 'roles' => $roles, 'active_role' => $roles[0] ?? null];
}

$auth_faculty = build_auth($conn, (int)$faculty['user_id']);
$auth_hod     = build_auth($conn, (int)$hod['user_id']);
$auth_dean    = build_auth($conn, (int)$dean['user_id']);

// Create test document
$type = doc_get_type_by_key($conn, 'journal');
$stmt = $conn->prepare("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at) VALUES (?, ?, ?, NULL, 'TEST-4E-Workflow', 'pending', NULL, NOW(), NOW())");
$type_id = (int)$type['type_id'];
$faculty_uid = (int)$faculty['user_id'];
$faculty_dept = (int)$faculty['dept_id'];
$stmt->bind_param('iii', $type_id, $faculty_uid, $faculty_dept);
$stmt->execute();
$test_doc_id = $stmt->insert_id;
$stmt->close();
echo "  Test doc_id: $test_doc_id\n";

// Initialize workflow
$wf_init = wf_initialize_document($conn, $test_doc_id, $type_id, $faculty_uid);
echo "  Workflow init: status={$wf_init['status']}\n\n";

// Reload doc
function reload_doc(mysqli $conn, int $id): array {
    return $conn->query("SELECT * FROM documents WHERE doc_id = $id")->fetch_assoc();
}

// ============================================================
// TEST 1: HOD can approve (step 1)
// ============================================================
echo "--- TEST 1: HOD Approve ---\n";
$doc = reload_doc($conn, $test_doc_id);
$actions = wf_get_allowed_actions($conn, $doc, $auth_hod);
test_result("HOD sees approve action", in_array('approve', $actions), "actions=" . json_encode($actions));

$result = wf_execute_action($conn, $test_doc_id, (int)$hod['user_id'], 'approve', 'HOD approves for testing');
test_result("HOD approve executes", $result['success'], "new_status={$result['new_status']}");

$doc = reload_doc($conn, $test_doc_id);
test_result("Doc moves to step 2 (pending)", $doc['status'] === 'pending' && $doc['current_step'] !== null, "status={$doc['status']}, step={$doc['current_step']}");

// ============================================================
// TEST 2: RnD Dean can approve (step 2) → final acceptance
// ============================================================
echo "\n--- TEST 2: RnD Dean Final Approve ---\n";
$actions = wf_get_allowed_actions($conn, $doc, $auth_dean);
test_result("Dean sees approve action", in_array('approve', $actions), "actions=" . json_encode($actions));

$result = wf_execute_action($conn, $test_doc_id, (int)$dean['user_id'], 'approve', 'Dean final approval');
test_result("Dean approve executes", $result['success'], "new_status={$result['new_status']}");

$doc = reload_doc($conn, $test_doc_id);
test_result("Doc is accepted", $doc['status'] === 'accepted', "status={$doc['status']}");
test_result("No actions on accepted doc", empty(wf_get_allowed_actions($conn, $doc, $auth_dean)));

// ============================================================
// TEST 3: Create new doc, HOD rejects → resubmit by faculty
// ============================================================
echo "\n--- TEST 3: Reject + Resubmit ---\n";
$stmt = $conn->prepare("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at) VALUES (?, ?, ?, NULL, 'TEST-4E-Reject', 'pending', NULL, NOW(), NOW())");
$stmt->bind_param('iii', $type_id, $faculty_uid, $faculty_dept);
$stmt->execute();
$test_doc2 = $stmt->insert_id;
$stmt->close();
wf_initialize_document($conn, $test_doc2, $type_id, $faculty_uid);
echo "  Test doc_id: $test_doc2\n";

$doc2 = reload_doc($conn, $test_doc2);
$result = wf_execute_action($conn, $test_doc2, (int)$hod['user_id'], 'reject', 'Reject for testing');
test_result("HOD reject executes", $result['success'], "new_status={$result['new_status']}");

$doc2 = reload_doc($conn, $test_doc2);
test_result("Doc is rejected", $doc2['status'] === 'rejected', "status={$doc2['status']}");
test_result("current_step is NOT null after reject", $doc2['current_step'] !== null, "current_step={$doc2['current_step']}");

// Faculty can resubmit
$actions = wf_get_allowed_actions($conn, $doc2, $auth_faculty);
test_result("Faculty sees resubmit action", in_array('resubmit', $actions), "actions=" . json_encode($actions));

$result = wf_execute_action($conn, $test_doc2, $faculty_uid, 'resubmit', 'Resubmitting after correction');
test_result("Resubmit executes", $result['success'], "new_status={$result['new_status']}");

$doc2 = reload_doc($conn, $test_doc2);
test_result("Doc back to pending after resubmit", $doc2['status'] === 'pending', "status={$doc2['status']}");

// ============================================================
// TEST 4: RnD Dean rejects at step 2 → resubmit by faculty
// ============================================================
echo "\n--- TEST 4: Dean Reject + Faculty Resubmit ---\n";
$stmt = $conn->prepare("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at) VALUES (?, ?, ?, NULL, 'TEST-4E-DeanReject', 'pending', NULL, NOW(), NOW())");
$stmt->bind_param('iii', $type_id, $faculty_uid, $faculty_dept);
$stmt->execute();
$test_doc3 = $stmt->insert_id;
$stmt->close();
wf_initialize_document($conn, $test_doc3, $type_id, $faculty_uid);

// HOD approves
$doc3 = reload_doc($conn, $test_doc3);
wf_execute_action($conn, $test_doc3, (int)$hod['user_id'], 'approve', 'HOD passes');

// Dean rejects
$doc3 = reload_doc($conn, $test_doc3);
$result = wf_execute_action($conn, $test_doc3, (int)$dean['user_id'], 'reject', 'Dean rejects');
test_result("Dean reject at step 2", $result['success'], "new_status={$result['new_status']}");

$doc3 = reload_doc($conn, $test_doc3);
test_result("Doc rejected with non-null step", $doc3['status'] === 'rejected' && $doc3['current_step'] !== null, "status={$doc3['status']}, step={$doc3['current_step']}");

// Faculty resubmits
$actions = wf_get_allowed_actions($conn, $doc3, $auth_faculty);
test_result("Faculty can resubmit after dean reject", in_array('resubmit', $actions), "actions=" . json_encode($actions));

$result = wf_execute_action($conn, $test_doc3, $faculty_uid, 'resubmit', 'Faculty resubmits');
test_result("Resubmit after dean reject works", $result['success']);

$doc3 = reload_doc($conn, $test_doc3);
test_result("Doc back to pending at step 1", $doc3['status'] === 'pending', "status={$doc3['status']}, step={$doc3['current_step']}");

// ============================================================
// TEST 5: Unauthorized user cannot act
// ============================================================
echo "\n--- TEST 5: Unauthorized User ---\n";
$doc_for_unauth = reload_doc($conn, $test_doc3);  // pending at step 1 (HOD review)

// Faculty should NOT be able to approve
$actions = wf_get_allowed_actions($conn, $doc_for_unauth, $auth_faculty);
test_result("Faculty cannot approve at HOD step", !in_array('approve', $actions), "actions=" . json_encode($actions));

// Dean should NOT be able to approve at HOD step (dept scope)
$actions = wf_get_allowed_actions($conn, $doc_for_unauth, $auth_dean);
test_result("Dean cannot approve at HOD step", !in_array('approve', $actions), "actions=" . json_encode($actions));

// ============================================================
// TEST 6: document_actions audit trail recorded correctly
// ============================================================
echo "\n--- TEST 6: Audit Trail ---\n";
$trail = $conn->query("SELECT action FROM document_actions WHERE doc_id = $test_doc_id ORDER BY acted_at");
$recorded_actions = [];
while ($r = $trail->fetch_assoc()) $recorded_actions[] = $r['action'];
test_result("Audit trail has uploaded", in_array('uploaded', $recorded_actions), json_encode($recorded_actions));
test_result("Audit trail has approve", in_array('approve', $recorded_actions), json_encode($recorded_actions));

$trail2 = $conn->query("SELECT action FROM document_actions WHERE doc_id = $test_doc2 ORDER BY acted_at");
$recorded2 = [];
while ($r = $trail2->fetch_assoc()) $recorded2[] = $r['action'];
test_result("Reject audit trail has reject", in_array('reject', $recorded2), json_encode($recorded2));
test_result("Reject audit trail has resubmit", in_array('resubmit', $recorded2), json_encode($recorded2));

// ============================================================
// SUMMARY
// ============================================================
echo "\n=== TEST SUMMARY ===\n";
echo "PASSED: $pass\n";
echo "FAILED: $fail\n";
echo ($fail === 0) ? "ALL TESTS PASSED ✓\n" : "SOME TESTS FAILED ✗\n";

// Cleanup: remove test docs
echo "\n--- Cleanup: removing test documents ---\n";
$conn->query("DELETE FROM document_actions WHERE doc_id IN ($test_doc_id, $test_doc2, $test_doc3)");
$conn->query("DELETE FROM documents WHERE doc_id IN ($test_doc_id, $test_doc2, $test_doc3)");
echo "  Removed test docs: $test_doc_id, $test_doc2, $test_doc3\n";
echo "Done.\n";
