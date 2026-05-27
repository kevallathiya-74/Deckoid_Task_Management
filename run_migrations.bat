@echo off
REM Run migration using php in PATH or fallback to common XAMPP/php
where php >nul 2>&1
if %ERRORLEVEL%==0 (
    php database\migrations\create_daily_reports.php
) else (
    if exist "C:\xampp\php\php.exe" (
        "C:\xampp\php\php.exe" database\migrations\create_daily_reports.php
    ) else (
        echo php.exe not found. Please add PHP to PATH or install XAMPP.
        exit /b 1
    )
)
