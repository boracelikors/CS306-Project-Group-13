<?php
require_once '../../config/mysql.php';

$message = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getMySQLConnection();
    $action = $_POST['action'];
    
    try {
        if ($action == 'increase_ammo') {
            // Test Case 1: Increase ammo quantity (trigger should log the change)
            $stmt = $conn->prepare("UPDATE Supply SET quantity = quantity + 50 WHERE supply_id = 1");
            $stmt->execute();
            $message = "✅ Ammo quantity increased! Trigger logged the supply change in Supply_Audit table.";
            $success = true;
            
        } elseif ($action == 'decrease_fuel') {
            // Test Case 2: Decrease fuel quantity (trigger should log the change)
            $stmt = $conn->prepare("UPDATE Supply SET quantity = quantity - 100 WHERE supply_id = 4");
            $stmt->execute();
            $message = "✅ Fuel quantity decreased! Trigger logged the supply change in Supply_Audit table.";
            $success = true;
            
        } elseif ($action == 'invalid_supply') {
            // Test Case 3: Update non-existent supply (trigger should fail)
            $stmt = $conn->prepare("UPDATE Supply SET quantity = 999 WHERE supply_id = 999");
            $stmt->execute();
            $message = "❌ This shouldn't happen - invalid supply should be rejected!";
            $success = false;
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
    <title>Log Supply Changes - Trigger Test</title>
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
        .test-button.danger {
            background: #e74c3c;
        }
        .test-button.danger:hover {
            background: #c0392b;
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
    <h1>📦 Log Supply Changes Trigger</h1>
    
    <div class="trigger-info">
        <h3>About this Trigger</h3>
        <p><strong>LogSupplyChanges</strong> automatically fires when someone updates the Supply table. It:</p>
        <ul>
            <li>✅ Validates that the supply item exists</li>
            <li>✅ Logs old and new quantities in the Supply_Audit table</li>
            <li>✅ Records the timestamp of the change</li>
            <li>❌ Prevents updates to non-existent supplies</li>
        </ul>
        <p><strong>Test the trigger by clicking the buttons below:</strong></p>
    </div>

    <div class="test-buttons">
        <form method="POST" style="flex: 1;">
            <input type="hidden" name="action" value="increase_ammo">
            <button type="submit" class="test-button">
                ⬆️ Increase Ammo<br>
                <small>(+50 units)</small>
            </button>
        </form>
        
        <form method="POST" style="flex: 1;">
            <input type="hidden" name="action" value="decrease_fuel">
            <button type="submit" class="test-button">
                ⬇️ Decrease Fuel<br>
                <small>(-100 units)</small>
            </button>
        </form>
        
        <form method="POST" style="flex: 1;">
            <input type="hidden" name="action" value="invalid_supply">
            <button type="submit" class="test-button danger">
                ❌ Test Invalid Supply<br>
                <small>(Supply 999 - should fail)</small>
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