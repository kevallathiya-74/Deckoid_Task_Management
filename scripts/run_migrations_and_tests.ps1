# Wrapper to run migrations and a quick save API test.
# Run from project root in PowerShell:
# .\scripts\run_migrations_and_tests.ps1

$ErrorActionPreference = 'Stop'
$php = & .\scripts\find_php.ps1
Write-Host "Using PHP: $php"

Write-Host "Running migration..."
& $php "database\migrations\create_daily_reports.php"

Write-Host "Verifying tables..."
& $php "database\verify.php"

Write-Host "Testing save API (POST)..."
try {
    $body = @{ date = (Get-Date -Format yyyy-MM-dd); rows = @(@{ task_text = 'Auto test task'; number_value = '3' }) } | ConvertTo-Json -Depth 5
    $res = Invoke-RestMethod -Method Post -Uri "http://localhost/Deckoid_Task_Management/api/daily-report/save" -ContentType "application/json" -Body $body -ErrorAction Stop
    Write-Host "Save API response:`n" ($res | ConvertTo-Json -Depth 5)
} catch {
    Write-Error "Save API test failed: $_"
}

Write-Host "Done."