<?php
include('db.php');

if (isset($_POST['send'])) {
    $user_name = $_POST['user_name'];
    $message = $_POST['message'];
    $stmt = $pdo->prepare("INSERT INTO messages (user_name, message) VALUES (?, ?)");
    $stmt->execute([$user_name, $message]);
}

$messages = $pdo->query("SELECT * FROM messages ORDER BY timestamp DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Chat System</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .chat-container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; }
        .messages { height: 300px; overflow-y: scroll; margin-bottom: 20px; }
        .message { padding: 5px; border-bottom: 1px solid #ddd; }
        .message span { font-weight: bold; }
        .input-area { display: flex; }
        .input-area input { flex: 1; padding: 10px; }
        .input-area button { padding: 10px; background: #4CAF50; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="chat-container">
    <h2>Chat System</h2>
    <div class="messages">
        <?php foreach ($messages as $msg): ?>
            <div class="message">
                <span><?php echo htmlspecialchars($msg['user_name']); ?>:</span>
                <?php echo htmlspecialchars($msg['message']); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" action="">
        <div class="input-area">
            <input type="text" name="user_name" placeholder="Your name" required>
            <input type="text" name="message" placeholder="Type a message" required>
            <button type="submit" name="send">Send</button>
        </div>
    </form>
</div>

<script>
    setInterval(function() {
        fetchMessages();
    }, 3000); // 3 seconds interval

    function fetchMessages() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_messages.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.querySelector('.messages').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }
</script>


</body>
</html>
