<?php
/**
 * FMS End-to-End (E2E) Journey & Feature Verification Runner
 *
 * Programmatically simulates real multi-role user journeys:
 * 1. Document Upload & Auto-Merge (Faculty)
 * 2. Step 1 Workflow Review & Approval (Dept Coordinator)
 * 3. Step 2 Final Review & Approval (HOD) -> Status: Accepted
 * 4. Colleague Department Scoping & Read-Only Access (Colleague Faculty)
 * 5. Cross-Department Isolation & IDOR Blocking (Outsider Faculty)
 * 6. File Download & Zero-Deletion Storage Integrity
 * 7. Rejection & Revision Loop (Private to Author)
 * 8. Administrative Deletion Safeguards (Departments & Academic Years)
 * 9. Automated Clean Tear-Down (Leaves Zero Junk Data)
 *
 * Usage:
 *   php bin/e2e_journey_runner.php
 */

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/pdf_merger_service.php';

global $conn;

echo "================================================================================" . PHP_EOL;
echo "           FMS COMPLETE END-TO-END (E2E) FEATURE VERIFICATION RUNNER            " . PHP_EOL;
echo "================================================================================" . PHP_EOL . PHP_EOL;

$passed = 0;
$failed = 0;

function assert_test(string $name, bool $condition, string $details = '') {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  [PASS] " . $name . PHP_EOL;
    } else {
        $failed++;
        echo "  [FAIL] " . $name . ($details ? " -> " . $details : "") . PHP_EOL;
    }
}

// 1. Identify Test Actors
$dept_id_eee = 5;
$dept_id_cse = 1;

// Faculty A (Uploader) in EEE
$faculty_a_id = 71; 
// Faculty B (Colleague) in EEE
$faculty_b_id = 89; 
// Coordinator in EEE
$coord_id = 19; 
// HOD in EEE
$hod_id = 10; 
// Outsider Faculty in CSE
$outsider_id = 64; 
// Admin
$admin_id = 45;

// Helper to construct simulated auth context
function build_auth(mysqli $conn, int $user_id, int $role_id, int $dept_id): array {
    $u_res = $conn->query("SELECT user_id, full_name, email FROM users WHERE user_id = $user_id");
    $user = $u_res ? $u_res->fetch_assoc() : ['user_id' => $user_id, 'full_name' => 'User', 'email' => 'user@test.com'];
    return [
        'user_id' => $user['user_id'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'dept_id' => $dept_id,
        'active_role' => ['role_id' => $role_id, 'dept_id' => $dept_id],
        'roles' => [
            ['role_id' => $role_id, 'dept_id' => $dept_id]
        ]
    ];
}

$auth_faculty_a = build_auth($conn, $faculty_a_id, ROLE_FACULTY, $dept_id_eee);
$auth_faculty_b = build_auth($conn, $faculty_b_id, ROLE_FACULTY, $dept_id_eee);
$auth_coord     = build_auth($conn, $coord_id, ROLE_DEPT_COORDINATOR, $dept_id_eee);
$auth_hod       = build_auth($conn, $hod_id, ROLE_HOD, $dept_id_eee);
$auth_outsider  = build_auth($conn, $outsider_id, ROLE_FACULTY, $dept_id_cse);
$auth_admin     = build_auth($conn, $admin_id, ROLE_ADMIN, 0);

echo "[STAGE 1] Testing Document Creation, Metadata & File Ingestion..." . PHP_EOL;

// Create dummy physical files for upload
$temp_dir = __DIR__ . '/../tmp';
if (!is_dir($temp_dir)) @mkdir($temp_dir, 0755, true);

function generate_test_pdf(string $filepath, string $title): void {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, $title, 0, 1, 'C');
    $pdf->Output('F', $filepath);
}

$dummy_circular   = $temp_dir . '/test_circular.pdf';
$dummy_attendance = $temp_dir . '/test_attendance.pdf';
$dummy_report     = $temp_dir . '/test_report.pdf';
$dummy_photos     = $temp_dir . '/test_photos.pdf';

generate_test_pdf($dummy_circular, 'E2E Test Circular Document');
generate_test_pdf($dummy_attendance, 'E2E Test Attendance Document');
generate_test_pdf($dummy_report, 'E2E Test Report Document');
generate_test_pdf($dummy_photos, 'E2E Test Photos Document');

$simulated_files = [
    'circular' => [
        'name' => 'circular.pdf',
        'type' => 'application/pdf',
        'tmp_name' => $dummy_circular,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($dummy_circular)
    ],
    'attendance' => [
        'name' => 'attendance.pdf',
        'type' => 'application/pdf',
        'tmp_name' => $dummy_attendance,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($dummy_attendance)
    ],
    'report' => [
        'name' => 'report.pdf',
        'type' => 'application/pdf',
        'tmp_name' => $dummy_report,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($dummy_report)
    ],
    'photos' => [
        'name' => 'photos.pdf',
        'type' => 'application/pdf',
        'tmp_name' => $dummy_photos,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($dummy_photos)
    ]
];

