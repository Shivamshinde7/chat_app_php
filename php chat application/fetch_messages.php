<?php
include('db.php');

$messages = $pdo->query("SELECT * FROM messages ORDER BY timestamp DESC")->fetchAll();
foreach ($messages as $msg) {
    echo '<div class="message">
            <span>' . htmlspecialchars($msg['user_name']) . ':</span>
            ' . htmlspecialchars($msg['message']) . '
          </div>';
}
?>
