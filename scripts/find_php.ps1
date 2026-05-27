# Finds php.exe path (returns full path)
# Usage: $php = & .\scripts\find_php.ps1
$ErrorActionPreference = 'Stop'
$php = $null
try {
    $cmd = Get-Command php -ErrorAction SilentlyContinue
    if ($cmd) { $php = $cmd.Source }
} catch {}
if (-not $php) {
    $candidates = @(
        "$env:ProgramFiles\php\php.exe",
        "$env:ProgramFiles(x86)\php\php.exe",
        'C:\xampp\php\php.exe',
        'C:\php\php.exe'
    )
    foreach ($p in $candidates) {
        if (Test-Path $p) { $php = $p; break }
    }
}
if (-not $php) {
    Write-Error 'php.exe not found. Add PHP to PATH or install XAMPP and ensure php.exe is accessible.'
    exit 1
}
Write-Output $php
