<?php
require_once '../../config/mysql.php';

$message = '';
$success = false;

// Get list of agents for dropdown
$agents = [];
$conn = getMySQLConnection();
$result = $conn->query("SELECT agent_id, name FROM Agents ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $agents[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getMySQLConnection();
    
    $agent_id = $_POST['agent_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $classification_level = $_POST['classification_level'];
    
    // Call the stored procedure
    $stmt = $conn->prepare("CALL GenerateIntelligenceReport(?, ?, ?, ?)");
    $stmt->bind_param("isss", $agent_id, $title, $content, $classification_level);
    
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
    <title>Generate Intelligence Report - Stored Procedure</title>
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
        input, select, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 150px;
            resize: vertical;
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
    </style>
</head>
<body>
    <h1>Generate Intelligence Report</h1>
    <p>Use this form to create a new intelligence report in the system.</p>

    <form method="POST">
        <div class="form-group">
            <label for="agent_id">Agent:</label>
            <select id="agent_id" name="agent_id" required>
                <option value="">Select an agent</option>
                <?php foreach ($agents as $agent): ?>
                    <option value="<?php echo htmlspecialchars($agent['agent_id']); ?>">
                        <?php echo htmlspecialchars($agent['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="title">Report Title:</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="content">Report Content:</label>
            <textarea id="content" name="content" required></textarea>
        </div>

        <div class="form-group">
            <label for="classification_level">Classification Level:</label>
            <select id="classification_level" name="classification_level" required>
                <option value="">Select classification level</option>
                <option value="Top Secret">Top Secret</option>
                <option value="Secret">Secret</option>
                <option value="Confidential">Confidential</option>
                <option value="Restricted">Restricted</option>
                <option value="Unclassified">Unclassified</option>
            </select>
        </div>

        <button type="submit">Generate Report</button>
    </form>

    <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <a href="../index.php" class="back-link">Back to Home</a>
</body>
</html> 