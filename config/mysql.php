<?php
// MySQL connection settings
define('MYSQL_HOST', 'localhost');
define('MYSQL_PORT', '3308');
define('MYSQL_USER', 'root');
define('MYSQL_PASSWORD', '');
define('MYSQL_DATABASE', 'cs306');

// Helper function to get MySQL connection
function getMySQLConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . MYSQL_HOST . 
            ";port=" . MYSQL_PORT . 
            ";dbname=" . MYSQL_DATABASE,
            MYSQL_USER,
            MYSQL_PASSWORD
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?> 