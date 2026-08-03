<?php
$base_dir = __DIR__;
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_dir));
$count = 0;
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filepath = $file->getPathname();
        
        // Exclude connection.php and session.php and fix scripts
        if (strpos($filepath, 'connection.php') !== false) continue;
        if (strpos($filepath, 'session.php') !== false) continue;
        if (strpos($filepath, 'fix_') !== false) continue;

        $content = file_get_contents($filepath);
        
        // Normalize: remove existing wrappers first to avoid double wrapping
        $new_content = preg_replace('/if\s*\(\s*session_status\(\)\s*===\s*PHP_SESSION_NONE\s*\)\s*\{\s*session_start\(\);\s*\}/', 'session_start();', $content);
        
        // Replace all bare session_start() calls with the wrapper
        $new_content = str_replace('session_start();', 'if (session_status() === PHP_SESSION_NONE) { session_start(); }', $new_content);
        
        if ($new_content !== $content) {
            file_put_contents($filepath, $new_content);
            $count++;
            echo "Fixed session_start in: " . $file->getFilename() . "\n";
        }
    }
}
echo "Total files fixed: $count\n";
?>
