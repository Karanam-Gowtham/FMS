<?php
include("../includes/connection.php");
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Security: require authentication before serving any file
if (!isset($_SESSION['admin']) && !isset($_SESSION['h_username']) && !isset($_SESSION['username'])) {
    http_response_code(403);
    die("Access denied. Please log in to view files.");
}

if (!isset($_GET['id'])) {
    die("Invalid file ID.");
}

$fileId = intval($_GET['id']);

// Fetch the file details
$sql = "SELECT file_path FROM a_files WHERE id = ?";
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
