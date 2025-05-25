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
        .back-link { display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #2c3e50; color: white; text-decoration: none; border-radius: 4px; }
        .back-link:hover { background-color: #34495e; }
    </style>
    ";

    echo "<h1>Military Intelligence Database State</h1>";
    echo "<div class='timestamp'>Timestamp: " . date('Y-m-d H:i:s', strtotime('+3 hours')) . "</div>";

    // Function to print table contents
    function printTable($conn, $tableName) {
        $result = $conn->query("SELECT * FROM `$tableName`");
        
        if (!$result) {
            echo "<h2>Table: $tableName</h2>";
            echo "<p>Error fetching data from $tableName: " . $conn->error . "</p>";
            return;
        }

        echo "<h2>Table: $tableName</h2>";
        echo "<table>";
        
        // Print headers
        echo "<tr>";
        $fields = $result->fetch_fields();
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>";
        
        // Print data
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
    }

    // List of actual tables that exist in the database
    $tables = [
        'Countries',
        'Base',
        'Person',
        'Soldier',
        'Civil',
        'Agents',
        'Satellites',
        'Intelligence_Reports',
        'Drones',
        'Missiles',
        'Operator',
        'Targets',
        'Vehicles',
        'Supply',
        'Intelligence_Report_Decides_Target',
        'Agent_Wrote_Report',
        'Target_Base_Radar',
        'Base_Stores_Supply'
    ];

    // Display each table
    foreach ($tables as $table) {
        printTable($conn, $table);
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