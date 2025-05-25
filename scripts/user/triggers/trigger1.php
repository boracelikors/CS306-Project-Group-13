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

// Get list of missiles
$missiles = [];
$result = $conn->query("SELECT missile_id, type, status FROM Missiles ORDER BY missile_id");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $missiles[] = $row;
    }
}

// Get current assignments
$assignments = [];
$result = $conn->query("
    SELECT ds.*, d.model as drone_model, m.type as missile_type 
    FROM DroneStatus ds
    JOIN Drones d ON ds.drone_id = d.drone_id
    JOIN Missiles m ON ds.missile_id = m.missile_id
    ORDER BY ds.assignment_date DESC
    LIMIT 10
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getMySQLConnection();
    
    $drone_id = $_POST['drone_id'];
    $missile_id = $_POST['missile_id'];
    
    try {
        // Insert into Drone_Missile_Usage to trigger the LogMissileAssignment trigger
        $stmt = $conn->prepare("INSERT INTO Drone_Missile_Usage (drone_id, missile_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $drone_id, $missile_id);
        $stmt->execute();
        
        $message = "Successfully assigned missile to drone. The trigger has logged this assignment.";
        $success = true;
        
        // Refresh assignments list
        $result = $conn->query("
            SELECT ds.*, d.model as drone_model, m.type as missile_type 
            FROM DroneStatus ds
            JOIN Drones d ON ds.drone_id = d.drone_id
            JOIN Missiles m ON ds.missile_id = m.missile_id
            ORDER BY ds.assignment_date DESC
            LIMIT 10
        ");
        $assignments = [];
        while ($row = $result->fetch_assoc()) {
            $assignments[] = $row;
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
    <title>Log Missile Assignment - Trigger Test</title>
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
    </style>
</head>
<body>
    <h1>Log Missile Assignment Trigger</h1>
    
    <div class="trigger-info">
        <h3>About this Trigger</h3>
        <p>This trigger automatically logs missile assignments to drones in the DroneStatus table. It performs the following actions:</p>
        <ul>
            <li>Validates that both the drone and missile exist in their respective tables</li>
            <li>Creates a new status record with the assignment details</li>
            <li>Automatically timestamps the assignment</li>
        </ul>
    </div>

    <form method="POST">
        <div class="form-group">
            <label for="drone_id">Select Drone:</label>
            <select id="drone_id" name="drone_id" required>
                <option value="">Choose a drone</option>
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
            <label for="missile_id">Select Missile:</label>
            <select id="missile_id" name="missile_id" required>
                <option value="">Choose a missile</option>
                <?php foreach ($missiles as $missile): ?>
                    <option value="<?php echo htmlspecialchars($missile['missile_id']); ?>">
                        Missile <?php echo htmlspecialchars($missile['missile_id']); ?> - 
                        <?php echo htmlspecialchars($missile['type']); ?>
                        (<?php echo htmlspecialchars($missile['status']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Assign Missile to Drone</button>
    </form>

    <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($assignments)): ?>
        <h2>Recent Assignments</h2>
        <table>
            <thead>
                <tr>
                    <th>Status ID</th>
                    <th>Drone</th>
                    <th>Missile</th>
                    <th>Status</th>
                    <th>Assignment Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $assignment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($assignment['status_id']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($assignment['drone_id']); ?> - 
                            <?php echo htmlspecialchars($assignment['drone_model']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($assignment['missile_id']); ?> - 
                            <?php echo htmlspecialchars($assignment['missile_type']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($assignment['status']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['assignment_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="../index.php" class="back-link">Back to Home</a>
</body>
</html> 