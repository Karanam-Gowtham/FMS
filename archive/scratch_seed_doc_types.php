<?php
require_once __DIR__ . '/core/bootstrap.php';

$types = [
    'journal' => 'Journal Paper',
    'conference' => 'Conference Paper',
    'patent' => 'Patent',
    'fdp_attended' => 'FDP Attended',
    'fdp_organised' => 'FDP Organised',
    'conf_organised' => 'Conference Organised',
    'criteria_file' => 'Criteria File',
    'dept_file' => 'Department File',
    'central_file' => 'Central File',
    'scholarship' => 'Scholarship',
    'placement' => 'Placement',
    'higher_ed' => 'Higher Education',
    'exam_qual' => 'Exam Qualification',
    'award' => 'Award',
    'student_event' => 'Student Event',
    'student_body' => 'Student Body',
    'student_journal' => 'Student Journal',
    'student_conference' => 'Student Conference'
];

foreach ($types as $code => $label) {
    // We'll use workflow_id 1 (Department Review) for everything for now.
    $workflow_id = 1;
    $stmt = $conn->prepare("INSERT IGNORE INTO document_types (type_code, label, workflow_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $code, $label, $workflow_id);
    $stmt->execute();
    echo "Inserted/Ensured $code\n";
}
