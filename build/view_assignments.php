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

    // Add some basic styling
    echo "
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 30px; }
        table { border-collapse: collapse; margin-bottom: 20px; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .timestamp { color: #666; font-size: 0.9em; margin-bottom: 20px; }
        .highlight { background-color: #fff3cd !important; }
        .status-available { color: #28a745; }
        .status-assigned { color: #dc3545; }
        .back-link { display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #2c3e50; color: white; text-decoration: none; border-radius: 4px; }
        .back-link:hover { background-color: #34495e; }
    </style>
    ";

    echo "<h1>Drone Assignment System Status</h1>";
    echo "<div class='timestamp'>Timestamp: " . date('Y-m-d H:i:s', strtotime('+3 hours')) . "</div>";

    // Display operators table (using correct column names)
    echo "<h2>Operators</h2>";
    $result = $conn->query("SELECT * FROM Operator");
    
    if (!$result) {
        echo "Error fetching operators: " . $conn->error;
    } else {
        echo "<table>";
        echo "<tr><th>ID</th><th>Rank</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $rowClass = '';
            if (isset($_GET['op_id']) && $row['op_id'] == $_GET['op_id']) {
                $rowClass = ' class="highlight"';
            }
            echo "<tr$rowClass>";
            echo "<td>" . htmlspecialchars($row['op_id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['rank'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Display drones table (using correct structure)
    echo "<h2>Drones</h2>";
    $result = $conn->query("SELECT * FROM Drones");
    
    if (!$result) {
        echo "Error fetching drones: " . $conn->error;
    } else {
        echo "<table>";
        echo "<tr><th>ID</th><th>Model</th><th>Range</th><th>Max Altitude</th><th>Assigned Operator</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $rowClass = '';
            
            if (isset($_GET['drone_id']) && $row['drone_id'] == $_GET['drone_id']) {
                $rowClass = ' class="highlight"';
            }
            
            echo "<tr$rowClass>";
            echo "<td>" . htmlspecialchars($row['drone_id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['model'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['range'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['max_altitude'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['op_id'] ?? 'Unassigned') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Display drone-operator relationships
    echo "<h2>Current Drone-Operator Assignments</h2>";
    $sql = "SELECT d.drone_id, d.model, d.range, d.max_altitude, 
                   o.op_id, o.rank
            FROM Drones d
            JOIN Operator o ON d.op_id = o.op_id
            ORDER BY d.drone_id";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        echo "Error fetching assignments: " . $conn->error;
    } else {
        if ($result->num_rows === 0) {
            echo "<p>No drone-operator assignments found.</p>";
        } else {
            echo "<table>";
            echo "<tr>
                  <th>Drone ID</th>
                  <th>Drone Model</th>
                  <th>Operator ID</th>
                  <th>Operator Rank</th>
                  </tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['drone_id'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['model'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['op_id'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['rank'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }

    echo "<a href='index.php' class='back-link'>Back to Home</a>";

} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 