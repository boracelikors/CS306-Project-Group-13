# Military Intelligence System

A web-based dashboard for managing military intelligence operations, drone assignments, and intelligence reporting.

## Features

- **Dashboard Overview**: Real-time statistics showing numbers of drones, operators, targets, and intelligence reports
- **Drone Assignment**: Assign operators to drones for military operations
- **Intelligence Reports**: Generate and view intelligence reports from field agents
- **Database Management**: View and manage the military intelligence database

## Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Database Setup
1. Run the `setup_database.php` script to create the database schema and populate it with sample data:
   ```
   php setup_database.php
   ```

### Running the Application
1. Start your web server and ensure PHP is configured correctly
2. Access the application through your browser at `http://localhost/path-to-project/index.php`

### Quick Start (Windows)
Use the provided batch files for quick setup:
1. `install_php.bat` - Installs PHP if not already installed
2. `setup_php.bat` - Configures PHP for the application
3. `run_mysql.bat` - Starts the MySQL server
4. `start_webapp.bat` - Starts the web application

## System Architecture

The system is built with:
- PHP backend for database operations
- MySQL database for data storage
- HTML/CSS frontend with responsive design
- JavaScript for enhanced user interaction

## Main Components

- `index.php` - Main dashboard interface
- `assignment.php` - Drone operator assignment interface
- `process_assignment.php` - Handles drone assignment processing
- `intelligence_reports.php` - Displays all intelligence reports
- `generate_report.php` - Interface for creating new intelligence reports
- `view_database.php` - View all database tables
- `view_assignments.php` - View current drone assignments

## Media Features

- Military theme with appropriate styling
- Background military march that plays automatically (when supported by browser)
- Military logo in the header

## Database Schema

The database includes tables for:
- Personnel (Operators, Soldiers, Agents, Civil Personnel)
- Equipment (Drones, Missiles, Satellites, Vehicles)
- Operations (Assignments, Intelligence Reports)
- Infrastructure (Bases, Supply Chain, Geographic Data)

## Screenshots

Located in the `build/screenshots` directory 