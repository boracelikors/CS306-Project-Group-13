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

:: Add MySQL to PATH using PowerShell with proper permissions
echo Adding MySQL to system PATH...
powershell -Command "& {[Environment]::SetEnvironmentVariable('Path', $env:Path + ';C:\Program Files\MySQL\MySQL Server 9.2\bin', [System.EnvironmentVariableTarget]::Machine)}"

:: Refresh PATH in current session
set "PATH=%PATH%;C:\Program Files\MySQL\MySQL Server 9.2\bin"

:: Check for MySQL installation
echo Checking MySQL installation...
mysql --version
if %errorLevel% neq 0 (
    echo MySQL is not installed or not in PATH.
    echo Please install MySQL first from: https://dev.mysql.com/downloads/installer/
    echo After installing MySQL, run this script again.
    pause
    exit /b 1
) else (
    echo MySQL is installed successfully.
)

:: Find PHP installation directory
for %%p in (php.exe) do set "PHP_PATH=%%~dp$PATH:p"
if not defined PHP_PATH (
    echo PHP not found in PATH. Please make sure PHP is installed.
    pause
    exit /b 1
)

:: Create php.ini if it doesn't exist
if not exist "%PHP_PATH%php.ini" (
    echo Creating php.ini...
    copy "%PHP_PATH%php.ini-development" "%PHP_PATH%php.ini"
)

:: Enable MySQL extensions
echo Enabling MySQL extensions...
powershell -Command "(Get-Content '%PHP_PATH%php.ini') -replace ';extension=mysqli', 'extension=mysqli' | Set-Content '%PHP_PATH%php.ini'"
powershell -Command "(Get-Content '%PHP_PATH%php.ini') -replace ';extension=pdo_mysql', 'extension=pdo_mysql' | Set-Content '%PHP_PATH%php.ini'"

:: Add PHP to PATH using PowerShell with proper permissions
echo Adding PHP to system PATH...
powershell -Command "& {[Environment]::SetEnvironmentVariable('Path', $env:Path + ';%PHP_PATH%', [System.EnvironmentVariableTarget]::Machine)}"

echo.
echo Setup complete! Please restart your computer for the PATH changes to take effect.
echo.
echo To test the installation, open a new command prompt and type:
echo php -v
echo.
pause 