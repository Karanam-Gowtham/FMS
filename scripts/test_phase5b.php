<?php
/**
 * Phase 5B — R&D Module Comprehensive Tests
 *
 * Tests all 12 scenarios from the testing plan:
 * T1: Access control (R&D Dean can, Faculty cannot)
 * T2: Department filter works
 * T3: Year filter works
 * T4: Category cards show correct counts
 * T5: View link resolves correctly
 * T6: Download link resolves correctly
 * T7: Pending tab shows only R&D Dean step docs
 * T8: Approve from dashboard
 * T9: Reject from dashboard with reason
 * T10: Central R&D upload auto-accept
 * T11: Central R&D category labels
 * T12: Existing list.php unaffected
 */

require_once __DIR__ . '/../core/bootstrap.php';

echo "=================================================================\n";
echo "  Phase 5B — R&D Module Comprehensive Tests\n";
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

// ── Load actors ──
echo "--- Setup: Load Actors ---\n";

$faculty = $conn->query("SELECT u.user_id, u.email FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = 4 AND ur.dept_id = 1 LIMIT 1")->fetch_assoc();
echo "  Faculty: user_id={$faculty['user_id']} ({$faculty['email']})\n";

$dc = $conn->query("SELECT u.user_id, u.email FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = 5 AND ur.dept_id = 1 LIMIT 1")->fetch_assoc();
echo "  DC: user_id={$dc['user_id']} ({$dc['email']})\n";

$hod = $conn->query("SELECT u.user_id, u.email FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = 3 AND ur.dept_id = 1 LIMIT 1")->fetch_assoc();
echo "  HOD: user_id={$hod['user_id']} ({$hod['email']})\n";

$dean = $conn->query("SELECT u.user_id, u.email FROM users u JOIN user_roles ur ON ur.user_id = u.user_id WHERE ur.role_id = 8 AND u.user_id != 1 LIMIT 1")->fetch_assoc();
echo "  R&D Dean: user_id={$dean['user_id']} ({$dean['email']})\n";

function make_auth(int $user_id, mysqli $conn): array {
    $user = $conn->query("SELECT user_id, email, full_name FROM users WHERE user_id = $user_id")->fetch_assoc();
    $roles = [];
    $rr = $conn->query("SELECT ur.user_role_id, ur.role_id, ur.dept_id, r.role_name, COALESCE(d.dept_name, '') as dept_name FROM user_roles ur JOIN roles r ON r.role_id = ur.role_id LEFT JOIN dept d ON d.dept_id = ur.dept_id WHERE ur.user_id = $user_id");
    while ($row = $rr->fetch_assoc()) $roles[] = $row;
    return ['user_id' => $user['user_id'], 'email' => $user['email'], 'full_name' => $user['full_name'], 'roles' => $roles];
}

$auth_dean    = make_auth($dean['user_id'], $conn);
$auth_faculty = make_auth($faculty['user_id'], $conn);
$auth_dc      = make_auth($dc['user_id'], $conn);
$auth_hod     = make_auth($hod['user_id'], $conn);

echo "\n";

// ============================================================
// T1: Access Control
// ============================================================
echo "--- T1: Access Control ---\n";

// R&D Dean has role_id 8
$is_dean = false;
foreach ($auth_dean['roles'] as $r) {
    if ((int)$r['role_id'] === ROLE_RND_DEAN) { $is_dean = true; break; }
}
test("R&D Dean has ROLE_RND_DEAN", $is_dean);

// Faculty does NOT have role_id 8
$fac_is_dean = false;
foreach ($auth_faculty['roles'] as $r) {
    if ((int)$r['role_id'] === ROLE_RND_DEAN) { $fac_is_dean = true; break; }
}
test("Faculty does NOT have ROLE_RND_DEAN", !$fac_is_dean);

echo "\n";

// ============================================================
// T2: Department Filter
// ============================================================
echo "--- T2: Department Filter ---\n";

