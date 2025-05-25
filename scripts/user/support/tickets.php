<?php
require_once '../../config/mongodb.php';

$message = '';
$success = false;
$selected_username = '';
$tickets = [];

// Get list of usernames with active tickets
$active_users = [];
$filter = ['status' => true];
$options = ['sort' => ['created_at' => -1]];
$documents = findDocuments(MONGODB_COLLECTION, $filter, $options);

foreach ($documents as $doc) {
    if (!in_array($doc->username, $active_users)) {
        $active_users[] = $doc->username;
    }
}
sort($active_users);

// If username is selected, get their tickets
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $selected_username = $_POST['username'];
    $filter = ['username' => $selected_username, 'status' => true];
    $options = ['sort' => ['created_at' => -1]];
    $tickets = findDocuments(MONGODB_COLLECTION, $filter, $options);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        button:hover {
            background: #2980b9;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .link-button {
            display: inline-block;
            background: #2ecc71;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .link-button:hover {
            background: #27ae60;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .ticket-list {
            margin-top: 20px;
        }
        .ticket {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .ticket-date {
            color: #666;
            font-size: 0.9em;
        }
        .ticket-body {
            margin-bottom: 10px;
        }
        .ticket-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #666;
            font-size: 0.9em;
        }
        .no-tickets {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
            color: #666;
        }
        .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <h1>Support Tickets</h1>

    <?php if (empty($active_users)): ?>
        <div class="no-tickets">
            <p>There are no active tickets in the system.</p>
            <a href="create_ticket.php" class="link-button">Create New Ticket</a>
        </div>
    <?php else: ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Select Username:</label>
                <select id="username" name="username" required>
                    <option value="">Choose a username</option>
                    <?php foreach ($active_users as $username): ?>
                        <option value="<?php echo htmlspecialchars($username); ?>"
                                <?php echo $selected_username === $username ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($username); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="action-buttons">
                <button type="submit">View Tickets</button>
                <a href="create_ticket.php" class="link-button">Create New Ticket</a>
            </div>
        </form>

        <?php if ($selected_username && !empty($tickets)): ?>
            <div class="ticket-list">
                <h2>Active Tickets for <?php echo htmlspecialchars($selected_username); ?></h2>
                <?php foreach ($tickets as $ticket): ?>
                    <div class="ticket">
                        <div class="ticket-header">
                            <h3>Ticket #<?php echo substr($ticket->_id, -6); ?></h3>
                            <span class="ticket-date"><?php echo $ticket->created_at; ?></span>
                        </div>
                        <div class="ticket-body">
                            <?php echo htmlspecialchars($ticket->message); ?>
                        </div>
                        <div class="ticket-footer">
                            <span>Comments: <?php echo count($ticket->comments); ?></span>
                            <a href="view_ticket.php?id=<?php echo $ticket->_id; ?>">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($selected_username): ?>
            <div class="no-tickets">
                <p>No active tickets found for <?php echo htmlspecialchars($selected_username); ?>.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <a href="../index.php" class="back-link">Back to Home</a>
</body>
</html> 