<?php
require_once '../config/mysql.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getMySQLConnection();
    try {
        // Test trigger by inserting/updating data
        $stmt = $conn->prepare("INSERT INTO your_table (column1, column2) VALUES (?, ?)");
        $stmt->execute([$_POST['value1'], $_POST['value2']]);
        $message = "Trigger test executed successfully!";
    } catch(PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Trigger 1</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .form-group { margin: 15px 0; }
        .message { padding: 10px; margin: 10px 0; background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Test Trigger 1: [Trigger Name]</h1>
    <p>Description: This trigger [describe what the trigger does]</p>

    <form method="POST">
        <div class="form-group">
            <label>Value 1:</label>
            <input type="text" name="value1" required>
        </div>
        <div class="form-group">
            <label>Value 2:</label>
            <input type="text" name="value2" required>
        </div>
        <button type="submit">Test Trigger</button>
    </form>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <p><a href="index.php">Back to Homepage</a></p>
</body>
</html> 