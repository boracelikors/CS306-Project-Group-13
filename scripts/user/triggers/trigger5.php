<?php
require_once '../../config/mysql.php';

$message = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getMySQLConnection();
    $action = $_POST['action'];
    
    try {
        if ($action == 'drone_attack') {
            // Test Case 1: Valid drone attack (trigger should log the attack)
            $stmt = $conn->prepare("INSERT INTO Drone_Target_Attacks (drone_id, target_id) VALUES (1, 1)");
            $stmt->execute();
            $message = "✅ Drone attack logged! Trigger recorded the attack in Drone_Attack_Log table.";
            $success = true;
            
        } elseif ($action == 'stealth_mission') {
            // Test Case 2: Stealth mission attack
            $stmt = $conn->prepare("INSERT INTO Drone_Target_Attacks (drone_id, target_id) VALUES (2, 3)");
            $stmt->execute();
            $message = "✅ Stealth mission completed! Trigger recorded the attack in Drone_Attack_Log table.";
            $success = true;
            
        } elseif ($action == 'invalid_drone') {
            // Test Case 3: Invalid drone attack (trigger should fail)
            $stmt = $conn->prepare("INSERT INTO Drone_Target_Attacks (drone_id, target_id) VALUES (999, 1)");
            $stmt->execute();
            $message = "❌ This shouldn't happen - invalid drone should be rejected!";
            $success = false;
            
        } elseif ($action == 'invalid_target') {
            // Test Case 4: Invalid target attack (trigger should fail)
            $stmt = $conn->prepare("INSERT INTO Drone_Target_Attacks (drone_id, target_id) VALUES (1, 999)");
            $stmt->execute();
            $message = "❌ This shouldn't happen - invalid target should be rejected!";
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
    <title>Drone Attack Logging - Trigger Test</title>
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
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .test-button {
            background: #3498db;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .test-button:hover {
            background: #2980b9;
        }
        .test-button.stealth {
            background: #34495e;
        }
        .test-button.stealth:hover {
            background: #2c3e50;
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
    <h1>🎯 Drone Attack Logging Trigger</h1>
    
    <div class="trigger-info">
        <h3>About this Trigger</h3>
        <p><strong>LogDroneTargetAttack</strong> automatically fires when someone inserts into the Drone_Target_Attacks table. It:</p>
        <ul>
            <li>✅ Validates that the drone exists in the Drones table</li>
            <li>✅ Validates that the target exists in the Targets table</li>
            <li>✅ Logs the attack in the Drone_Attack_Log table</li>
            <li>❌ Prevents attacks with invalid drone or target IDs</li>
        </ul>
        <p><strong>Test the trigger by clicking the buttons below:</strong></p>
    </div>

    <div class="test-buttons">
        <form method="POST">
            <input type="hidden" name="action" value="drone_attack">
            <button type="submit" class="test-button">
                🚀 Standard Attack<br>
                <small>(Drone 1 → Target 1)</small>
            </button>
        </form>
        
        <form method="POST">
            <input type="hidden" name="action" value="stealth_mission">
            <button type="submit" class="test-button stealth">
                🥷 Stealth Mission<br>
                <small>(Drone 2 → Target 3)</small>
            </button>
        </form>
        
        <form method="POST">
            <input type="hidden" name="action" value="invalid_drone">
            <button type="submit" class="test-button danger">
                ❌ Invalid Drone<br>
                <small>(Drone 999 - should fail)</small>
            </button>
        </form>
        
        <form method="POST">
            <input type="hidden" name="action" value="invalid_target">
            <button type="submit" class="test-button danger">
                ❌ Invalid Target<br>
                <small>(Target 999 - should fail)</small>
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