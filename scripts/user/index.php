<?php
require_once '../config/mysql.php';
$conn = getMySQLConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS306 Project - User Interface</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .section {
            margin-bottom: 30px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        h2 {
            color: #34495e;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .item {
            margin: 15px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        .item:hover {
            background: #f1f3f5;
        }
        a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }
        a:hover {
            color: #2980b9;
            text-decoration: underline;
        }
        p {
            color: #666;
            margin: 10px 0;
        }
        .responsible {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>CS306 Database Management System</h1>
    
    <div class="section">
        <h2>Database Triggers</h2>
        <div class="item">
            <a href="triggers/trigger1.php">Log Missile Assignment</a>
            <p>Automatically logs and validates missile assignments to drones.</p>
            <div class="responsible">Responsible: Team Member 1</div>
        </div>
        <div class="item">
            <a href="triggers/trigger2.php">Log Supply Changes</a>
            <p>Tracks and audits all changes to supply quantities.</p>
            <div class="responsible">Responsible: Team Member 2</div>
        </div>
        <div class="item">
            <a href="triggers/trigger3.php">Agent Activity Logging</a>
            <p>Logs agent activities and maintains activity history for performance tracking.</p>
            <div class="responsible">Responsible: Team Member 3</div>
        </div>
        <div class="item">
            <a href="triggers/trigger4.php">Personnel Assignment Trigger</a>
            <p>Manages staff assignments and shift scheduling.</p>
            <div class="responsible">Responsible: Team Member 4</div>
        </div>
        <div class="item">
            <a href="triggers/trigger5.php">Equipment Maintenance Trigger</a>
            <p>Schedules and tracks equipment maintenance activities.</p>
            <div class="responsible">Responsible: Team Member 5</div>
        </div>
    </div>

    <div class="section">
        <h2>Stored Procedures</h2>
        <div class="item">
            <a href="procedures/reserve_vehicle.php">Reserve Vehicle</a>
            <p>Reserve an available vehicle from a specific base with the desired vehicle type.</p>
            <div class="responsible">Responsible: Team Member 1</div>
        </div>
        <div class="item">
            <a href="procedures/agent_reports.php">View Agent Reports</a>
            <p>View and analyze reports submitted by specific agents.</p>
            <div class="responsible">Responsible: Team Member 2</div>
        </div>
        <div class="item">
            <a href="procedures/generate_report.php">Generate Intelligence Report</a>
            <p>Create and manage intelligence reports with classification levels.</p>
            <div class="responsible">Responsible: Team Member 3</div>
        </div>
        <div class="item">
            <a href="procedures/assign_operator.php">Assign Operator to Drone</a>
            <p>Assign or update drone operators and their ranks.</p>
            <div class="responsible">Responsible: Team Member 1</div>
        </div>
        <div class="item">
            <a href="procedures/order_supply.php">Order Supply</a>
            <p>Order and update quantities of supplies in the inventory.</p>
            <div class="responsible">Responsible: Team Member 5</div>
        </div>
    </div>

    <div class="section">
        <h2>Support System</h2>
        <div class="item">
            <a href="support/tickets.php">Support Tickets</a>
            <p>Create and manage support tickets for assistance.</p>
        </div>
    </div>
</body>
</html> 