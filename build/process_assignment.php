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

    // Validate input
    if (!isset($_POST['operator_id'], $_POST['drone_id'])) {
        throw new Exception("Missing required fields");
    }

    $operator_id = (int)$_POST['operator_id'];
    $drone_id = (int)$_POST['drone_id'];

    // Check if operator exists
    $stmt = $conn->prepare("SELECT operator_id FROM Operator WHERE operator_id = ?");
    $stmt->bind_param("i", $operator_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Invalid operator ID");
    }

    // Check if drone exists
    $stmt = $conn->prepare("SELECT drone_id FROM Drones WHERE drone_id = ?");
    $stmt->bind_param("i", $drone_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Invalid drone ID");
    }

    // Check if drone is already assigned
    $stmt = $conn->prepare("SELECT assignment_id FROM Assignments WHERE drone_id = ?");
    $stmt->bind_param("i", $drone_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception("This drone is already assigned to an operator");
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert assignment
        $stmt = $conn->prepare("INSERT INTO Assignments (operator_id, drone_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $operator_id, $drone_id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("Failed to create assignment");
        }

        // Commit transaction
        $conn->commit();

        // Redirect to view page with success message
        header("Location: assignment.php?success=1");
        exit;

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    // Redirect back to form with error message
    header("Location: assignment.php?error=" . urlencode($e->getMessage()));
    exit;
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 