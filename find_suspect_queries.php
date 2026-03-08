<?php
$dir = new RecursiveDirectoryIterator('E:/set/xampp/htdocs/mini/FMS');
$it = new RecursiveIteratorIterator($dir);
foreach ($it as $file) {
    if ($file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (preg_match_all('/SELECT\s+\*\s+FROM\s+(fdps_tab|fdps_org_tab|published_tab|conference_tab|patents_table|s_events|dept_files).*?;/is', $content, $matches)) {
            foreach ($matches[0] as $query) {
                if (stripos($query, 'status') === false && stripos($file->getFilename(), 'debug') === false) {
                    echo "File: " . $file->getPathname() . "\n";
                    echo "Query: " . trim($query) . "\n\n";
                }
            }
        }
    }
}
