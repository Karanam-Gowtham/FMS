$files = Get-ChildItem -Path ".\modules", ".\HOD", ".\admin" -Recurse -Filter "*.php" -File
foreach ($f in $files) {
    $content = Get-Content $f.FullName -Raw
    if ($content -match 'admin/admins\.php\?dept=') {
        $content = $content -replace 'admin/admins\.php\?dept=', 'public/dept.php?dept='
        Set-Content -Path $f.FullName -Value $content -NoNewline
    }
}
