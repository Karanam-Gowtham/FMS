<?php
/**
 * API: Save NBA Criterion 2
 *
 * Handles POST requests to save Criterion 2 data including:
 * - JSON payload (PDF file paths for 2.1, 2.2, 2.3)
 */
require_once __DIR__ . '/../core/bootstrap.php';

header('Content-Type: application/json');

try {
    require_login();
    $auth = auth_context();
    $active_role = auth_active_role();

    $dept_id = (int)$active_role['dept_id'];
    if ($dept_id <= 0) {
        throw new Exception("You must have a department assigned to save NBA data.");
    }

    $year = trim($_POST['year'] ?? '');
    if (empty($year)) {
        throw new Exception("Academic year is required.");
    }

    $json_raw = $_POST['json_data'] ?? '{}';
    $payload = json_decode($json_raw, true) ?: [];

    $conn = db_connect();

    // --- DATABASE UPSERT ---
    $conn->begin_transaction();

    // 1. Check/Create Submission
    $sub_stmt = $conn->prepare("SELECT submission_id FROM nba_submissions WHERE dept_id = ? AND academic_year = ?");
    $sub_stmt->bind_param("is", $dept_id, $year);
    $sub_stmt->execute();
    $sub_res = $sub_stmt->get_result();
    
    if ($sub_res->num_rows > 0) {
        $sub_id = $sub_res->fetch_assoc()['submission_id'];
    } else {
        $sub_insert = $conn->prepare("INSERT INTO nba_submissions (dept_id, academic_year, status) VALUES (?, ?, 'Draft')");
        $sub_insert->bind_param("is", $dept_id, $year);
        $sub_insert->execute();
        $sub_id = $conn->insert_id;
        $sub_insert->close();
    }
    $sub_stmt->close();

    // 2. Check/Create Criteria Data (JSON)
    $final_json = json_encode($payload);
    
    // Using criterion_number = 2
    $crit_stmt = $conn->prepare("SELECT id FROM nba_criteria_data WHERE submission_id = ? AND criterion_number = 2");
    $crit_stmt->bind_param("i", $sub_id);
    $crit_stmt->execute();
    
    if ($crit_stmt->get_result()->num_rows > 0) {
        $update = $conn->prepare("UPDATE nba_criteria_data SET data_json = ? WHERE submission_id = ? AND criterion_number = 2");
        $update->bind_param("si", $final_json, $sub_id);
        $update->execute();
        $update->close();
    } else {
        $insert = $conn->prepare("INSERT INTO nba_criteria_data (submission_id, criterion_number, data_json) VALUES (?, 2, ?)");
        $insert->bind_param("is", $sub_id, $final_json);
        $insert->execute();
        $insert->close();
    }
    $crit_stmt->close();

    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Criterion 2 data saved successfully.',
        'debug_payload_size' => strlen($final_json)
    ]);

} catch (Exception $e) {
    if (isset($conn) && $conn->ping()) {
        $conn->rollback();
    }
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
