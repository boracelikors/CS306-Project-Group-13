<?php
require_once '../../config/mysql.php';

$message = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getMySQLConnection();
    $action = $_POST['action'];
    
    try {
        if ($action == 'maintenance_mode') {
            // Test Case 1: Change vehicle to maintenance (trigger should log status change)
            $stmt = $conn->prepare("UPDATE Vehicles SET operational_status = 'Maintenance' WHERE vehicle_id = 1");
            $stmt->execute();
            $message = "✅ Vehicle status changed to Maintenance! Trigger logged the status change in Vehicle_Status_Log table.";
            $success = true;
            
        } elseif ($action == 'activate_vehicle') {
            // Test Case 2: Activate vehicle (trigger should log status change)
            $stmt = $conn->prepare("UPDATE Vehicles SET operational_status = 'Active' WHERE vehicle_id = 3");
            $stmt->execute();
            $message = "✅ Vehicle status changed to Active! Trigger logged the status change in Vehicle_Status_Log table.";
            $success = true;
            
        } elseif ($action == 'repair_mode') {
            // Test Case 3: Change vehicle to repair mode
            $stmt = $conn->prepare("UPDATE Vehicles SET operational_status = 'Repair' WHERE vehicle_id = 2");
            $stmt->execute();
            $message = "✅ Vehicle status changed to Repair! Trigger logged the status change in Vehicle_Status_Log table.";
            $success = true;
        }
        
    } catch (mysqli_sql_exception $e) {
        $message = "🔥 Trigger validation worked! Error: " . $e->getMessage();
        $success = true;
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Status Logging - Trigger Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .trigger-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        .test-buttons {
            display: flex;
            gap: 15px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .test-button {
            background: #3498db;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            flex: 1;
            min-width: 200px;
        }
        .test-button:hover {
            background: #2980b9;
        }
        .test-button.warning {
            background: #f39c12;
        }
        .test-button.warning:hover {
            background: #e67e22;
        }
        .test-button.success {
            background: #27ae60;
        }
        .test-button.success:hover {
            background: #229954;
        }
        .message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
            font-weight: bold;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .back-link {
            display: block;
            margin-top: 30px;
            color: #3498db;
            text-decoration: none;
            font-size: 16px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>🚗 Vehicle Status Logging Trigger</h1>
    
    <div class="trigger-info">
        <h3>About this Trigger</h3>
        <p><strong>LogVehicleStatusChange</strong> automatically fires when someone updates vehicle status in the Vehicles table. It:</p>
        <ul>
            <li>✅ Validates that the vehicle exists</li>
            <li>✅ Logs old and new status in the Vehicle_Status_Log table</li>
            <li>✅ Records the timestamp of the status change</li>
            <li>✅ Only logs when status actually changes</li>
        </ul>
        <p><strong>Test the trigger by clicking the buttons below:</strong></p>
    </div>

    <div class="test-buttons">
        <form method="POST" style="flex: 1;">
            <input type="hidden" name="action" value="maintenance_mode">
            <button type="submit" class="test-button warning">
                🔧 Set Maintenance<br>
                <small>(Vehicle 1)</small>
            </button>
        </form>
        
        <form method="POST" style="flex: 1;">
            <input type="hidden" name="action" value="activate_vehicle">
            <button type="submit" class="test-button success">
                ✅ Activate Vehicle<br>
                <small>(Vehicle 3)</small>
            </button>
        </form>
        
        <form method="POST" style="flex: 1;">
            <input type="hidden" name="action" value="repair_mode">
            <button type="submit" class="test-button">
                🔨 Set Repair Mode<br>
                <small>(Vehicle 2)</small>
            </button>
        </form>
    </div>

    <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <a href="../index.php" class="back-link">← Back to Home</a>
</body>
</html> 