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
    
    // Get all intelligence reports with related information
    $sql = "SELECT ir.*, a.name as agent_name, t.name as target_name
            FROM Intelligence_Reports ir
            LEFT JOIN Agent_Wrote_Report awr ON ir.report_id = awr.report_id
            LEFT JOIN Agents a ON awr.agent_id = a.agent_id
            LEFT JOIN Intelligence_Report_Decides_Target irdt ON ir.report_id = irdt.report_id
            LEFT JOIN Targets t ON irdt.target_id = t.target_id
            ORDER BY ir.date_created DESC";
              
    $reports = $conn->query($sql);
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intelligence Reports - Military Intelligence System</title>
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
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .report-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--card-shadow);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .report-id {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .report-date {
            color: #666;
        }

        .report-title {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .report-content {
            margin: 1rem 0;
            line-height: 1.6;
        }

        .report-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
        }

        .meta-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .meta-value {
            font-weight: 500;
        }

        .classification {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 3px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .classification.top-secret {
            background-color: #e74c3c;
            color: white;
        }

        .classification.secret {
            background-color: #e67e22;
            color: white;
        }

        .classification.confidential {
            background-color: #f1c40f;
            color: #2c3e50;
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

        .error-message {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c00;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Intelligence Reports</h1>
        <p>Military Intelligence System</p>
    </div>

    <div class="container">
        <a href="index.php" class="back-button">← Back to Dashboard</a>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($reports) && $reports->num_rows > 0): ?>
            <?php while ($report = $reports->fetch_assoc()): ?>
                <div class="report-card">
                    <div class="report-header">
                        <div class="report-id">Report #<?php echo htmlspecialchars($report['report_id'] ?? 'N/A'); ?></div>
                        <div class="report-date"><?php echo htmlspecialchars($report['date_created'] ?? 'N/A'); ?></div>
                    </div>
                    
                    <div class="report-title">
                        <?php echo htmlspecialchars($report['title'] ?? 'Untitled Report'); ?>
                    </div>
                    
                    <div class="report-content">
                        <?php echo nl2br(htmlspecialchars($report['content'] ?? 'No content available')); ?>
                    </div>
                    
                    <div class="report-meta">
                        <div class="meta-item">
                            <span class="meta-label">Classification</span>
                            <span class="classification <?php echo strtolower(str_replace(' ', '-', $report['classification_level'] ?? 'unclassified')); ?>">
                                <?php echo htmlspecialchars($report['classification_level'] ?? 'Unclassified'); ?>
                            </span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Agent</span>
                            <span class="meta-value"><?php echo htmlspecialchars($report['agent_name'] ?? 'Unknown Agent'); ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Target</span>
                            <span class="meta-value"><?php echo htmlspecialchars($report['target_name'] ?? 'No Target Specified'); ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Report ID</span>
                            <span class="meta-value"><?php echo htmlspecialchars($report['report_id'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="report-card">
                <p>No intelligence reports found.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 