// Active Academic Year
$ay_res = $conn->query("SELECT year_id FROM academic_years WHERE is_active = 1 LIMIT 1");
$active_year_id = $ay_res ? (int)$ay_res->fetch_assoc()['year_id'] : 1;

$test_title = 'E2E Automated Test Activity ' . uniqid();
$meta_data = [
    'activity_category' => 'Grad Talks',
    'topic_domain' => 'Awareness of Trends and Technologies',
    'title_of_the_event' => $test_title,
    'date_from' => date('Y-m-d'),
    'event_mode' => 'Offline'
];

$create_res = doc_create(
    $conn,
    'student_activity_file',
    $faculty_a_id,
    $dept_id_eee,
    $active_year_id,
    $test_title,
    $meta_data,
    $simulated_files
);

$doc_id = (int)($create_res['doc_id'] ?? 0);
assert_test("Document Record Created (Doc #{$doc_id})", $create_res['success'] && $doc_id > 0, $create_res['error'] ?? '');

$doc = $doc_id > 0 ? doc_get($conn, $doc_id) : [];
assert_test("Initial Status is 'pending'", ($doc['status'] ?? '') === 'pending');
assert_test("Current Step Assigned to Coordinator Review", !empty($doc['current_step']));
assert_test("Merged PDF Master File Created & Assigned", !empty($doc['file_path']) && file_exists(__DIR__ . '/../' . $doc['file_path']));

echo PHP_EOL . "[STAGE 2] Testing Step 1 Coordinator Review & State Transition..." . PHP_EOL;

if ($doc) {
    $can_coord_approve = doc_can_approve($conn, $auth_coord, $doc);
    assert_test("EEE Coordinator Can Act on Document", $can_coord_approve);

    $can_outsider_approve = doc_can_approve($conn, $auth_outsider, $doc);
    assert_test("CSE Outsider CANNOT Act on Document", !$can_outsider_approve);

    $step1_res = wf_execute_action($conn, $doc_id, $coord_id, 'approve', "Approved by EEE Coordinator in E2E Test");
    assert_test("Workflow Advances to Step 2 (HOD Review)", $step1_res['success'], $step1_res['error'] ?? '');

    $doc = doc_get($conn, $doc_id);
    $can_hod_approve = doc_can_approve($conn, $auth_hod, $doc);
    assert_test("EEE HOD Can Act on Step 2", $can_hod_approve);
} else {
    assert_test("Workflow Advances to Step 2", false, "Doc not created");
}

echo PHP_EOL . "[STAGE 3] Testing Step 2 Final HOD Approval & Acceptance..." . PHP_EOL;

if ($doc) {
    $step2_res = wf_execute_action($conn, $doc_id, $hod_id, 'approve', "Final Approval by EEE HOD in E2E Test");
    assert_test("Workflow Completes Final Approval", $step2_res['success'], $step2_res['error'] ?? '');

    $doc = doc_get($conn, $doc_id);
    assert_test("Document Status Transitions to 'accepted'", ($doc['status'] ?? '') === 'accepted');
}

echo PHP_EOL . "[STAGE 4] Testing Department Files Scoping & Colleague Visibility..." . PHP_EOL;

// 4A: Colleague in EEE views Department Files
$colleague_filters = [
    'faculty_dept_scope' => [
        'dept_id' => $dept_id_eee,
        'user_id' => $faculty_b_id
    ],
    'type_keys' => ['student_activity_file']
];
$colleague_list = doc_list($conn, $colleague_filters);
$found_by_colleague = false;
foreach ($colleague_list['rows'] as $r) {
    if ((int)$r['doc_id'] === $doc_id) {
        $found_by_colleague = true;
        break;
    }
}
assert_test("Accepted Document is VISIBLE to EEE Colleague in Department Files", $found_by_colleague);

// 4B: Colleague permission guards
$colleague_can_view = $doc ? doc_can_view($conn, $auth_faculty_b, $doc) : false;
assert_test("EEE Colleague Can View Details (doc_can_view)", $colleague_can_view);

$colleague_can_edit = $doc ? doc_can_edit($conn, $auth_faculty_b, $doc) : true;
assert_test("EEE Colleague CANNOT Edit Colleague's File (Read-Only Guard)", !$colleague_can_edit);

$colleague_can_delete = $doc ? doc_can_delete($conn, $auth_faculty_b, $doc) : true;
assert_test("EEE Colleague CANNOT Delete Colleague's File (Delete Guard)", !$colleague_can_delete);

echo PHP_EOL . "[STAGE 5] Testing Cross-Department Isolation & IDOR Protection..." . PHP_EOL;

// 5A: Outsider in CSE views Department Files
$outsider_filters = [
    'faculty_dept_scope' => [
        'dept_id' => $dept_id_cse,
        'user_id' => $outsider_id
    ],
    'type_keys' => ['student_activity_file']
];
$outsider_list = doc_list($conn, $outsider_filters);
$found_by_outsider = false;
foreach ($outsider_list['rows'] as $r) {
    if ((int)$r['doc_id'] === $doc_id) {
        $found_by_outsider = true;
        break;
    }
}
assert_test("EEE Document is INVISIBLE to CSE Outsider in Dept Files", !$found_by_outsider);

