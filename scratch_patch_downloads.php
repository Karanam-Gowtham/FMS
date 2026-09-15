<?php
function patchFile($filePath) {
    $c = file_get_contents($filePath);
    
    // Replace delete block
    $c = preg_replace(
        '/if \(\$file && file_exists\(\$file\[\$fileColumn\]\)\) \{\s*unlink\(\$file\[\$fileColumn\]\);\s*\}/s',
        "if (\$file && !empty(\$file[\$fileColumn])) {
            \$resolved = fms_resolve_file_path(\$file[\$fileColumn], __DIR__);
            if (file_exists(\$resolved)) {
                unlink(\$resolved);
            }
        }",
        $c
    );

    // Replace single download block
    $c = preg_replace(
        '/if \(\$file && file_exists\(\$file\[\$fileColumn\]\)\) \{\s*\$filePath = \$file\[\$fileColumn\];\s*header\(\'Content-Type: application\/octet-stream\'\);\s*header\(\'Content-Disposition: attachment; filename="\' \. basename\(\$filePath\) \. \'"\'\);\s*header\(\'Content-Length: \' \. filesize\(\$filePath\)\);\s*ob_clean\(\);\s*flush\(\);\s*readfile\(\$filePath\);\s*exit;\s*\}/s',
        "if (\$file && !empty(\$file[\$fileColumn])) {
            \$filePath = fms_resolve_file_path(\$file[\$fileColumn], __DIR__);
            if (file_exists(\$filePath)) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=\"' . basename(\$filePath) . '\"');
                header('Content-Length: ' . filesize(\$filePath));
                ob_clean();
                flush();
                readfile(\$filePath);
                exit;
            }
        }",
        $c
    );

    // Replace zip download block
    $c = preg_replace(
        '/if \(\$file && file_exists\(\$file\[\$fileColumn\]\)\) \{\s*\/\/ Get the base name of the file \(e\.g\., \'report\.pdf\'\)\s*\$fileName = basename\(\$file\[\$fileColumn\]\);\s*\/\/ If the file already exists in the zip, append a unique identifier \(fileCounter\)\s*\$newFileName = \$fileName;\s*\/\/ Ensure unique filename by appending a counter if file already exists\s*while \(\$zip->locateName\(\$newFileName\) !== false\) \{\s*\$newFileName = pathinfo\(\$fileName, PATHINFO_FILENAME\) \. "_\$fileCounter\." \. pathinfo\(\$fileName, PATHINFO_EXTENSION\);\s*\$fileCounter\+\+;\s*\}\s*\/\/ Add file to the ZIP with the new unique name\s*\$zip->addFile\(\$file\[\$fileColumn\], \$newFileName\);\s*\}/s',
        "if (\$file && !empty(\$file[\$fileColumn])) {
            \$filePath = fms_resolve_file_path(\$file[\$fileColumn], __DIR__);
            if (file_exists(\$filePath)) {
                \$fileName = basename(\$filePath);
                \$newFileName = \$fileName;
                while (\$zip->locateName(\$newFileName) !== false) {
                    \$newFileName = pathinfo(\$fileName, PATHINFO_FILENAME) . \"_\$fileCounter.\" . pathinfo(\$fileName, PATHINFO_EXTENSION);
                    \$fileCounter++;
                }
                \$zip->addFile(\$filePath, \$newFileName);
            }
        }",
        $c
    );

    file_put_contents($filePath, $c);
}

patchFile('e:\set\xampp\htdocs\mini\FMS\modules\dept_coordinator\dc_down_st_act_files.php');
patchFile('e:\set\xampp\htdocs\mini\FMS\modules\dept_coordinator\dc_down_st_act_files_hod.php');

// common/download_papers1.php has a slightly different pattern for zip (it uses `$paperFilePath = $row[$fileColumn]`)
$c = file_get_contents('e:\set\xampp\htdocs\mini\FMS\modules\common\download_papers1.php');
$c = preg_replace(
    '/if \(file_exists\(\$file\[\$fileColumn\]\)\) \{\s*unlink\(\$file\[\$fileColumn\]\);\s*\}/s',
    "if (!empty(\$file[\$fileColumn])) {
        \$resolved = fms_resolve_file_path(\$file[\$fileColumn], __DIR__);
        if (file_exists(\$resolved)) {
            unlink(\$resolved);
        }
    }",
    $c
);
$c = preg_replace(
    '/if \(file_exists\(\$file\[\$fileColumn\]\)\) \{\s*\$filePath = \$file\[\$fileColumn\];\s*header\(\'Content-Type: application\/octet-stream\'\);\s*header\(\'Content-Disposition: attachment; filename="\' \. basename\(\$filePath\) \. \'"\'\);\s*header\(\'Content-Length: \' \. filesize\(\$filePath\)\);\s*ob_clean\(\);\s*flush\(\);\s*readfile\(\$filePath\);\s*exit;\s*\}/s',
    "if (!empty(\$file[\$fileColumn])) {
        \$filePath = fms_resolve_file_path(\$file[\$fileColumn], __DIR__);
        if (file_exists(\$filePath)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=\"' . basename(\$filePath) . '\"');
            header('Content-Length: ' . filesize(\$filePath));
            ob_clean();
            flush();
            readfile(\$filePath);
            exit;
        }
    }",
    $c
);
$c = preg_replace(
    '/if \(file_exists\(\$file\[\$fileColumn\]\)\) \{\s*\$zip->addFile\(\$file\[\$fileColumn\], basename\(\$file\[\$fileColumn\]\)\);\s*\}/s',
    "if (!empty(\$file[\$fileColumn])) {
        \$filePath = fms_resolve_file_path(\$file[\$fileColumn], __DIR__);
        if (file_exists(\$filePath)) {
            \$zip->addFile(\$filePath, basename(\$filePath));
        }
    }",
    $c
);
file_put_contents('e:\set\xampp\htdocs\mini\FMS\modules\common\download_papers1.php', $c);

echo "DONE PATCHING";
