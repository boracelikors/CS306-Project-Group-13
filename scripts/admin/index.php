<?php
// MongoDB connection
require_once '../config/mongodb.php';

try {
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    
    // Query to get all active tickets
    $query = new MongoDB\Driver\Query(['status' => true]);
    $cursor = $manager->executeQuery('cs306.tickets', $query);
    
} catch (MongoDB\Driver\Exception\Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS306 Project - Admin Interface</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .ticket {
            margin: 10px 0;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .ticket:hover {
            background: #f0f0f0;
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        a {
            color: #0066cc;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Admin Dashboard - Support Tickets</h1>
    
    <div class="tickets-section">
        <h2>Active Tickets</h2>
        <?php
        foreach ($cursor as $ticket) {
            echo '<div class="ticket">';
            echo '<h3>Ticket from: ' . htmlspecialchars($ticket->username) . '</h3>';
            echo '<p>Created at: ' . htmlspecialchars($ticket->created_at) . '</p>';
            echo '<p>' . htmlspecialchars($ticket->message) . '</p>';
            echo '<a href="ticket_detail.php?id=' . $ticket->_id . '">View Details</a>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html> 