<?php
$directory = new RecursiveDirectoryIterator('e:/set/xampp/htdocs/mini/FMS');
$iterator = new RecursiveIteratorIterator($directory);
$filesChanged = 0;

foreach ($iterator as $info) {
    if ($info->isFile() && in_array($info->getExtension(), ['php', 'html'])) {
        $filePath = $info->getPathname();
        
        // Skip this script itself
        if (basename($filePath) == 'replace_fdps.php') continue;

        $content = file_get_contents($filePath);
        if ($content !== false && strpos($content, 'FDPs') !== false) {
            $newContent = str_replace('FDPs', 'FDPS', $content);
            file_put_contents($filePath, $newContent);
            echo "Updated: $filePath\n";
            $filesChanged++;
        }
    }
}

echo "Total files updated: $filesChanged\n";
?>
