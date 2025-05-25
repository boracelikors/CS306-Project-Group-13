@echo off
echo PHP Setup Script
echo ===============

:: Check for administrator privileges
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo This script requires administrator privileges.
    echo Please right-click the script and select "Run as administrator"
    pause
    exit /b 1
)

:: Ask for PHP ZIP file location
echo Please enter the full path to your PHP ZIP file:
echo (e.g., C:\Users\BA\Downloads\php-8.4.5-Win32-vs17-x64.zip)
set /p phpzip=PHP ZIP path: 

:: Check if file exists
if not exist "%phpzip%" (
    echo Error: The file "%phpzip%" does not exist.
    echo Please make sure you entered the correct path.
    pause
    exit /b 1
)

:: Create PHP directory
echo Creating PHP directory...
mkdir "C:\php" 2>nul

:: Extract PHP
echo Extracting PHP...
powershell -Command "& {Expand-Archive -Path '%phpzip%' -DestinationPath 'C:\php' -Force}"

:: Create php.ini
echo Creating php.ini...
copy "C:\php\php.ini-development" "C:\php\php.ini"

:: Enable MySQL extensions
echo Enabling MySQL extensions...
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=mysqli', 'extension=mysqli' | Set-Content 'C:\php\php.ini'"
powershell -Command "(Get-Content 'C:\php\php.ini') -replace ';extension=pdo_mysql', 'extension=pdo_mysql' | Set-Content 'C:\php\php.ini'"

:: Add PHP to PATH using PowerShell with proper permissions
echo Adding PHP to system PATH...
powershell -Command "& {[Environment]::SetEnvironmentVariable('Path', $env:Path + ';C:\php', [System.EnvironmentVariableTarget]::Machine)}"

echo.
echo Setup complete! Please restart your computer for the PATH changes to take effect.
echo.
echo To test the installation, open a new command prompt and type:
echo php -v
echo.
pause 