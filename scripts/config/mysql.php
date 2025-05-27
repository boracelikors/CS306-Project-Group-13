<?php
// MySQL connection settings
define('MYSQL_HOST', 'localhost:3308');
define('MYSQL_USER', 'root');
define('MYSQL_PASSWORD', '2112');
define('MYSQL_DATABASE', 'cs306');

// Helper function to get MySQL connection
function getMySQLConnection() {
    $conn = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}
?> 