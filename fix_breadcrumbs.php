<?php
$base_dir = __DIR__;
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_dir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filepath = $file->getPathname();
        $content = file_get_contents($filepath);
        $changed = false;
        
        // Fix 1: admin and HOD pointing to c_login_n.php etc.
        if (strpos($filepath, '\\admin\\') !== false || strpos($filepath, '/admin/') !== false || 
            strpos($filepath, '\\HOD\\') !== false || strpos($filepath, '/HOD/') !== false) {
            
            $replacements = [
                '../c_login_n.php' => '../modules/central/c_login_n.php',
                '../c_login.php' => '../modules/central/c_login.php',
                '../c_aqar_files.php' => '../modules/central/c_aqar_files.php'
            ];
            
            foreach ($replacements as $search => $replace) {
                if (strpos($content, $search) !== false) {
                    $content = str_replace($search, $replace, $content);
                    $changed = true;
                }
            }
        }
        
        // Fix 2: admin/admins.php -> ../reg.php
        if (strpos($filepath, 'admin\\admins.php') !== false || strpos($filepath, 'admin/admins.php') !== false) {
            if (strpos($content, '../reg.php') !== false) {
                $content = str_replace('../reg.php', '../modules/auth/reg.php', $content);
                $changed = true;
            }
        }
        
        // Fix 3: faculty/edit_profile.php
        if (strpos($filepath, 'faculty\\edit_profile.php') !== false || strpos($filepath, 'faculty/edit_profile.php') !== false) {
            $content = str_replace('./admin/admins.php', '../../admin/admins.php', $content, $count);
            if ($count > 0) $changed = true;
            
            $content = str_replace('./reg.php', '../auth/reg.php', $content, $count);
            if ($count > 0) $changed = true;
            
            $content = str_replace('./logout.php', '../auth/logout.php', $content, $count);
            if ($count > 0) $changed = true;
        }

        // Fix 5: dept_files.php and dept_down_files.php pointing to acd_year.php
        if (strpos($filepath, 'dept_coordinator\\dept_files.php') !== false || strpos($filepath, 'dept_coordinator/dept_files.php') !== false ||
            strpos($filepath, 'dept_coordinator\\dept_down_files.php') !== false || strpos($filepath, 'dept_coordinator/dept_down_files.php') !== false ||
            strpos($filepath, 'dept_coordinator\\dc_down_st_act_files_hod.php') !== false || strpos($filepath, 'dept_coordinator/dc_down_st_act_files_hod.php') !== false) {
            
            if (strpos($content, 'href="acd_year.php') !== false) {
                $content = str_replace('href="acd_year.php', 'href="../faculty/acd_year.php', $content);
                $changed = true;
            }
            if (strpos($content, 'href="cc_acd_year.php') !== false) {
                $content = str_replace('href="cc_acd_year.php', 'href="../central/cc_acd_year.php', $content);
                $changed = true;
            }
        }
        
        // Fix 6: cc_down_dc_files.php -> dc_acd_year.php
        if (strpos($filepath, 'central\\cc_down_dc_files.php') !== false || strpos($filepath, 'central/cc_down_dc_files.php') !== false) {
            if (strpos($content, 'href="dc_acd_year.php') !== false) {
                $content = str_replace('href="dc_acd_year.php', 'href="../dept_coordinator/dc_acd_year.php', $content);
                $changed = true;
            }
        }
        
        if ($changed) {
            file_put_contents($filepath, $content);
            echo "Fixed: " . str_replace($base_dir . DIRECTORY_SEPARATOR, '', $filepath) . "\n";
        }
    }
}
?>
