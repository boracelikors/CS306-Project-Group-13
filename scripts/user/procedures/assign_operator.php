<?php
require_once '../../config/mysql.php';

$message = '';
$success = false;

// Get list of drones
$drones = [];
$conn = getMySQLConnection();
$result = $conn->query("SELECT drone_id, model, status FROM Drones ORDER BY drone_id");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $drones[] = $row;
    }
}

// Get list of operators
$operators = [];
$result = $conn->query("SELECT op_id, name, rank FROM Operator ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $operators[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getMySQLConnection();
    
    $operator_id = $_POST['operator_id'];
    $drone_id = $_POST['drone_id'];
    $rank = $_POST['rank'];
    
    // Call the stored procedure
    $stmt = $conn->prepare("CALL AssignOperatorToDrone(?, ?, ?)");
    $stmt->bind_param("iis", $operator_id, $drone_id, $rank);
    
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
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>Assign Operator to Drone</h1>
    <p>Use this form to assign or update an operator's drone assignment.</p>

    <form method="POST">
        <div class="form-group">
            <label for="operator_id">Operator:</label>
            <select id="operator_id" name="operator_id" required>
                <option value="">Select an operator</option>
                <?php foreach ($operators as $operator): ?>
                    <option value="<?php echo htmlspecialchars($operator['op_id']); ?>">
                        <?php echo htmlspecialchars($operator['name'] ?? 'Operator ' . $operator['op_id']); ?>
                        <?php if ($operator['rank']): ?>
                            (<?php echo htmlspecialchars($operator['rank']); ?>)
                        <?php endif; ?>
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
                        (<?php echo htmlspecialchars($drone['status']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="rank">Operator Rank:</label>
            <select id="rank" name="rank" required>
                <option value="">Select a rank</option>
                <option value="Junior">Junior</option>
                <option value="Senior">Senior</option>
                <option value="Expert">Expert</option>
                <option value="Master">Master</option>
            </select>
            <div class="info">This will update the operator's rank if they already exist.</div>
        </div>

        <button type="submit">Assign Operator</button>
    </form>

    <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <a href="../index.php" class="back-link">Back to Home</a>
</body>
</html> 