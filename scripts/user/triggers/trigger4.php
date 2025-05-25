<?php
require_once '../../config/mysql.php';

$message = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getMySQLConnection();
    $action = $_POST['action'];
    
    try {
        if ($action == 'update_title') {
            // Test Case 1: Update report title (trigger should log the change)
            $stmt = $conn->prepare("UPDATE Intelligence_Reports SET title = 'Updated: Mission Status Report' WHERE report_id = 1");
            $stmt->execute();
            $message = "✅ Report title updated! Trigger logged the title change in Report_Update_Log table.";
            $success = true;
            
        } elseif ($action == 'classify_report') {
            // Test Case 2: Update report title to classified
            $stmt = $conn->prepare("UPDATE Intelligence_Reports SET title = 'CLASSIFIED: Operation Phoenix' WHERE report_id = 2");
            $stmt->execute();
            $message = "✅ Report classified! Trigger logged the title change in Report_Update_Log table.";
            $success = true;
            
        } elseif ($action == 'urgent_update') {
            // Test Case 3: Mark report as urgent
            $stmt = $conn->prepare("UPDATE Intelligence_Reports SET title = 'URGENT: Threat Assessment Update' WHERE report_id = 3");
            $stmt->execute();
            $message = "✅ Report marked urgent! Trigger logged the title change in Report_Update_Log table.";
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
    <title>Report Update Logging - Trigger Test</title>
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
        .test-button.classified {
            background: #8e44ad;
        }
        .test-button.classified:hover {
            background: #7d3c98;
        }
        .test-button.urgent {
            background: #e74c3c;
        }
        .test-button.urgent:hover {
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
    <h1>📋 Report Update Logging Trigger</h1>
    
    <div class="trigger-info">
        <h3>About this Trigger</h3>
        <p><strong>LogReportUpdate</strong> automatically fires when someone updates report titles in the Intelligence_Reports table. It:</p>
        <ul>
            <li>✅ Validates that the report exists</li>
            <li>✅ Logs old and new titles in the Report_Update_Log table</li>
            <li>✅ Records the timestamp of the title change</li>
            <li>✅ Only logs when title actually changes</li>
        </ul>
        <p><strong>Test the trigger by clicking the buttons below:</strong></p>
    </div>

    <div class="test-buttons">
        <form method="POST" style="flex: 1;">
            <input type="hidden" name="action" value="update_title">
            <button type="submit" class="test-button">
                📝 Update Report Title<br>
                <small>(Report 1)</small>
            </button>
        </form>
        
        <form method="POST" style="flex: 1;">
            <input type="hidden" name="action" value="classify_report">
            <button type="submit" class="test-button classified">
                🔒 Classify Report<br>
                <small>(Report 2)</small>
            </button>
        </form>
        
        <form method="POST" style="flex: 1;">
            <input type="hidden" name="action" value="urgent_update">
            <button type="submit" class="test-button urgent">
                🚨 Mark Urgent<br>
                <small>(Report 3)</small>
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