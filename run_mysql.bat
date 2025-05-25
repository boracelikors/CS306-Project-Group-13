@echo off
echo Running MySQL setup script...
echo Please enter your MySQL password when prompted.
echo.
mysql -u root -p -e "source scripts/setup_database.sql"
echo.
echo Script completed. Press any key to exit.
pause > nul 