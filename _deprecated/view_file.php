<?php
include("connection.php");
session_start();

if (!isset($_SESSION['username'])) {
    die("You need to log in to view files.");
}

if (!isset($_GET['id'])) {
    die("Invalid file ID.");
}

$fileId = intval($_GET['id']);

// Fetch the file details
$sql = "SELECT file_path FROM files WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $fileId);
$stmt->execute();
$result = $stmt->get_result();
$file = $result->fetch_assoc();

if ($file) {
    $filePath = $file['file_path'];

    if (file_exists($filePath)) {
        // Serve the file
        $mimeType = mime_content_type($filePath);
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
        readfile($filePath);
        exit;
    } else {
        echo "File does not exist.";
    }
} else {
    echo "Invalid file ID.";
}

$conn->close();
?>
