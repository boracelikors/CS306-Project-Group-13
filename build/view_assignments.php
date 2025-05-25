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

    // Display operators table
    echo "<h2>Operators</h2>";
    $result = $conn->query("SELECT * FROM Operator");
    
    if (!$result) {
        echo "Error fetching operators: " . $conn->error;
    } else {
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Rank</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $rowClass = '';
            if (isset($_GET['operator_id']) && $row['operator_id'] == $_GET['operator_id']) {
                $rowClass = ' class="highlight"';
            }
            echo "<tr$rowClass>";
            echo "<td>" . htmlspecialchars($row['operator_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['rank']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Display drones table
    echo "<h2>Drones</h2>";
    $result = $conn->query("SELECT d.*, 
                           CASE WHEN a.drone_id IS NOT NULL THEN 'Assigned' ELSE 'Available' END AS status 
                           FROM Drones d
                           LEFT JOIN Assignments a ON d.drone_id = a.drone_id");
    
    if (!$result) {
        echo "Error fetching drones: " . $conn->error;
    } else {
        echo "<table>";
        echo "<tr><th>ID</th><th>Model</th><th>Range</th><th>Max Altitude</th><th>Status</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $rowClass = '';
            $statusClass = '';
            
            if (isset($_GET['drone_id']) && $row['drone_id'] == $_GET['drone_id']) {
                $rowClass = ' class="highlight"';
            }
            
            if ($row['status'] === 'Available') {
                $statusClass = ' class="status-available"';
            } else {
                $statusClass = ' class="status-assigned"';
            }
            
            echo "<tr$rowClass>";
            echo "<td>" . htmlspecialchars($row['drone_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['model']) . "</td>";
            echo "<td>" . htmlspecialchars($row['range']) . "</td>";
            echo "<td>" . htmlspecialchars($row['max_altitude']) . "</td>";
            echo "<td$statusClass>" . htmlspecialchars($row['status']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Display assignments table
    echo "<h2>Current Assignments</h2>";
    $sql = "SELECT a.assignment_id, a.assignment_date, a.status,
                  o.operator_id, o.name as operator_name, o.rank as operator_rank,
                  d.drone_id, d.model as drone_model
           FROM Assignments a
           JOIN Operator o ON a.operator_id = o.operator_id
           JOIN Drones d ON a.drone_id = d.drone_id
           ORDER BY a.assignment_date DESC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        echo "Error fetching assignments: " . $conn->error;
    } else {
        if ($result->num_rows === 0) {
            echo "<p>No assignments found. Use the assignment form to create one.</p>";
        } else {
            echo "<table>";
            echo "<tr>
                  <th>Assignment ID</th>
                  <th>Operator</th>
                  <th>Drone</th>
                  <th>Assignment Date</th>
                  <th>Status</th>
                  </tr>";
            
            while ($row = $result->fetch_assoc()) {
                $rowClass = '';
                if ((isset($_GET['operator_id']) && $row['operator_id'] == $_GET['operator_id']) || 
                    (isset($_GET['drone_id']) && $row['drone_id'] == $_GET['drone_id'])) {
                    $rowClass = ' class="highlight"';
                }
                
                echo "<tr$rowClass>";
                echo "<td>" . htmlspecialchars($row['assignment_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['operator_id'] . ' - ' . $row['operator_name'] . ' (' . $row['operator_rank'] . ')') . "</td>";
                echo "<td>" . htmlspecialchars($row['drone_id'] . ' - ' . $row['drone_model']) . "</td>";
                echo "<td>" . htmlspecialchars($row['assignment_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
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