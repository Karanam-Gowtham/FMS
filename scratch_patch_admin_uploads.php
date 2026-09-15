<?php
$files = [
    'e:\set\xampp\htdocs\mini\FMS\admin\my_uploads_cri.php',
    'e:\set\xampp\htdocs\mini\FMS\admin\my_uploads_cent.php',
    'e:\set\xampp\htdocs\mini\FMS\admin\my_uploads.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);

    // 1. Add helpers.php include if not exists
    if (strpos($content, 'helpers.php') === false) {
        $content = preg_replace(
            '/include\("\.\.\/\.\.\/includes\/connection\.php"\);/s',
            "include(\"../../includes/connection.php\");\ninclude_once __DIR__ . '/../../includes/helpers.php';",
            $content
        );
        $content = preg_replace(
            '/include\("\.\.\/includes\/connection\.php"\);/s',
            "include(\"../includes/connection.php\");\ninclude_once __DIR__ . '/../includes/helpers.php';",
            $content
        );
    }

    // 2. Fix the single file download
    $content = preg_replace(
        '/\$filePath = \$file\[\'file_path\'\];\s*\$fileName = basename\(\$filePath\);/s',
        "\$filePath = fms_resolve_file_path(\$file['file_path']);\n                    \$fileName = basename(\$filePath);",
        $content
    );

    // 3. Fix the zip file download
    $content = preg_replace(
        '/while \(\$file = \$result->fetch_assoc\(\)\) \{\s*\$filePath = \$file\[\'file_path\'\];/s',
        "while (\$file = \$result->fetch_assoc()) {\n                        \$filePath = fms_resolve_file_path(\$file['file_path']);",
        $content
    );

    file_put_contents($file, $content);
    echo "Patched $file\n";
}
