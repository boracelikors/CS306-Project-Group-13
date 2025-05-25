<?php
require_once '../../config/mysql.php';

$message = '';
$success = false;

// Get list of supplies for dropdown
$supplies = [];
$conn = getMySQLConnection();
$result = $conn->query("SELECT supply_id, name, quantity FROM Supply ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $supplies[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getMySQLConnection();
    
    $supply_id = $_POST['supply_id'];
    $order_quantity = $_POST['order_quantity'];
    
    // Call the stored procedure
    $stmt = $conn->prepare("CALL OrderSupply(?, ?)");
    $stmt->bind_param("ii", $supply_id, $order_quantity);
    
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
    <title>Order Supply - Stored Procedure</title>
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
        .current-quantity {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>Order Supply</h1>
    <p>Use this form to order additional quantities of supplies.</p>

    <form method="POST">
        <div class="form-group">
            <label for="supply_id">Supply Item:</label>
            <select id="supply_id" name="supply_id" required>
                <option value="">Select a supply item</option>
                <?php foreach ($supplies as $supply): ?>
                    <option value="<?php echo htmlspecialchars($supply['supply_id']); ?>">
                        <?php echo htmlspecialchars($supply['name']); ?> 
                        (Current Quantity: <?php echo htmlspecialchars($supply['quantity']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="order_quantity">Order Quantity:</label>
            <input type="number" id="order_quantity" name="order_quantity" min="1" required>
        </div>

        <button type="submit">Place Order</button>
    </form>

    <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <a href="../index.php" class="back-link">Back to Home</a>
</body>
</html> 