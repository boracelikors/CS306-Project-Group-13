<?php
header('Content-Type: text/html; charset=utf-8');

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "2003";
$dbname = "military_intelligence";

try {
    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Get basic stats for display
    $drone_count = 0;
    $report_count = 0;
    $operator_count = 0;
    
    $result = $conn->query("SELECT COUNT(*) as count FROM Drones");
    if ($result) {
        $row = $result->fetch_assoc();
        $drone_count = $row['count'];
    }
    
    $result = $conn->query("SELECT COUNT(*) as count FROM Intelligence_Reports");
    if ($result) {
        $row = $result->fetch_assoc();
        $report_count = $row['count'];
    }
    
    $result = $conn->query("SELECT COUNT(*) as count FROM Operator");
    if ($result) {
        $row = $result->fetch_assoc();
        $operator_count = $row['count'];
    }

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Military Intelligence System - User Homepage</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #2c3e50;
        }

        .header {
            background-color: #2c3e50;
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 2.5rem;
        }

        .header p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .stats-bar {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .stat-item {
            flex: 1;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #3498db;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .section h2 {
            color: #2c3e50;
            margin-top: 0;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-item {
            background: #f8f9fa;
            margin: 1rem 0;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }

        .feature-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .feature-description {
            color: #666;
            margin-bottom: 1rem;
        }

        .feature-responsible {
            font-size: 0.9rem;
            color: #7f8c8d;
            font-style: italic;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 1rem;
            transition: background-color 0.2s;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .btn-support {
            background-color: #e74c3c;
            font-size: 1.1rem;
            padding: 1rem 2rem;
        }

        .btn-support:hover {
            background-color: #c0392b;
        }

        .support-section {
            text-align: center;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .support-section h2 {
            color: white;
            border-bottom: 2px solid white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Military Intelligence System</h1>
        <p>User Interface - Database Integration Portal</p>
    </div>

    <div class="container">
        <!-- System Statistics -->
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number"><?php echo $drone_count; ?></div>
                <div class="stat-label">Active Drones</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $operator_count; ?></div>
                <div class="stat-label">Operators</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $report_count; ?></div>
                <div class="stat-label">Intelligence Reports</div>
            </div>
        </div>

        <!-- Database Triggers Section -->
        <div class="section">
            <h2>Database Triggers</h2>
            <p>Interactive pages to test and demonstrate database triggers. Each trigger has dedicated test cases with different conditions.</p>
            
            <ul class="feature-list">
                <li class="feature-item">
                    <div class="feature-title">Trigger 1: Operator Assignment Validation</div>
                    <div class="feature-description">Validates operator assignments when drones are updated. Tests different assignment scenarios.</div>
                    <div class="feature-responsible">Responsible: Group Member 1</div>
                    <a href="triggers/trigger_1.php" class="btn">Test Trigger 1</a>
                </li>
                
                <li class="feature-item">
                    <div class="feature-title">Trigger 2: Intelligence Report Classification</div>
                    <div class="feature-description">Automatically handles classification levels when new intelligence reports are created.</div>
                    <div class="feature-responsible">Responsible: Group Member 2</div>
                    <a href="triggers/trigger_2.php" class="btn">Test Trigger 2</a>
                </li>
                
                <li class="feature-item">
                    <div class="feature-title">Trigger 3: Vehicle Status Update</div>
                    <div class="feature-description">Manages vehicle operational status changes and availability tracking.</div>
                    <div class="feature-responsible">Responsible: Group Member 3</div>
                    <a href="triggers/trigger_3.php" class="btn">Test Trigger 3</a>
                </li>
                
                <li class="feature-item">
                    <div class="feature-title">Trigger 4: Supply Inventory Management</div>
                    <div class="feature-description">Handles supply quantity updates and low-stock alerts.</div>
                    <div class="feature-responsible">Responsible: Group Member 4</div>
                    <a href="triggers/trigger_4.php" class="btn">Test Trigger 4</a>
                </li>
                
                <li class="feature-item">
                    <div class="feature-title">Trigger 5: Agent Activity Logging</div>
                    <div class="feature-description">Logs agent activities and maintains activity history.</div>
                    <div class="feature-responsible">Responsible: Group Member 5</div>
                    <a href="triggers/trigger_5.php" class="btn">Test Trigger 5</a>
                </li>
            </ul>
        </div>

        <!-- Stored Procedures Section -->
        <div class="section">
            <h2>Stored Procedures</h2>
            <p>Interactive forms to execute stored procedures with custom parameters. Each procedure includes input validation and result display.</p>
            
            <ul class="feature-list">
                <li class="feature-item">
                    <div class="feature-title">Procedure 1: Assign Operator to Drone</div>
                    <div class="feature-description">Assigns operators to drones with rank validation and conflict checking.</div>
                    <div class="feature-responsible">Responsible: Group Member 1</div>
                    <a href="procedures/procedure_1.php" class="btn">Execute Procedure 1</a>
                </li>
                
                <li class="feature-item">
                    <div class="feature-title">Procedure 2: Get Agent Reports</div>
                    <div class="feature-description">Retrieves all intelligence reports for a specific agent with statistics.</div>
                    <div class="feature-responsible">Responsible: Group Member 2</div>
                    <a href="procedures/procedure_2.php" class="btn">Execute Procedure 2</a>
                </li>
                
                <li class="feature-item">
                    <div class="feature-title">Procedure 3: Generate Intelligence Report</div>
                    <div class="feature-description">Creates new intelligence reports with automatic ID generation and validation.</div>
                    <div class="feature-responsible">Responsible: Group Member 3</div>
                    <a href="procedures/procedure_3.php" class="btn">Execute Procedure 3</a>
                </li>
                
                <li class="feature-item">
                    <div class="feature-title">Procedure 4: Reserve Vehicle</div>
                    <div class="feature-description">Reserves available vehicles at specific bases by type and updates status.</div>
                    <div class="feature-responsible">Responsible: Group Member 4</div>
                    <a href="procedures/procedure_4.php" class="btn">Execute Procedure 4</a>
                </li>
                
                <li class="feature-item">
                    <div class="feature-title">Procedure 5: Order Supply</div>
                    <div class="feature-description">Updates supply quantities with order processing and inventory management.</div>
                    <div class="feature-responsible">Responsible: Group Member 5</div>
                    <a href="procedures/procedure_5.php" class="btn">Execute Procedure 5</a>
                </li>
            </ul>
        </div>

        <!-- Support System Section -->
        <div class="section support-section">
            <h2>Support Ticket System</h2>
            <p>Need help? Create a support ticket and track your requests. Our admin team will respond to your inquiries.</p>
            <a href="support/index.php" class="btn btn-support">Access Support System</a>
        </div>
    </div>
</body>
</html> 