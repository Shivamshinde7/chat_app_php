<?php
include('db.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sender_id = $_SESSION['user_id'];

// Fetch users to chat with (excluding the current user)
$users = $pdo->query("SELECT * FROM users WHERE id != $sender_id")->fetchAll();

// Fetch the latest messages for each user
$latest_messages = [];
foreach ($users as $user) {
    $receiver_id = $user['id'];
    
    // Fetch the latest message between the logged-in user and the selected user
    $latest_message = $pdo->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp DESC LIMIT 1");
    $latest_message->execute([$sender_id, $receiver_id, $receiver_id, $sender_id]);

    // Check if a message is found
    $result = $latest_message->fetch();
    if ($result) {
        $latest_messages[$receiver_id] = $result;
    } else {
        $latest_messages[$receiver_id] = null;  // No messages yet
    }
}

// Check if a user is selected for chatting
if (isset($_GET['receiver_id'])) {
    $receiver_id = $_GET['receiver_id'];
    
    // Fetch previous messages between the logged-in user and the selected user
    $messages = $pdo->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp ASC");
    $messages->execute([$sender_id, $receiver_id, $receiver_id, $sender_id]);
    $messages = $messages->fetchAll();
} else {
    $messages = [];
    $receiver_id = null;
}

// Send a new message
if (isset($_POST['send'])) {
    $message = $_POST['message'];
    if (!empty($message) && $receiver_id) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$sender_id, $receiver_id, $message]);
        header("Location: chat.php?receiver_id=$receiver_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat System</title>
    <style>
        .chat-container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; }
        .user-list { border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 20px; }
        .user-item { display: flex; justify-content: space-between; padding: 10px; cursor: pointer; }
        .user-item:hover { background-color: #f0f0f0; }
        .messages { height: 300px; overflow-y: scroll; margin-bottom: 20px; }
        .message { padding: 5px; border-bottom: 1px solid #ddd; }
        .message .sender { font-weight: bold; }
        .input-area { display: flex; }
        .input-area input { flex: 1; padding: 10px; }
        .input-area button { padding: 10px; background: #4CAF50; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="chat-container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
    
    <div class="user-list">
        <h3>Chats</h3>
        <?php foreach ($users as $user): ?>
            <div class="user-item">
                <a href="chat.php?receiver_id=<?php echo $user['id']; ?>">
                    <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                </a>
                <span>
                    <?php 
                    if (isset($latest_messages[$user['id']])) {
                        if ($latest_messages[$user['id']] !== null) {
                            echo htmlspecialchars($latest_messages[$user['id']]['message']);
                        } else {
                            echo 'No messages yet';
                        }
                    }
                    ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($receiver_id): ?>
        <h3>Chat with <?php echo htmlspecialchars($users[array_search($receiver_id, array_column($users, 'id'))]['username']); ?></h3>

        <div class="messages">
            <?php foreach ($messages as $msg): ?>
                <div class="message">
                    <span class="sender">
                        <?php 
                            if ($msg['sender_id'] == $sender_id) {
                                echo "You";
                            } else {
                                // Find the other user's name
                                $receiver_name = ($msg['sender_id'] == $sender_id) ? $users[array_search($receiver_id, array_column($users, 'id'))]['username'] : $users[array_search($msg['sender_id'], array_column($users, 'id'))]['username'];
                                echo htmlspecialchars($receiver_name);
                            }
                        ?>
                    </span>: <?php echo htmlspecialchars($msg['message']); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" action="">
            <div class="input-area">
                <input type="text" name="message" placeholder="Type a message" required>
                <button type="submit" name="send">Send</button>
            </div>
        </form>
    <?php else: ?>
        <p>Select a user to start chatting.</p>
    <?php endif; ?>
</div>

</body>
</html>
