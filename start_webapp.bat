@echo off
echo Starting Web Application...
echo ========================

:: Start MySQL service if not running
echo Checking MySQL service...
net start MySQL80 >nul 2>&1
if %errorLevel% neq 0 (
    echo MySQL service is already running or could not be started.
    echo Please make sure MySQL is installed and running.
)

:: Check if database exists
echo Checking database...
mysql -u root -p2003 -e "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'military_intelligence'" >nul 2>&1
if %errorLevel% neq 0 (
    echo Database not found. Creating new database...
    cd build
    php setup_database.php
    if %errorLevel% neq 0 (
        echo Database setup failed!
        pause
        exit /b 1
    )
    cd ..
) else (
    echo Database already exists. Skipping setup.
)

:: Import SQL procedures and triggers
echo Setting up database procedures and triggers...
cd scripts
mysql -u root -p2003 military_intelligence < stored_procedures/stored_procedure_1.sql
mysql -u root -p2003 military_intelligence < stored_procedures/stored_procedure_2.sql
mysql -u root -p2003 military_intelligence < triggers/trigger_1/trigger_1.sql
mysql -u root -p2003 military_intelligence < triggers/trigger_2/trigger_2.sql
cd ..

:: Start PHP development server
echo Starting PHP development server...
echo The web application will be available at http://localhost:8000
echo Press Ctrl+C to stop the server when you're done.
cd build
php -S localhost:8000 