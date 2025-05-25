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

    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    // Get form data
    $op_id = $_POST['op_id'] ?? null;
    $drone_id = $_POST['drone_id'] ?? null;

    // Validate input
    if (empty($op_id) || empty($drone_id)) {
        throw new Exception("Both operator and drone must be selected");
    }

    // Validate that operator exists
    $stmt = $conn->prepare("SELECT op_id FROM Operator WHERE op_id = ?");
    $stmt->bind_param("i", $op_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Selected operator does not exist");
    }

    // Validate that drone exists
    $stmt = $conn->prepare("SELECT drone_id FROM Drones WHERE drone_id = ?");
    $stmt->bind_param("i", $drone_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Selected drone does not exist");
    }

    // Update the drone's assigned operator
    $stmt = $conn->prepare("UPDATE Drones SET op_id = ? WHERE drone_id = ?");
    $stmt->bind_param("ii", $op_id, $drone_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update assignment: " . $stmt->error);
    }

    // Success - redirect back to assignment page
    header("Location: assignment.php?success=1");
    exit();

} catch (Exception $e) {
    // Error - redirect back with error message
    $error = urlencode($e->getMessage());
    header("Location: assignment.php?error=$error");
    exit();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 