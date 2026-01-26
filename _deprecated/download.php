<?php
include("connection.php");
if (isset($_GET['file'])) {
    $file = basename($_GET['file']); // Get the file name and sanitize it
    $filePath = './' . $file; // Path to the file directory

    // Check if file exists
    if (file_exists($filePath)) {
        // Set headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        // Read the file and output its content
        readfile($filePath);
        exit;
    } else {
        echo "File not found!";
    }
} else {
    echo "No file specified!";
}
?>
