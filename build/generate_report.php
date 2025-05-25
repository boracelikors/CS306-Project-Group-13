<?php
header('Content-Type: text/html; charset=utf-8');

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "2003";
$dbname = "military_intelligence";

try {
    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Get agents for dropdown
    $agents = $conn->query("SELECT agent_id, name FROM Agents ORDER BY name");
    
    // Get targets for dropdown
    $targets = $conn->query("SELECT target_id, name, `type` FROM Targets ORDER BY name");
    
    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $agent_id = $_POST['agent_id'];
        $target_id = $_POST['target_id'];
        $content = $_POST['content'];
        $classification = $_POST['classification'];
        $title = $_POST['title'] ?? 'New Intelligence Report';
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Get the next report_id
            $result = $conn->query("SELECT MAX(report_id) as max_id FROM Intelligence_Reports");
            $row = $result->fetch_assoc();
            $report_id = ($row['max_id'] ?? 0) + 1;
            
            // Insert new report (using correct table name and column structure)
            $stmt = $conn->prepare("INSERT INTO Intelligence_Reports (report_id, title, content, date_created, classification_level, agent_id) VALUES (?, ?, ?, NOW(), ?, ?)");
            $stmt->bind_param("isssi", $report_id, $title, $content, $classification, $agent_id);
            $stmt->execute();
            
            // Insert into Intelligence_Report_Decides_Target table to link report with target
            $stmt = $conn->prepare("INSERT INTO Intelligence_Report_Decides_Target (report_id, target_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $report_id, $target_id);
            $stmt->execute();
            
            // Commit transaction
            $conn->commit();
            $success = "Intelligence report successfully generated with ID: " . $report_id;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            throw new Exception("Error generating report: " . $e->getMessage());
        }
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Intelligence Report - Military Intelligence System</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --background-color: #f5f7fa;
            --text-color: #2c3e50;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .header {
            background-color: var(--primary-color);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .form-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            font-family: inherit;
            box-sizing: border-box;
        }

        textarea {
            min-height: 200px;
            resize: vertical;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.2s;
            display: inline-block;
        }

        .btn-primary {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .back-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--accent-color);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 2rem;
            transition: background-color 0.2s;
        }

        .back-button:hover {
            background-color: #2980b9;
        }

        .success-message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .error-message {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c00;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Generate Intelligence Report</h1>
        <p>Military Intelligence System</p>
    </div>

    <div class="container">
        <a href="index.php" class="back-button">← Back to Dashboard</a>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="form-card">
            <h2>Create New Intelligence Report</h2>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">Report Title</label>
                    <input type="text" id="title" name="title" required placeholder="Enter report title">
                </div>

                <div class="form-group">
                    <label for="agent_id">Reporting Agent</label>
                    <select id="agent_id" name="agent_id" required>
                        <option value="">Select an agent</option>
                        <?php if (isset($agents) && $agents->num_rows > 0): ?>
                            <?php while ($agent = $agents->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($agent['agent_id']); ?>">
                                    <?php echo htmlspecialchars($agent['agent_id'] . ' - ' . $agent['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="target_id">Target</label>
                    <select id="target_id" name="target_id" required>
                        <option value="">Select a target</option>
                        <?php if (isset($targets) && $targets->num_rows > 0): ?>
                            <?php while ($target = $targets->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($target['target_id']); ?>">
                                    <?php echo htmlspecialchars($target['target_id'] . ' - ' . $target['name'] . ' (' . ($target['type'] ?? 'Unknown') . ')'); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="classification">Classification Level</label>
                    <select id="classification" name="classification" required>
                        <option value="">Select classification</option>
                        <option value="Unclassified">Unclassified</option>
                        <option value="Confidential">Confidential</option>
                        <option value="Secret">Secret</option>
                        <option value="Top Secret">Top Secret</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="content">Report Content</label>
                    <textarea id="content" name="content" required placeholder="Enter detailed intelligence report content..."></textarea>
                </div>

                <div class="button-group">
                    <a href="intelligence_reports.php" class="btn btn-secondary">View Reports</a>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 