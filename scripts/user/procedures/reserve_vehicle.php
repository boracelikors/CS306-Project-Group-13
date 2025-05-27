<?php
require_once '../../config/mysql.php';

$message = '';
$success = false;

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
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
        }
        button {
            background: #0066cc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #0052a3;
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
        }
    </style>
</head>
<body>
    <h1>Reserve Vehicle</h1>
    <p>Use this form to reserve an available vehicle from a specific base.</p>

    <form method="POST">
        <div class="form-group">
            <label for="base_id">Base ID:</label>
            <input type="number" id="base_id" name="base_id" required>
        </div>

        <div class="form-group">
            <label for="vehicle_type">Vehicle Type:</label>
            <select id="vehicle_type" name="vehicle_type" required>
                <option value="">Select a vehicle type</option>
                <option value="Car">Car</option>
                <option value="Truck">Truck</option>
                <option value="Van">Van</option>
                <option value="Motorcycle">Motorcycle</option>
            </select>
        </div>

        <button type="submit">Reserve Vehicle</button>
    </form>

    <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <a href="../index.php" class="back-link">Back to Home</a>
</body>
</html> 