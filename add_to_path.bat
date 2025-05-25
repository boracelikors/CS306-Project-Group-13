@echo off
echo This script will help you add MySQL and PHP to your system PATH.
echo.
echo Please enter the full path to your MySQL bin directory 
echo (e.g., C:\Program Files\MySQL\MySQL Server 8.0\bin)
set /p mysqlpath=MySQL bin path: 

echo.
echo Please enter the full path to your PHP directory
echo (e.g., C:\xampp\php)
set /p phppath=PHP path: 

echo.
echo Adding paths to your environment...

setx PATH "%PATH%;%mysqlpath%;%phppath%" /M

echo.
echo Paths have been added! Please restart your terminal to apply changes.
echo You can test by running:
echo   php --version
echo   mysql --version
echo.
pause 