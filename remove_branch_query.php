<?php

$dir = __DIR__ . '/modules/faculty/';
$files = [
    'patents.php',
    'published.php',
    'fdps.php',
    'fdps_org.php',
    'conf_org.php',
    'conference.php'
];

$block_regex = '/\s*\$branch_query\s*=\s*"SELECT dept FROM reg_tab WHERE userid = \'\$user\'";\s*\$branch_result\s*=\s*\$conn->query\(\$branch_query\);\s*if\s*\(\$branch_result\s*&&\s*\$branch_result->num_rows\s*>\s*0\)\s*\{\s*\$branch_row\s*=\s*\$branch_result->fetch_assoc\(\);\s*\$branch\s*=\s*\$branch_row\[\'dept\'\];\s*\}\s*else\s*\{\s*die\("Branch not found for the user."\);\s*\}/ms';

foreach ($files as $file) {
    $path = $dir . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        
        $new_content = preg_replace($block_regex, '', $content);
        
        if ($new_content !== $content) {
            file_put_contents($path, $new_content);
            echo "Removed redundant branch block from $file\n";
        } else {
            echo "Block not found or didn't match in $file\n";
        }
    }
}
echo "Done.\n";
?>
