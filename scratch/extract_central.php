<?php
$sql = file_get_contents("C:/Users/gowth/.gemini/antigravity-ide/brain/69f19092-3e7b-4472-8b57-6c4e2a8d184c/scratch/master_utf8.sql");
$lines = explode("\n", $sql);
$in_central = false;
foreach ($lines as $line) {
    if (stripos($line, 'INSERT INTO') !== false && stripos($line, 'central_tab') !== false) {
        echo $line . "\n";
    }
}
