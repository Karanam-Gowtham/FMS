<?php
/**
 * Bug Hunter: Deep CSRF, Form, View Variable, and Controller Wiring Scanner
 */

require_once __DIR__ . '/../core/bootstrap.php';

echo "================================================================================" . PHP_EOL;
echo "           DEEP CODE HUNTER: FORMS, CSRF, FETCH & VIEW INTEGRITY                " . PHP_EOL;
echo "================================================================================" . PHP_EOL . PHP_EOL;

$findings = [];

// 1. SCAN ALL HTML FORMS IN src/Views/
echo "[1] Scanning all POST <form> tags for CSRF tokens..." . PHP_EOL;
$view_dir = realpath(__DIR__ . '/../src/Views');
$view_it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($view_dir));

foreach ($view_it as $f) {
    if (!$f->isFile() || $f->getExtension() !== 'php') continue;
    $vpath = $f->getPathname();
    $vrel = str_replace(realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR, '', $vpath);
    $content = file_get_contents($vpath);

    // Find all <form ... method="POST" ...>
    preg_match_all('/<form\b[^>]*method=[\'"]POST[\'"][^>]*>(.*?)<\/form>/is', $content, $forms, PREG_SET_ORDER);
    foreach ($forms as $form) {
        $form_html = $form[0];
        // Check if csrf_token input exists
        if (!preg_match('/name=[\'"]csrf_token[\'"]/i', $form_html) && !preg_match('/csrf_field\(\)/i', $form_html)) {
            $findings[] = [
                'type' => 'SECURITY / CSRF',
                'severity' => 'HIGH',
                'file' => $vrel,
                'msg' => "POST <form> is missing CSRF token input field!",
                'snippet' => substr(trim(preg_replace('/\s+/', ' ', $form_html)), 0, 100)
            ];
        }
    }
}

// 2. SCAN ALL fetch() POST CALLS IN src/Views/ and assets/
echo "[2] Scanning all JavaScript fetch(..., { method: 'POST' }) for CSRF tokens..." . PHP_EOL;
$scan_dirs = [realpath(__DIR__ . '/../src/Views'), realpath(__DIR__ . '/../assets')];
foreach ($scan_dirs as $sdir) {
    if (!$sdir || !is_dir($sdir)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sdir));
    foreach ($it as $f) {
        if (!$f->isFile() || !in_array($f->getExtension(), ['php', 'js'])) continue;
        $fpath = $f->getPathname();
        $frel = str_replace(realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR, '', $fpath);
        $content = file_get_contents($fpath);

        // Find fetch calls
        preg_match_all('/fetch\s*\((.*?)\)\s*\.then/is', $content, $fetches, PREG_SET_ORDER);
        preg_match_all('/(?:await\s+)?fetch\s*\(\s*([^,]+),\s*\{([^}]+)\}/is', $content, $fetches2, PREG_SET_ORDER);

        foreach ($fetches2 as $fetch) {
            $url = trim($fetch[1]);
            $options = $fetch[2];
            if (stripos($options, "'POST'") !== false || stripos($options, '"POST"') !== false) {
                // If it's POST, check if csrf_token is present in headers or body
                if (stripos($options, 'csrf') === false && stripos($content, 'csrf_token') === false && stripos($content, 'csrfToken') === false) {
                    $findings[] = [
                        'type' => 'API / CSRF',
                        'severity' => 'HIGH',
                        'file' => $frel,
                        'msg' => "fetch() POST call to $url might be missing CSRF protection",
                        'snippet' => substr(trim(preg_replace('/\s+/', ' ', $options)), 0, 100)
                    ];
                }
            }
        }
    }
}

// 3. SCAN CONTROLLER ACTIONS FOR MISSING require_login()
echo "[3] Scanning Controller Actions for Authentication Guards..." . PHP_EOL;
$ctrl_dir = realpath(__DIR__ . '/../src/Controllers');
$ctrl_it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($ctrl_dir));

foreach ($ctrl_it as $f) {
    if (!$f->isFile() || $f->getExtension() !== 'php') continue;
    $cpath = $f->getPathname();
    $crel = str_replace(realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR, '', $cpath);
    $cname = pathinfo($cpath, PATHINFO_FILENAME);

    // Skip PublicController and AuthController (which naturally have public endpoints)
    if (in_array($cname, ['PublicController', 'AuthController', 'ReportDownloadController'])) {
        continue;
    }

    $code = file_get_contents($cpath);
    // Find public functions
    preg_match_all('/public\s+function\s+([a-zA-Z0-9_]+)\s*\([^)]*\)\s*\{([^}]+(?:\s*\{[^}]*\})*)/', $code, $methods, PREG_SET_ORDER);
    foreach ($methods as $m) {
        $mname = $m[1];
        $mbody = $m[2];

        // Skip constructors or abstract methods
        if ($mname === '__construct') continue;

        // Check if method calls require_login() or calls an abstract method like processSave, processUploadPdf
        if (stripos($mbody, 'require_login') === false && stripos($mbody, 'processSave') === false && stripos($mbody, 'processUploadPdf') === false && stripos($mbody, 'processGeneratePdf') === false && stripos($mbody, 'authorizeDepartmentAction') === false) {
            $findings[] = [
                'type' => 'SECURITY / AUTH',
                'severity' => 'HIGH',
                'file' => $crel,
                'msg' => "Method {$cname}::{$mname}() appears to be missing require_login() guard!",
                'snippet' => substr(trim(preg_replace('/\s+/', ' ', $mbody)), 0, 80)
            ];
        }
    }
}

echo PHP_EOL . "================================================================================" . PHP_EOL;
echo "HUNTER RESULTS (" . count($findings) . " FINDINGS):" . PHP_EOL;
echo "================================================================================" . PHP_EOL;

foreach ($findings as $idx => $f) {
    $num = $idx + 1;
    echo "[$num] [{$f['severity']}] [{$f['type']}] {$f['msg']}" . PHP_EOL;
    echo "     File: {$f['file']}" . PHP_EOL;
    if (!empty($f['snippet'])) {
        echo "     Code: {$f['snippet']}" . PHP_EOL;
    }
    echo PHP_EOL;
}
