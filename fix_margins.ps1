$files = Get-ChildItem -Path "e:\set\xampp\htdocs\mini\FMS" -Recurse -Include *.php, *.css
foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    $newContent = $content -replace 'margin-top:\s*70px;', 'margin-top: 0;'
    
    if ($content -ne $newContent) {
        Set-Content -Path $file.FullName -Value $newContent -NoNewline
        Write-Output "Updated $($file.FullName)"
    }
}