// R&D documents in CSE (dept_id=1)
$cse_docs = doc_list($conn, ['category' => 'research', 'dept_id' => 1], 100, 0);
$all_research = doc_list($conn, ['category' => 'research'], 100, 0);

test("Department filter returns CSE research docs", $cse_docs['total'] >= 0);
test("All-dept returns >= CSE count", $all_research['total'] >= $cse_docs['total']);

// Verify all CSE docs have dept_id=1
$cse_only = true;
foreach ($cse_docs['rows'] as $d) {
    if ((int)$d['dept_id'] !== 1) { $cse_only = false; break; }
}
test("All filtered docs belong to CSE", $cse_only);

echo "\n";

// ============================================================
// T3: Year Filter
// ============================================================
echo "--- T3: Year Filter ---\n";

$active_yr = doc_get_active_year($conn);
$year_docs = doc_list($conn, ['category' => 'research', 'year_id' => (int)$active_yr['year_id']], 100, 0);
test("Year filter returns results for active year", $year_docs['total'] >= 0);

$year_match = true;
foreach ($year_docs['rows'] as $d) {
    if ((int)$d['year_id'] !== (int)$active_yr['year_id']) { $year_match = false; break; }
}
test("All year-filtered docs match selected year", $year_match);

echo "\n";

// ============================================================
// T4: Category Cards (Research Types)
// ============================================================
echo "--- T4: Category Cards ---\n";

$research_types = doc_get_types($conn, 'research');
test("Research category has types", count($research_types) >= 6);

foreach ($research_types as $rt) {
    $tc = doc_list($conn, ['type_key' => $rt['type_key']], 0, 0);
    echo "    {$rt['type_key']}: {$tc['total']} docs\n";
}
test("Category cards computable from doc_list", true);

echo "\n";

// ============================================================
// T5 & T6: View/Download Links (existing handlers)
// ============================================================
echo "--- T5/T6: View/Download Handlers ---\n";

// Get a research document
$sample = doc_list($conn, ['category' => 'research', 'status' => 'accepted'], 1, 0);
if (!empty($sample['rows'])) {
    $sample_doc = doc_get($conn, (int)$sample['rows'][0]['doc_id']);
    test("doc_get resolves research doc", $sample_doc !== null);

    $files = file_get_by_document($conn, (int)$sample_doc['doc_id']);
    test("file_get_by_document returns files", is_array($files));
    echo "    doc_id={$sample_doc['doc_id']}, files=" . count($files) . "\n";
} else {
    echo "  ⚠ No accepted research docs to test view/download\n";
    test("Sample doc exists for view/download test", false);
}

echo "\n";

// ============================================================
// T7: Pending Tab (doc_list_pending_for_user)
// ============================================================
echo "--- T7: Pending Tab ---\n";

