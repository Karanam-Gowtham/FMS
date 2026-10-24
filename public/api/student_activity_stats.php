<?php
declare(strict_types=1);

require_once __DIR__ . '/../../core/bootstrap.php';

header('Content-Type: application/json');

try {
    $stats = \App\Controllers\StudentActivityDashboardController::calculateStats($_GET ?? []);
    echo json_encode([
        'status' => 'success',
        'data'   => $stats
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to load student activity statistics.'
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
}
exit();
