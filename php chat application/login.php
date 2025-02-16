<?php
include('db.php');
session_start();

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check user credentials
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: chat.php");
        exit();
    } else {
        echo "Invalid credentials!";
    }
}
?>

<form method="POST" action="">
    <label>Username: </label><input type="text" name="username" required><br>
    <label>Password: </label><input type="password" name="password" required><br>
    <button type="submit" name="login">Login</button>
</form>
