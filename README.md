# CS306 Military Intelligence System - Phase III
## Web Integration of SQL and NoSQL Databases

A comprehensive web-based military intelligence management system with user and admin interfaces, featuring database triggers, stored procedures, and MongoDB-based support ticket system.

## 📋 Project Overview

This project implements a full-stack web application for CS306 Database Management Systems course, integrating:
- **MySQL Database** for core military intelligence data
- **MongoDB** for support ticket system
- **PHP Backend** with trigger and stored procedure integration
- **Dual Interface** design (User + Admin)

## 🚀 Quick Start

### Windows Users (Recommended)
```bash
# 1. Start the web application
start_webapp.bat

# 2. Access the application
# User Interface: http://localhost:8000/user
# Admin Interface: http://localhost:8000/admin
```

## 📦 Installation Requirements

### Prerequisites
- **PHP 8.4.5** (NTS x64)
- **MySQL 8.0+** 
- **MongoDB Community Server**
- **MongoDB PHP Driver** (php_mongodb.dll)

### Detailed Setup

#### 1. PHP Setup
```bash
# Install PHP (if needed)
install_php.bat

# Configure PHP
setup_php.bat
```

#### 2. MySQL Database Setup
```bash
# Start MySQL server
run_mysql.bat

# Import database schema and data
mysql -u root -p2003 < scripts/setup_database.sql
```

#### 3. MongoDB Setup
1. **Install MongoDB Community Server**
2. **Install PHP MongoDB Driver:**
   - Download `php_mongodb.dll` for PHP 8.4.5 NTS x64
   - Copy to `xampp/php/ext/`
   - Add `extension=mongodb` to `php.ini`
3. **Start MongoDB service**

#### 4. Configuration
Update database credentials in `scripts/config/mysql.php`:
```php
$host = "localhost";
$username = "root"; 
$password = "2003";
$database = "military_intelligence";
$port = 3306;
```

## 🏗️ System Architecture

### Directory Structure
```
PROJECT/
├── scripts/
│   ├── user/                    # User interface
│   │   ├── index.php           # User homepage
│   │   ├── triggers/           # 5 Trigger demonstration pages
│   │   │   ├── trigger1.php    # Log Missile Assignment
│   │   │   ├── trigger2.php    # Log Supply Changes
│   │   │   ├── trigger3.php    # Vehicle Status Logging
│   │   │   ├── trigger4.php    # Report Update Logging
│   │   │   └── trigger5.php    # Drone Attack Logging
│   │   ├── procedures/         # 5 Stored procedure pages
│   │   │   ├── assign_operator.php    # Assign Operator to Drone
│   │   │   ├── reserve_vehicle.php    # Reserve Vehicle
│   │   │   ├── agent_reports.php     # View Agent Reports
│   │   │   ├── generate_report.php   # Generate Intelligence Report
│   │   │   └── order_supply.php      # Order Supply
│   │   └── support/            # Support ticket system (MongoDB)
│   ├── admin/                  # Admin interface
│   │   └── index.php          # Admin dashboard for tickets
│   └── config/
│       └── mysql.php          # Database configuration
├── CS306_GROUP_X_HW3_SQLDUMP.sql    # Complete database dump
└── README.md                   # This documentation
```

## 🎯 Features

### User Interface (`localhost:8000/user`)

#### Database Triggers (5 Total)
Each trigger demonstrates automatic database operations:

1. **Log Missile Assignment** - Automatically logs drone-missile assignments
2. **Log Supply Changes** - Tracks supply quantity modifications  
3. **Vehicle Status Logging** - Records vehicle operational status changes
4. **Report Update Logging** - Logs intelligence report title changes
5. **Drone Attack Logging** - Records drone-target attack operations

#### Stored Procedures (5 Total)
Interactive forms for database operations:

1. **Assign Operator to Drone** - Assign operators to specific drones
2. **Reserve Vehicle** - Reserve available vehicles from bases
3. **View Agent Reports** - Retrieve reports by specific agents
4. **Generate Intelligence Report** - Create new intelligence reports
5. **Order Supply** - Update supply quantities in inventory

#### Support Ticket System
- Create support tickets (stored in MongoDB)
- View personal ticket history
- Add comments to existing tickets

### Admin Interface (`localhost:8000/admin`)

#### Ticket Management
- View all active support tickets
- Add admin comments to tickets
- Mark tickets as resolved
- MongoDB-based ticket storage

## 🗄️ Database Schema

### MySQL Tables
- **Core Entities**: Countries, Base, Agents, Drones, Missiles, Vehicles, Supply
- **Personnel**: Operator, Person, Soldier, Civil
- **Operations**: Intelligence_Reports, Targets, Satellites
- **Relationships**: Drone_Missile_Usage, Drone_Target_Attacks, etc.
- **Audit Tables**: DroneStatus, Supply_Audit, Vehicle_Status_Log, etc.

### MongoDB Collections
- **support_tickets**: User support requests with comments and status

## 🔧 Technical Implementation

