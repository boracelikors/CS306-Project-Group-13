<?php
header('Content-Type: text/html; charset=utf-8');

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "2003";
$dbname = "military_intelligence";

$message = '';

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get system statistics
    $stats = [];
    $tables = ['Countries', 'Base', 'Agents', 'Drones', 'Intelligence_Reports', 'Vehicles', 'Supply'];
    
    foreach ($tables as $table) {
        $result = $conn->query("SELECT COUNT(*) as count FROM `$table`");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats[$table] = $row['count'];
        }
    }

    // Get recent activity
    $recent_reports = $conn->query("SELECT report_id, title, date_created FROM Intelligence_Reports ORDER BY date_created DESC LIMIT 5");
    $recent_assignments = $conn->query("SELECT d.drone_id, d.model, o.op_id, o.rank FROM Drones d JOIN Operator o ON d.op_id = o.op_id ORDER BY d.drone_id DESC LIMIT 5");

} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Military Intelligence System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f7fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background-color: #e74c3c;
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .admin-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .admin-card h3 {
            color: #2c3e50;
            margin-top: 0;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #e74c3c;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            margin: 0.5rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.2s;
        }
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
        .btn-success {
            background-color: #27ae60;
            color: white;
        }
        .btn-success:hover {
            background-color: #229954;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 2rem;
            padding: 0.75rem 1.5rem;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-link:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">← Back to Dashboard</a>
        
        <div class="header">
            <h1>🔒 Admin Panel</h1>
            <p>System Administration and Management</p>
        </div>

        <?php if ($message): ?>
            <div style="padding: 1rem; margin: 1rem 0; border-radius: 4px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- System Statistics -->
        <div class="stat-grid">
            <?php foreach ($stats as $table => $count): ?>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $count; ?></div>
                    <div class="stat-label"><?php echo str_replace('_', ' ', $table); ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="admin-grid">
            <!-- Database Management -->
            <div class="admin-card">
                <h3>Database Management</h3>
                <p>Manage database tables, procedures, and triggers.</p>
                <a href="view_database.php" class="btn btn-primary">View All Data</a>
                <a href="stored_procedures.php" class="btn btn-primary">Stored Procedures</a>
                <a href="trigger_monitor.php" class="btn btn-primary">Trigger Monitor</a>
            </div>

            <!-- User Management -->
            <div class="admin-card">
                <h3>User Management</h3>
                <p>Manage operators, agents, and personnel.</p>
                <a href="user_management.php" class="btn btn-success">Manage Users</a>
                <a href="add_user.php" class="btn btn-success">Add New User</a>
            </div>

            <!-- System Monitoring -->
            <div class="admin-card">
                <h3>System Monitoring</h3>
                <p>Monitor system performance and activity.</p>
                <a href="system_logs.php" class="btn btn-primary">View Logs</a>
                <a href="performance_monitor.php" class="btn btn-primary">Performance</a>
            </div>

            <!-- Ticket System -->
            <div class="admin-card">
                <h3>Support Tickets</h3>
                <p>Manage user support tickets and issues.</p>
                <a href="admin/index.php" class="btn btn-danger">Ticket Dashboard</a>
                <a href="create_ticket.php" class="btn btn-danger">Create Ticket</a>
            </div>

            <!-- Security -->
            <div class="admin-card">
                <h3>Security</h3>
                <p>Security settings and access control.</p>
                <a href="security_settings.php" class="btn btn-danger">Security Settings</a>
                <a href="access_logs.php" class="btn btn-danger">Access Logs</a>
            </div>

            <!-- Backup & Restore -->
            <div class="admin-card">
                <h3>Backup & Restore</h3>
                <p>Database backup and restoration tools.</p>
                <a href="backup.php" class="btn btn-success">Create Backup</a>
                <a href="restore.php" class="btn btn-success">Restore Data</a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="admin-card">
            <h3>Recent Activity</h3>
            
            <h4>Recent Intelligence Reports</h4>
            <?php if ($recent_reports && $recent_reports->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Report ID</th>
                        <th>Title</th>
                        <th>Date Created</th>
                    </tr>
                    <?php while ($report = $recent_reports->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($report['report_id']); ?></td>
                            <td><?php echo htmlspecialchars($report['title']); ?></td>
                            <td><?php echo htmlspecialchars($report['date_created']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No recent reports found.</p>
            <?php endif; ?>

            <h4>Recent Drone Assignments</h4>
            <?php if ($recent_assignments && $recent_assignments->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Drone ID</th>
                        <th>Model</th>
                        <th>Operator ID</th>
                        <th>Operator Rank</th>
                    </tr>
                    <?php while ($assignment = $recent_assignments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($assignment['drone_id']); ?></td>
                            <td><?php echo htmlspecialchars($assignment['model']); ?></td>
                            <td><?php echo htmlspecialchars($assignment['op_id']); ?></td>
                            <td><?php echo htmlspecialchars($assignment['rank']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No recent assignments found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 