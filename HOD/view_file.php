<?php
include("../includes/connection.php");


// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    die("You need to log in to view files.");
}

// Ensure there's a file ID present
if (!isset($_GET['file_path'])) {
    die("Invalid file path.");
}

$filePath = urldecode($_GET['file_path']); // Decode the file path

// Check if the file exists
if (file_exists($filePath)) {
    // Serve the file for viewing
    $mimeType = mime_content_type($filePath); // Determine file type

    // Set appropriate headers
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: inline; filename="' . basename($filePath) . '"');

    // Output the file content
    readfile($filePath);
    exit;
} else {
    echo "File does not exist.";
}

$conn->close();
?>
