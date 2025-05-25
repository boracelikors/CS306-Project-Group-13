<?php
require_once '../config/mongodb.php';

$message = '';
$ticket = null;

if (isset($_GET['id'])) {
    try {
        $manager = getMongoDBConnection();
        $id = new MongoDB\BSON\ObjectId($_GET['id']);
        
        // Get ticket details
        $query = new MongoDB\Driver\Query(['_id' => $id]);
        $cursor = $manager->executeQuery('cs306.tickets', $query);
        $ticket = current($cursor->toArray());

        // Handle comment submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update(
                ['_id' => $id],
                ['$push' => ['comments' => [
                    'text' => $_POST['comment'],
                    'by' => 'admin',
                    'at' => date('Y-m-d H:i:s')
                ]]]
            );
            $manager->executeBulkWrite('cs306.tickets', $bulk);
            
            // Refresh ticket data
            $cursor = $manager->executeQuery('cs306.tickets', $query);
            $ticket = current($cursor->toArray());
            $message = "Comment added successfully!";
        }

        // Handle status update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resolve'])) {
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update(
                ['_id' => $id],
                ['$set' => ['status' => false]]
            );
            $manager->executeBulkWrite('cs306.tickets', $bulk);
            $message = "Ticket marked as resolved!";
            header("Location: index.php");
            exit;
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket Details</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .ticket-info { margin: 20px 0; padding: 15px; background: #f9f9f9; }
        .comments { margin: 20px 0; }
        .comment { padding: 10px; margin: 10px 0; background: #f0f0f0; }
        .message { padding: 10px; margin: 10px 0; background: #e0e0e0; }
        textarea { width: 100%; height: 100px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Ticket Details</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if ($ticket): ?>
        <div class="ticket-info">
            <h2>Ticket from: <?php echo htmlspecialchars($ticket->username); ?></h2>
            <p>Created at: <?php echo htmlspecialchars($ticket->created_at); ?></p>
            <p>Message: <?php echo htmlspecialchars($ticket->message); ?></p>
            <p>Status: <?php echo $ticket->status ? 'Active' : 'Resolved'; ?></p>
        </div>

        <div class="comments">
            <h3>Comments</h3>
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

        <?php if ($ticket->status): ?>
            <form method="POST">
                <h3>Add Comment</h3>
                <textarea name="comment" required></textarea>
                <button type="submit">Add Comment</button>
            </form>

            <form method="POST" style="margin-top: 20px;">
                <input type="hidden" name="resolve" value="1">
                <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 20px;">
                    Mark as Resolved
                </button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <p>Ticket not found.</p>
    <?php endif; ?>

    <p><a href="index.php">Back to Admin Dashboard</a></p>
</body>
</html> 