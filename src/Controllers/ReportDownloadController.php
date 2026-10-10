<?php
namespace App\Controllers;

class ReportDownloadController {
    public function download() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        
        require_login();
        
        $active_role_id = auth_active_role_id();
        
        // Fail closed if active role is missing, invalid, or belongs to a student.
        // Also verify the active role legitimately belongs to the user's assigned roles.
        if (!$active_role_id || $active_role_id === ROLE_STUDENT || !auth_has_role($active_role_id)) {
            http_response_code(403);
            die("Access denied. You do not have permission to download accreditation reports.");
        }

        $type = $_GET['type'] ?? '';
        $filename = $_GET['file'] ?? '';

        // Accept only 'nba' or 'naac'
        if (!in_array($type, ['nba', 'naac'], true)) {
            http_response_code(400);
            die("Invalid report type.");
        }

        // Validate the filename against the generated pattern, preventing directory traversal natively
        if (!preg_match('/^dummy_criterion\d+_\d+\.pdf$/', $filename)) {
            http_response_code(400);
            die("Invalid filename format.");
        }

        // Map type to fixed server-side directory
        $base_dir = realpath(__DIR__ . '/../../uploads/' . $type . '_pdfs');
        if (!$base_dir) {
            http_response_code(500);
            die("Report directory configuration error.");
        }

        // Ensure canonical base directory has a trailing separator for secure bounding
        $canonical_base = rtrim($base_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $filepath = $canonical_base . $filename;
        $real_filepath = realpath($filepath);

        // Resolve canonically and ensure the file remains strictly inside the base directory
        // Use stripos for Windows case-insensitive filesystem boundary checking
        if (!$real_filepath || stripos($real_filepath, $canonical_base) !== 0 || !is_file($real_filepath)) {
            http_response_code(404);
            die("Report file not found.");
        }

        // Safely clear any previously buffered output
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Stream the PDF
        header("Content-Type: application/pdf");
        header("Content-Disposition: inline; filename=\"" . basename($real_filepath) . "\"");
        header("Content-Length: " . filesize($real_filepath));
        header("Cache-Control: private, must-revalidate, max-age=0");
        
        readfile($real_filepath);
        exit;
    }
}
