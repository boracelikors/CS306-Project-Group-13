<?php
require_once '../config/mongodb.php';

$tickets = [];
$message = '';

if (isset($_GET['username'])) {
    try {
        $manager = getMongoDBConnection();
        
        // Get active tickets for the user
        $query = new MongoDB\Driver\Query([
            'username' => $_GET['username'],
            'status' => true
        ]);
        $cursor = $manager->executeQuery('cs306.tickets', $query);
        $tickets = $cursor->toArray();
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle adding comments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'], $_POST['comment'])) {
    try {
        $manager = getMongoDBConnection();
        $id = new MongoDB\BSON\ObjectId($_POST['ticket_id']);
        
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(
            ['_id' => $id],
            ['$push' => ['comments' => [
                'text' => $_POST['comment'],
                'by' => $_GET['username'],
                'at' => date('Y-m-d H:i:s')
            ]]]
        );
        $manager->executeBulkWrite('cs306.tickets', $bulk);
        
        // Refresh the page to show new comment
        header("Location: view_tickets.php?username=" . urlencode($_GET['username']));
        exit;
    } catch (Exception $e) {
        $message = "Error adding comment: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Tickets</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .ticket { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .comments { margin-top: 10px; }
        .comment { background: #f0f0f0; padding: 10px; margin: 5px 0; }
        .message { padding: 10px; margin: 10px 0; background: #f0f0f0; }
        textarea { width: 100%; height: 60px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Your Support Tickets</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!empty($tickets)): ?>
        <?php foreach ($tickets as $ticket): ?>
            <div class="ticket">
                <h3>Ticket #<?php echo $ticket->_id; ?></h3>
                <p>Created at: <?php echo htmlspecialchars($ticket->created_at); ?></p>
                <p>Message: <?php echo htmlspecialchars($ticket->message); ?></p>
                
                <div class="comments">
                    <h4>Comments:</h4>
                    <?php if (!empty($ticket->comments)): ?>
                        <?php foreach ($ticket->comments as $comment): ?>
                            <div class="comment">
                                <p><?php echo htmlspecialchars($comment->text); ?></p>
                                <small>By <?php echo htmlspecialchars($comment->by); ?> 
                                       at <?php echo htmlspecialchars($comment->at); ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No comments yet.</p>
                    <?php endif; ?>
                </div>

                <form method="POST">
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket->_id; ?>">
                    <textarea name="comment" placeholder="Add a comment..." required></textarea>
                    <button type="submit">Add Comment</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No active tickets found.</p>
    <?php endif; ?>

    <p><a href="support.php">Back to Support Page</a></p>
</body>
</html> 