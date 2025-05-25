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

    // Get available operators (using correct column names: op_id, rank)
    $operators = $conn->query("SELECT op_id, `rank` FROM Operator ORDER BY op_id");
    if (!$operators) {
        throw new Exception("Error fetching operators: " . $conn->error);
    }

    // Get available drones (escape 'range' keyword with backticks)
    $drones = $conn->query("SELECT drone_id, model, `range`, max_altitude, op_id FROM Drones ORDER BY drone_id");
    if (!$drones) {
        throw new Exception("Error fetching drones: " . $conn->error);
    }

    // Get the error message from URL if exists
    $error = isset($_GET['error']) ? $_GET['error'] : null;

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Operator to Drone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        .header p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
        }
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: bold;
        }
        select, input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .help-text {
            font-size: 0.875rem;
            color: #666;
            margin-top: 0.25rem;
        }
        .button-group {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
            text-decoration: none;
        }
        .btn-primary {
            background-color: #2c3e50;
            color: white;
        }
        .btn-primary:hover {
            background-color: #34495e;
        }
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        .error-message {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c00;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .success-message {
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Assign Operator to Drone</h1>
        <p>Module 1: Drone Operator Assignment System</p>
    </div>

    <div class="container">
        <h2>Assignment Form</h2>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">Assignment was successfully updated!</div>
        <?php endif; ?>

        <form action="process_assignment.php" method="POST">
            <div class="form-group">
                <label for="op_id">Operator</label>
                <select name="op_id" id="op_id" required>
                    <option value="">Select an operator</option>
                    <?php while ($operator = $operators->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($operator['op_id']); ?>">
                            <?php echo htmlspecialchars('ID: ' . $operator['op_id'] . ' - Rank: ' . $operator['rank']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <div class="help-text">Select the operator for this assignment.</div>
            </div>

            <div class="form-group">
                <label for="drone_id">Drone</label>
                <select name="drone_id" id="drone_id" required>
                    <option value="">Select a drone</option>
                    <?php 
                    // Reset the result pointer and escape 'range' keyword
                    $drones = $conn->query("SELECT drone_id, model, `range`, max_altitude, op_id FROM Drones ORDER BY drone_id");
                    while ($drone = $drones->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($drone['drone_id']); ?>">
                            <?php echo htmlspecialchars('ID: ' . $drone['drone_id'] . ' - ' . $drone['model'] . ' (Currently assigned to Operator ' . $drone['op_id'] . ')'); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <div class="help-text">Select the drone to reassign to the selected operator.</div>
            </div>

            <div class="button-group">
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
                <button type="submit" class="btn btn-primary">Update Assignment</button>
            </div>
        </form>

        <h2>Current Assignments</h2>
        <table>
            <tr>
                <th>Drone ID</th>
                <th>Model</th>
                <th>Range</th>
                <th>Max Altitude</th>
                <th>Assigned Operator</th>
                <th>Operator Rank</th>
            </tr>
            <?php 
            // Escape 'range' keyword in the assignments query too
            $assignments = $conn->query("
                SELECT d.drone_id, d.model, d.`range`, d.max_altitude, d.op_id, o.`rank`
                FROM Drones d
                JOIN Operator o ON d.op_id = o.op_id
                ORDER BY d.drone_id
            ");
            
            if ($assignments && $assignments->num_rows > 0):
                while ($assignment = $assignments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($assignment['drone_id']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['model']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['range']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['max_altitude']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['op_id']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['rank']); ?></td>
                    </tr>
                <?php endwhile;
            endif; ?>
        </table>
    </div>
</body>
</html> 