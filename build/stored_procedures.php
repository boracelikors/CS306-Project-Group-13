<?php
header('Content-Type: text/html; charset=utf-8');

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "2003";
$dbname = "military_intelligence";

$message = '';
$results = [];

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Handle procedure execution
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $procedure = $_POST['procedure'];
        
        switch ($procedure) {
            case 'assign_operator':
                $operator_id = $_POST['operator_id'];
                $drone_id = $_POST['drone_id'];
                $rank = $_POST['rank'];
                
                $stmt = $conn->prepare("CALL AssignOperatorToDrone(?, ?, ?)");
                $stmt->bind_param("iis", $operator_id, $drone_id, $rank);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $results = $result->fetch_all(MYSQLI_ASSOC);
                }
                $message = "Operator assignment procedure executed successfully!";
                break;
                
            case 'agent_reports':
                $agent_id = $_POST['agent_id'];
                
                $stmt = $conn->prepare("CALL GetAgentReports(?)");
                $stmt->bind_param("i", $agent_id);
                $stmt->execute();
                
                // Get multiple result sets
                do {
                    if ($result = $stmt->get_result()) {
                        $results[] = $result->fetch_all(MYSQLI_ASSOC);
                    }
                } while ($stmt->next_result());
                
                $message = "Agent reports retrieved successfully!";
                break;
                
            case 'generate_report':
                $agent_id = $_POST['agent_id'];
                $title = $_POST['title'];
                $content = $_POST['content'];
                $classification = $_POST['classification'];
                
                $stmt = $conn->prepare("CALL GenerateIntelligenceReport(?, ?, ?, ?)");
                $stmt->bind_param("isss", $agent_id, $title, $content, $classification);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $results = $result->fetch_all(MYSQLI_ASSOC);
                }
                $message = "Intelligence report generated successfully!";
                break;
                
            case 'reserve_vehicle':
                $base_id = $_POST['base_id'];
                $vehicle_type = $_POST['vehicle_type'];
                
                $stmt = $conn->prepare("CALL ReserveVehicle(?, ?)");
                $stmt->bind_param("is", $base_id, $vehicle_type);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $results = $result->fetch_all(MYSQLI_ASSOC);
                }
                $message = "Vehicle reservation procedure executed successfully!";
                break;
                
            case 'order_supply':
                $supply_id = $_POST['supply_id'];
                $quantity = $_POST['quantity'];
                
                $stmt = $conn->prepare("CALL OrderSupply(?, ?)");
                $stmt->bind_param("ii", $supply_id, $quantity);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $results = $result->fetch_all(MYSQLI_ASSOC);
                }
                $message = "Supply order procedure executed successfully!";
                break;
        }
    }

    // Get data for dropdowns
    $operators = $conn->query("SELECT op_id, `rank` FROM Operator ORDER BY op_id");
    $drones = $conn->query("SELECT drone_id, model FROM Drones ORDER BY drone_id");
    $agents = $conn->query("SELECT agent_id, name FROM Agents ORDER BY name");
    $bases = $conn->query("SELECT base_id, name FROM Base ORDER BY name");
    $vehicles = $conn->query("SELECT DISTINCT `type` FROM Vehicles ORDER BY `type`");
    $supplies = $conn->query("SELECT supply_id, name, quantity FROM Supply ORDER BY name");

} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stored Procedures - Military Intelligence System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f7fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .procedure-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .procedure-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .procedure-card h3 {
            color: #2c3e50;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        .btn {
            background-color: #3498db;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .message {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .results {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 2rem;
            padding: 0.75rem 1.5rem;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-link:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">← Back to Dashboard</a>
        
        <div class="header">
            <h1>Stored Procedures</h1>
            <p>Execute database stored procedures</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'Error') === 0 ? 'error' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="procedure-grid">
            <!-- Assign Operator to Drone -->
            <div class="procedure-card">
                <h3>Assign Operator to Drone</h3>
                <form method="POST">
                    <input type="hidden" name="procedure" value="assign_operator">
                    <div class="form-group">
                        <label for="operator_id">Operator ID</label>
                        <select name="operator_id" required>
                            <option value="">Select Operator</option>
                            <?php if ($operators): ?>
                                <?php while ($op = $operators->fetch_assoc()): ?>
                                    <option value="<?php echo $op['op_id']; ?>">
                                        ID: <?php echo $op['op_id']; ?> - <?php echo $op['rank']; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="drone_id">Drone ID</label>
                        <select name="drone_id" required>
                            <option value="">Select Drone</option>
                            <?php if ($drones): ?>
                                <?php while ($drone = $drones->fetch_assoc()): ?>
                                    <option value="<?php echo $drone['drone_id']; ?>">
                                        ID: <?php echo $drone['drone_id']; ?> - <?php echo $drone['model']; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="rank">Rank</label>
                        <select name="rank" required>
                            <option value="">Select Rank</option>
                            <option value="Sergeant">Sergeant</option>
                            <option value="Lieutenant">Lieutenant</option>
                            <option value="Captain">Captain</option>
                            <option value="Major">Major</option>
                            <option value="Colonel">Colonel</option>
                        </select>
                    </div>
                    <button type="submit" class="btn">Execute Procedure</button>
                </form>
            </div>

            <!-- Get Agent Reports -->
            <div class="procedure-card">
                <h3>Get Agent Reports</h3>
                <form method="POST">
                    <input type="hidden" name="procedure" value="agent_reports">
                    <div class="form-group">
                        <label for="agent_id">Agent</label>
                        <select name="agent_id" required>
                            <option value="">Select Agent</option>
                            <?php if ($agents): ?>
                                <?php while ($agent = $agents->fetch_assoc()): ?>
                                    <option value="<?php echo $agent['agent_id']; ?>">
                                        ID: <?php echo $agent['agent_id']; ?> - <?php echo $agent['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn">Execute Procedure</button>
                </form>
            </div>

            <!-- Generate Intelligence Report -->
            <div class="procedure-card">
                <h3>Generate Intelligence Report</h3>
                <form method="POST">
                    <input type="hidden" name="procedure" value="generate_report">
                    <div class="form-group">
                        <label for="agent_id">Agent</label>
                        <select name="agent_id" required>
                            <option value="">Select Agent</option>
                            <?php 
                            $agents = $conn->query("SELECT agent_id, name FROM Agents ORDER BY name");
                            if ($agents): ?>
                                <?php while ($agent = $agents->fetch_assoc()): ?>
                                    <option value="<?php echo $agent['agent_id']; ?>">
                                        ID: <?php echo $agent['agent_id']; ?> - <?php echo $agent['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea name="content" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="classification">Classification</label>
                        <select name="classification" required>
                            <option value="">Select Classification</option>
                            <option value="Unclassified">Unclassified</option>
                            <option value="Confidential">Confidential</option>
                            <option value="Secret">Secret</option>
                            <option value="Top Secret">Top Secret</option>
                        </select>
                    </div>
                    <button type="submit" class="btn">Execute Procedure</button>
                </form>
            </div>

            <!-- Reserve Vehicle -->
            <div class="procedure-card">
                <h3>Reserve Vehicle</h3>
                <form method="POST">
                    <input type="hidden" name="procedure" value="reserve_vehicle">
                    <div class="form-group">
                        <label for="base_id">Base</label>
                        <select name="base_id" required>
                            <option value="">Select Base</option>
                            <?php if ($bases): ?>
                                <?php while ($base = $bases->fetch_assoc()): ?>
                                    <option value="<?php echo $base['base_id']; ?>">
                                        ID: <?php echo $base['base_id']; ?> - <?php echo $base['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="vehicle_type">Vehicle Type</label>
                        <select name="vehicle_type" required>
                            <option value="">Select Type</option>
                            <?php if ($vehicles): ?>
                                <?php while ($vehicle = $vehicles->fetch_assoc()): ?>
                                    <option value="<?php echo $vehicle['type']; ?>">
                                        <?php echo $vehicle['type']; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn">Execute Procedure</button>
                </form>
            </div>

            <!-- Order Supply -->
            <div class="procedure-card">
                <h3>Order Supply</h3>
                <form method="POST">
                    <input type="hidden" name="procedure" value="order_supply">
                    <div class="form-group">
                        <label for="supply_id">Supply Item</label>
                        <select name="supply_id" required>
                            <option value="">Select Supply</option>
                            <?php if ($supplies): ?>
                                <?php while ($supply = $supplies->fetch_assoc()): ?>
                                    <option value="<?php echo $supply['supply_id']; ?>">
                                        ID: <?php echo $supply['supply_id']; ?> - <?php echo $supply['name']; ?> (Current: <?php echo $supply['quantity']; ?>)
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Order Quantity</label>
                        <input type="number" name="quantity" min="1" required>
                    </div>
                    <button type="submit" class="btn">Execute Procedure</button>
                </form>
            </div>
        </div>

        <?php if (!empty($results)): ?>
            <div class="results">
                <h3>Procedure Results</h3>
                <?php foreach ($results as $index => $resultSet): ?>
                    <?php if (!empty($resultSet)): ?>
                        <h4>Result Set <?php echo $index + 1; ?></h4>
                        <table>
                            <tr>
                                <?php foreach (array_keys($resultSet[0]) as $column): ?>
                                    <th><?php echo htmlspecialchars($column); ?></th>
                                <?php endforeach; ?>
                            </tr>
                            <?php foreach ($resultSet as $row): ?>
                                <tr>
                                    <?php foreach ($row as $value): ?>
                                        <td><?php echo htmlspecialchars($value ?? 'NULL'); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 