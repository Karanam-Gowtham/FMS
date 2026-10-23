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
            $this->renderError(403, "Access denied. You do not have permission to download accreditation reports.");
        }

        $type = (isset($_GET['type']) && is_string($_GET['type'])) ? trim($_GET['type']) : '';
        $filename = (isset($_GET['file']) && is_string($_GET['file'])) ? trim($_GET['file']) : '';

        // Map type to fixed server-side directory using static whitelist dictionary
        $type_dirs = [
            'nba'  => __DIR__ . '/../../uploads/nba_pdfs',
            'naac' => __DIR__ . '/../../uploads/naac_pdfs',
        ];
        if (!isset($type_dirs[$type])) {
            $this->renderError(400, "Invalid report type specified.");
        }

        // Validate the filename against safe patterns, preventing directory traversal natively
        if (!preg_match('/^(dummy_criterion\d+_\d+|dept_\d+_[a-zA-Z0-9_]+)\.pdf$/', $filename)) {
            $this->renderError(400, "Invalid report filename format.");
        }

        $raw_dir = $type_dirs[$type];
        if (!is_dir($raw_dir)) {
            @mkdir($raw_dir, 0755, true);
        }
        $base_dir = realpath($raw_dir);
        if (!$base_dir) {
            $this->renderError(500, "Report directory configuration error.");
        }

        // Ensure canonical base directory has a trailing separator for secure bounding
        $canonical_base = rtrim($base_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $filepath = $canonical_base . $filename;
        $real_filepath = realpath($filepath);

        // Resolve canonically and ensure the file remains strictly inside the base directory
        // Use stripos for Windows case-insensitive filesystem boundary checking
        if (!$real_filepath || stripos($real_filepath, $canonical_base) !== 0 || !is_file($real_filepath)) {
            $this->renderError(404, "Requested accreditation report file was not found on server.");
        }

        // Safely clear any previously buffered output
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Stream the PDF
        $filename_disp = basename($real_filepath);
        header("X-Content-Type-Options: nosniff");
        header("Content-Type: application/pdf");
        header("Content-Disposition: inline; filename=\"" . $filename_disp . "\"; filename*=UTF-8''" . rawurlencode($filename_disp));
        $fsize = @filesize($real_filepath);
        if ($fsize !== false && $fsize >= 0) {
            header("Content-Length: " . $fsize);
        }
        header("Cache-Control: private, must-revalidate, max-age=0");
        
        readfile($real_filepath);
        exit;
    }

    private function renderError(int $statusCode, string $message): void {
        http_response_code($statusCode);
        while (ob_get_level()) {
            ob_end_clean();
        }
        $app_title = "File Management System (FMS)";
        $back_url = BASE_URL . "/public/index.php?route=dashboard";
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Download Error &mdash; <?= htmlspecialchars($app_title) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        body { background: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; }
        .error-card { background: white; max-width: 520px; width: 100%; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; overflow: hidden; text-align: center; }
        .error-header { background: #fee2e2; padding: 2rem; color: #dc2626; }
        .error-header i { font-size: 3rem; margin-bottom: 0.5rem; }
        .error-body { padding: 2rem; color: #334155; }
        .error-body h2 { margin: 0 0 1rem; color: #1e293b; font-size: 1.4rem; font-weight: 700; }
        .error-body p { margin: 0 0 1.5rem; color: #64748b; line-height: 1.5; }
        .btn-return { display: inline-flex; align-items: center; gap: 8px; background: #003366; color: white; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 6px; font-weight: 600; font-size: 0.95rem; }
        .btn-return:hover { background: #1e3a8a; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-header">
            <i class="fas fa-exclamation-triangle"></i>
            <div style="font-weight: 700; font-size: 1.1rem; margin-top: 4px;">HTTP <?= $statusCode ?></div>
        </div>
        <div class="error-body">
            <h2>Accreditation Report Unavailable</h2>
            <p><?= htmlspecialchars($message) ?></p>
            <a href="<?= $back_url ?>" class="btn-return"><i class="fas fa-arrow-left"></i> Return to Dashboard</a>
        </div>
    </div>
</body>
</html>
        <?php
        exit;
    }
}
