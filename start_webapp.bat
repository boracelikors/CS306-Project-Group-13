@echo off
echo Starting Web Application...
echo ========================

:: Get the current directory
set "PROJECT_DIR=%~dp0"
echo Working from: %PROJECT_DIR%

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
    if exist "%PROJECT_DIR%build\setup_database.php" (
        cd /d "%PROJECT_DIR%build"
    php setup_database.php
    if %errorLevel% neq 0 (
        echo Database setup failed!
        pause
        exit /b 1
    )
        cd /d "%PROJECT_DIR%"
    ) else (
        echo setup_database.php not found!
        pause
        exit /b 1
    )
) else (
    echo Database already exists. Skipping setup.
)

:: Import SQL procedures and triggers
echo Setting up database procedures and triggers...

:: Import all stored procedures
echo Importing stored procedures...
if exist "%PROJECT_DIR%scripts\stored_procedures\stored_procedure_1.sql" (
    echo Importing stored_procedure_1.sql...
    mysql -u root -p2003 military_intelligence < "%PROJECT_DIR%scripts\stored_procedures\stored_procedure_1.sql" 2>nul
)

if exist "%PROJECT_DIR%scripts\stored_procedures\stored_procedure_2.sql" (
    echo Importing stored_procedure_2.sql...
    mysql -u root -p2003 military_intelligence < "%PROJECT_DIR%scripts\stored_procedures\stored_procedure_2.sql" 2>nul
)

if exist "%PROJECT_DIR%scripts\stored_procedures\stored_procedures_3.sql" (
    echo Importing stored_procedures_3.sql...
    mysql -u root -p2003 military_intelligence < "%PROJECT_DIR%scripts\stored_procedures\stored_procedures_3.sql" 2>nul
)

if exist "%PROJECT_DIR%scripts\stored_procedures\stored_procedures_4.sql" (
    echo Importing stored_procedures_4.sql...
    mysql -u root -p2003 military_intelligence < "%PROJECT_DIR%scripts\stored_procedures\stored_procedures_4.sql" 2>nul
)

if exist "%PROJECT_DIR%scripts\stored_procedures\stored_procedures_5.sql" (
    echo Importing stored_procedures_5.sql...
    mysql -u root -p2003 military_intelligence < "%PROJECT_DIR%scripts\stored_procedures\stored_procedures_5.sql" 2>nul
)

:: Import all triggers
echo Importing triggers...
if exist "%PROJECT_DIR%scripts\triggers\trigger_1\trigger_1.sql" (
    echo Importing trigger_1.sql...
    mysql -u root -p2003 military_intelligence < "%PROJECT_DIR%scripts\triggers\trigger_1\trigger_1.sql" 2>nul
)

if exist "%PROJECT_DIR%scripts\triggers\trigger_2\trigger_2.sql" (
    echo Importing trigger_2.sql...
    mysql -u root -p2003 military_intelligence < "%PROJECT_DIR%scripts\triggers\trigger_2\trigger_2.sql" 2>nul
)

if exist "%PROJECT_DIR%scripts\triggers\trigger_3\trigger_3.sql" (
    echo Importing trigger_3.sql...
    mysql -u root -p2003 military_intelligence < "%PROJECT_DIR%scripts\triggers\trigger_3\trigger_3.sql" 2>nul
)

if exist "%PROJECT_DIR%scripts\triggers\trigger_4\trigger_4.sql" (
    echo Importing trigger_4.sql...
    mysql -u root -p2003 military_intelligence < "%PROJECT_DIR%scripts\triggers\trigger_4\trigger_4.sql" 2>nul
)

if exist "%PROJECT_DIR%scripts\triggers\trigger_5\trigger_5.sql" (
    echo Importing trigger_5.sql...
    mysql -u root -p2003 military_intelligence < "%PROJECT_DIR%scripts\triggers\trigger_5\trigger_5.sql" 2>nul
)

echo Database setup completed.

:: Test PHP syntax first
echo Testing PHP syntax...
cd /d "%PROJECT_DIR%build"
php -l index.php
if %errorLevel% neq 0 (
    echo PHP syntax error found! Please check the code.
    pause
    exit /b 1
)

:: Create a simple test file
echo Creating test file...
echo ^<?php echo "PHP is working!"; ?^> > test.php

:: Start PHP development server from the scripts directory (not build)
echo Starting PHP development server...
echo User interface will be available at http://localhost:8000/user/
echo Admin interface will be available at http://localhost:8000/admin/
echo Press Ctrl+C to stop the server when you're done.

if exist "%PROJECT_DIR%scripts" (
    cd /d "%PROJECT_DIR%scripts"
php -S localhost:8000 
) else (
    echo Error: scripts directory not found!
    echo Available directories:
    dir "%PROJECT_DIR%" /b
    pause
) 