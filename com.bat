@echo off
REM ============================================
REM Laravel Icon Packages Installer - Robust
REM --------------------------------------------
REM Usage: run this from the Laravel project root
REM (where composer.json lives). This script will:
REM  - verify composer is available
REM  - skip packages already present in composer.json
REM  - call "composer require" for missing packages (using CALL)
REM  - print a summary with installed / skipped / failed counts
REM NOTE: Use cmd.exe to run this (not PowerShell), or adapt if needed.
REM ============================================

setlocal enabledelayedexpansion

REM ------------------------------
REM Header
REM ------------------------------
echo =========================================
echo   Laravel Icon Packages Installer
echo =========================================
echo.
echo [INFO] Starting installation process...
echo.

REM ------------------------------
REM Check for composer on PATH
REM ------------------------------
where composer >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Composer executable not found in PATH.
    echo Please install Composer or ensure 'composer' is on your PATH.
    pause
    exit /b 1
) else (
    echo [SUCCESS] Composer found.
)
echo.

REM ------------------------------
REM List packages to install
REM ------------------------------
REM Add or remove packages from this list as you wish.
REM Keep them space-separated.
set "packages=blade-ui-kit/blade-icons blade-ui-kit/blade-heroicons codeat3/blade-phosphor-icons codeat3/blade-carbon-icons codeat3/blade-iconpark"

REM ------------------------------
REM Counters initialization
REM ------------------------------
set /a installed_count=0
set /a failed_count=0
set /a already_installed_count=0
set /a package_count=0

REM Count total packages (so we can display [i/N])
for %%P in (%packages%) do set /a package_count+=1

echo [INFO] %package_count% package(s) to process.
echo.

REM ------------------------------
REM Main loop: install each package
REM ------------------------------
set /a idx=0
for %%P in (%packages%) do (
    set /a idx+=1
    set "pkg=%%P"

    echo ----------------------------------------
    echo [!idx!/%package_count%] Package: !pkg!
    echo ----------------------------------------

    REM If composer.json exists, check if package is already referenced there
    if exist "composer.json" (
        REM Use findstr /C to search literal package string
        findstr /i /c:"!pkg!" "composer.json" >nul 2>&1
        if errorlevel 1 (
            REM package not found -> try installing
            echo [INFO] !pkg! not listed in composer.json - attempting install...
            REM IMPORTANT: use CALL so we return to this batch when composer is a batch file
            call composer require "!pkg!" --no-interaction --prefer-dist
            if errorlevel 1 (
                echo [ERROR] Failed to install: !pkg!
                set /a failed_count+=1
            ) else (
                echo [SUCCESS] Successfully installed: !pkg!
                set /a installed_count+=1
            )
        ) else (
            REM package found in composer.json
            echo [INFO] !pkg! appears to be already present in composer.json - skipping.
            set /a already_installed_count+=1
        )
    ) else (
        REM composer.json missing: inform user, but attempt install anyway
        echo [WARNING] composer.json not found. Will attempt to install !pkg! anyway.
        call composer require "!pkg!" --no-interaction --prefer-dist
        if errorlevel 1 (
            echo [ERROR] Failed to install: !pkg!
            set /a failed_count+=1
        ) else (
            echo [SUCCESS] Successfully installed: !pkg!
            set /a installed_count+=1
        )
    )

    echo.
)

REM ------------------------------
REM Summary
REM ------------------------------
echo =========================================
echo          Installation Summary
echo =========================================
echo Total packages processed: %package_count%
echo Successfully installed: !installed_count!
echo Already listed in composer.json (skipped): !already_installed_count!
echo Failed installs: !failed_count!
echo.

if !installed_count! gtr 0 (
    echo [SUCCESS] %installed_count% new package(s) installed.
    echo Next steps for Laravel projects:
    echo  1. Publish the icon sets you want to use if required:
    echo     php artisan blade-ui-kit:install
    echo  2. Check packages' docs for usage and publish commands.
    echo     (See their repositories for details)
    echo.
) else (
    echo [INFO] No new packages were installed.
)

echo =========================================
pause
endlocal
exit /b 0
