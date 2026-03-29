# Force Refresh All Caches Script
# Run this script to clear ALL possible caches

Write-Host "=== Clearing All Laravel Caches ===" -ForegroundColor Green

# 1. Clear view cache
Write-Host "1. Clearing view cache..." -ForegroundColor Yellow
Remove-Item "storage\framework\views\*.php" -Force -ErrorAction SilentlyContinue
$viewCount = (Get-ChildItem "storage\framework\views" -Filter "*.php" -ErrorAction SilentlyContinue | Measure-Object).Count
Write-Host "   View cache cleared. Remaining files: $viewCount" -ForegroundColor $(if ($viewCount -eq 0) { "Green" } else { "Red" })

# 2. Clear application cache
Write-Host "2. Clearing application cache..." -ForegroundColor Yellow
if (Test-Path "storage\framework\cache\data") {
    Remove-Item "storage\framework\cache\data\*" -Recurse -Force -ErrorAction SilentlyContinue
}
Write-Host "   Application cache cleared" -ForegroundColor Green

# 3. Clear config cache
Write-Host "3. Clearing config cache..." -ForegroundColor Yellow
if (Test-Path "bootstrap\cache\config.php") {
    Remove-Item "bootstrap\cache\config.php" -Force -ErrorAction SilentlyContinue
}
Write-Host "   Config cache cleared" -ForegroundColor Green

# 4. Clear route cache
Write-Host "4. Clearing route cache..." -ForegroundColor Yellow
if (Test-Path "bootstrap\cache\routes-v7.php") {
    Remove-Item "bootstrap\cache\routes-v7.php" -Force -ErrorAction SilentlyContinue
}
Write-Host "   Route cache cleared" -ForegroundColor Green

# 5. Clear database cache
Write-Host "5. Clearing database cache..." -ForegroundColor Yellow
& "D:\XAMPP (2)\mysql\bin\mariadb.exe" -u root -e "USE article_base; DELETE FROM cache WHERE 1=1;" 2>&1 | Out-Null
Write-Host "   Database cache cleared" -ForegroundColor Green

# 6. Verify database content
Write-Host "`n=== Verifying Database Content ===" -ForegroundColor Green
$accountContent = & "D:\XAMPP (2)\mysql\bin\mariadb.exe" -u root -e "USE article_base; SELECT JSON_EXTRACT(data_values, '$.freelancer_title') as title FROM frontends WHERE tempname = 'basic' AND data_keys = 'account.content' ORDER BY id DESC LIMIT 1;" 2>&1 | Select-Object -Last 1
Write-Host "Account Content: $accountContent" -ForegroundColor Cyan

$bannerContent = & "D:\XAMPP (2)\mysql\bin\mariadb.exe" -u root -e "USE article_base; SELECT JSON_EXTRACT(data_values, '$.heading') as heading FROM frontends WHERE tempname = 'basic' AND data_keys = 'banner.content' ORDER BY id DESC LIMIT 1;" 2>&1 | Select-Object -Last 1
Write-Host "Banner Heading: $bannerContent" -ForegroundColor Cyan

Write-Host "`n=== Cache Clear Complete ===" -ForegroundColor Green
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Restart XAMPP Apache server" -ForegroundColor White
Write-Host "2. Open browser in INCOGNITO/PRIVATE mode" -ForegroundColor White
Write-Host "3. Visit http://localhost/article/" -ForegroundColor White
Write-Host "4. If it works in incognito, clear your browser cache completely" -ForegroundColor White
