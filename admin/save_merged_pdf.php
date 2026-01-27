<?php
session_start();


$uploadDir = "uploads1/merged/";

// Function to delete folder and its contents
function deleteFolder($folderPath) {
    if (!is_dir($folderPath)) {
        return;
    }
    $files = array_diff(scandir($folderPath), ['.', '..']);
    foreach ($files as $file) {
        $filePath = "$folderPath/$file";
        is_dir($filePath) ? deleteFolder($filePath) : unlink($filePath);
    }
    rmdir($folderPath);
}

// Check if the page is refreshed
if (isset($_SESSION['refreshed'])) {
    deleteFolder($uploadDir);
    unset($_SESSION['refreshed']); // Prevent repeated deletion
}

// Set session variable to detect refresh on next load
$_SESSION['refreshed'] = true;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["merged_pdf"])) {
    // Ensure directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = "merged_" . time() . ".pdf";
    $filePath = $uploadDir . $filename;
    $fileUrl = $filePath; // Adjust if needed

    if (move_uploaded_file($_FILES["merged_pdf"]["tmp_name"], $filePath)) {
        echo json_encode(["fileUrl" => $fileUrl]);
    } else {
        echo json_encode(["error" => "Failed to save file."]);
    }
} else {
    echo json_encode(["error" => "Invalid request."]);
}
?>