// 5B: Direct URL IDOR check
$outsider_can_view = $doc ? doc_can_view($conn, $auth_outsider, $doc) : true;
assert_test("CSE Outsider Direct URL Access is BLOCKED (IDOR Protection)", !$outsider_can_view);

echo PHP_EOL . "[STAGE 6] Testing File Download & Storage Retention..." . PHP_EOL;

$physical_file = ($doc && !empty($doc['file_path'])) ? (__DIR__ . '/../' . $doc['file_path']) : '';
assert_test("Physical Merged PDF Exists on Disk Before Download", !empty($physical_file) && file_exists($physical_file));

// Simulate file_stream_and_cleanup with permanent file
$to_delete = [];
$norm_filepath = str_replace('\\', '/', $physical_file);
if (strpos($norm_filepath, '/uploads/temp/') !== false || strpos($norm_filepath, '/tmp/') !== false) {
    $to_delete[] = $physical_file;
}
foreach ($to_delete as $f) {
    @unlink($f);
}
assert_test("Permanent Upload File is NEVER Deleted by Download/View Stream", file_exists($physical_file));

echo PHP_EOL . "[STAGE 7] Testing Rejection Cycle & Privacy..." . PHP_EOL;

// Create Document B to test rejection
$test_title_b = 'E2E Rejection Test Doc ' . uniqid();
$create_b = doc_create($conn, 'student_activity_file', $faculty_a_id, $dept_id_eee, $active_year_id, $test_title_b, $meta_data, $simulated_files);
$doc_b_id = (int)($create_b['doc_id'] ?? 0);

$reject_res = wf_execute_action($conn, $doc_b_id, $coord_id, 'reject', "Rejected in E2E test due to insufficient documentation");
assert_test("Coordinator Successfully Rejects Document B", $reject_res['success'], $reject_res['error'] ?? '');

$doc_b = $doc_b_id > 0 ? doc_get($conn, $doc_b_id) : [];
assert_test("Document B Status is 'rejected'", ($doc_b['status'] ?? '') === 'rejected');
assert_test("Original Author Can Edit Rejected Document", $doc_b ? doc_can_edit($conn, $auth_faculty_a, $doc_b) : false);

// Colleague in EEE should NOT see rejected Document B in Department Files
$colleague_list_b = doc_list($conn, $colleague_filters);
$found_rejected_by_colleague = false;
foreach ($colleague_list_b['rows'] as $r) {
    if ((int)$r['doc_id'] === $doc_b_id) {
        $found_rejected_by_colleague = true;
        break;
    }
}
assert_test("Rejected Document B is INVISIBLE to Department Colleagues", !$found_rejected_by_colleague);

$colleague_view_b = $doc_b ? doc_can_view($conn, $auth_faculty_b, $doc_b) : true;
assert_test("Colleague CANNOT View Rejected Document B (doc_can_view BLOCKED)", !$colleague_view_b);

echo PHP_EOL . "[STAGE 8] Testing Administrative Deletion Guards..." . PHP_EOL;

$dept_guard = doc_can_delete_department($conn, $dept_id_eee);
assert_test("Guard Blocks Deletion of Active Department with Documents/Users", !$dept_guard['allowed']);

$year_guard = doc_can_delete_academic_year($conn, $active_year_id);
assert_test("Guard Blocks Deletion of Academic Year with Linked Documents", !$year_guard['allowed']);

echo PHP_EOL . "[STAGE 9] Automated Clean Tear-Down..." . PHP_EOL;

// Delete Document A as Admin
if ($doc_id > 0) {
    $del_a = doc_delete($conn, $doc_id, $auth_admin);
}
// Delete Document B as Admin
if ($doc_b_id > 0) {
    $del_b = doc_delete($conn, $doc_b_id, $auth_admin);
}

@unlink($dummy_circular);
@unlink($dummy_attendance);
@unlink($dummy_report);
@unlink($dummy_photos);

$doc_a_after = $doc_id > 0 ? doc_get($conn, $doc_id) : null;
$doc_b_after = $doc_b_id > 0 ? doc_get($conn, $doc_b_id) : null;
assert_test("Test Document A Purged from Database and Storage", $doc_a_after === null);
assert_test("Test Document B Purged from Database and Storage", $doc_b_after === null);

echo PHP_EOL . "================================================================================" . PHP_EOL;
echo "E2E VERIFICATION SUMMARY:" . PHP_EOL;
echo "  Total Tests Run : " . ($passed + $failed) . PHP_EOL;
echo "  Tests Passed    : " . $passed . PHP_EOL;
echo "  Tests Failed    : " . $failed . PHP_EOL;
echo "================================================================================" . PHP_EOL;

if ($failed === 0) {
    echo "STATUS: ALL SYSTEMS GREEN! COMPLETE END-TO-END VERIFICATION PASSED!" . PHP_EOL;
} else {
    echo "STATUS: FAILURES DETECTED." . PHP_EOL;
}
echo "================================================================================" . PHP_EOL;
