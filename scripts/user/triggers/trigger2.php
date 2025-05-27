<?php
require_once '../../config/mysql.php';

$message = '';
$success = false;

// Get list of supplies
$supplies = [];
$conn = getMySQLConnection();
$result = $conn->query("SELECT supply_id, name, quantity FROM Supply ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $supplies[] = $row;
    }
}

// Get recent audit logs
$audits = [];
$result = $conn->query("
    SELECT * FROM Supply_Audit
    ORDER BY changed_at DESC
    LIMIT 10
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $audits[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getMySQLConnection();
    
    $supply_id = $_POST['supply_id'];
    $new_quantity = $_POST['new_quantity'];
    
    try {
        // Update Supply table to trigger LogSupplyChanges
        $stmt = $conn->prepare("
            UPDATE Supply 
            SET quantity = ? 
            WHERE supply_id = ?
        ");
        $stmt->bind_param("ii", $new_quantity, $supply_id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            $message = "Supply quantity updated successfully. Check the audit log below.";
            $success = true;
            
            // Refresh audit logs
            $result = $conn->query("
                SELECT * FROM Supply_Audit
                ORDER BY changed_at DESC
                LIMIT 10
            ");
            $audits = [];
            while ($row = $result->fetch_assoc()) {
                $audits[] = $row;
            }
            
            // Refresh supplies list
            $result = $conn->query("SELECT supply_id, name, quantity FROM Supply ORDER BY name");
            $supplies = [];
            while ($row = $result->fetch_assoc()) {
                $supplies[] = $row;
            }
        } else {
            $message = "No changes were made to the supply quantity.";
            $success = false;
        }
    } catch (mysqli_sql_exception $e) {
        $message = $e->getMessage();
        $success = false;
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #2980b9;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
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
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #f8f9fa;
            font-weight: 500;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .trigger-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        .current-quantity {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .quantity-change {
            color: #2c3e50;
            font-weight: bold;
        }
        .quantity-increase {
            color: #27ae60;
        }
        .quantity-decrease {
            color: #c0392b;
        }
    </style>
</head>
<body>
    <h1>Log Supply Changes Trigger</h1>
    
    <div class="trigger-info">
        <h3>About this Trigger</h3>
        <p>This trigger automatically logs changes to supply quantities in the Supply_Audit table. It performs the following actions:</p>
        <ul>
            <li>Validates that the supply item exists</li>
            <li>Records the old and new quantities</li>
            <li>Timestamps the change</li>
            <li>Maintains an audit trail of all supply quantity modifications</li>
        </ul>
    </div>

    <form method="POST">
        <div class="form-group">
            <label for="supply_id">Select Supply Item:</label>
            <select id="supply_id" name="supply_id" required>
                <option value="">Choose a supply item</option>
                <?php foreach ($supplies as $supply): ?>
                    <option value="<?php echo htmlspecialchars($supply['supply_id']); ?>">
                        <?php echo htmlspecialchars($supply['name']); ?> 
                        (Current Quantity: <?php echo htmlspecialchars($supply['quantity']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="new_quantity">New Quantity:</label>
            <input type="number" id="new_quantity" name="new_quantity" min="0" required>
            <div class="current-quantity">Enter the new quantity for the selected supply item</div>
        </div>

        <button type="submit">Update Supply Quantity</button>
    </form>

    <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($audits)): ?>
        <h2>Recent Supply Changes</h2>
        <table>
            <thead>
                <tr>
                    <th>Audit ID</th>
                    <th>Supply Name</th>
                    <th>Quantity Change</th>
                    <th>Changed At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($audits as $audit): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($audit['audit_id']); ?></td>
                        <td><?php echo htmlspecialchars($audit['name']); ?></td>
                        <td>
                            <?php 
                            $change = $audit['new_quantity'] - $audit['old_quantity'];
                            $changeClass = $change > 0 ? 'quantity-increase' : ($change < 0 ? 'quantity-decrease' : '');
                            ?>
                            <span class="quantity-change <?php echo $changeClass; ?>">
                                <?php echo htmlspecialchars($audit['old_quantity']); ?> → 
                                <?php echo htmlspecialchars($audit['new_quantity']); ?>
                                (<?php echo $change > 0 ? '+' : ''; echo $change; ?>)
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($audit['changed_at']); ?></td>
                        <td><?php echo htmlspecialchars($audit['action_type']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="../index.php" class="back-link">Back to Home</a>
</body>
</html> 