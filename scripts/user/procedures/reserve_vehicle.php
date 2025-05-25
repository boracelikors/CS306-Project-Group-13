<?php
require_once '../../config/mysql.php';

$message = '';
$success = false;

// Get list of bases
$bases = [];
$conn = getMySQLConnection();
$result = $conn->query("SELECT base_id, name FROM Base ORDER BY base_id");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bases[] = $row;
    }
}

// Get available vehicle types from the database
$vehicle_types = [];
$result = $conn->query("SELECT DISTINCT type FROM Vehicles WHERE operational_status = 'Active' ORDER BY type");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vehicle_types[] = $row['type'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getMySQLConnection();
    
    $base_id = $_POST['base_id'];
    $vehicle_type = $_POST['vehicle_type'];
    
    // Call the stored procedure
    $stmt = $conn->prepare("CALL ReserveVehicle(?, ?)");
    $stmt->bind_param("is", $base_id, $vehicle_type);
    
    try {
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $message = $row['message'];
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
    <title>Reserve Vehicle - Stored Procedure</title>
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
    <h1>Reserve Vehicle</h1>
    
    <div class="info">
        <h3>About this Stored Procedure</h3>
        <p><strong>ReserveVehicle</strong> allows you to reserve an available vehicle from a specific base. It:</p>
        <ul>
            <li>✅ Searches for active vehicles of the specified type at the given base</li>
            <li>✅ Reserves the first available vehicle by changing its status to 'Reserved'</li>
            <li>❌ Returns an error if no vehicles are available</li>
        </ul>
    </div>

    <form method="POST">
        <div class="form-group">
            <label for="base_id">Base:</label>
            <select id="base_id" name="base_id" required>
                <option value="">Select a base</option>
                <?php foreach ($bases as $base): ?>
                    <option value="<?php echo htmlspecialchars($base['base_id']); ?>">
                        Base <?php echo htmlspecialchars($base['base_id']); ?> - 
                        <?php echo htmlspecialchars($base['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="vehicle_type">Vehicle Type:</label>
            <select id="vehicle_type" name="vehicle_type" required>
                <option value="">Select a vehicle type</option>
                <?php foreach ($vehicle_types as $type): ?>
                    <option value="<?php echo htmlspecialchars($type); ?>">
                        <?php echo htmlspecialchars($type); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Reserve Vehicle</button>
    </form>

    <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <a href="../index.php" class="back-link">← Back to Home</a>
</body>
</html> 