<?php
$base_dir = __DIR__;

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_dir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && $file->getFilename() !== 'fix_includes.php') {
        $filepath = $file->getPathname();
        $content = file_get_contents($filepath);
        $changed = false;
        
        // Calculate relative depth
        $rel_path = str_replace($base_dir . DIRECTORY_SEPARATOR, '', $filepath);
        $depth = substr_count($rel_path, DIRECTORY_SEPARATOR);
        
        $prefix = "";
        if ($depth == 0) {
            $prefix = "includes/";
        } else {
            $prefix = str_repeat("../", $depth) . "includes/";
        }
        
        // Replace connection.php
        if (preg_match('/(include|require)(_once)?\s*\(\s*["\']connection\.php["\']\s*\)\s*;?/', $content)) {
            $content = preg_replace('/(include|require)(_once)?\s*\(\s*["\']connection\.php["\']\s*\)\s*;?/', "$1$2(\"" . $prefix . "connection.php\");", $content);
            $changed = true;
        }

        // Replace header.php
        if (preg_match('/(include|require)(_once)?\s*\(\s*["\'](\.\/)?header\.php["\']\s*\)\s*;?/', $content)) {
            $content = preg_replace('/(include|require)(_once)?\s*\(\s*["\'](\.\/)?header\.php["\']\s*\)\s*;?/', "$1$2(\"" . $prefix . "header.php\");", $content);
            $changed = true;
        }

        if ($changed) {
            file_put_contents($filepath, $content);
            echo "Fixed: $rel_path\n";
        }
    }
}
?>
