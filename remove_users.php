<?php
require 'includes/connection.php';

$users_to_remove = [
    'cse@gmail.com'
];

echo "Starting removal process...\n";

// Helper function to safely delete files
function deleteFile($filePath) {
    if (!empty($filePath)) {
        // Strip out base URLs or relative dots if present
        $cleanPath = str_replace(['../', './', 'http://localhost/'], '', $filePath);
        $absolutePath = __DIR__ . DIRECTORY_SEPARATOR . ltrim($cleanPath, '/\\');
        if (file_exists($absolutePath) && is_file($absolutePath)) {
            unlink($absolutePath);
            echo " - Deleted file: $absolutePath\n";
        }
    }
}

foreach ($users_to_remove as $identifier) {
    echo "\nProcessing: $identifier\n";
    
    // Find the user
    $stmt = $conn->prepare("SELECT user_id, email, full_name FROM users WHERE email = ? OR full_name = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows === 0) {
        echo " -> User not found in 'users' table, checking legacy tables only.\n";
        $email = $identifier;
        $username = $identifier;
    } else {
        $user = $res->fetch_assoc();
        $user_id = $user['user_id'];
        $email = $user['email'];
        $username = $user['full_name'];
        
        echo " -> Found user_id: $user_id, email: $email\n";
        
        // Remove from user_roles
        $conn->query("DELETE FROM user_roles WHERE user_id = $user_id");
        echo " -> Removed from user_roles\n";
        
        // Remove modern Documents
        $doc_res = $conn->query("SELECT file_path FROM Documents WHERE uploaded_by = $user_id");
        while ($doc = $doc_res->fetch_assoc()) {
            deleteFile($doc['file_path']);
        }
        $conn->query("DELETE FROM Documents WHERE uploaded_by = $user_id");
        echo " -> Removed from Documents\n";
        
        // Finally remove from users
        $conn->query("DELETE FROM users WHERE user_id = $user_id");
        echo " -> Removed from users table\n";
    }
    
    $stmt->close();
    
    // Remove from legacy tables (using email or username)
    $legacy_queries = [
        ['table' => 'patents_table', 'user_col' => 'Username', 'file_col' => 'patent_file'],
        ['table' => 'published_tab', 'user_col' => 'username', 'file_col' => 'paper_file'],
        ['table' => 'conference_tab', 'user_col' => 'username', 'file_col' => 'paper_file_path'],
        ['table' => 'fdps_tab', 'user_col' => 'username', 'file_col' => 'certificate'],
        ['table' => 'conf_org_tab', 'user_col' => 'username', 'file_col' => 'brochure'],
        ['table' => 'fdps_org_tab', 'user_col' => 'username', 'file_col' => 'merged_file'],
        ['table' => 'dept_files', 'user_col' => 'username', 'file_col' => 'file_path']
    ];
    
    foreach ($legacy_queries as $lq) {
        $tbl = $lq['table'];
        $ucol = $lq['user_col'];
        $fcol = $lq['file_col'];
        
        $lst = $conn->prepare("SELECT $fcol FROM $tbl WHERE $ucol = ? OR $ucol = ?");
        $lst->bind_param("ss", $email, $username);
        $lst->execute();
        $lres = $lst->get_result();
        $count = 0;
        while ($lrow = $lres->fetch_assoc()) {
            deleteFile($lrow[$fcol]);
            $count++;
        }
        $lst->close();
        
        if ($count > 0) {
            $del = $conn->prepare("DELETE FROM $tbl WHERE $ucol = ? OR $ucol = ?");
            $del->bind_param("ss", $email, $username);
            $del->execute();
            $del->close();
            echo " -> Removed $count records from legacy table $tbl\n";
        }
    }
    
    // Cleanup from legacy auth tables
    $conn->query("DELETE FROM reg_tab WHERE userid = '$username' OR email = '$email'");
    $conn->query("DELETE FROM reg_hod WHERE userid = '$username'");
    $conn->query("DELETE FROM reg_dept_cord WHERE userid = '$username'");
    $conn->query("DELETE FROM reg_central_cord WHERE userid = '$username'");
}

$conn->close();
echo "\nDone!\n";
?>
