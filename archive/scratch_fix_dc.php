<?php
$c = file_get_contents('e:\set\xampp\htdocs\mini\FMS\modules\dept_coordinator\dc_down_fdps_files.php');
$replacement = <<<'EOD'
// DOWNLOAD ACTION
    elseif ($action == 'download') {
        if (ob_get_length()) {
            ob_end_clean();
        }
        if (count($selectedFiles) == 1) {
            $fileId = $selectedFiles[0];
            $sql = "SELECT $fileColumn FROM $tableName WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
            $result = $stmt->get_result();
            $file = $result->fetch_assoc();
            if ($file && !empty($file[$fileColumn])) {
                $filePath = fms_resolve_file_path($file[$fileColumn], __DIR__);
                if (file_exists($filePath)) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                    header('Content-Length: ' . filesize($filePath));
                    ob_clean();
                    flush();
                    readfile($filePath);
                    exit;
                }
            }
            echo "<script>alert('File not found.'); window.location.href = window.location.href;</script>";
            exit;
        } else {
            $zip = new ZipArchive();
            $zipFileName = $category . "_files_" . time() . ".zip";
            $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($selectedFiles as $fileId) {
                    $sql = "SELECT $fileColumn FROM $tableName WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $fileId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $file = $result->fetch_assoc();
                    if ($file && !empty($file[$fileColumn])) {
                        $filePath = fms_resolve_file_path($file[$fileColumn], __DIR__);
                        if (file_exists($filePath)) {
                            $zip->addFile($filePath, basename($filePath));
                        }
                    }
                }
                $zip->close();
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($zipFileName) . '"');
                header('Content-Length: ' . filesize($zipFilePath));
                ob_clean();
                flush();
                readfile($zipFilePath);
                unlink($zipFilePath);
                exit;
            } else {
                echo "<script>alert('Failed to create zip file.'); window.location.href = window.location.href;</script>";
                exit;
            }
        }
    }
}
$1
EOD;

$c = preg_replace('/\/\/ DOWNLOAD ACTION.*?(\/\/\/------------------------------------------------------------------------------------------------------------)/s', $replacement, $c);
file_put_contents('e:\set\xampp\htdocs\mini\FMS\modules\dept_coordinator\dc_down_fdps_files.php', $c);
echo "DONE";
