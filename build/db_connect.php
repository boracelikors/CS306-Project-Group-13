<?php
// Database connection parameters
$host = "localhost";
$username = "root";
$password = "2003";
$database = "military_intelligence";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}
?> 