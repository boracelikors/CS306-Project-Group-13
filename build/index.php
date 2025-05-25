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
    
    // Get table counts for dashboard
    $tableNames = ['Countries', 'Base', 'Person', 'Soldier', 'Civil', 'Agents', 
                   'Satellites', 'IntelligenceReports', 'Drones', 'Missiles', 
                   'Operator', 'Targets', 'Vehicles', 'Supply'];
    
    $counts = [];
    foreach ($tableNames as $table) {
        $result = $conn->query("SELECT COUNT(*) as count FROM `$table`");
        if ($result) {
            $row = $result->fetch_assoc();
            $counts[$table] = $row['count'];
        } else {
            $counts[$table] = 'Error';
        }
    }
    
    // Count current assignments
    $assignmentCount = 0;
    $result = $conn->query("SELECT COUNT(*) as count FROM Assignments WHERE status = 'ACTIVE'");
    if ($result) {
        $row = $result->fetch_assoc();
        $assignmentCount = $row['count'];
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
    <title>Military Intelligence System</title>
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
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.1) 100%);
            z-index: 1;
        }

        .header-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            padding: 1rem;
        }

        .military-logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-right: 20px;
        }

        .header-text {
            text-align: left;
        }

        .header h1 {
            margin: 0;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .header p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: var(--card-shadow);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--accent-color);
        }

        .stat-label {
            color: #666;
            margin-top: 0.5rem;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .module-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .module-card h2 {
            color: var(--primary-color);
            margin-top: 0;
            font-size: 1.5rem;
        }

        .module-card p {
            color: #666;
            margin-bottom: 1.5rem;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
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

        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .status-bar {
            background-color: white;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-bar p {
            margin: 0;
            color: #666;
        }

        .status-bar strong {
            color: var(--primary-color);
        }

        .documentation {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: var(--card-shadow);
        }

        .documentation h2 {
            color: var(--primary-color);
            margin-top: 0;
        }

        .documentation ul {
            list-style-type: none;
            padding: 0;
        }

        .documentation li {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
            position: relative;
        }

        .documentation li:before {
            content: "•";
            color: var(--accent-color);
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .audio-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(44, 62, 80, 0.9);
            padding: 10px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .audio-controls button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .audio-controls button:hover {
            opacity: 0.8;
        }

        .audio-controls .icon {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body>
    <audio id="bgMusic" loop>
        <source src="assets/military-march.mp3" type="audio/mpeg">
    </audio>

    <script>
        window.addEventListener('load', function() {
            const audio = document.getElementById('bgMusic');
            
            // Try to play automatically
            const playPromise = audio.play();
            
            if (playPromise !== undefined) {
                playPromise.catch(function(error) {
                    // Auto-play was prevented
                    // Show a button to start playing
                    const playButton = document.createElement('button');
                    playButton.innerHTML = '🎵 Play Military March';
                    playButton.style.cssText = 'position: fixed; bottom: 20px; right: 20px; padding: 10px; background: rgba(44, 62, 80, 0.9); color: white; border: none; border-radius: 5px; cursor: pointer; z-index: 1000;';
                    
                    playButton.addEventListener('click', function() {
                        audio.play();
                        playButton.remove();
                    });
                    
                    document.body.appendChild(playButton);
                });
            }
        });
    </script>

    <div class="header">
        <div class="header-content">
            <img src="assets/military-logo.png" alt="Military Intelligence Logo" class="military-logo">
            <div class="header-text">
                <h1>Military Intelligence System</h1>
                <p>Command and Control Dashboard</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="status-bar">
            <p><strong>System Status:</strong> Online | <strong>Last Update:</strong> <?php echo date('Y-m-d H:i:s', strtotime('+3 hours')); ?></p>
            <a href="view_database.php" class="btn btn-primary">View Current Data</a>
        </div>

        <!-- Database Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $counts['Drones'] ?? 0; ?></div>
                <div class="stat-label">Drones</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $counts['Operator'] ?? 0; ?></div>
                <div class="stat-label">Operators</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $counts['Targets'] ?? 0; ?></div>
                <div class="stat-label">Targets</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $counts['IntelligenceReports'] ?? 0; ?></div>
                <div class="stat-label">Reports</div>
            </div>
        </div>

        <div class="dashboard">
            <div class="module-card">
                <h2>Drone Assignment Operations</h2>
                <p>Manage drone and operator assignments for military operations.</p>
                <div class="button-group">
                    <a href="assignment.php" class="btn btn-primary">Assign Operator to Drone</a>
                    <a href="view_assignments.php" class="btn btn-secondary">View Current Assignments</a>
                </div>
            </div>

            <div class="module-card">
                <h2>Intelligence Operations</h2>
                <p>Access and manage intelligence reports from field operations.</p>
                <div class="button-group">
                    <a href="intelligence_reports.php" class="btn btn-primary">View Intelligence Reports</a>
                    <a href="generate_report.php" class="btn btn-secondary">Generate New Report</a>
                </div>
            </div>
        </div>

        <!-- Documentation Section -->
        <div class="documentation">
            <h2>About the System</h2>
            <p>This Military Intelligence System provides a comprehensive platform for managing military operations, intelligence gathering, and resource allocation.</p>
            
            <h3>Database Structure</h3>
            <ul>
                <li><strong>Personnel Tables:</strong> Operators, Soldiers, Agents, Civil Personnel</li>
                <li><strong>Equipment Tables:</strong> Drones, Missiles, Satellites, Vehicles</li>
                <li><strong>Operations Tables:</strong> Assignments, Missions, Intelligence Reports</li>
                <li><strong>Infrastructure Tables:</strong> Bases, Supply Chain, Geographic Data</li>
            </ul>

            <h3>Main Operations</h3>
            <ul>
                <li><strong>Drone Assignments:</strong> Manage operator-drone pairings for missions</li>
                <li><strong>Intelligence Reports:</strong> Access and generate mission intelligence reports</li>
            </ul>

            <h3>Database Overview</h3>
            <p>The system maintains a comprehensive military intelligence database with real-time updates for mission-critical operations. Use the "View Current Data" button to see the latest state of all database tables.</p>
        </div>
    </div>
</body>
</html> 