<?php
require_once __DIR__ . '/core/bootstrap.php';
require_once __DIR__ . '/core/document_service.php';

// Simulate an upload for 'fdp_attended'
$meta_data = [
    'mode' => 'Online',
    'date_from' => '2023-01-01',
    'date_to' => '2023-01-05',
    'organised_by' => 'Google',
    'location' => 'Virtual'
];

$files = [
    'certificate' => [
        'name' => 'test_cert.pdf',
        'type' => 'application/pdf',
        'tmp_name' => __DIR__ . '/scratch/test_cert.pdf',
        'error' => UPLOAD_ERR_OK,
        'size' => 1024
    ]
];

// Create dummy file with PDF signature
if (!is_dir(__DIR__ . '/scratch')) mkdir(__DIR__ . '/scratch', 0777, true);
file_put_contents(__DIR__ . '/scratch/test_cert.pdf', "%PDF-1.4\n dummy content");

$result = doc_create($conn, 'fdp_attended', 1, 1, 1, 'My Test FDP', $meta_data, $files);

print_r($result);

if ($result['success']) {
    $doc_id = $result['doc_id'];
    
    // Check documents table
    $res1 = $conn->query("SELECT * FROM documents WHERE doc_id = $doc_id");
    echo "--- documents ---\n";
    print_r($res1->fetch_assoc());
    
    // Check metadata
    $res2 = $conn->query("SELECT * FROM meta_fdp_attended WHERE doc_id = $doc_id");
    echo "--- meta_fdp_attended ---\n";
    print_r($res2->fetch_assoc());
    
    // Check files
    $res3 = $conn->query("SELECT * FROM document_files WHERE doc_id = $doc_id");
    echo "--- document_files ---\n";
    while($row = $res3->fetch_assoc()) print_r($row);
    
    // Check legacy dual-write
    $res4 = $conn->query("SELECT * FROM fdps_tab ORDER BY id DESC LIMIT 1");
    echo "--- legacy fdps_tab ---\n";
    print_r($res4->fetch_assoc());
}
