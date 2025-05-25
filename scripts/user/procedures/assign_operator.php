<?php
require_once '../../config/mysql.php';

$message = '';
$success = false;

// Get list of drones
$drones = [];
$conn = getMySQLConnection();
$result = $conn->query("SELECT drone_id, model, `range`, max_altitude FROM Drones ORDER BY drone_id");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $drones[] = $row;
    }
}

// Get list of operators with their current ranks
$operators = [];
$result = $conn->query("SELECT op_id, `rank` FROM Operator ORDER BY op_id");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $operators[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getMySQLConnection();
    
    $operator_id = $_POST['operator_id'];
    $drone_id = $_POST['drone_id'];
    
    // Get the operator's current rank to pass to the procedure
    $stmt = $conn->prepare("SELECT `rank` FROM Operator WHERE op_id = ?");
    $stmt->bind_param("i", $operator_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $operator = $result->fetch_assoc();
    $current_rank = $operator['rank'] ?? 'Sergeant'; // Default rank if not found
    
    // Call the stored procedure with current rank (no rank change)
    $stmt = $conn->prepare("CALL AssignOperatorToDrone(?, ?, ?)");
    $stmt->bind_param("iis", $operator_id, $drone_id, $current_rank);
    
    try {
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $message = $row['result'];
        $success = true;
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
    <title>Assign Operator to Drone - Stored Procedure</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
            max-width: 800px;
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
        .info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>Assign Operator to Drone</h1>
    
    <div class="info">
        <h3>About this Stored Procedure</h3>
        <p><strong>AssignOperatorToDrone</strong> assigns an existing operator to a specific drone. It:</p>
        <ul>
            <li>✅ Assigns the selected operator to the chosen drone</li>
            <li>✅ Maintains the operator's current rank</li>
            <li>✅ Validates that both operator and drone exist</li>
            <li>❌ Prevents assignment to non-existent drones</li>
        </ul>
    </div>

    <form method="POST">
        <div class="form-group">
            <label for="operator_id">Operator:</label>
            <select id="operator_id" name="operator_id" required>
                <option value="">Select an operator</option>
                <?php foreach ($operators as $operator): ?>
                    <option value="<?php echo htmlspecialchars($operator['op_id']); ?>">
                        Operator <?php echo htmlspecialchars($operator['op_id']); ?> - 
                        <?php echo htmlspecialchars($operator['rank']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="drone_id">Drone:</label>
            <select id="drone_id" name="drone_id" required>
                <option value="">Select a drone</option>
                <?php foreach ($drones as $drone): ?>
                    <option value="<?php echo htmlspecialchars($drone['drone_id']); ?>">
                        Drone <?php echo htmlspecialchars($drone['drone_id']); ?> - 
                        <?php echo htmlspecialchars($drone['model']); ?>
                        (Range: <?php echo htmlspecialchars($drone['range']); ?>km, 
                         Max Alt: <?php echo htmlspecialchars($drone['max_altitude']); ?>m)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Assign Operator to Drone</button>
    </form>

    <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <a href="../index.php" class="back-link">← Back to Home</a>
</body>
</html> 