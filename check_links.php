<?php
$base_dir = __DIR__;

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_dir));
$broken_links = [];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && $file->getFilename() !== 'check_links.php') {
        $filepath = $file->getPathname();
        $content = file_get_contents($filepath);
        
        preg_match_all('/<a\s+[^>]*href=["\']([^"\']+)["\']/i', $content, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $href) {
                if (empty($href) || strpos($href, '#') === 0 || preg_match('/^(mailto|javascript|http|https):/i', $href)) {
                    continue;
                }
                
                if (preg_match('/^<\?php.*\?>$/i', trim($href))) {
                    continue;
                }
                
                $path_only = explode('?', $href)[0];
                
                if (strpos($path_only, '<?php echo $base_url; ?>') !== false) {
                    $path_only = str_replace('<?php echo $base_url; ?>', '', $path_only);
                    $target_file = $base_dir . '/' . ltrim($path_only, '/');
                } else if (strpos($path_only, '<?php echo BASE_URL; ?>') !== false) {
                    $path_only = str_replace('<?php echo BASE_URL; ?>', '', $path_only);
                    $target_file = $base_dir . '/' . ltrim($path_only, '/');
                } else if (strpos($path_only, '<?php') !== false) {
                    continue;
                } else {
                    // Check if it starts with /
                    if (strpos($path_only, '/') === 0) {
                        // Assuming Document Root mapping
                        // In XAMPP, usually /mini/FMS is the project root. We can skip checking absolute URLs to be safe,
                        // or try to map them if they start with /mini/FMS.
                        if (strpos($path_only, '/mini/FMS') === 0) {
                            $path_only = substr($path_only, strlen('/mini/FMS'));
                            $target_file = $base_dir . '/' . ltrim($path_only, '/');
                        } else {
                            continue; // Unmapped absolute path
                        }
                    } else {
                        $target_file = dirname($filepath) . '/' . $path_only;
                    }
                }
                
                $resolved = realpath($target_file);
                if ($resolved === false || (!is_file($resolved) && !is_dir($resolved))) {
                    // Ignore if target file is empty (e.g. href="?param=1")
                    if (trim($path_only) === '') continue;
                    
                    $broken_links[] = [
                        'file' => str_replace($base_dir . DIRECTORY_SEPARATOR, '', $filepath),
                        'href' => $href
                    ];
                }
            }
        }
    }
}

$unique_broken = [];
foreach ($broken_links as $b) {
    $key = $b['file'] . '|' . $b['href'];
    $unique_broken[$key] = $b;
}

foreach ($unique_broken as $broken) {
    echo "BROKEN LINK in " . $broken['file'] . "\n    Target: " . $broken['href'] . "\n\n";
}
if (empty($unique_broken)) {
    echo "No broken links found.\n";
}
?>
