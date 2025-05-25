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
    </style>
    ";

    echo "<h1>Military Intelligence Database State</h1>";
    echo "<div class='timestamp'>Timestamp: " . date('Y-m-d H:i:s', strtotime('+3 hours')) . "</div>";

    // Function to print table contents
    function printTable($conn, $tableName) {
        $result = $conn->query("SELECT * FROM `$tableName`");
        
        if (!$result) {
            echo "Error fetching data from $tableName: " . $conn->error;
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

    // List of all tables to display
    $tables = [
        'Countries',
        'Base',
        'Person',
        'Soldier',
        'Civil',
        'Agents',
        'Satellites',
        'IntelligenceReports',
        'Drones',
        'Missiles',
        'Operator',
        'Targets',
        'Vehicles',
        'Supply',
        'Decides',
        'Watches',
        'Attacks',
        'In_Radar',
        'Stores'
    ];

    // Display each table
    foreach ($tables as $table) {
        printTable($conn, $table);
    }

} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 