### Triggers
All triggers are automatically executed on database events:
```sql
-- Example: Log Missile Assignment Trigger
CREATE TRIGGER LogMissileAssignment
AFTER INSERT ON Drone_Missile_Usage
FOR EACH ROW
BEGIN
    INSERT INTO DroneStatus (drone_id, missile_id)
    VALUES (NEW.drone_id, NEW.missile_id);
END
```

### Stored Procedures
Callable procedures with parameters:
```sql
-- Example: Assign Operator to Drone
CALL AssignOperatorToDrone(operator_id, drone_id, rank);
```

### MongoDB Integration
```php
// Example: Create support ticket
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->insert([
    'username' => $username,
    'message' => $message,
    'created_at' => date('Y-m-d H:i:s'),
    'status' => true,
    'comments' => []
]);
```

## 🧪 Testing

### Trigger Testing
Each trigger page provides buttons to test different scenarios:
- ✅ Valid operations (trigger succeeds)
- ❌ Invalid operations (trigger validation fails)

### Procedure Testing
Each procedure page provides forms with:
- Dropdown selections for existing data
- Input validation
- Success/error message display

### Sample Test Data
The database includes 10 sample records for each entity:
- 10 Operators, 10 Drones, 10 Missiles
- 10 Bases, 10 Vehicles, 10 Supply items
- 10 Agents, 10 Intelligence Reports

## 🚨 Troubleshooting

### Common Issues

#### "Unknown column 'status'" Error
- **Cause**: Reserved keyword not escaped
- **Fix**: Use backticks around `range` and `rank` columns
```php
SELECT drone_id, `range`, `rank` FROM table_name
```

#### "No available vehicle found"
- **Cause**: No active vehicles at selected base
- **Fix**: Check vehicle availability in database
- **Available combinations**: Base 1 + Humvee, Base 2 + APC, etc.

#### MongoDB Connection Failed
- **Cause**: MongoDB service not running or driver not installed
- **Fix**: 
  1. Start MongoDB service
  2. Verify `php_mongodb.dll` in `php/ext/`
  3. Add `extension=mongodb` to `php.ini`

#### Database Connection Error
- **Cause**: Wrong credentials or MySQL not running
- **Fix**: Verify credentials in `scripts/config/mysql.php`

## 📊 Demo Requirements

### For CS306 Evaluation
Each team member must demonstrate:
1. **Their assigned trigger** (1 per member)
2. **Their assigned stored procedure** (1 per member)  
3. **Support ticket system** (randomly selected member)

### Demo Checklist
- [ ] XAMPP server running (Apache + MySQL)
- [ ] MongoDB Compass accessible
- [ ] All 5 triggers functional
- [ ] All 5 stored procedures functional
- [ ] Support ticket creation/management working
- [ ] Both user and admin interfaces accessible

## 📁 File Descriptions

### Core Files
- `start_webapp.bat` - Starts PHP development server
- `scripts/setup_database.sql` - Complete database schema and data
- `CS306_GROUP_X_HW3_SQLDUMP.sql` - Project submission SQL dump

### Configuration Files
- `scripts/config/mysql.php` - Database connection settings
- `php.ini` - PHP configuration (MongoDB extension)

### Interface Files
- `scripts/user/index.php` - User homepage with trigger/procedure links
- `scripts/admin/index.php` - Admin dashboard for ticket management

## 🎓 Academic Context

**Course**: CS306 Database Management Systems  
**Phase**: III - Web Integration  
**Focus**: SQL/NoSQL integration, triggers, stored procedures  
**Technologies**: PHP, MySQL, MongoDB, HTML/CSS  
**Evaluation**: 80% Demo + 20% Submission  

## ⚠️ Known Issues & Fixes Needed

### Issues Fixed ✅
- ✅ Reserved keyword errors (`range`, `rank`) - Fixed with backticks
- ✅ Database schema mismatches - Corrected column references
- ✅ Trigger pages had forms instead of buttons - Fixed to button-based testing
- ✅ Wrong vehicle types in reserve_vehicle.php - Fixed to use actual database types

### Remaining Issues to Fix 🔧
1. **Support ticket system** - Needs MongoDB connection testing
2. **All trigger pages** - Verify all 5 triggers have button-based interfaces
3. **All procedure pages** - Verify all 5 procedures work with correct parameters
4. **Admin interface** - Test MongoDB ticket management functionality

### Quick Demo Check
Run the automated demo verification script:
```bash
php demo_check.php
```
This will check all prerequisites and system readiness.

### Final Demo Checklist
- [ ] Start `start_webapp.bat` successfully
- [ ] Access `localhost:8000/user` - shows all 5 triggers + 5 procedures
- [ ] Test each trigger page - buttons work and show trigger firing
- [ ] Test each procedure page - forms work with database
- [ ] Test support ticket creation (MongoDB)
- [ ] Access `localhost:8000/admin` - shows ticket management
- [ ] Verify all database connections work

## 📞 Support

For technical issues:
1. Check troubleshooting section above
2. Verify all prerequisites are installed
3. Ensure database credentials are correct
4. Test with provided sample data

---

**Project Status**: ⚠️ Almost Demo Ready - Minor Issues Remaining  
**Last Updated**: January 2025  
**Version**: 1.0 - CS306 Phase III Submission 