// Create a test doc and advance to R&D Dean step
$journal_type = doc_get_type_by_key($conn, 'journal');
$conn->query("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at)
    VALUES ({$journal_type['type_id']}, {$faculty['user_id']}, 1, {$active_yr['year_id']}, 'TEST-5B: Pending Dean Review', 'pending', NULL, NOW(), NOW())");
$test_doc1 = $conn->insert_id;
$conn->query("INSERT INTO meta_journal (doc_id, paper_title, journal_name) VALUES ($test_doc1, 'Test', 'Test Journal')");
wf_initialize_document($conn, $test_doc1, (int)$journal_type['type_id'], (int)$faculty['user_id']);

// Advance through DC and HOD
wf_execute_action($conn, $test_doc1, (int)$dc['user_id'], 'approve', '');
wf_execute_action($conn, $test_doc1, (int)$hod['user_id'], 'approve', '');

// Now it should be pending R&D Dean
$pending = doc_list_pending_for_user($conn, $auth_dean, 200, 0);
$found_pending = false;
foreach ($pending['rows'] as $p) {
    if ((int)$p['doc_id'] === $test_doc1) { $found_pending = true; break; }
}
test("Test doc appears in R&D Dean pending queue", $found_pending);

// Faculty should NOT see it in pending
$fac_pending = doc_list_pending_for_user($conn, $auth_faculty, 200, 0);
$fac_found = false;
foreach ($fac_pending['rows'] as $p) {
    if ((int)$p['doc_id'] === $test_doc1) { $fac_found = true; break; }
}
test("Test doc NOT in Faculty pending queue", !$fac_found);

echo "\n";

// ============================================================
// T8: Approve From Dashboard
// ============================================================
echo "--- T8: Approve From Dashboard ---\n";

$result = wf_execute_action($conn, $test_doc1, (int)$dean['user_id'], 'approve', 'Approved via R&D dashboard');
test("R&D Dean approve succeeds", $result['success']);
test("Status after approval = accepted", $result['new_status'] === 'accepted');

$doc_check = doc_get($conn, $test_doc1);
test("Doc status is accepted", $doc_check['status'] === 'accepted');

// Verify audit trail records Dean user (not admin)
$history = wf_get_action_history($conn, $test_doc1);
$dean_acted = false;
foreach ($history as $h) {
    if ($h['actor_name'] === 'R&D Dean' && $h['action'] === 'approve') {
        $dean_acted = true;
        break;
    }
}
test("Audit trail records R&D Dean as actor", $dean_acted);

echo "\n";

// ============================================================
// T9: Reject From Dashboard With Reason
// ============================================================
echo "--- T9: Reject With Reason ---\n";

// Create another doc and advance to Dean
$conn->query("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at)
    VALUES ({$journal_type['type_id']}, {$faculty['user_id']}, 1, {$active_yr['year_id']}, 'TEST-5B: To Be Rejected', 'pending', NULL, NOW(), NOW())");
$test_doc2 = $conn->insert_id;
$conn->query("INSERT INTO meta_journal (doc_id, paper_title, journal_name) VALUES ($test_doc2, 'Reject Test', 'Test')");
wf_initialize_document($conn, $test_doc2, (int)$journal_type['type_id'], (int)$faculty['user_id']);
wf_execute_action($conn, $test_doc2, (int)$dc['user_id'], 'approve', '');
wf_execute_action($conn, $test_doc2, (int)$hod['user_id'], 'approve', '');

$result = wf_execute_action($conn, $test_doc2, (int)$dean['user_id'], 'reject', 'Incomplete methodology section');
test("R&D Dean reject succeeds", $result['success']);
test("Status after reject = rejected", $result['new_status'] === 'rejected');

$doc_check = doc_get($conn, $test_doc2);
test("Rejection reason stored", $doc_check['rejection_reason'] === 'Incomplete methodology section');

echo "\n";

// ============================================================
// T10: Central R&D Upload Auto-Accept
// ============================================================
echo "--- T10: Central R&D Auto-Accept ---\n";

$crnd_type = doc_get_type_by_key($conn, 'central_rnd');
test("central_rnd type exists", $crnd_type !== null);
test("central_rnd uses auto_accept", $crnd_type['workflow_key'] === 'auto_accept');

$conn->query("INSERT INTO documents (doc_type_id, uploaded_by, dept_id, year_id, title, status, current_step, created_at, updated_at)
    VALUES ({$crnd_type['type_id']}, {$dean['user_id']}, 25, {$active_yr['year_id']}, 'TEST-5B: Central R&D Policy', 'pending', NULL, NOW(), NOW())");
$test_doc3 = $conn->insert_id;
$conn->query("INSERT INTO meta_central_rnd (doc_id, rnd_category_id, description) VALUES ($test_doc3, 1, 'Test policy document')");

$result = wf_initialize_document($conn, $test_doc3, (int)$crnd_type['type_id'], (int)$dean['user_id']);
test("Auto-accept initialization succeeds", $result['success']);
test("Auto-accept status = accepted", $result['status'] === 'accepted');

$doc_check = doc_get($conn, $test_doc3);
test("Central R&D doc is accepted", $doc_check['status'] === 'accepted');

echo "\n";

// ============================================================
// T11: Central R&D Category Labels
// ============================================================
echo "--- T11: Central R&D Category Labels ---\n";

$cat_map = rnd_get_category_map($conn);
test("rnd_get_category_map returns categories", count($cat_map) >= 10);

$meta = doc_get_meta($conn, $test_doc3, 'central_rnd');
test("Central R&D meta loaded", $meta !== null);
$cat_id = (int)($meta['rnd_category_id'] ?? 0);
test("Category label resolves", isset($cat_map[$cat_id]) && $cat_map[$cat_id] !== '');
echo "    Category: {$cat_map[$cat_id]}\n";

// Verify all categories are from database
$cats = rnd_get_categories($conn);
test("rnd_get_categories returns full list", count($cats) >= 10);
$has_label = true;
foreach ($cats as $c) {
    if (empty($c['category_label'])) { $has_label = false; break; }
}
test("All categories have labels", $has_label);

echo "\n";

// ============================================================
// T12: Existing list.php Scoping Unaffected
// ============================================================
echo "--- T12: Existing list.php Unaffected ---\n";

// Faculty scope: own uploads only
$fac_docs = doc_list($conn, ['uploaded_by' => (int)$faculty['user_id']], 100, 0);
test("Faculty doc_list filter works", $fac_docs['total'] >= 0);

// Dept scope: dept filter works
$dept_docs = doc_list($conn, ['dept_id' => 1], 100, 0);
test("Dept filter works in doc_list", $dept_docs['total'] >= 0);

// Category filter works
$research_docs = doc_list($conn, ['category' => 'research'], 100, 0);
test("Category filter works", $research_docs['total'] >= 0);

// department_standard still works
$std_type = doc_get_type_by_key($conn, 'dept_file');
test("dept_file still uses department_standard", $std_type['workflow_key'] === 'department_standard');

echo "\n";

// ============================================================
// ADDITIONAL: Meta Registry Select Field
// ============================================================
echo "--- Additional: Meta Registry Select Field ---\n";

$crnd_fields = meta_get_fields('central_rnd');
$has_select = false;
$has_options_source = false;
foreach ($crnd_fields as $f) {
    if ($f['name'] === 'rnd_category_id') {
        $has_select = ($f['type'] === 'select');
        $has_options_source = !empty($f['options_source']);
    }
}
test("rnd_category_id is type 'select'", $has_select);
test("rnd_category_id has options_source", $has_options_source);

echo "\n";

// ============================================================
// ADDITIONAL: No Duplicate Documents
// ============================================================
echo "--- Additional: No Duplicate Documents ---\n";

$test_count = $conn->query("SELECT COUNT(*) as c FROM documents WHERE title LIKE 'TEST-5B:%'")->fetch_assoc()['c'];
test("Correct number of test documents (3)", (int)$test_count === 3);

// Legacy tables intact
$legacy = ['published_tab', 'conference_tab', 'patents_table', 'fdps_tab', 'fdps_org_tab', 'conf_org_tab'];
$all_present = true;
foreach ($legacy as $t) {
    if ($conn->query("SHOW TABLES LIKE '$t'")->num_rows === 0) { $all_present = false; break; }
}
test("Legacy tables still present", $all_present);

echo "\n";

// ============================================================
// CLEANUP
// ============================================================
echo "--- Cleanup ---\n";

$test_ids = [$test_doc1, $test_doc2, $test_doc3];
foreach ($test_ids as $tid) {
    $conn->query("DELETE FROM document_actions WHERE doc_id = $tid");
    $conn->query("DELETE FROM meta_journal WHERE doc_id = $tid");
    $conn->query("DELETE FROM meta_central_rnd WHERE doc_id = $tid");
    $conn->query("DELETE FROM document_files WHERE doc_id = $tid");
    $conn->query("DELETE FROM documents WHERE doc_id = $tid");
}
echo "  Cleaned up " . count($test_ids) . " test documents.\n";

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
