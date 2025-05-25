<?php
require_once '../../config/mysql.php';

$message = '';
$success = false;
$reports = [];
$summary = null;
$agent_info = '';

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
    
    // Call the stored procedure
    $stmt = $conn->prepare("CALL GetAgentReports(?)");
    $stmt->bind_param("i", $agent_id);
    
    try {
        $stmt->execute();
        
        // Get agent info
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $agent_info = $row['agent_info'];
        }
        
        // Get reports
        $stmt->next_result();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }
        
        // Get summary
        $stmt->next_result();
        $result = $stmt->get_result();
        $summary = $result->fetch_assoc();
        
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
    <title>Agent Reports - Stored Procedure</title>
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
        select {
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
        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .agent-info {
            font-size: 1.2em;
            color: #2c3e50;
            margin-bottom: 20px;
            padding: 10px;
            background: #edf2f7;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>Agent Reports</h1>
    <p>View reports submitted by a specific agent.</p>

    <form method="POST">
        <div class="form-group">
            <label for="agent_id">Select Agent:</label>
            <select id="agent_id" name="agent_id" required>
                <option value="">Choose an agent</option>
                <?php foreach ($agents as $agent): ?>
                    <option value="<?php echo htmlspecialchars($agent['agent_id']); ?>">
                        <?php echo htmlspecialchars($agent['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">View Reports</button>
    </form>

    <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($agent_info): ?>
        <div class="agent-info">
            <?php echo htmlspecialchars($agent_info); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($reports)): ?>
        <table>
            <thead>
                <tr>
                    <th>Report ID</th>
                    <th>Date Created</th>
                    <th>Title</th>
                    <th>Classification Level</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['report_id']); ?></td>
                        <td><?php echo htmlspecialchars($report['date_created']); ?></td>
                        <td><?php echo htmlspecialchars($report['title']); ?></td>
                        <td><?php echo htmlspecialchars($report['classification_level']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($summary): ?>
            <div class="summary">
                <h3>Summary Statistics</h3>
                <p>Total Reports: <?php echo htmlspecialchars($summary['total_reports']); ?></p>
                <p>Most Recent Report: <?php echo htmlspecialchars($summary['most_recent_report']); ?></p>
                <p>Oldest Report: <?php echo htmlspecialchars($summary['oldest_report']); ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <a href="../index.php" class="back-link">Back to Home</a>
</body>
</html> 