<?php
$base_dir = __DIR__;
$directories = ['modules/faculty', 'modules/dept_coordinator', 'modules/central', 'HOD', 'admin'];

foreach ($directories as $dir) {
    $path = $base_dir . '/' . $dir;
    if (!is_dir($path)) continue;

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $filepath = $file->getPathname();
            $relative_path = str_replace($base_dir . '\\', '', $filepath);
            $relative_path = str_replace('\\', '/', $relative_path);

            $content = file_get_contents($filepath);
            
            // Skip if it already has the breadcrumb block we're about to add
            if (strpos($content, '<nav class="navbar"') !== false && strpos($content, 'margin-top:') !== false) {
                continue;
            }

            // Skip if no header include
            if (strpos($content, 'header.php') === false) {
                continue;
            }

            // Get the old content from commit eec13dd
            $cmd = "git show eec13dd:" . escapeshellarg($relative_path);
            $old_content = shell_exec($cmd);

            if ($old_content && preg_match('/<nav class="navbar">.*?<\/nav>/s', $old_content, $matches)) {
                $breadcrumb_html = $matches[0];
                
                // Add styling so it sits under the new fixed header (which is 70px)
                $breadcrumb_html = str_replace('<nav class="navbar">', '<nav class="navbar" style="position: relative; margin-top: 10px; z-index: 10; background: transparent; border: none;">', $breadcrumb_html);
                
                // Also fix any home-icon classes that might clash with header.php home-icon which is white.
                // We'll leave them for now or change them if needed, but the original ones had their own CSS.
                // Actually the original CSS for nav-container had `margin-top: 80px`. But now it's inline.
                $breadcrumb_html = preg_replace('/class="nav-container"/', 'class="nav-container" style="margin-top: 0; background: transparent;"', $breadcrumb_html);
                
                // Inject after include header.php
                $pattern = '/(<\?php\s+(include|require)(_once)?\s*\(\s*["\'].*?header\.php["\']\s*\)\s*;?\s*\?>)/i';
                $new_content = preg_replace($pattern, "$1\n" . $breadcrumb_html . "\n", $content);
                
                if ($new_content !== $content) {
                    file_put_contents($filepath, $new_content);
                    echo "Restored breadcrumb in $relative_path\n";
                }
            }
        }
    }
}
?>
