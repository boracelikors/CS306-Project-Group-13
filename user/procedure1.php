<?php
require_once '../config/mysql.php';

$result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getMySQLConnection();
    try {
        // Call the stored procedure
        $stmt = $conn->prepare("CALL your_procedure(?, ?, @output)");
        $stmt->execute([$_POST['param1'], $_POST['param2']]);
        
        // Get the output parameter
        $result = $conn->query("SELECT @output AS result")->fetch(PDO::FETCH_ASSOC)['result'];
    } catch(PDOException $e) {
        $result = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Stored Procedure 1</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .form-group { margin: 15px 0; }
        .result { padding: 10px; margin: 10px 0; background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Test Stored Procedure 1: [Procedure Name]</h1>
    <p>Description: This stored procedure [describe what the procedure does]</p>

    <form method="POST">
        <div class="form-group">
            <label>Parameter 1:</label>
            <input type="text" name="param1" required>
        </div>
        <div class="form-group">
            <label>Parameter 2:</label>
            <input type="text" name="param2" required>
        </div>
        <button type="submit">Execute Procedure</button>
    </form>

    <?php if ($result): ?>
        <div class="result">
            <h3>Result:</h3>
            <pre><?php echo htmlspecialchars($result); ?></pre>
        </div>
    <?php endif; ?>

    <p><a href="index.php">Back to Homepage</a></p>
</body>
</html> 